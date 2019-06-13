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
     * 按列删除
     * @param array $fieldValues
     */
    public function deleteByField(array $fieldValues) :void
    {
        $this->getDaoObj()->deleteByField($fieldValues);
    }

    /**
     * 获取单条
     * @param int|null $id
     * @return mixed
     */
    public function getById(?int $id)
    {
        return $this->getDaoObj()->getById($id);
    }

    /**
     * 根据某列获取单条
     * @param array $params
     * @param array $searchLinkType
     * @param array $page
     * @param array $orderBys
     * @param array $groupBys
     * @return mixed
     */
    public function getOneByField(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = [])
    {
        return  $this->getDaoObj()->getOneByField($params, $searchLinkType, $page, $orderBys, $groupBys);
    }

    /**
     * 查列表
     * @param array $params
     * @param array $searchLinkType
     * @param array $page
     * @param array $orderBys
     * @param array $groupBys
     * @return array
     */
    public function getAll(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = []): array
    {
        return  $this->getDaoObj()->getAll($params, $searchLinkType, $page, $orderBys, $groupBys);
    }

    /**
     * 搜索
     * @param array $params
     * @param array $searchLinkType
     * @param array $page
     * @param array $orderBys
     * @param array $groupBys
     * @return array
     */
    public function searchAll(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = []): array
    {
        return  $this->getDaoObj()->searchAll($params, $searchLinkType, $page, $orderBys, $groupBys);
    }

    /**
     * 根据id删除
     * @param array $ids
     */
    public function deleteByIds(array $ids):void
    {
        $this->getDaoObj()->deleteByIds($ids);
    }

    /**
     * 批量插入
     * @param array $params
     * @return array
     */
    public function insertAll(array $params): array
    {
        return $this->getDaoObj()->insertAll($params);
    }

    /**
     * 插入单条数据
     * @param array $params
     */
    public function insert(array $params) :int
    {
        return $this->getDaoObj()->insert($params);
    }

    /**
     * 更新全表中某列等于某个值的所有数据.
     *
     * @param string $field
     * @param string $value
     *
     * @throws MysqlException
     */
    public function updateByField(array $originalFieldValues, array $updateFieldValues): void
    {
        $this->getDaoObj()->updateByField($originalFieldValues, $updateFieldValues);
    }

    /**
     * 根据id更新
     * @param array $ids
     * @param array $params
     * @return mixed
     */
    public function updateByIds(array $ids, array $params)
    {
        return $this->getDaoObj()->updateByIds($ids, $params);
    }

    /**
     * 截断表
     * @return mixed
     */
    public function truncate()
    {
        return $this->getDaoObj()->truncate();
    }
}
