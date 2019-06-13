<?php

namespace AsaEs\Base;

use App\Sock\Account;
use App\Sock\Auth;
use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use AsaEs\Sock\Service\WebSockService;
use AsaEs\Sock\SockConst;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;
use EasySwoole\Core\Swoole\ServerManager;

class BaseSockController extends WebSocketController
{
    /**
     * 心跳包
     */
    public function ping()
    {
        $fd = $this->client()->getFd();
        WebSockService::sendMsgByFds([$fd], "PONG");
    }

    /**
     * 控制器不存在兜底方法
     * @param null|string $actionName
     */
    public function actionNotFound(?string $actionName)
    {
        //if ($env != 'PRODUCTION') {
        //    FileLogger::getInstance()->console("找不到相应操作 已断开 . (该提示会在正式环境消失)");
        //}

        //var_dump('22');
        //$fd = $this->client()->getFd();
        //if ($actionName == "PING") {
        //    $msg =  WebSockService::getSendContent("system", "pong");
        //    WebSockService::sendMsgByFds([$fd], $msg);
        //    return;
        //}
        //
        //ServerManager::getInstance()->getServer()->close($fd);
    }
}
