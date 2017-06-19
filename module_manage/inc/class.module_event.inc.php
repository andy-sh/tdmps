<?php
/**
 * description:系统管理事件处理类
 * create time: 2010-3-10 03:25:37
 * @version $Id: class.module_event.inc.php 145 2013-08-22 05:43:43Z liqt $
 * @author FuYing
 */

class module_event extends scap_module_event
{
    function __construct()
    {
        parent::__construct();

        scap_load_module_define('module_g_00', 'log_type');// 加载通用日志类型的定义
    }

    public function process_ui_event()
    {
        switch($this->current_method_name)
        {
            case 'edit_account':
                if ($_POST['button']['btn_save'])
                {
                    $this->excute_event('account_save');
                }
                else if ($_POST['button']['btn_set_pw'])
                {
                    $this->excute_event('account_set_pw');
                }
                else if (isset($_GET['a_s_id']) && $_GET['act'] ==  STAT_ACT_REMOVE)
                {
                    $this->excute_event('account_remove');
                }
                break;
            case 'edit_config':
                if ($_POST['button']['btn_save'])
                {
                    $this->excute_event('config_save');
                }
                break;
            case 'index_module':
                if ($_POST['button']['btn_order'] || $_POST['btn_register'] || $_POST['btn_update'] || $_POST['btn_unregister'] || $_POST['btn_start'] || $_POST['btn_stop'])
                {
                    $this->excute_event('module_save');
                }
                break;
            case 'assign_acl':
                if ($_POST['button']['btn_save'])
                {
                    $this->excute_event('assign_acl');
                }
                break;
            case 'import_sql':
                if ($_POST['button']['btn_import'])
                {
                    $this->excute_event('import_sql');
                }
                break;
        }
    }

    //保存账户
    protected function event_account_save()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $flag_save = true;
        $result = false;// 事件返回结果

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息

        $data_save['content'] = array();// 主表单内容保存数据
        //--------变量定义及声明[end]--------

