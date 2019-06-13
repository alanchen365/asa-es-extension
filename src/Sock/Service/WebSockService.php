<?php
namespace AsaEs\Sock\Service;

use App\Module\Platform\Service\SystemService;
use App\Utility\Exception\WebSocketException;
use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use AsaEs\Logger\FileLogger;
use AsaEs\Sock\SockConst;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;

class WebSockService
{
    /**
     * 给全局fd发送消息
     */
    public static function sendMsgByFds(?array $fds, ?string $msg) :void
    {
        if (Tools::superEmpty($fds) || empty($msg)) {
            throw new WebSocketException(1000, "发送消息失败，请传递消息");
            return;
        }

        // 循环发送
        foreach ($fds as $fd) {
            ServerManager::getInstance()->getServer()->push($fd, $msg);
        }
    }

    /**
     *  绑定用户uniqueId / Fd
     */
    public static function bindingUniqueIdFd($redisObj, ?string $uniqueId, ?string $fd, ?string $sysKey)
    {
        if (empty($uniqueId) || empty($fd) || empty($sysKey)) {
            throw new WebSocketException(1000, "绑定用户失败");
            return;
        }

        // 记录用户在线 后期改造成lua todo
        $redisKey = SockConst::getWsGlobalUserOnlineKey($sysKey);
        $redisObj->hset($redisKey, $fd, $uniqueId);
    }

    /**
     *  解绑定用户by fd
     */
    public static function ubindingUniqueIdByFd($redisObj, ?string $sysKey, ?string $fd):void
    {
        if (empty($fd) || empty($sysKey)) {
            return;
        }

        // 记录用户在线 后期改造成lua todo
        $redisKey = SockConst::getWsGlobalUserOnlineKey($sysKey);
        $redisObj->hdel($redisKey, $fd);
    }

    /**
     * 获取唯一用户标识
     */
    public static function getUniqueIdBySysKey(?string $sysKey, ?string $uniqueId)
    {
        if (empty($uniqueId) || empty($sysKey)) {
            throw new WebSocketException(1000, "生成唯一用户标识失败");
            return;
        }
        
        return strtoupper($sysKey) . "-". $uniqueId;
    }

    /**
     * 获取推送内容
     */
    public static function getSendContent(?string $opt, ?string $act, ?array $data = []) : string
    {
        if (empty($opt) || empty($act)) {
            throw new WebSocketException(1000, "获取推送内容失败");
            return '';
        }

        $pushData = [
            'opt' => strtolower($opt),
            'act' => strtolower($act),
            'data' => $data,
        ];

        return json_encode($pushData);
    }

    /**
     * 记录fd分给了哪个系统
     */
    public static function setFdToSys($redisObj, ?string $fd, ?string  $sysKey)
    {
        $redisKey = EsRedis::getKeyPre(SockConst::WS_GLOBAL_FD_SYS_KEY);
        $redisObj->hset($redisKey, $fd, $sysKey);
    }

    /**
     * 通过fd获取绑定到的系统
     */
    public static function getSysByFd($redisObj, ?string $fd) :?string
    {
        if (empty($fd)) {
            return null;
        }

        $redisKey = EsRedis::getKeyPre(SockConst::WS_GLOBAL_FD_SYS_KEY);
        $hash = $redisObj->hgetall($redisKey) ?? [];

        return $hash[$fd] ?? null;
    }

    /**
     * 解除fd和系统的绑定关系
     */
    public static function ubindingSysByFd($redisObj, ?string $fd):void
    {
        if (empty($fd)) {
            return;
        }

        $redisKey = EsRedis::getKeyPre(SockConst::WS_GLOBAL_FD_SYS_KEY);
        $redisObj->hdel($redisKey, $fd);
    }

    /**
     * 根据uid syskey 获取 用户绑定的所有fds
     */
    public static function getFdsByUid($redisObj, ?string $sysKey, ?string $uid)
    {
        $redisKey = SockConst::getWsGlobalUserOnlineKey($sysKey);
        $bindingRelation = $redisObj->hgetAll($redisKey);
    }

    /**
     * 系统关闭的时候 处理的事儿.
     * @param string $fd 链接id
     */
    public static function systemClose($fd)
    {
        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);

        // 获取绑定系统的key
        $sysKey = WebSockService::getSysByFd($redisObj, $fd);
        if ($sysKey) {
            // 解除和在线记录的绑定
            WebSockService::ubindingUniqueIdByFd($redisObj, $sysKey, $fd);
            // 解除和系统的绑定 TODO
            WebSockService::ubindingSysByFd($redisObj, $fd);
        }
    }

    /**
     * 清空fd和系统的绑定关系
     */
    public static function clearFdToSys($redisObj)
    {
        $systemService = new SystemService();
        $sysKeys = $systemService->getSystemKyes();

        $redisKey = [];
        foreach ($sysKeys as $sysKey) {
            $redisKey[] = EsRedis::getKeyPre(SockConst::WS_GLOBAL_FD_SYS_KEY);
        }
        
        $redisObj->delAll($redisKey);
    }
    
    /**
     * 清空fd和uniqueid的绑定关系
     */
    public static function clearFdToUniqueId($redisObj)
    {
        // 记录用户在线 后期改造成lua todo
        $systemService = new SystemService();
        $sysKeys = $systemService->getSystemKyes();

        $redisKey = [];
        foreach ($sysKeys as $sysKey) {
            $redisKey[] = SockConst::getWsGlobalUserOnlineKey($sysKey);
        }

        $redisObj->del($redisKey);
    }
}
