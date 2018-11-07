<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;

class Db
{

    /**
     * 开启事物
     */
    public static function startTransaction(string $dbType = AsaEsConst::DI_MYSQL_DEFAULT)
    {
        return Di::getInstance()->get($dbType)->startTransaction();
    }
    
    /**
     * 提交事物
     */
    public static function commit(string $dbType = AsaEsConst::DI_MYSQL_DEFAULT)
    {
        return Di::getInstance()->get($dbType)->commit();
    }

    /**
     * 回滚事物
     */
    public function rollback(string $dbType = AsaEsConst::DI_MYSQL_DEFAULT)
    {
        return Di::getInstance()->get($dbType)->rollback();
    }
}
