<?php
/**
 * page类实现文件
 * create time: 2011-12-28 下午05:18:25
 * @version $Id: class.page.inc.php 104 2012-03-27 04:08:49Z liqt $
 * @author LiQintao
 */
scap_load_module_define('module_touchview_basic', 'entity_id');
scap_load_module_define('module_touchview_page', 'page_type');

/**
 * page(页面)实体类
 */
class page extends scap_g_entity
{
    /**
     * 设置继承类的实体相关信息
     */
    protected function set_current_entity_info()
    {
        $this->current_entity_id = ENTITY_ID_TOUCHVIEW_PAGE;
        $this->current_entity_name = '页面';
    }
    
    /**
     * 检查当前的对象($this->current_object_id)是否存在
     * 
     * @return bool 如果存在返回true，否则返回false
     */
    protected function check_current_object_exist()
    {
        $result = false;
        
        if ($this->get_row_count('touchview_page', "p_id = '{$this->current_object_id}'") > 0)
        {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * 创建一个实体对象
     * 
     * @param array $content 对象内容数据，需要指定$content['refer_id'],$content['position_type']
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
        unset($data_save['content']['p_sn']);
        if (empty($data_save['content']['p_id']))
        {
            $data_save['content']['p_id'] = scap_get_guid();// id内部生成
        }
        $data_save['content']['p_parent_id'] = empty($content['p_parent_id']) ? $content['b_id'] : $content['p_parent_id'];// 父章节id，如果为1级节点，则为b_id
        $data_save['content']['refer_id'] = empty($content['refer_id']) ? $content['b_id'] : $content['refer_id'];
        $data_save['content']['position_type'] = empty($content['position_type']) ? page::TYPE_POSITION_INSERT_PAGE_LAST : $content['position_type'];
        $data_save['content']['p_sort_sn'] = 0;
        
        // 如果页面内容为空，则添加p
        if (empty($data_save['content']['p_content']))
        {
            $data_save['content']['p_content'] = "<p></p>";
        }
        //--------入库数据赋值[end]--------
        
        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;
        
        // 更新touchview_page表
        if($ok)
        {
            $ok = scap_entity::edit_row("touchview_page", $data_save['content'], 'insert', '', 'module_touchview_basic');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = new page($data_save['content']['p_id']);
            // 更新顺序
            page::update_page_sort($data_save['content']['b_id'], $data_save['content']['refer_id'], $data_save['content']['position_type'], $data_save['content']['p_id']);
        }
        else
        {
            throw new Exception("创建页面失败。");
        }
        
        //--------数据库事物处理[end]--------
        
        return $result;
    }
    
    /**
     * 读取当前对象主体内容
     * 
     * @return array 对象内容数据
     */
    public function read()
    {
        $data_db = array(); // 数据库相关数据
        $data_db['content'] = array();
        
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM touchview_page
WHERE (p_id = '{$this->current_object_id}')
SQL;
        $data_db['content'] = scap_entity::query($data_db['query'], false);
        
        return current($data_db['content']);
    }
    
    /**
     * 更新当前对象主体内容
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
        
        $data_in['b_id'] = page::get_bookid_from_id($this->current_object_id);
        //--------变量定义及声明[end]--------
        
        //--------入库数据赋值[start]--------
        $data_save['content'] = $content;
        //--------入库数据赋值[end]--------
        
        //--------数据库事物处理[start]--------
        $this->db_begin_trans();// 事务开始
        $ok = true;
        
        // 更新touchview_page表
        if($ok)
        {
            $ok = $this->edit_row("touchview_page", $data_save['content'], 'update', "p_id = '{$this->current_object_id}'", 'module_touchview_basic');
        }
        
        if($ok)// 重新排序
        {
            if (isset($content['refer_id']) && isset($content['position_type']))// 需改自身变顺序
            {
                $ok = page::update_page_sort($data_in['b_id'], $content['refer_id'], $content['position_type'], $this->current_object_id);
            }
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
     * 删除当前对象数据
     * 
     * @return bool
     */
    public function delete()
    {
        $data_db = $this->read();
        //--------数据库事物处理[start]--------
        $this->db_begin_trans();// 事务开始
        $ok = true;
        
        // 更新touchview_page表
        if($ok)
        {
            $ok = $this->remove_rows("touchview_page", "p_id = '{$this->current_object_id}'");
        }
        
        // 重新排序
        if($ok)
        {
            $ok = page::update_book_sort($data_db['b_id']);
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
     * 获取父路径信息
     * 
     * @param bool $flag_include_self 是否包含自身，默认为false
     * 
     * @return array 按顺序排列的父路径信息数组
     */
    public function get_parent_path_info($flag_include_self = false)
    {
        $data_out = array();
        
        $temp_data = $this->read();
        $b_id = $temp_data['b_id'];
        $temp_parent_id = $temp_data['p_parent_id'];
        
        if ($flag_include_self)// 包含自身
        {
            $data_out[] = array('id' => $temp_data['p_id'], 'name' => $temp_data['p_name']);
        }
        
        // 循环查询父路径
        while(!empty($temp_parent_id) && strcasecmp($temp_parent_id, $b_id) != 0)
        {
            $temp_page = new page($temp_parent_id);
            $temp_data = $temp_page->read();
            $temp_parent_id = $temp_data['p_parent_id'];
            
            $data_out[] = array('id' => $temp_data['p_id'], 'name' => $temp_data['p_name']);
        }
        
        $data_out = array_reverse($data_out);// 倒序
        
        return $data_out;
    }
    
    /**
     * 更新指定page的顺序
     * 
     * @param string $b_id 当前page的book id
     * @param string $refer_id 参照id
     * @param int $position 插入页面位置(都相对与refer id而言)，见TYPE_POSITION_INSERT_PAGE_XX 定义
     * @param string $current_id 当前移动的page id，如果为空则标识自动重新排列
     * 
     * @return bool
     */
    const TYPE_POSITION_INSERT_PAGE_FIRST = 1;// refter id下一级的顶部位置
    const TYPE_POSITION_INSERT_PAGE_LAST = 2;// refter id下一级的尾部位置
    const TYPE_POSITION_INSERT_PAGE_AFTER = 3;// refter id平级之后位置
    const TYPE_POSITION_INSERT_PAGE_BEFORE = 4;// refter id平级之前位置
    public static function update_page_sort($b_id, $refer_id, $position_type, $current_id)
    {
        $result = false;
        $new_sort_sn = 1;
        $child_list = array();
        $count_child = 0;
        
        switch($position_type)
        {
            case page::TYPE_POSITION_INSERT_PAGE_FIRST:// refter id下一级的顶部位置
                // 1.获取refter id序号
                $min_sn = page::get_sortsn_from_id($refer_id);
                
                // 2.新的序号等于最小序号+1
                $new_sort_sn = $min_sn + 1;
                
                // 3.查看current id的子页面有多少个
                page::get_all_child_pageid_list($current_id, $child_list);
                $count_child = count($child_list);
                
                // 4.将book内所有大于min sn的页面序号都加1
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = p_sort_sn+1+{$count_child}
WHERE b_id = '{$b_id}' AND p_sort_sn > {$min_sn}
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 5.将current id的sn设为min sn
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}
WHERE p_id = '{$current_id}'
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 6.将current id的所有子页面顺序重新计算
                $i = 1;
                foreach ($child_list as $v)
                {
                    $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}+{$i}
WHERE p_id = '{$v}'
SQL;
                    $result = scap_entity::excute_sql($sql);
                    if (!$result)
                    {
                        throw new Exception("更新页面顺序失败。");
                    }
                    $i ++;
                }
                break;
            case page::TYPE_POSITION_INSERT_PAGE_BEFORE:// refter id平级之前位置
                // 1.获取refter id序号
                $min_sn = page::get_sortsn_from_id($refer_id);
                
                // 2.新的序号等于最小序号+1
                $new_sort_sn = $min_sn;
                
                // 3.查看current id的子页面有多少个
                page::get_all_child_pageid_list($current_id, $child_list);
                $count_child = count($child_list);
                
                // 4.将book内所有大于min sn的页面序号都加1
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = p_sort_sn+1+{$count_child}
WHERE b_id = '{$b_id}' AND p_sort_sn >= {$min_sn}
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 5.将current id的sn设为min sn
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}
WHERE p_id = '{$current_id}'
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 6.将current id的所有子页面顺序重新计算
                $i = 1;
                foreach ($child_list as $v)
                {
                    $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}+{$i}
WHERE p_id = '{$v}'
SQL;
                    $result = scap_entity::excute_sql($sql);
                    if (!$result)
                    {
                        throw new Exception("更新页面顺序失败。");
                    }
                    $i ++;
                }
                break;
            case page::TYPE_POSITION_INSERT_PAGE_LAST:// refter id下一级的尾部位置
            case page::TYPE_POSITION_INSERT_PAGE_AFTER:// refter id平级之前位置
                // 1.获取该级下最大的序号
                $max_sn = page::get_child_max_sn($refer_id);
                
                // 2.新的序号等于最大序号+1
                $new_sort_sn = $max_sn + 1;
                
                // 3.查看current id的子页面有多少个
                page::get_all_child_pageid_list($current_id, $child_list);
                $count_child = count($child_list);
                
                // 4.将book内所有大于max sn的页面序号都加1
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = p_sort_sn+1+{$count_child}
WHERE b_id = '{$b_id}' AND p_sort_sn > {$max_sn}
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 5.将current id的sn设为$new_sort_sn
                $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}
WHERE p_id = '{$current_id}'
SQL;
                $result = scap_entity::excute_sql($sql);
                if (!$result)
                {
                    throw new Exception("更新页面顺序失败。");
                }
                
