<?php
/**
 * 模块事件处理类
 * create time: 2011-12-13 下午04:29:48
 * @version $Id: class.module_event.inc.php 153 2012-10-26 05:45:34Z liqt $
 * @author LiQintao
 */

class module_event extends scap_module_event
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process_ui_event()
    {
        switch($this->current_method_name)
        {
            case 'save_page_content':
                $this->excute_event('page_save_content');
                break;
        }
    }
    
    /**
     * page保存事件
     * 
     * @return bool true|false
     */
    protected function event_page_save_content()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $flag_save = true;
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        
        $data_save['content'] = array();// 主表单内容保存数据
        
        $result = false;// 事件返回结果
        //--------变量定义及声明[end]--------
        
        //--------获取表单上传数据[start]--------
        $data_in['post'] = $_POST['content'];
        
        $data_in['get']['p_id'] = $_GET['p_id'];// page id
        $data_in['get']['tpl'] = $_GET['tpl'];// page tpl
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据存储前的预处理[start]--------
        $data_save['content']['p_content'] = $data_in['post'];
        
        try
        {
            $page = new page($data_in['get']['p_id']);

            // 更新page主体
            $page->update($data_save['content']);
            
            $page::set_page_tpl($data_in['get']['p_id'], $data_in['get']['tpl']);

            // 插入page日志
            $page->get_instance_log()->update_log(G_TYPE_AL_EDIT);

        }
        catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        
        $str_info = sprintf("页面保存已成功。");
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
}
?>