<?php

namespace AsaEs\Exception;

use AsaEs\AsaEsConst;
use AsaEs\Logger\BaseLogger;
use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Env;
use AsaEs\Utility\ExceptionUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\ExceptionHandlerInterface;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

class SystemException implements ExceptionHandlerInterface
{
    /**
     * 所有异常都走这里.
     *
     * @param \Throwable $exception
     * @param Request    $request
     * @param Response   $response
     */
    public function handle(\Throwable $exception, Request $request, Response $response)
    {
        $results = new Results();
        $msg = $exception->getMessage();
        $code = $exception->getCode();

        // 如果错误为空，拿着错误码去msg查一下
        if (empty($msg)) {
            $msg = Msg::get($code);
            // 还为空的话 ， 就给个默认了
            if (empty($msg)) {
                $msg = '服务器异常,请稍后再试';
            }
        }

        // 获取异常信息
        $exceptionData = ExceptionUtility::getExceptionData($exception, $code, $msg);
        $env = $confVal = \AsaEs\Config::getInstance()->getEnv();
        if ($env == 'LOCAL' && \AsaEs\Config::getInstance()->getConf('DEBUG') && Env::isHttp()) {

            // 获取请求信息
            $exceptionData['request_data'] = BaseLogger::getRequestLogData();
            $response->write(json_encode($exceptionData ?? []));
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->end();
            return;
        }

        if(Env::isHttp()){
            Web::failBody($response, $results, $exception->getCode(), "服务器异常,请稍后再试");
        }

        FileLogger::getInstance()->log(json_encode($exceptionData), strtoupper("RUNNING_ERROR"));
    }
}
