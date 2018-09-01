<?php

namespace AsaEs\Output;

use EasySwoole\Config;
use EasySwoole\Core\Http\Response;

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
            $results->setCode(empty($code) ? 100000 : $code);
            $data = $results->getData();
        } else {
            $results->setCode(empty($code) ? 0 : $code);

            $data = $results->getData();
        }

        $response->write(json_encode($data));
        $response->end();
        return;
    }

    public static function failBody(Response $response, Results $results, int $code = 0, string $msg = ''): void
    {
        self::setBody($response, $results, $code, $msg, false);
    }
}
