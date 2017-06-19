<?php
/**
 * description: 系统二进制数据类
 * create time: 2009-4-1-下午02:17:59
 * @version $Id: class.binary_data.inc.php 50 2012-11-07 09:53:40Z liqt $
 * @author LiQintao
 */

define('G_TYPE_BINARY_STORAGE_DB', 1);// 数据库存储
define('G_TYPE_BINARY_STORAGE_FS', 2);// 文件系统存储
if (!defined('G_BINARY_FS_DIR'))
{
    define('G_BINARY_FS_DIR', 'D:/Wamp/scap_bd_file/');// 默认FS存储路径
}

/**
 * 基于G模块的二进制数据类
 */
class binary_data extends scap_entity
{
	/**
	 * 当前bd_id
	 * 
	 * @var string
	 */
	private $current_bd_id = NULL;
	
	/**
	 * 文件系统的存储路径
	 * 
	 * @var string
	 */
	private $current_fs_dir = G_BINARY_FS_DIR;
	
	/**
	 * 当前文件的索引信息
	 * @var array
	 */
	private $index_info = array();
	
	function __construct($bd_id)
	{
		parent::__construct();
		$this->set_current_bd_id($bd_id);
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * 设置当前bd_id
	 * @param string $bd_id
	 */
	public function set_current_bd_id($bd_id)
	{
		if (binary_data::check_bd_exist($bd_id))
		{
			$this->current_bd_id = $bd_id;
			$this->read_index();//读取索引信息
		}
		else
		{
			$this->current_bd_id = NULL;
			throw new Exception("bd_id不存在于系统中。");
		}
	}
	
	/**
	 * 获得当前bd_id
	 * 
	 * @author Liqt
	 * 
	 * @return string
	 */
	public function get_current_bd_id()
	{
		return $this->current_bd_id;
	}
	
	/**
	 * 从g_binary_data读取文件索引信息赋值给$this->index_info
	 * 
	 * @author Liqt
	 * 
	 * @return null
	 */
	private function read_index()
	{
		$data_db = array();	// 数据库相关数据
		$data_db['content'] = array();
		
		$data_db['query']['sql'] = <<<SQL
SELECT * FROM g_binary_data 
WHERE (bd_id = '{$this->current_bd_id}')
SQL;
		$temp = scap_entity::query($data_db['query'], false);
		$this->index_info = $temp[0];
	}
	
	/**
	 * 获取当前文件索引信息
	 * 
	 * @return array 文件索引信息
	 */
	public function get_index()
	{
		return $this->index_info;
	}
	
	/**
	 * 设置当前文件存储路径
	 * 
	 * @param string $fs_dir
	 */
	public function set_current_fs_dir($fs_dir)
	{
		if (file_exists($fs_dir))
		{
			$this->current_fs_dir = $fs_dir;
		}
		else
		{
			$this->current_fs_dir = NULL;
			throw new Exception("指定文件存储路径不存在于系统中。");
		}
		
	}
	
	/**
	 * 获得当前fs_id
	 * 
	 * @author Liqt
	 * 
	 * @return string
	 */
	public function get_current_fs_dir()
	{
		return $this->current_fs_dir;
	}
	
	/**
	 * 检查指定的bd是否存在
	 * 
	 * @author Liqt
	 * 
	 * @param string $bd_id
	 * 
	 * @return bool
	 */
	public static function check_bd_exist($bd_id)
	{
		$result = false;
		
		$result = scap_db_get_row_count('g_binary_data', "bd_id = '{$bd_id}'");
		
		if ($result === false)
		{
			throw new Exception("检查二进制数据失败。");
		}
		elseif ($result > 0)
		{
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * 上传文件到系统
	 * 
	 * @author Liqt
	 * 
	 * @param array $upload_info POST上传的$_FILES信息
	 * @param array $index_info 上传的文件索引信息:bd_entity_id/bd_comment
	 * @param int $storage_type 存储的类型：G_TYPE_BINARY_STORAGE_DB G_TYPE_BINARY_STORAGE_FS
	 * @param int $max_size 限制的最大上传字节数，默认为NULL，不设置限制
	 * 
	 * @return obj binary_data
	 */
	public static function upload($upload_info, $index_info, $storage_type, $max_size = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		// 上传合法性检查
		if (!empty($max_size) && $upload_info['size'] > $max_size)
		{
			throw new Exception("上传文件大小超出限制：$max_size字节。");
		}
		if (empty($upload_info['size']))
		{
			throw new Exception("上传文件不能为空。");
		}
		
		// 数据预处理
		$data_save['content'] = $index_info;
		
		$data_save['content']['bd_id'] = scap_get_guid();
		$data_save['content']['bd_storage_type'] = $storage_type;
		$data_save['content']['bd_file_name'] = $upload_info['name'];
		$data_save['content']['bd_file_postfix'] = binary_data::get_filename_postfix($upload_info['name']);
		$data_save['content']['bd_file_size'] = $upload_info['size'];
		$data_save['content']['bd_file_type'] = $upload_info['type'];
		$data_save['content']['bd_upload_time'] = time();
		$data_save['content']['bd_upload_id'] = $GLOBALS['scap']['auth']['account_s_id'];
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		
		$ok = true;
		
		// 更新g_binary_data表
		if ($ok)
		{
			$ok = scap_entity::edit_row("g_binary_data", $data_save['content'], 'insert', '', 'module_g_00');
		}
		
		if ($ok)
		{
			switch($storage_type)
			{
				case G_TYPE_BINARY_STORAGE_DB:
					$data_save['content_bdc']['bd_id'] = $data_save['content']['bd_id'];
					
					$handle = fopen($upload_info['tmp_name'], "rb");
					if ($handle == false)
					{
						throw new Exception("上传文件到系统失败。");
					}
					$data_save['content_bdc']['bdc_file_content'] = fread($handle, filesize($upload_info['tmp_name']));
					fclose($handle);
					
					$ok = scap_entity::edit_row("g_binary_data_content", $data_save['content_bdc'], 'insert', '', 'module_g_00');
					break;
				case G_TYPE_BINARY_STORAGE_FS:
					// 判断指定的存储路径是否有效
					if (!file_exists(G_BINARY_FS_DIR))
					{
						throw new Exception("上传文件到系统失败：指定的存储路径无效。");
					}
					$ok = move_uploaded_file($upload_info['tmp_name'], G_BINARY_FS_DIR.$data_save['content']['bd_id']);
					break;
				default:
					$ok = false;
			}
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)// 执行成功标志
		{
			$result = new binary_data($data_save['content']['bd_id']);
		}
		else
		{
			throw new Exception("上传文件到系统失败。");
		}
		
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 解析文件中的后缀
	 * 
	 * @param $file_name 文件名称（可含路径）
	 * 
	 * @return string 文件后缀
	 */
	public static function get_filename_postfix($file_name)
	{
		$type = pathinfo($file_name);
		$type = strtolower($type["extension"]);
		return $type;
	}
	
	/**
	 * 更新文件索引信息
	 * 
	 * @author Liqt
	 * 
	 * @param array $upload_info POST上传的$_FILES信息
	 * @param array $index_info 上传的文件索引信息:bd_entity_id/bd_comment
	 * 
	 * @return bool
	 */
	public function update_index($upload_info, $index_info = array())
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		// 入库数据赋值
		$data_save['content'] = $index_info;
		
		$data_save['content']['bd_file_name'] = $upload_info['name'];
		$data_save['content']['bd_file_postfix'] = binary_data::get_filename_postfix($upload_info['name']);
		$data_save['content']['bd_file_size'] = $upload_info['size'];
		$data_save['content']['bd_file_type'] = $upload_info['type'];
		$data_save['content']['bd_upload_time'] = time();
		$data_save['content']['bd_upload_id'] = $GLOBALS['scap']['auth']['account_s_id'];
		
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		// 更新g_binary_data表
		if($ok)
		{
			$ok = scap_entity::edit_row("g_binary_data", $data_save['content'], 'update', "bd_id = '{$this->current_bd_id}'", 'module_g_00');
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)
		{
			$result = true;// 执行成功标志
		}
		else
		{
			throw new Exception("更新文件索引信息失败。");
		}
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 更新文件二进制内容
	 * 
	 * @param array $upload_info POST上传的$_FILES信息
	 * @param int $max_size 限制的最大上传字节数，默认为NULL，不设置限制
	 * 
	 * @return bool
	 */
	public function update_binary($upload_info, $max_size = NULL)
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		// 上传合法性检查
		if (!empty($max_size) && $upload_info['size'] > $max_size)
		{
			throw new Exception("上传文件大小超出限制：$max_size字节。");
		}
		if (empty($upload_info['size']))
		{
			throw new Exception("更新文件不能为空。");
		}
		
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		switch($this->index_info['bd_storage_type'])
		{
			case G_TYPE_BINARY_STORAGE_DB:
				$handle = fopen($upload_info['tmp_name'], "rb");
				if ($handle == false)
				{
					throw new Exception("更新文件到系统失败。");
				}
				$data_save['content_bdc']['bdc_file_content'] = fread($handle, filesize($upload_info['tmp_name']));
				fclose($handle);
				
				$ok = scap_entity::edit_row("g_binary_data_content", $data_save['content_bdc'], 'update', "bd_id = '{$this->current_bd_id}'", 'module_g_00');
				break;
			case G_TYPE_BINARY_STORAGE_FS:
				// 判断指定的存储路径是否有效
				if (!file_exists($this->current_fs_dir))
				{
					throw new Exception("更新文件到系统失败：指定的存储路径无效。");
				}
				
				// move_uploaded_file:如果目标文件已经存在，将会被覆盖。
				$ok = move_uploaded_file($upload_info['tmp_name'], "{$this->current_fs_dir}{$this->current_bd_id}");
				break;
			default:
				$ok = false;
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)// 执行成功标志
		{
			$result = true;
		}
		else
		{
			throw new Exception("更新文件到系统失败。");
		}
		
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 删除当前文件对应的二进制数据
	 * 
	 * @return bool
	 */
	public function remove_binary()
	{
		$data_in = array();// 输入数据
		$data_save = array();// 入库数据
		$result = false;// 返回结果
		
		$ok = true;
		
		switch($this->index_info['bd_storage_type'])
		{
			case G_TYPE_BINARY_STORAGE_DB:
				$ok = scap_entity::remove_rows("g_binary_data_content", "bd_id = '{$this->current_bd_id}'");
				break;
			case G_TYPE_BINARY_STORAGE_FS:
				// 判断原文件是否存在
				if (!file_exists("{$this->current_fs_dir}{$this->current_bd_id}"))
				{
//					throw new Exception("删除文件二进制数据失败：指定的文件不存在。");
				}
				else
				{
					$ok = unlink("{$this->current_fs_dir}{$this->current_bd_id}");
				}
				
				break;
			default:
				$ok = false;
		}
		
		
		if ($ok)// 执行成功标志
		{
			$result = true;
		}
		else
		{
			throw new Exception("删除文件二进制数据失败。");
		}
		
		return $result;
	}
	
	/**
	 * 删除当前文件所有信息
	 * 
	 * @return bool
	 */
	public function remove()
	{
		$data_in = array();// 输入数据
		$result = false;// 返回结果
		
		//--------数据库事物处理[start]--------
		scap_entity::db_begin_trans();// 事务开始
		$ok = true;
		
		if ($ok)
		{
			$ok = scap_entity::remove_rows("g_binary_data", "bd_id = '{$this->current_bd_id}'");
		}
		
		if ($ok)
		{
			$ok = $this->remove_binary();
		}
		
		$data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
		
		if ($data_flag['db_commit'] == 1)// 执行成功标志
		{
			$result = true;
		}
		else
		{
			throw new Exception("删除文件失败。");
		}
		
		//--------数据库事物处理[end]--------
		
		return $result;
	}
	
	/**
	 * 获取文件二进制数据
	 * 
	 * @author Liqt
	 * 
	 * @return 二进制数据
	 */
	public function get_binary()
	{
		$data_out = array();
		
		$ok = true;
		
		switch($this->index_info['bd_storage_type'])
		{
			case G_TYPE_BINARY_STORAGE_DB:
				$data_db['query']['sql'] = <<<SQL
SELECT * FROM g_binary_data_content  
WHERE (bd_id = '{$this->current_bd_id}')
SQL;
				$temp = scap_entity::query($data_db['query'], false);
				$data_out = $temp[0]['bdc_file_content'];
				break;
			case G_TYPE_BINARY_STORAGE_FS:
				$file_name = "{$this->current_fs_dir}{$this->current_bd_id}";
				// 判断原文件是否存在
				if (!file_exists($file_name))
				{
					throw new Exception("指定的文件不存在。");
				}
				
				$handle = fopen($file_name, "rb");
				if ($handle == false)
				{
					throw new Exception("指定的文件打开失败。");
				}
				$data_out = fread($handle, filesize($file_name));
				fclose($handle);
				
				break;
			default:
				$ok = false;
		}
		
		
		if ($ok && !empty($data_out))// 执行成功标志
		{
			$result = true;
		}
		else
		{
			throw new Exception("读取二进制数据失败。");
		}
		
		return $data_out;
	}
	
	/**
	 * 获取实体对象关联的二进制数据列表
	 * 
	 * @author Liqt
	 * 
	 * @param string $object_id 对象id
	 * @param string $where 附加查询条件(不含关键字where)
	 * 
	 * @return array 输出数组格式为array('obl_sn' => array(...), ...)
	 */
	public static function get_object_binary_list($object_id, $where = '')
	{
		//--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		$list = array();
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT obl.*, bd.* FROM g_object_binary_link obl 
LEFT JOIN g_binary_data bd ON (bd.bd_id = obl.bd_id) 
WHERE (obl_object_id = '{$object_id}' {$where})
SQL;
	
		$data_db['query']['id'] = 'obl_sn';
		$data_db['query']['order'] = 'obl_sn';
		$data_db['query']['sort'] = 'ASC';
		
		$list = scap_entity::query($data_db['query'], false);
		
		return $list;
	}
	
	/**
	 * 插入或更新实体与二进制数据关联信息
	 * 
	 * @author Liqt
	 * 
	 * @param string $object_id 对象ID
	 * @param array $data_in 更新数据内容,对应g_object_binary_link表
	 * @param int $sn 默认为NULL，如果指定，则覆盖原sn的数据
	 * 
	 * @return bool
	 */
	public static function insert_object_binary_link($object_id, $data_in, $sn = NULL)
	{
		//--------变量定义及声明[start]--------
		$data_save = array();
		//--------变量定义及声明[end]--------
		
		$data_save['content'] = $data_in;
		
		if (is_null($sn))
		{
			$data_save['type'] = 'INSERT';
			$data_save['content']['obl_object_id'] = $object_id;
			
			$max_sn = scap_db_get_max_value('g_object_binary_link', 'obl_sn', "obl_object_id = '{$object_id}'");
		
			if ($max_sn === false)
			{
				$str_info = sprintf('插入对象与二进制数据关联失败。错误信息：%s', scap_db_error_msg());
				throw new Exception($str_info);
			}
			$data_save['content']['obl_sn'] = $max_sn + 1;
		}
		else
		{
			$data_save['type'] = 'UPDATE';
			$data_save['where'] = "obl_object_id = '{$object_id}' AND obl_sn = {$sn}";
		}
		
		if (scap_entity::edit_row("g_object_binary_link", $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00') === false)
		{
			$str_info = sprintf('插入对象与二进制数据关联失败。错误信息：%s', scap_db_error_msg());
			throw new Exception($str_info);
		}
	
		return true;
	}
	
	/**
	 * 删除实体与二进制数据关联信息
	 * 
	 * @author Liqt
	 * 
	 * @param string $object_id 对象ID
	 * @param array $data_in 更新数据内容,对应g_object_binary_link表
	 * @param int $sn 默认为NULL，如果指定，则删除指定sn的数据，否则删除全部
	 * 
	 * @return bool
	 */
	public static function remove_object_binary_link($object_id, $sn = NULL)
	{
		if (is_null($sn))
		{
			$data_save['where'] = "obl_object_id = '{$object_id}'";
		}
		else
		{
			$data_save['where'] = "obl_object_id = '{$object_id}' AND obl_sn = {$sn}";
		}
		
		if (scap_entity::remove_rows("g_object_binary_link", $data_save['where']) === false)
		{
			$str_info = sprintf('删除对象与二进制数据关联失败。错误信息：%s', scap_db_error_msg());
			throw new Exception($str_info);
		}
	
		return true;
	}
	
	/**
	 * 删除实体与二进制数据的所有信息（关联信息、对应二进制数据）
	 * 
	 * @author Liqt
	 * 
	 * @param string $object_id 对象ID
	 * @param string $entity_id 对象所属实体id
	 * 
	 * @return bool
	 */
	public static function remove_object_all_binary($object_id, $entity_id)
	{
		$data_db['list'] = binary_data::get_object_binary_list($object_id);
		
		// 删除关联表数据
		binary_data::remove_object_binary_link($object_id);
		
		// 删除惯量二进制数据
		foreach($data_db['list'] as $k => $v)
		{
			$binary_data = new binary_data($v['bd_id']);
			$binary_data->remove();
		}
	
		return true;
	}
	
	/**
	 * 获取实体对象关联的二进制数据id
	 * 
	 * @author Liqt
	 * 
	 * @param string $object_id 对象ID
	 * @param int $sn 关联序号 
	 * @return id string
	 */
	public static function get_bdid_from_object($object_id, $sn)
	{
		//--------变量定义及声明[start]--------
		$bd_id = NULL;
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT bd_id FROM g_object_binary_link  
WHERE (obl_object_id = '{$object_id}' AND obl_sn = {$sn})
SQL;
		$temp = scap_entity::query($data_db['query'], false);
		$bd_id = $temp[0]['bd_id'];
		
		return $bd_id;
	}
}
?>