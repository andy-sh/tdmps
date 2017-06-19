<?php
/**
 * description: 通用时间节点类
 * create time: 2009-4-23-上午09:11:41
 * @version $Id: class.g_time_node.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用时间节点类
 *
 */
class g_time_node extends scap_entity
{
	/**
	 * 时间节点表名称
	 * @var string
	 */
	private $time_table_name = 'g_object_time_node';
	
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
	 * 根据sn检查指定时间是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param int $sn 链接序号
	 * @return bool
	 */
	public function check_time_exist_by_sn($sn)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_sn = {$sn}");
		
		if ($result === false)
		{
			throw new Exception("检查时间节点失败。");
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
	public function check_time_exist_by_category($category_id)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_category = '{$category_id}'");
		
		if ($result === false)
		{
			throw new Exception("检查时间节点失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 根据time_name检查指定链接是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param string $time_name 关联名称
	 * @return bool
	 */
	public function check_time_exist_by_name($time_name)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_time = '{$time_name}'");
		
		if ($result === false)
		{
			throw new Exception("检查时间节点失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 更新实体对象的时间节点数据
	 * 
	 * @author Liqt
	 * 
	 * @param string $category_id 类别id
	 * @param string $time_name 关联名称
	 * @param string $name 时间节点名称
	 * @param int $sn 序号，默认为NULL（自动递增赋值）,指定sn则更新原sn的数据
	 * 
	 * @return bool
	 */
	public function update_time($category_id, $time_name, $time, $sn = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		// 入库数据赋值
		$data_save['content']['otn_object_id'] = $this->current_object_id;
		$data_save['content']['otn_entity_id'] = $this->current_entity_id;
		$data_save['content']['otn_category'] = $category_id;
		$data_save['content']['otn_name'] = $time_name;
		$data_save['content']['otn_time'] = $time;
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->time_table_name, 'otn_sn', "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}'");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入时间节点失败。");
			}
			
			$data_save['content']['otn_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['content']['otn_sn'] = $sn;
			
			if ($this->check_time_exist_by_sn($sn))
			{
				$data_save['type'] = 'UPDATE';
				$data_save['where'] = "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_sn = {$sn}";
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
			$ok = scap_entity::edit_row($this->time_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新时间节点失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 根据时间名称更新实体对象的时间节点数据（保持时间名称的唯一性）
	 * 
	 * @author Liqt
	 * 
	 * @param string $time_name 时间节点名称
	 * @param string $time 时间值
	 * @param string $category 所属类别
	 * 
	 * @return bool
	 */
	public function update_time_by_name($time_name, $time, $category='')
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$result = $this->remove_time_by_name($time_name);
		$result = $this->update_time($category, $time_name, $time);
		
		return $result;
	}
	
	/**
	 * 清空object_id对应的所有时间节点
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_all_time()
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空时间节点失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除sn对应时间节点
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_time_by_sn($sn)
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_sn = {$sn}");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除时间节点失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除time_name对应时间节点
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_time_by_name($time_name)
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->time_table_name, "otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' AND otn_name = '$time_name'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除时间节点失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取时间节点内容数组
	 * 
	 * @author Liqt
	 * 
	 * @param string $where 附加查询条件（以AND开头）
	 * 
	 * @return array 输出数组格式为array('otn_sn' => 'otn_category', ...)
	 */
	public function get_time_list($where = '')
	{
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		$list = array();
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT * FROM {$this->time_table_name}  
WHERE (otn_object_id = '{$this->current_object_id}' AND otn_entity_id = '{$this->current_entity_id}' {$where})
SQL;
	
		$data_db['query']['id'] = 'otn_sn';
		$data_db['query']['order'] = 'otn_sn';
		$data_db['query']['sort'] = 'ASC';
		
		$list = scap_entity::query($data_db['query'], false);
		
		return $list;
	}
	
	/**
	 * 根据时间名称获取时间值
	 * 
	 * @author Liqt
	 * 
	 * @tutorial 如果有多个重名的，则只获取sn最大的
	 * 
	 * @param string $time_name 时间名称
	 * @param string $format 返回的时间格式，默认为"Y-m-d"
	 * 
	 * @return date 格式化后的时间值
	 */
	public function get_time_by_name($time_name, $format="Y-m-d")
	{
		$data_out = array();
		$result = NULL;
		
		$data_out = $this->get_time_list("AND otn_name = '$time_name'");
		
		krsort($data_out);// 降序排列
		$temp = current($data_out);
		$result = empty($temp['otn_time']) ? '' : date($format, strtotime($temp['otn_time']));
		
		return $result;
	}
}
?>