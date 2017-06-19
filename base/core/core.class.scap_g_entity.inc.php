<?php
/**
 * description: 使用g模块设计的实体抽象类
 * create time: 2009-7-29-10:59:38
 * @version $Id: core.class.scap_g_entity.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 使用g模块设计的实体抽象类
 * 
 * 由于PHP5暂未支持多重继承，故扩展自scap_entity
 * 
 * @author Liqt
 *
 */
abstract class scap_g_entity extends scap_entity
{
	/**
	 * 当前对象id
	 * @var string
	 */
	protected $current_object_id = '';
	
	/**
	 * 当前实体类型ID
	 * @var string
	 */
	protected $current_entity_id = '';
	
	/**
	 * 当前的实体名称(用于信息反馈)
	 * @var string
	 */
	protected $current_entity_name = '';
	
	/**
	 * 需关联的联系人(contact)实体ID
	 * @var string
	 */
	protected $contact_entity_id = '';
	
	/**
	 * 需关联的机构(org)实体ID
	 * @var string
	 */
	protected $org_entity_id = '';
	
	/**
	 * 需关联的OU实体ID
	 * @var string
	 */
	protected $ou_entity_id = '';
	
	/**
	 * 关联附加要素实例
	 * @var class g_ae
	 */
	protected $instance_ae = NULL;
	
	/**
	 * 关联日志实例
	 * @var class g_app_log
	 */
	protected $instance_log = NULL;
	
	/**
	 * 关联对象关联实例
	 * @var class g_object_link
	 */
	protected $instance_object_link = NULL;
	
	/**
	 * 关联对象关联实例
	 * @var class g_object_relation
	 */
	protected $instance_object_relation = NULL;
	
	/**
	 * 关联时间节点实例
	 * @var class g_time_node
	 */
	protected $instance_time_node = NULL;
	
	/**
	 * 关联状态实例
	 * @var class g_status
	 */
	protected $instance_status = NULL;
	
	/**
	 * 关联类别实例
	 * @var class g_category_link
	 */
	protected $instance_category_link = NULL;
	
	/**
	 * 关联联系人(contact)实例
	 * @var class g_party_link
	 */
	protected $instance_contact_link = NULL;
	
	/**
	 * 关联机构(org)实例
	 * @var class g_party_link
	 */
	protected $instance_org_link = NULL;
	
	/**
	 * 关联ou实例
	 * @var class g_party_link
	 */
	protected $instance_ou_link = NULL;
    
    /**
     * 关联文件实例
     * @var class g_binary_link
     */
    protected $instance_binary_link = NULL;
	
	/**
	 * 构造函数
	 * 
	 * @param string $object_id 预操作的对象id
	 * @return void
	 */
	function __construct($object_id)
	{
		scap_append_module_include_path('module_g_00');// 增加module_g_00为当前路径
		scap_append_module_include_path('module_g_org');// 增加module_g_org为当前路径
		
		$this->set_current_entity_info();
		$this->set_current_object_id($object_id);
	}
	
	/**
	 * 设置继承类的实体相关信息，应包含：
	 * 1.$this->current_entity_id (必设)
	 * 2.$this->current_entity_name (必设)
	 * 3.$this->contact_entity_id (可选)
	 * 4.$this->org_entity_id (可选)
	 * 5.$this->ou_entity_id (可选)
	 * 
	 * @return void
	 */
	abstract protected function set_current_entity_info();
	
	/**
	 * 检查当前的对象($this->current_object_id)是否存在
	 * 
	 * @return bool 如果存在返回true，否则返回false
	 */
	abstract protected function check_current_object_exist();
	
	/**
	 * 创建一个实体对象
	 * 
	 * @param array $content 对象内容数据
	 * 
	 * @return class 实体对象实例
	 */
	abstract public static function create($content);
	
	/**
	 * 读取当前对象主体内容
	 * 
	 * @return array 对象内容数据
	 */
	abstract public function read();
	
	/**
	 * 更新当前对象主体内容
	 * 
	 * @param array $content 对象内容数据
	 * @return bool
	 */
	abstract public function update($content);
	
	/**
	 * 删除当前对象数据
	 * 
	 * @return bool
	 */
	abstract public function delete();
	
	/**
	 * 设置当前对象id
	 * @param string $object_id
	 * @return void
	 */
	protected function set_current_object_id($object_id)
	{
		if (empty($this->current_entity_id))
		{
			throw new Exception("未设置当前实体类型ID。");
		}
		
		$this->current_object_id = $object_id;
		
		if ($this->check_current_object_exist() == false)
		{
			$this->current_object_id = '';
			throw new Exception("指定的{$this->current_entity_name}ID'{$object_id}'不存在于系统中。");
		}
	}
	
