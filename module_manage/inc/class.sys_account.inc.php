<?php
/**
 * 系统账号实体类
 * create time: 2010-3-11 02:19:35
 * @version $Id: class.sys_account.inc.php 50 2012-11-07 09:53:40Z liqt $
 * @author FuYing
 */

scap_load_module_define('module_manage', 'entity_id');// 加载manage entity定义文件

/**
 * 系统账号实体类
 */
class sys_account extends scap_g_entity
{
    /**
     * 设置继承类的实体相关信息
     */
    protected function set_current_entity_info()
    {
        $this->current_entity_id = ENTITY_ID_MANAGE_SYSTEM_ACCOUNT; //实体ID
        $this->current_entity_name = '系统账号';
    }

    /**
     * 检查当前的对象($this->current_object_id)是否存在
     *
     * @return bool 如果存在返回true，否则返回false
     */
    protected function check_current_object_exist()
    {
        $result = false;

        if ($this->get_row_count('scap_accounts', "a_s_id = '{$this->current_object_id}'") > 0)
        {
            $result = true;
        }

        return $result;
    }

    /**
     * 创建一个实体对象
     *
     * @param array $content 对象内容数据
     *
     * @return class 实体对象实例
     */
    public static function create($content)
    {
        //--------变量定义及声明[start]--------
        $data_in = array();// 输入数据
        $data_save = array();// 入库数据
        $result = false;// 返回结果
        //--------变量定义及声明[end]--------

        //--------入库数据赋值[start]--------
        $data_save['content'] = $content;
        $data_save['content']['a_s_id'] = scap_get_guid();// 内部生成guid
        $data_save['content']['a_s_password'] = scap_encrypt_password($data_save['content']['a_new_password']);
        //--------入库数据赋值[end]--------

        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;

        // 更新scap_accounts表
        if($ok)
        {
            $ok = scap_entity::edit_row("scap_accounts", $data_save['content'], 'insert', '', 'module_basic');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务

        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = new sys_account($data_save['content']['a_s_id']);
        }
        else
        {
            throw new Exception("创建系统账号失败。");
        }
        //--------数据库事物处理[end]--------

        return $result;
    }

    /**
     * 读取当前实体对象数据
     *
     * @return array 对象内容数据
     */
    public function read()
    {
        $data_db = array(); // 数据库相关数据
        $data_db['content'] = array();

        $data_db['query']['sql'] = <<<SQL
SELECT * FROM scap_accounts
WHERE (a_s_id = '{$this->current_object_id}')
SQL;
        $data_db['content'] = $this->query($data_db['query'], false);

        return current($data_db['content']);
    }

    /**
     * 更新当前实体对象数据一部分(非全部)
     * 
     * 编辑时，对帐户数据内容非全部更改
     * 例：只更改用户基本信息不更改密码 或 只更改用户密码不更改用户基本信息
     *
     * @param array $content 对象内容数据
     * @return bool
     */
    public function update($content)
    {
        //--------变量定义及声明[start]--------
        $data_in = array();// 输入数据
        $data_save = array();// 入库数据
        $result = false;// 返回结果
        //--------变量定义及声明[end]--------

        //--------入库数据赋值[start]--------
        $data_save['content'] = $content;
        //--------入库数据赋值[end]--------
        //--------数据库事物处理[start]--------
        $this->db_begin_trans();// 事务开始
        $ok = true;

        // 更新scap_accounts表
        if($ok)
        {
            $ok = $this->edit_row("scap_accounts", $data_save['content'], 'update', "a_s_id = '{$this->current_object_id}'", 'module_basic');
        }

        $data_flag['db_commit'] = $this->db_commit_trans($ok);// 提交事务

        if ($data_flag['db_commit'] == 1)
        {
            $result = true;// 执行成功标志
        }
        else
        {
            throw new Exception("更新{$this->current_entity_name}失败。");
        }
        //--------数据库事物处理[end]--------

        return $result;
    }     

    /**
     * 删除当前实体对象数据
     *
     * @return bool
     */
    public function delete()
    {
        //--------数据库事物处理[start]--------
        $this->db_begin_trans();// 事务开始
        $ok = true;

        // 更新scap_accounts表
        if($ok)
        {
            $ok = $this->remove_rows("scap_accounts", "a_s_id = '{$this->current_object_id}'");
        }

        $data_flag['db_commit'] = $this->db_commit_trans($ok);// 提交事务

        if ($data_flag['db_commit'] == 1)
        {
            $result = true;// 执行成功标志
        }
        else
        {
            throw new Exception("删除{$this->current_entity_name}失败。");
        }
        //--------数据库事物处理[end]--------

        return $result;
    }
    
    /**
     * 账号间复制权限
     * 
     * @param $source_a_s_id 源账户系统id
     * @param $target_a_s_id 目标账户系统id
     */
    static public function copy_account_acl($source_a_s_id, $target_a_s_id)
    {
        // 变量声明
        $data_db = array();
        $data_save = array();
        
        // 读取模板账户所有权限
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM scap_acl
WHERE (acl_s_account_id = '{$source_a_s_id}')
SQL;
        $data_db['content'] = scap_g_entity::query($data_db['query'], false);
        
        // 替换账户id
        foreach($data_db['content'] as $k => $v)
        {
            $data_save['content'][$k]['acl_s_account_id'] = $target_a_s_id;
            $data_save['content'][$k]['acl_s_module'] = $v['acl_s_module'];
            $data_save['content'][$k]['acl_c_acl_code'] = $v['acl_c_acl_code'];
        }
        
        // 更新scap_acl表
        
        $ok = true;
        // 清空原有权限
        if($ok)
        {
            $ok = scap_g_entity::remove_rows("scap_acl", "acl_s_account_id = '{$target_a_s_id}'");
        }
        
        // 添加权限
        foreach($data_save['content'] as $v)
        {
            if($ok)
            {
                $ok = scap_g_entity::edit_row("scap_acl", $v, 'insert', '', 'module_basic');
            }
        }

        $data_flag['db_commit'] = scap_g_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)
        {
            $result = true;// 执行成功标志
        }
        else
        {
            throw new Exception("添加权限位失败。");
        }
        
        return $result;
    }
}

?>