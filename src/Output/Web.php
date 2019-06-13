<?php

namespace AsaEs\Output;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Web
{
    public static function setBody(Response $response, Results $results, int $code = 100000, string $msg = '', bool $successFlg = true): void
    {
        $response->withHeader('Content-type', 'application/json;charset=utf-8');

        // 返回值封装
        $msg = empty($msg) ? Msg::get($code) : $msg;
        $results->setMsg((string) $msg);

        // 成功逻辑
        if ($successFlg) {
            $results->setCode(empty($code) ? AppInfo::RESULTS_RETURN_SUCCES_CODE : $code);
            $data = $results->getData();
        } else {
            $results->setCode(empty($code) ? 0 : $code);
            $data = $results->getData();
        }

        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $saveData = [
            AsaEsConst::REQUEST_ID => $requestObj->getRequestId(),
            'swoole_http_request' => (array)$requestObj->getSwooleRequest(),
            'response_code' =>$code,
            'response_msg' => $msg,
            'raw_data' => $requestObj->getRawContent(),
//                'response_body' => $data
        ];

        // 异步写文件
//        TaskManager::async(function () use ($saveData) {
        FileLogger::getInstance()->log(json_encode($saveData), AsaEsConst::LOG_REQUEST_RESPONSE);
//        });

        $response->withAddedHeader('request_id',$requestObj->getRequestId());
        $response->write(json_encode($data));
        $response->end();
        return;
    }

    public static function failBody(Response $response, Results $results, int $code = 0, string $msg = ''): void
    {
        self::setBody($response, $results, $code, $msg, false);
    }
}
