<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Exception\Service;

use AsaEs\Exception\BaseException;
use AsaEs\Utility\Tools;

class SignException extends BaseException
{
    /**
     * SignException constructor.
     *
     * @param string     $code
     * @param int|string $logLevel
     */
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        parent::__construct($code, $msg, $this);
    }
}
