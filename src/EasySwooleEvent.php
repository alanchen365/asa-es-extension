<?php

namespace AsaEs;

use AsaEs\Config\Router;
use AsaEs\Router\HttpRouter;

class EasySwooleEvent
{
    public static function frameInitialize(): void
    {
        // 时区设置
        date_default_timezone_set('Asia/Shanghai');
        // 载入路由
        HttpRouter::getInstance()->set();
        // 载入全部配置文件
    }
}