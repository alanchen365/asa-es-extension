<?php
namespace AsaEs\Base;

use AsaEs\Exception\AppException;
use AsaEs\Utility\Tools;

class BaseBean
{
    const FILTER_NOT_NULL  = 1;
    const FILTER_NOT_EMPTY = 2; // 0 不算empty

    /**
     * 数据表名.
     */
    protected static $_tableName;

    /**
     * 表前缀
     */
    protected static $_prefix;

    /**
     * 主键
     */
    protected static $_primaryKey = 'id';

    /**
     * 获取所有列
     * @return mixed
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * 获取所有列
     * @param mixed $fields
     */
    public function setFields($fields): void
    {
        $this->_fields = $fields;
    }

    public function __construct(array $data = null, $autoCreateProperty = true)
    {
        // 数据填前做一些操作
        $this->afterInitialize();

        if ($data) {
            $this->arrayToBean($data, $autoCreateProperty);
        }
    }

    /**
     * 数组转为Bean
     * @param array $data
     * @param bool  $autoCreateProperty
     * @return SplBean
     */
    final public function arrayToBean(array $data, $autoCreateProperty = true): BaseBean
    {
        // 防止数据错乱 每一次赋值 都获取一个新的对象
        $modelName = Tools::getModelNameByClass(get_called_class());
        $newBeanInstance = new $modelName();

        // 如果初始化为空 就返回一个空的bean
        if (empty($data)) {
            foreach ($this->_fields as $field) {
                unset($newBeanInstance->$field);
            }

            return $newBeanInstance;
        }

        if ($autoCreateProperty == false) {
            $data = array_intersect_key($data, array_flip($this->allProperty()));
        }

        /**
         * 批量写数据
         */
        foreach ($this->_fields as $field) {
            if (isset($data[$field])) {
                $newBeanInstance->addProperty($field, $data[$field]);
            }
        }

        // 数据填充完毕后之后做一些操作
        $newBeanInstance->beforeInitialize();
        return $newBeanInstance;
    }

    /**
     * 添加成员
     * @param string $name
     * @param mixed  $value
     */
    final public function addProperty($name, $value = null): void
    {
        $this->$name = $value;
    }

    /**
     * 数据填充前做一些操作
     */
    protected function beforeInitialize(): void
    {
    }

    /**
     * 数据填充后做一些操作
     */
    protected function afterInitialize():void
    {
    }
    /**
     * 输出为数组
     * @param array|null $columns
     * @param mixed      $filter
     * @return array
     */
    function toArray(array $columns = null, $filter = null): array
    {
        $data = $this->jsonSerialize();
        if ($columns) {
            $data = array_intersect_key($data, array_flip($columns));
        }
        if ($filter === self::FILTER_NOT_NULL) {
            return array_filter($data, function ($val) {
                return !is_null($val);
            });
        } elseif ($filter === self::FILTER_NOT_EMPTY) {
            return array_filter($data, function ($val) {
                if ($val === 0 || $val === '0') {
                    return true;
                } else {
                    return !empty($val);
                }
            });
        } elseif (is_callable($filter)) {
            return array_filter($data, $filter);
        }
        unset($data['_fields']);
        return $data;
    }

    /**
     * 序列化
     * @return array
     */
    final public function jsonSerialize(): array
    {
        // TODO: Implement jsonSerialize() method.
        $data = [];
        foreach ($this as $key => $item) {
            $data[$key] = $item;
        }
        return $data;
    }

    public function __call($actionName, $arguments)
    {
        $modelName = Tools::getLastNameSpaceName(get_called_class());

        $ref = new \ReflectionClass($this);
        if (!($ref->hasMethod($actionName) && $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic())) {
            throw new AppException(1013, "你调用了{$modelName}中的{$actionName}()方法,但它不存在");
        }
    }

    /**
     * 对private变量可写
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }

    public function &__get(string $name)
    {
        return $this->$name;
    }
}
