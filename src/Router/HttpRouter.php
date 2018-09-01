<?php

namespace AsaEs\Router;

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
    public function set():void
    {
        $this->loadConfig('web');
        $this->loadConfig('api');
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
        /**
         * Web 路由
         */
        $routeCollector->addGroup('/web', function (RouteCollector $route) {
            $routeArray = $this->router["web"];
            foreach ($routeArray as $routerArray) {
                foreach ($routerArray as $perfix =>  $routerFunction) {
                    $route->addGroup($perfix, $routerFunction);
                }
            }
        });

        /**
         * APP 路由
         */
        $routeCollector->addGroup('/api', function (RouteCollector $route) {
        });
    }

    /**
     * 写入配置文件
     */
    private function loadConfig(string $type) :void
    {
        $type = strtolower($type);
        $path = EASYSWOOLE_ROOT."/App/HttpController/Router/$type";
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
}
