<?php

namespace AsaEs\Utility;

use App\AppConst\AppInfo;
use App\AppConst\ListOperation;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use EasySwoole\Core\Component\Di;
use ReflectionClass;
use think\validate\ValidateRule;

trait View
{

    /**
     * 分页处理方法.
     *
     * @param null $page_no
     * @param int  $page_size
     */
    public function pageParam(int $no = 1, int $pageNum): array
    {
        // 前端不传递分页 给个默认
        $pageNum = 0 === $pageNum ? AppInfo::APP_PAGE_DEFAULT_NUM : $pageNum;

        if (0 === $no) {
            return [0, intval($pageNum)];
        }

        $no = $no > 0 ? $no : 1;
        $offset = ($no - 1) * $pageNum;

        return [intval($offset), intval($pageNum)];
    }

    /**
     * 去掉命名空间获取类名对象(全小写).
     */
    public function getModuleResultsName(string $className, string $type = AsaEsConst::RESULTS_RETURN_TYPE_OBJ): string
    {
        $className = explode('\\', $className);
        $resultsName = (string) end($className);

        return strtolower($resultsName.'_'.$type);
    }

    /**
     * 获取列表操作项判断 TODO 获取roid.
     *
     * @param string $name     配置文件中名称
     * @param bool   $rDefault 当role_id不存在时都显示还是都不显示
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getListOperation(String $name, bool $rDefault = true): array
    {
        $reflect = new ReflectionClass(get_class(new ListOperation()));
        $name = empty($name) ? 'DEFAULT' : strtoupper($name);
        $operations = $reflect->getConstant($name);
        if (false === $operations || !is_array($operations)) {
            return [];
        }

        //根据TOKEN获取rid
        $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
        $tokenObj = $esRequest->getTokenObj();
        $rId = $tokenObj->rids;

        $data = [];
        foreach ($operations as $k => $operation) {
            if ($rDefault) {
                if (!isset($operation['role_id']) || !empty(array_intersect($operation['role_id'], $rId))) {
                    unset($operation['role_id']);
                    $data[] = $operation;
                }
            } else {
                if (isset($operation['role_id']) && !empty(array_intersect($operation['role_id'], $rId))) {
                    unset($operation['role_id']);
                    $data[] = $operation;
                }
            }
        }

        return $data;
    }
}
