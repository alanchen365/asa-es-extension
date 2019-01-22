<?php
namespace AsaEs\Sock\Process;

use AsaEs\Logger\FileLogger;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use EasySwoole\Core\Swoole\ServerManager;
use Swoole\Process;

class WebSock extends AbstractProcess
{
    /**
     * 给fd发送消息通知
     */
    public function run(Process $process)
    {
        try {

        } catch (\Throwable $throwable) {

        }
    }

    public function onShutDown()
    {
    }

    public function onReceive(string $str, ...$args)
    {
    }
}
