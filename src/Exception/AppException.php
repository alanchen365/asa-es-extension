<?php
/**
 * Created by PhpStorm.
 * User: asaesh
 * Date: 2017/8/16
 * Time: 12:08.
 */

namespace AsaEs\Exception;

use AsaEs\Utility\Tools;

class AppException extends BaseException
{
    /**
     * SignException constructor.
     *
     * @param string     $code
     * @param int|string $logLevel
     */
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        // 写log
        $className = Tools::getLastNameSpaceName(__CLASS__);
        parent::log($className, $code, $msg, $this->getFile(), $this->getTrace());
        parent::__construct($code, $msg, $this);
    }
}
