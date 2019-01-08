<?php

namespace AsaEs\Base;

use App\AppConst\AppInfo;
use App\Module\Logistics\Bean\LogisticsBean;
use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use AsaEs\Config;
use AsaEs\Db\EsMysqliDb;
use AsaEs\Exception\Service\MysqlException;
use AsaEs\Logger\FileLogger;
use AsaEs\Proxy\DaoProxy;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Db;
use AsaEs\Utility\Env;
use AsaEs\Utility\RedisUtility;
use AsaEs\Utility\Time;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;
use think\Validate;
use think\validate\ValidateRule;

class BaseDao
{
    /**
     * @var Bean 对象
     */
    protected $beanObj;

    /**
     * 自动加缓存
     * @var bool
     */
    protected $autoCache = AppInfo::DAO_AOTO_CACHE;

    /**
     * db实例
     * @var array
     */
    private $db = [];

    /**
     * 获取表最后的一条插入记录
     */
    public function getByLast(string $field = 'id')
    {
        // 数据填充
        $this->getDb()->orderBy($field, 'DESC');
        $fields =  $this->getBeanObj()->getFields();
        $row = $this->getDb()->getOne($this->getBeanObj()->getTableName(), Db::setFieldsGraveAccent($fields)) ?? [];
        $this->getDb()->saveLog(__FUNCTION__);
        return $row ?? [];
    }

    /**
     * getById查询缓存
     */
    public function getByIdCache(?int $id) :?array
    {
        if (!isset($id)) {
            return [];
        }

        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $redisKey = $this->getBasicRedisHashKey();

        $rowJson = $redisObj->hGet($redisKey, $id);
        $row = json_decode($rowJson, true) ?? [];

        // 如果查询出来是空 看看缓存中是否有key
        if (empty($row)) {
            if ($redisObj->hExists($redisKey, $id)) {
                return null;
            }
        }

        return $row;
    }

    /**
     * getById保存缓存
     */
    public function setByIdCache(?int $id, $row):void
    {
        if (!isset($id)) {
            return;
        }

        $isTransaction = $this->getDb()->isTransactionInProgress();
        if (!$isTransaction) {
            $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
            $redisKey = $this->getBasicRedisHashKey();

            // 改造缓存方式
            $row = $this->clearIllegalParams($row);
            $redisObj->hSet($redisKey, $id, json_encode($row));
        }
    }

    /**
     * 获取列表缓存
     * @param string $functionName
     * @param $arguments
     * @return array|mixed
     */
    public function getListCache(string $functionName, $arguments)
    {
        $hashKey = md5(json_encode($arguments));
        $redisKey =  EsRedis::getBeanKeyPre($this->getBeanObj()->getTableName(), $functionName);

        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $rows = $redisObj->hGet($redisKey, $hashKey);
        $rows = unserialize($rows) ?? [];

        if (empty($rows)) {
            // key 是否存在
            if ($redisObj->hExists($redisKey, $hashKey)) {
                return null;
            }
        }

        return $rows;
    }

    /**
     * 给列表写入缓存
     * @param string $functionName
     * @param $arguments
     * @param array|null $rows
     */
    public function setListCache(string $functionName, $arguments, ?array $rows = [])
    {
        $redisKey =  EsRedis::getBeanKeyPre($this->getBeanObj()->getTableName(), $functionName);
        $hashKey = md5(json_encode($arguments));

        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $redisObj->hSet($redisKey, $hashKey, serialize($rows));
    }

    /**
     * 获取单条数据
     * @param int $id
     * @return object
     */
    final public function getById(?int $id): array
    {
        if (!isset($id)) {
            return [];
        }

        // 查询条件拼接 检测是否存在逻辑删除 存在则拼接上逻辑删除的条件
        $this->getDb()->where('id', $id);

        $logicDeleteField = $this->getLogicDeleteField();
        if ($logicDeleteField) {
            $this->getDb()->where($logicDeleteField, 0);
        }

        // 数据填充
        $fields =  $this->getBeanObj()->getFields();
        $row = $this->getDb()->getOne($this->getBeanObj()->getTableName(),Db::setFieldsGraveAccent($fields)) ?? [];
        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        return $row ?? [];
    }

