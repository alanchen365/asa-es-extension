<?php
/**
 * Created by PhpStorm.
 * User: shixq
 * Date: 2019-06-18
 * Time: 15:34
 */

namespace AsaEs\Sdk\Baseservice;


use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class RbacService extends BaseBaseservice {


    /**
     * 用户信息
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function userInfo(int $userId,bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'id' => $userId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    #########################  jwt 相关开始 #########################

    public static function jwtDecode(string $token, bool $isIgnoreErr = false):array
    {
        // 参数整理
        $requestParams = [
            'token' => $token,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res['token'] ?? [];
    }

    #########################  jwt 相关结束 #########################


    #########################  用户相关开始 #########################

    /**
     * 登录
     * @param string $account  账号
     * @param string $password  密码
     * @param bool|null $isIgnoreErr 是否忽略错误
     * @param string|null $tokenType token类型  解释 pc | app
     * @return |null
     */
    public static function login(string $account, string $password, ?bool $isIgnoreErr = false,?string $tokenType = null):array
    {
        // 参数整理
        $requestParams = [
            'account' => $account,
            'password' => $password,
            'token_type' => $tokenType,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 登录
     * @param string $account
     * @param string $password
     * @param string $openid
     * @param string $accessToken
     * @param bool|null $isIgnoreErr
     * @return array
     */
    public static function loginWithOpenid(string $account, string $password, string $openid, string $accessToken, ?bool $isIgnoreErr = false,?string $tokenType = null): array
    {
        // 参数整理
        $requestParams = [
            'account' => $account,
            'password' => $password,
            'openid' => $openid,
            'access_token' => $accessToken,
            'system_id' => AppInfo::SYSTEM_ID,
            'token_type' => $tokenType,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 微信登录后，根据openid获取登录信息
     * @param string $openid
     * @param bool|null $isIgnoreErr
     * @return array
     */
    public static function getLoginInfoByOpenId(string $openid, ?bool $isIgnoreErr = false,?string $tokenType = null): array
    {
        // 参数整理
        $requestParams = [
            'openid' => $openid,
            'system_id' => AppInfo::SYSTEM_ID,
            'token_type' => $tokenType,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 用户列表
     * @param string $account
     * @param string $password
     * @param bool|null $isIgnoreErr
     * @return |null
     */
    public static function getUserList(?int $pageNo,?string $account, ?string $name, ?array $roleIds = [] , ?bool $isIgnoreErr = false, ?int $pageNum = null):array
    {
        // 参数整理
        $requestParams = [
            'page_no' => $pageNo,
            'page_num' => $pageNum,
            'account' => $account,
            'name' => $name,
            'role_ids' => $roleIds,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 添加用户
     * @param string $account  账户
     * @param string $name 用户名
     * @param string $password 密码
     * @param bool|null $isIgnoreErr 是否忽略错误
     * @return |null
     */
    public static function addUser(string $account, string $name, string $password, array $roleIds = [],?bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'account' => $account,
            'name' => $name,
            'password' => $password,
            'system_id' => AppInfo::SYSTEM_ID,
            'role_ids' => $roleIds,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res;
    }

    public static function addUserAll(array $accountList, string $name, string $password, array $roleIds = [],?bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'account_list' => $accountList,
            'name' => $name,
            'password' => $password,
            'system_id' => AppInfo::SYSTEM_ID,
            'role_ids' => $roleIds,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res;
    }

    /**
     * 获取用户单条
     * @param int $userId
     * @param bool|null $isIgnoreErr
     * @return array|null
     */
    public static function getUserById(int $userId,?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'id' => $userId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res;
    }

    /**
     * 删除用户单条
     */
    public static function delUserById(int $userId,?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'id' => $userId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res;
    }

    /**
     * 修改用户单条
     */
    public static function updateUserById(int $userId,?string $account, ?string $name, ?string $password ,?array $roleIds , ?bool $isIgnoreErr = false):void
    {
        // 参数整理
        $requestParams = [
            'account' => $account,
            'name' => $name,
            'password' => $password,
            'role_ids' => $roleIds,
        ];

        // 过滤空数组
        $requestParams = ArrayUtility::unsetEmpty($requestParams);
        if(Tools::superEmpty($requestParams)){
            return;
        }

        // 重新拼装参数
        $requestParams['system_id'] = AppInfo::SYSTEM_ID;
        $requestParams['id'] = $userId;

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);
    }

    /**
     * 修改密码
     */
    public static function changePwd(int $userId, string $oldPass, string $password, ?bool $isIgnoreErr = false) {
        // 参数整理
        $requestParams = [
            'user_id' => $userId,
            'old_pass' => $oldPass,
            'password' => $password,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }


    /**
     * 重置密码
     */
    public static function resetPwd(int $userId, string $password, ?bool $isIgnoreErr = false) {
        // 参数整理
        $requestParams = [
            'user_id' => $userId,
            'password' => $password,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 物理删除用户
     */
    public static function deleteUsersPhysically(array $userIds,?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'user_ids' => $userIds,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res;
    }

    #########################  用户相结束 #########################


    #########################  角色相关开始 #########################

    public static function getRoleList(?int $pageNo,?string $name,?string $description, ?bool $isIgnoreErr = false):array
    {
        // 参数整理
        $requestParams = [
            'page_no' => $pageNo,
            'name' => $name,
            'description' => $description,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Role',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    public static function addRole(string $name, ?string $key = null,string $description, array $menuIds = [], array $elementIds = [],?bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'name' => $name,
            'key' => $key,
            'description' => $description,
            'menu_ids' => $menuIds,
            'element_ids' => $elementIds,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 过滤空数组
        $requestParams = ArrayUtility::unsetEmpty($requestParams);

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Role',__FUNCTION__,$requestParams);

        return $res;
    }

    public static function getRoleById(int $roleId, ?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'id' => $roleId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Role',__FUNCTION__,$requestParams);

        return $res;
    }

    public static function updateRoleById(int $roleId,?string $name, ?string $key = null,?string $description, array $menuIds = [] , array $elementIds = [], ?bool $isIgnoreErr = false):void
    {
        // 参数整理
        $requestParams = [
            'description' => $description,
            'name' => $name,
            'key' => $key,
            'menu_ids' => $menuIds,
            'element_ids' => $elementIds,
        ];

        // 过滤空数组
        $requestParams = ArrayUtility::unsetEmpty($requestParams);
        if(Tools::superEmpty($requestParams)){
            return;
        }

        // 重新拼装参数
        $requestParams['system_id'] = AppInfo::SYSTEM_ID;
        $requestParams['id'] = $roleId;

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Role',__FUNCTION__,$requestParams);
    }

    public static function delRoleById(int $roleId, ?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'id' => $roleId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Role',__FUNCTION__,$requestParams);

        return $res;
    }

    #########################  角色相关结束 #########################


    #########################  菜单相关开始 #########################
    public static function getMenuList(?int $pageNo,?string $name,?int $parentId =null, ?bool $isIgnoreErr = false):array
    {
        // 参数整理
        $requestParams = [
            'page_no' => $pageNo,
            'name' => $name,
            'parent_id' => $parentId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Menu',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    public static function addMenu(string $name, string $domain, string $url , int $parentId, int $level, string $icon, int $sort, string $status,?bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'name' => $name,
            'domain' => $domain,
            'url' => $url,
            'parent_id' => $parentId,
            'level' => $level,
            'icon' => $icon,
            'sort' => $sort,
            'status' => $status,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 过滤空数组
        $requestParams = ArrayUtility::unsetEmpty($requestParams);

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Menu',__FUNCTION__,$requestParams);

        return $res;
    }

    public static function getMenuById(int $menuId, ?bool $isIgnoreErr = false):?array
    {
        // 参数整理
        $requestParams = [
            'id' => $menuId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Menu',__FUNCTION__,$requestParams);

        return $res;
    }

    public static function updateMenuById(int $menuId,?string $name, ?string $domain, ?string $url , ?int $parentId, ?int $level, ?string $icon, ?int $sort, ?string $status,?bool $isIgnoreErr = false):void
    {
        // 参数整理
        $requestParams = [
            'name' => $name,
            '$domain' => $domain,
            'url' => $url,
            'parentId' => $parentId,
            'level' => $level,
            'icon' => $icon,
            'sort' => $sort,
            'status' => $status,
        ];

        // 过滤空数组
        $requestParams = ArrayUtility::unsetEmpty($requestParams);
        if(Tools::superEmpty($requestParams)){
            return;
        }

        // 重新拼装参数
        $requestParams['system_id'] = AppInfo::SYSTEM_ID;
        $requestParams['id'] = $menuId;

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Menu',__FUNCTION__,$requestParams);
    }

    public static function delMenuById(int $menuId, ?bool $isIgnoreErr = false):void
    {
        // 参数整理
        $requestParams = [
            'id' => $menuId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Menu',__FUNCTION__,$requestParams);
    }


    #########################  菜单相关结束 #########################

    /**
     * 获取某个用户的页面按钮操作权限
     */
    public static function getViewButtonPermission(int $userId,string $group,bool $isIgnoreErr = false):array {

        // 参数整理
        $requestParams = [
            'group' => $group,
            'system_id' => AppInfo::SYSTEM_ID,
            'user_id' => $userId,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res['view_button_permission'] ?? [];
    }

//    /**
//     * 获取页面元素
//     */
//    public static function getTableButtonPermission(int $userId,string $group,bool $isIgnoreErr = false):array {
//
//        // 参数整理
//        $requestParams = [
//            'group' => $group,
//            'system_id' => AppInfo::SYSTEM_ID,
//            'id' => $userId,
//        ];
//
//        // 实例化请求类
//        $res = null;
//        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
//        $remoteService->setIsIgnoreErr($isIgnoreErr);
//        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
//        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);
//
//        return $res['button_permission_page'] ?? [];
//    }

    /**
     * 返回拥有角色的用户ID数组
     * @param array $userList
     * @param int $roleId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getUserListRoles(array $userIds, int $roleId, bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'user_ids' => $userIds,
            'role_id' => $roleId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    /**
     * 返回拥有角色的用户ID数组
     * @param array $userIds
     * @param array $roleIds
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getUserIdsInRoleIds(array $userIds, array $roleIds, bool $isIgnoreErr = false) {
        // 参数整理
        $requestParams = [
            'user_ids' => $userIds,
            'role_ids' => $roleIds,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res['user_ids'] ?? [];
    }

    /**
     * 修改用户的角色
     * @param int $userId
     * @param int $roleId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function updateUserRole(int $userId, int $roleId, bool $isIgnoreErr = false) {
        // 参数整理
        $requestParams = [
            'user_id' => $userId,
            'role_id' => $roleId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    public static function getRoleListByAccount(string $account, ?bool $isIgnoreErr = false)
    {
        // 参数整理
        $requestParams = [
            'account' => $account,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res ?? [];
    }


    /** 获取用户的角色 */
    public static function getRoleListWithUsers(array $userIds, bool $isIgnoreErr = false) {
        // 参数整理
        $requestParams = [
            'user_ids' => $userIds,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'User',__FUNCTION__,$requestParams);

        return $res['user_ids'] ?? [];
    }

}
