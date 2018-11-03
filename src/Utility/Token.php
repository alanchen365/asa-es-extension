<?php

namespace AsaEs\Utility;

use AsaEs\Config;
use AsaEs\Exception\BaseException;
use AsaEs\Exception\Service\SignException;
use Firebase\JWT\JWT;

class Token
{
//    public static function encode(int $uid):string
//    {
//        try {
//            $jwtConf = Config::getInstance()->getConf('jwt',true) ?? null;
//            if (!$jwtConf) {
//                $code = 1012;
//                throw new SignException($code);
//            }
//
//            $token = JWT::encode(['uid'=>$uid], $jwtConf['KEY'], $jwtConf['ALG']) ?? '';
//            return $token;
//        } catch (\Exception $e) {
//            throw new SignException($e->getCode(), $e->getMessage());
//        }
//    }

    public static function decode(string $token): object
    {
        try {
            $jwtConf = Config::getInstance()->getConf('jwt',true);
            $tokenObj = (object) JWT::decode($token, $jwtConf['KEY'], [$jwtConf['ALG']]);

            return  $tokenObj;
        } catch (\Exception $exception) {
            if ($exception instanceof BaseException) {
                throw $exception;
            }
            throw new SignException(3002);
        }
    }
}
