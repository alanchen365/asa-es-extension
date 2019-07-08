<?php

namespace AsaEs\Utility;

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;
use PhpParser\Node\Expr\Isset_;

class StringUtility
{
    /*
     * 下划线转驼峰
     */
    public static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
            return strtoupper($matches[2]);
        },$str);
        return $str;
    }

    /*
     * 驼峰转下划线
     */
    public static function humpToLine($str){
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$str);
        return ltrim($str, "_");
    }

}