	/**
	 * 获取当前对象ID
	 * 
	 * @return string
	 */
	public function get_current_object_id()
	{
		return $this->current_object_id;
	}
	
	/**
	 * 获取当前实体类型ID
	 * 
	 * @return string
	 */
	public function get_current_entity_id()
	{
		return $this->current_entity_id;
	}
	
	/**
	 * 获取附加要素实例
	 * 
	 * @return class g_ae
	 */
	public function get_instance_ae()
	{
		if (!$this->instance_ae instanceof g_ae)
		{
			$this->instance_ae = new g_ae($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_ae;
	}
	
	/**
	 * 获取日志实例
	 * 
	 * @return class g_app_log
	 */
	public function get_instance_log()
	{
		if (!$this->instance_log instanceof g_app_log)
		{
			$this->instance_log = new g_app_log($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_log;
	}
	
	/**
	 * 获取对象关联object_link实例
	 * 
	 * @return class g_object_link
	 */
	public function get_instance_object_link()
	{
		if (!$this->instance_object_link instanceof g_object_link)
		{
			$this->instance_object_link = new g_object_link($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_object_link;
	}
	
	/**
	 * 获取对象关联object_relation实例
	 * 
	 * @return class g_object_relation
	 */
	public function get_instance_object_relation()
	{
	    if (!$this->instance_object_relation instanceof g_object_relation)
		{
			$this->instance_object_relation = new g_object_relation($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_object_relation;
	}
	
	/**
	 * 获取时间节点实例
	 * 
	 * @return class g_time_node
	 */
	public function get_instance_time_node()
	{
		if (!$this->instance_time_node instanceof g_time_node)
		{
			$this->instance_time_node = new g_time_node($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_time_node;
	}
	
	/**
	 * 获取状态关联实例
	 * 
	 * @return class g_status
	 */
	public function get_instance_status()
	{
		if (!$this->instance_status instanceof g_status)
		{
			$this->instance_status = new g_status($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_status;
	}
	
	/**
	 * 获取类别关联实例
	 * @return class g_category_link
	 */
	public function get_instance_category_link()
	{
		if (!$this->instance_category_link instanceof g_category_link)
		{
			$this->instance_category_link = new g_category_link($this->current_object_id, $this->current_entity_id);
		}
		return $this->instance_category_link;
	}
	
	/**
	 * 获取关联联系人(contact)实例
	 * 
	 * @return class g_party_link
	 */
	public function get_instance_contact_link()
	{
		if (empty($this->contact_entity_id))
		{
			throw new Exception("未设置{$this->current_entity_name}关联的联系人实体ID。");
		}
		
		if (!$this->instance_contact_link instanceof g_party_link)
		{
			$this->instance_contact_link = new g_party_link($this->current_object_id, $this->current_entity_id, $this->contact_entity_id);
		}
		return $this->instance_contact_link;
	}
	
	/**
	 * 获取关联机构(org)实例
	 * 
	 * @return class g_party_link
	 */
	public function get_instance_org_link()
	{
		if (empty($this->org_entity_id))
		{
			throw new Exception("未设置{$this->current_entity_name}关联的机构实体ID。");
		}
		
		if (!$this->instance_org_link instanceof g_party_link)
		{
			$this->instance_org_link = new g_party_link($this->current_object_id, $this->current_entity_id, $this->org_entity_id);
		}
		return $this->instance_org_link;
	}
	
	/**
	 * 获取ou实例
	 * 
	 * @return class g_party_link
	 */
	public function get_instance_ou_link()
	{
		if (empty($this->ou_entity_id))
		{
			throw new Exception("未设置{$this->current_entity_name}关联的OU实体ID。");
		}
		
		if (!$this->instance_ou_link instanceof g_party_link)
		{
			$this->instance_ou_link = new g_party_link($this->current_object_id, $this->current_entity_id, $this->ou_entity_id);
		}
		return $this->instance_ou_link;
	}
	
    /**
     * 获取关联文件实例
     * 
     * @return class g_binary_link
     */
    public function get_instance_binary_link()
    {
        if (!$this->instance_binary_link instanceof g_binary_link)
        {
            $this->instance_binary_link = new g_binary_link($this->current_object_id, $this->current_entity_id);
        }
        return $this->instance_binary_link;
    }
}
?>