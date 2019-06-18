<?php

namespace AsaEs\Utility;

use AsaEs\Config;

/**
 * 已经废弃 只有LMS在用 如果LMS的权限切出去之后 类即可废止
 * Class Blake
 * @package AsaEs\Utility
 */
class Blake
{
    /**
     * @deprecate
     * @param string $string
     * @return mixed
     */
    public static function set(string $string)
    {
        $blakeConf = Config::getInstance()->getConf('auth.BLAKE',true);
        return blake2($string, $blakeConf['LENGTH'], $blakeConf['KEY']);
    }

}
