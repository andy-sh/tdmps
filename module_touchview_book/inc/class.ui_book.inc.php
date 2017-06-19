<?php
/**
 * 书籍ui文件
 * create time: 2011-12-26 下午02:23:43
 * @version $Id: class.ui_book.inc.php 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
 */
use scap\module\g_tool\matrix;
use scap\module\g_tool\string;
use scap\module\g_template\template;
use scap\module\g_form\form;

/**
 * book ui类
 *
 */
class ui_book extends scap_ui
{
    public function __construct()
    {
        parent::__construct();
        
        scap_append_module_include_path('module_g_00');
        scap_load_module_function('module_g_00', 'g');
        scap_load_module_define('module_touchview_basic', 'entity_id');
        scap_load_module_class('module_touchview_page', 'page');
        scap_load_module_define('module_touchview_book', 'book_status');
        scap_load_module_function('module_touchview_basic', 'touchview');
    }
    
	/**
     * 后台管理用：编辑书籍
     */
    public function edit_book()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        $data_in['get']['b_id'] = $_GET['b_id'];// bookid
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        switch($data_in['get']['act'])
        {
            case STAT_ACT_CREATE:
                $data_flag['is_create'] = true;
                $data_def['text_menu'] = '创建';
                $data_def['title'] .="创建书籍";
                $data_def['text_act'] = "创建";
                break;
            case STAT_ACT_EDIT:
                $data_flag['is_edit'] = true;
                $data_def['text_menu'] = '书籍';
                $data_def['title'] .="编辑书籍";
                $data_def['text_act'] = "编辑";
                break;
            case STAT_ACT_REMOVE:
                $data_flag['is_remove'] = true;
                $data_def['title'] .="删除书籍";
                $data_def['text_act'] = "删除";
                break;
            case STAT_ACT_VIEW:
                $data_flag['is_view'] = true;
                $data_def['text_menu'] = '书籍';
                $data_def['title'] .="查看书籍";
                $data_def['text_act'] = "查看";
                break;
        }
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'book_save':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = matrix::trim($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('b_id' => $_GET['b_id'], 'act' => STAT_ACT_EDIT, 'nonav' => $data_in['get']['nonav']));
                }
                break;
            case 'book_remove':
                if ($this->current_event_result === false)
                {
                    g_redirect_feedback_info_page("删除操作失败。");
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'index_book'), array('nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        if ($data_flag['is_view'] || $data_flag['is_edit'])
        {
            $book = new book($data_in['get']['b_id']);
            $data_db['content'] = $book->read();
            $data_db['content']['tpl'] = book::get_book_tpl($data_in['get']['b_id']);
        }
        
        $data_def['tpl_list'] = book::get_tpl_list();// 模板列表
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }
        
        if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        
        if (!isset($data_in['content']['b_sort_sn']) || strlen($data_in['content']['b_sort_sn']) < 1)
        {
            $data_in['content']['b_sort_sn'] = 100;
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        $data_flag['b_name']['readonly'] = $data_flag['is_view'];
        $data_flag['b_name']['required'] =  !$data_flag['b_name']['readonly'];
        
        $data_flag['b_description']['readonly'] = $data_flag['is_view'];
        $data_flag['b_description']['required'] =  false;
        
        $data_flag['common']['readonly'] = $data_flag['is_view'];
        
        $data_flag['btn_save']['show'] = ($data_flag['is_create'] || $data_flag['is_edit']);
        $data_flag['btn_close']['show'] = $data_flag['is_view'];
        $data_flag['link_log']['show'] = $data_flag['is_view'] || $data_flag['is_edit'];
        $data_flag['link_remove']['show'] = $data_flag['is_view'] || $data_flag['is_edit'];
        $data_flag['link_edit']['show'] = $data_flag['is_view'];
        $data_flag['link_view']['show'] = $data_flag['is_edit'];
        $data_flag['link_edit']['link_structure'] = $data_flag['is_edit'];
        $data_flag['link_edit']['link_preview'] = $data_flag['is_edit'];
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $i = 0;
        foreach($data_def['tpl_list'] as $k => $v)
        {
            if ($v == $data_in['content']['tpl'])
            {
                $data_render['tpl_list'][$i]['item'] = scap_html::input_radio(array('name' => 'content[tpl]', 'value' => $v, 'id' => $v, 'checked' => 'checked'), $data_flag['common']['readonly']);
            }
            else
            {
                $data_render['tpl_list'][$i]['item'] = scap_html::input_radio(array('name' => 'content[tpl]', 'value' => $v, 'id' => $v), $data_flag['common']['readonly']);
            }
            $data_render['tpl_list'][$i]['item'] .= scap_html::label(array('for' => $v), scap_html::image(array('src' => book::get_url_tpl_front_cover_img($v), 'width' => 83, 'height' => 52)));
            $i ++;
        }
        
        $data_render['b_name'] = scap_html::input_text(  array(  'name'=>'content[b_name]', 
                                                              'value' => $data_in['content']['b_name'], 
                                                              'maxlength' => 200,
                                                              'style' => 'width:98%;'
                                                            ), 
                                                      $data_flag['b_name']['readonly'], 
                                                      false, 
                                                      true, 
                                                      $data_flag['b_name']['required']
                                                    );
        $data_render['b_sort_sn'] = scap_html::input_text(  array(  'name'=>'content[b_sort_sn]', 
                                                              'value' => $data_in['content']['b_sort_sn'], 
                                                              'maxlength' => 10,
                                                              'style' => 'width:50px;'
                                                            ), 
                                                      $data_flag['common']['readonly'], 
                                                      false, 
                                                      true, 
                                                      false
                                                    );
        $data_render['b_status'] = scap_html::select(  array('name'=>'content[b_status]'),
                                                    $GLOBALS['scap']['text']['module_touchview_book']['status_book_show'],
                                                    array($data_in['content']['b_status']),
                                                    $data_flag['common']['readonly'],
                                                    false,
                                                    true,
                                                    !$data_flag['common']['readonly'],
                                                    scap_get_image_url('module_basic', 'require.gif')
                                                 );
        $data_render['b_description'] = scap_html::textarea(   array('name' => "content[b_description]", 'id' => 'b_description', 'rows' => 3), 
                                                            $data_in['content']['b_description'],
                                                            $data_flag['b_description']['readonly'],
                                                            false,
                                                            true,
                                                            $data_flag['b_description']['required']
                                                        );
        
        $data_render['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => TEXT_SAVE, 'title' => TEXT_TIP_BTN_SAVE), false, $data_flag['btn_save']['show']);
        $data_render['btn_close'] = scap_html::input_button(array('name' => 'button[btn_close]', 'value' => TEXT_CLOSE, 'title' => TEXT_TIP_BTN_CLOSE, 'onclick' => 'window.close();'), false, $data_flag['btn_close']['show']);
        
        $data_render['link_log'] = $data_flag['link_log']['show'] ? g_create_object_log_link($data_in['get']['b_id'], '操作日志') : '';
        $data_render['link_remove'] = scap_html::anchor( array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'edit_book'), array('b_id' => $data_in['get']['b_id'], 'b_name' => $data_in['content']['b_name'], 'act' => STAT_ACT_REMOVE)), 
                                                                'class' => 'scap_button',
                                                                'title' => '删除',
                                                                'onclick' => "return confirm('确认删除该数据么,此过程将不可恢复!');"),
                                                        '删除',
                                                        $data_flag['link_remove']['show']
                                                    );
        $data_render['link_edit'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'edit_book'), array('b_id' => $data_in['get']['b_id'], 'act' => STAT_ACT_EDIT, 'nonav' => $data_in['get']['nonav'])), 
                                                                'class' => 'scap_button',
                                                                'title' => '编辑'),
                                                        '编辑',
                                                        $data_flag['link_edit']['show']
                                                    );
        $data_render['link_structure'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'book_structure'), array('b_id' => $data_in['get']['b_id'], 'act' => STAT_ACT_EDIT, 'nonav' => $data_in['get']['nonav'])), 
                                                                'class' => 'scap_button',
                                                                'title' => '页面结构'),
                                                        '页面结构',
                                                        $data_flag['link_edit']['link_structure']
                                                    );  
                                                    
        $data_render['link_preview'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book_front', 'method' => 'book'), array('b_id' => $data_in['get']['b_id'])), 
                                                                'target' => '_blank',
        														'class' => 'scap_button',
                                                                'title' => '预览'),
                                                        '预览',
                                                        $data_flag['link_edit']['link_preview']
                                                    );                                                                                       
        $data_render['link_view'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'edit_book'), array('b_id' => $data_in['get']['b_id'], 'act' => STAT_ACT_VIEW, 'nonav' => $data_in['get']['nonav'])), 
                                                                'class' => 'scap_button',
                                                                'title' => '查看'),
                                                        '查看',
                                                        $data_flag['link_view']['show']
                                                    );
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('edit.book.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
	/**
     * 后台管理用：编辑书籍配置
     */
    public function edit_book_config()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        $data_in['get']['act'] = intval($_GET['act']); // 操作类型
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        switch($data_in['get']['act'])
        {
            case STAT_ACT_EDIT:
                $data_flag['is_edit'] = true;
                $data_def['text_menu'] = '配置';
                $data_def['title'] .="编辑配置";
                $data_def['text_act'] = "编辑";
                break;
            case STAT_ACT_VIEW:
                $data_flag['is_view'] = true;
                $data_def['text_menu'] = '配置';
                $data_def['title'] .="查看配置";
                $data_def['text_act'] = "查看";
                break;
        }
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'book_config_save':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = matrix::trim($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('act' => STAT_ACT_EDIT, 'nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        if ($data_flag['is_view'] || $data_flag['is_edit'])
        {
            $data_db['content']['config_auto_flip_switch'] = book::get_config_book_auto_flip_switch();
            $data_db['content']['config_auto_flip_waiting'] = book::get_config_book_auto_flip_waiting();
            $data_db['content']['config_max_book_count'] = book::get_config_book_max_book_count();
            $data_db['content']['config_max_page_count'] = book::get_config_book_max_page_count();
        }
        
        // 设置自动翻页等待时间选项
        $data_def['list_auto_flip_waiting'] = array(60 => '1分钟', 180 => '3分钟', 300 => '5分钟', 480 => '8分钟', 600 => '10分钟', 900 => '15分钟', 1200 => '20分钟', 1800 => '30分钟', 2700 => '45分钟', 3600 => '60分钟');
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }
        
        if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        $data_flag['common']['readonly'] = $data_flag['is_view'];
        
        $data_flag['btn_save']['show'] = ($data_flag['is_create'] || $data_flag['is_edit']);
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['config_auto_flip_switch'] = scap_html::checkbox(array('name' => 'content[config_auto_flip_switch]', 'id' => 'config_auto_flip_switch'), $data_in['content']['config_auto_flip_switch']);
        $data_render['config_auto_flip_switch'] .= scap_html::label(array('for' => 'config_auto_flip_switch'), '启用');
        
        $i = 0;
        foreach($data_def['list_auto_flip_waiting'] as $k => $v)
        {
            if ($k == $data_in['content']['config_auto_flip_waiting'])
            {
                $data_render['list_auto_flip_waiting'][$i]['item'] = scap_html::input_radio(array('name' => 'content[config_auto_flip_waiting]', 'value' => $k, 'id' => $k, 'checked' => 'checked'));
            }
            else
            {
                $data_render['list_auto_flip_waiting'][$i]['item'] = scap_html::input_radio(array('name' => 'content[config_auto_flip_waiting]', 'value' => $k, 'id' => $k));
            }
            $data_render['list_auto_flip_waiting'][$i]['item'] .= scap_html::label(array('for' => $k), $v);
            $i ++;
        }
        
        $data_render['config_max_book_count'] = scap_html::input_text(  array(  'name'=>'content[config_max_book_count]', 
                                                              'value' => $data_in['content']['config_max_book_count'], 
                                                              'maxlength' => 4,
                                                              'style' => 'width:100px;'
                                                            ));
        
        $data_render['config_max_page_count'] = scap_html::input_text(  array(  'name'=>'content[config_max_page_count]', 
                                                              'value' => $data_in['content']['config_max_page_count'], 
                                                              'maxlength' => 4,
                                                              'style' => 'width:100px;'
                                                            ));
        
        $data_render['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => TEXT_SAVE, 'title' => TEXT_TIP_BTN_SAVE), false, $data_flag['btn_save']['show']);
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('edit.book.config.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     *  book索引
     */
    public function index_book()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_menu'] = '';
        
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        //--------GET参数处理[end]--------
        
        //--------查询参数处理[start]--------
        // 处理传入的查询参数数组:$_REQUEST['search']
        if (!empty($_REQUEST['search']))
        {
            foreach($_REQUEST['search'] as $k => $v)
            {
                //post优先
                if (empty($_POST['search']))
                {
                    $data_in['search'][$k] = $v;
                }
                else
                {
                    $data_in['search'][$k] = $_POST['search'][$k];
                }
            }
        }
                
        // 具体处理不同参数的传递
        
        if (strlen($data_in['search']['b_name']) > 0)
        {
            $data_db['where'] .= " AND b_name LIKE '%{$data_in['search']['b_name']}%'";
            $data_in['extra_vars']['search[b_name]'] = $data_in['search']['b_name'];
        }

        //--------查询参数处理[end]----------
        
        //--------分页/步长处理[start]--------
        $data_def['steps_options'] = array(20, 40, 80);// 设置分页步长选项
        $data_def['step_default'] = $data_def['steps_options'][2];// 默认步长
        $data_in['split_page'] = array();// 分页参数输入数据
        
        // 步长处理(步长参数名称为'steps')
        if (!is_null(scap_html::scap_index_steps_select_get()))// 获取步长下拉菜单选项值
        {
            $data_in['split_page']['steps'] = scap_html::scap_index_steps_select_get();
        }
        elseif(isset($_REQUEST['steps']))
        {
            $data_in['split_page']['steps'] = (int)$_REQUEST['steps'];
            // 如果获取步长数值不在步长选项之中,则将其置为默认步长
            if (!in_array($data_in['split_page']['steps'], $data_def['steps_options']))
            {
                $data_in['split_page']['steps'] = $data_def['step_default'];
            }
        }
        else
        {
            $data_in['split_page']['steps'] = $data_def['step_default'];
        }
        
        $data_in['extra_vars']['steps'] = $data_in['split_page']['steps'];
        
        // 分页位置处理(分页参数名称为'start')
        if (!is_null(scap_html::scap_index_pages_select_get()))
        {
            $data_in['split_page']['start'] = scap_html::scap_index_pages_select_get();
        }
        elseif(isset($_REQUEST['start']))
        {
            $data_in['split_page']['start'] = (int)$_REQUEST['start'];
        }
        else
        {
            $data_in['split_page']['start'] = 0;
        }
        
        $data_in['extra_vars']['start'] = $data_in['split_page']['start'];
        //--------分页/步长处理[end]--------
        
        //--------排序参数处理[start]--------
        $data_in['taxis'] = array();// 排序参数输入数据
        switch($_REQUEST['order'])// 定义合法的可排序列名
        {
            case 'b_name':
            case 'b_status':
            case 'b_sort_sn':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'b_name';// 默认排序的列名
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];
        
        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'ASC';// 默认排序方式
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
        //--------排序参数处理[end]--------
        
        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = matrix::urlencode($data_in['extra_vars']);
        
        //--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[start]--------
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }
        //--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[end]--------
        
        // 构造查询所需参数
        $data_db['query'] = array(
                'id' => 'b_id',
                'sql' => "SELECT * FROM touchview_book WHERE (1=1 {$data_db['where']})",
                'order' => $data_in['taxis']['order'],
                'sort' => $data_in['taxis']['sort'],
                'start' => $data_in['split_page']['start'],
                'steps' => $data_in['split_page']['steps'],
            );
            
        // 执行查询,并返回查询集合到$data_db['content']
        $data_db['content'] = scap_entity::query($data_db['query']);
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]-------
        
        //--------模版赋值[start]--------
        
        if (count($data_db['content']) > 0)
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_render['data_list'][$i] = $v;
                $data_render['data_list'][$i]['sn'] = $i+1;
                $data_render['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
                
                $data_render['data_list'][$i]['b_name'] =  scap_html::anchor(
                                                                        array('href' => scap_get_url(array('module' => $this->current_module_id, 'class' => 'ui_book', 'method' => 'edit_book'), array('b_id' => $v['b_id'], 'act' => STAT_ACT_EDIT)), 'title' => '点击编辑'),
                                                                        $v['b_name']
                                                                    );
                $data_render['data_list'][$i]['b_status'] = $GLOBALS['scap']['text']['module_touchview_book']['status_book_show'][$v['b_status']];
                $data_render['data_list'][$i]['b_description'] = $v['b_description'];
                $data_render['data_list'][$i]['tpl'] = book::get_book_tpl($v['b_id']);
                $i ++;
            }
        }
        
        // [分页功能输出]
        $data_render['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
        $data_render['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
        $data_render['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
        $data_render['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
        $data_render['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);
        
        // [索引头部信息输出]
        $data_render['head_b_name'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'b_name', $data_in['taxis']['order'], "书名", $data_in['extra_vars']);
        $data_render['head_b_sort_sn'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'b_sort_sn', $data_in['taxis']['order'], "序号", $data_in['extra_vars']);
        $data_render['head_b_status'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'b_status', $data_in['taxis']['order'], "状态", $data_in['extra_vars']);
        $data_render['head_b_description'] = "简介";
        $data_render['head_tpl'] = "模板";
        
        // [查询项目输出]
        $data_render['search_name'] = scap_html::input_text(array('title' => "输入名称的一部分即可查询，如果留空，则不对该项进行筛选。", 'name' => 'search[b_name]', 'value' => $data_in['search']['b_name'], 'size' => 60, 'maxlength' => 200));
        
        $data_render['btn_search'] = scap_html::input_submit(array('name' => 'button[search]', 'value' => '查询', 'title' => TEXT_TIP_BTN_SEARCH));
        $data_render['btn_reset'] = scap_html::input_button(array('name' => 'button[reset]', 'value' => '重置', 'onclick' =>"clearForm(form_index,'sel_page,sel_steps');", 'title' => TEXT_TIP_BTN_RESET));
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $data_def['title'] .= '书籍';
        $data_def['text_menu'] = '书籍';
        
        self::insert_head_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('index.book.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 导出book数据文件
     */
    public function export_book_file()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '书籍';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = true; // 是否显示系统导航栏,空为显示,否则为不显示
        $data_in['get']['b_id'] = $_GET['b_id'];// book id
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        $data_in['content']['link_data_file'] = book::generate_book_data_file($data_in['get']['b_id']);
        $data_in['content']['link_media_file'] = book::zip_book_media_file($data_in['get']['b_id']);
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
                                                    
        $data_render['link_data_file'] = scap_html::anchor(   array(  'href' => $data_in['content']['link_data_file']),
                                                           book::get_name_from_id($data_in['get']['b_id']).':数据文件'
                                                    );                                                                                       
        $data_render['link_media_file'] = scap_html::anchor(   array(  'href' => $data_in['content']['link_media_file']),
                                                           book::get_name_from_id($data_in['get']['b_id']).':媒体文件'
                                                    );                                                                                       
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('export.book.file.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 导入书籍数据step 1
     * 上传书籍内容数据
     */
    public function import_book_step_1()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '导入书籍第一步:上传书籍内容数据';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        //--------GET参数处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'book_import_step_1':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = matrix::trim($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'import_book_step_2'), array('file_name' => $_GET['file_name'], 'nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['upload_file']	= scap_html::input_file(array('name' => 'upload_file', 'id' => 'upload_file', 'size' => '50'));
        $data_render['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '下一步'));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $data_def['text_menu'] = '导入';
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('import.book.step1.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 导入书籍数据step 2
     * 将内容数据导入数据库
     */
    public function import_book_step_2()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '导入书籍第二步:内容数据导入数据库';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        $data_in['get']['file_name'] = $_GET['file_name'];
        //--------GET参数处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'book_import_step_2':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = matrix::trim($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'import_book_step_3'), array('b_id' => $_GET['b_id'], 'nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        $data_db = json_decode(file_get_contents(SCAP_PATH_ROOT."module_touchview_basic/templates/cache/{$data_in['get']['file_name']}"), true);
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['b_id'] = $data_db['book']['b_id'];
        if (scap_entity::get_row_count('touchview_book', "b_id = '{$data_db['book']['b_id']}'") > 0)
        {
            $data_render['b_id'] .= " ".scap_html::span(array('style' => 'color:red;'), '(该书籍在数据库已存在，如执行“下一步”将会覆盖当前已存在书籍。)');
        }
        $data_render['b_name']	= $data_db['book']['b_name'];
        $data_render['page_count'] = count($data_db['page']);
        
        $data_render['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '下一步'));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $data_def['text_menu'] = '导入';
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('import.book.step2.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 导入书籍数据step 3
     * 上传书籍媒体文件
     */
    public function import_book_step_3()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '导入书籍第三步:上传书籍媒体文件';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        //--------GET参数处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'book_import_step_3':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = matrix::trim($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'edit_book'), array('b_id' => $_GET['b_id'], 'act' => STAT_ACT_EDIT, 'nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['upload_file']	= scap_html::input_file(array('name' => 'upload_file', 'id' => 'upload_file', 'size' => '50'));
        $data_render['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '完成'));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $data_def['text_menu'] = '导入';
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('import.book.step3.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * book架构展现与交互
     */
    public function book_structure()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '书籍页面结构';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        $data_in['get']['b_id'] = $_GET['b_id'];// book id
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['b_id'] = $data_in['get']['b_id'];
        $data_render['link_menu_book'] = scap_get_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'menu_book'), array('b_id' => ''));
        $data_render['link_menu_section'] = scap_get_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'menu_section'), array('p_id' => ''));
        $data_render['link_menu_page'] = scap_get_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => 'menu_page'), array('p_id' => ''));
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        self::insert_head_js_file(scap_get_js_url('module_touchview_book', 'third/browserdetect.js'));
        form::load_jstree_base_file();
        form::load_fgmenu_base_file();
        
        $data_def['text_menu'] = '书籍';
        template::render_default_tpl($data_def['title'], $data_def['text_menu'], !$data_in['get']['nonav']);
        $this->render_tpl('book.structure.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 加载架构数据，返回html样式
     */
    public function load_structure_html()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_render = '';
        
        $data_def['ENTITY_ID_TOUCHVIEW_BOOK'] = ENTITY_ID_TOUCHVIEW_BOOK;
        $data_def['ENTITY_ID_TOUCHVIEW_PAGE'] = ENTITY_ID_TOUCHVIEW_PAGE;
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['id'] = $_GET['id'];// 
        $data_in['get']['b_id'] = $_GET['b_id'];// 
        $data_in['get']['entity_id'] = $_GET['entity_id'];// 对应实体id
        $data_in['get']['p_type'] = $_GET['p_type'];// 页面类型
        //--------GET参数处理[end]--------
        
        $data_def['icon_book'] = scap_html::image(array('src' => scap_get_image_url('module_touchview_book', 'book-icon.png'), 'style' => 'vertical-align:middle;'));
        $data_def['icon_section'] = scap_html::image(array('src' => scap_get_image_url('module_touchview_book', 'section-icon.png'), 'style' => 'vertical-align:middle;'));
        $data_def['icon_page'] = scap_html::image(array('src' => scap_get_image_url('module_touchview_book', 'page-icon.png'), 'style' => 'vertical-align:middle;'));
        
        // 根据输入实体id辨别对象类型
        if (empty($data_in['get']['entity_id']))// 初始根节点
        {
            $book = new book($data_in['get']['b_id']);
            $data_db['content_book'] = $book->read();
            $data_render .= <<<HTML
<li rel='book' alt='{$data_db['content_book']['b_name']}' id='id-{$data_in['get']['b_id']}' class="jstree-closed" entity_id='{$data_def['ENTITY_ID_TOUCHVIEW_BOOK']}' p_type='0'>
<span class="menu-book fg-button" id='menu-id-{$data_in['get']['b_id']}'>{$data_def['icon_book']}</span>
<a class="book" href="#">{$data_db['content_book']['b_name']}</a>
</li>
HTML;
        }
        else
        {
            // 查询page
            $data_db['list_page'] = page::get_child_page_list($data_in['get']['id']);
            
            foreach($data_db['list_page'] as $v)
            {
                if ($v['p_type'] == TYPE_PAGE_NORMAL)
                {
                    $data_render .= <<<HTML
<li rel='page' id='id-{$v['p_id']}' alt='{$v['p_sort_sn']}' title='{$v['p_sort_sn']}' entity_id='{$data_def['ENTITY_ID_TOUCHVIEW_PAGE']}' p_type='{$v['p_type']}'>
	<span class="menu-page fg-button" id='menu-id-{$v['p_id']}'>{$data_def['icon_page']}</span>
	<a class="page" href="#">第{$v['p_sort_sn']}页</a>
</li>
HTML;
                }
                elseif ($v['p_type'] == TYPE_PAGE_SECTION)
                {
                    // 是否显示 + 号展开符号
                    $data_def['class_icon_closed'] = (page::check_child_page_exist($v['p_id'])) ? "jstree-closed" : "";
                    
                    $data_render .= <<<HTML
<li rel='section' class="{$data_def['class_icon_closed']}" id='id-{$v['p_id']}' alt='{$v['p_sort_sn']}' title='{$v['p_sort_sn']}' entity_id='{$data_def['ENTITY_ID_TOUCHVIEW_PAGE']}' p_type='{$v['p_type']}'>
	<span class="menu-section fg-button" id='menu-id-{$v['p_id']}'>{$data_def['icon_section']}</span>
	<a class="page" href="#">{$v['p_name']}</a>
	第{$v['p_sort_sn']}页
</li>
HTML;
                }
            }
            
        }
        
        echo $data_render;
    }
    
    /**
     * book操作菜单
     */
    public function menu_book()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['b_id'] = $_GET['b_id'];// book id
        //--------GET参数处理[end]--------
        
        //--------模版赋值[start]--------
        $data_render['b_id'] = $data_in['get']['b_id'];
        $data_render['id'] = $data_in['get']['b_id'];
        $data_render['link_book_view'] = scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'edit_book'), array('b_id' => $data_in['get']['b_id'], 'act' => STAT_ACT_EDIT));
        $data_render['link_book_preview'] = scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book_front', 'method' => 'book'), array('b_id' => $data_in['get']['b_id'], 'force_update' => 1));  
        $data_render['link_export_book_file'] = scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui_book', 'method' => 'export_book_file'), array('b_id' => $data_in['get']['b_id']));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $this->render_tpl('book.structure.menu.book.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * section操作菜单
     */
    public function menu_section()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['p_id'] = $_GET['p_id'];// section id
        //--------GET参数处理[end]--------
        
        //--------模版赋值[start]--------
        $data_render['id'] = $data_in['get']['p_id'];
        $data_render['parent_id'] = page::get_parentid_from_id($data_in['get']['p_id']);
        $data_render['link_section_view'] = scap_get_url(array('module' => 'module_touchview_page', 'class' =>'ui_page', 'method' => 'edit_page_content'), array('p_id' => $data_in['get']['p_id'], 'act' => STAT_ACT_EDIT));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $this->render_tpl('book.structure.menu.section.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * page操作菜单
     */
    public function menu_page()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['p_id'] = $_GET['p_id'];// page id
        //--------GET参数处理[end]--------
        
        //--------模版赋值[start]--------
        $data_render['p_id'] = $data_in['get']['p_id'];
        $data_render['id'] = $data_in['get']['p_id'];
        $data_render['parent_id'] = page::get_parentid_from_id($data_in['get']['p_id']);
        $data_render['link_page_view'] = scap_get_url(array('module' => 'module_touchview_page', 'class' =>'ui_page', 'method' => 'edit_page_content'), array('p_id' => $data_in['get']['p_id'], 'act' => STAT_ACT_EDIT));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $this->render_tpl('book.structure.menu.page.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 创建节点
     */
    public function create_node()
    {
        echo '';
    }
    
	/**
     * 删除节点
     */
    public function remove_node()
    {
        echo '';
    }
    
	/**
     * 清空章节
     */
    public function empty_section()
    {
        echo '';
    }
    
	/**
     * 强制删除章节
     */
    public function force_remove_section()
    {
        echo '';
    }
    
	/**
     * 重命名节点
     */
    public function rename_node()
    {
        echo '';
    }
    
    /**
     * 移动节点
     */
    public function move_node()
    {
        echo '';
    }
}
?>