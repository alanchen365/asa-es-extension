<?php

namespace AsaEs\Base;

use AsaEs\Proxy\DaoProxy;

class BaseService
{
    public function __call($actionName, $arguments)
    {
        if ($this->daoObj instanceof DaoProxy) {
            $ref = new \ReflectionClass($this->daoObj->getClass());
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic()) {
                return call_user_func_array([$this->daoObj,$actionName], $arguments);
            }
        }
    }

    /**
     * dao对象
     *
     * @var
     */
    protected $daoObj;

    /**
     * @return mixed
     */
    public function getDaoObj()
    {
        return $this->daoObj;
    }

    /**
     * @param mixed $daoObj
     */
    public function setDaoObj($daoObj): void
    {
        // 这里先走代理
        $this->daoObj = new DaoProxy($daoObj);
    }

    /**
     * 列表查询
     */
    public function index()
    {
    }
}
