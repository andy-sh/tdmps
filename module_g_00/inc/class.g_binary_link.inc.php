<?php
/**
 * 通用二进制文件实现类
 * create time: 2010-9-15 11:13:50
 * @version $Id: class.g_binary_link.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用二进制文件类
 * 
 * 为实体对象提供二进制文件的添加、编辑、输出、关联服务
 * 
 */
class g_binary_link extends scap_entity
{
    /**
     * 文件表名称
     * @var string
     */
    private $link_table_name = 'g_object_binary_link';
    
    /**
     * 当前对象id
     * @var string uid
     */
    private $current_object_id = NULL;
    
    /**
     * 当前对象的实体id
     * @var string uid
     */
    private $current_entity_id = NULL;
    
    /**
     * 构造函数
     * @param string $object_id 对象id
     * @param string $entity_id 实体id
     */
    public function __construct($object_id, $entity_id)
    {
        parent::__construct();
        $this->set_current_object_id($object_id);
        $this->set_current_entity_id($entity_id);
        scap_load_module_class('module_g_00', 'binary_data');
    }
    
    /**
     * 设置当前object_id
     * @param string $object_id
     */
    public function set_current_object_id($object_id)
    {
        $this->current_object_id = $object_id;
    }
    
    /**
     * 设置当前entity_id
     * @param string $entity_id
     */
    public function set_current_entity_id($entity_id)
    {
        $this->current_entity_id = $entity_id;
    }
    
    /**
     * 获得当前object_id,如果object_id不存在返回NULL
     * 
     * @author Liqt
     * 
     * @return string
     */
    public function get_current_object_id()
    {
        return $this->current_object_id;
    }
    
