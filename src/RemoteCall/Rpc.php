<?php

namespace AsaEs\RemoteCall;

use App\AppConst\RpcConst;
use AsaEs\Exception\Service\CurlException;
use AsaEs\Exception\Service\RpclException;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Utility\Curl\Response;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Field;

class Rpc
{
    protected $isIgnoreErr = false;

    protected $client = null;

    protected $results = null;

    public function __construct(array $data = null, $autoCreateProperty = false,$isIgnoreErr = true)
    {
        $ServiceManager = \EasySwoole\Core\Component\Rpc\Server::getInstance();
        $ServiceManager->updateServiceNode(new \EasySwoole\Core\Component\Rpc\Common\ServiceNode($data,$autoCreateProperty));
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     */
    public function request(string $serviceName,string $serviceGroup,string $action, $args)
    {
        try {
            $client = new \EasySwoole\Core\Component\Rpc\Client();
            $client->addCall($serviceName,$serviceGroup,$action,json_encode($args ?? []))
                ->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){

                    if(!$this->isIgnoreErr()){
                        $status = $response->getStatus();
                        throw new RpclException($status,'远程服务调用失败');
                    }
                ;})
                ->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
                    $this->setResults($response->getArgs());
                });

            // 开始调用
            $client->call();

            // 返回
            return $this->getResults();
//            return $responseObj;
        } catch (\Exception $e) {
            throw new RpclException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @return null
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param null $results
     */
    public function setResults($results): void
    {
        $this->results = $results;
    }

    
    
    /**
     * @return bool
     */
    public function isIgnoreErr(): bool
    {
        return $this->isIgnoreErr;
    }

    /**
     * @param bool $isIgnoreErr
     */
    public function setIsIgnoreErr(bool $isIgnoreErr): void
    {
        $this->isIgnoreErr = $isIgnoreErr;
    }
}