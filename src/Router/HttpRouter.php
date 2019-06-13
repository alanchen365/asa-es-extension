<?php

namespace AsaEs\Router;

use AsaEs\AsaEsConst;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Utility\File;
use FastRoute\RouteCollector;
use think\validate\ValidateRule;

class HttpRouter
{
    protected $router = [];

    use Singleton;

    /**
     * 获取路由
     * Router constructor.
     */
    public function __construct()
    {
    }

    /**
     * 分别获取web和api的路由
     */
    public function registered():void
    {
        $this->loadConfig();
    }

    /**
     * 获取所有配置
     */
    public function get() :array
    {
        return $this->router;
    }

    /**
     * 路由注册
     */
    public function register(RouteCollector $routeCollector):void
    {
        // 兼容老路由
        foreach (AsaEsConst::HTTP_ROUTER_REGISTER_TYPE as $type){
            $routeCollector->addGroup("/$type", function (RouteCollector $route) use($type) {
                $routeArray = $this->router[$type] ?? [];
                foreach ($routeArray as $routerArray) {
                    if (is_array($routerArray)) {
                        foreach ($routerArray as $perfix =>  $routerFunction) {
                            $route->addGroup($perfix, $routerFunction);
                        }
                    }
                }
            });
        }
    }

    /**
     * 写入配置文件
     */
    private function loadConfig() :void
    {
        // 兼容老路由
        foreach (AsaEsConst::HTTP_ROUTER_REGISTER_TYPE as $type){

            $type = strtolower($type);
            $path = EASYSWOOLE_ROOT."/App/HttpRouter/$type";
            $files = File::scanDir($path) ?? [];

            foreach ($files as $file) {
                $data = require_once $file;

                // 如果配置文件是空 就跳过
                if (empty($data)) {
                    continue;
                }

                $this->router[$type][] = $data;
            }
        }

        // 动态获取路由
        $path = EASYSWOOLE_ROOT."/App/Module";
        $files = File::scanDir($path,File::TYPE_DIR) ?? [];

        // 获取路由文件下所有目录
        foreach ($files as $dir) {
            
            $routeFiles = File::scanDir($dir.'/Route',File::TYPE_FILE) ?? [];
            foreach ($routeFiles as $item =>$file){
                $data = require_once $file ?? [];

//              如果配置文件是空 就跳过
                if (empty($data)) {
                    continue;
                }

                $this->router[basename($file,'.php')][] = $data;
            }
        }
    }
}
