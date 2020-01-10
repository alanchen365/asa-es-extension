<?php

namespace AsaEs\Output;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Utility\Env;
use EasySwoole\Core\Component\Di;

class Results
{
    private $_code;
    private $_msg;

    private $_result;

    public function __construct()
    {
    }

    /**
     * @param mixed $code
     */
    public function setCode(int $code): void
    {
        $this->_code = $code;
    }

    /**
     * @param mixed $msg
     */
    public function setMsg(string $msg): void
    {
        $this->_msg = $msg;
    }

    public function set(string $key, $value, bool $overlay = true): void
    {
        //key为空或不是字串
        if (!$key) {
            return;
        }
        //禁止覆盖
        if ((!$overlay) && isset($this->_result[$key])) {
            return;
        }
        $this->_result[$key] = $value;
    }

    public function getData(): array
    {
        $result = empty($this->_result) ? (object) [] : $this->_result;


        $data = [
            AppInfo::RESULTS_RETURN_CODE_KEY => $this->_code,
            AppInfo::RESULTS_RETURN_DATE_KEY => $result,
            AppInfo::RESULTS_RETURN_MSG_KEY => $this->_msg,
            AppInfo::RESULTS_RETURN_TIME_KEY => date(AppInfo::RESULTS_RETURN_TIME_FORMAT),
        ];

        if(Env::isHttp()){
            $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $data['request_id'] = $requestObj->getRequestId();
        }

        if (empty($this->_result)) {
            unset($data['data']);
        }

        return $data;
    }

    public function delete($key): void
    {
        if (isset($this->_result[$key])) {
            unset($this->_result[$key]);
        }

        return;
    }

    public function deleteAll(): void
    {
        foreach ((array) $this->_result as $key => $item) {
            unset($this->_result[$key]);
        }
    }
}
