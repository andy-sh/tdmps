<?php
/**
 * 图片处理服务UI文件
 * create time: 2012-3-7 下午04:32:35
 * @version $Id: class.ui_image_server.inc.php 4 2012-07-21 07:04:47Z liqt $
 * @author LiQintao
 */

class ui_image_server extends scap_module_ui
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 删除指定图片
     * 
     * @return bool true | false
     */
    public function delete_image()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['content'] = array();// 保存主表单数据
        //--------变量定义及声明[end]--------

        //--------GET参数处理[start]--------
        $data_in['get']['path'] = $_GET['path']; // 图片路径,相对当前系统的路径
        //--------GET参数处理[end]--------
        
        $data_out['message'] = false;
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'image_delete':
                $data_out['message'] = $this->current_event_result;
                break;
        }
        //--------消息/事件处理[end]--------

        echo $data_out['message'];
    }
}
?>