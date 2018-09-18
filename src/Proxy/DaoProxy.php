<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午12:54
 */
namespace AsaEs\Proxy;

use AsaEs\AsaEsConst;
use AsaEs\Base\BaseDao;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use EasySwoole\Core\Swoole\ServerManager;
use think\validate\ValidateRule;

class DaoProxy
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    public function __call($actionName, $arguments)
    {
        if ($this->class instanceof BaseDao) {

            // 方法是否存在
            $ref = new \ReflectionClass($this->class);
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic()) {

                /**
                 * get by id
                 */
                if ($actionName == 'getById') {

                    // 如果开启了缓存
                    if ($this->class->isAutoCache()) {

                        // 缓存是否命中
                        $cacheObj = call_user_func_array([$this->class,'getByIdCache'], $arguments);
                        $cacheId = $cacheObj->getId() ?? null;

                        if (isset($cacheId)) {
                            return $cacheObj;
                        }

                        // 如果数据已被删除
                        $isDelete = call_user_func_array([$this->class,'basicIsDeleted'], $arguments);
                        if ($isDelete) {
                            return $cacheObj;
                        }
                    }
                    
                    // 走数据库
                    $dbObj = call_user_func_array([$this->class,'getById'], $arguments);
                    $dbId = method_exists($dbObj, 'getId') ? $dbObj->getId() : null;

                    if ($this->class->isAutoCache()) {
                        // 保存缓存
                        if (isset($dbId)) {
                            call_user_func_array([$this->class,'setByIdCache'], [$dbId,$dbObj->toArray()]);
                        }
                    }

                    return $dbObj;
                }

                /**
                 * get all
                 */
                if ($actionName == 'searchAll' || $actionName == 'getAll') {
                    $functionName;
                    if ($actionName == 'searchAll') {
                        $functionName = AsaEsConst::REDIS_BASIC_SEARCH_ALL;
                    } else {
                        $functionName = AsaEsConst::REDIS_BASIC_GET_ALL;
                    }

                    // 先在缓存中查
                    if ($this->class->isAutoCache()) {
                        $cacheList = call_user_func_array([$this->class,'getListCache'], [$functionName,$arguments]);

                        if (!empty($cacheList)) {
                            return $cacheList;
                        }
                    }

                    // 走数据库
                    $dbList = call_user_func_array([$this->class,$actionName], $arguments);

                    // 保存到数据库
                    if ($this->class->isAutoCache()) {
                        call_user_func_array([$this->class,'setListCache'], [$functionName,$arguments,$dbList]);
                    }

                    return $dbList;
                }


                /**
                 * 如果走了如下方法 将缓存清空
                 */
                $cacheFunction = [
                    'updateByIds','updateByField','insert','insertAll','deleteByField','deleteByIds'
                ];
                if (ArrayUtility::arrayFlip($cacheFunction, $actionName)) {
                    // 清空缓存
                    call_user_func_array([$this->class,'delListCache'],[$actionName]);
                }

                return call_user_func_array([$this->class,$actionName], $arguments);
            }
        }
    }
}
