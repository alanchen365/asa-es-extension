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

class MiddlewareException extends BaseException
{
    /**
     * LOG 等级
     * INFO level表明 消息在粗粒度级别上突出强调应用程序的运行过程。
     * WARN level表明会出现潜在错误的情形。
     * ERROR level指出虽然发生错误事件，但仍然不影响系统的继续运行。
     * FATAL level指出每个严重的错误事件将会导致应用程序的退出。
     */

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
