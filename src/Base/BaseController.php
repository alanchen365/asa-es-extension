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
use App\AppConst\RpcConst;
use App\Module\Logistics\Bean\LogisticsBean;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\MsgException;
use AsaEs\Exception\Service\SignException;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
use AsaEs\Sdk\Baseservice\RbacService;
use AsaEs\Utility\ExceptionUtility;
use AsaEs\Utility\ObjectUtility;
use AsaEs\Utility\Request;
use AsaEs\Utility\Token;
use AsaEs\Utility\Tools;
use AsaEs\Utility\View;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Message\Status;
use ReflectionClass;
use think\validate\ValidateRule;

class BaseController extends Controller
{
    use View;

    /**
     * @var service
     */
    protected $serviceObj;

    /**
     * @var validate
     */
    protected $validate;

    /**
     * @var searchObj
     */
    protected $searchObj;


    /**
     * 获取单条数据
     */
    public function getById()
    {
        $results = new Results();
        $id = $this->request()->getQueryParam('id');

        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_OBJ);
        $obj = ObjectUtility::objectEmpty($this->getServiceObj()->getById($id));
        $results->set($moduleObjName, $obj);

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
        if (!isset($id)) {
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
        if (!isset($id)) {
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
        return $this->pageParam(intval($pageNo), intval($pageNum));
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
     * @return searchObj
     */
    public function getSearchObj(): searchObj
    {
        return $this->searchObj;
    }

    /**
     * @param searchObj $searchObj
     */
    public function setSearchObj(searchObj $searchObj): void
    {
        $this->searchObj = $searchObj;
    }

    /**
     * 是否需要鉴权
     * @return bool
     */
    protected function isAuth():bool{

        $isAuth = true;

        // 是否开启鉴权
        if (!AppInfo::APP_TOKEN_AUTH_SWITCH) {
            $isAuth = true;
            return true;
        }

        // 路由鉴权
        $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);

        // 路由白名单
        $server = $esRequest->getSwooleRequest();
        $requestUri = $server['server']['request_uri'];

        // 不鉴权域名
        $whitelistsRoute = Config::getInstance()->getConf("auth.ROUTE_WHITE_LIST", true) ?? [];

        $isAuth = true;
        foreach ($whitelistsRoute as $url) {
            $tmpUrl = substr($requestUri, 0, strlen($url));
            if ($tmpUrl === $url && !empty($url)) {
                $isAuth = false;
            }
        }

        return $isAuth;
    }

    /**
     * token 类型的鉴权
     * @param string $tokenType
     * @return bool
     * @throws SignException
     */
    protected function tokenTypeAuth(string $tokenType):bool {

        // 鉴权
        $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $tokenObj = $esRequest->getTokenObj();

        $tokenTypeConf = array_flip(AsaEsConst::TOKEN_TYPE);
        if($tokenType != $tokenTypeConf[$tokenObj->token_type]){
            throw new SignException(3003);
        }

        return true;
    }

    public function onRequest($action): ?bool
    {
        // 判断当前url是否需要鉴权
        $isAuth = $this->isAuth();

        // 需要鉴权
        if ($isAuth) {

            $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $tokenStr = $esRequest->getHeaderToken() ?? '';
            if (!$tokenStr) {
                $this->response()->withStatus(Status::CODE_UNAUTHORIZED);
                $this->response()->end();
                return false;
            }

            // 决定是否走远程鉴权 如果开启RBAC的鉴权模式 就走远程
            $rpcConstNamespace = "App\AppConst\RpcConst";
            $rbacConstNamespace = 'AsaEs\Sdk\Baseservice\RbacService';
            if(class_exists($rpcConstNamespace) && class_exists($rbacConstNamespace)){
                $rpcConst= new \ReflectionClass($rpcConstNamespace);
                $rbacService = new \ReflectionClass($rbacConstNamespace);

                $rpcConf = $rpcConst->getConstant('BUSINESSLOG_RRC_SERVICE_CONF');
                $isRpc = $rpcConf['enable'] ?? false;

                if(!$isRpc){
                    return false;
                }
                $tokenObj = (object)$rbacConstNamespace::jwtDecode($tokenStr,false);
            }else{
                $tokenObj =  Token::decode($tokenStr);
            }

            $esRequest->setTokenObj($tokenObj);
        }

        return true;
    }
}
