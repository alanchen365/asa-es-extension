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
use AsaEs\Config;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\MsgException;
use AsaEs\Exception\Service\SignException;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
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
use EasySwoole\Core\Component\Rpc\AbstractInterface\AbstractRpcService;

class BaseRpcController extends AbstractRpcService
{

    public function index()
    {
        // TODO: Implement index() method.
    }

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
     * 获取分页
     * @return array
     */
    final protected function getRequestJsonParam() :array
    {
        $args = $this->serviceCaller();
         return json_decode($args->getArgs()[0] ?? '',true) ?? [];
//        $rawData = $this->request()->getSwooleRequest()->rawContent();
//        return json_decode((string)$rawData, true) ?? [];
    }

    public function getPageParam():array
    {
        $params = $this->getRequestJsonParam();
        $pageNo = $params[AsaEsConst::PAGE_KEY] ?? 0;
        $pageNum = $params[AsaEsConst::PAGE_NUM_KEY] ?? AppInfo::APP_PAGE_DEFAULT_NUM;
        
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
}
