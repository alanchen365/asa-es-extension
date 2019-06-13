<?php

namespace AsaEs\Utility;

use think\validate\ValidateRule;

class ObjectUtility
{
    /**
     * 从一个数组中返回某个对象key的值
     *
     * 用法：
     * <code>
     * $rows = [
     *   {
     *     id:1,
     *     name:'zhangsan',
     *   },
     *  {
     *     id:2,
     *     name:'lisi',
     *   }
     * ]
     * $values = ArrayUtility::cols($rows, 'name');
     *
     * print_r($values);
     *   // 输出结果为
     *   // array(
     *   //   'zhangsan',
     *   //   'lisi',
     *   // )
     * </code>
     *
     * @param array  $arrList 数据源
     * @param string $col     要查询的键
     *
     * @return array 包含指定键所有值的数组
     */
    public static function cols(array $arrList, string $col)
    {
        $ret = array();
        foreach ($arrList as $obj) {
            if (!empty($obj->$col)) {
                $ret[] = $obj->$col;
            }
        }

        return $ret;
    }


    /**
     * 将一个二维数组转换为 HashMap，并返回结果.
     *
     * 用法1：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = Util_Array::hashmap($rows, 'id', 'value');
     *
     * print_r($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => '1-1',
     *   //   2 => '2-1',
     *   // )
     * </code>
     *
     * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
     *
     * 用法2：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = Util_Array::hashMap($rows, 'id');
     *
     * print_r($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => array('id' => 1, 'value' => '1-1'),
     *   //   2 => array('id' => 2, 'value' => '2-1'),
     *   // )
     * </code>
     *
     * @param array  $arr              数据源
     * @param string $key_field        按照什么键的值进行转换
     * @param string $value_field      对应的键值
     * @param bool   $force_string_key 强制使用字符串KEY
     *
     * @return array 转换后的 HashMap 样式数组
     */
    public static function hashmap($arr, $key_field, $value_field = null, $force_string_key = false)
    {
        if (empty($arr)) {
            return array();
        }
        $ret = array();
        if ($value_field) {
            foreach ($arr as $row) {
                $key = $force_string_key ? (string) $row->$key_field : $row->$key_field;
                $ret[$key] = $row->$value_field;
            }
        } else {
            foreach ($arr as $row) {
                $key = $force_string_key ? (string) $row->$key_field : $row->$key_field;
                $ret[$key] = $row;
            }
        }

        return $ret;
    }


    /**
     * 判断一个对象是否为空
     * 如果为空 返回一个空对象
     * @param object $obj
     * @return object
     */
    public static function objectEmpty($obj)
    {
        if(Tools::superEmpty($obj)){
            return null;
        }

        return $obj;
    }
}