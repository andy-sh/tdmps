<?php
/**
 * description: 通用对象状态类
 * create time: 2009-4-22-下午04:55:13
 * @version $Id: class.g_status.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用对象状态类
 *
 */
class g_status extends scap_entity
{
	/**
	 * 状态表名称
	 * @var string
	 */
	private $status_table_name = 'g_object_status';
	
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
	
	public function __construct($object_id, $entity_id)
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
	 * 根据sn检查指定日志是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param int $status_type 状态类型
	 * @param int $sn 序号
	 * @return bool
	 */
	public function check_status_exist_by_sn($status_type, $sn)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->status_table_name, "os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}' AND os_status_type = {$status_type} AND os_sn = {$sn}");
		
		if ($result === false)
		{
			throw new Exception("检查状态失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 更新状态
	 * 
	 * @author Liqt
	 * 
	 * @param int $status_type 状态类型
	 * @param int $status 状态值
	 * @param string $comment 备注说明
	 * @param int $sn 默认为NULL，如果指定，则覆盖原sn的数据
	 * @package string $trigger_id 触发状态的帐号id,默认NULL(为不筛选触发者id)
	 * @return bool
	 */
	public function update_status($status_type, $status, $comment = '', $sn = NULL, $trigger_id = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		
		// 入库数据赋值
		$data_save['content']['os_object_id'] = $this->current_object_id;
		$data_save['content']['os_entity_id'] = $this->current_entity_id;
		$data_save['content']['os_status_type'] = $status_type;
		$data_save['content']['os_status'] = $status;
		$data_save['content']['os_comment'] = $comment;
		$data_save['content']['os_trigger_time'] = time();
		$data_save['content']['os_trigger_id'] = empty($trigger_id) ? $GLOBALS['scap']['auth']['account_s_id'] : $trigger_id;
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->status_table_name, 'os_sn', "os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}' AND os_status_type = {$status_type}");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入状态失败。");
			}
			
			$data_save['content']['os_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['content']['os_sn'] = $sn;
			
			if ($this->check_status_exist_by_sn($status_type, $sn))
			{
				$data_save['type'] = 'UPDATE';
				$data_save['where'] = "os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}' AND os_status_type = {$status_type} AND os_sn = {$sn}";
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
			$ok = scap_entity::edit_row($this->status_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新状态失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 根据触发者id删除相关状态关联
	 * 
	 * @param string $trigger_id 触发者id
	 * @param int $status_type 状态类别
	 * 
	 * @return bool
	 */
	public function remove_status_by_trigger($trigger_id, $status_type)
	{
	    $result = false;// 返回结果
        
        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;
        
        if($ok)
        {
            $ok = scap_entity::remove_rows($this->status_table_name, "os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}' AND os_trigger_id = '{$trigger_id}' AND os_status_type = {$status_type}");
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)
        {
            $result = true;// 执行成功标志
        }
        else
        {
            throw new Exception("清空触发者相关状态失败。");
        }
        //--------数据库事物处理[end]--------
        
        return $result;
	}
	
	/**
	 * 清空object_id对应的所有状态
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_all_status()
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->status_table_name, "os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空状态失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取对象最新的状态值
	 * 
	 * @author Liqt
	 * 
	 * @param int $status_type 状态类别
	 * @package string $trigger_id 触发状态的帐号id,默认NULL(为不筛选触发者id)
	 * 
	 * @return int 最近状态值
	 */
	public function get_last_status_value($status_type, $trigger_id = NULL)
	{
		$data_db = array();	// 数据库相关数据
		$rtn = NULL;
		
		// 构造筛选os_trigger_id的条件
		$where_trigger_id = empty($trigger_id) ? '' : "AND os_trigger_id = '{$trigger_id}'";
		
		$data_db['query']['sql'] = <<<SQL
SELECT os_status FROM {$this->status_table_name} 
WHERE (
	os_object_id = '{$this->current_object_id}' 
	AND os_entity_id = '{$this->current_entity_id}' 
	AND os_status_type = {$status_type} 
	AND os_sn = (SELECT MAX(os_sn) FROM {$this->status_table_name} WHERE (os_object_id = '{$this->current_object_id}' AND os_entity_id = '{$this->current_entity_id}' AND os_status_type = {$status_type} {$where_trigger_id}))
)
SQL;
		$temp = scap_entity::query($data_db['query'], false);
		$rtn = $temp[0]['os_status'];
		
		return $rtn;
	}
}
?>