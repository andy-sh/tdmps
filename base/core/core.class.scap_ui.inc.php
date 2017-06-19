<?php
/**
 * scap ui类文件
 *
 * @category scap
 * @package kernel
 * @subpackage view
 * @version $Id: core.class.scap_ui.inc.php 94 2013-04-10 02:17:50Z liqt $
 * @creator LiQintao @ 2013-02-05 上午11:44:32
 */
use scap\module\g_tool\config;

require_once(SCAP_PATH_LIBRARY.'smarty3/Smarty.class.php');

/**
 * 系统ui类
 * -应用入口的定义
 * -应用所需输出数据($data_output)的赋值
 * -使用smarty3作为模板引擎
 */
abstract class scap_ui
{
    /**
     * $data_render的域名称：render head
     * 用于存储标准的render元素信息
     * @var string
     */
    const DATA_RENDER_DOMAIN_HEAD_ELEMENT = 'head';
    
    /**
     * $data_render的域名称：当前应用的数据域render current app
     * 用于存储标准的render元素信息
     * @var string
     */
    const DATA_RENDER_DOMAIN_CURRENT_APP = 'a';
    
    /**
     * 为显示所需而渲染的数据
     * @var array
     */
    protected static $data_render = array();

    /**
     * 显示输出的内容
     * @var string
     */
    protected $display_content = NULL;

    /**
     * smarty实例
     * @var object Smarty
     */
    protected $smarty = NULL;
    
    /**
     * 当前模块id
     * @var string
     */
    protected $current_module_id = NULL;

    /**
     * 当前类名称
     * @var string
     */
    protected $current_class_name = NULL;

    /**
     * 当前方法名称
     * @var string
     */
    protected $current_method_name = NULL;
    
    /**
	 * 当前调用的事件执行结果
	 * @var bool
	 */
	protected $current_event_result = false;
    
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->current_module_id = $GLOBALS['scap']['info']['current_module_id'];
	    $this->current_class_name = $GLOBALS['scap']['info']['current_class'];
	    $this->current_method_name = $GLOBALS['scap']['info']['current_method'];
	    
        $this->smarty = new Smarty();
        $this->initial_smarty();
        
        // 初始化模板所需数据
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['lang'] = config::get('tpl', 'head.lang');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['keywords'] = config::get('tpl', 'head.keywords');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['description'] = config::get('tpl', 'head.description');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['author'] = config::get('tpl', 'head.author');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['title'] = config::get('tpl', 'head.title');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['icon'] = config::get('tpl', 'head.icon');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['enable_chrome_in_ie'] = config::get('tpl', 'head.enable_chrome_in_ie');
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_list'] = array();
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_list'] = array();
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_code'] = array();
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_code'] = array();
        self::$data_render[self::DATA_RENDER_DOMAIN_CURRENT_APP] = array();// 当前应用数据区域
        
        scap_load_module_language($this->current_module_id, 'zh-cn');
        
