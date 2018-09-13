<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Base;

use App\AppConst\AppInfo;
use App\AppConst\MysqlGroupBys;
use App\AppConst\MysqlOrderBys;
use App\Module\Logistics\Bean\LogisticsBean;
use AsaEs\AsaEsConst;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\MsgException;
use AsaEs\Exception\Service\SignException;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
use AsaEs\Utility\Token;
use AsaEs\Utility\Tools;
use AsaEs\Utility\View;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use ReflectionClass;

class BaseController extends Controller
{
    /**
     * @var service
     */
    protected $serviceObj;

    /**
     * @var validate
     */
    protected $validate;

    /**
     * 获取单条数据
     */
    public function getById()
    {
        $results = new Results();
        $id = $this->request()->getQueryParam('id');

        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_OBJ);
        $results->set($moduleObjName, $this->getServiceObj()->getById($id));

        Web::setBody($this->response(), $results);
    }

    /**
     * 更新单条数据
     */
    public function updateById()
    {
        $results = new Results();
        $id = $this->request()->getRequestParam('id');
        $params = $this->getRequestJsonParam();

        $vData = $this->getValidate()->getBindingField($params);
        $this->getValidate()->verify(__FUNCTION__, $vData);

        // 先查一下
        $obj = $this->getServiceObj()->getById($id);
        $id = $obj->getId() ?? null;

        // 数据是否存在
        if(!isset($id)){
            throw new MsgException(1009);
        }

        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_OBJ);
        $this->getServiceObj()->updateByIds([$id], $params);

        $results->set($moduleObjName, $this->getServiceObj()->getById($id));
        Web::setBody($this->response(), $results);
    }

    /**
     * 删除单条数据
     */
    public function deleteById()
    {
        $results = new Results();
        $id = $this->request()->getRequestParam('id');

        // 先查一下
        $obj = $this->getServiceObj()->getById($id);
        $id = $obj->getId() ?? null;

        // 数据是否存在
        if(!isset($id)){
            throw new MsgException(1009);
        }

        $this->getServiceObj()->deleteByIds([$id]);
        Web::setBody($this->response(), $results);
    }

    /**
     * 插入单条数据
     */
    public function insert()
    {
        $results = new Results();
        $params = $this->getRequestJsonParam();
        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_OBJ);

        // 默认的规则校验
        $vData = $this->getValidate()->getBindingField($params);
        $this->getValidate()->verify(__FUNCTION__, $vData);

        $id = $this->getServiceObj()->insert($params);
        $results->set($moduleObjName, $this->getServiceObj()->getById($id));
        Web::setBody($this->response(), $results);
    }

    /**
     * 列表查询
     */
    public function index()
    {
        $results = new Results();
        $params = $this->request()->getRequestParam();

        $searchLinkType = $this->getValidate()->getSearchParam();
        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_LIST);

        $list = $this->getServiceObj()->searchAll($params, $searchLinkType, $this->getPageParam(), [], []);

        $results->set($moduleObjName, $list);
        Web::setBody($this->response(), $results);
    }

    final protected function onException(\Throwable $throwable, $actionName): void
    {
        if ($throwable->getPrevious() instanceof BaseException) {

            Web::failBody($this->response(), new Results(), $throwable->getCode(), $throwable->getMessage());
        } else {
            throw $throwable;
        }
    }

    /**
     * 获取分页
     * @return array
     */
    final protected function getRequestJsonParam() :array
    {
        $rawData = $this->request()->getSwooleRequest()->rawContent();
        return json_decode((string)$rawData, true) ?? [];
    }

    public function getPageParam():array
    {
        $pageNo = $this->request()->getQueryParam(AsaEsConst::PAGE_KEY) ?? 0;
        $pageNum = $this->request()->getQueryParam(AsaEsConst::PAGE_NUM_KEY) ?? AppInfo::APP_PAGE_DEFAULT_NUM;
        return View::pageParam(intval($pageNo), intval($pageNum));
    }

    /**
     * 去掉命名空间获取类名对象(全小写).
     */
    final public function getModuleResultsName(string $className, string $type = AsaEsConst::RESULTS_RETURN_TYPE_OBJ): string
    {
        $className = explode('\\', $className);
        $resultsName = (string) end($className);

        return strtolower($resultsName.'_'.$type);
    }

    public function onRequest($action): ?bool
    {
        try {
            // 解token
            $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $tokenStr = $esRequest->getHeaderToken();
            $tokenObj = Token::decode($tokenStr);

            return true;
        } catch (\Exception $e) {
            throw new SignException(3002);
            return false;
        }
    }

    /**
     * @return validate
     */
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * @param validate $validate
     */
    public function setValidate($validate): void
    {
        $this->validate = $validate;
    }

    /**
     * @return mixed
     */
    public function getServiceObj()
    {
        return $this->serviceObj;
    }

    /**
     * @param mixed $serviceObj
     */
    public function setServiceObj($serviceObj): void
    {
        $this->serviceObj = $serviceObj;
    }

    /**
     * 获取列表操作项判断
     *
     * @param string $name     配置文件中名称
     * @param bool   $rDefault 当role_id不存在时都显示还是都不显示
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getListOperation(String $name, bool $rDefault = true): array
    {
//        $reflect = new ReflectionClass(get_class(new ListOperation()));
//        $name = empty($name) ? 'DEFAULT' : strtoupper($name);
//        $operations = $reflect->getConstant($name);
//        if (false === $operations || !is_array($operations)) {
//            return [];
//        }
//
//        //根据TOKEN获取rid
//        $token = Di::getInstance()->get(AppInfo::APP_TOKEN)->getToken();
//        $rId = $token->rid;
//        $data = [];
//        foreach ($operations as $k => $operation) {
//            if ($rDefault) {
//                if (!isset($operation['role_id']) || !empty(array_intersect($operation['role_id'], $rId))) {
//                    unset($operation['role_id']);
//                    $data[] = $operation;
//                }
//            } else {
//                if (isset($operation['role_id']) && !empty(array_intersect($operation['role_id'], $rId))) {
//                    unset($operation['role_id']);
//                    $data[] = $operation;
//                }
//            }
//        }
//
//        return $data;
    }
}
