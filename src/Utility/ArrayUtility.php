<?php

namespace AsaEs\Utility;

class ArrayUtility
{

    /**
     * 判断是否是索引数组
     */
    public static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * 判断一个一位数组中的id是是否是null（不过滤9） 如果等于null 最终判断注销后的数组是否为空
     */
    public static function emptyIds(array $ids)
    {
        foreach ((array)$ids as $key => $id) {
            if (!isset($id)) {
                unset($ids[$key]);
            }
        }

        if (empty($ids)) {
            return true;
        }
        return false;
    }

    /**
     * 在一个一维数组中 查找某个value是否存在.
     */
    public static function arrayFlip(array $arr, $value): bool
    {
        $arr = array_flip($arr);

        return isset($arr[$value]) ? true : false;
    }

    /**
     * 从数组中移除空白的元素（其中包括只有空白字符的元素）.
     *
     * 用法：
     * <code>
     * $arr = array('', 'test', '   ');
     * Util_Array::remove_empty($arr);
     *
     * var_dump($arr); # 结果只有一个test
     * </code>
     *
     * @param array $arr  要处理的数组
     * @param bool  $trim 是否对数组元素调用 trim 函数
     */
    public static function removeEmpty(&$arr, $trim = true)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                self::removeEmpty($arr[$key]);
            } else {
                $value = trim($value);
                if ('' == $value) {
                    unset($arr[$key]);
                } elseif ($trim) {
                    $arr[$key] = $value;
                }
            }
        }
    }

    /**
     * 从一个二维数组中返回指定键的所有值
     *
     * 用法：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $values = Util_Array::cols($rows, 'value');
     *
     * print_r($values);
     *   // 输出结果为
     *   // array(
     *   //   '1-1',
     *   //   '2-1',
     *   // )
     * </code>
     *
     * @param array  $arr 数据源
     * @param string $col 要查询的键
     *
     * @return array 包含指定键所有值的数组
     */
    public static function cols($arr, $col)
    {
        $ret = array();
        foreach ($arr as $row) {
            if (isset($row[$col])) {
                $ret[] = $row[$col];
            }
        }

        return $ret;
    }

    public static function cols_str($arr, $col)
    {
        $ret = self::cols($arr, $col);
        $ret_str = implode(',', $ret);

        return $ret_str;
    }

    public static function nullTo0(array $arr)
    {
        if (count($arr) <= 0) {
            return array(0);
        }

        return $arr;
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
                $key = $force_string_key ? (string) $row[$key_field] : $row[$key_field];
                $ret[$key] = $row[$value_field];
            }
        } else {
            foreach ($arr as $row) {
                $key = $force_string_key ? (string) $row[$key_field] : $row[$key_field];
                $ret[$key] = $row;
            }
        }

        return $ret;
    }

    /**
     * 将一个二维数组按照指定字段的值分组.
     *
     * 用法：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     * $values = Util_Array::group_by($rows, 'parent');
     *
     * print_r($values);
     *   // 按照 parent 分组的输出结果为
     *   // array(
     *   //   1 => array(
     *   //        array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *   //        array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *   //        array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *   //   ),
     *   //   2 => array(
     *   //        array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *   //        array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *   //   ),
     *   //   3 => array(
     *   //        array('id' => 6, 'value' => '6-1', 'parent' => 3),
     *   //   ),
     *   // )
     * </code>
     *
     * @param array  $arr       数据源
     * @param string $key_field 作为分组依据的键名
     *
     * @return array 分组后的结果
     */
    public static function groupBy($arr, $key_field)
    {
        $ret = array();
        foreach ($arr as $row) {
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }

        return $ret;
    }

    public static function groupByMultiField($arr, $multi_key_field)
    {
        $ret = array();
        foreach ($arr as $row) {
            $key = array();
            foreach ($multi_key_field as $key_field) {
                $key[] = $row[$key_field];
            }
            $key = implode('_', $key);
            $ret[$key][] = $row;
        }

        return $ret;
    }

    public static function groupByArray($arr, $key_field_arr)
    {
        $ret = array();
        foreach ($arr as $row) {
            $key_arr = [];
            foreach ($key_field_arr as $v) {
                $key_arr[] = $row[$v];
            }
            $key = implode('_', $key_arr);
            $ret[$key][] = $row;
        }

        return $ret;
    }

    /**
     * 将一个平面的二维数组按照指定的字段转换为树状结构.
     *
     * 用法：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 0),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 0),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 0),
     *
     *     array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
     *     array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
     *     array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
     *     array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
     * );
     *
     * $tree = Util_Array::tree($rows, 'id', 'parent', 'nodes');
     *
     * print_r($tree);
     *   // 输出结果为：
     *   // array(
     *   //   array('id' => 1, ..., 'nodes' => array()),
     *   //   array('id' => 2, ..., 'nodes' => array(
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //   ),
     *   //   array('id' => 3, ..., 'nodes' => array(
     *   //        array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
     *   //             array(..., , 'parent' => 9, 'nodes' => array(),
     *   //        ),
     *   //   ),
     *   // )
     * </code>
     *
     * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
     * <code>
     * $refs = null;
     * $tree = Util_Array::tree($rows, 'id', 'parent', 'nodes', $refs);
     *
     * // 输出 id 为 3 的节点及其所有子节点
     * $id = 3;
     * print_r($refs[$id]);
     * </code>
     *
     * @param array  $arr           数据源
     * @param string $key_node_id   节点ID字段名
     * @param string $key_parent_id 节点父ID字段名
     * @param string $key_childrens 保存子节点的字段名
     * @param bool   $refs          是否在返回结果中包含节点引用
     *
     * return array 树形结构的数组
     */
    public static function tree($arr, $key_node_id, $key_parent_id = 'parent_id', $key_childrens = 'childrens', &$refs = null)
    {
        $refs = array();
        foreach ($arr as $offset => $row) {
            $arr[$offset][$key_childrens] = null;
            $refs[$row[$key_node_id]] = &$arr[$offset];
        }

        $tree = array();
        foreach ($arr as $offset => $row) {
            $parent_id = $row[$key_parent_id];
            if ($parent_id) {
                if (!isset($refs[$parent_id])) {
                    $tree[] = &$arr[$offset];
                    continue;
                }
                $parent = &$refs[$parent_id];
                $parent[$key_childrens][] = &$arr[$offset];
            } else {
                $tree[] = &$arr[$offset];
            }
        }

        return $tree;
    }

    /**
     * 将树形数组展开为平面的数组.
     *
     * 这个方法是 tree() 方法的逆向操作。
     *
     * @param array  $tree          树形数组
     * @param string $key_childrens 包含子节点的键名
     *
     * @return array 展开后的数组
     */
    public static function treeToArray($tree, $key_childrens = 'childrens')
    {
        $ret = array();
        if (isset($tree[$key_childrens]) && is_array($tree[$key_childrens])) {
            $childrens = $tree[$key_childrens];
            unset($tree[$key_childrens]);
            $ret[] = $tree;
            foreach ($childrens as $node) {
                $ret = array_merge($ret, self::treeToArray($node, $key_childrens));
            }
        } else {
            unset($tree[$key_childrens]);
            $ret[] = $tree;
        }

        return $ret;
    }

    /**
     * 根据指定的键对数组排序.
     *
     * 用法：
     * <code>
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     *
     * $rows = Util_Array::sort_by_col($rows, 'id', SORT_DESC);
     * print_r($rows);
     * // 输出结果为：
     * // array(
     * //   array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * //   array('id' => 5, 'value' => '5-1', 'parent' => 2),
     * //   array('id' => 4, 'value' => '4-1', 'parent' => 2),
     * //   array('id' => 3, 'value' => '3-1', 'parent' => 1),
     * //   array('id' => 2, 'value' => '2-1', 'parent' => 1),
     * //   array('id' => 1, 'value' => '1-1', 'parent' => 1),
     * // )
     * </code>
     *
     * @param array  $array   要排序的数组
     * @param string $keyname 排序的键
     * @param int    $dir     排序方向
     *
     * @return array 排序后的数组
     */
    public static function sortByCol($array, $keyname, $dir = SORT_ASC)
    {
        return self::sortByMultiCols($array, array($keyname => $dir));
    }

    /**
     * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY.
     *
     * 用法：
     * <code>
     * $rows = Util_Array::sort_by_multiCols($rows, array(
     *     'parent' => SORT_ASC,
     *     'name' => SORT_DESC,
     * ));
     * </code>
     *
     * @param array $rowset 要排序的数组
     * @param array $args   排序的键
     *
     * @return array 排序后的数组
     */
    public static function sortByMultiCols($rowset, $args)
    {
        $sortArray = array();
        $sortRule = '';
        foreach ($args as $sortField => $sortDir) {
            foreach ($rowset as $offset => $row) {
                $sortArray[$sortField][$offset] = $row[$sortField];
            }
            $sortRule .= '$sortArray[\''.$sortField.'\'], '.$sortDir.', ';
        }
        if (empty($sortArray) || empty($sortRule)) {
            return $rowset;
        }
        eval('array_multisort('.$sortRule.'$rowset);');

        return $rowset;
    }

    /**
     * 根据key从数组中找到相关值，其中key是依据$delimiter分离的，默认为“.”.
     *
     * // 比如获取值： $array['foo']['bar']
     * $value = Util_Array::path($array, 'foo.bar');
     *
     * 使用 "*"作为匿名
     *
     * // Get the values of "color" in theme
     * $colors = Util_Array::path($array, 'theme.*.color');
     *
     * @param array  $array   数组
     * @param string $path    字符串，多维的由$delimiter连接
     * @param mixed  $default 如果没有查到数组中的该值返回的默认值
     *
     * @return mixed
     */
    public static function path($array, $path, $default = null)
    {
        if (array_key_exists($path, $array)) {
            return $array[$path];
        }

        $delimiter = '.';
        //$path = trim($path, "{$delimiter}* ");
        $keys = explode($delimiter, $path);
        do {
            $key = array_shift($keys);

            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } elseif ('*' === $key) {
                $values = array();
                $inner_path = implode($delimiter, $keys);
                foreach ($array as $arr) {
                    $value = is_array($arr) ? self::path($arr, $inner_path) : $arr;
                    if ($value) {
                        $values[] = $value;
                    }
                }

                if ($values) {
                    return $values;
                } else {
                    break;
                }
            } else {
                break;
            }
        } while ($keys);

        return $default;
    }

    /**
     * 递归合并两个或多个数组
     * 本函数内使用for语句，以及func_get_arg函数，实现多个数组递归合并
     * $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
     * $mary = array('name' => 'mary', 'children' => array('jane'));.
     *
     * $john = Util_Array::merge($john, $mary);
     *
     * array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
     *
     * @param array $a1 原始数组
     * @param array $a2 需要合并的数组
     *
     * @return array
     */
    public static function merge(array $a1, array $a2)
    {
        $result = array();
        for ($i = 0, $total = func_num_args(); $i < $total; ++$i) {
            $arr = func_get_arg($i);
            $assoc = Util_Array::isAssoc($arr);
            foreach ($arr as $key => $val) {
                if (isset($result[$key])) {
                    if (is_array($val) && is_array($result[$key])) {
                        if (Util_Array::isAssoc($val)) {
                            $result[$key] = Util_Array::merge($result[$key], $val);
                        } else {
                            $diff = array_diff($val, $result[$key]);
                            $result[$key] = array_merge($result[$key], $diff);
                        }
                    } else {
                        if ($assoc) {
                            $result[$key] = $val;
                        } elseif (!in_array($val, $result, true)) {
                            $result[] = $val;
                        }
                    }
                } else {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }

    /**
     * 是否为关联数组.
     *
     * @param array $array array to check
     *
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * 将多维数组转为一维数组.
     *
     * @param array $arr arr
     *
     * @return array
     */
    public static function arrMd2Ud($arr)
    {
        //将数值第一元素作为容器，作地址赋值。
        $ar_room = &$arr[key($arr)];
        //第一容器不是数组进去转呀
        if (!is_array($ar_room)) {
            //转为成数组
            $ar_room = array($ar_room);
        }
        //指针下移
        next($arr);
        //遍历
        while (list($k, $v) = each($arr)) {
            //是数组就递归深挖，不是就转成数组
            $v = is_array($v) ? call_user_func(array(__CLASS__, __FUNCTION__), $v) : array($v);
            //递归合并
            $ar_room = array_merge_recursive($ar_room, $v);
            //释放当前下标的数组元素
            unset($arr[$k]);
        }

        return $ar_room;
    }

    /**
     * filterKeys
     * 在原有数组上，过滤获取部分key.
     *
     * @param mixed $array
     * @param mixed $filter_keys
     * @static
     */
    public static function filterKeys($array, $filter_keys, $check = false)
    {
        if (!is_array($array) || !is_array($array)) {
            return false;
        }
        $return = array();
        foreach ($filter_keys as $key) {
            if ($check && !isset($array[$key])) {
                return false;
            }
            $return[$key] = isset($array[$key]) ? $array[$key] : '';
        }

        return $return;
    }

    public static function join($left_arr, $right_arr, $left_key, $right_key)
    {
        $ret = array();
        $merg = [];
        //使用数组下标的办法
        foreach ($left_arr as $key => $value) {
            $merg[$value[$left_key]] = $value;
        }
        foreach ($right_arr as $key => $value) {
            $ret[] = array_merge($merg[$value[$right_key]], $value);
        }

        return $ret;
    }

    /**
     * 将一个字符串按照固定分隔符，分割为数组
     * Enter description here ...
     *
     * @param unknown_type $str
     * @param unknown_type $sp
     */
    public static function ConvertToArray($str, $sp = ',')
    {
        $str = trim($str);
        $arr = explode($sp, $str);
        foreach ($arr as $k => $v) {
            if ('' == $v) {
                unset($arr[$k]);
            }
        }

        return $arr;
    }

    /* @param array   $arr              数据源
     * @param string  $key_field        按照什么键的值进行转换
     * @param string  $value_field      对应的键值
     * @param boolean $force_string_key 强制使用字符串KEY
     *
     * @return array 转换后的 HashMap 样式数组
     */
    public static function objHashMap($arr, $key_field, $value_field = null, $force_string_key = false)
    {
        $ret = array();
        if ($value_field) {
            foreach ($arr as $row) {
                $key = $force_string_key ? (string) $row->$key_field : $row->$key_field;
                $ret[$key] = $row[$value_field];
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
     * 提取一维或二维数组的指定一列或多列
     * @param $arr
     * @param $keys
     * @return array
     */
    public static function arrayFilterKeys($arr, $keys)
    {
        if (empty($arr)) {
            return [];
        }
        $arr = (array)$arr;
        //判断是否是一维数组
        if (!isset($arr[0])) {
            foreach ((array)$arr as $key => $value) {
                if (in_array($key, $keys)) {
                    $newArr[$key] = $value;
                }
            }
        } else {
            foreach ($arr as $v) {
                extract((array) $v);
                $newArr[] = compact($keys);
            }
        }
        return $newArr;
    }

    /**
     * unset 数组中的空变量
     */
    public static function unsetEmpty(array $array)
    {
        if (empty($array)) {
            return [];
        }

        foreach ($array as $item => $value) {
            if (Tools::superEmpty($value)) {
                unset($array[$item]);
            }
        }

        return $array;
    }

    /**
     * unset 数组中指定的value
     */
    public static function unsetValue(array $array, array $values = [])
    {
        foreach ($array as $item => $value) {
            // 数组和对象不管
            if (is_array($value) || is_object($value)) {
                continue;
            }

            if(in_array($value,$values)){
                unset($array[$item]);
            }
        }

        return array_values($array) ?? [];
    }


    /**
     * 判断多个数组是否为空
     * 判断原则 有一个为空 就都为空
     */
    public static function ArrayEmpty(array $array):bool {

        foreach ($array as $value){
            if(Tools::superEmpty($value)){
                return true;
            }
        }

        return false;
    }


    /**
     * 保留数组中部分元素
     * @param array $array 原始数组
     * @param array $keys 过滤掉的元素
     */
    public static function getArrByKeys(array $array,array $keys = []):array {

        $nList = [];
        foreach ($array as $item => $value){
            if(ArrayUtility::arrayFlip($keys,$item)){
                $nList[$item] = $array[$item];
            }
        }

        return $nList;
    }
}
