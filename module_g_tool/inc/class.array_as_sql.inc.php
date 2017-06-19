<?php
/**
 * 数组作为sql查询类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.array_as_sql.inc.php 503 2013-05-13 08:18:22Z liqt $
 * @creator liqt @ 2013-05-12 16:27:54 by caster0.0.3
 */
namespace scap\module\g_tool;

/**
 * 将二维数组作作为像SQL语法样的查询类
 * - 该接口使用方法类似scap_entity:query()
 * - 改编自jqGrid的部分代码(A PHP class to work with jqGrid jQuery plugin.http://www.trirand.net/)
 * - 参考源码：
 *     http://code.google.com/p/hat-framework/source/browse/recursos/grid/jsplugins/jqgrid2/
 *     https://www.assembla.com/code/logzilla/subversion-2/nodes/277/trunk/html/includes/grid/php
 */
class array_as_sql
{
    public static  $acnt = 0;
    
	/**
	 *
	 * Stores the connection passed to the constructor
	 * @var resourse
	 */
	protected $pdo;
	/**
	 * This is detected automatically from the passed connection. Used to
	 * construct the appropriate pagging for the database and in case of
	 * PostgreSQL to set case insensitive search
	 * @var string
	 */
	protected $dbtype;
	/**
	 *
	 * Holds the modified select command used into grid
	 * @var string
	 */
	protected $select="";

	/**
	 * Last error message from the server
	 * @var string
	 */
	public $errorMessage = '';

	/**
	 * In case if no table is set, this holds the sql command for
	 * retrieving the data from the db to the grid
	 * @var string
	 */
	public $SelectCommand = "";

	/**
	 *
	 * Constructor
	 * @param resource -  $db the database connection passed to the constructor
	 */
	public function __construct()
	{
		$this->pdo = new array_implement();
		$this->dbtype = 'array';
	}
	
	/**
	 * 将制定数组注册为类SQL表信息
	 * 
	 * @param string $name 在sql中被识别的表名称
	 * @param array $content 表数据内容(二维数组)，如
	 * array(
                array('code' => '101', 'name' => '中文系'), 
                array('code' => '102', 'name' => '英文系'), 
                array('code' => '104', 'name' => '物理系'), 
                array('code' => '103', 'name' => '数学系'), 
        );
     * 
     * @return void
	 */
	public function regist_table($name, $content)
	{
	    $this->pdo->regist_table($name, $content);
	}
	
	/**
	 * Prepares a $sqlElement and binds a parameters $params
	 * Return prepared sql statement
	 * @param string $sqlElement sql to be prepared
	 * @return string
	 */
	protected function parseSql($sqlElement, $bind=true)
	{
		$sql = self::prepare($this->pdo,$sqlElement, $bind);
		return $sql;
	}
	/**
	 * Executes a prepared sql statement. Also if limit is set to true is used
	 * to return limited set of records
	 * Return true on success
	 * @param string $sqlId - sql to pe executed
	 * @param resource $sql - pointer to the constructed sql
	 * @param boolean $limit - if set to true we use a pagging mechanizm
	 * @param integer $nrows - number of rows to return
	 * @param integer $offset - the offset from which the nrows should be returned
	 * @return boolean
	 */
	protected function execute($sqlId, &$sql, $limit=false,$nrows=-1,$offset=-1)
	{
		$this->select= $sqlId;
		if($limit) {
			$this->select = self::limit($this->select, $this->dbtype, $nrows,$offset);
		}
		try {
		    $sql = $this->parseSql($this->select);
			$ret = true;
			if($sql) $ret = $sql;
			if(!$ret) {
				$this->errorMessage = 'Array Error.';
				throw new Exception($this->errorMessage);
			}
		} catch (Exception $e) {
			if(!$this->errorMessage) $this->errorMessage = $e->getMessage();
			echo $this->errorMessage;
			return false;
	}
		return true;
	}