                // 6.将current id的所有子页面顺序重新计算
                $i = 1;
                foreach ($child_list as $v)
                {
                    $sql = <<<SQL
UPDATE touchview_page SET p_sort_sn = {$new_sort_sn}+{$i}
WHERE p_id = '{$v}'
SQL;
                    $result = scap_entity::excute_sql($sql);
                    if (!$result)    
                    {
                        throw new Exception("更新页面顺序失败。");
                    }
                    $i ++;
                }
                break;
            default:
                throw new Exception("更新页面顺序失败:无效的位置参数。");
        }
        
        $result = page::update_book_sort($b_id);
        
        return $result;
    }
    
    /**
     * 更新书籍所有页面的序号
     * 
     * @param string $b_id 当前page的book id
     * 
     * @return bool
     */
    public static function update_book_sort($b_id)
    {
        $result = false;
        
        // 全部重新计算排序
        $sql = <<<SQL
CALL p_page_update_sort('{$b_id}')
SQL;
        
        $result = scap_entity::excute_sql($sql);
        if (!$result)
        {
            throw new Exception("更新书籍页面顺序失败。");
        }
        
        return $result;
    }
    
	/**
	 * 根据page id获取所有当前子page列表
	 * 
	 * @param string $id page id/book id
	 * 
	 * @return array 返回每个子page信息数组
	 */
	public static function get_child_page_list($id)
	{
	    $data_db = array();    // 数据库相关数据
        $data_db['content'] = array();
        
        $data_db['query']['order'] = 'p_sort_sn';
        $data_db['query']['sort'] = 'ASC';
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM touchview_page 
WHERE (p_parent_id = '{$id}')
SQL;
        $data_db['content'] = scap_entity::query($data_db['query'], false);
        
        return $data_db['content'];
	}
    
	/**
	 * 根据book id获取所有page列表
	 * 
	 * @param string $id book id
	 * 
	 * @return array 返回每个子page信息数组
	 */
	public static function get_book_page_list($b_id)
	{
	    $data_db = array();    // 数据库相关数据
        $data_db['content'] = array();
        
        $data_db['query']['order'] = 'p_sort_sn';
        $data_db['query']['sort'] = 'ASC';
        $data_db['query']['sql'] = <<<SQL
SELECT * FROM touchview_page 
WHERE (b_id = '{$b_id}')
SQL;
        $data_db['content'] = scap_entity::query($data_db['query'], false);
        
        return $data_db['content'];
	}
	
	/**
     * 获取指定类别id下的所有子类别id列表
     * 
     * @param string $id page id/book id
     * @param array &$child_list 子类别id表
     * 
     * @return NULL
     */
    static public function get_all_child_pageid_list($id, &$child_list)
    {
        //--------变量定义及声明[start]--------
        $data_db    = array();  // 数据库相关数据
        $list = array();
        //--------变量定义及声明[end]--------
         
        $data_db['query']['sql'] = <<<SQL
SELECT p_id, p_sort_sn FROM touchview_page
WHERE (p_parent_id = '{$id}')
SQL;
     
        $data_db['query']['id'] = 'p_id';
        $data_db['query']['order'] = 'p_sort_sn';
        $data_db['query']['sort'] = 'ASC';
         
        $list = scap_entity::query($data_db['query'], false);
        foreach($list as $k => $v)
        {
            $child_list[] = $k;
            page::get_all_child_pageid_list($k, $child_list);
        }
    }
	
    /**
     * 检查子页面是否已存在
     * 
     * @param string $id page id/book id
     * 
     * @return bool
     */
    public static function check_child_page_exist($id)
    {
        $result = false;
        
        if (scap_entity::get_row_count('touchview_page', "p_parent_id = '{$id}'") > 0)
        {
            $result = true;
        }
        
        return $result;
    }
	
	/**
	 * 获取子页面下最大的序号（含其下多层子页面）
	 * 递归算法
	 * 
	 * @param string $id 父页面id
	 * 
	 * @return int 最大的排序序号(如果没有子页面，则最大排序序号与当前页面序号相同)
	 */
    public static function get_child_max_sn($id)
    {
        $result = NULL;
        
        $max_sn = scap_entity::get_max_value('touchview_page', 'p_sort_sn', "p_parent_id = '{$id}' AND p_sort_sn > 0");

        if (is_null($max_sn))
        {
            // 没有子页面则将当前id对应序号传回
            $result = page::get_sortsn_from_id($id);
        }
        else
        {
            // 有子页面则递归获取max sn
            $result = page::get_child_max_sn(page::get_id_from_sortsn(page::get_bookid_from_id($id), $max_sn));
        }
        
        return $result;
    }

    /**
     * 通过id获取对应parent id
     * 
     * @param string $id page id
     * 
     * @return string p_parent_id，如不存在则为NULL
     */
    public static function get_parentid_from_id($id)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT p_parent_id FROM touchview_page WHERE (p_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_parent_id'];
    	}
    
    	return $rtn;
    }

    /**
     * 通过id获取对应sort sn
     * 
     * @param string $id page id
     * 
     * @return int p_sort_sn，如不存在则为0
     */
    public static function get_sortsn_from_id($id)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT p_sort_sn FROM touchview_page WHERE (p_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_sort_sn'];
    	}
    	
    	$rtn = is_null($rtn) ? 0 : $rtn;
    	
    	return $rtn;
    }

    /**
     * 通过sort sn获取对应id
     * 
     * @param string $b_id 当前page的book id
     * @param int $sort_sn sort sn
     * 
     * @return string p_id，如不存在则为NULL
     */
    public static function get_id_from_sortsn($b_id, $sort_sn)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT p_id FROM touchview_page WHERE (b_id = '{$b_id}' AND p_sort_sn = {$sort_sn})";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_id'];
    	}
    
    	return $rtn;
    }

    /**
     * 通过id获取对应book id
     * 如果传入的id是book id也可以正确获取
     * 
     * @param string $id page id
     * 
     * @return string b_id，如不存在则为NULL
     */
    public static function get_bookid_from_id($id)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT b_id FROM touchview_page WHERE (p_id = '{$id}' OR b_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['b_id'];
    	}
    
    	return $rtn;
    }

    /**
     * 通过id获取对应页面类型
     * 
     * @param string $id page id
     * 
     * @return int p_type，如不存在则为NULL
     */
    public static function get_type_from_id($id)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT p_type FROM touchview_page WHERE (p_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_type'];
    	}
    
    	return $rtn;
    }

    /**
     * 通过id获取对应页面name
     * 
     * @param string $id page id
     * 
     * @return string p_name，如不存在则为''
     */
    public static function get_name_from_id($id)
    {
        $rtn = '';
    	$data_db['sql'] = "SELECT p_name FROM touchview_page WHERE (p_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_name'];
    	}
    
    	return $rtn;
    }

    /**
     * 通过id获取对应页面内容
     * 
     * @param string $id page id
     * 
     * @return string p_content，如不存在则为''
     */
    public static function get_content_from_id($id)
    {
        $rtn = '';
    	$data_db['sql'] = "SELECT p_content FROM touchview_page WHERE (p_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['p_content'];
    	}
    
    	return $rtn;
    }
    
    /**
     * 获取页面id对应的顶层页面的名称
     * 递归计算
     * 
     * @param string $id 父页面id
     * 
     * @return string 顶层页面名称
     */
    public static function get_top_page_name($id)
    {
        $result = '';
        
        $parent_id = page::get_parentid_from_id($id);

        if ($parent_id == page::get_bookid_from_id($id))
        {
            // 没有子页面则将当前id对应序号传回
            $result = page::get_name_from_id($id);
        }
        else
        {
            $result = page::get_top_page_name($parent_id);
        }
        
        return $result;
    }
    
    /**
     * 清空指定page的所有子页面
     * 不删除当前page
     * 
     * @param string $id page id
     * 
     * @return bool
     */
    public static function empty_all_child_page($id)
    {
        //--------变量定义及声明[start]--------
        $data_def = array();
        $result = false;// 返回结果
        //--------变量定义及声明[end]--------
        $data_def['child_list'] = array();
        page::get_all_child_pageid_list($id, $data_def['child_list']);
        
        $data_def['sql'] = "DELETE FROM touchview_page WHERE p_id IN ('' ";
        
        foreach ($data_def['child_list'] as $v)
        {
            $data_def['sql'] .= ",'{$v}'";
        }
        
        $data_def['sql'] .= ")";
        
        $result = scap_entity::excute_sql($data_def['sql']);
        if (!$result)
        {
            throw new Exception("清空子页面失败。");
        }
        
        $result = page::update_book_sort(page::get_bookid_from_id($id));
        
        return $result;
    }
    
    /**
     * 强制删除指定page及其所有子页面
     * 
     * @param string $id page id
     * 
     * @return bool
     */
    public static function force_delete_page($id)
    {
        //--------变量定义及声明[start]--------
        $data_def = array();
        $result = false;// 返回结果
        //--------变量定义及声明[end]--------
        $data_def['child_list'] = array();
        $data_def['b_id'] = page::get_bookid_from_id($id);
        page::get_all_child_pageid_list($id, $data_def['child_list']);
        
        $data_def['sql'] = "DELETE FROM touchview_page WHERE p_id IN ('{$id}' ";
        
        foreach ($data_def['child_list'] as $v)
        {
            $data_def['sql'] .= ",'{$v}'";
        }
        
        $data_def['sql'] .= ")";
        
        $result = scap_entity::excute_sql($data_def['sql']);
        if (!$result)
        {
            throw new Exception("强制删除页面失败。");
        }
        
        $result = page::update_book_sort($data_def['b_id']);
        
        return $result;
    }
    
    /**
     * 设置页面的配置值
     * 
     * @param string $id page id
     * @param string $key 配置项名称
     * @param string $value 配置项值
     * 
     * @return bool
     */
    public static function set_config_value($id, $key, $value)
    {
        $result = false;
        $data_db = array();
        $data_save = array();
        $config_arr = array();
        
    	$page = new page($id);
    	$data_db = $page->read();
    	
    	$config_arr = json_decode($data_db['p_config'], true);
    	unset($config_arr[$key]);
    	$config_arr[$key] = $value;
    	
    	$data_save['p_config'] = json_encode($config_arr);
    	
    	$result = $page->update($data_save);
    	
    	return $result;
    }
    
    /**
     * 获取页面的配置值
     * 
     * @param string $id page id
     * @param string $key 配置项名称
     * 
     * @return string 配置项值
     */
    public static function get_config_value($id, $key)
    {
        $value = '';
        $data_db = array();
        $config_arr = array();
        
        $page = new page($id);
        $data_db = $page->read();
        $config_arr = json_decode($data_db['p_config'], true);
        $value = $config_arr[$key];
        
        return $value;
    }
    
    /**
     * 设置页面的模板值
     * 
     * @param string $id page id
     * @param string $value
     * 
     * return bool
     */
    public static function set_page_tpl($id, $value)
    {
        return page::set_config_value($id, 'tpl', $value);
    }
    
    /**
     * 获取页面的模板值
     * 
     * @param string $id page id
     * 
     * @return string 模板值
     */
    public static function get_page_tpl($id)
    {
        return page::get_config_value($id, 'tpl');
    }
}
?>