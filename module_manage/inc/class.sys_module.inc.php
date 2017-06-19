<?php
/**
 * description:系统模块实体类
 * create time: 2010-3-17 03:33:36
 * @version $Id: class.sys_module.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author FuYing
 */

scap_load_module_define('module_manage', 'entity_id');// 加载manage entity定义文件

/**
 * 系统模块实体类
 */
class sys_module extends scap_g_entity
{
    /**
     * 设置继承类的实体相关信息
     */
    protected function set_current_entity_info()
    {
        $this->current_entity_id = ENTITY_ID_MANAGE_SYSTEM_MODULE; //实体ID
        $this->current_entity_name = '系统模块';
    } 

    /**
     * 检查当前的对象($this->current_object_id)是否存在
     *
     * @return bool 如果存在返回true，否则返回false
     */
    protected function check_current_object_exist()
    {
        $result = false;

        if ($this->get_row_count('scap_module_list', "ml_s_id = '{$this->current_object_id}'") > 0)
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
        //--------入库数据赋值[end]--------

        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;

        // 更新scap_module_list表
        if($ok)
        {
            $ok = scap_entity::edit_row("scap_module_list", $data_save['content'], 'insert', '', 'module_basic');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务

        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = new sys_module($data_save['content']['ml_s_id']);
        }
        else
        {
            throw new Exception("创建系统模块失败。");
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
SELECT * FROM scap_module_list
WHERE (ml_s_id = '{$this->current_object_id}')
SQL;
        $data_db['content'] = $this->query($data_db['query'], false);

        return current($data_db['content']);
    }

    /**
     * 更新当前实体对象数据
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

        // 更新scap_module_list表
        if($ok)
        {
            $ok = $this->edit_row("scap_module_list", $data_save['content'], 'update', "ml_s_id = '{$this->current_object_id}'", 'module_basic');
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

        // 更新scap_module_list表
        if($ok)
        {
            $ok = $this->remove_rows("scap_module_list", "ml_s_id = '{$this->current_object_id}'");
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
}
    
?>