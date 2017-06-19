<?php
/**
 * description: scap实体抽象类
 * create time: 2009-3-30-14:51:45
 * @version $Id: core.class.scap_entity.inc.php 50 2012-11-07 09:53:40Z liqt $
 * @author LiQintao
 */

/**
 * scap实体抽象类
 * 
 * @author liqt
 *
 */
abstract class scap_entity
{
	/**
	 * 系统db实例
	 * @var object
	 */
	protected $db;
	
	function __construct()
	{
		$this->db = scap_entity::get_db_handle();
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * 获取db操作handle
	 * 
	 * @author liqt
	 * 
	 * @return object
	 */
	protected static function get_db_handle()
	{
		return $GLOBALS['scap']['db'];
	}
	
	/**
	 * db事物开始
	 * 
	 * @author liqt
	 * 
	 * @return bool true | false
	 */
	public static function db_begin_trans()
	{
		$db = scap_entity::get_db_handle();
		return $db->begin_trans();
	}
	
	/**
	 * db提交事务过程
	 * 
	 * @author liqt
	 * 
	 * @param bool $flag_commit 是否提交事务标志,默认为true
	 * 
	 * @return int 1-执行无错误,事务提交成功, 2-执行无错误,事务提交失败, 3-有错误发生,事务被成功回滚, 4-有错误发生,事务回滚失败
	 */
	public static function db_commit_trans($flag_commit = true)
	{
		$db = scap_entity::get_db_handle();
		return $db->commit_trans($flag_commit);
	}
	
	/**
	 * 向指定数据库编辑或添加行数据
	 * 
	 * @author liqt
	 * 
	 * @uses scap_entity::edit_row("g_org_unit", $content, 'insert', '', 'module_itsm_00');
	 * 
	 * @param string $table 操作表的名称
	 * @param array $content 对应的数据库行数据
	 * @param string $type {insert,update}操作类型
	 * @param string $where 指定数据行的查询条件(insert模式则为空)
	 * @param string $db_in_module 相关数据表定义所在的模块ID
	 * @param string $db_name 数据表定义文件名称，默认为tables(/setup/db.xxx.inc.php)
	 * 
	 * @return bool 执行成功返回true，反之为false
	 */
	public static function edit_row($table, $content, $type, $where = '', $db_in_module, $db_name = 'tables')
	{
		$rtn = false;
		$db = scap_entity::get_db_handle();
		
		// 读取数据表列信息,不能用include_once
		include (SCAP_PATH_ROOT.$db_in_module."/setup/db.$db_name.inc.php");
		
		$record = array();
		$type = strtoupper($type);
		
		$db_cols = $scap_db_tables[$table];
		unset($scap_db_tables);
		
		foreach($content as $k => $v)
		{
			// 非表数据
			if (!isset($db_cols[$k]))
			{
				continue;
			}
			
			$record[$k] = $v;
		}
		
		// 如果没有匹配元素，则跳过更新并返回成功
		if (empty($record))
		{
			return true;
		}
		
		$rtn = $db->db_connect->AutoExecute($table, $record, $type, empty($where) ? false : $where);
		return $rtn;
	}
	
	/**
	 * 按条件获取数据表数据集合(支持排序功能)
	 * @todo query查询方法效率有提高潜力：目前会执行2遍同样的查询，第一遍是为了获取总条目数，考虑是否有更好的方式。
	 * 
	 * @author liqt
	 * 
	 * @param string $query['id'] 返回数组的键值为该id的值
	 * @param string $query['order'] 当前排序的列名
	 * @param string $query['default_order'] 排序的默认列名
	 * @param string $query['sort'] 当前排序的方式(ASC|DESC)
	 * @param string $query['sql'] 查询的sql语句
	 * @param int $query['start'] 查询开始位置(0-based)
	 * @param int $query['steps'] 查询的步长(最大个数)
	 * @param [out]int $query['total'] 查询结果的数目
	 * @param [out]int $query['pages'] 共有多少分页
	 * 
	 * @param bool $flag_split 是否分页，默认为true（分页）
	 * 
	 * @return array 返回数据集合
	 */
	public static function query(&$query, $flag_split = true)
	{
		$data_rtn = array();// 返回的数据数组
		$data_in = array();
		$db = scap_entity::get_db_handle();
		
		$data_in['id'] = $query['id'];
		$data_in['default_order'] = $query['default_order'];
		$data_in['sql_order'] = '';
		$data_in['sql'] = $query['sql'];
		$data_in['start'] = intval($query['start']);
		$data_in['steps'] = (intval($query['steps']) <= 0) ? $GLOBALS['scap']['info']['index_steps'] : intval($query['steps']);
		
		$query['total'] = 0;
		$query['pages'] = 0;
		
		if (!empty($query['order']) && (empty($query['sort']) || preg_match('/^(DESC|ASC)$/', $query['sort'])))
		{
			$data_in['sql_order'] = " ORDER BY {$query['order']} {$query['sort']}";
		}
		else
		{
			if (!empty($data_in['default_order']))
			{
				$data_in['sql_order'] = " ORDER BY {$data_in['default_order']} DESC";
			}
		}
		
		// 获取总条目
		$rs = $db->db_connect->Execute($data_in['sql']);

		$query['total'] = is_object($rs) ? $rs->RecordCount() : 0;
		if ($data_in['steps'] > 0)
		{
			$query['pages'] = ceil($query['total'] / $data_in['steps']);
		}
		
		if (is_object($rs))
		{
			$rs->Close();
		}
		
		if ($data_in['start'] >= $query['pages'])
		{
			$query['start'] = $data_in['start'] = 0;
		}
		
		// 仅在分页查询时才加入order属性，可以提高查询总数的效率
		$data_in['sql'] .= $data_in['sql_order'];
		
		$db->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($flag_split)
		{
			$rs = $db->db_connect->SelectLimit($data_in['sql'], $data_in['steps'], $data_in['start']*$data_in['steps']);
		}
		else
		{
			$rs = $db->db_connect->SelectLimit($data_in['sql']);
		}
		
		if ($rs)
		{
			$i = 0;
			
			while(!$rs->EOF)
			{
				if (!empty($data_in['id']))// 以指定的id键值对应的数据作为返回集合的键值
				{
					$data_rtn[$rs->fields[$data_in['id']]] = $rs->fields;
				}
				else
				{
					$data_rtn[$i ++] = $rs->fields;
				}
				
				$rs->MoveNext();
			}
			
			$rs->Close();
		}
		
		return $data_rtn;
	}
	
	/**
	 * 删除指定条件的数据行
	 * 
	 * @author liqt
	 * 
	 * @param string $table 操作表的名称
	 * @param string $where 指定数据行的查询条件(不能为空)
	 * 
	 * @return bool 如果失败返回false,成功返回true
	 */
	public static function remove_rows($table, $where)
	{
		$rtn = false;
		$db = scap_entity::get_db_handle();
		
		$sql = "DELETE FROM $table WHERE $where";
		
		$rtn = $db->db_connect->Execute($sql);
		
		if ($rtn !== false)
		{
			$rtn = true;
		}
		
		return $rtn;
	}
	
	/**
	 * 执行sql语句
	 * 
	 * @author liqt
	 * 
	 * @param string $sql sql语句
	 * 
	 * @return bool 如果失败返回false,成功返回true
	 */
	public static function excute_sql($sql)
	{
		$rtn = false;
		$db = scap_entity::get_db_handle();
		
		$rtn = $db->db_connect->Execute($sql);
		
		if ($rtn !== false)
		{
			$rtn = true;
		}
		
		return $rtn;
	}
	
	/**
	 * 获取指定查询条件的数据行数目
	 * 
	 * @author Liqt
	 * 
	 * @param string $table 数据表名称
	 * @param string $where 数据表查询条件(不能带WHRER关键字)
	 * 
	 * @return int 条目数目，如果查询失败返回false
	 */
	public static function get_row_count($table, $where)
	{
		$count = false;
		$db = scap_entity::get_db_handle();
	
		$sql = "SELECT COUNT(*) row_count FROM $table";
		if (!empty($where))
		{
			$sql .= " WHERE ".$where;
		}

		$db->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $db->db_connect->Execute($sql);

		if ($rs)
		{
			$rs->MoveFirst();
			$count = $rs->fields['row_count'];
			$rs->Close();
		}

		return $count;
	}
	
	/**
	 * 获取数据表中指定表达式的最大的数值
	 *
	 * @param string $table 数据表名称
	 * @param string $max_item MAX中的表达式
	 * @param string $where 数据表查询条件
	 *
	 * @return int 条目数目，如果查询失败返回false
	 */
	public static function get_max_value($table, $max_item, $where)
	{
		$value = false;
		$db = scap_entity::get_db_handle();
		
		$sql = "SELECT MAX($max_item) max_value FROM $table";
		if (!empty($where))
		{
			$sql .= " WHERE ".$where;
		}

		$db->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $db->db_connect->Execute($sql);

		if ($rs)
		{
			$rs->MoveFirst();
			$value = $rs->fields['max_value'];
			$rs->Close();
		}

		return $value;
	}
	
	/**
	 * 获取数据表中指定表达式的最小的数值
	 *
	 * @param string $table 数据表名称
	 * @param string $min_item MAX中的表达式
	 * @param string $where 数据表查询条件
	 *
	 * @return int 条目数目，如果查询失败返回false
	 */
	public static function get_min_value($table, $min_item, $where)
	{
		$value = false;
		$db = scap_entity::get_db_handle();
		
		$sql = "SELECT MIN($min_item) min_value FROM $table";
		if (!empty($where))
		{
			$sql .= " WHERE ".$where;
		}

		$db->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $db->db_connect->Execute($sql);

		if ($rs)
		{
			$rs->MoveFirst();
			$value = $rs->fields['min_value'];
			$rs->Close();
		}

		return $value;
	}
}
?>