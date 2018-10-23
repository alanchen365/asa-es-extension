<?php

namespace AsaEs\RemoteCall;

use App\Utility\Exception\CurlException;
use EasySwoole\Core\Utility\Curl\Response;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Field;

class Curl
{
    public function __construct()
    {
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     */
    public function request(string $method, string $url, array $params = null): Response {
        try {
            $request = new Request($url);

            switch ($method) {
                case 'GET':
                    if ($params && isset($params['query'])) {
                        foreach ($params['query'] as $key => $value) {
                            $request->addGet(new Field($key, $value));
                        }
                    }
                    break;

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
            return $request->exec();
        } catch (\Exception $e) {
            throw new \AsaEs\Exception\Service\CurlException($e->getCode(), $e->getMessage());
        }
    }
}
