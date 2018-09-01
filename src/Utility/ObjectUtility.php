<?php

namespace AsaEs\Utility;

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
}
