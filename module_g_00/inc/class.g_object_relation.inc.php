<?php
/**
 * 实体对象关系操作类
 * create time: 2010-7-29 16:45:59
 * @version $Id: class.g_object_relation.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 实体对象关系操作类
 *
 */
class g_object_relation extends scap_entity
{
	/**
	 * 当前对象角色类型的常量定义:主对象
	 */
	const TYPE_ROLE_PRIMARY = 1;

	/**
	 * 当前对象角色类型的常量定义:次对象
	 */
	const TYPE_ROLE_SECONDARY = 2;

	/**
	 * 当前对象角色类型的常量定义:平等的
	 */
	const TYPE_ROLE_EGALITY = 3;

	/**
	 * 关系表名称
	 * @var string
	 */
	private $table_name = 'g_object_relation';

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

	/**
	 * 当前关联类型
	 * @var int
	 */
	private $current_relation_type = NULL;

	/**
	 * 当前对象角色类型
	 * @var int
	 */
	private $current_object_role = NULL;

	/**
	 * 关联实体的id
	 * @var string
	 */
	private $relation_entity_id = NULL;

	/**
	 * 当前设置下的条件语句
	 * @var string
	 */
	private $current_condition_statement = NULL;

	/**
	 * 通用对象关联类构造函数
	 *
	 * @param $object_id 当前关联对象id
	 * @param $entity_id 当前关联对象的实体id
	 */
	function __construct($object_id, $entity_id)
	{
		parent::__construct();
		scap_load_module_define('module_g_00', 'relation_type');
		
		$this->set_current_object_id($object_id);
		$this->set_current_entity_id($entity_id);
	}
	
