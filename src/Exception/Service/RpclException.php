<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Exception\Service;

use AsaEs\Utility\Tools;
use AsaEs\Exception\BaseException;

class RpclException extends BaseException
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        parent::__construct($code, $msg, $this);
    }
}
