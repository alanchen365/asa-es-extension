<?php
namespace AsaEs\Sock;

use AsaEs\Cache\EsRedis;

class SockConst
{
    // fd分给了哪个系统
    const WS_GLOBAL_FD_SYS_KEY = 'WS_GLOBAL_FD_SYS_KEY';
    // ws 协议用户在线记录的key 分系统
    const WS_GLOBAL_UNIQUEID_ONLINE_KEY = 'WS_GLOBAL_FD_UNIQUEID_ONLINE_';
    // 一个uniqueId 关联d
    const WS_GLOBAL_UNIQUEID_FD_KEY = 'WS_GLOBAL_UNIQUEID_FD_KEY';

    /**
     * 获取不同sys的在线key
     */
    public static function getWsGlobalUserOnlineKey(string $sysKey) :string
    {
        $key = SockConst::WS_GLOBAL_UNIQUEID_ONLINE_KEY . $sysKey;
        return strtoupper(EsRedis::getKeyPre($key));
    }
}
