<?php

namespace AsaEs\Utility;

class Md5
{
    /**
     * 两次md5.
     *
     * @param string $string
     *
     * @return string
     */
    public static function toMd5(string $string)
    {
        return md5(md5($string));
    }
}
