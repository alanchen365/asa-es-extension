<?php

namespace AsaEs\Base;

use AsaEs\Base\BaseController;
use EasySwoole\Config;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 视图控制器
 * Class ViewController
 * @author  : evalor <master@evalor.cn>
 * @package App
 */
abstract class BaseViewController extends BaseController
{
    protected $view;

    /**
     * 初始化模板引擎
     * ViewController constructor.
     * @param string   $actionName
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(string $actionName, Request $request, Response $response)
    {
        $this->view = new \Smarty();
        $tempPath =  \AsaEs\Config::getInstance()->getConf('TEMP_DIR');
        $templateDir =  \AsaEs\Config::getInstance()->getConf('MAIN_SERVER.SETTING.document_root');

        $this->view->setCompileDir("{$tempPath}//");      # 模板编译目录
        $this->view->setCacheDir("{$tempPath}/cache/");              # 模板缓存目录
        $this->view->setTemplateDir($templateDir);    # 模板文件目录
        $this->view->setCaching(false);

        parent::__construct($actionName, $request, $response);
    }

    /**
     * 输出模板到页面
     * @param  string|null $template 模板文件
     * @author : evalor <master@evalor.cn>
     * @throws \Exception
     * @throws \SmartyException
     */
    public function fetch($template = null)
    {
        $content = $this->view->fetch($template);
        $this->response()->write($content);
        $this->view->clearAllAssign();
        $this->view->clearAllCache();
    }

    /**
     * 添加模板变量
     * @param array|string $tpl_var 变量名
     * @param mixed        $value   变量值
     * @param boolean      $nocache 不缓存变量
     * @author : evalor <master@evalor.cn>
     */
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        $this->view->assign($tpl_var, $value, $nocache);
    }
}
