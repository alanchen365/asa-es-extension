<?php

namespace AsaEs\Utility;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use AsaEs\Config;

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
            $this->setHeaderToken($tokenHeader);
        }
    }

    /**
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
        $this->requestId = Md5::toMd5(Random::randStr(16), time());
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
