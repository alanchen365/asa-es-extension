<?php

namespace AsaEs\Utility;

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;

class Tools
{
    /**
     * 去掉命名空间获取类名对象(全小写).
     */
    public static function getModuleObjNameByClass(string $className, string $type = 'obj'): string
    {
        $className = explode('\\', $className);
        $objName = (string) end($className);

        return strtolower($objName.'_'.$type);
    }

    /**
     * 去掉命名空间获取类名对象
     */
    public static function getModelNameByClass(string $className): string
    {
        return str_replace('Service', 'Model', $className);
    }

    /**
     * 打印保存Log.
     *
     * @param $db
     * @param $name
     */
    public static function printAndSaveDbQueryLog($db, $name): void
    {
        // 如果是debug模式 打印sql
        $debug = strtoupper(Config::getInstance()->getConf('DEBUG'));
        if ($debug) {
            echo "\n==================== {$name} ====================\n";
            Logger::getInstance()->console($db->getLastQuery());
            echo "==================== {$name} ====================\n";
        } else {
            // 如果不是debug模式， 则保存sql
            Logger::getInstance()->log("\n".$db->getLastQuery()."\n", 'QUERY-SQL');
        }
    }

    /**
     * 判断一个变量是否为空，但不包括 0 和 '0'.
     *
     * @param $value
     *
     * @return bool 返回true 说明为空 返回false 说明不为空
     */
    public static function superEmpty($value): bool
    {
        // 如果是一个数组
        if (is_array($value)) {
            return empty($value) ? true : false;
        }

        // 如果是一个对象
        if (is_object($value)) {
            return empty($value->id) ? true : false;
        }

        // 如果是其它
        if (empty($value)) {
            if (is_int($value) && 0 === $value) {
                return false;
            }

            if (is_string($value) && '0' === $value) {
                return false;
            }

            return true;
        }

        return false;
    }
}
