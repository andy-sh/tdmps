<?php
/**
 * description: 通用附加要素类
 * create time: 2009-4-23-上午11:18:13
 * @version $Id: class.g_ae.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用附加要素类
 *
 */
class g_ae extends scap_entity
{
	/**
	 * 附加要素表名称
	 * @var string
	 */
	private $ae_table_name = 'g_object_attach_element';
	
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
	public function check_ae_exist_by_sn($sn)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->ae_table_name, "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_sn = {$sn}");
		
		if ($result === false)
		{
			throw new Exception("检查附加要素失败。");
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
	public function check_ae_exist_by_category($category_id)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->ae_table_name, "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_category = '{$category_id}'");
		
		if ($result === false)
		{
			throw new Exception("检查附加要素失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 根据ae_name检查指定链接是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param string $ae_name 关联名称
	 * @return bool
	 */
	public function check_ae_exist_by_name($ae_name)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->ae_table_name, "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_name = '{$ae_name}'");
		
		if ($result === false)
		{
			throw new Exception("检查附加要素失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 更新实体对象的附加要素数据
	 * 
	 * @author Liqt
	 * 
	 * @param string $ae_name 要素名称
	 * @param string $ae_value 要素值
	 * @param string $category_id 类别id
	 * @param int $logic_flag 要素的逻辑标志
	 * @param int $sn 序号，默认为NULL（自动递增赋值）,指定sn则更新原sn的数据
	 * 
	 * @return bool
	 */
	public function update_ae($ae_name, $ae_value, $category_id, $logic_flag = 0, $sn = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		// 入库数据赋值
		$data_save['content']['oae_object_id'] = $this->current_object_id;
		$data_save['content']['oae_entity_id'] = $this->current_entity_id;
		$data_save['content']['oae_category'] = $category_id;
		$data_save['content']['oae_name'] = $ae_name;
		$data_save['content']['oae_value'] = $ae_value;
		$data_save['content']['oae_logic_flag'] = $logic_flag;
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->ae_table_name, 'oae_sn', "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}'");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入附加要素失败。");
			}
			
			$data_save['content']['oae_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['content']['oae_sn'] = $sn;
			
			if ($this->check_ae_exist_by_sn($sn))
			{
				$data_save['type'] = 'UPDATE';
				$data_save['where'] = "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_sn = {$sn}";
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
			$ok = scap_entity::edit_row($this->ae_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新附加要素失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 更新实体对象的附加要素数据
	 * 
	 * @author shengyj
	 * 
	 * @param string $ae_name 要素名称
	 * @param string $ae_value 要素值
	 * @param string $category_id 类别id
	 * @param int $logic_flag 要素的逻辑标志
	 * 
	 * @return bool
	 */
	public function update_ae_by_name($ae_name, $ae_value, $category_id, $logic_flag = 0)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		// 入库数据赋值
		$data_save['content']['oae_object_id'] = $this->current_object_id;
		$data_save['content']['oae_entity_id'] = $this->current_entity_id;
		$data_save['content']['oae_category'] = $category_id;
		$data_save['content']['oae_name'] = $ae_name;
		$data_save['content']['oae_value'] = $ae_value;
		$data_save['content']['oae_logic_flag'] = $logic_flag;

		if (!$this->check_ae_exist_by_name($ae_name))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->ae_table_name, 'oae_sn', "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}'");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入附加要素失败。");
			}
			
			$data_save['content']['oae_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['type'] = 'UPDATE';
			$data_save['where'] = "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_name = '{$ae_name}'";
		}
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::edit_row($this->ae_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务

		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新附加要素失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 清空object_id对应的所有附加要素
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_all_ae($where = "")
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->ae_table_name, "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' {$where}");
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空附加要素失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除sn对应附加要素
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_ae_by_sn($sn)
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->ae_table_name, "oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' AND oae_sn = {$sn}");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除附加要素失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取附加要素内容数组
	 * 
	 * @author Liqt
	 * 
	 * @param string $where 附加查询条件（以AND开头）
	 * 
	 * @return array 输出数组格式为array('oae_sn' => 'oae_category', ...)
	 */
	public function get_ae_list($where = '')
	{
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		$list = array();
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT * FROM {$this->ae_table_name}  
WHERE (oae_object_id = '{$this->current_object_id}' AND oae_entity_id = '{$this->current_entity_id}' {$where})
SQL;
	
		$data_db['query']['id'] = 'oae_sn';
		$data_db['query']['order'] = 'oae_sn';
		$data_db['query']['sort'] = 'ASC';
		
		$list = scap_entity::query($data_db['query'], false);
		
		return $list;
	}
	
	/**
	 * 根据sn获取逻辑位
	 * 
	 * @author Liqt
	 * 
	 * @param string $time_name 时间名称
	 * @return int 逻辑位值
	 */
	public function get_logic_flag_by_sn($sn)
	{
		$data_out = array();
		$result = NULL;
		
		$data_out = $this->get_ae_list("AND oae_sn = {$sn}");
		
		$result = $data_out[$sn]['oae_logic_flag'];
		
		return $result;
	}
}
?>