	/**
	 * Returns object which holds the total records in the query and optionally
	 * the sum of the records determined in sumcols
	 * @param string $sql - string to be parsed
	 * The array should be associative where the index corresponds to the names
	 * of colModel in the grid, and the value correspond to the actual name in
	 * the query
	 * @return object
	 */
	protected function _getcount($sql)
	{
		$qryRecs = new \stdClass();
		$qryRecs->COUNT = 0;
		$s ='';
		if (preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) ||
			preg_match('/\s+GROUP\s+BY\s+/is',$sql) ||
			preg_match('/\s+UNION\s+/is',$sql) ||
			substr_count(strtoupper($sql), 'SELECT') > 1 ||
			substr_count(strtoupper($sql), 'FROM') > 1 ) {
				$rewritesql = "SELECT COUNT(*) AS COUNT ".$s." FROM ($sql) gridalias";
		} else {
			// now replace SELECT ... FROM with SELECT COUNT(*) FROM
			$rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) AS COUNT '.$s.' FROM ',$sql);
		}
		if (isset($rewritesql) && $rewritesql != $sql) {
			if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr))
			{
			    $rewritesql .= $limitarr[0];
			}
			$qryRecs = $this->queryForObject($rewritesql, false);
			if ($qryRecs) return $qryRecs;
		}
		return $qryRecs;
	}

	/**
	 * Return the object from the query
	 * @param string $sqlId the sql to be queried
	 * @param boolean $fetchAll - if set to true fetch all records
	 * @return object
	 */
	protected function queryForObject($sqlId, $fetchAll=false)
	{
		$sql = null;
		$ret = $this->execute($sqlId, $sql, false);
		if ($ret) {
			$ret = self::fetch_object($sql,$fetchAll,$this->pdo);
			self::closeCursor($sql);
		}
		return $ret;
	}

	/**
	 * Bulid the sql based on $readFromXML, $SelectCommand and $table variables
	 * The logic is: first we look if readFromXML is set to true, then we look for
	 * SelectCommand and at end if none of these we use the table varable
	 * Return string or false if the sql found
	 * @return mixed
	 */
	protected function _setSQL()
	{
		$sqlId = false;
		$sqlId = $this->SelectCommand;
		
		return $sqlId;
	}

	/**
	 * Will select, getting rows from $offset (1-based), for $nrows.
	 * This simulates the MySQL "select * from table limit $offset,$nrows" , and
	 * the PostgreSQL "select * from table limit $nrows offset $offset". Note that
	 * MySQL and PostgreSQL parameter ordering is the opposite of the other.
	 * eg. Also supports Microsoft SQL Server
	 * SelectLimit('select * from table',3); will return rows 1 to 3 (1-based)
	 * SelectLimit('select * from table',3,2); will return rows 3 to 5 (1-based)
	 * Return object containing the limited record set
	 * @param string $limsql - optional sql clause
	 * @param integer is the number of rows to get
	 * @param integer is the row to start calculations from (1-based)
	 * @param array	array of bind variables
	 * @return object
	 */
	protected function select_limit($limsql='', $nrows=-1, $offset=-1)
	{
		$sql = null;
		$sqlId = strlen($limsql)>0 ? $limsql : $this->_setSQL();
		if(!$sqlId) return false;
		$ret = $this->execute($sqlId, $sql, true,$nrows,$offset);
		if ($ret === true) {
			$ret = self::fetch_object($sql, true, $this->pdo);
			self::closeCursor($sql);
			return $ret;
		} else
			return $ret;
	}
	
	/**
	 * 按条件查询数据集合
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
	public function query(&$query, $flag_split = true)
	{
	    $data_rtn = array();// 返回的数据数组
		$data_in = array();
		
		$data_in['id'] = $query['id'];
		$data_in['default_order'] = $query['default_order'];
		$data_in['sql_order'] = '';
		$data_in['sql'] = $query['sql'];
		$data_in['start'] = intval($query['start']);
		$data_in['steps'] = (intval($query['steps']) <= 0) ? 20 : intval($query['steps']);
		
		$query['total'] = 0;
		$query['pages'] = 0;
		
		if(!$data_in['sql']) return $data_rtn;
		
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
    	$qryData = $this->_getcount($data_in['sql']);
    	
		if(is_object($qryData))
		{
			if(!isset($qryData->count)) $qryData->count = null;
			if(!isset($qryData->COUNT)) $qryData->COUNT = null;
			$query['total'] = $qryData->COUNT ? $qryData->COUNT : ($qryData->count ?  $qryData->count : 0);
		}
		else
		{
		    $query['total'] = isset($qryData['COUNT']) ? $qryData['COUNT'] : 0;
		}
		
		if( $query['total'] > 0 )
		{
			$query['pages'] = ceil($query['total'] / $data_in['steps']);
		}
		
		if ($data_in['start'] > $query['pages'] || $data_in['start'] < 0)
		{
		    $query['start'] = $data_in['start'] = 0;
		}
		
		// 仅在分页查询时才加入order属性，可以提高查询总数的效率
		$data_in['sql'] .= $data_in['sql_order'];
		
		$ret = $this->execute($data_in['sql'], $sql, $flag_split ,$data_in['steps'], $data_in['start']*$data_in['steps']);
		if ($ret && is_array($sql))
		{
			$i = 0;
			foreach($sql as $v)
			{
			    if (!empty($data_in['id']))// 以指定的id键值对应的数据作为返回集合的键值
				{
					$data_rtn[$v[$data_in['id']]] = $v;
				}
				else
				{
					$data_rtn[$i ++] = $v;
				}
			}
		}
		
		return $data_rtn;
	}

    protected static function prepare ($conn, $sqlElement, $bind=true)
	{
		if($conn && strlen($sqlElement)>0) {
			$sql =  $conn->query($sqlElement);
			return $sql;
		}
		return false;
	}
	
    protected static function fetch_object( $psql, $fetchall, $conn=null )
	{
		if($psql) {
			$ret = array();
			if(!$fetchall)
			{
				if(is_array($psql) && count($psql)==1 )
				{
					return (object)$psql;
				}
				if( isset($psql[self::$acnt]) ) {
					return  (object)$psql[self::$acnt];
				}
				self::$acnt++;

			} else {
				foreach ($psql as $akey => $aval)
				{
					$ret[] = (object)$psql[$akey];
				}
				return $ret;
			}
		}
		return false;
	}
	
    protected static function limit($sqlId, $dbtype, $nrows=-1,$offset=-1)
	{
		$psql = $sqlId;
		$offsetStr = ($offset >= 0) ? " OFFSET ".$offset : '';
		$limitStr  = ($nrows >= 0)  ? " LIMIT ".$nrows : '';
		$psql .= "$limitStr$offsetStr";
		return $psql;
	}
    
    protected static function closeCursor($sql)
	{
		self::$acnt = 0;
		return true;
	}
}

/*
 * Parameters available :
 * SELECT, DISTINCT, FROM, WHERE, ORDER BY, LIMIT, OFFSET
 *
 * Operators available :
 * =, <, >, <=, >=, <>, !=, IS, IS IN, IS NOT, IS NOT IN, LIKE, ILIKE, NOT LIKE, NOT ILIKE
 *
 * Functions available in WHERE parameters :
 * LOWER(var), UPPER(var), TRIM(var)
 */
