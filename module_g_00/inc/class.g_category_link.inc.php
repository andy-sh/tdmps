<?php
/**
 * description: 通用category实体类
 * create time: 2009-4-21-下午02:53:01
 * @version $Id: class.g_category_link.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用category实体类
 * 
 * @tutorial 对g_object_category_link数据表进行操作，实现各实体与类别的各种关系。
 *
 */
class g_category_link extends scap_entity
{
	/**
	 * 关联表名称
	 * @var string
	 */
	private $link_table_name = 'g_object_category_link';
	
	/**
	 * 当前关联对象id
	 * @var string uid
	 */
	private $current_object_id = NULL;
	
	/**
	 * 当前关联对象的实体id
	 * @var string uid
	 */
	private $current_entity_id = NULL;
	
	function __construct($object_id, $entity_id)
	{
		parent::__construct();
		$this->set_current_object_id($object_id);
		$this->set_current_entity_id($entity_id);
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
	 * 根据sn检查指定链接是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param int $sn 链接序号
	 * @return bool
	 */
	public function check_link_exist_by_sn($sn)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_sn = {$sn}");
		
		if ($result === false)
		{
			throw new Exception("检查类别关联失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 根据category_id检查指定链接是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param string $category_id 关联的类别id
	 * @return bool
	 */
	public function check_link_exist_by_category($category_id)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_category_id = '{$category_id}'");
		
		if ($result === false)
		{
			throw new Exception("检查类别关联失败。");
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
	 * @author Liqt
	 * 
	 * @param string $link_name 关联名称
	 * @return bool
	 */
	public function check_link_exist_by_name($link_name)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_name = '{$link_name}'");
		
		if ($result === false)
		{
			throw new Exception("检查类别关联失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 更新实体对象的类别关联数据
	 * 
	 * @author Liqt
	 * 
	 * @param string $category_id 类别id
	 * @param string $link_name 关联名称
	 * @param int $sn 序号，默认为NULL（自动递增赋值）,指定sn则更新原sn的数据
	 * 
	 * @return bool
	 */
	public function update_link($category_id, $link_name, $sn = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		// 入库数据赋值
		$data_save['content']['ocl_object_id'] = $this->current_object_id;
		$data_save['content']['ocl_entity_id'] = $this->current_entity_id;
		$data_save['content']['ocl_category_id'] = $category_id;
		if (!empty($link_name))
		{
			$data_save['content']['ocl_name'] = $link_name;
		}
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->link_table_name, 'ocl_sn', "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}'");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入类别关联失败。");
			}
			
			$data_save['content']['ocl_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['content']['ocl_sn'] = $sn;
			
			if ($this->check_link_exist_by_sn($sn))
			{
				$data_save['type'] = 'UPDATE';
				$data_save['where'] = "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_sn = {$sn}";
			}
			else
			{
				$data_save['type'] = 'INSERT';
			}
		}
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
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
			throw new Exception("更新类别关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 依据类别名称更新实体对象的类别关联数据（覆盖类名相同的数据,保持名称对应值的唯一性）
	 * 
	 * @author Liqt
	 * 
	 * @param string $category_id 类别id
	 * @param string $link_name 关联名称
	 * 
	 * @return bool
	 */
	public function update_link_by_name($category_id, $link_name)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		$result = $this->remove_link_by_name($link_name);
		
		$result = $this->update_link($category_id, $link_name);
		
		
		return $result;
	}
	
	/**
	 * 清空object_id对应的所有类别关联
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_all_link()
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空类别关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除sn对应类别关联
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_link_by_sn($sn)
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_sn = {$sn}");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除类别关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除link_name对应类别关联
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_link_by_name($link_name)
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->link_table_name, "ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' AND ocl_name = '{$link_name}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除类别关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取关联内容数组
	 * 
	 * @author Liqt
	 * 
	 * @param string $where 附加查询条件（以AND开头）
	 * 
	 * @return array 输出数组格式为array('ocl_sn' => 'ocl_category_id', ...)
	 */
	public function get_link_list($where = '')
	{
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		$list = array();
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT * FROM {$this->link_table_name}  
WHERE (ocl_object_id = '{$this->current_object_id}' AND ocl_entity_id = '{$this->current_entity_id}' {$where})
SQL;
	
		$data_db['query']['id'] = 'ocl_sn';
		$data_db['query']['order'] = 'ocl_sn';
		$data_db['query']['sort'] = 'ASC';
		
		$list = scap_entity::query($data_db['query'], false);
		
		return $list;
	}
	
	/**
	 * 根据类别的名称获取对应类别的值（如有多个，仅返回sn最大的）
	 * 
	 * @author Liqt
	 * 
	 * @param string $category_name 类别的ID（类型）
	 * @return string 类别的值
	 */
	public function get_category_by_name($category_name)
	{
		$data_out = array();
		$result = '';
		
		$data_out = $this->get_link_list("AND ocl_name = '$category_name'");
		
		krsort($data_out);// 降序排列
		$result = current($data_out);
		
		return $result['ocl_category_id'];
	}
	
	/**
	 * 根据关联类别的类别值获取sn
	 * 
	 * @param string $category_id 类别值
	 * @param string $category_name 关联名称
	 * 
	 * @return int 序号
	 */
	public function get_sn_by_id($category_id, $category_name)
	{
	    $data_out = array();
        $result = '';
        
        $data_out = $this->get_link_list("AND ocl_category_id = '$category_id' AND ocl_name = '$category_name'");
        
        krsort($data_out);// 降序排列
        $result = current($data_out);
        
        return $result['ocl_sn'];
	}
}
?>