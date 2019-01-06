<?php

namespace AsaEs\Utility;

class PhoneNumber
{

    /**
     * 隐藏手机号码的中间区号
     * @param string $mobile 13812345678
     * @param null|string $replaceStr
     * @return string 138****5678
     */
    public static function hiddenMobileArea(string $mobile,?string $replaceStr = '****') :string {

        return substr_replace($mobile,$replaceStr,3,4);
    }
}
