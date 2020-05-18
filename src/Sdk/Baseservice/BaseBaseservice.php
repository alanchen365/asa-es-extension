<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/2/26
 * Time: 10:58
 */

namespace AsaEs\Sdk\Baseservice;


use App\AppConst\EnvConst;

class BaseBaseservice {

    /**
     * 获取
     * @param string $path
     * @return string
     */
    public static function getBaseserviceUrl($path = ''){
        return EnvConst::BASESERVICE_PROTOCOL . "://" . EnvConst::BASESERVICE_HOST . ":" . EnvConst::BASESERVICE_PROT . $path;
    }

}