<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Exception\Service;

use App\Utility\Tools\Tools;

class CrontabException extends BaseException
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        parent::__construct($code, $msg, $this);
        $this->log($this, Tools::getModuleObjNameByClass(__CLASS__,'log'));
    }
}