class array_implement
{
	/* Init
	-------------------------------------------- */
	private $query				= FALSE;
	private $parse_query		= FALSE;
	private $parse_query_lower	= FALSE;
	private $parse_select		= FALSE;
	private $parse_select_as	= FALSE;
	private $parse_from			= FALSE;
	private $parse_from_as		= FALSE;
	private $parse_where		= FALSE;
	private $distinct_query		= FALSE;
	private $count_query		= FALSE;
	private $tables				= array();
	private $response			= array();
	private $regist_tables      = array();
	
    /**
	 * 将制定数组注册为类SQL表信息
	 * 
	 * @param string $name 在sql中被识别的表名称
	 * @param array $content 表数据内容(二维数组)，如
	 * array(
                array('code' => '101', 'name' => '中文系'), 
                array('code' => '102', 'name' => '英文系'), 
                array('code' => '104', 'name' => '物理系'), 
                array('code' => '103', 'name' => '数学系'), 
        );
	 */
	public function regist_table($name, $content)
	{
	    $this->regist_tables[$name] = $content;
	}

	/* Query function
	-------------------------------------------- */
	public function query($query)
	{
		$this->destroy();
		$this->query = $query;
		$this->parse_query();
		$this->parse_select();
		$this->parse_select_as();
		$this->parse_from();
		$this->parse_from_as();
		$this->parse_order();
		$this->parse_where();
		$this->exec_query();
		//$this->parse_order();

		return $this->response;
	}
	public function execute($query)
	{
		return $this->query($query);
	}
	/* Destroy current values
	-------------------------------------------- */
	private function destroy()
	{
		$this->query				= FALSE;
		$this->parse_query			= FALSE;
		$this->parse_query_lower	= FALSE;
		$this->parse_select			= FALSE;
		$this->parse_select_as		= FALSE;
		$this->parse_from			= FALSE;
		$this->parse_from_as		= FALSE;
		$this->parse_where			= FALSE;
		$this->distinct_query		= FALSE;
		$this->count_query			= FALSE;
		$this->tables				= array();
		$this->response				= array();
	}

