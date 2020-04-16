<?php

namespace AsaEs\Utility;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use AsaEs\Config;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\Service\SignException;
use Firebase\JWT\JWT;

class Request
{
    /**
     * request_id
     */
    protected $requestId;

    /**
     * token
     */
    protected $headerToken;

    /**
     * token 对象
     */
    protected $tokenObj;


    /**
     * raw
     * @var
     */
    protected $rawContent;

    /**
     * @return mixed
     */
    public function getRawContent(bool $decode = true)
    {
        return json_decode($this->rawContent,true) ?? [];
    }

    /**
     * @param mixed $rawContent
     */
    public function setRawContent($rawContent): void
    {
        $this->rawContent = $rawContent;
    }


    /**
     *swoole_request
     */
    protected $swooleRequest;

    public function __construct($request = null)
    {
        if ($request instanceof \EasySwoole\Core\Http\Request) {
            // 生成请求id
            $this->setRequestId();
            // 记录swoole request
            $this->setSwooleRequest($request->getSwooleRequest());
            // 记录当时的token
            $tokenHeader = current($request->getHeader(AppInfo::APP_HEADER_TOKEN)) ?? '';
            // 写token
            $this->setHeaderToken($tokenHeader);
            // 获取raw
            $rawStr = $request->getSwooleRequest()->rawContent();
            $this->setRawContent($rawStr);
        }
    }


    /**
     * @param mixed $tokenObj
     */
    public function setTokenObj($tokenObj): void
    {
        $this->tokenObj = $tokenObj;
    }

    /**
     * @return mixed
     */
    public function getTokenObj()
    {
////        // 设置默认值 防止外部调用报错
//        if(Tools::superEmpty($this->tokenObj->uid ?? null)){
//
//            // 对象为空
//            if($this->tokenObj == null){
//                $this->tokenObj = new \stdClass();
//            }
//
//            $this->tokenObj->uid = null;
//        }

        return $this->tokenObj;
    }

    /**
     * 鉴权
     * @param mixed $headerToken
     */
    public function setHeaderToken($headerToken): void
    {
        $this->headerToken = $headerToken;
    }

    /**
     * @return mixed
     */
    public function getHeaderToken()
    {
        return $this->headerToken;
    }

    /**
     * @return mixed
     */
    public function getRequestId():string
    {
        return $this->requestId ?? 'cli_running';
    }

    /**
     * @param mixed $request_id
     */
    public function setRequestId(): void
    {
//        $this->requestId = Md5::toMd5(Random::randStr(16).time());
        $this->requestId = md5(uniqid(microtime(true), true));
    }

    /**
     * @return mixed
     */
    public function getSwooleRequest() :array
    {
        return $this->swooleRequest ?? [];
    }

    /**
     * @param mixed $swoole_request
     */
    public function setSwooleRequest($swoole_request): void
    {
        $this->swooleRequest = (array)$swoole_request;
    }
}
