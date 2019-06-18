<?php

namespace AsaEs\Db;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Env;
use AsaEs\Utility\Time;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;
use think\validate\ValidateRule;

class EsMysqliDb extends \MysqliDb
{
    public function __construct($host = null, $username = null, $password = null, $db = null, $port = null, $charset = 'utf8', $socket = null)
    {
        parent::__construct($host, $username, $password, $db, $port, $charset, $socket);
    }

    /**
     * @return bool
     */
    public function isTransactionInProgress(): bool
    {
        return $this->_transaction_in_progress;
    }

    /**
     * @param bool $transaction_in_progress
     */
    public function setTransactionInProgress(bool $transaction_in_progress): void
    {
        $this->_transaction_in_progress = $transaction_in_progress;
    }

    /**
     * 记录log
     */
    public function saveLog(?string $actionName):void
    {
        // 唯一请求id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $requestId =  Env::isHttp() ? $requestObj->getRequestId() : "cli_running";

        $saveData = [

            'request_id' => $requestId,
            'database_query' => $this->getLastQuery(),
        ];

        // 本机和开发环境 打印SQL
        if (Config::getInstance()->getConf('DEBUG')) {
            $env = Config::getInstance()->getEnv();
            $nowDate = Time::getNowDataTime();
            if ($env == "LOCAL" || $env == "DEVELOP") {
                echo "\n==================== {$actionName} {$nowDate} ====================\n";
                echo $this->getLastQuery()."\n";
                echo "==================== {$actionName} {$nowDate} ====================\n";
            }
        }

        // 如果是http 就走异步记录
        if (Env::isHttp()) {
            // 异步写文件
            TaskManager::async(function () use ($saveData) {
                FileLogger::getInstance()->log(json_encode($saveData), AsaEsConst::LOG_MYSQL_QUERY);
            });
            return;
        }

        FileLogger::getInstance()->log(json_encode($saveData), AsaEsConst::LOG_MYSQL_QUERY);
    }
}