	/**
	 * 检查关联是否存在
	 * 
	 * @param $relation_object_id
	 * @param $relation_type
	 * @return bool
	 */
	public function check_relation_exist($relation_object_id)
	{
	    $result = false;
		$where = '';
		
	    $where = "or_relation_type = '{$this->current_relation_type}' AND ((or_primary_object_id = '{$this->current_object_id}' AND or_primary_entity_id = '{$this->current_entity_id}' AND or_secondary_entity_id = '{$this->relation_entity_id}' AND or_secondary_object_id = '{$relation_object_id}') OR (or_secondary_object_id = '{$this->current_object_id}' AND or_secondary_entity_id = '{$this->current_entity_id}' AND or_primary_entity_id = '{$this->relation_entity_id}' AND or_primary_object_id = '{$relation_object_id}'))";
		
		$result = scap_entity::get_row_count($this->table_name, $where);
		
		if ($result === false)
		{
			throw new Exception("检查关联失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}

	/**
	 * 设置当前object_id
	 * @param string $object_id
	 */
	private function set_current_object_id($object_id)
	{
		$this->current_object_id = $object_id;
	}

	/**
	 * 设置当前entity_id
	 * @param string $entity_id
	 */
	private function set_current_entity_id($entity_id)
	{
		$this->current_entity_id = $entity_id;
	}

	/**
	 * 获得当前object_id,如果object_id不存在返回NULL
	 *
	 * @return string
	 */
	public function get_current_object_id()
	{
		return $this->current_object_id;
	}

	/**
	 * 设置当前的对象关系信息
	 *
	 * @param int $relation_type 关系类型
	 * @param int $current_object_role 当前对象的角色类型：TYPE_ROLE_PRIMARY | TYPE_ROLE_SECONDARY | TYPE_ROLE_EGALITY
	 * @param string $relation_entity_id 关联对象所属的实体id
	 */
	public function set_current_relation($relation_type, $current_object_role, $relation_entity_id)
	{
		$this->current_relation_type = $relation_type;

		if ($current_object_role != self::TYPE_ROLE_PRIMARY
		&& $current_object_role != self::TYPE_ROLE_SECONDARY
		&& $current_object_role != self::TYPE_ROLE_EGALITY)
		{
			throw new Exception('无效的对象角色类型。');
		}
		else
		{
			$this->current_object_role = $current_object_role;
		}

		$this->relation_entity_id = $relation_entity_id;
	}

	/**
	 * 插入关系
	 * @param string $relation_object_id 关联的对象id
	 * @param string $comment 备注，默认为空
	 * 
	 * @return bool 操作是否成功
	 */
	public function update_relation($relation_object_id, $comment = '')
	{
		if (empty($this->relation_entity_id))
		{
			throw new Exception("当前的对象关系信息未设置。");
		}
		
		if($this->current_object_id == $relation_object_id)
		{
		    return false;
		}

		$data_in = array();// 输入数据
		$data_flag = array();
		$data_save = array();// 入库数据
		$result = false;// 返回结果

		$data_save['where'] = '';

		$data_flag['compare_entity_id'] = strcasecmp($this->current_entity_id, $this->relation_entity_id);
		$data_flag['compare_object_id'] = strcasecmp($this->current_object_id, $relation_object_id);

		$data_save['content']['or_relation_type'] = $this->current_relation_type;
		$data_save['content']['or_comment'] = $comment;
        
		if ($this->current_object_role == self::TYPE_ROLE_PRIMARY ||
		($this->current_object_role == self::TYPE_ROLE_EGALITY && $data_flag['compare_entity_id'] < 0) || // 实体id小的
		($this->current_object_role == self::TYPE_ROLE_EGALITY && $data_flag['compare_entity_id'] == 0 && $data_flag['compare_object_id'] <= 0) // 对象id小的
		)
		{
			$data_save['content']['or_primary_entity_id'] = $this->current_entity_id;
			$data_save['content']['or_primary_object_id'] = $this->current_object_id;
			$data_save['content']['or_secondary_entity_id'] = $this->relation_entity_id;
			$data_save['content']['or_secondary_object_id'] = $relation_object_id;
		}
		else
		{
			$data_save['content']['or_primary_entity_id'] = $this->relation_entity_id;
			$data_save['content']['or_primary_object_id'] = $relation_object_id;
			$data_save['content']['or_secondary_entity_id'] = $this->current_entity_id;
			$data_save['content']['or_secondary_object_id'] = $this->current_object_id;
		}
		
	    if ($this->check_relation_exist($relation_object_id))
		{
			$data_save['type'] = 'UPDATE';
			$data_save['where'] = "or_primary_entity_id = '{$data_save['content']['or_primary_entity_id']}' AND or_primary_object_id = '{$data_save['content']['or_primary_object_id']}' AND or_secondary_entity_id = '{$data_save['content']['or_secondary_entity_id']}' AND or_secondary_object_id = '{$data_save['content']['or_secondary_object_id']}' AND or_relation_type = '{$data_save['content']['or_relation_type']}' ";
		}
		else
		{
			$data_save['type'] = 'INSERT';
		}
		//--------数据库事物处理[start]--------
		
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::edit_row($this->table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新关系对象关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}

	/**
	 * 清空当前object对应的所有关联
	 * 
	 * @return bool
	 */
	public function remove_all_relation()
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
	
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->table_name, "or_primary_object_id = '{$this->current_object_id}' OR  or_secondary_object_id = '{$this->current_object_id}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空对象所有关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除指定的关联
	 * @param $relation_object_id 关联的实体对象id,默认为NULL(为NULL时删除当前对应的实体和关系类型的所有关联)
	 * 
	 * @return bool
	 * 
	 */
	public function remove_relation($relation_object_id = NULL)
	{
	    if (empty($this->relation_entity_id))
		{
			throw new Exception("关联对象的实体ID未设置。");
		}
		
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
	    if($ok)
		{
		    if (is_null($relation_object_id))
		    {
		        $ok = scap_entity::remove_rows($this->table_name, "or_relation_type = '{$this->current_relation_type}' AND ((or_primary_object_id = '{$this->current_object_id}' AND or_primary_entity_id = '{$this->current_entity_id}' AND or_secondary_entity_id = '{$this->relation_entity_id}') OR (or_secondary_object_id = '{$this->current_object_id}' AND or_secondary_entity_id = '{$this->current_entity_id}' AND or_primary_entity_id = '{$this->relation_entity_id}'))");
		    }
		    else
		    {
			     $ok = scap_entity::remove_rows($this->table_name, "or_relation_type = '{$this->current_relation_type}' AND ((or_primary_object_id = '{$this->current_object_id}' AND or_primary_entity_id = '{$this->current_entity_id}' AND or_secondary_object_id = '{$relation_object_id}' AND or_secondary_entity_id = '{$this->relation_entity_id}') OR (or_secondary_object_id = '{$this->current_object_id}' AND or_secondary_entity_id = '{$this->current_entity_id}' AND or_primary_object_id = '{$relation_object_id}' AND or_primary_entity_id = '{$this->relation_entity_id}'))");
		    }
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("删除对象关联失败。");
		}
		//--------数据库事物处理[end]--------
    	
		return $result;
	}
	
	/**
	 * 删除两个实体之间的关联
	 * 
	 */
	public static function remove_relation_between($object_id1, $object_id2)
	{
	    $result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
	
		if($ok)
		{
			$ok = scap_entity::remove_rows('g_object_relation', "(or_primary_object_id = '{$object_id1}' AND  or_secondary_object_id = '{$object_id2}') OR (or_primary_object_id = '{$object_id2}' AND  or_secondary_object_id = '{$object_id1}')");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空对象关联失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取关联内容数组
	 * 
	 * @return array array(array('relation_object_id' => 'xxx', 'comment' => 'yyyyy'), ...)
	 */
	public function get_relation_object_id_list()
	{
		if (empty($this->relation_entity_id))
		{
			throw new Exception("关联对象的实体ID未设置。");
		}
		
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		
		$list_when_current_is_primary = array();
		$list_when_current_is_secondary = array();
		
		$list = array();
		//--------变量定义及声明[end]--------
		
		if($this->current_object_role == self::TYPE_ROLE_PRIMARY || $this->current_object_role == self::TYPE_ROLE_EGALITY )
		{
		    //先得到当前object id为primary的所有关联$list_when_current_is_primary
		    $data_db['query_primary']['sql'] = <<<SQL
SELECT or_secondary_object_id relation_object_id, or_comment comment FROM {$this->table_name}  
WHERE (or_relation_type = '{$this->current_relation_type}' AND or_primary_object_id = '{$this->current_object_id}' AND or_primary_entity_id = '{$this->current_entity_id}' AND or_secondary_entity_id = '{$this->relation_entity_id}')
SQL;
	
		    $list_when_current_is_primary = scap_entity::query($data_db['query_primary'], false);
		}
		
		if($this->current_object_role == self::TYPE_ROLE_SECONDARY || $this->current_object_role == self::TYPE_ROLE_EGALITY )
		{
		    //再得到当前object id为secondary的所有关联$list_when_current_is_secondary
		    $data_db['query_secondary']['sql'] = <<<SQL
SELECT or_primary_object_id relation_object_id, or_comment comment FROM {$this->table_name}  
WHERE (or_relation_type = '{$this->current_relation_type}' AND or_secondary_object_id = '{$this->current_object_id}' AND or_secondary_entity_id = '{$this->current_entity_id}' AND or_primary_entity_id = '{$this->relation_entity_id}')
SQL;
	
		    $list_when_current_is_secondary = scap_entity::query($data_db['query_secondary'], false);
		}
		
		$list = array_merge($list_when_current_is_primary, $list_when_current_is_secondary);
		return $list;
	}
	
	
	/**
	 * 获得已关联对象id列表
	 * 
	 * @param String $column_name 数据表字段名
	 * @param String $table_name 数据表名
	 * 
	 * @return Array() list
	 */
	function get_relation_link_object_id_list($column_name, $table_name)
	{
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
			
		$list_when_current_is_primary = array();
		$list_when_current_is_secondary = array();
			
		$list = array();
		//--------变量定义及声明[end]--------
			
		//先得到当前object id为primary的所有关联$list_when_current_is_primary
		$data_db['query_primary']['sql'] = <<<SQL
SELECT or_secondary_object_id relation_object_id FROM g_object_relation 
WHERE (or_secondary_object_id IN (SELECT {$column_name} FROM $table_name))
SQL;
		$list_when_current_is_primary = scap_entity::query($data_db['query_primary'], false);
			
		//再得到当前object id为secondary的所有关联$list_when_current_is_secondary
		$data_db['query_secondary']['sql'] = <<<SQL
SELECT or_primary_object_id relation_object_id FROM g_object_relation  
WHERE (or_primary_object_id IN (SELECT {$column_name} FROM $table_name))
SQL;
		$list_when_current_is_secondary = scap_entity::query($data_db['query_secondary'], false);
			
		$list = array_merge($list_when_current_is_primary, $list_when_current_is_secondary);
		
		return $list;		
	}	
}
?>