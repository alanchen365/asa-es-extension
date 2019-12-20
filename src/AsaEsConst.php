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
    // websock 进程
    const PROCESS_WEBSOCK= 'PROCESS_WEBSOCK';
    // 入参和出参LOG分类标识
    const LOG_REQUEST_RESPONSE = 'REQUEST_RESPONSE';
    // 数据库查询的log
    const LOG_MYSQL_QUERY = 'MYSQL_QUERY';
    // 慢日志log
    const LOG_SLOW = 'SLOW';
    // 访问log
    const LOG_ACCESS = 'ACCESS';
    // request id
    const REQUEST_ID = 'request_id';
    // 框架级定时器
    const PROCESS_TIMER = "PROCESS_TIMER";
    // 日志超过几天自动删除 (默认30天)
    const AUTO_CLEAR_LOG_DAY = (60 * 60 * 24) * 30;
    // 日志删除排除文件
    const AUTO_CLEAR_LOG_FILTER = ['swoole.log'];
    // redis 默认过期时间  (1天)
    const REDIS_DEFAULT_EXPIRE_ONE_DAY = (60 * 60 * 24) * 1;
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
    // 路由前缀 app（手机app） web (H5) view(套php模板) public(公用无需鉴权) api(待定) pc (pc端)
    const HTTP_ROUTER_REGISTER_TYPE = ['app', 'web',  'view', 'public', 'api', 'pc'];
    // 平台 app（手机app） web (H5) view(套php模板) public(公用无需鉴权) api(待定) pc (pc端)
    const TOKEN_TYPE = [
        'app' => '8ikj2s9j',
        'web' => '2s8i9jkj',
        'pc' => 's9kj2j8i',
    ];

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
        'update_user_id',
        'modify_user',
        'modify_user_id',
    ];

    //  Mysql 删除时间
    const MYSQL_AUTO_DELETETIME = [
        'delete_time',
    ];

    // mysql 删除用户
    const MYSQL_AUTO_DELETEUSER = [
        'delete_user',
        'delete_user_id',
    ];

    // mysql 添加时间
    const MYSQL_AUTO_INSERTTIME = [
        'create_time',
    ];

    // mysql 添加用户
    const MYSQL_AUTO_INSERTUSER = [
        'create_user',
        'create_user_id',
    ];
}
