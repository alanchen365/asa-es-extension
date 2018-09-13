<?php
namespace AsaEs\Base;

use AsaEs\Exception\AppException;
use think\Validate;
use think\validate\ValidateRule;

class BaseValidate extends Validate
{
    /**
     * 验证和字段绑定.
     *
     * @var array
     */
    protected $bindingField = [];

    /**
     * 搜索数据类型配置.
     */
    protected $searchParam = [];

    /**
     * @return mixed
     */
    public function getSearchParam(): array
    {
        return $this->searchParam;
    }

    /**
     * 获取绑定关系.
     *
     * @return array
     */
    public function getBindingField(array $userInputData): array
    {

        $data = [];
        $bindingField = array_keys($this->rule) ?? [];
        foreach ($bindingField as $verifyKey) {
            if (isset($userInputData[$verifyKey])) {
                $data[$verifyKey] = $userInputData[$verifyKey];
            }
        }
        
        return $data;
    }

    /**
     * @param $vData
     *
     * @throws AppException
     */
    public function verify(string $scene, array $vData, int $code = 1000): void
    {
        if(!empty($this->scene['$scene'])){
            if (!$this->scene($scene)->check($vData)) {
                throw new AppException($code, $this->getError());
            }
        }
    }
}
