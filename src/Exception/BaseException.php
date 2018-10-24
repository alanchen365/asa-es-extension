<?php

namespace AsaEs\Exception;

use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;

class BaseException extends \Exception
{
    public function __construct($code = null, $message = '', \Throwable $previous = null)
    {
        // 写log
        $className = Tools::getLastNameSpaceName(get_called_class());
        $this->log(strtoupper($className));

        parent::__construct($message, $code, $this);
    }

    /**
     * 抛异常时 保存Log
     * @param \Throwable $throwable
     * @param string $className
     */
    public function log(string $className)
    {
        // request id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);

        $msg = $this->getMessage();
        $data = [
            'code' => $this->getCode(),
            'msg' => empty($msg) ? Msg::get($this->getCode()) : $msg,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            AsaEsConst::REQUEST_ID=>$requestObj->getRequestId(),
            'trace' => $this->getTrace(),
        ];
        FileLogger::getInstance()->log(json_encode($data), strtoupper($className));
    }
}
