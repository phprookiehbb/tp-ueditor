<?php

/**
 * @Author: CraspHB彬
 * @Date:   2018-12-29 11:23:53
 * @Email:   646054215@qq.com
 * @Last Modified time: 2018-12-29 15:30:12
 */
namespace Crasphb\upload;
use think\Config;
use think\View;
class Ueditor{
	public $path = '';
	public $template = [
	        // 模板引擎类型 支持 php think 支持扩展
	        'type'         => 'Think',
	        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
	        'auto_rule'    => 1,
	        // 模板路径
	        'view_path'    => '',
	        // 模板后缀
	        'view_suffix'  => 'html',
	        // 模板文件名分隔符
	        'view_depr'    => DS,
	        // 模板引擎普通标签开始标记
	        'tpl_begin'    => '{',
	        // 模板引擎普通标签结束标记
	        'tpl_end'      => '}',
	        // 标签库标签开始标记
	        'taglib_begin' => '{',
	        // 标签库标签结束标记
	        'taglib_end'   => '}',
    ];  	
    public $view;
    /**
     * 构造函数
     */
	public function __construct(){
        
        $this->path = VENDOR_PATH.'phprookiehbb\tp-ueditor\src\upload'.DS;
        
        //实例化视图
        $template = $this->template;
        $template['view_path'] = $this->path;
        $this->view = new View($template);
    }
    
    /**
     * 模板输出
     * @param  string $template [description]
     * @param  [type] $vars     [description]
     * @param  [type] $replace  [description]
     * @param  [type] $config   [description]
     * @return [type]           [description]
     */
 	public function fetch($template = '', $vars = [], $replace = [], $config = []){
        //关闭模板布局
        $this->view->engine->layout(false);
        if(!empty($template)){
        	$template = '/'.$template;
        }else{
            $template = '/ueditor';
        }
        echo $this->view->fetch($template,$vars,$replace,$config);
    }	
    public function index($params){
    	$this->view->assign('params',json_encode($params));
		return $this->fetch();
	}
}