    /**
     * 更新单条数据
     * @param int $id
     * @param array $params
     * @throws MysqlException
     */
    final public function updateByIds(array $ids, array $params)
    {
        // 看变量是否是该属性
        $params = BaseDao::clearIllegalParams($params);
        unset($params['id']);
        if (empty($params) || empty($ids) || ArrayUtility::emptyIds($ids)) {
            $code = 4005;
            throw new MysqlException($code);
        }

        // 自动加一些属性
        $params = BaseDao::autoWriteTime(AsaEsConst::MYSQL_AUTO_UPDATETIME, $params);
        $params = BaseDao::autoWriteUid(AsaEsConst::MYSQL_AUTO_UPDATEUSER, $params);

        // 开始更新
        $this->getDb()->where('id', $ids, 'IN');
        $flg = $this->getDb()->update($this->getBeanObj()->getTableName(), $params) ?? [];

        if (!$flg || 0 !== $this->getDb()->getLastErrno()) {
            $code = 4004;
            throw new MysqlException($code, $this->getDb()->getLastError());
        }

        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), $ids);
        $this->getDb()->saveLog(__FUNCTION__);
    }

    /**
     * 更新全表中某列等于某个值的所有数据.
     *
     * @param string $field
     * @param string $value
     *
     * @throws MysqlException
     */
    final public function updateByField(array $originalFieldValues, array $updateFieldValues): void
    {
        // 看变量是否是该属性
        $updateFieldValues = BaseDao::clearIllegalParams($updateFieldValues);
        if (Tools::superEmpty($originalFieldValues) || Tools::superEmpty($updateFieldValues)) {
            $code = 4020;
            throw new MysqlException($code);
        }

        // 先找到需要更新哪些条数据
        $tableName = $this->getBeanObj()->getTableName();
        foreach ($originalFieldValues as $field => $value) {
            $value = !is_array($value) ? [$value] : $value;
            $this->getDb()->where($field, $value, 'IN');
        }
        $rows = $this->getDb()->get($tableName, null, 'id');

        // 先查出id
        $ids = ArrayUtility::cols($rows, 'id') ?? [];
        if (empty($ids)) {
            return;
        }

        // 拼接最终更新的数据
        $params = [];
        $params = BaseDao::autoWriteTime(AsaEsConst::MYSQL_AUTO_UPDATETIME, $params);
        $params = BaseDao::autoWriteUid(AsaEsConst::MYSQL_AUTO_UPDATEUSER, $params);

        foreach ($updateFieldValues as $field => $value) {
            if (is_array($value)) {
                unset($params[$field]);
                continue;
            }
            $params[$field] = $value;
        }

        $this->getDb()->where('id', $ids, 'IN');
        $this->getDb()->update($tableName, $params);

        if (0 !== $this->getDb()->getLastErrno()) {
            $code = 4010;
            throw new MysqlException($code, $this->getDb()->getLastError());
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);

        // 批量更新缓存
        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), $ids);
    }

    /**
     * 插入单条数据
     * @param array $params
     */
    final public function insert(array $params) :int
    {
        // 看变量是否是该属性
        $params = BaseDao::clearIllegalParams($params);
        if (empty($params) || count($params) < 1) {
            $code = 4001;
            throw new MysqlException($code);
        }

        $params = BaseDao::autoWriteTime(AsaEsConst::MYSQL_AUTO_INSERTTIME, $params);
        $params = BaseDao::autoWriteUid(AsaEsConst::MYSQL_AUTO_INSERTUSER, $params);

        // 逻辑删除
        $logicDeleteField = $this->getLogicDeleteField($params);
        if ($logicDeleteField) {
            $params[$logicDeleteField] = 0;
        }

        $id = $this->getDb()->insert($this->getBeanObj()->getTableName(), $params);
        if ($id < 1 || 0 !== $this->getDb()->getLastErrno()) {
            $code = 4002;
            throw new MysqlException($code, $this->getDb()->getLastError());
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);

        // 不直接写缓存 是因为数据库会有默认值， 直接写会造成数据不同步

        // 安全起见 删除一下对应id的缓存
        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $redisKey =  $this->getBasicRedisHashKey();
        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), [$id]);
        return $id;
    }

    /**
     * 插入一组数据
     * @param array $params
     * @return array
     */
    final public function insertAll(array $params): array
    {
        if (empty($params)) {
            $code = 4012;
            throw new MysqlException($code);
        }

        $logicDeleteField = $this->getLogicDeleteField($params);
        foreach ($params as $key => $param) {
            $params[$key] = BaseDao::clearIllegalParams($param);
            $params[$key] = BaseDao::autoWriteTime(AsaEsConst::MYSQL_AUTO_INSERTTIME, $params[$key]);
            $params[$key] = BaseDao::autoWriteUid(AsaEsConst::MYSQL_AUTO_INSERTUSER, $params[$key]);

            // 逻辑删除
            if ($logicDeleteField) {
                $params[$key][$logicDeleteField] = 0;
            }
        }

        $ids = $this->getDb()->insertMulti($this->getBeanObj()->getTableName(), $params) ?? [];

        if (empty($ids) || !is_array($ids) ||  0 !== $this->getDb()->getLastErrno()) {
            $code = 4016;
            throw new MysqlException($code, $this->getDb()->getLastError());
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);

        // 安全起见 删除一下对应id的缓存
        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $redisKey =  $this->getBasicRedisHashKey();

        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), $ids);
        return  $ids;
    }

    /**
     * 按照某一列删除
     * @param string $field
     * @param $value
     * @return array
     */
    final public function deleteByField(array $fieldValues): void
    {
        if (empty($fieldValues)) {
            $code = 4009;
            throw new MysqlException($code);
        }

        // 先找到需要更新哪些条数据
        foreach ($fieldValues as $field => $value) {
            $value = !is_array($value) ? [$value] : $value;
            $this->getDb()->where($field, $value, 'IN');
        }
        $rows = $this->getDb()->get($this->getBeanObj()->getTableName(), null, 'id');

        // 先查出id
        $ids = ArrayUtility::cols($rows, 'id') ?? [];
        if (empty($ids)) {
            return;
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        $this->deleteByIds($ids);
        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), $ids);
    }

    /**
     * 删除单条数据
     * @param int $id
     */
    final public function deleteByIds(array $ids):void
    {
        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $pipe = $redisObj->multi(\Redis::PIPELINE);

        // 先删缓存 标注该条数据已删除 写入集合中 减少直接查库的可能 可能存在并发问题 后期可写入lua脚本
        foreach ($ids as $key => $id) {

            // 如果该数据已经被删除 就不需要再查数据库了
//            if ($this->basicIsDeleted($id)) {
//                unset($ids[$key]);
//                continue;
//            }

            $redisKey = $this->getBasicRedisHashKey();
            $pipe->hDel($redisKey, $id);
//            $pipe->sAdd(EsRedis::getBeanKeyPre($this->getBeanObj()->getTableName(), AsaEsConst::REDIS_BASIC_DELETED), $id);
        }
        $pipe->exec();

        // 都是空的话 就没有必要再走数据库
        if (empty($ids)) {
            return;
        }

        $this->getDb()->where('id', $ids, 'IN');

        $field = $this->getLogicDeleteField();
        if ($field) {
            // 走更新方法 自动加一些属性
            $params = [$field=>1];
            $params = BaseDao::autoWriteTime(AsaEsConst::MYSQL_AUTO_UPDATETIME, $params);
            $params = BaseDao::autoWriteUid(AsaEsConst::MYSQL_AUTO_UPDATEUSER, $params);

            // 开始更新
            $flg = $this->getDb()->update($this->getBeanObj()->getTableName(), $params) ?? [];
        } else {
            $flg = $this->getDb()->delete($this->getBeanObj()->getTableName());
        }

        if (!$flg || 0 !== $this->getDb()->getLastErrno()) {
            $code = 4013;
            throw new MysqlException($code);
        }
        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        RedisUtility::clearModuleCache($this->getBeanObj()->getTableName(), $ids);
    }

    /**
     * 查全部 团队内部调用.
     */
    final public function getAll(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = []): array
    {
        // 看变量是否是该属性
        $params = BaseDao::clearIllegalParams($params);

        // 是否存在空值 每个字段的查询分类
        foreach ($params as $field => $value) {
            if (Tools::superEmpty($value)) {
                $code = 1011;
                throw new MysqlException($code, $field.'的值不能为空');
            }

            // 默认是 等号 链接起来
            $searchType = $searchLinkType[$field]['search_type'] ?? 'IN';
            switch (strtoupper($searchType)) {

                // ARRAY 绝对等于 绝对不等 BETWEEN
                case 'NOT IN':
                    $value = !is_array($value) ? [$value] : $value;
                    $this->getDb()->where($field, $value, $searchType);
                    break;

                case 'BETWEEN':

                    if (!isset($value[0]) || !isset($value[1])) {
                        $code = 1011;
                        throw new MysqlException($code, $field.'字段的BETWEEN规则传递有误');
                    }

                    $this->getDb()->where($field, $value, $searchType);
                    break;

                // 单个值
                case '>':
                case '<':
                case '>=':
                case '<=':
                    $this->getDb()->where($field, [$searchType => $value]);
                    break;

                // 单个值
                case 'IS NOT':
                case 'IS':
                    $this->getDb()->where($field, null, $searchType);
                    break;
                default:
                    // 默认为in 防止抛错的
                    is_array($value) ? $this->getDb()->where($field, $value, $searchType) : $this->getDb()->where($field, $value);
            }
        }

        // 分组规则
        foreach ($groupBys as $fields) {
            $this->getDb()->groupBy($fields);
        }

        // 排序规则 如果为空则默认降序
        if (empty($orderBys)) {
            $orderBys = AppInfo::APP_DEFAULT_ORDER_BY;
        }

        foreach ($orderBys as $fields => $orderType) {
            $this->getDb()->orderBy($fields, $orderType);
        }

        // 逻辑删除
        $logicDeleteField = $this->getLogicDeleteField($params);
        if ($logicDeleteField) {
            $this->getDb()->where($logicDeleteField, 0);
        }

        // 如果分页没有传递 给一个默认分页
        if (empty($page)) {
            $page = [0,AppInfo::APP_PAGE_MAX];
        }

        $fields =  $this->getBeanObj()->getFields();
        $rows = $this->getDb()->get($this->getBeanObj()->getTableName(), $page,Db::setFieldsGraveAccent($fields)) ?? [];

        // 转成bean
        $data = [];
        foreach ($rows as $key => $row) {
            $data[] = $this->getBeanObj()->arrayToBean($row);
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        return $data;
    }

    /**
     * 搜索全部数据.
     */
    final public function searchAll(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = []): array
    {
        // 看变量是否是该属性
        $params = BaseDao::clearIllegalParams($params);

        $searchBinding = [];
        $whereKey = [];
        $whereValue = [];
        $whereOrSql = '';

        foreach ($params as $field => $value) {
            if (Tools::superEmpty($value)) {
                $code = 1011;
                throw new MysqlException($code, $field.'的值不能为空');
            }

            // 默认是 等号 链接起来
            $searchType = $searchLinkType[$field]['search_type'] ?? 'IN';
            $linkType = $searchLinkType[$field]['link_type'] ?? 'AND';

            if ($linkType == 'OR') {
                switch (strtoupper($searchType)) {

                    case 'LIKE':
                        $whereKey[] = "  {$field} LIKE ? ";
                        $whereValue[] = '%'."{$value}".'%';
                        break;

                    case 'LLIKE':
                        $whereKey[] = "  {$field} LIKE ? ";
                        $whereValue[] = '%'."{$value}";
                        break;

                    case 'RLIKE':
                        $whereKey[] = "  {$field} LIKE ? ";
                        $whereValue[] = "{$value}".'%';
                        break;

                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        $whereKey[] = "  {$field} {$searchType} ?  ";
                        $whereValue[] = $value;
                        break;

                    case 'NOT IN':
                        $whereKey[] = "  {$field} NOT IN (?)  ";
                        if (is_array($value) && !empty($value)) {
                            $whereValue[] = implode("','", $value);
                        } else {
                            $whereValue[] = $value;
                        }

                    // no break
                    case 'IS NOT':
                        $whereKey[] = "  {$field} IS NOT NULL  ";
                        break;

                    case 'IS':
                        $whereKey[] = "  {$field} IS NULL  ";
                        break;

                    case 'BETWEEN':
                        $value = !is_array($value) ? [$value] : $value;
                        if (!empty($value[0]) && !empty($value[1])) {
                            $whereKey[] = "  {$field} BETWEEN ? AND ? ";
                            $whereValue[] = $value[0];
                            $whereValue[] = $value[1];
                        }
                    // no break
                    default:
                        $whereKey[] = "  {$field} IN (?)  ";
                        if (is_array($value) && !empty($value)) {
                            $whereValue[] = implode("','", $value);
                        } else {
                            $whereValue[] = $value;
                        }
                }
            }

            if ($linkType == 'AND') {
                switch (strtoupper($searchType)) {

                    case 'NOT IN':
                    case 'BETWEEN':
                        $value = !is_array($value) ? [$value] : $value;
                        $this->getDb()->where($field, $value, $searchType);
                        break;

                    case 'LIKE':
                        $this->getDb()->where($field, "%{$value}%", "LIKE");
                        break;

                    case 'LLIKE':
                        $this->getDb()->where($field, "%{$value}", "LIKE");
                        break;

                    case 'RLIKE':
                        $this->getDb()->where($field, "{$value}%", "LIKE");
                        break;

                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        $this->getDb()->where($field, [$searchType => $value]);
                        break;

                    case 'IS NOT':
                    case 'IS':
                        $this->getDb()->where($field, null, $searchType);
                        break;
                    default:
                        is_array($value) ? $this->getDb()->where($field, $value, $searchType) : $this->getDb()->where($field, $value);
                }
            }
        }

        $whereOrSql = implode('OR', $whereKey) ?? null;

        if (!empty($whereOrSql)) {
            $this->getDb()->where("({$whereOrSql})", $whereValue);
        }

        // 分组规则
        foreach ($groupBys as $fields) {
            $this->getDb()->groupBy($fields);
        }

        // 排序规则 如果为空则默认降序
        if (empty($orderBys)) {
            $orderBys = AppInfo::APP_DEFAULT_ORDER_BY;
        }
        // 排序规则
        foreach ($orderBys as $fields => $orderType) {
            $this->getDb()->orderBy($fields, $orderType);
        }

        // 逻辑删除
        $logicDeleteField = $this->getLogicDeleteField($params);
        if ($logicDeleteField) {
            $this->getDb()->where($logicDeleteField, 0);
        }

        // 如果分页没有传递 给一个默认分页
        if (empty($page)) {
            $page = [0,AppInfo::APP_PAGE_DEFAULT_NUM];
        }

        $fields =  $this->getBeanObj()->getFields();
        $rows = $this->getDb()->get($this->getBeanObj()->getTableName(), $page, Db::setFieldsGraveAccent($fields)) ?? [];

        // 转成bean
        $data = [];
        foreach ($rows as $key => $row) {
            $data[] = $this->getBeanObj()->arrayToBean($row);
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        return $data;
    }

    /**
     * @return bool
     */
    public function isAutoCache(): bool
    {
        return $this->autoCache;
    }

    /**
     * @param bool $autoCache
     */
    public function setAutoCache(bool $autoCache): void
    {
        $this->autoCache = $autoCache;
    }

    /**
     * @return mixed
     */
    public function getBeanObj()
    {
        return $this->beanObj;
    }

    /**
     * @return Bean
     */
    public function setBeanObj($beanObj)
    {
        $this->beanObj = $beanObj;
    }

    /**
     * @return null|string
     */
    final public function getDb($dbType = AsaEsConst::DI_MYSQL_DEFAULT) :EsMysqliDb
    {
        $db = Di::getInstance()->get($dbType);
        return $db;
    }

    /**
     * 自动写入时间
     * @param string $a
     * @param array $params
     */
    final protected function autoWriteTime(array $autoType, array $params)
    {
        foreach ($autoType as $field) {
            if (property_exists($this->getBeanObj(), $field)) {
                // 外部传入就不走默认值
                $params[$field] = $params[$field] ?? Time::getNowDataTime();
            }
        }
        return $params;
    }

    /**
     * 用户
     * @param string $a
     * @param array $params
     */
    final protected function autoWriteUid(array $autoType, array $params)
    {
        // 是否是http方式运行
        if (!Env::isHttp()) {
            return $params;
        }

        // 获取当前用户uid
        $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $tokenObj = $esRequest->getTokenObj();

        foreach ($autoType as $field) {
            if (property_exists($this->getBeanObj(), $field)) {
                // 外部传入就不走默认值
                $params[$field] = $params[$field] ??  $tokenObj->uid;
            }
        }
        return $params;
    }

    /**
     * 清空非法变量
     */
    final protected function clearIllegalParams(array $params):array
    {
        foreach ($params as $field => $value) {
            if (!property_exists($this->getBeanObj(), $field)) {
                unset($params[$field]);
            }
        }
        return $params;
    }

    /**
     * 查询sql.
     *
     * @param string $sql    执行的sql语句
     * @param array  $param  相关参数
     * @param bool   $isMore
     *
     * @return mixed
     *
     * @throws MysqlException
     */
    protected function querySql(string $sql, array $param = null, bool $isOne = false)
    {
        if (!$isOne) {
            $result = $this->getDb()->rawQuery($sql, $param);
        } else {
            $result = $this->getDb()->rawQueryOne($sql, $param);
        }

        if (0 !== $this->getDb()->getLastErrno()) {
            throw new MysqlException(4017, $this->getDb()->getLastError());
        }

        // 记录log
        $this->getDb()->saveLog(__FUNCTION__);
        return $result;
    }

    /**
     * 基础数据是否已经被删除
     */
//    public function basicIsDeleted(?int $id) :bool
//    {
//        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
//        return false;
//        return (bool)$redisObj->sIsMember(EsRedis::getBeanKeyPre($this->getBeanObj()->getTableName(), AsaEsConst::REDIS_BASIC_DELETED), $id);
//    }

    /**
     * 获取基础数据操作redis key
     */
    private function getBasicRedisHashKey() :string
    {
        return EsRedis::getBeanKeyPre($this->getBeanObj()->getTableName(), AsaEsConst::REDIS_BASIC_TYPE);
    }

    /**
     * 获取逻辑删除列的名称
     * @return string
     */
    private function getLogicDeleteField(?array $params = []) :?string
    {
        $logicDeleteField = null;
        foreach (AsaEsConst::MYSQL_AUTO_LOGICDELETE as $field) {
            // 存在删除属性 并且外部没有传入该属性 才不带 否则带
            if (property_exists($this->getBeanObj(), $field)  && !isset($params[$field])) {
                $logicDeleteField = $field;
            }
        }

        return $logicDeleteField;
    }
}