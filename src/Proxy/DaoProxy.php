<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午12:54
 */
namespace AsaEs\Proxy;

use AsaEs\AsaEsConst;
use AsaEs\Base\BaseDao;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use EasySwoole\Core\Swoole\ServerManager;
use think\validate\ValidateRule;

class DaoProxy
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    public function __call($actionName, $arguments)
    {
        if ($this->class instanceof BaseDao) {

            /**
             * 获取单条转发
             */
            if ($actionName == 'getOneByField') {
                $rows = call_user_func_array([$this->class,'getALl'], $arguments);
                return $rows[0] ?? null;
            }

            // 方法是否存在
            $ref = new \ReflectionClass($this->class);
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic()) {

                /**
                 * get by id
                 */
                if ($actionName == 'getById') {
                    // 如果开启了缓存
                    if ($this->class->isAutoCache()) {

                        // 缓存是否命中
                        $cacheRow = call_user_func_array([$this->class,'getByIdCache'], $arguments);

                        if (!empty($cacheRow)) {
                            return $this->getClass()->getBeanObj()->arrayToBean($cacheRow);
                        }

                        // 如果数据已被删除
                        $isDelete = call_user_func_array([$this->class,'basicIsDeleted'], $arguments);
                        if($isDelete){
                            return $this->getClass()->getBeanObj()->arrayToBean([]);
                        }
                    }

                    // 走数据库
                    $dbRow = call_user_func_array([$this->class,'getById'], $arguments);
                    $newBeanObj =  $this->class->getBeanObj()->arrayToBean($dbRow);

                    if ($this->class->isAutoCache()) {
                        // 保存缓存
                        if (!empty($dbRow)) {
                            call_user_func_array([$this->class,'setByIdCache'], [$newBeanObj->getId(),$newBeanObj->toArray()]);
                        }
                    }

                    return $newBeanObj;
                }

                /**
                 * get all
                 */
                if ($actionName == 'searchAll' || $actionName == 'getAll') {
                    $functionName;
                    if ($actionName == 'searchAll') {
                        $functionName = AsaEsConst::REDIS_BASIC_SEARCH_ALL;
                    } else {
                        $functionName = AsaEsConst::REDIS_BASIC_GET_ALL;
                    }

                    // 先在缓存中查
                    if ($this->class->isAutoCache()) {
                        $cacheList = call_user_func_array([$this->class,'getListCache'], [$functionName,$arguments]);

                        if (!empty($cacheList)) {
                            $nCacheList = [];
                            // 动态转bean下 这里是为了防止bean中的数据不更新
                            foreach ($cacheList as $key => $cacheObj){
                                $newBeanObj =  $this->class->getBeanObj()->arrayToBean($cacheObj->toArray());
                                $nCacheList[] = $newBeanObj;
                            }
                            return $nCacheList;
                        }
                    }

                    // 走数据库
                    $dbList = call_user_func_array([$this->class,$actionName], $arguments);

                    // 保存到数据库
                    if ($this->class->isAutoCache()) {
                        call_user_func_array([$this->class,'setListCache'], [$functionName,$arguments,$dbList]);
                    }

                    return $dbList;
                }

                /**
                 * 如果走了如下方法 将缓存清空（基础数据列表缓存）
                 */
                $cacheFunction = [
                    'updateByIds','updateByField','insert','insertAll','deleteByField','deleteByIds'
                ];
                if (ArrayUtility::arrayFlip($cacheFunction, $actionName)) {
                    // 清空缓存 基础数据列表缓存
                    call_user_func_array([$this->class,'delListCache'], [$actionName]);
                }

                return call_user_func_array([$this->class,$actionName], $arguments);
            }
        }
    }
}
