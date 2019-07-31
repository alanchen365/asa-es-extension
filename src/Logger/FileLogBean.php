<?php

namespace AsaEs\Logger;


use App\AppConst\AppInfo;
use EasySwoole\Core\Component\Spl\SplBean;

class FileLogBean extends SplBean
{

    protected $project_name = AppInfo::APP_EN_NAME; // 所属项目

    protected $category; // 日志分类

    protected $message; // 日志详情

    protected $create_time; // 创建时间

    private $requestLog;

    private $responseLog;

    private $fileName;

    private $fileBasePath;

    /**
     * @return mixed
     */
    public function getFileBasePath()
    {
        return $this->fileBasePath;
    }

    /**
     * @param mixed $fileBasePath
     */
    public function setFileBasePath($fileBasePath): void
    {
        $this->fileBasePath = $fileBasePath;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $type
     */
    public function setCategory($category): void
    {
        $this->category = strtolower($category);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $_data = [];
        if(is_string($message)){
            $_data = json_decode($message,true);
            if(json_last_error() != JSON_ERROR_NONE){
                $_data = [$message];
            }
        }else{
            $_data = [$message];
        }

        $_data['request_data'] = BaseLogger::getRequestLogData();
        $this->message = $_data;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * @param mixed $create_time
     */
    public function setCreateTime($create_time): void
    {
        $this->create_time = $create_time;
    }

    /**
     * @return mixed
     */
    public function getRequestLog()
    {
        return $this->requestLog;
    }

    /**
     * @param mixed $requestLog
     */
    public function setRequestLog($requestLog): void
    {
        $this->requestLog = $requestLog;
    }

    /**
     * @return mixed
     */
    public function getResponseLog()
    {
        return $this->responseLog;
    }

    /**
     * @param mixed $responseLog
     */
    public function setResponseLog($responseLog): void
    {
        $this->responseLog = $responseLog;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName(string $format = 'Ymd'): void
    {
        // 时间处理
        $fileName = date($format,time());
        $this->fileName = $fileName . '.log';
    }
}
