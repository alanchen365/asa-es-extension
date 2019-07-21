<?php

namespace AsaEs\Utility;

use App\Utility\Log;
use AsaEs\AsaEsConst;
use AsaEs\Output\Msg;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;

class Env
{

    /**
     * 判断程序运行方式是否为HTTP
     */
    public static function isHttp()
    {
        // work_id
        $workId = ServerManager::getInstance()->getServer()->worker_id ?? -1;
        // 唯一请求id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ) ?? null;

        if ($workId  < 0 || !is_object($requestObj)) {
            return false;
        }

        return true;
    }

    /**
     * 自动删除日志
     */
    public static function clearLog(string $logPath,float $day,array $filter = []){

        if (is_dir($logPath)) {    //判断是否是目录
            $p=scandir($logPath);     //获取目录下所有文件
            foreach ($p as $value) {
                if ($value != '.' && $value != '..') {    //排除掉当./和../
                    if (is_dir($logPath.'/'.$value)) {
                        Env::clearLog($logPath.'/'.$value,$day,$filter);    //递归调用删除方法
//                        rmdir($path.'/'.$value);    //删除当前文件夹
                    }else{
                        // 超过多少天的日志删除
                        $fileString = $logPath.'/'.$value;
                        if ((time() - filectime($fileString)) >$day){
                            // 排除文件
                            if(ArrayUtility::arrayFlip($filter,$value)){
                                continue;
                            }
                        }

                        unlink($fileString);
                    }
                }
            }
        }
    }
}
