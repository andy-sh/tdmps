<?php
/**
 * 模块事件处理类实现文件
 * 
 * @package module_g_jquery
 * @subpackage controller
 * @version $Id: class.module_event.inc.php 205 2013-02-03 06:11:33Z liqt $
 * @creator liqt @ 2013-02-03 13:58:00 by caster0.0.2
 */

/**
 * module_g_jquery事件类
 * 
 */
class module_event extends scap_module_event
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 事件路由函数
     * @see scap_module_event::process_ui_event()
     */
    public function process_ui_event()
    {
        switch($this->current_method_name)
        {
            
        }
    }
    
}
?>