	/* Parse SQL query
	-------------------------------------------- */
	private function parse_query()
	{
		$this->parse_query 			= preg_replace('#ORDER(\s){2,}BY(\s+)(.*)(\s+)(ASC|DESC)#i', 'ORDER BY \\3 \\5', $this->query);
		$this->parse_query 			= preg_split('#(SELECT|DISTINCT|FROM|JOIN|WHERE|ORDER(\s+)BY|LIMIT|OFFSET|COUNT)+#i', $this->parse_query, -1, PREG_SPLIT_DELIM_CAPTURE);
		$this->parse_query			= array_map('trim', $this->parse_query);
		$this->parse_query_lower	= array_map('strtolower', $this->parse_query);
	}

	/* Parse SQL select parameters
	-------------------------------------------- */
	private function parse_select()
	{
		$key = array_search("distinct", $this->parse_query_lower);

		if ($key === FALSE) {
			$key = array_search("select", $this->parse_query_lower);
			//$key1 = array_search("count", $this->parse_query_lower);
			if( array_search("count", $this->parse_query_lower) ) {
				$this->count_query = TRUE;
			}
		} else {
			$this->distinct_query = TRUE;
		}
		$string	= $this->parse_query[$key+1];

		$arrays	= preg_split('#((\s)*,(\s)*)#i', $string, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($arrays as $array)
			$this->parse_select[] = $array;
	}

	/* Parse again SQL select parameters with as keyword
	-------------------------------------------- */
	private function parse_select_as()
	{
		if(empty($this->parse_select)) return;
		foreach ($this->parse_select as $select)
		{
			if (preg_match('/ AS /i', $select))
			{
				$arrays	= preg_split('#((\s)+AS(\s)+)#i', $select, -1, PREG_SPLIT_NO_EMPTY);
				$this->parse_select_as[$arrays[1]] = $arrays[0];
			}
			else
			{
				$this->parse_select_as[$select] = $select;
			}
		}
	}

	/* Parse SQL from parameters
	-------------------------------------------- */
	private function parse_from()
	{
		$key	= array_search("from", $this->parse_query_lower);
		$string	= $this->parse_query[$key+1];
		$arrays	= preg_split('#((\s)*,(\s)*)#i', $string, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($arrays as $array)
			$this->parse_from[] = $array;
	}

	/* Parse again SQL from parameters with as keyword
	-------------------------------------------- */
	private function parse_from_as()
	{
		foreach ($this->parse_from as $from)
		{
			if (preg_match('/\bAS\b/i', $from))
			{
				$arrays	= preg_split('#((\s)+AS(\s)+)#i', $from, -1, PREG_SPLIT_NO_EMPTY);

				$table = $arrays[0];
				$this->parse_from_as[$arrays[1]] = $table;
				$this->tables[$arrays[1]] = $this->regist_tables[$table];
			}
			else
			{
				$table = $from;
				$this->parse_from_as[$from] = $table;
				$this->tables[$from] = $this->regist_tables[$table];
			}

		}
	}

	/* Parse SQL where parameters
	-------------------------------------------- */
	private function parse_where()
	{
		$key	= array_search("where", $this->parse_query_lower);

		if ($key == FALSE)
			return $this->parse_where = "return TRUE;";

		$string	= $this->parse_query[$key+1];
		if (trim($string) == '')
			return $this->parse_where =  "return TRUE;";

		/* SQL Functions
		-------------------------------------------- */
		$patterns[]		= '#LOWER\((.*)\)#ie';
		$patterns[]		= '#UPPER\((.*)\)#ie';
		$patterns[]		= '#TRIM\((.*)\)#ie';

		$replacements[]	= "'strtolower(\\1)'";
		$replacements[]	= "'strtoupper(\\1)'";
		$replacements[]	= "'trim(\\1)'";

		/* Basics SQL operators
		-------------------------------------------- */
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(=|IS)(\s)+([[:digit:]]+)(\s)*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(=|IS)(\s)+(\'|\")(.*)(\'|\")(\s)*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(>|<)(\s)+([[:digit:]]+)(\s)*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(>|<)(\s)+(\'|\")(.*)(\'|\")*#ie';

		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(<=|>=)(\s)+([[:digit:]]+)(\s)*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(<=|>=)(\s)+(\'|\")(.*)(\'|\")*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(<>|IS NOT|!=)(\s)+([[:digit:]]+)(\s)*#ie';
		$patterns[]		= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(<>|IS NOT|!=)(\s)+(\'|\")(.*)(\'|\")(\s)*#ie';
		$patterns[] 	= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(IS)?(NOT IN)(\s)+\((.*)\)#ie';
		$patterns[] 	= '#(([a-zA-Z0-9\._]+)(\())?([a-zA-Z0-9\._]+)(\))?(\s)+(IS)?(IN)(\s)+\((.*)\)#ie';

		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == \\9 '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == \"\\10\" '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \\9 '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \'\\10\ '";

		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \\9 '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 \\7 \'\\10\ '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != \\9 '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != \"\\10\" '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 != ('.\$this->parse_in(\"\\10\").') '";
		$replacements[]	= "'\\1'.\$this->parse_where_key(\"\\4\").'\\5 == ('.\$this->parse_in(\"\\10\").') '";

		/* match SQL operators
		-------------------------------------------- */
		//$ereg = array('%' => '(.*)', '_' => '(.)');

		$patterns[] 	= '#([a-zA-Z0-9\._]+)(\s)+NOT LIKE(\s)*(\'|\")(.*)(\'|\")#ie';
		//$patterns[] 	= '#([a-zA-Z0-9\._]+)(\s)+NOT ILIKE(\s)*(\'|\")(.*)(\'|\")#ie';
		$patterns[] 	= '#([a-zA-Z0-9\._]+)(\s)+LIKE(\s)*(\'|\")(.*)(\'|\")#ie';
		//$patterns[] 	= '#([a-zA-Z0-9\._]+)(\s)+ILIKE(\s)*(\'|\")(.*)(\'|\")#ie';

		$replacements[]	= "'!\$this->isLike(\"\\5\", '.\$this->parse_where_key(\"\\1\").')'";
		//$replacements[]	= "'!\$this->isLike(\"\\5\", '.\$this->parse_where_key(\"\\1\").')'";
		$replacements[]	= "'\$this->isLike(\"\\5\", '.\$this->parse_where_key(\"\\1\").')'";
		//$replacements[]	= "'\$this->isLike(\"\\5\", '.\$this->parse_where_key(\"\\1\").')'";

		$wdata = explode(" AND ", $string);
		foreach($wdata as $k=>$wh) {
			$wdataor = explode(" OR ", $wh);

			foreach($wdataor as $kk=>$whor) {
				$wdataor[$kk] = stripslashes(trim(preg_replace($patterns, $replacements, $whor)));
	}
			$wdata[$k] = implode(" OR ", $wdataor);
		}
		$this->parse_where = "return ".implode(" AND ", $wdata).";";
		//"return ".stripslashes(trim(preg_replace($patterns, $replacements, $string))).";";
	}

	/*
	-------------------------------------------- */
	private function parse_where_key($key)
	{
		if ( preg_match('/\./', $key) )
		{
			list($table, $col) = explode('.', $key);
			//return '$row[$this->parse_select_as['.$col.']]';
			return '$row[\''.$col.'\']';
		}
		else
		{
			//return '$row[$this->parse_select_as['.$key.']]';
			return '$row[\''.$key.'\']';
		}
	}

	/* Format IN parameters for PHP
	-------------------------------------------- */
	private function parse_in($string)
	{
		$array	= explode(',', $string);
		$array	= array_map('trim', $array);

		return implode(' || ', $array);
	}
	private function isLike($needle,$haystack) {
		$regex = '#^'.preg_quote($needle, '#').'$#i';
		//add support for wildcards
		$regex = str_replace(array('%', '_'), array('.*?', '.?'), $regex);
		return 0 != preg_match($regex, $haystack);
	}

	/* Execute query
	-------------------------------------------- */
	private function exec_query()
	{
		$klimit		= array_search("limit", $this->parse_query_lower);
		$koffset	= array_search("offset", $this->parse_query_lower);
		if ($klimit !== FALSE)
			$limit	= (int) $this->parse_query[$klimit+1];

		if ($koffset !== FALSE)
			$offset	= (int) $this->parse_query[$koffset+1];
		$irow		= 0;
		$rcount = 0;
		$distinct	= array();
		foreach ($this->tables as $table)
		{
			foreach ($table as $row)
			{
				// Offset
				//if ($koffset !== FALSE && $irow < $offset)
				//{
					//$irow++;
					//continue;
				//}

				if (eval($this->parse_where))
				{
					if ($koffset !== FALSE && $irow < $offset)
					{
						$irow++;
						continue;
					}

					if ($this->parse_select[0] == '*')
					{
						if($this->count_query == FALSE) {
							foreach (array_keys($row) as $key)
								$temp[$key] = $row[$key];

							if ($this->distinct_query && in_array($temp, $distinct))
								continue;
							else
								$this->response[] = $temp;

							$distinct[] = $temp;
						}
						$rcount++;
					}
					else
					{
						if($this->count_query == FALSE) {
							foreach ($this->parse_select_as as $key => $value)
								$temp[$key] = $row[$value];

							if ($this->distinct_query && in_array($temp, $distinct))
								continue;
							else
								$this->response[] = $temp;

							$distinct[] = $temp;
						}
						$rcount++;
					}
					// Limit
					if ($klimit !== FALSE && count($this->response) == $limit)
						break;
					$irow++;
				}
				if($this->count_query==TRUE) {
					$this->response = array("COUNT"=>$rcount);
				}

			}
		}
	}

	/* Parse SQL order by parameters
	-------------------------------------------- */
	private function parse_order()
	{
		$key	= array_search("order by", $this->parse_query_lower);

		if ($key === FALSE)
			return;

		$string	= $this->parse_query[$key+2];
		$arrays	= explode(',', $string);
		if (!is_array($arrays))
			$arrays[] = $string;
		$arrays	= array_map('trim', $arrays);
		$akey = array_keys($this->tables);
		$multisort	= "array_multisort(";

		foreach ($arrays as $array)
		{
			if(strpos($array, " ASC") === false ) {
				if(strpos($array, " DESC") === false ) {
					$array .= " ASC";
		}
			}
			list($col, $sort) = preg_split('#((\s)+)#', $array, -1, PREG_SPLIT_NO_EMPTY);
			$multisort .= "\$this->split_array(\$this->tables['$akey[0]'], '$col'), SORT_".strtoupper($sort).", SORT_REGULAR, ";
		}
		$multisort	.= "\$this->tables['$akey[0]']);";
		eval($multisort);
	}

	/* Return a column of an array
	-------------------------------------------- */
	private function split_array($input_array, $column)
	{
		$output_array	= array();
		foreach ($input_array as $key => $value)
			$output_array[] = $value[$column];

		return $output_array;
	}

}
?>