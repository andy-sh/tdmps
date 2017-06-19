<?php
/**
 * description: scap db operate
 * create time: 2006-10-20 12:30:51
 * @version $Id: core.class.scap_db.inc.php 50 2012-11-07 09:53:40Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

require_once (SCAP_PATH_LIBRARY.'adodb/adodb.inc.php');

class scap_db
{
	/**
	 * @var object 数据库链接实例
	 */
	var $db_connect = NULL;
	
	/**
	 * 构造函数
	 * 
	 * @param string $host 主机地址
	 * @param string $user db用户名
	 * @param string $password db密码
	 * @param string $database 所链接数据库名称
	 * @param string $type_db 所链接数据库类型
	 */
	function scap_db($host, $user, $password, $database, $type_db = "mysql")
	{
		$this->db_connect = &ADONewConnection($type_db);
		$this->db_connect->Connect($host, $user, $password, $database);
		$this->db_connect->Execute("SET NAMES UTF8");// 设置连接为UTF8字符串，否则adodb会出现乱码
	}
	
	/**
	 * 更新指定数据表结构
	 * 
	 * @param string $tablename 数据表名称(case sensitive)
	 * @param array $arr_struct 数据表结构数组
	 * @param array $arr_tableoptions 数据表参数数组
	 * 
	 * @return bool true | false
	 */
	function update_table_struct($tablename, $arr_struct, $arr_tableoptions = array())
	{
		$rtn = false;
		$dict = NewDataDictionary($this->db_connect);
		
		if (empty($arr_tableoptions))
		{
			$arr_tableoptions = false;
		}
		
		$arr_sql = $dict->ChangeTableSQL($tablename, $arr_struct, $arr_tableoptions);
		
		if ($dict->ExecuteSQLArray($arr_sql) == 2)
		{
			$rtn = true;
		}
		
		return $rtn;		 
	}
	
	/**
	 * 开始事务过程
	 * 
	 * * @return bool true | false
	 */
	function begin_trans()
	{
		return $this->db_connect->BeginTrans();
	}
	
	/**
	 * 提交事务过程
	 * 
	 * @param bool $flag_commit 是否提交事务标志,默认为true
	 * 
	 * @return int 1-执行无错误,事务提交成功, 2-执行无错误,事务提交失败, 3-有错误发生,事务被成功回滚, 4-有错误发生,事务回滚失败
	 */
	function commit_trans($flag_commit = true)
	{
		$flag = $this->db_connect->CommitTrans($flag_commit);
		
		if ($flag_commit && $flag)// 执行无错误,事务提交成功
		{
			$result = 1;
		}
		elseif ($flag_commit && !$flag)// 执行无错误,事务提交失败
		{
			$result = 2;
		}
		elseif (!$flag_commit && $flag)// 有错误发生,事务被成功回滚
		{
			$result = 3;
		}
		elseif (!$flag_commit && !$flag)// 有错误发生,事务回滚失败
		{
			$result = 4;
		}
		
		return $result;
	}
}
?>