<?php

namespace AsaEs\Exception;

use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;

class BaseException extends \Exception
{
    public function __construct($code = null, $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, $code, $this);
    }

    public function log(\Throwable $throwable, string $className)
    {
        $msg = $this->getMessage();
        $data = [
            'code' => $this->getCode(),
            'msg' => empty($msg) ? Msg::get($this->getCode()) : $msg,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace(),
        ];
        FileLogger::getInstance()->log(json_encode($data), strtoupper($className));
    }
}
