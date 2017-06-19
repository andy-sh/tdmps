<?php
/**
 * 模块事件处理类
 * create time: 2011-12-15 06:36:33
 * @version $Id: class.module_event.inc.php 153 2012-10-26 05:45:34Z liqt $
 * @author zhangzhengqi
 */

class module_event extends scap_module_event
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function process_ui_event()
    {
        switch($this->current_method_name)
        {
            case 'edit_book':// book主表单
                if ($_POST['button']['btn_save'])// 保存事件
                {
                    $this->excute_event('book_save');
                }
                elseif (isset($_GET['b_id']) && $_GET['act'] ==  STAT_ACT_REMOVE)//删除事件
                {
                    $this->excute_event('book_remove');
                }
                break;
            case 'edit_book_config':
                if ($_POST['button']['btn_save'])// 保存事件
                {
                    $this->excute_event('book_config_save');
                }
                break;
            case 'import_book_step_1':// 导入book第1步
                if ($_POST['button']['btn_save'])// 保存事件
                {
                    $this->excute_event('book_import_step_1');
                }
                break;
            case 'import_book_step_2':// 导入book第2步
                if ($_POST['button']['btn_save'])// 保存事件
                {
                    $this->excute_event('book_import_step_2');
                }
                break;
            case 'import_book_step_3':// 导入book第3步
                if ($_POST['button']['btn_save'])// 保存事件
                {
                    $this->excute_event('book_import_step_3');
                }
                break;
            case 'create_node':// 创建book节点
                $this->excute_event('create_node');
                break;
            case 'remove_node':// 删除book节点
                $this->excute_event('remove_node');
                break;
            case 'empty_section':// 清空book节点
                $this->excute_event('empty_section');
                break;
            case 'force_remove_section':// 清空book节点
                $this->excute_event('force_remove_section');
                break;
            case 'rename_node':// 重命名节点
                $this->excute_event('rename_node');
                break;
            case 'move_node':// 移动book节点
                $this->excute_event('move_node');
                break;
        }
    }
    
    /**
     * book保存事件
     * 
     * @return bool true|false
     */
    protected function event_book_save()
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
        $data_in['post'] = trimarray($_POST['content']);
        
        $data_in['get']['b_id'] = $_GET['b_id'];// 机构主表内部id
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if ($data_in['get']['act'] == STAT_ACT_CREATE)
        {
            $max_book_count = book::get_config_book_max_book_count();
            if (book::get_book_count() >= $max_book_count)
            {
                scap_insert_sys_info('warn', "您的授权许可的书籍数量已经达到上限{$max_book_count}本，请升级您的许可。");
                $flag_save = false;
                
                return $result; // 【注意】返回
            }
        }
        
        if (!verify_content_legal($data_in['post']['b_name'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【书名】请填写。');
            $flag_save = false;
        }
        
        if (!verify_content_legal($data_in['post']['b_status'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【状态】请选择。');
            $flag_save = false;
        }
        
        if (!verify_content_legal($data_in['post']['tpl'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【模板】请选择。');
            $flag_save = false;
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据存储前的预处理[start]--------
        $data_save['content'] = $data_in['post'];
        
        switch($data_in['get']['act'])
        {
            case STAT_ACT_CREATE:
                try
                {
                    // 创建book主体
                    $book = book::create($data_save['content']);
                    
                    $book::set_book_tpl($book->get_current_object_id(), $data_save['content']['tpl']);
                    
                    // 插入book日志
                    $book->get_instance_log()->update_log(G_TYPE_AL_CREATE);
                    
                    // 为ui重定向编辑页面设置参数
                    $_GET['b_id'] = $book->get_current_object_id();
                }catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;// 【注意】返回
                }
                break;
            case STAT_ACT_EDIT:
                try
                {
                    $book = new book($data_in['get']['b_id']);
                    
                    // 更新book主体
                    $book->update($data_save['content']);
                    
                    $book::set_book_tpl($data_in['get']['b_id'], $data_save['content']['tpl']);
                    
                    // 插入book日志
                    $book->get_instance_log()->update_log(G_TYPE_AL_EDIT);
                
                }catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;// 【注意】返回
                }
                break;
            default:
                return $result;// 【注意】返回
        }
        
        
        $str_info = sprintf("【%s】操作已成功。",$data_in['post']['b_name']);
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * book导入第一步事件
     * 
     * @return bool true|false
     */
    protected function event_book_import_step_1()
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
        $data_in['post'] = trimarray($_POST['content']);
        
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (empty($_FILES['upload_file']['size']))
        {
            scap_insert_sys_info('warn', '上传文件不能为空。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        
        if (!in_array($_FILES['upload_file']['type'], array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed')))
        {
            scap_insert_sys_info('warn', '请上传有效的压缩文件。');
            $flag_save = false;
        }
        
        // 判断是否符合标准文件名，如：0492E8F085C63BAEB1D453A256988753.book.zip
//        if (!verify_content_legal($_FILES['upload_file']['name'], VCL_TYPE_CUSTOM, false, "/^.*(\.book\.zip)$/"))
//        {
//            scap_insert_sys_info('warn', '请上传可识别的数据文件。');
//            $flag_save = false;
//        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        $data_save['target_path'] = SCAP_PATH_ROOT."module_touchview_basic/templates/cache/{$_FILES['upload_file']['name']}";
        
        if(move_uploaded_file($_FILES["upload_file"]['tmp_name'], $data_save['target_path']))
        {
            $zip = new ZipArchive();
            $x = $zip->open($data_save['target_path']);
            if ($x === true)
            {
                $zip->extractTo(SCAP_PATH_ROOT."module_touchview_basic/templates/cache/");
                // 获取解压后的文件名称
                $_GET['file_name'] = $zip->getNameIndex(0);
                $zip->close();

                unlink($data_save['target_path']);
            }
        }
        else
        {
            scap_insert_sys_info('warn', '处理上传文件遇到问题。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        
        $str_info = sprintf("上传内容数据已成功。");
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    
    /**
     * book导入第一步事件
     * 
     * @return bool true|false
     */
    protected function event_book_import_step_2()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $flag_save = true;
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        
        $result = false;// 事件返回结果
        //--------变量定义及声明[end]--------
        
        //--------获取表单上传数据[start]--------
        $data_in['get']['file_name'] = $_GET['file_name'];
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据存储前的预处理[start]--------
        $data_save = json_decode(file_get_contents(SCAP_PATH_ROOT."module_touchview_basic/templates/cache/{$data_in['get']['file_name']}"), true);
        
        if (scap_entity::get_row_count('touchview_book', "b_id = '{$data_save['book']['b_id']}'") > 0)
        {
            $book = new book($data_save['book']['b_id']);
            $book->delete();
        }
        
        // 创建book主体
        $book = book::create($data_save['book']);

        // 插入book日志
        $book->get_instance_log()->update_log(G_TYPE_AL_CREATE, '导入书籍数据。');
        
        foreach($data_save['page'] as $v)
        {
            $page = page::create($v);
            
            // 插入page日志
            $page->get_instance_log()->update_log(G_TYPE_AL_EDIT, '导入书籍数据。');
        }
        
        $_GET['b_id'] = $data_save['book']['b_id'];
        
        $str_info = sprintf("【%s】导入内容数据已成功。",$data_save['book']['b_name']);
        scap_insert_sys_info('tip', $str_info);
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    
    /**
     * book导入第3步事件
     * 
     * @return bool true|false
     */
    protected function event_book_import_step_3()
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
        $data_in['get']['b_id'] = $_GET['b_id'];
        $data_in['post'] = trimarray($_POST['content']);
        
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (empty($_FILES['upload_file']['size']))
        {
            scap_insert_sys_info('warn', '上传文件不能为空。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        
        if (!in_array($_FILES['upload_file']['type'], array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed')))
        {
            scap_insert_sys_info('warn', '请上传有效的压缩文件。');
            $flag_save = false;
        }
        
        // 判断是否符合标准文件名，如：0492E8F085C63BAEB1D453A256988753.media.zip
//        if (!verify_content_legal($_FILES['upload_file']['name'], VCL_TYPE_CUSTOM, false, "/^.*(\.media\.zip)$/"))
//        {
//            scap_insert_sys_info('warn', '请上传可识别的数据文件。');
//            $flag_save = false;
//        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        $data_save['target_path'] = SCAP_PATH_ROOT."module_touchview_basic/templates/cache/{$_FILES['upload_file']['name']}";
        
        if(move_uploaded_file($_FILES["upload_file"]['tmp_name'], $data_save['target_path']))
        {
            $zip = new ZipArchive();
            $x = $zip->open($data_save['target_path']);
            if ($x === true)
            {
                $zip->extractTo(SCAP_PATH_ROOT."media/{$data_in['get']['b_id']}/");
                $zip->close();

                unlink($data_save['target_path']);
            }
        }
        else
        {
            scap_insert_sys_info('warn', '处理上传文件遇到问题。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        
        $str_info = sprintf("导入媒体数据已成功。");
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /** 
     * 删除book事件
     * 
     * @return bool true|false
     */
    protected function event_book_remove()
    {
        $data_in['get'] = array();// 保存表单获取到的get信息
        
        $result = false;// 事件返回结果
        //--------变量定义及声明[end]--------
        
        //--------获取表单上传数据[start]--------
        $data_in['get']['b_id'] = $_GET['b_id'];// 机构主表内部id
        $data_in['get']['b_name'] = $_GET['b_name'];
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        //--------获取表单上传数据[end]--------
        
        //--------数据库事物处理[start]--------
        try
        {
            $book = new book($data_in['get']['b_id']);
            $data_db['content'] = $book->read();
            
            // 删除book
            $book->delete();
                
            // 插入book日志
            $book->get_instance_log()->update_log(G_TYPE_AL_DELETE);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        //--------数据库事物处理[end]--------
        
        $str_info = sprintf("删除%s操作已成功。",$data_db['content']['b_name']);
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }

    /**
     * book结构创建节点事件
     * 节点只能是section或者page
     */
    protected function event_create_node()
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
        $data_in['get']['b_id'] = $_GET['b_id'];//book id
        $data_in['get']['p_type'] = $_GET['p_type'];//创建的实体类型
        $data_in['get']['parent_id'] = $_GET['parent_id'];// 父id
        $data_in['get']['name'] = $_GET['name'];//节点名称
        $data_in['get']['refer_id'] = $_GET['refer_id'];//顺序参考id
        $data_in['get']['position_type'] = $_GET['position_type'];//位置类型
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        $max_book_page_count = book::get_config_book_max_page_count();
        if (book::get_book_page_count($data_in['get']['b_id']) >= $max_book_page_count)
        {
            scap_insert_sys_info('warn', "您的授权许可的每本书页面数量已经达到上限{$max_book_page_count}页，请升级您的许可。");
            $flag_save = false;

            return $result; // 【注意】返回
        }
        
        if (!verify_content_legal($data_in['get']['b_id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '请给出所属书籍。');
            $flag_save = false;
        }
        
        if (!verify_content_legal($data_in['get']['name'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '名称不能为空。');
            $flag_save = false;
        }
        
        if (!($data_in['get']['p_type'] == TYPE_PAGE_NORMAL || $data_in['get']['p_type'] == TYPE_PAGE_SECTION))
        {
            scap_insert_sys_info('warn', '增加节点必须是章节或页面。');
            $flag_save = false;
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        $data_save['content'] = $data_in['get'];
        
        $data_save['content']['b_id'] = $data_in['get']['b_id'];
        $data_save['content']['p_parent_id'] = $data_in['get']['parent_id'];
        $data_save['content']['refer_id'] = $data_in['get']['refer_id'];
        $data_save['content']['p_type'] = $data_in['get']['p_type'];
        $data_save['content']['position_type'] = $data_in['get']['position_type'];
        $data_save['content']['p_name'] = ($data_in['get']['p_type'] == TYPE_PAGE_SECTION) ? $data_in['get']['name'] : '';
        $data_save['content']['p_content'] = '';

        try
        {
            $page = page::create($data_save['content']);

            // 插入page日志
            $page->get_instance_log()->update_log(G_TYPE_AL_CREATE);

            $str_info = sprintf("插入页面已成功。");
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * book结构删除节点事件
     */
    protected function event_remove_node()
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
        $data_in['get']['id'] = $_GET['id'];//id
        $data_in['get']['name'] = $_GET['name'];//节点名称
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['get']['id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '删除项无效。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        else
        {
            $page = new page($data_in['get']['id']);
            $data_db['content'] = $page->read();
        }
        
        if (!($data_db['content']['p_type'] == TYPE_PAGE_NORMAL || $data_db['content']['p_type'] == TYPE_PAGE_SECTION))
        {
            scap_insert_sys_info('warn', '删除节点必须是章节或者页面。');
            $flag_save = false;
        }
        
        if ($data_db['content']['p_type'] == TYPE_PAGE_SECTION)
        {
            // 检查是否有子节点，如果存在则不允许删除
            $sub_section = page::get_child_page_list($data_in['get']['id']);
            if (!empty($sub_section))
            {
                scap_insert_sys_info('warn', "{$data_in['get']['name']}下包含有字节点，请先清空所有字节点后再行删除。");
                $flag_save = false;
            }
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        try
        {
            $page->delete();
            $str_info = sprintf("删除页面【%s】已成功。", $data_in['get']['name']);
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * 清空章节事件
     */
    protected function event_empty_section()
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
        $data_in['get']['id'] = $_GET['id'];//id
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['get']['id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '指定章节无效。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        else
        {
            $page = new page($data_in['get']['id']);
            $data_db['content'] = $page->read();
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        try
        {
            page::empty_all_child_page($data_in['get']['id']);
            $str_info = sprintf("清空章节【%s】已成功。",$data_db['content']['p_name']);
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * 强制删除章节事件
     */
    protected function event_force_remove_section()
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
        $data_in['get']['id'] = $_GET['id'];//id
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['get']['id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '指定章节无效。');
            $flag_save = false;
            return $result; // 【注意】返回
        }
        else
        {
            $page = new page($data_in['get']['id']);
            $data_db['content'] = $page->read();
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        try
        {
            page::force_delete_page($data_in['get']['id']);
            $str_info = sprintf("强制删除章节【%s】已成功。",$data_db['content']['p_name']);
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * 重命名节点事件
     */
    protected function event_rename_node()
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
        $data_in['get']['id'] = $_GET['id'];//id
        $data_in['get']['name'] = $_GET['name'];//节点名称
        $data_in['get']['new_name'] = $_GET['new_name'];//修改后的名称
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['get']['id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '修改项无效。');
            $flag_save = false;
        }
                
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        try
        {
            $data_save['content']['p_name'] = $data_in['get']['new_name'];
            $page = new page($data_in['get']['id']);
            $page->update($data_save['content']);
            $page->get_instance_log()->update_log(G_TYPE_AL_EDIT, "重命名【{$data_in['get']['name']}】>【{$data_in['get']['new_name']}】。");
            $str_info = sprintf("重命名【{$data_in['get']['name']}】>【{$data_in['get']['new_name']}】已成功。");
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    /**
     * book结构移动节点事件
     */
    protected function event_move_node()
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
        $data_in['get']['id'] = $_GET['id'];//id
        $data_in['get']['name'] = $_GET['name'];//节点名称
        $data_in['get']['parent_id'] = $_GET['parent_id'];
        $data_in['get']['parent_name'] = $_GET['parent_name'];
        $data_in['get']['refer_id'] = $_GET['refer_id'];// 位置参考id
        
        switch($_GET['position'])
        {
            case 'last':
                $data_in['get']['position_type'] = page::TYPE_POSITION_INSERT_PAGE_LAST;
                break;
            case 'first':
                $data_in['get']['position_type'] = page::TYPE_POSITION_INSERT_PAGE_FIRST;
                break;
            case 'before':
                $data_in['get']['position_type'] = page::TYPE_POSITION_INSERT_PAGE_BEFORE;
                break;
            case 'after':
                $data_in['get']['position_type'] = page::TYPE_POSITION_INSERT_PAGE_AFTER;
                break;
            default:
                
        }
        
        $data_in['get']['b_id'] = page::get_bookid_from_id($data_in['get']['parent_id']);// 获取书籍id
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['get']['id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '移动项无效。');
            $flag_save = false;
        }
        
        if (!verify_content_legal($data_in['get']['parent_id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '新的父节点无效。');
            $flag_save = false;
        }
        
        if (!(strcasecmp($data_in['get']['b_id'], $data_in['get']['parent_id']) == 0 || page::get_type_from_id($data_in['get']['parent_id']) == TYPE_PAGE_SECTION))
        {
            scap_insert_sys_info('warn', '新的父节点必须是书籍或章节。');
            $flag_save = false;
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        try
        {
            $data_save['content']['p_parent_id'] = $data_in['get']['parent_id'];// 新的父节点
            
            // 1.计算新顺序
            page::update_page_sort($data_in['get']['b_id'], $data_in['get']['refer_id'], $data_in['get']['position_type'], $data_in['get']['id']);
            // 2.更新父节点
            $page = new page($data_in['get']['id']);
            $page->update($data_save['content']);

            $page->get_instance_log()->update_log(G_TYPE_AL_EDIT, "移动【{$data_in['get']['name']}】>【{$data_in['get']['parent_name']}】。");
            $str_info = sprintf("移动页面【{$data_in['get']['name']}】>【{$data_in['get']['parent_name']}】已成功。");
            scap_insert_sys_info('tip', $str_info);
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    
    /**
     * book配置保存事件
     * 
     * @return bool true|false
     */
    protected function event_book_config_save()
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
        $data_in['post'] = trimarray($_POST['content']);
        
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------

        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据存储前的预处理[start]--------
        $data_save['content'] = $data_in['post'];
        
        switch($data_in['get']['act'])
        {
            case STAT_ACT_EDIT:
                try
                {
                    book::set_config_book_auto_flip_switch(!empty($data_save['content']['config_auto_flip_switch']));
                    book::set_config_book_auto_flip_waiting($data_save['content']['config_auto_flip_waiting']);
                    book::set_config_book_max_book_count($data_save['content']['config_max_book_count']);
                    book::set_config_book_max_page_count($data_save['content']['config_max_page_count']);
                }catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;// 【注意】返回
                }
                break;
            default:
                return $result;// 【注意】返回
        }
        
        
        $str_info = sprintf("配置保存成功。");
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
}
?>