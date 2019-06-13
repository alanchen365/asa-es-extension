<?php

namespace AsaEs\Router;

use AsaEs\Config;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Utility\File;

class ProcessRoute
{

    public static function run()
    {
        // 注入老的进程
        if(method_exists(\App\Process\Router::class,"run")){
            \App\Process\Router::run();
        }
        
        // 动态获取路由
        $path = EASYSWOOLE_ROOT."/App/Module";
        $files = File::scanDir($path,File::TYPE_DIR) ?? [];

        // 获取路由文件下所有目录
        foreach ($files as $dir) {

            $tmp = explode('/',$dir) ?? [];
            $moduleName = end($tmp);

            if(empty($moduleName)){
                continue;
            }

            // 文件是否存在
            $processFile = $dir . "/Process/Route.php";
            if(!file_exists($processFile)){
                continue;
            }

            $moduleClass = '\App\Module\\'.$moduleName.'\Process\Route';
            $moduleClass::run();
        }
    }
}