<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Base;

use App\AppConst\MysqlGroupBys;
use App\AppConst\MysqlOrderBys;
use App\Module\Logistics\Bean\LogisticsBean;
use AsaEs\AsaEsConst;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\Service\SignException;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
use AsaEs\Utility\Token;
use AsaEs\Utility\Tools;
use AsaEs\Utility\View;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class BaseController extends Controller
{
    /**
     * @var service
     */
    protected $serviceObj;

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

        // TODO
        if (empty($params)) {
            // 抛出参数错误异常
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

        // TODO
        if (empty($params)) {
            // 抛出参数错误异常
        }

        $id = $this->getServiceObj()->insert($params);
        $results->set($moduleObjName, $this->getServiceObj()->getById($id));
        Web::setBody($this->response(), $results);
    }

    /**
     * 根据某列删除数据 （内部）
     */
    public function deleteByField()
    {
        $results = new Results();

        $this->getServiceObj()->deleteByField('name', ['zhangsan']);
        Web::setBody($this->response(), $results);
    }

    /**
     * 列表查询
     */
    public function index()
    {
        $results = new Results();
        $params = $this->request()->getRequestParam();

        $searchLinkType = [
            'name' => [
                'link_type' => 'AND',
                'search_type' => 'LIKE',
            ],
        ];

        $moduleObjName = $this->getModuleResultsName(get_called_class(), AsaEsConst::RESULTS_RETURN_TYPE_LIST);
        $list = $this->getServiceObj()->searchAll($params, $searchLinkType, $this->getPageParam(), MysqlOrderBys::LOGISTICS_LOGISTICS, MysqlGroupBys::LOGISTICS_LOGISTICS);
//        $list = $this->getServiceObj()->getAll($params, $searchLinkType, $this->getPageParam(), MysqlOrderBys::LOGISTICS_LOGISTICS, MysqlGroupBys::LOGISTICS_LOGISTICS);

        $results->set($moduleObjName, $list);
        $results->set(AsaEsConst::RESULTS_RETURN_OPTION_LIST, get_called_class());
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
        $pageNum = $this->request()->getQueryParam(AsaEsConst::PAGE_NUM_KEY) ?? 0;
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

    public function onRequest($action): ?bool
    {
        try {
            // 解token
            $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $tokenStr = $esRequest->getHeaderToken();
            $tokenObj = Token::decode($tokenStr);

            var_dump($tokenObj);
            return true;
        } catch (\Exception $e) {
            throw new SignException(3002);
            return false;
        }
    }
}
