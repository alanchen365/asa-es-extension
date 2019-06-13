<?php

namespace AsaEs\Output;


use AsaEs\Config;

class Msg
{
    /**
     * 返回值封装.
     *
     * @param int $code
     *
     * @return string
     */
    public static function get(int $code): string
    {
        $msgConfig = Config::getInstance()->getConf('msg');
        return $msgConfig[$code] ?? '';
    }
}