        //--------获取表单上传数据[start]--------
        $data_in['post'] = trimarray($_POST['content']);
        $data_in['button'] = trimarray($_POST['button']);
        $data_in['get']['a_s_id'] = $_GET['a_s_id'];// 系统帐号id
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        //--------获取表单上传数据[end]--------

        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['post']['a_c_login_id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【登录名称】请填写。');
            $flag_save = false;
        }

        if ($data_in['get']['act'] == STAT_ACT_CREATE)
        {
            if (scap_db_get_row_count(NAME_T_SYS_ACCOUNTS, "a_c_login_id = '{$data_in['post']['a_c_login_id']}'") >= 1)
            {
                scap_insert_sys_info('warn', '【登录名称】不能和现有登录名称重复!');
                $flag_save = false;
            }
        }

        if (!verify_content_legal($data_in['post']['a_c_display_name'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【显示名称】请填写。');
            $flag_save = false;
        }

        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------

        //--------数据库事物处理[start]--------
        $data_save['content'] = $data_in['post'];

        try
        {
            if ($data_in['get']['act'] == STAT_ACT_CREATE)
            {
                // 创建sys_account主体
                $account = sys_account::create($data_save['content']);
                
                // 插入日志
                $account->get_instance_log()->update_log(G_TYPE_AL_CREATE);
            }
            elseif ($data_in['get']['act'] == STAT_ACT_EDIT)
            {
                $account = new sys_account($data_in['get']['a_s_id']);
                //仅更新用户基本信息
                $account->update($data_save['content']);
                
                // 插入日志
                $account->get_instance_log()->update_log(G_TYPE_AL_EDIT);
            }

            // 为ui重定向编辑页面设置参数
            $_GET['a_s_id'] = $account->get_current_object_id();

        }
        catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        //--------数据库事物处理[end]--------

        $str_info = sprintf("【%s】操作已成功。", $data_in['post']['a_c_display_name']);
        scap_insert_sys_info('tip', $str_info);

        $result = true;// 执行成功标志

        return $result; // 【注意】返回
    }

    //设置账户密码
    protected function event_account_set_pw()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $flag_save = true;
        $result = false;// 事件返回结果

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息

        $data_save['content'] = array();// 主表单内容保存数据
        //--------变量定义及声明[end]--------

        //--------获取表单上传数据[start]--------
        $data_in['post'] = trimarray($_POST['content']);
        $data_in['button'] = trimarray($_POST['button']);
        $data_in['get']['a_s_id'] = $_GET['a_s_id'];// 系统帐号id
        //--------获取表单上传数据[end]--------

        //--------输入合法性检查[start]--------
        if ($_POST['content']['a_new_password'] != $_POST['content']['a_confirm_new_password'])
        {
            scap_insert_sys_info('warn', '【确认新口令】与【新口令】不一致!');
            $flag_save = false;
        }

        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------       

        //--------数据库事物处理[start]--------
        $data_save['content']['a_s_password'] = scap_encrypt_password($data_in['post']['a_new_password']);

        try
        {
            $account = new sys_account($data_in['get']['a_s_id']);
            //仅更新用户密码
            $account->update($data_save['content']);

            // 插入日志
            $account->get_instance_log()->update_log(G_TYPE_AL_EDIT, '设置口令');

            // 为ui重定向编辑页面设置参数
            $_GET['a_s_id'] = $account->get_current_object_id();
        }
        catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        //--------数据库事物处理[end]--------       

        $str_info = sprintf("【%s】设置口令操作已成功。", $data_in['post']['a_c_display_name']);
        scap_insert_sys_info('tip', $str_info);

        $result = true;// 执行成功标志

        return $result; // 【注意】返回

    }

    //移除账户
    protected function event_account_remove()
    {
        //--------变量定义及声明[start]--------
        $flag_save = true;
        $result = false;// 事件返回结果

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_db['content'] = array();//从数据库中获得数据
        //--------变量定义及声明[end]--------

        //--------获取表单上传数据[start]--------
        $data_in['get']['a_s_id'] = $_GET['a_s_id'];// 系统帐号id
        //--------获取表单上传数据[end]--------

        //--------输入合法性检查[start]--------
        try
        {
            //获得sys_account实体对象
            $account = new sys_account($data_in['get']['a_s_id']);
        }
        catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        if (strcasecmp($data_in['get']['a_s_id'], $GLOBALS['scap']['auth']['account_s_id']) == 0)
        {
            // 插入sys_account日志
            $account->get_instance_log()->update_log(G_TYPE_AL_DELETE, '删除帐户');
            
            scap_insert_sys_info('warn', '不能删除自身帐户!');
            $flag_save = false;
        }

        if (strcasecmp(g_get_cloginid_from_asid($data_in['get']['a_s_id']), 'admin') == 0)
        {
            // 插入sys_account日志
            $account->get_instance_log()->update_log(G_TYPE_AL_DELETE, '删除帐户');
            
            scap_insert_sys_info('warn', '不能删除系统保留帐户[admin]!');
            $flag_save = false;
        }

        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------

        //--------数据库事物处理[start]--------
        $data_db['content'] = $account->read();
        // 删除sys_account
        $account->delete();
        //--------数据库事物处理[end]--------

        $str_info = sprintf("删除【%s】系统帐号已成功。", $data_db['content']['a_c_display_name']);
        scap_insert_sys_info('tip', $str_info);

        $result = true;// 执行成功标志

        return $result; // 【注意】返回
    }

    //保存参数
    protected function event_config_save()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $flag_save = true;
        $result = false;// 事件返回结果

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息

        $data_save['content'] = array();// 主表单内容保存数据
        //--------变量定义及声明[end]--------

        //--------获取表单上传数据[start]--------
        $data_in['post'] = trimarray($_POST['content']);
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        $data_in['get']['module'] = $_GET['module'];
        $data_in['get']['key'] = $_GET['key'];
        //--------获取表单上传数据[end]--------

        //--------数据库事物处理[start]--------
        // 获取对应自定义参数的属性信息
        $data_def['config_item'] = scap_get_special_custom_value_property($data_in['get']['module'], $data_in['get']['key']);

        if ($data_def['config_item']['set_type'] == TYPE_CONFIG_TEXTAREA || $data_def['config_item']['set_type'] == TYPE_CONFIG_RICH_TEXT)
        {
            // An example use of stripslashes() is when the PHP directive magic_quotes_gpc is on (it's on by default), and you aren't inserting this data into a place (such as a database) that requires escaping. For example, if you're simply outputting data straight from an HTML form.
            $data_save['content']['c_c_value'] = stripslashes($data_in['post']['c_c_value']);
        }
        else
        {
            $data_save['content']['c_c_value'] = $data_in['post']['c_c_value'];
        }

        try
        {
            $config = new sys_config($data_in['get']['module'], $data_in['get']['key']);
            $config->update(array('c_c_value' => $data_save['content']['c_c_value']));

            // 插入日志
            $config->get_instance_log()->update_log(G_TYPE_AL_EDIT, '系统参数配置修改');
        }
        catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        //--------数据库事物处理[end]--------

        $str_info = sprintf("编辑参数【%s】已成功。", $data_def['config_item']['config_name']);
        scap_insert_sys_info('tip', $str_info);

        $result = true;// 执行成功标志

        return $result; // 【注意】返回
    }

    //保存模块属性设置
    protected function event_module_save()
    {
        //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $flag_save = true;
        $result = false;// 事件返回结果

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息

        $data_save['content'] = array();// 主表单内容保存数据
        //--------变量定义及声明[end]--------

        //--------获取表单上传数据[start]--------
        $data_in['button'] = trimarray($_POST['button']);
        $data_in['btn_register'] = trimarray($_POST['btn_register']);
        $data_in['btn_unregister'] = trimarray($_POST['btn_unregister']);
        $data_in['btn_update'] = trimarray($_POST['btn_update']);
        $data_in['btn_start'] = trimarray($_POST['btn_start']);
        $data_in['btn_stop'] = trimarray($_POST['btn_stop']);
        //--------获取表单上传数据[end]--------        

        // 保存显示顺序的按钮
        if ($data_in['button']['btn_order'])
        {
            //按“排序”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['input_order']);

            //对数组进行排序并保持索引关系
            asort($data_in['post']);

            $i = 1;
            foreach($data_in['post'] as $k => $v)
            {
                $data_save['content']['ml_s_id'] = $k;
                $data_save['content']['ml_c_order'] = $i ++;

                try
                {
                    $module = new sys_module($data_save['content']['ml_s_id']);
                    $module->update($data_save['content']);
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;// 【注意】返回
                }
            }
            
            scap_insert_sys_info('tip', sprintf("【%s】操作已成功。", '模块排序'));
        }

        // 注册按钮
        if ($data_in['btn_register'])
        {
            //按“注册”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['btn_register']);

            foreach($data_in['post'] as $k => $v)
            {
                $info = scap_get_module_local_info($k);
                
                $data_save['content']['ml_s_id'] = $k;
                // 如果是前台模块，初始状态为STAT_MODULE_STOP；后台模块状态为STAT_MODULE_NULL
                $data_save['content']['ml_s_status'] = ($info['property'] == PROP_MODULE_BACK) ? STAT_MODULE_NORMAL : STAT_MODULE_STOP;
                $data_save['content']['ml_c_order'] = 100;
                $data_save['content']['ml_c_version'] = $info['version'];

                // 创建模块相关数据库
                $tables = empty($info['tables']) ? array() : $info['tables'];

                include (SCAP_PATH_ROOT.$k.'/setup/db.tables.inc.php');
                foreach($tables as $k2 => $v2)
                {
                    try
                    {
                        // 处理mysql5.5之后对数据表引擎关键字type的废弃问题:替换TYPE为ENGINE
                        foreach($scap_db_table_options[$v2] as $k3 => $v3)
                        {
                            $scap_db_table_options[$v2][$k3] = str_ireplace('TYPE', 'ENGINE', $v3);
                        }
                        
                        if ($GLOBALS['scap']['db']->update_table_struct($v2, $scap_db_tables[$v2], $scap_db_table_options[$v2]))
                        {
                            scap_insert_sys_info('tip', sprintf("更新数据表【%s】成功。", $v2));
                        }
                        else
                        {
                            scap_insert_sys_info('error', sprintf("更新数据表【%s】失败。", $v2));
                        }
                    }
                    catch(Exception $e)
                    {
                        scap_insert_sys_info('error', $e->getMessage());
                        return $result;
                    }
                }
                unset($scap_db_tables);

                // 执行相关sql语句
                $sqls = empty($scap_db_sql) ? array() : $scap_db_sql;
                foreach($sqls as $k2 => $v2)
                {
                    try
                    {
                        $GLOBALS['scap']['db']->db_connect->Execute($v2); 
                        
                        scap_insert_sys_info('tip', sprintf("执行sql语句【%s】成功。", $k2));
                    }
                    catch(Exception $e)
                    {
                        scap_insert_sys_info('error', $e->getMessage());
                        return $result;
                    }
                }
                unset($scap_db_sql);

                try
                {
                    sys_module::create($data_save['content']);
                    
                    scap_insert_sys_info('tip', sprintf("【%s】模块已注册。", $k));
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;
                }                
            }
        }

        // 更新按钮
        if ($data_in['btn_update'])
        {
            //按“更新”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['btn_update']);

            foreach($data_in['post'] as $k => $v)
            {
                $info = scap_get_module_local_info($k);
                
                $data_save['content']['ml_s_id'] = $k;
                $data_save['content']['ml_c_version'] = $info['version'];

                // 创建模块相关数据库
                $tables = empty($info['tables']) ? array() :$info['tables'];
                include (SCAP_PATH_ROOT.$k.'/setup/db.tables.inc.php');
                foreach($tables as $k2 => $v2)
                {
                    try
                    {
                        // 处理mysql5.5之后对数据表引擎关键字type的废弃问题:替换TYPE为ENGINE
                        foreach($scap_db_table_options[$v2] as $k3 => $v3)
                        {
                            $scap_db_table_options[$v2][$k3] = str_ireplace('TYPE', 'ENGINE', $v3);
                        }
                        
                        if ($GLOBALS['scap']['db']->update_table_struct($v2, $scap_db_tables[$v2], $scap_db_table_options[$v2]))
                        {
                            scap_insert_sys_info('tip', sprintf("更新数据表【%s】成功。", $v2));
                        }
                        else
                        {
                            scap_insert_sys_info('error', sprintf("更新数据表【%s】失败。", $v2));
                        }
                    }
                    catch(Exception $e)
                    {
                        scap_insert_sys_info('error', $e->getMessage());
                        return $result;
                    }
                }
                unset($scap_db_tables);

                try
                {
                    $module = new sys_module($data_save['content']['ml_s_id']);
                    $module->update($data_save['content']);
                    
                    scap_insert_sys_info('tip', sprintf("【%s】模块已更新。", $k));
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;
                }    
            }
        }

        // 注销按钮
        if ($data_in['btn_unregister'])
        {
            //按“注销”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['btn_unregister']);
            
            foreach($data_in['post'] as $k => $v)
            {
                $data_save['content']['ml_s_id'] = $k;
                
                try
                {
                    $module = new sys_module($data_save['content']['ml_s_id']);
                    $module->delete();
                    
                    scap_insert_sys_info('tip', sprintf("【%s】模块已注销。", $k));
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;
                }                
            }
        }

        // 启用按钮
        if ($data_in['btn_start'])
        {
            //按“启用”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['btn_start']);

            foreach($data_in['post'] as $k => $v)
            {
                $data_save['content']['ml_s_id'] = $k;
                $data_save['content']['ml_s_status'] = STAT_MODULE_NORMAL;

                try
                {
                    $module = new sys_module($data_save['content']['ml_s_id']);
                    $module->update($data_save['content']);
                    
                    scap_insert_sys_info('tip', sprintf("【%s】模块已启用。", $k));
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;
                } 
            }
        }

        // 停用按钮
        if ($data_in['btn_stop'])
        {
            //按“停用”后获得界面相关数据数组
            $data_in['post'] = trimarray($_POST['btn_stop']);

            foreach($data_in['post'] as $k => $v)
            {
                $data_save['content']['ml_s_id'] = $k;
                $data_save['content']['ml_s_status'] = STAT_MODULE_STOP;

                try
                {
                    $module = new sys_module($data_save['content']['ml_s_id']);
                    $module->update($data_save['content']);
                    
                    scap_insert_sys_info('tip', sprintf("【%s】模块已停用。", $k));
                }
                catch(Exception $e)
                {
                    scap_insert_sys_info('error', $e->getMessage());
                    return $result;
                } 
            }
        }

        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
    
    protected function event_assign_acl()
    {
        // 变量声明
        $result = false;// 事件返回结果
        $data_in['post'] = $_POST;
        $data_in['button'] = trimarray($_POST['button']);
        
        // 提交表单内容检查
        $flag_save = true;
        
        if (!verify_content_legal($data_in['post']['acl_template_account_id'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('warn', '【角色权限模板】请选择。');
            $flag_save = false;
        }
        
        if (!count($data_in['post']['acl_account_id']))
        {
            scap_insert_sys_info('warn', '【账户】请选择。');
            $flag_save = false;
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        
        // 批量设置权限
        $i = 0;
        foreach($data_in['post']['acl_account_id'] as $k => $v)
        {
            sys_account::copy_account_acl($data_in['post']['acl_template_account_id'], $k);
            $i++;
        }
        
        $str_info = "成功设置".$i."个账户的权限。";
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        return $result; // 【注意】返回
    }
    
    /**
     * 导入sql执行事件
     * 
     * @return bool
     */
    protected function event_import_sql()
    {
        // 变量声明
        $result = false;
        $data_in['files'] = array();
        $content = '';
        
        // 获取post数据
        $data_in['files'] = $_POST['files'];
        
        if(count($data_in['files']) == 0)
        {
            return $result;
        }
        
        foreach($data_in['files'] as $k => $v)
        {
            $content .= file_get_contents($k);
        }
        
        $content = preg_replace('/\/\*(\s|.)*?\*\//', '', $content);
        $query_array = preg_split('/;[^(#!)]/', $content);//以分号(;)作为分割符，但不解析 ;#! 的行（不需要分割的语句请以次为标识，如trigger中的语句）
        
        $ok = true;
        scap_entity::db_begin_trans();// 事务开始
        
        foreach($query_array as $k => $v)
        {
            if(strlen(trim($v)) > 0)
            {
                $ok = scap_entity::excute_sql($v);
            }
        }
        
        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = true;
            scap_insert_sys_info('tip', '导入成功');
        }
        else
        {
            scap_insert_sys_info('error', '导入失败');
        }
        
        return $result;
    }
}

?>