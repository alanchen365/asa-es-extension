<?php

namespace AsaEs\Router;

use App\EsCrontab;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\Env;
use EasySwoole\Core\Component\Crontab\CronTab;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Utility\File;

class CrontabRoute
{

    public static function run()
    {
        // 注入老的定时任务
        if(method_exists(\App\EsCrontab::class,"run")){
            \App\EsCrontab::run();
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
            $crontabFile = $dir . "/Crontab/".$moduleName.'Crontab.php';;
            if(!file_exists($crontabFile)){
                continue;
            }

            $moduleClass = '\App\Module\\'.$moduleName.'\Crontab\\'.$moduleName.'Crontab';
            $moduleClass::run();
        }

//        //每日1点1分执行 定时删除日志脚本
//        CronTab::getInstance()->addRule('AutoClearLog', '1 1 * * *', function (){
//
//            $logDir = Config::getInstance()->getConf('LOG_DIR');
//
//            Env::clearLog($logDir,AsaEsConst::AUTO_CLEAR_LOG_DAY,AsaEsConst::AUTO_CLEAR_LOG_FILTER);
//        });
    }
}