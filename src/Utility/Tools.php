<?php

namespace AsaEs\Utility;

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;

class Tools
{
    /**
     * 去掉命名空间获取类名对象
     */
    public static function getModelNameByClass(string $className): string
    {
        return str_replace('Dao', 'Bean', $className);
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
