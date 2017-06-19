<?php
/**
 * description: 通用应用日志类
 * create time: 2009-4-22-下午04:22:29
 * @version $Id: class.g_app_log.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 通用应用日志类
 *
 */
class g_app_log extends scap_entity
{
	/**
	 * 日志表名称
	 * @var string
	 */
	private $log_table_name = 'g_app_log';
	
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
		scap_load_module_define('module_g_00', 'log_type');
		
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
	 * @param int $log_type 日志类型
	 * @param int $sn 序号 默认为NULL，如果指定，则覆盖原sn的数据
	 * @return bool
	 */
	public function check_log_exist_by_sn($log_type, $sn)
	{
		$result = false;
		
		$result = scap_entity::get_row_count($this->log_table_name, "al_object_id = '{$this->current_object_id}' AND al_entity_id = '{$this->current_entity_id}' AND al_type = {$log_type} AND al_sn = {$sn}");
		
		if ($result === false)
		{
			throw new Exception("检查日志失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 更新日志
	 * 
	 * @author Liqt
	 * 
	 * @param int $log_type 操作类型
	 * @param string $comment 备注说明
	 * @param int $sn 操作的序号,如果为0,则默认在该类型操作的最大序号上自动+1作为新操作的序号
	 * 
	 * @return bool
	 */
	public function update_log($log_type, $comment = '', $sn = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$data_save['where'] = '';
		
		// 入库数据赋值
		$data_save['content']['al_object_id'] = $this->current_object_id;
		$data_save['content']['al_entity_id'] = $this->current_entity_id;
		$data_save['content']['al_type'] = $log_type;
		$data_save['content']['al_comment'] = $comment;
		$data_save['content']['al_client_ip'] = $_SERVER['REMOTE_ADDR'];
		$data_save['content']['al_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$data_save['content']['al_time'] = time();
		$data_save['content']['al_operator_id'] = $GLOBALS['scap']['auth']['account_s_id'];
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$max_sn = scap_entity::get_max_value($this->log_table_name, 'al_sn', "al_object_id = '{$this->current_object_id}' AND al_entity_id = '{$this->current_entity_id}' AND al_type = {$log_type}");
			
			if ($max_sn === false)
			{
				throw new Exception("获取max sn出错，插入日志失败。");
			}
			
			$data_save['content']['al_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['content']['al_sn'] = $sn;
			
			if ($this->check_log_exist_by_sn($log_type, $sn))
			{
				$data_save['type'] = 'UPDATE';
				$data_save['where'] = "al_object_id = '{$this->current_object_id}' AND al_entity_id = '{$this->current_entity_id}' AND al_type = {$log_type} AND al_sn = {$sn}";
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
			$ok = scap_entity::edit_row($this->log_table_name, $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00');
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新日志失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 清空object_id对应的所有日志
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	public function remove_all_log()
	{
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if($ok)
		{
			$ok = scap_entity::remove_rows($this->log_table_name, "al_object_id = '{$this->current_object_id}' AND al_entity_id = '{$this->current_entity_id}'");
		}

		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("清空日志失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获得执行指定类型操作的操作者
	 * @param $al_type
	 * @author Jianghb
	 * 
	 * @return a_s_id
	 */
	public function get_operator_by_type($al_type)
	{
		$data_db = array();
		$data_db['query']['sql'] = <<<SQL
SELECT al_operator_id from {$this->log_table_name}
WHERE al_object_id = '{$this->current_object_id}' AND al_type = {$al_type}
SQL;
		$temp = scap_entity::query($data_db['query'], false);
		$a_s_id = $temp[0]['al_operator_id'];
	
		return $a_s_id;
	}
	
	/**
	 * 获得指定操作状态下日志的创建时间
	 * 
	 * @author FuYing
	 * @param $al_type
	 * @return la_time
	 */
	public function get_altime_by_type($al_type)
	{
		$data_db = array();
		$data_db['query']['sql'] = <<<SQL
SELECT al_time from {$this->log_table_name}
WHERE al_object_id = '{$this->current_object_id}' AND al_sn = 1 AND al_type = {$al_type}
SQL;
		$temp = scap_entity::query($data_db['query'], false);
		$al_time = $temp[0]['al_time'];
	
		return $al_time;
	}	
}
?>