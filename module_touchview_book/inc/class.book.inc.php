<?php
/**
 * 书籍实体类实现文件
 * create time: 2011-12-15 06:39:39
 * @version $Id: class.book.inc.php 155 2012-10-26 06:08:18Z liqt $
 */

scap_load_module_define('module_touchview_basic', 'entity_id');

/**
 * book(书籍)实体类
 */
class book extends scap_g_entity
{
    /**
     * 设置继承类的实体相关信息
     */
    protected function set_current_entity_info()
    {
        $this->current_entity_id = ENTITY_ID_TOUCHVIEW_BOOK;
        $this->current_entity_name = '书籍';
    }
    
    /**
     * 检查当前的对象($this->current_object_id)是否存在
     * 
     * @return bool 如果存在返回true，否则返回false
     */
    protected function check_current_object_exist()
    {
        $result = false;
        
        if ($this->get_row_count('touchview_book', "b_id = '{$this->current_object_id}'") > 0)
        {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * 创建一个实体对象
     * 
     * @param array $content 对象内容数据
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
        unset($data_save['content']['b_sn']);
        if (empty($data_save['content']['b_id']))
        {
            $data_save['content']['b_id'] = scap_get_guid();// id内部生成
        }
        //--------入库数据赋值[end]--------
        
        //--------数据库事物处理[start]--------
        scap_entity::db_begin_trans();// 事务开始
        $ok = true;
        
        // 更新touchview_book表
        if($ok)
        {
            $ok = scap_entity::edit_row("touchview_book", $data_save['content'], 'insert', '', 'module_touchview_basic');
        }

        $data_flag['db_commit'] = scap_entity::db_commit_trans($ok);// 提交事务
        
        if ($data_flag['db_commit'] == 1)// 执行成功标志
        {
            $result = new book($data_save['content']['b_id']);
        }
        else
        {
            throw new Exception("创建书籍失败。");
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
SELECT * FROM touchview_book
WHERE (b_id = '{$this->current_object_id}')
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
        //--------变量定义及声明[end]--------
        
        //--------入库数据赋值[start]--------
        $data_save['content'] = $content;
        //--------入库数据赋值[end]--------
        
        //--------数据库事物处理[start]--------
        $this->db_begin_trans();// 事务开始
        $ok = true;
        
        // 更新touchview_book表
        if($ok)
        {
            $ok = $this->edit_row("touchview_book", $data_save['content'], 'update', "b_id = '{$this->current_object_id}'", 'module_touchview_basic');
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
        
        // 更新touchview_book表
        if($ok)
        {
            $ok = $this->remove_rows("touchview_book", "b_id = '{$this->current_object_id}'");
        }
        
        if($ok)
        {
            $ok = $this->remove_rows("touchview_page", "b_id = '{$this->current_object_id}'");
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
     * 通过id获取对应name
     * 
     * @param string $id
     * 
     * @return string 名称，如不存在则为NULL
     */
    public static function get_name_from_id($id)
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT b_name FROM touchview_book WHERE (b_id = '{$id}')";
    	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
    	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
    	if ($rs)
    	{
    		$rs->MoveFirst();
    		$rtn = $rs->fields['b_name'];
    	}
    
    	return $rtn;
    }
    
    /**
     * 获得所有book信息列表
     * - 按sort sn
     * - 名称升序
     * - 需上线状态
     * 
     * @return array book数组array(array('b_sn', 'b_name', 'b_id', 'b_description'), ...)
     */
    public static function get_book_list()
    {
        //--------变量定义及声明[start]--------
		$data_db	= array();	// 数据库相关数据
		$list = array();
		//--------变量定义及声明[end]--------
		
		$data_db['query']['sql'] = <<<SQL
SELECT * FROM touchview_book WHERE b_status = 1
SQL;
	
		$data_db['query']['id'] = '';
		$data_db['query']['order'] = 'b_sort_sn, b_name';
		$data_db['query']['sort'] = 'ASC';
		
		$list = scap_entity::query($data_db['query'], false);
		
		return $list;
    }
    
    /**
     * 获取书籍数量
     * 
     * @return int 书籍总数量
     */
    public static function get_book_count()
    {
        return scap_entity::get_row_count('touchview_book', '');
    }
    
    /**
     * 获取书籍下页面数量
     * 
     * @param string $id book id
     * 
     * @return int 页面数量
     */
    public static function get_book_page_count($id)
    {
        return scap_entity::get_row_count('touchview_page', "b_id='{$id}'");
    }
    
    
    
    /**
     * 获取默认展示书籍id
     * 按照sort sn取得第一个（上线状态下的）
     * 
     * @return book id
     */
    public static function get_default_book_id()
    {
        $rtn = NULL;
    	$data_db['sql'] = "SELECT b_id FROM touchview_book WHERE b_status = 1 ORDER BY b_sort_sn ASC LIMIT 1";
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
     * 设置页面的配置值
     * 
     * @param string $id book id
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
        
    	$book = new book($id);
    	$data_db = $book->read();
    	
    	$config_arr = json_decode($data_db['b_config'], true);
    	unset($config_arr[$key]);
    	$config_arr[$key] = $value;
    	
    	$data_save['b_config'] = json_encode($config_arr);
    	
    	$result = $book->update($data_save);
    	
    	return $result;
    }
    
    /**
     * 获取页面的配置值
     * 
     * @param string $id book id
     * @param string $key 配置项名称
     * 
     * @return string 配置项值
     */
    public static function get_config_value($id, $key)
    {
        $value = '';
        $data_db = array();
        $config_arr = array();
        
        $book = new book($id);
        $data_db = $book->read();
        $config_arr = json_decode($data_db['b_config'], true);
        $value = $config_arr[$key];
        
        return $value;
    }
    
    /**
     * 设置book的模板值
     * 
     * @param string $id book id
     * @param string $value
     * 
     * return bool
     */
    public static function set_book_tpl($id, $value)
    {
        return book::set_config_value($id, 'tpl', $value);
    }
    
    /**
     * 获取book的模板值
     * 如果模板不存在，则使用default
     * 
     * @param string $id book id
     * 
     * @return string 模板值
     */
    public static function get_book_tpl($id)
    {
        $result = book::get_config_value($id, 'tpl');
        $list_tpl = book::get_tpl_list();
        
        if (empty($result) || !in_array($result, $list_tpl))
        {
            $result = 'default';
        }
        return $result;
    }
    
    /**
     * 获取book支持的模板列表
     * 将default放在首位
     * 
     * @return array('default', 'tpl1', 'tpl2', ...) 模板名称列表
     */
    public static function get_tpl_list()
    {
        $data = book::directory_to_array(SCAP_PATH_ROOT.'module_touchview_book/templates/default/book_templates');
        sort($data);
        array_unshift($data, 'default');
        $data = array_unique($data);
        return $data;
    }
    
    /**
     * 获取书籍模板的封面图片url
     * 
     * @param string $name 模板名称
     */
    public static function get_url_tpl_front_cover_img($name)
    {
        return $GLOBALS['scap']['info']['site_url']."/module_touchview_book/templates/default/book_templates/{$name}/front-cover.jpg";
    }
    
    /**
     * 将目录列表转换为数组
     * 
     * @param string $directory 读取目录
     * @param bool $recursive 是否递归目录，默认false
     * 
     * @return array 目录下所有文件夹名称
     */
    public static function directory_to_array($directory, $recursive = false)
    {
        $array_items = array();
        if ($handle = opendir($directory))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (!preg_match("/^(\.).*$/", $file))// 以.开头的文件均忽略
                {
                    if (is_dir($directory. "/" . $file))
                    {
                        if($recursive)
                        {
                            $array_items = array_merge($array_items, book::directory_to_array($directory. "/" . $file, $recursive));
                        }
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                    else
                    {
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }
    
    /**
     * 设置自动翻书标志
     * 
     * @param int $value
     * 
     * @return bool
     */
    public static function set_config_book_auto_flip_switch($value)
    {
        $value = (intval($value) == 0) ? '0' : '1';
        return scap_set_config_value('module_touchview_book', 'auto_flip_switch', $value);
    }
    
    /**
     * 获取自动翻书标志
     * 
     * @return int
     */
    public static function get_config_book_auto_flip_switch()
    {
        $result = scap_get_config_value('module_touchview_book', 'auto_flip_switch');
        $result = (intval($result) == 0) ? 0 : 1;
        return $result;
    }
    
    /**
     * 设置自动翻书等待时长
     * 
     * @param int $value
     * 
     * @return bool
     */
    public static function set_config_book_auto_flip_waiting($value)
    {
        return scap_set_config_value('module_touchview_book', 'auto_flip_waiting', intval($value));
    }
    
    /**
     * 获取自动翻书等待时长
     * 
     * @return int
     */
    public static function get_config_book_auto_flip_waiting()
    {
        $result = scap_get_config_value('module_touchview_book', 'auto_flip_waiting');
        $result = empty($result) ? 300 : $result;// 默认等待300秒
        return $result;
    }
    
    /**
     * 设置书籍最大数量
     * 
     * @param int $value
     * 
     * @return bool
     */
    public static function set_config_book_max_book_count($value)
    {
        return scap_set_config_value('module_touchview_book', 'max_book_count', intval($value));
    }
    
    /**
     * 获取书籍最大数量
     * 
     * @return int
     */
    public static function get_config_book_max_book_count()
    {
        $result = scap_get_config_value('module_touchview_book', 'max_book_count');
        $result = empty($result) ? 5 : $result;// 默认最大数量
        return $result;
    }
    
    /**
     * 设置每本书籍页数最大数量
     * 
     * @param int $value
     * 
     * @return bool
     */
    public static function set_config_book_max_page_count($value)
    {
        return scap_set_config_value('module_touchview_book', 'max_page_count', intval($value));
    }
    
    /**
     * 获取每本书籍页数最大数量
     * 
     * @return int
     */
    public static function get_config_book_max_page_count()
    {
        $result = scap_get_config_value('module_touchview_book', 'max_page_count');
        $result = empty($result) ? 10 : $result;// 默认最大数量
        return $result;
    }
    
    /**
     * 生成指定book的内容数据文件
     * 
     * @param string $id book id
     * 
     * @return string 文件URL路径
     */
    public static function generate_book_data_file($id)
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_save = array();// 入库数据
        $data_def   = array();  // 相关定义数据
        $result = '';// 返回结果
        //--------变量定义及声明[end]--------
        
        $data_def['file_name'] = SCAP_PATH_ROOT."module_touchview_book/templates/cache/{$id}.book";
        $data_def['file_zip_name'] = SCAP_PATH_ROOT."module_touchview_book/templates/cache/{$id}.book.zip";
        $data_def['file_zip_url'] = $GLOBALS['scap']['info']['site_url']."/module_touchview_book/templates/cache/{$id}.book.zip";
        
        // 读取touchview_book
        $book = new book($id);
        $data_db['book'] = $book->read();
        
        // 读取touchview_page
        $data_db['page'] = page::get_book_page_list($id);
        
        $data_save = json_encode($data_db);
        
        // 存储到文件
        file_put_contents($data_def['file_name'], $data_save);
        
        // 将文件打包
        touchview_zip($data_def['file_name'], $data_def['file_zip_name']);
        
        // 删除文件夹
        touchview_rmdir($data_def['file_name']);
        
        return $data_def['file_zip_url'];
    }
    
    /**
     * 生成指定book使用到的媒体文件
     * 
     * @param string $id book id
     * 
     * @return string 文件URL路径
     */
    public static function generate_book_media_file($id)
    {
        require_once SCAP_PATH_ROOT.'module_touchview_basic/inc/third/simple_html_dom.php';
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_save = array();// 入库数据
        $data_def   = array();  // 相关定义数据
        $result = '';// 返回结果
        //--------变量定义及声明[end]--------
        
        $data_def['dir_name'] = SCAP_PATH_ROOT."module_touchview_book/templates/cache/{$id}/";
        $data_def['file_zip_name'] = SCAP_PATH_ROOT."module_touchview_book/templates/cache/{$id}.media.zip";
        $data_def['file_zip_url'] = $GLOBALS['scap']['info']['site_url']."/module_touchview_book/templates/cache/{$id}.media.zip";
        $data_def['dir_media'] = SCAP_PATH_ROOT."media/";// 书籍图片存储的文件夹位置
        
        // 先删除同名文件夹
        touchview_rmdir($data_def['dir_name']);
        
        // 创建放置book所用图片的目录
        mkdir($data_def['dir_name']);
        
        // 根据book所有的page内容，提出image文件信息
        $data_db['page'] = page::get_book_page_list($id);
        foreach($data_db['page'] as $v)
        {
            if (empty($v['p_content']))
            {
                continue;
            }
            $temp_dom = str_get_html($v['p_content']);
            foreach($temp_dom->find('img') as $v2)
            {
                // 获取文件名称
                $temp_name = basename($v2->src);
                // 复制文件到目录
                @copy($data_def['dir_media'].$temp_name, $data_def['dir_name'].$temp_name);
            }
        }
        
        // 将目录打包
        touchview_zip($data_def['dir_name'], $data_def['file_zip_name']);
        
        // 删除文件夹
        touchview_rmdir($data_def['dir_name']);
        
        return $data_def['file_zip_url'];
    }
    
    /**
     * 生成指定book所属的媒体文件
     * 
     * @param string $id book id
     * 
     * @return string 文件URL路径
     */
    public static function zip_book_media_file($id)
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_save = array();// 入库数据
        $data_def   = array();  // 相关定义数据
        $result = '';// 返回结果
        //--------变量定义及声明[end]--------
        
        $data_def['file_zip_name'] = SCAP_PATH_ROOT."module_touchview_book/templates/cache/{$id}.media.zip";
        $data_def['file_zip_url'] = $GLOBALS['scap']['info']['site_url']."/module_touchview_book/templates/cache/{$id}.media.zip";
        $data_def['dir_media'] = SCAP_PATH_ROOT."media/{$id}";// 书籍图片存储的文件夹位置
        
        @unlink($data_def['file_zip_name']);
        touchview_zip($data_def['dir_media'], $data_def['file_zip_name']);
        
        return $data_def['file_zip_url'];
    }
}
?>