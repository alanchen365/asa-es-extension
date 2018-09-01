<?php

namespace AsaEs\Utility;

class Time
{
    /**
     * 获取当前时间.
     */
    public static function getNowDataTime(string $format = 'Y-m-d H:i:s')
    {
        return date($format, time());
    }

    /**
     * 返回相对时间（如：20分钟前，3天前）.
     *
     * @param $date
     *
     * @return string
     */
    public static function formatDate($date): string
    {
        $t = time() - strtotime($date);
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒',
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int) $k)) {
                return $c.$v.'前';
            }
        }
    }
}
