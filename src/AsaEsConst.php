<?php

namespace AsaEs;

use AsaEs\Config\Router;
use AsaEs\Router\HttpRouter;
use AsaEs\Utility\Env;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Utility\File;

class AsaEsConst
{
    // 每个请求中的request对象
    const DI_REQUEST_OBJ = 'REQUEST_OBJ';

    // Mysql默认实例
    const DI_MYSQL_DEFAULT = "MYSQL_DEFAULT";

    // Redis默认实例
    const DI_REDIS_DEFAULT = 'REDIS_DEFAULT';

    // 服务热重启
    const PROCESS_AUTO_RELOAD = 'AUTO_RELOAD';

    // 入参和出参LOG分类标识
    const LOG_REQUEST_RESPONSE = 'REQUEST_RESPONSE';

    // 数据库查询的log
    const LOG_MYSQL_QUERY = 'MYSQL_QUERY';

    // request id
    const REQUEST_ID = 'request_id';

    // redis 默认过期时间  (秒)
    const REDIS_DEFAULT_EXPIRE = (60 * 60 * 24) * 30;

    // redis 基础数据分类
    const REDIS_BASIC_TYPE = 'BASIC';

    // redis 基础数据已删除的key
    const REDIS_BASIC_DELETED = 'BASIC_DELETED';

    // redis 基础数据源 search all 的列表
    const REDIS_BASIC_SEARCH_ALL = 'SEARCH_ALL';

    // redis 技术数据 get all 的列表
    const REDIS_BASIC_GET_ALL = 'GET_ALL';

    // 控制器中列表返回类型
    const RESULTS_RETURN_TYPE_LIST = 'list';
    const RESULTS_RETURN_TYPE_OBJ = 'obj';
    const RESULTS_RETURN_OPTION_LIST  = 'option_list';

    // 分页参数
    const PAGE_KEY = 'page_no';
    // 分页数量
    const PAGE_NUM_KEY = 'page_num';

    // Mysql 逻辑删除
    const MYSQL_AUTO_LOGICDELETE = [
        'delete_flg',
        'deleteflg'
    ];

    // Mysql 更新时间
    const MYSQL_AUTO_UPDATETIME = [
        'update_time',
        'modify_time',
    ];

    // Mysql 更新用户
    const MYSQL_AUTO_UPDATEUSER = [
        'update_user',
        'modify_user',
    ];

    //  Mysql 删除时间
    const MYSQL_AUTO_DELETETIME = [
        'delete_time',
    ];

    // mysql 删除用户
    const MYSQL_AUTO_DELETEUSER = [
        'delete_user',
    ];

    // mysql 添加时间
    const MYSQL_AUTO_INSERTTIME = [
        'create_time',
    ];

    // mysql 添加用户
    const MYSQL_AUTO_INSERTUSER = [
        'create_user',
    ];
}
