<?php
/**
 * 系统参数配置实体类
 * create time: 2010-3-11 03:57:28
 * @version $Id: class.sys_config.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author FuYing
 */

scap_load_module_define('module_manage', 'entity_id');// 加载manage entity定义文件

/**
 * 系统参数配置实体类
 */
class sys_config extends scap_g_entity
{
    /**
     * 重载构造函数
     * 
     * @param string $module_id 模块id
     * @param string $key key
     * 
     * @return void
     */
    function __construct($module_id, $key)    
    {
        //将$module_id.$key作为联合主键current_object_id
        parent::__construct($module_id.$key);
    }
    
    /**
     * 设置继承类的实体相关信息
     */
    protected function set_current_entity_info()
    {
        $this->current_entity_id = ENTITY_ID_MANAGE_SYSTEM_CONFIG;//实体ID
        $this->current_entity_name = '系统参数配置';
    }
    
    /**
     * 检查当前的对象($this->current_object_id)是否存在
     * 
     * @return bool 如果存在返回true，否则返回false
     */
    protected function check_current_object_exist()
    {
        $result = false;

        if ($this->get_row_count('scap_config', "concat(c_s_module, c_s_key) = '{$this->current_object_id}'") > 0)
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

        // 更新scap_config表
        if($ok)
        {
            $ok = scap_entity::edit_row("scap_config", $data_save['content'], 'insert', '', 'module_basic');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务

        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = new sys_config($data_save['content']['c_s_module'], $data_save['content']['c_s_key']);
        }
        else
        {
            throw new Exception("创建系统参数配置失败。");
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
SELECT * FROM scap_config
WHERE (concat(c_s_module, c_s_key) = '{$this->current_object_id}')
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

        // 更新scap_config表
        if($ok)
        {
            $ok = $this->edit_row("scap_config", $data_save['content'], 'update', "concat(c_s_module, c_s_key) = '{$this->current_object_id}'", 'module_basic');
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

        // 更新scap_config表
        if($ok)
        {
            $ok = $this->remove_rows("scap_config", "concat(c_s_module, c_s_key) = '{$this->current_object_id}'");
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