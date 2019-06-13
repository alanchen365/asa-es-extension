<?php

namespace AsaEs\Utility;

use AsaEs\Config;

class Blake
{
    public static function set(string $string)
    {
        $blakeConf = Config::getInstance()->getConf('auth.BLAKE',true);
        return blake2($string, $blakeConf['LENGTH'], $blakeConf['KEY']);
    }
}