        $this->initial();
    }

    /**
     * 初始化
     * 供继承类实现一些需要初始调用的代码，该方法自动被构造函数调用
     */
    protected function initial()
    {
        \scap\module\g_template\template::add_tpl_dir_for_smarty($this->smarty);// 添加g_template的模板库到当前路径
    }
    
    /**
     * 初始化smarty对象
     *
     * @return $this
     */
    protected function initial_smarty()
    {
        $this->smarty->use_sub_dirs = false;// 如果允许创建子目录,在拥有成千上万的文件环境中，这有可能加速你的文件系统
        $this->smarty->setCacheDir(SCAP_PATH_ROOT."tmp/tpl_cache");
        $this->smarty->setCompileDir(SCAP_PATH_ROOT."tmp/tpl_complie");
        $this->smarty->left_delimiter = "{<";
        $this->smarty->right_delimiter = ">}";
        
        return $this;
    }

    /**
     * 获取渲染的输出数据
     *
     * @return array
     */
    public function get_data_render()
    {
        return self::$data_render;
    }

    /**
     * 获取最终显示内容字符串
     *
     * @return string
     */
    public function get_display_content()
    {
        return $this->display_content;
    }

    /**
     * 显示输出显示内容字符串
     */
    public function show_display_content()
    {
        echo $this->display_content;
    }

    /**
     * 设置当前模块
     * @param string $name 模块id
     *
     * @return $this
     */
    public function set_current_module($name)
    {
        $this->current_module_id = $name;

        return $this;
    }

    /**
     * 获取当前模块id
     *
     * @return string
     */
    public function get_current_module()
    {
        return $this->current_module_id;
    }

    /**
     * 设置当前类名称
     * @param string $name 类名称
     *
     * @return $this
     */
    public function set_current_class($name)
    {
        $this->current_class_name = $name;
        
        return $this;
    }

    /**
     * 获取当前类名称
     *
     * @return string
     */
    public function get_current_class()
    {
        return $this->current_class_name;
    }

    /**
     * 设置当前方法名称
     * @param string $name 方法名称
     *
     * @return $this
     */
    public function set_current_method($name)
    {
        $this->current_method_name = $name;
        
        return $this;
    }

    /**
     * 获取当前方法名称
     *
     * @return string
     */
    public function get_current_method()
    {
        return $this->current_method_name;
    }

	/**
	 * BC:设置当前处理事件的信息
	 */
	public function set_current_event_info()
	{
		$this->current_event_name = $GLOBALS['scap']['event']['name'];
		$this->current_event_result = $GLOBALS['scap']['event']['result'];
	}
    
    /**
     * 渲染指定模板
     * - 并将内容附加在display_content中
     * - 支持css文件加载重量
     * - 支持js文件加载重量
     *
     * @param string $name 模板名称
     * @param array $old_data_out 兼容老版本的输出，默认为空
     * @param string $module 模板所属模块，默认为null(使用当前模板)
     *
     * @return $this
     */
    public function render_tpl($file, $old_data_out = array(), $module = NULL)
    {
        if (empty($module))
        {
            $module = $this->current_module_id;
        }
        
        // 根据css文件重量变化加载顺序,越重越放在后面
        usort(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_list'], 'self::sort_by_weight');
        // 根据js文件重量变化加载顺序,越重越放在后面
        usort(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_list'], 'self::sort_by_weight');
        // 根据css代码重量变化加载顺序,越重越放在后面
        usort(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_code'], 'self::sort_by_weight');
        // 根据js代码重量变化加载顺序,越重越放在后面
        usort(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_code'], 'self::sort_by_weight');
        
        $this->smarty->addTemplateDir(SCAP_PATH_ROOT.$module."/templates/default");
        $this->smarty->assign(self::$data_render);
        if (!empty($old_data_out))
        {
            $this->smarty->assign($old_data_out);
        }
        $this->display_content .= $this->smarty->fetch($file);
        
        return $this;
    }

    /**
     * 数组weight值比较方法(供usort排序用)
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    private static function sort_by_weight($a, $b)
    {
        $result = 0;
        $weight_a = intval($a['weight']);
        $weight_b = intval($b['weight']);

        if ($weight_a > $weight_b)
        {
            $result = 1;
        }
        elseif ($weight_a < $weight_b)
        {
            $result = -1;
        }
        
        return $result;
    }
    
    /**
     * 为页面头部插入加载的css文件
     * - http://www.w3schools.com/tags/att_link_media.asp
     * - http://www.w3schools.com/tags/tag_link.asp
     * - http://www.w3schools.com/css/css_mediatypes.asp
     * - ie判断触发：http://www.quirksmode.org/css/condcom.html
     * 
     * @param string $url 文件url地址
     * @param string $media 该css文件试用的设备，默认为空，多个用逗号分开
     * @param int $weight 文件加载重量，值越小越靠前，越大越靠后，默认值NULL,则自动按当前加入顺序+100赋值
     * @param string $ie_condition 根据ie条件触发，只在满足ie指定条件下加载css，默认为空，填写如：IE,IE 6,lt IE 9,lte IE 7,gt IE 6
     * @param bool $flag_avoid_duplication 避免重复加载标志，默认为true
     *
     * @return void
     */
    public static function insert_head_css_file($url, $media = '', $weight = NULL, $ie_condition = '', $flag_avoid_duplication = true)
    {
        if ($flag_avoid_duplication)
        {
            if (\scap\module\g_tool\matrix::check_value_exist($url, self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_list'], 'url'))
            {
                return;
            }
        }
        
        if (is_null($weight))// 自动计算重量
        {
            $weight = count(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_list']) + 100;
        }
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_list'][] = array('url' => $url, 'media' => $media, 'weight' => $weight, 'ie_condition' => $ie_condition);
    }

    /**
     * 为页面头部插入加载的js文件
     *
     * @param string $url 文件url地址
     * @param int $weight 文件加载重量，值越小越靠前，越大越靠后，默认值NULL,则自动按当前加入顺序+100赋值
     * @param string $ie_condition 根据ie条件触发，只在满足ie指定条件下加载css，默认为空，填写如：IE,IE 6,lt IE 9,lte IE 7,gt IE 6
     * @param bool $flag_avoid_duplication 避免重复加载标志，默认为true
     *
     * @return void
     */
    public static function insert_head_js_file($url, $weight = NULL, $ie_condition = '', $flag_avoid_duplication = true)
    {
        if ($flag_avoid_duplication)
        {
            if (\scap\module\g_tool\matrix::check_value_exist($url, self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_list'], 'url'))
            {
                return;
            }
        }
        
        if (is_null($weight))// 自动计算重量
        {
            $weight = count(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_list']) + 100;
        }
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_list'][] = array('url' => $url, 'weight' => $weight, 'ie_condition' => $ie_condition);
    }
    
    /**
     * 为页面头部插入加载的css代码
     *
     * @param string $code css代码
     * @param int $weight 文件加载重量，值越小越靠前，越大越靠后，默认值NULL,则自动按当前加入顺序+100赋值
     *
     * @return void
     */
    public static function insert_head_css_code($code, $weight = NULL)
    {
        if (is_null($weight))// 自动计算重量
        {
            $weight = count(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_code']) + 100;
        }
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['css_code'][] = array('code' => $code, 'weight' => $weight);
    }
    
    /**
     * 为页面头部插入加载的js代码
     *
     * @param string $code js代码
     * @param int $weight 文件加载重量，值越小越靠前，越大越靠后，默认值NULL,则自动按当前加入顺序+100赋值
     *
     * @return void
     */
    public static function insert_head_js_code($code, $weight = NULL)
    {
        if (is_null($weight))// 自动计算重量
        {
            $weight = count(self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_code']) + 100;
        }
        self::$data_render[self::DATA_RENDER_DOMAIN_HEAD_ELEMENT]['js_code'][] = array('code' => $code, 'weight' => $weight);
    }
    
    /**
     * 设置数据渲染值
     * 
     * @param string $domain 值域
     * @param string $name 数据名称
     * @param mixed $value 数据值
     * 
     * @return void
     */
    public static function set_data_render_value($domain, $name, $value)
    {
        self::$data_render[$domain][$name] = $value;
    }
    
    /**
     * 将传入数据合并至$data_render中
     * 
     * @param array $data_render 待合并的数组
     * 
     * @return void
     */
    public static function merge_data_render($data_render)
    {
        if (!is_array($data_render)) return;
        self::$data_render = array_merge(self::$data_render, $data_render);
    }
    
}
?>