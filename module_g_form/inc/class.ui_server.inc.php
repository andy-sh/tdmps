<?php
/**
 * ui_server类实现文件
 * 
 * @package module_g_form
 * @subpackage view
 * @version $Id: class.ui_server.inc.php 931 2013-12-10 08:00:02Z liqt $
 * @creator liqt @ 2013-01-31 11:35:05 by caster0.0.2
 */

/**
 * ui_server类
 * 
 */
class ui_server extends scap_ui
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 为xheditor提供图片及其他文件上传应用接口
     * - 供upLinkUrl、upImgUrl、upFlashUrl和upMediaUrl参数调用
     * - 如所需upload参数不同，其他模块应用可参照实现该应用
     */
    public function upload_for_xheditor()
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo \scap\module\g_form\xheditor::excute_upload();
    }
    
    /**
     * 获取系统所有的session系统信息，并清除
     * - 供异步调用
     * - 返回json
     */
    public function load_system_info()
    {
        //--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		
		$data_in['sys_info'] = array();// 保存系统信息
		//--------变量定义及声明[end]--------
		
		// 系统反馈信息处理
		$data_in['scap_sys_info'] = scap_get_sys_info();
		
		if (!empty($data_in['scap_sys_info']))
		{
			$data_in['sys_info'] +=  $data_in['scap_sys_info'];// 获取系统反馈信息
			scap_clear_sys_info();// 清空系统反馈信息
		}
		
		echo json_encode($data_in['sys_info']);
    }
    
    /**
     * 关闭弹出的colorbox iframe窗口的应用
     * - $_GET['reload']: 不为false,则自动刷新父窗口
     * - 供使用colorbox iframe机制的弹出框调用
     * 
     */
    public function close_colorbox()
    {
        $data_in['get']['reload'] = $_GET['reload'];// 是否关闭后自动刷新父窗口
        
        if ($data_in['get']['reload'])
        {
            echo <<<RETURN
<script>
    parent.$.fn.colorbox.close(); // 关闭colorbox
    parent.location.reload();// 刷新父窗口
</script>
RETURN;
        }
        else
        {
            echo <<<RETURN
<script>
    parent.$.fn.colorbox.close(); // 关闭colorbox
</script>
RETURN;
        }
        exit;
    }
}
?>