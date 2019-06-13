<?php

namespace AsaEs\Exception;

use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;
use AsaEs\Utility\ExceptionUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;

class BaseException extends \Exception
{
    public function __construct($code = null, $message = '', \Throwable $previous = null)
    {
        $exceptionData = ExceptionUtility::getExceptionData($previous, $code, $message);
        $logName = Tools::getLastNameSpaceName(get_called_class());

        FileLogger::getInstance()->log(json_encode($exceptionData), strtoupper($logName));
        parent::__construct($message, $code, $this);
    }
}
