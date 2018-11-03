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

class EsMysqliDb
{
    private static $dbInstance;

    /**
     * 需要保存日志的配置 可在外部覆写
     * @var array
     */
    protected $saveLog = ['get','getOne','update','delete','insert','rawQuery','rawQueryOne'];

    /**
     * 链接到哪个数据库
     * @var string
     */
    protected $dbType;

    public function __construct($dbType)
    {
        $this->dbType = $dbType;
    }

    /**
     * @return array
     */
    public function getSaveLog(): array
    {
        return $this->saveLog;
    }

    /**
     * @param array $saveLog
     */
    public function setSaveLog(array $saveLog): void
    {
        $this->saveLog = $saveLog;
    }

    /**
     * @param string $dbType
     */
    public function setDbType(string $dbType): void
    {
        $this->dbType = $dbType;
    }

    public function __call($actionName, $arguments)
    {
        self::$dbInstance =  self::$dbInstance ?? Di::getInstance()->get($this->dbType);
        if (self::$dbInstance instanceof \MysqliDb) {
            $ref = new \ReflectionClass(\MysqliDb::class);
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic()) {
                $return = call_user_func_array([self::$dbInstance,$actionName], $arguments);
                // 需要记log的方法 TODO 换成O1

                if (in_array($actionName, $this->saveLog)) {
                    $this->__hook($actionName, $arguments);
                }
                return $return;
            }
        }
    }

    protected function __hook(?string $actionName, $argumentsr):void
    {
        // 唯一请求id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $requestId =  Env::isHttp() ? $requestObj->getRequestId() : "cli_running";

        $saveData = [
            'request_id' => $requestId,
            'database_query' => self::$dbInstance->getLastQuery(),
        ];

        // 本机和开发环境 打印SQL
        if (Config::getInstance()->getConf('DEBUG')) {
            $env = Config::getInstance()->getEnv();
            $nowDate = Time::getNowDataTime();
            if ($env == "LOCAL" || $env == "DEVELOP") {
                echo "\n==================== {$actionName} {$nowDate} ====================\n";
                var_dump(self::$dbInstance->getLastQuery());
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
