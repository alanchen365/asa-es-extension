<?php

namespace AsaEs\RemoteCall;

use AsaEs\Exception\Service\CurlException;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Utility\Curl\Response;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Field;

class Curl
{
    // 用户配置
    protected $config = [];
    // 是否合并配置
    protected $isMerge = true;
    // 是否忽略错误
    protected $isIgnoreErr = false;

    public function __construct(?array $config = [], ?bool $isMerge = true,?bool $isIgnoreErr = false)
    {
        $this->config = $config;
        $this->isMerge = $isMerge;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     */
    public function request(string $method, string $url, array $params = null): Response
    {
        try {
            $request = new Request($url);

            // 动态修改配置
            if (!Tools::superEmpty($this->getConfig())) {
                $request->setUserOpt($this->config, $this->isMerge);
            }

            switch ($method) {
                case 'GET':
                    if ($params && isset($params['query'])) {
                        foreach ($params['query'] as $key => $value) {
                            $request->addGet(new Field($key, $value));
                        }
                    }
                    break;
                case 'PUT':
                case 'POST':
                    if ($params && isset($params['form_params'])) {
                        foreach ($params['form_params'] as $key => $value) {
                            $request->addPost(new Field($key, $value));
                        }
                    } elseif ($params && isset($params['body'])) {
                        if (!isset($params['header']['Content-Type'])) {
                            $params['header']['Content-Type'] = 'application/json; charset=utf-8';
                        }
                        $request->setUserOpt([CURLOPT_POSTFIELDS => $params['body']]);
                    }
                    break;

                case 'DELETE':
                    $request->setUserOpt([CURLOPT_CUSTOMREQUEST => $method]);
                    break;
                default:
                    throw new \InvalidArgumentException('method eroor');
                    break;
            }

            if (isset($params['header']) && !empty($params['header']) && is_array($params['header'])) {
                foreach ($params['header'] as $key => $value) {
                    $string = "{$key}:$value";
                    $header[] = $string;
                }

                $request->setUserOpt([CURLOPT_HTTPHEADER => $header]);
            }

            // 错误判断
            $responseObj = $request->exec();
            $errNo = $responseObj->getErrorNo();
            $errMsg = $responseObj->getError();

            if (isset($errNo) && $errNo > 0 && !$this->isIgnoreErr) {
                throw new CurlException(2008, $errMsg);
            }

            return $responseObj;
        } catch (\Exception $e) {
            throw new CurlException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 获取配置
     */
    public function getConfig() :array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @param bool $isMerge
     */
    public function setIsMerge(bool $isMerge): void
    {
        $this->isMerge = $isMerge;
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
