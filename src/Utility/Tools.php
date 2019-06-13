<?php

namespace AsaEs\Utility;

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;
use PhpParser\Node\Expr\Isset_;

class Tools
{
    /**
     * 获取命名空间最后一位
     * @param string $nameSpace
     * @return string
     */
    public static function getLastNameSpaceName(string $nameSpace) :?string
    {
        $nameSpaceArr = explode('\\', $nameSpace);
        return end($nameSpaceArr) ?? null;
    }

    /**
     * 去掉命名空间获取类名对象
     */
    public static function getModelNameByClass(string $className, ?string $find = 'Dao', ?string $replace = 'Bean'): string
    {
        return str_replace($find, $replace, $className);
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
            if (count($value) == 1 && isset($value[0]) && $value[0] !== 0 && $value[0] !== '0' && empty($value[0])) {
                unset($value[0]);
            }
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