    /**
     * 根据sn检查指定文件是否存在
     * 
     * @param int $sn 链接序号
     * @return bool
     */
    public function check_link_exist_by_sn($sn)
    {
        $result = false;
        
        $result = scap_entity::get_row_count($this->link_table_name, "obl_object_id = '{$this->current_object_id}' AND obl_sn = {$sn}");
        
        if ($result === false)
        {
            throw new Exception("检查文件失败。");
        }
        elseif ($result > 0)
        {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * 根据link_name检查指定链接是否存在
     * 
     * @param string $link_name 关联名称
     * @return bool
     */
    public function check_link_exist_by_name($link_name)
    {
        $result = false;
        
        $result = scap_entity::get_row_count($this->link_table_name, "obl_object_id = '{$this->current_object_id}' AND obl_name = '{$link_name}'");
        
        if ($result === false)
        {
            throw new Exception("检查文件失败。");
        }
        elseif ($result > 0)
        {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * 从关联名称获取对应的最大的sn
     * 
     * @param string $link_name 关联名称
     * 
     * @return int SN值
     */
    private function get_max_sn_from_name($link_name)
    {
        $max_sn = scap_entity::get_max_value($this->link_table_name, 'obl_sn', "obl_object_id = '{$this->current_object_id}' AND obl_name = '{$link_name}'");
        
        return $max_sn;
    }
    
    /**
     * 获取文件关联表所有sn值
     * 
     * @param string $where 附加查询条件（以AND开头）
     * 
     * @return array array(obl_sn => array('obl_sn' => obl_sn), ....)
     */
    public function get_sn_list($where = '')
    {
        //--------变量定义及声明[start]--------
        $data_db    = array();  // 数据库相关数据
        $list = array();
        //--------变量定义及声明[end]--------
        
        $data_db['query']['sql'] = <<<SQL
SELECT obl_sn FROM {$this->link_table_name}  
WHERE (obl_object_id = '{$this->current_object_id}' {$where})
SQL;
    
        $data_db['query']['id'] = 'obl_sn';
        $data_db['query']['order'] = 'obl_sn';
        $data_db['query']['sort'] = 'ASC';
        
        $list = scap_entity::query($data_db['query'], false);
        
        return $list;
    }
    
    /**
     * 根据sn读取关联信息
     * 
     * @param int $sn 序号
     * 
     * @return array
     */
    public function read_link_by_sn($sn)
    {
        $data_db = array(); // 数据库相关数据
        $data_db['content'] = array();
        
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM {$this->link_table_name}
WHERE (obl_object_id = '{$this->current_object_id}' AND obl_sn = {$sn})
SQL;
        $data_db['content'] = scap_entity::query($data_db['query'], false);
        
        return current($data_db['content']);
    }
    
    /**
     * 根据关联名称读取关联信息
     * 
     * @param string $link_name 关联名称
     * 
     * @return array
     */
    public function read_link_by_name($link_name)
    {
        $sn = $this->get_max_sn_from_name($link_name);
        
        return $this->read_link_by_sn($sn);
    }
    
    /**
     * 根据sn获取关联文件信息
     * @param int $sn 序号
     * 
     * @return array 文件信息
     */
    public function read_binary_index_by_sn($sn)
    {
        $result = array();
        
        // 读取关联的文件id
        $temp = $this->read_link_by_sn($sn);
        if (binary_data::check_bd_exist($temp['bd_id']))
        {
            $binary = new binary_data($temp['bd_id']);
            $result = $binary->get_index();
        }
        
        return $result;
    }
    
    /**
     * 根据关联名称读取关联文件信息
     * @param $link_name 关联名称
     * 
     * @return array 文件信息
     */
    public function read_binary_index_by_name($link_name)
    {
        $sn = $this->get_max_sn_from_name($link_name);
        
        return $this->read_binary_index_by_sn($sn);
    }
    
    /**
     * 根据sn获取关联文件内容
     * @param int $sn 序号
     * 
     * @return 二进制数据 文件内容
     */
    public function read_binary_content_by_sn($sn)
    {
        $result = array();
        
        // 读取关联的文件id
        $temp = $this->read_link_by_sn($sn);
        if (binary_data::check_bd_exist($temp['bd_id']))
        {
            $binary = new binary_data($temp['bd_id']);
            $result = $binary->get_binary();
        }
        
        return $result;
    }
    
    /**
     * 根据关联名称读取关联文件内容
     * @param $link_name 关联名称
     * 
     * @return 二进制数据 文件内容
     */
    public function read_binary_content_by_name($link_name)
    {
        $sn = $this->get_max_sn_from_name($link_name);
        
        return $this->read_binary_content_by_sn($sn);
    }
    
    /**
     * 更新(添加、编辑)关联文件信息及文件内容
     * @param $_FILES $upload_info 文件上传系统变量，如果为空，则不创建或不变更文件
     * @param string $link_name 关联名称
     * @param string $link_comment 关联备注
     * @param int $sn 关联序号
     * 
     * @return bool
     */
    public function update($upload_info, $link_name = '', $link_comment = '', $sn = NULL)
    {
        $data_in = array();// 输入数据
        $data_save = array();// 入库数据
        $result = false;// 返回结果
        
        $binary = null;// 文件对象
        
        $data_save['where'] = '';
        
        // 入库数据赋值
        $data_save['content']['obl_object_id'] = $this->current_object_id;
        $data_save['content']['obl_entity_id'] = $this->current_entity_id;
        $data_save['content']['bd_id'] = '';
        $data_save['content']['obl_name'] = $link_name;
        $data_save['content']['obl_comment'] = $link_comment;
        
        if (is_null($sn))
        {
            $data_save['type'] = 'INSERT';
            $max_sn = scap_entity::get_max_value($this->link_table_name, 'obl_sn', "obl_object_id = '{$this->current_object_id}'");
            
            if ($max_sn === false)
            {
                throw new Exception("获取max sn出错。");
            }
            
            $data_save['content']['obl_sn'] = $max_sn + 1;
        }
        else
        {
            $data_save['content']['obl_sn'] = $sn;
            
            if ($this->check_link_exist_by_sn($sn))
            {
                $data_save['type'] = 'UPDATE';
                $data_save['where'] = "obl_object_id = '{$this->current_object_id}' AND obl_sn = {$sn}";
            }
            else
            {
                $data_save['type'] = 'INSERT';
            }
            
            // 读取关联的文件id
            $temp = $this->read_link_by_sn($sn);
            if (binary_data::check_bd_exist($temp['bd_id']))
            {
                // 如果存在有效的bd_id，则保留之
                $data_save['content']['bd_id'] = $temp['bd_id'];
            }
            
        }
        
        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;
        
        if($ok)
        {
            if (!empty($upload_info['size']))// 如果文件不为空
            {
                if (empty($data_save['content']['bd_id']))
                {
                    $binary = binary_data::upload($upload_info, array('bd_entity_id' => $this->current_entity_id), G_TYPE_BINARY_STORAGE_FS);
                    $data_save['content']['bd_id'] = $binary->get_current_bd_id();
                }
                else
                {
                    $binary = new binary_data($data_save['content']['bd_id']);
                    $binary->update_index($upload_info);
                    $binary->update_binary($upload_info);
                }
            }
        }
        
        if($ok)
        {
            $ok = scap_entity::edit_row($this->link_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)
        {
            $result = true;// 执行成功标志
        }
        else
        {
            throw new Exception("更新文件关联失败。");
        }
        //--------数据库事物处理[end]--------
        
        return $result;
    }
    
    /**
     * 根据关联名称更新(添加、编辑)关联文件信息及文件内容
     * 如果存在多个关联名称一样的关联，则仅变更sn最大的那个
     * 
     * @param $_FILES $upload_info 文件上传系统变量，如果为空，则不创建或不变更文件
     * @param string $link_name 关联名称
     * @param string $link_comment 关联备注
     * 
     * @return bool
     */
    public function update_by_name($upload_info, $link_name, $link_comment = '')
    {
        $sn = $this->get_max_sn_from_name($link_name);
        
        if (empty($sn))
        {
            $sn = NULL;
        }
        
        return $this->update($upload_info, $link_name, $link_comment, $sn);
    }
    
    /**
     * 删除文件关联及相关文件
     * 
     * @param unknown_type $where
     */
    private function delete($where = '')
    {
        //--------变量定义及声明[start]--------
        $result = false;// 返回结果
        $data_db    = array();  // 数据库相关数据
        $list = array();
        //--------变量定义及声明[end]--------
        $data_db['where'] = "obl_object_id = '{$this->current_object_id}' {$where}";
        
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM {$this->link_table_name} 
WHERE ({$data_db['where']})
SQL;
    
        $data_db['query']['id'] = 'obl_sn';
        $data_db['query']['order'] = 'obl_sn';
        $data_db['query']['sort'] = 'ASC';
        
        $list = scap_entity::query($data_db['query'], false);
        
        if (scap_entity::remove_rows($this->link_table_name, $data_db['where']) === false)
        {
            $str_info = sprintf('删除对象与二进制数据关联失败。错误信息：%s', scap_db_error_msg());
            throw new Exception($str_info);
        }
        
        foreach($list as $k => $v)
        {
            $binary = new binary_data($v['bd_id']);
            $binary->remove();
        }
        
        return true;
    }
    
    /**
     * 删除所有关联信息及关联的文件
     * 
     * @return bool
     */
    public function delete_all()
    {
        return $this->delete();
    }
    
    /**
     * 根据sn删除关联信息及关联文件
     * 
     * @param int $sn 序号
     * 
     * @return bool
     */
    public function delete_by_sn($sn)
    {
        return $this->delete("AND obl_sn={$sn}");
    }
    
    /**
     * 根据关联名称删除关联信息及关联文件
     * 
     * @param string $link_name 关联名称
     * 
     * @return bool
     */
    public function delete_by_name($link_name)
    {
        return $this->delete("AND obl_name='{$link_name}'");
    }
}
?>