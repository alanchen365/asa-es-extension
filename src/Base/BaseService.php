<?php

namespace AsaEs\Base;

use AsaEs\Exception\AppException;
use AsaEs\Proxy\DaoProxy;

class BaseService
{
    public function __call($actionName, $arguments)
    {
        if ($this->daoObj instanceof DaoProxy) {
            $ref = new \ReflectionClass($this->daoObj->getClass());
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic()) {
                return call_user_func_array([$this->daoObj,$actionName], $arguments);
            } else {
                throw new AppException(1013, "你调用了service中的{$actionName}()方法,但它不存在");
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
     * 纯属为了ide提示
     * @param array $params
     * @param array $searchLinkType
     * @param array $page
     * @param array $orderBys
     * @param array $groupBys
     * @return mixed
     */
    final public function getOneByField(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = [])
    {
        return  $this->getDaoObj()->getOneByField($params, $searchLinkType, $page, $orderBys, $groupBys);
    }
}
