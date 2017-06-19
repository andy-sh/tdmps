<?php
/**
 * description: 系统界面模版类
 * create time: 2006-10-18 18:57:30
 * @version $Id: core.class.scap_tpl.inc.php 73 2013-01-24 09:40:42Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

require_once(SCAP_PATH_LIBRARY.'smarty3/Smarty.class.php');

class scap_tpl extends Smarty {
	/**
	 * @var string 模板路径
	 */
	var $tpl_root_dir;
	
	/**
	 * @var string 默认模板名称
	 */
	var $default_dir;
	
	/**
	 * @var string 当前模板名称
	 */
	var $cur_tpl;
	
	/**
	 * 构造函数
	 * 
	 * 进行smarty初始设置
	 */
	public function __construct() 
	{
	    parent::__construct();
		$this->compile_dir = SCAP_PATH_ROOT . "cache/";
		$this->cache_dir = SCAP_PATH_ROOT . "cache/";
		$this->left_delimiter = "{<";
		$this->right_delimiter = ">}";
	}
	
	/**
	 * 设置模块的模版路径
	 * 
	 * @param string $moudle_name 模块名称
	 * @param string $template 模版名称,默认为'default'
	 */
	function set_module_tpl($moudle_name, $template = 'default')
	{
		$this->addTemplateDir(SCAP_PATH_ROOT.$moudle_name.'/templates/'.$template);
		$this->cache_dir = $this->compile_dir = SCAP_PATH_ROOT.$moudle_name.'/templates/cache';
	}
}
?>