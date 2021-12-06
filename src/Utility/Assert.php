<?php

namespace AsaEs\Utility;


use AsaEs\Exception\MsgException;

class Assert
{
    /**
     * 是否为true
     * 例 $val为true通过 不为true则报错
     * @param $val 判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isTrue($val, int $errorCode, string $errorMsg): void
    {
        if ($val !== true) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 检查是否在一个区间
     * 例 判断 1000是否介于 1 ~ 1000的区间内,介于通过，否则报错
     * @param $val 需要判断的变量 只能是整数或小数
     * @param $start 起始值 只能是整数或小数
     * @param $end 结束值 只能是整数或小数
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isBetween($val, $start, $end, int $errorCode, string $errorMsg): void
    {
        /** 如果是空 但不是0或 '0' 报错 */
        if (superEmpty($val)) {
            throw new MsgException(1900, "Assert checkBetween val 不能为null");
        }

        /** 数据类型校验 只能是int和double */
        if (!(isInt($val) || isDouble($val))) {
            throw new MsgException(1901, "Assert checkBetween val 数据类型错误");
        }

        if (!(isInt($start) || isDouble($val))) {
            throw new MsgException(1902, "Assert checkBetween start 数据类型错误");
        }

        if (!(isInt($end) || isDouble($val))) {
            throw new MsgException(1903, "Assert checkBetween end 数据类型错误");
        }

        /** 判断是否在一个区间 */
        if (!($val >= $start && $val <= $end)) {
            throw new MsgException($errorCode, $errorMsg);
        }

    }

    /**
     * 判断变量是否为false
     * 例 $val为false通过 不为false则报错
     * @param $val  判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isFalse($val, int $errorCode, string $errorMsg): void
    {
        if ($val !== false) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * obj 是否为某一个类的实例
     * 例 $obj是$instance的实例通过 不是则报错
     * @param object $obj 判断变量
     * @param string $instance 类的实例
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isInstanceOf(object $obj, string $instance, int $errorCode, string $errorMsg): void
    {
        if (!($obj instanceof $instance)) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 判断变量是否为null
     * 例 $val为null通过 不为null则报错
     * @param $val 判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isNull($val, int $errorCode, string $errorMsg): void
    {
        if (!is_null($val)) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 判断变量是否不为null
     * 例 $val为不为null通过 为null则报错
     * @param $val 判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function notNull($val, int $errorCode, string $errorMsg): void
    {
        if (is_null($val)) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 判断变量是否为空
     * 例 $val为空通过 不为空报错
     * @param $val 判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 错误提示信息
     * @throws MsgException
     */
    static function isEmpty($val, int $errorCode, string $errorMsg): void
    {
        if (!superEmpty($val)) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 判断变量是否不为空
     * 例 $val不为空通过 为空报错
     * @param $val 判断变量
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 误提示信息
     * @throws MsgException
     */
    static function notEmpty($val, int $errorCode, string $errorMsg): void
    {
        if (superEmpty($val)) {
            throw new MsgException($errorCode, $errorMsg);
        }
    }

    /**
     * 一维数组中是否包含null元素
     * 例 $array中不包含null通过 包含null报错
     * @param array $array 判断数组
     * @param int $errorCode 错误code(code唯一便于问题的定位)
     * @param string $errorMsg 误提示信息
     * @throws MsgException
     */
    static function noNullElements(array $array, int $errorCode, string $errorMsg): void
    {
        array_filter($array, function ($row) use ($errorCode, $errorMsg) {
            //数组中包含null报错
            if (is_null($row)) {
                throw new MsgException($errorCode, $errorMsg);
            }
        });
    }
}