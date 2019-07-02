<?php

namespace AsaEs\Utility;


use Overtrue\Pinyin\Pinyin;

/**
Composer : "overtrue/pinyin": "3.0"
 * github : https://github.com/overtrue/pinyin
 */
class PinyinUtility
{

    /**
     * @param string $string
     * @param string $delimiter
     */
    public static function abbr(string $string,string $delimiter = ''){

//        $pinyinObj = new Pinyin();
//        $v2 = $pinyinObj->abbr($string ? strtoupper($pinyinObj->abbr($string)) : "");
//        var_dump($v2);
//        $v2 = $pinyinObj->abbr($string);
//        var_dump($v2);
//        return $v2;

    }

    /**
     * @param string $string
     * @param string $option
     */
    public static function convert(string $string,string $option = 'none'){

//        $pinyinObj = new Pinyin();
////        $v1 = $pinyinObj->convert($string ? implode('', $pinyinObj->convert($string)) : "");
//        $v1 = $pinyinObj->convert($string );
//        return $v1;
    }

    /**
     * 获取拼音全拼
     * @param string $string
     * @param string $option
     */
    public static function completeSpellingString(string $string,string $delimiter = '',?string $option='',bool $isUppercase = true):string {

        $pinyinObj = new Pinyin();
        $str = $pinyinObj->convert($string) ? implode($delimiter, $pinyinObj->convert($string)) : "";

        return $isUppercase ?  strtoupper($str) : strtolower($str);
    }

    /**
     * 获取拼音首字母
     * @param string $string
     * @param string $option
     * @return string
     */
    public static function firstLetterString(string $string,string $delimiter = '',string $option = '',bool $isUppercase = true) :string {

        $pinyinObj = new Pinyin();
        $str = $pinyinObj->abbr($string,$delimiter) ? $pinyinObj->abbr($string,$delimiter) : "";

        return $isUppercase ?  strtoupper($str) : strtolower($str);
    }
}