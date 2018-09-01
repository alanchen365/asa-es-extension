<?php

namespace AsaEs\Exception;

use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;
use AsaEs\Output\Results;
use AsaEs\Output\Web;
use EasySwoole\Config;
use EasySwoole\Core\Http\AbstractInterface\ExceptionHandlerInterface;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

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

        // 如果错误为空，拿着错误码去msg查一下
        if (empty($msg)) {
            $msg = Msg::get($exception->getCode());
            // 还为空的话 ， 就给个默认了
            if (empty($msg)) {
                $msg = '服务器竟然出现了错误,请联系管理员';
            }
        }

        // 要记录的异常信息
        $data = [
            'code' => $exception->getCode(),
            'msg' => $msg,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];

        FileLogger::getInstance()->log(json_encode($data), 'RUNNING_ERROR');

        if (\AsaEs\Config::getConf('DEBUG')) {
            $response->write(json_encode($data));
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->end();
            return;
        }

        Web::failBody($response, $results, $exception->getCode(), "服务器竟然出现了错误,请联系管理员");
    }
}