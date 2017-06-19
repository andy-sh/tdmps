<?php
/**
 * 模块事件处理类
 * create time: 2011-11-21 上午10:15:42
 * @version $Id: class.module_event.inc.php 4 2012-07-21 07:04:47Z liqt $
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
            case delete_image:
                if (!empty($_GET['path']))
                {
                    $this->excute_event('image_delete');
                }
                break;
        }
    }
    
    /**
     * 图片删除事件
     * 
     */
    protected function event_image_delete()
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
        $data_in['get']['path'] = SCAP_PATH_ROOT.$_GET['path']; // 图片完整路径
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据存储前的预处理[start]--------
        
        //--------数据存储前的预处理[end]--------
        
        //--------数据库事物处理[start]--------
        $result = unlink($data_in['get']['path']);
        //--------数据库事物处理[end]--------
        
        
        return $result; // 【注意】返回
    }
}
?>