<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午2:53
 */
namespace AsaEs\Sock\Parser;

use App\Utility\Exception\WebSocketException;
use AsaEs\Base\BaseSockController;
use AsaEs\Config;
use AsaEs\Logger\FileLogger;
use AsaEs\Sock\Service\WebSockService;
use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;
use EasySwoole\Core\Swoole\ServerManager;

class WebSock implements ParserInterface
{
    public static function decode($raw, $client)
    {
        try {
            $CommandBean = new CommandBean();
            $commandLine = json_decode($raw, true);
            $env = Config::getInstance()->getEnv();
        
            // 如果是心跳
            if ($raw == 'PING') {
                $CommandBean->setControllerClass(BaseSockController::class);
                $CommandBean->setAction('ping');
                return $CommandBean;
            }

            if (!is_array($commandLine)) {
                FileLogger::getInstance()->console("不是一个数组");
                return '';
            }

            //这里会获取JSON数据中class键对应的值，并且设置一些默认值
            //当用户传递class键的时候，会去App/WebSocket命名空间下寻找类
            $control = isset($commandLine['opt']) ? 'App\\Sock\\'.ucfirst($commandLine['opt']) : '';
            $action = $commandLine['act'] ?? 'none';
            $data = $commandLine['data'] ?? null;

            // 先检查这个类是否存在，如果不存在则使用index默认类
            $CommandBean->setControllerClass(class_exists($control) ? $control : BaseSockController::class);
            // 检查传递的action键是否存在，如果不存在则访问默认方法
            $CommandBean->setAction(class_exists($control) ? $action : 'actionNotFound');
            $CommandBean->setArg('data', $data);

            return $CommandBean;
        } catch (\Throwable $throwable) {
            FileLogger::getInstance()->log(json_encode(['code'=>$throwable->getCode(),'msg'=>$throwable->getMessage()]), "WebSocketException");
            //throw new WebSocketException($throwable->getCode(),$throwable->getMessage());
        }
    }

    public static function encode(string $raw, $client): ?string
    {
        /*
         * 注意，return ''与return null不一样，空字符串一样会回复给客户端，比如在服务端主动心跳测试的场景
         */
        if (strlen($raw) == 0) {
            return null;
        }
        return $raw;
    }
}
