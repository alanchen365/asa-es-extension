<?php
/**
 * Created by PhpStorm.
 * User: Lius
 * Date: 2018/12/12 0012
 * Time: 14:30
 */

namespace AsaEs\Sdk\WxpayAPI;


class WxPayException extends \Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}