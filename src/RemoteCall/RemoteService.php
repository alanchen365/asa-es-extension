<?php

namespace AsaEs\RemoteCall;

use App\AppConst\RpcConst;
use AsaEs\Config;
use AsaEs\Exception\MsgException;
use AsaEs\Exception\Service\CurlException;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Utility\Curl\Response;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Field;

/**
 * curl代理类
 * Class RemoteCallProxy
 * @package AsaEs\RemoteCall
 */
class RemoteService
{
    const REQUEST_WAY_CURL = 'Curl';    // curl
    const REQUEST_WAY_RPC = 'Rpc';    // rpc

    protected $isIgnoreErr = null; // 是否忽略错误
    protected $instance = null;     // 获取实例

    public function __construct(string $requestWay)
    {
        $this->setRequestWay($requestWay);
    }

    /**
     * @return null
     */
    public function getRequestWay()
    {
        return $this->requestWay;
    }

    /**
     * @param null $requestWay
     */
    public function setRequestWay($requestWay): void
    {
        $this->requestWay = $requestWay;
    }

    /**
     * @return null
     */
    public function getisIgnoreErr()
    {
        return $this->isIgnoreErr;
    }

    /**
     * @param null $isIgnoreErr
     */
    public function setIsIgnoreErr($isIgnoreErr): void
    {
        $this->isIgnoreErr = $isIgnoreErr;
    }

    // 代理
    public function __call($actionName, $arguments){

        if($actionName == 'getInstance'){

            // 如果是rpc
            if($this->getRequestWay() == RemoteService::REQUEST_WAY_RPC){
                $reflect  = new \ReflectionClass(Rpc::class);
            }

            // 如果是curl
            if($this->getRequestWay() == RemoteService::REQUEST_WAY_CURL){
                $reflect  = new \ReflectionClass(Curl::class);
            }

            $this->instance = $reflect->newInstanceArgs($arguments);
        }

        if($actionName == 'request'){

            $relusts = call_user_func_array([$this->instance,'request'], $arguments);

            // 如果是rpc
            if($this->getRequestWay() == RemoteService::REQUEST_WAY_RPC){
                $res = json_decode($relusts,true) ?? [];
            }

            // 如果是curl
            if($this->getRequestWay() == RemoteService::REQUEST_WAY_CURL){
                $res = json_decode($relusts->getBody(), true) ?? [];
            }

            $code = $res['code'] ?? -1;
            $msg = $res['msg'] ?? '第三方服务连接失败';
            $result = $res['result'] ?? [];

            // 不忽略错误
            if(!$this->getisIgnoreErr()){
                if ($code < 100000) {
                    if(Config::getInstance()->getDebug()){
                        // 打印错误

                        echo "====== 第三方返回结果开始 ======\n";
                        var_dump($res);
                        echo "====== 第三方返回结果结束 ======\n";

                    }
                    throw new MsgException($code,$msg);
                }
            }

            return $result;
        }
    }
}
