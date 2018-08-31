<?php

namespace AsaEs\Utility;

use AsaEs\Config;

/**
 * 获取当前框架环境
 * Class Env
 * @package AsaEs\Utility
 */
class Env
{
    /**
     * 开发环境 本机(LOCAL) 开发服务器(DEVELOP) 测试服务器(TESTING) 生产服务器(PRODUCTION)
     * @return string
     */
    public static function get():string {
        return strtoupper(\EasySwoole\Config::getInstance()->getConf('ENV'));
    }
}
