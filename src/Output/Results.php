<?php

namespace AsaEs\Output;

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
            'code' => $this->_code,
            'result' => $result,
            'msg' => $this->_msg,
            'time' => date('Y-m-d H:i:s'),
        ];

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
