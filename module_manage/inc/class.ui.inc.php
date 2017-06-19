<?php
/**
 * description: 系统管理ui
 * create time: 2006-8-2 12:47:05
 * @version $Id: class.ui.inc.php 145 2013-08-22 05:43:43Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

class ui extends scap_module_ui
{
    function __construct()
    {
        parent::__construct();

        scap_load_module_function('module_g_00', 'g');
    }
    
    /**
     * 模块默认界面
     */
    public function index_default()
    {
        //--------构造界面输出[start]--------
        $this->output_html("系统管理", 'default.tpl');
        //--------构造界面输出[end]----------
    }

    /**
     * 帐户编辑
     */
    public function edit_account()
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
        $data_in['get']['a_s_id'] = $_GET['a_s_id']; // 帐户系统id
        //--------GET参数处理[end]--------

        //--------操作类型分类处理[start]--------
        switch($data_in['get']['act'])
        {
            case STAT_ACT_CREATE:
                $data_flag['is_create'] = true;
                $data_def['title'] = "创建新帐户";
                $data_def['text_act'] = "创建";
                break;
            case STAT_ACT_EDIT:
                $data_flag['is_edit'] = true;
                $data_def['title'] = "编辑帐户";
                $data_def['text_act'] = "编辑";
                break;
            case STAT_ACT_VIEW:
                $data_flag['is_view'] = true;
                $data_def['title'] = "查看帐户";
                $data_def['text_act'] = "查看";
                break;
        }
        //--------操作类型分类处理[end]--------

        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'account_save':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = trimarray($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('a_s_id' => $_GET['a_s_id'], 'act' => STAT_ACT_EDIT));
                }
                break;
            case 'account_set_pw':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = trimarray($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('a_s_id' => $_GET['a_s_id'], 'act' => STAT_ACT_EDIT));
                }
                break;
            case 'account_remove':
                if ($this->current_event_result === false)
                {
                    scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('a_s_id' => $_GET['a_s_id'], 'act' => STAT_ACT_EDIT));
                }
                elseif ($this->current_event_result === true)
                {
                    g_redirect_feedback_info_page("删除操作成功。");
                }
                break;
        }
        //--------消息/事件处理[end]--------

        //--------数据表查询操作[start]--------
        if ($data_flag['is_view'] || $data_flag['is_edit'])
        {
            try
            {
                $account = new sys_account($data_in['get']['a_s_id']);
                $data_db['content'] = $account->read();
            }
            catch(Exception $e)
            {
                exit($e->getMessage());
            }
        }
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
        $data_flag['btn_save']['show'] = ($data_flag['is_create'] || $data_flag['is_edit']);
        $data_flag['btn_close']['show'] = $data_flag['is_view'];
        $data_flag['btn_set_pw']['show'] = $data_flag['is_edit'];
        $data_flag['link_log']['show'] = $data_flag['is_view'] || $data_flag['is_edit'];
        $data_flag['link_remove']['show'] = $data_flag['is_view'] || $data_flag['is_edit'];
        $data_flag['link_edit']['show'] = $data_flag['is_view'];
        $data_flag['link_view']['show'] = $data_flag['is_edit'];
        //--------html元素只读/必填/显示等逻辑设定[end]--------

        //--------模版赋值[start]--------
        //密码设置框显示标记
        $data_out['flag_show_set_pw'] = ($data_flag['is_create'] || $data_flag['is_edit']);

        if ($data_flag['is_create'] || $data_flag['is_edit'])
        {
            if ($data_flag['is_create'])
            {
                $data_out['tip_password'] = scap_html::scap_info_sup_tip('如不填写口令设置，系统将默认为当前所创帐户口令为空，这是不推荐的。');
            }
            elseif($data_flag['is_edit'])
            {
                $data_out['tip_password'] = scap_html::scap_info_sup_tip('在编辑状态下设置帐户口令，请使用\'设置口令\'按钮让口令生效，\'保存\'按钮将不会对口令进行设置。');
            }

            $data_out['a_new_password'] = scap_html::input_password(array('name' => 'content[a_new_password]', 'size' => 15, 'maxlength' => $this->elements_maxlength['account']['password']));
            $data_out['a_confirm_new_password'] = scap_html::input_password(array('name' => 'content[a_confirm_new_password]', 'size' => 15, 'maxlength' => $this->elements_maxlength['account']['password']));
        }

        $data_out['a_c_login_id'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip(TEXT_TIP_EDIT_LOGIN_ID, array('BALLOON' => "'true'")), 'name' => 'content[a_c_login_id]', 'value' => $data_in['content']['a_c_login_id'], 'size' => 25, 'maxlength' => $this->elements_maxlength['account']['login_id']), ($data_flag['is_view'] || $data_flag['is_edit']), false, true, ($data_flag['is_create']));
        $data_out['a_c_display_name'] = scap_html::input_text(array('name' => 'content[a_c_display_name]', 'value' => $data_in['content']['a_c_display_name'], 'size' => 25, 'maxlength' => $this->elements_maxlength['account']['display_name']), $data_flag['is_view'], false, true, ($data_flag['is_create'] || $data_flag['is_edit']));
        $data_out['a_c_note'] = scap_html::textarea(array('name' => 'content[a_c_note]', 'rows' => 3, 'wrap' => 'soft'), $data_in['content']['a_c_note'], $data_flag['is_view']);

        $data_out['a_s_status'] = scap_html::select(array('name' => 'content[a_s_status]'), array(STAT_ACCOUNT_STOP => TEXT_STOP, STAT_ACCOUNT_NORMAL => TEXT_NORMAL), array($data_in['content']['a_s_status']), $data_flag['is_view']);

        $data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => TEXT_SAVE, 'title' => TEXT_TIP_BTN_SAVE), false, $data_flag['btn_save']['show']);
        $data_out['btn_close'] = scap_html::input_button(array('name' => 'button[btn_close]', 'value' => TEXT_CLOSE, 'title' => TEXT_TIP_BTN_CLOSE, 'onclick' => 'window.close();'), false, $data_flag['btn_close']['show']);
        $data_out['btn_set_pw'] = scap_html::input_submit(array('name' => 'button[btn_set_pw]', 'value' => TEXT_SET_PASSWORD, 'title' => TEXT_TIP_BTN_SET_PASSWORD), false, $data_flag['btn_set_pw']['show']);

        $data_out['link_remove'] = scap_html::anchor( array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui', 'method' => 'edit_account'), array('a_s_id' => $data_in['get']['a_s_id'], 'act' => STAT_ACT_REMOVE)),
                                                                'title' => '删除',
                                                                'onclick' => "return confirm('确认删除该数据么,此过程将不可恢复!');"),
                                                        '删除',
        $data_flag['link_remove']['show']
        );
        $data_out['link_edit'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui', 'method' => 'edit_account'), array('a_s_id' => $data_in['get']['a_s_id'], 'act' => STAT_ACT_EDIT)),
                                                                'title' => '编辑'),
                                                        '编辑',
        $data_flag['link_edit']['show']
        );
        $data_out['link_view'] = scap_html::anchor(   array(  'href' => scap_get_url(array('module' => $this->current_module_id, 'class' =>'ui', 'method' => 'edit_account'), array('a_s_id' => $data_in['get']['a_s_id'], 'act' => STAT_ACT_VIEW)),
                                                                'title' => '查看',
                                                                'target' => '_blank'),
                                                        '查看',
        $data_flag['link_view']['show']
        );
        $data_out['link_log'] = $data_flag['link_log']['show'] ? g_create_object_log_link($data_in['get']['a_s_id'], '操作日志') : '';
        //--------模版赋值[end]--------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
	    $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'edit.account.tpl', $data_out);
        //--------构造界面输出[end]----------      
    }

    /**
     * 帐户索引
     */
    public function index_account()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '帐户索引';
        $data_def['text_menu'] = '帐户管理';

        //--------查询参数处理[start]--------
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)

        // 处理传入的查询参数数组:$_REQUEST['search']
        if (!empty($_REQUEST['search']))
        {
            foreach($_REQUEST['search'] as $k => $v)
            {
                // 解码已编码的查询参数
                $data_in['search'][$k] = trim(urldecode($v));
            }
        }

        // 具体处理不同参数的传递
        if (strlen($data_in['search']['login_id']) > 0)
        {
            $data_db['where'] .= " AND a_c_login_id LIKE '%{$data_in['search']['login_id']}%'";
            $data_in['extra_vars']['search[login_id]'] = $data_in['search']['login_id'];
        }

        if (strlen($data_in['search']['display_name']) > 0)
        {
            $data_db['where'] .= " AND a_c_display_name LIKE '%{$data_in['search']['display_name']}%'";
            $data_in['extra_vars']['search[display_name]'] = $data_in['search']['display_name'];
        }

        if (!empty($data_in['search']['status']))
        {
            $data_db['where'] .= " AND a_s_status = {$data_in['search']['status']}";
            $data_in['extra_vars']['search[status]'] = $data_in['search']['status'];
        }
        //--------查询参数处理[end]----------

        //--------分页/步长处理[start]--------
        $data_def['steps_options'] = array(10, 20, 40, 80);// 设置分页步长选项
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
            case 'a_c_login_id':
            case 'a_c_display_name':
            case 'a_s_status':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'a_c_login_id';// 默认排序的列名
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];

        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'DESC';// 默认排序方式
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
        //--------排序参数处理[end]--------

        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);

        // 避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }

        //--------数据表查询操作[start]--------
        // 构造查询所需参数
        $data_db['query'] = array(
                'id' => 'a_c_login_id',
                'sql' => "SELECT * FROM ".NAME_T_SYS_ACCOUNTS." WHERE (1=1 {$data_db['where']})",
                'order' => $data_in['taxis']['order'],
                'sort' => $data_in['taxis']['sort'],
                'start' => $data_in['split_page']['start'],
                'steps' => $data_in['split_page']['steps'],
        );

        // 执行查询,并返回查询集合到$data_db['content']
        $data_db['content'] = scap_entity::query($data_db['query']);
        //--------数据表查询操作[end]--------

        //--------模版赋值[start]--------
        if (count($data_db['content']) == 0)
        {
            $data_out['text_no_data'] = "没有相关数据。";
        }
        else
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_out['data_list'][$i] = $v;
                $data_out['data_list'][$i]['sn'] = $i+1;
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);

                switch($v['a_s_status'])
                {
                    case STAT_ACCOUNT_STOP:
                        $data_out['data_list'][$i]['a_s_status'] = TEXT_STOP;
                        break;
                    case STAT_ACCOUNT_NORMAL:
                        $data_out['data_list'][$i]['a_s_status'] = TEXT_NORMAL;
                        break;
                    default:
                        $data_out['data_list'][$i]['a_s_status'] = TEXT_UNKNOWN;
                }

                $data_out['data_list'][$i]['op'] = scap_html::anchor( array('target' => '_blank', 'href' => scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'edit_account'), array('a_s_id' => $v['a_s_id'], 'act' => STAT_ACT_VIEW)), 'title' => '查看'),
                scap_html::image(array('src' => scap_get_image_url('module_basic', 'view.gif')))
                );
                $i ++;
            }
        }

        // [分页功能输出]
        $data_out['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
        $data_out['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
        $data_out['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
        $data_out['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
        $data_out['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);

        // [索引头部信息输出]
        $data_out['head_login_id'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'a_c_login_id', $data_in['taxis']['order'], "登录名称", $data_in['extra_vars']);
        $data_out['head_display_name'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'a_c_display_name', $data_in['taxis']['order'], "显示名称", $data_in['extra_vars']);

        $data_out['head_status'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'a_s_status', $data_in['taxis']['order'], "状态", $data_in['extra_vars']);
        $data_out['head_op'] = "操作";

        // [查询项目输出]
        $data_out['search_login_id'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip('输入对应系统ID即可查询，如果留空，则不对该项进行筛选。', array('BALLOON' => "'true'")), 'name' => 'search[login_id]', 'id' => 'search_login_id' , 'value' => $data_in['search']['login_id'], 'size' => 20, 'maxlength' => $this->elements_maxlength['common']['login_id']));
        $data_out['search_display_name'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip('输入对应系统ID即可查询，如果留空，则不对该项进行筛选。', array('BALLOON' => "'true'")), 'name' => 'search[display_name]', 'id' => 'search_name' , 'value' => $data_in['search']['display_name'], 'size' => 20, 'maxlength' => $this->elements_maxlength['common']['display_name']));        

        $data_out['search_status'] = scap_html::select(array('onmouseover' => scap_html::scap_wz_tooltip(TEXT_TIP_SEARCH_STATUS, array('BALLOON' => "'true'")), 'name' => 'search[status]'), array(0 => '-', STAT_ACCOUNT_STOP => TEXT_STOP, STAT_ACCOUNT_NORMAL => TEXT_NORMAL), array($data_in['search']['status']));

        $data_out['btn_search'] = scap_html::input_submit(array('name' => 'button[search]', 'value' => '查询', 'title' => TEXT_TIP_BTN_SEARCH));
        $data_out['btn_reset'] = scap_html::input_button(array('name' => 'button[reset]', 'value' => '重置', 'onclick' =>"clearForm(form_index,'sel_page,sel_steps');", 'title' => TEXT_TIP_BTN_RESET));

        $data_out['link_add_account'] = scap_html::anchor(array('class' => 'scap_button', 'href' => scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'edit_account'), array('act' => STAT_ACT_CREATE)), 'title' => '创建一个新的系统帐户'), '创建系统帐户');
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'index.account.tpl', $data_out);
        //--------构造界面输出[end]----------
    }

    /**
     * 参数编辑
     */
    public function edit_config()
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

        //--------获取GET参数[start]--------
        $data_in['get']['module'] = $_GET['module'];
        $data_in['get']['key'] = $_GET['key'];
        $data_in['get']['act'] = $_GET['act'];// 操作类型
        //--------获取GET参数[end]--------

        // 获取对应自定义参数的属性信息
        $data_def['config_item'] = scap_get_special_custom_value_property($data_in['get']['module'], $data_in['get']['key']);

        //--------操作类型分类处理[start]--------
        switch($data_in['get']['act'])
        {
            case STAT_ACT_EDIT:
                $data_flag['is_edit'] = true;
                $data_def['title'] .= "编辑";
                break;
        }
        //--------操作类型分类处理[end]--------

        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'account_save':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = trimarray($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                    g_redirect_feedback_info_page("参数设置成功。");
                }
                break;
        }
        //--------消息/事件处理[end]--------

        //--------数据表查询操作[start]--------
        
        if ($data_flag['is_edit'])
        {
            try
            {
                $config = new sys_config($data_in['get']['module'], $data_in['get']['key']);
                $data_db['content'] = $config->read();
            }
            catch(Exception $e)
            {
                exit($e->getMessage());
            }
        }
        //--------数据表查询操作[end]--------

        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }

        if (!empty($data_in['post']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        //--------影响界面输出的$data_in数据预处理[end]--------

        //--------模版赋值[start]--------
        $data_out['config_module'] = scap_lang_module_name($data_def['config_item']['config_module']);
        $data_out['config_cat'] = $data_def['config_item']['config_cat'];
        $data_out['config_name'] = $data_def['config_item']['config_name'];

        switch($data_def['config_item']['set_type'])
        {
            case TYPE_CONFIG_INPUT_TEXT:
                $data_out['c_c_value'] = scap_html::input_text(array('name' => 'content[c_c_value]', 'value' => $data_in['content']['c_c_value'], 'size' => $data_def['config_item']['parameter']['size'], 'maxlength' => $data_def['config_item']['parameter']['maxlength']), !$data_flag['is_edit']);
                break;
            case TYPE_CONFIG_TEXTAREA:
            case TYPE_CONFIG_RICH_TEXT:
                $data_out['c_c_value'] = scap_html::textarea(array('name' => 'content[c_c_value]', 'cols' => $data_def['config_item']['parameter']['cols'], 'rows' => $data_def['config_item']['parameter']['rows'], 'wrap' => $data_def['config_item']['parameter']['wrap']), $data_in['content']['c_c_value'], !$data_flag['is_edit']);
                break;
            case TYPE_CONFIG_SELECT:
                $data_out['c_c_value'] = scap_html::select(array('name' => 'content[c_c_value]'), $data_def['config_item']['parameter']['options'], array($data_in['content']['c_c_value']), !$data_flag['is_edit']);
                break;
        }

        $data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => TEXT_SAVE, 'title' => TEXT_TIP_BTN_SAVE), false, $data_flag['is_edit']);
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'edit.config.tpl', $data_out);
        //--------构造界面输出[end]----------
    }

    /**
     * 参数索引
     */
    public function index_config()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '索引';
        $data_def['text_menu'] = '参数管理';

        //--------查询参数处理[start]--------
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)

        // 获取前台未停用模块列表
        $data_def['module_list'] = scap_get_module_list("ml_s_status=".STAT_MODULE_NORMAL);
        foreach($data_def['module_list'] as $k => $v)
        {
            $temp_config = scap_get_module_custom_values_define($k);
            if (empty($temp_config))
            {
                unset($data_def['module_list'][$k]);
            }
            else
            {
                $data_def['module_list'][$k] = scap_lang_module_name($k);
            }
        }
        $data_def['module_list'] = array('' => '-') + $data_def['module_list'];

        // 处理传入的查询参数数组:$_REQUEST['search']
        if (!empty($_REQUEST['search']))
        {
            foreach($_REQUEST['search'] as $k => $v)
            {
                // 解码已编码的查询参数
                $data_in['search'][$k] = trim(urldecode($v));
            }
        }

        // 具体处理不同参数的传递
        if (strlen($data_in['search']['config_module']) > 0)
        {
            // 获取模块的的配置项定义
            $data_in['config_items'] = scap_get_module_custom_values_define($data_in['search']['config_module']);

            $data_db['where'] .= " AND c_s_module = '{$data_in['search']['config_module']}'";
            $data_in['extra_vars']['search[config_module]'] = $data_in['search']['config_module'];
        }
        //--------查询参数处理[end]----------

        //--------排序参数处理[start]--------
        $data_in['taxis'] = array();// 排序参数输入数据
        switch($_REQUEST['order'])
        {
            case 'config_name':
            case 'config_cat':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'config_name';// 默认排序的列名
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];

        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'DESC';
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];

        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);

        // 避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }

        //--------数据表查询操作[start]--------
        if (count($data_in['config_items']) > 0)
        {
            $data_db['where'] .= " AND c_s_key IN(";
            foreach ($data_in['config_items'] as $k => $v)
            {
                $data_db['where'] .= "'{$v['config_key']}',";
            }
            $data_db['where'] = rtrim($data_db['where'], ',');
            $data_db['where'] .= ")";

            // 配置参数进行排序
            \scap\module\g_tool\matrix::musort($data_in['config_items'], $data_in['taxis']['order'], $data_in['taxis']['sort']);
        }
        else
        {
            $data_db['where'] = " AND 0";
        }

        // 构造查询所需参数
        $data_db['query'] = array(
                'id' => 'c_s_key',
                'sql' => "SELECT * FROM ".NAME_T_SYS_CONFIG." WHERE (1=1 {$data_db['where']})",
                'order' => '',
                'sort' => '',
                'start' => -1,
                'steps' => -1,
        );

        // 执行查询,并返回查询集合到$data_db['content']
        $data_db['content'] = scap_entity::query($data_db['query'], false);
        //--------数据表查询操作[end]--------

        //--------模版赋值[start]--------
        if (count($data_in['config_items']) == 0)
        {
            $data_out['text_no_data'] = "没有相关数据。";
        }
        else
        {
            $i = 0;
            foreach($data_in['config_items'] as $k => $v)
            {
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);

                $data_out['data_list'][$i]['config_name'] = $v['config_name'];
                $data_out['data_list'][$i]['config_cat'] = $v['config_cat'];
                $data_out['data_list'][$i]['config_module'] = scap_lang_module_name($v['config_module']);

                if (isset($data_db['content'][$v['config_key']]['c_c_value']))
                {
                    $data_out['data_list'][$i]['c_c_value'] = scap_html::label(array('title' => \scap\module\g_tool\string::get_clean_substr($data_db['content'][$v['config_key']]['c_c_value'], 0, 200)), \scap\module\g_tool\string::get_clean_substr($data_db['content'][$v['config_key']]['c_c_value'], 0, 50));
                }
                else// 如果该配置数据还未写入db,则以默认值写入db
                {
                    if (!sys_config::create(array('c_s_module' => $v['config_module'], 'c_s_key' => $v['config_key'], 'c_c_value' => $v['default_value'])))
                    {
                        scap_insert_sys_info('error', sprintf("向系统添加配置数据【%s】时失败！错误信息：%s", $v['config_name'], scap_db_error_msg()));
                    }
                    else
                    {
                        $data_out['data_list'][$i]['c_c_value'] = $v['default_value'];
                        scap_insert_sys_info('tip', sprintf("系统自动添加配置数据【%s】成功。", $v['config_name']));
                    }
                }

                // 如果输入类型为选择框
                if ($v['set_type'] == TYPE_CONFIG_SELECT)
                {
                    $data_out['data_list'][$i]['c_c_value'] = $v['parameter']['options'][$data_db['content'][$v['config_key']]['c_c_value']];
                }

                $data_out['data_list'][$i]['op'] = scap_html::anchor(
                                                            array('href' => scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'edit_config'), array('act' => STAT_ACT_EDIT, 'module' => $v['config_module'], 'key' => $v['config_key'])), 'title' => '编辑'),
                                                            scap_html::image(array('src' => scap_get_image_url('module_basic', 'edit.gif')))
                                                    );
                $i ++;
            }
        }

        // [索引头部信息输出]
        $data_out['head_name'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'config_name', $data_in['taxis']['order'], "名称", $data_in['extra_vars']);
        $data_out['head_cat'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'config_cat', $data_in['taxis']['order'], "类别", $data_in['extra_vars']);
        $data_out['head_module'] = "模块";
        $data_out['head_value'] = "数值";
        $data_out['head_op'] = "操作";

        // [查询项目输出]
        $data_out['search_config_module'] = scap_html::select(array('onmouseover' => scap_html::scap_wz_tooltip(TEXT_TIP_SEARCH_CONFIG_MODULE, array('BALLOON' => "'true'")), 'name' => 'search[config_module]', 'onchange' => 'this.form.submit();'), $data_def['module_list'], array($data_in['search']['config_module']));
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'index.config.tpl', $data_out);
        //--------构造界面输出[end]----------
    }

    /**
     *模块属性索引 
     */
    public function index_module()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '索引/编辑';
        $data_def['text_menu'] = '模块管理';

        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据

        //--------影响界面输出的$data_in数据预处理[start]--------
        
        // 获取系统本地模块信息
        $data_in['local'] = scap_get_module_local_list();
        
        // 获取db注册的模块信息
        $data_in['register'] = scap_get_module_register_list();
        
        $temp = array_merge(array_flip(array_keys($data_in['local'])), array_flip(array_keys($data_in['register'])));
        
        // 模块列表数据
        $data_in['list'] = array();
        
        $i = 0;
        foreach($temp as $k => $v)
        {
            $data_in['list'][$i]['module_id'] = $k;
            
            $data_in['list'][$i]['module_name'] = isset($data_in['local'][$k]) ? scap_lang_module_name($k) : '-';
            $data_in['list'][$i]['module_property'] = isset($data_in['local'][$k]) ? $GLOBALS['scap']['text']['module_basic']['prop_module'][$data_in['local'][$k]['property']]: '-';
            $data_in['list'][$i]['module_status_local'] = isset($data_in['local'][$k]) ? '正常' : '未发现';
            $data_in['list'][$i]['module_version_local'] = isset($data_in['local'][$k]) ? $data_in['local'][$k]['version'] : '-';
            $data_in['list'][$i]['module_version_register'] = isset($data_in['register'][$k]) ? $data_in['register'][$k]['ml_c_version'] : '-';
            $data_in['list'][$i]['module_status_register'] = isset($data_in['register'][$k]) ? '已注册' : '未注册';
            $data_in['list'][$i]['module_status_active'] = isset($data_in['register'][$k]) ? $GLOBALS['scap']['text']['module_basic']['stat_module'][$data_in['register'][$k]['ml_s_status']] : '-';
            $data_in['list'][$i]['module_order'] = isset($data_in['register'][$k]) ? $data_in['register'][$k]['ml_c_order'] : '-';
            
            if ($data_in['local'][$k]['property'] == PROP_MODULE_BACK)
            {
                $data_in['list'][$i]['module_order'] = '-';
            }
            
            $i ++;
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------排序参数处理[start]--------
        $data_in['taxis'] = array();// 排序参数输入数据
        switch($_REQUEST['order'])
        {
            case 'module_id':
            case 'module_name':
            case 'module_property':
            case 'module_version_local':
            case 'module_version_register':
            case 'module_status_local':
            case 'module_status_register':
            case 'module_status_active':
            case 'module_order':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'module_id';// 默认排序的列名
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];
        
        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'ASC';
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
        //--------排序参数处理[end]--------
        
        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);
        
        // 避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }
        
        //--------模版赋值[start]--------
        if (count($data_in['list']) == 0)
        {
            $data_out['text_no_data'] = "没有相关数据。";
        }
        else
        {
            // [配置参数进行排序]
            \scap\module\g_tool\matrix::musort($data_in['list'], $data_in['taxis']['order'], $data_in['taxis']['sort']);

            $i = 0;
            foreach($data_in['list'] as $k => $v)
            {
                $data_out['data_list'][$i] = $v;
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
                $data_out['data_list'][$i]['op'] = ''; 
                
                // 排序操作
                if (is_numeric($v['module_order']))
                {
                    $data_out['data_list'][$i]['module_order'] = scap_html::input_text(array('name' => "input_order[{$v['module_id']}]", 'value' => $v['module_order'],'size' => '2', 'maxlength' => '2'));
                }
                
                // 注册操作
                if (isset($data_in['local'][$v['module_id']]) && !isset($data_in['register'][$v['module_id']]))
                {
                    $data_out['data_list'][$i]['op'] .= scap_html::input_submit(array('name' => "btn_register[{$v['module_id']}]", 'value' => '注册'));
                }
                
                // 注销操作
                if (isset($data_in['register'][$v['module_id']]))
                {
                    $data_out['data_list'][$i]['op'] .= scap_html::input_submit(array('name' => "btn_unregister[{$v['module_id']}]", 'value' => '注销'));
                }
                
                // 更新操作
                if (isset($data_in['local'][$v['module_id']]) && isset($data_in['register'][$v['module_id']]) && strcmp($data_in['register'][$v['module_id']]['ml_c_version'], $data_in['local'][$v['module_id']]['version']) < 0)
                {
                    $data_out['data_list'][$i]['op'] .= scap_html::input_submit(array('name' => "btn_update[{$v['module_id']}]", 'value' => '更新'));
                }
                
                // 启用/停用操作
                if ($data_in['register'][$v['module_id']]['ml_s_status'] == STAT_MODULE_NORMAL)
                {
                    $data_out['data_list'][$i]['op'] .= scap_html::input_submit(array('name' => "btn_stop[{$v['module_id']}]", 'value' => '停用'));
                }
                elseif ($data_in['register'][$v['module_id']]['ml_s_status'] == STAT_MODULE_STOP)
                {
                    $data_out['data_list'][$i]['op'] .= scap_html::input_submit(array('name' => "btn_start[{$v['module_id']}]", 'value' => '启用'));
                }
                $i ++;
            }
        }

        // [索引头部信息输出]
        $data_out['head_id'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_id', $data_in['taxis']['order'], "标识", $data_in['extra_vars']);
        $data_out['head_name'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_name', $data_in['taxis']['order'], "名称", $data_in['extra_vars']);
        $data_out['head_property'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_property', $data_in['taxis']['order'], "属性", $data_in['extra_vars']);
        $data_out['head_version'] = "版本";
        $data_out['head_version_local'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_version_local', $data_in['taxis']['order'], "本地", $data_in['extra_vars']);
        $data_out['head_version_register'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_version_register', $data_in['taxis']['order'], "注册", $data_in['extra_vars']);
        $data_out['head_status'] = "状态";
        $data_out['head_status_local'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_status_local', $data_in['taxis']['order'], "本地", $data_in['extra_vars']);
        $data_out['head_status_register'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_status_register', $data_in['taxis']['order'], "注册", $data_in['extra_vars']);
        $data_out['head_active'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_status_active', $data_in['taxis']['order'], "启用状态", $data_in['extra_vars']);
        $data_out['head_order'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'module_order', $data_in['taxis']['order'], "显示顺序", $data_in['extra_vars']);
        $data_out['head_op'] = "操作";
        
        $data_out['btn_order'] = scap_html::input_submit(array('name' => 'button[btn_order]', 'value' => '排序'));
        //--------模版赋值[end]----------        
        
        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'index.module.tpl', $data_out);
        //--------构造界面输出[end]----------      
    }
    
    /**
     * 权限索引
     */
    public function index_acl()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据

        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        $data_in['acl_items'] = array();// 模块acl定义列表
        $data_in['account_list'] = array();// 帐号列表数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '索引/编辑';// 当前界面标题设置
        $data_def['text_menu'] = '权限管理';

        // 获取前台模块列表
        $data_def['module_list'] = scap_get_module_list("ml_s_status=".STAT_MODULE_NORMAL." OR ml_s_status=".STAT_MODULE_STOP);
        
        foreach($data_def['module_list'] as $k => $v)
        {
            $temp_acl_define = scap_get_module_acl_define($k);
            if (empty($temp_acl_define))
            {
                unset($data_def['module_list'][$k]);
            }
            else
            {
                $data_def['module_list'][$k] = scap_lang_module_name($k);
            }
        }
        $data_def['module_list'] = array_merge(array('' => '-'), $data_def['module_list']);

        //--------查询参数处理[start]--------
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)

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
        if (strlen($data_in['search']['account']) > 0)
        {
            $data_db['where'] .= " AND (a_c_display_name LIKE '%{$data_in['search']['account']}%' OR a_c_login_id LIKE '%{$data_in['search']['account']}%')";
            $data_in['extra_vars']['search[account]'] = $data_in['search']['account'];
        }
        
        if (strlen($data_in['search']['module']) > 0)
        {
            // 获取模块的的acl定义
            $data_in['acl_items'] = scap_get_module_acl_define($data_in['search']['module']);
            $data_in['extra_vars']['search[module]'] = $data_in['search']['module'];
        }
        
        if ($data_in['search']['filter'] && strlen($data_in['search']['module']) > 0)
        {
            $data_db['where'] .= " AND a_s_id IN (SELECT acl_s_account_id FROM scap_acl WHERE (acl_s_module='{$data_in['search']['module']}' AND acl_c_acl_code > 0 ))";
            $data_in['extra_vars']['search[filter]'] = $data_in['search']['filter'];
        }
        //--------查询参数处理[end]----------

        //--------分页/步长处理-此处为横向分页和步长[start]--------
        $data_def['steps_options'] = array(20, 40, 80);// 设置分页步长选项
        $data_def['step_default'] = $data_def['steps_options'][0];// 默认步长
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
        switch($_REQUEST['order'])
        {
            case 'a_c_display_name':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'a_c_display_name';
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
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);

        //--------消息/事件处理[start]--------
        if ($_POST['button']['btn_save'])
        {
            $data_save = array();
            $flag_save = true;
            $flag_success = true;

            $data_in['post'] = $_POST['check'];
            
            if ($flag_save)
            {
                foreach($_POST['account'] as $v)
                {
                    for ($i = 0; $i < count($data_in['acl_items']); $i ++)
                    {
                        $bit = $i;
                        if (isset($data_in['acl_items'][$bit]))
                        {
                            $data_save[$v]['acl'][$bit] = empty($data_in['post'][$v][$bit]) ? 0 : 1;
                        }
                    }
                    if (!scap_set_acl($data_in['search']['module'], $data_save[$v]['acl'], $v))
                    {
                        $flag_success = false;
                        scap_insert_sys_info('error', sprintf("编辑权限时有错误发生！错误信息：%s", scap_db_error_msg()));
                    }
                }
                
                if ($flag_success)
                {
                    scap_insert_sys_info('tip', sprintf("编辑权限成功。"));
                }
            }
        }
        //--------消息/事件处理[end]--------      

        // 避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }

        //--------数据表查询操作[start]--------
        // 构造查询所需参数
        $data_db['query'] = array(
                'id' => 'a_c_login_id',
                'sql' => "SELECT * FROM ".NAME_T_SYS_ACCOUNTS." WHERE (1=1 {$data_db['where']})",
                'order' => $data_in['taxis']['order'],
                'sort' => $data_in['taxis']['sort'],
                'start' => $data_in['split_page']['start'],
                'steps' => $data_in['split_page']['steps'],
        );

        // 执行查询,并返回查询集合到$data_db['content']
        $data_db['content'] = scap_entity::query($data_db['query']);
        
        //--------数据表查询操作[end]--------

        //--------模版赋值[start]--------
        $data_out['count_col'] = count($data_in['acl_items'])+1;
        $data_flag['show_btn_save'] = !empty($data_in['acl_items']);
        
        // 构造权限位-人员表单的权限head描述信息
        for ($i = 0; $i < count($data_in['acl_items']); $i ++)
        {
            $bit = $i;
            $data_out['acl_def_list'][$i]['acl_name'] = $data_in['acl_items'][$bit]['acl_name'];
            $data_out['acl_def_list'][$i]['acl_name_checkbox'] = scap_html::checkbox(
                        array('id' => "check_{$bit}", 'onclick' => "check_all($bit);", 'title' => '点击批量选择当前权限位。'),
                        false
                        );
        }
        
        if (count($data_db['content']) > 0)
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_out['data_list'][$i] = $v;
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
                
                $data_out['data_list'][$i]['account'] =  "{$v['a_c_display_name']}[{$v['a_c_login_id']}]";
                $data_out['data_list'][$i]['account'] .= scap_html::input_hidden(array('name' => "account[]", 'value' => $v['a_s_id']));  
                
                // 权限显示
                for($j = 0; $j < count($data_in['acl_items']); $j ++)
                {
                    $bit = $j;
                    if (isset($data_in['acl_items'][$bit]))
                    {
                        $data_out['data_list'][$i]['acl'][$j]['check_box'] = scap_html::checkbox(
                            array('name' => "check[{$v['a_s_id']}][$bit]"),
                            scap_check_acl($data_in['search']['module'], $bit, $v['a_s_id'], true)
                        );
                    }
                }
                
                $i ++;
            }
        }
        
        // [分页功能输出]
        $data_out['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
        $data_out['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
        $data_out['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
        $data_out['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
        $data_out['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);
        
        // [索引头部信息输出]
        $data_out['head_account'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'a_c_display_name', $data_in['taxis']['order'], "帐户", $data_in['extra_vars']);

        // [查询项目输出]
        $data_out['search_module'] = scap_html::select(array('onmouseover' => scap_html::scap_wz_tooltip(TEXT_TIP_SEARCH_MODULE, array('BALLOON' => "'true'")), 'name' => 'search[module]', 'onchange' => 'this.form.submit();'), $data_def['module_list'], array($data_in['search']['module']));
        $data_out['search_account'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip('输入对应系统ID即可查询，如果留空，则不对该项进行筛选。', array('BALLOON' => "'true'")), 'name' => 'search[account]', 'id' => 'search_login_id' , 'value' => $data_in['search']['account'], 'size' => 25, 'maxlength' => $this->elements_maxlength['account']['display_name']));
        $data_out['search_filter'] = scap_html::checkbox(array('name' => 'search[filter]', 'id' => 'search_filter'), $data_in['search']['filter']);
        $data_out['search_filter'] .= scap_html::label(array('for' => 'search_filter'), '仅显示有授权账户');

        $data_out['btn_search'] = scap_html::input_submit(array('name' => 'button[search]', 'value' => '查询', 'title' => TEXT_TIP_BTN_SEARCH));
        $data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '保存设置', 'title' => TEXT_TIP_BTN_SAVE), false, $data_flag['show_btn_save']);
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'index.acl.tpl', $data_out);
        //--------构造界面输出[end]----------
    }
    
    /**
     * 系统日志索引
     */
    public function index_log()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '系统日志';
        $data_def['text_menu'] = '系统日志';

        //--------查询参数处理[start]--------
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)

        // 处理传入的查询参数数组:$_REQUEST['search']
        if (!empty($_REQUEST['search']))
        {
            foreach($_REQUEST['search'] as $k => $v)
            {
                // 解码已编码的查询参数
                $data_in['search'][$k] = trim(urldecode($v));
            }
        }

        //-----------数据预处理---------[start]---------
        if (empty ($data_in['search']['time_from']))
        {
            $data_in['search']['time_from'] = date('Y-m-01 00:00', strtotime(date("Y-m-d")));
        }

        if (empty ($data_in['search']['time_to']))
        {
            $data_in['search']['time_to'] = date('Y-m-t 23:59', strtotime(date("Y-m-d")));
        }
        //-----------数据预处理---------[end]----------

        // 具体处理不同参数的传递
        if (strlen($data_in['search']['module']) > 0)
        {
            $data_db['where'] .= " AND l_module LIKE '%{$data_in['search']['module']}%'";
            $data_in['extra_vars']['search[module]'] = $data_in['search']['module'];
        }

        if (strlen($data_in['search']['from']) > 0)
        {
            $data_db['where'] .= " AND l_from LIKE '%{$data_in['search']['from']}%'";
            $data_in['extra_vars']['search[from]'] = $data_in['search']['from'];
        }

        if (strlen($data_in['search']['operator_info']) > 0)
        {
            $data_db['where'] .= " AND l_operator_info LIKE '%{$data_in['search']['operator_info']}%'";
            $data_in['extra_vars']['search[operator_info]'] = $data_in['search']['operator_info'];
        }

        if (!empty($data_in['search']['act_type']))
        {
            $data_db['where'] .= " AND l_act_type = '{$data_in['search']['act_type']}'";
            $data_in['extra_vars']['search[act_type]'] = $data_in['search']['act_type'];
        }

        if (!empty($data_in['search']['act_result']))
        {
            $data_db['where'] .= " AND l_act_result = '{$data_in['search']['act_result']}'";
            $data_in['extra_vars']['search[act_result]'] = $data_in['search']['act_result'];
        }

        if(!empty($data_in['search']['time_from']))
        {
            $data_db['where'] .= " AND l_time >= '{$data_in['search']['time_from']}'";
            $data_in['extra_vars']['search[time_from]'] = $data_in['search']['time_from'];
        }
        if(!empty($data_in['search']['time_to']))
        {
            $data_db['where'] .= " AND l_time <= '{$data_in['search']['time_to']}'";
            $data_in['extra_vars']['search[time_to]'] = $data_in['search']['time_to'];
        }
        //--------查询参数处理[end]----------

        //--------分页/步长处理[start]--------
        $data_def['steps_options'] = array(20, 40, 80, 120);// 设置分页步长选项
        $data_def['step_default'] = $data_def['steps_options'][1];// 默认步长
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
            case 'l_time':
            case 'l_module':
            case 'l_operator_info':
            case 'l_operator_type':
            case 'l_from':
            case 'l_act_type':
            case 'l_act_object_type':
            case 'l_act_object_info':
            case 'l_act_result':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'l_time';// 默认排序的列名
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];

        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'DESC';// 默认排序方式
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
        //--------排序参数处理[end]--------

        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);

        //--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[start]--------
        if (!empty($_POST))
        {
            // 将post数据及时转化为get数据
            scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
        }
        //--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[end]--------

        //--------数据表查询操作[start]--------
        // 构造查询所需参数
        $data_db['query'] = array(
                        'id' => 'l_id',
                        'sql' => "SELECT * FROM scap_log WHERE (1=1 {$data_db['where']})",
                        'order' => $data_in['taxis']['order'],
                        'sort' => $data_in['taxis']['sort'],
                        'start' => $data_in['split_page']['start'],
                        'steps' => $data_in['split_page']['steps'],
        );
        try
        {
            // 执行查询,并返回查询集合到$data_db['content']
            $data_db['content'] = scap_entity::query($data_db['query']);
        }
        catch(Exception $e)
        {
            exit($e->getMessage());
        }
        //--------数据表查询操作[end]--------

        //--------模版赋值[start]--------
        if (count($data_db['content']) == 0)
        {
            $data_out['text_no_data'] = "没有相关数据。";
        }
        else
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_out['data_list'][$i] = $v;
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
                $data_out['data_list'][$i]['l_time'] =  scap_html::label(array('title' => $v['l_time']), $v['l_time']);
                $data_out['data_list'][$i]['l_module'] =  scap_html::label(array('title' => $v['l_module']), $v['l_module']);
                $data_out['data_list'][$i]['l_operator_type'] = $GLOBALS['scap']['text']['module_basic']['type_log_op'][$v['l_operator_type']];
                $data_out['data_list'][$i]['l_operator_info'] =  scap_html::label(array('title' => $v['l_operator_info']), $v['l_operator_info']);
                $data_out['data_list'][$i]['l_from'] =  scap_html::label(array('title' => $v['l_from']), $v['l_from']);
                $data_out['data_list'][$i]['l_act_type'] = $GLOBALS['scap']['text']['module_basic']['type_log_act'][$v['l_act_type']];
                $data_out['data_list'][$i]['l_act_object_type'] = $GLOBALS['scap']['text']['module_basic']['type_log_act_object'][$v['l_act_object_type']];
                $data_out['data_list'][$i]['l_act_object_info'] =  scap_html::label(array('title' => $v['l_act_object_info']), $v['l_act_object_info']);
                $data_out['data_list'][$i]['l_act_result'] = $GLOBALS['scap']['text']['module_basic']['type_log_result'][$v['l_act_result']];
                $data_out['data_list'][$i]['l_note'] =  scap_html::label(array('title' => $v['l_note']), $v['l_note']);
                $i ++;
            }
        }
        // [分页功能输出]
        $data_out['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
        $data_out['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
        $data_out['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
        $data_out['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
        $data_out['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);

        // [索引头部信息输出]
        $data_out['head_time'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'l_time', $data_in['taxis']['order'], "时间", $data_in['extra_vars']);
        $data_out['head_operator'] = "操作者";
        $data_out['head_from'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'l_from', $data_in['taxis']['order'], "操作来源IP", $data_in['extra_vars']);
        $data_out['head_act_type'] = "操作类型";
        $data_out['head_act_result'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'l_act_result', $data_in['taxis']['order'], "动作结果", $data_in['extra_vars']);
        $data_out['head_note'] = "备注";

        // [查询项目输出]
        //操作者
        $data_out['search_operator_info'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip('输入对应系统ID即可查询，如果留空，则不对该项进行筛选。', array('BALLOON' => "'true'")), 'name' => 'search[operator_info]', 'id' => 'search_login_id' , 'value' => $data_in['search']['operator_info'], 'size' => 20, 'maxlength' => $this->elements_maxlength['common']['name']));
        //操作来源IP
        $data_out['search_from'] = scap_html::input_text(array('name' => 'search[from]', 'value' => $data_in['search']['from'], 'size' => 20, 'maxlength' => 15));
        //操作类型
        $data_out['search_act_type'] = scap_html::select(  array('name'=>'search[act_type]'),
        $GLOBALS['scap']['text']['module_basic']['type_log_act'],
        array($data_in['search']['act_type']),
        false);
        //动作结果    
        $data_out['search_act_result'] = scap_html::select(  array('name'=>'search[act_result]'),
        $GLOBALS['scap']['text']['module_basic']['type_log_result'],
        array($data_in['search']['act_result']),
        false);
        //起止时间
        $data_out['search_time_from'] = scap_html::input_text(array('name' => 'search[time_from]', 'id' => 'content[time_from]', 'value' => $data_in['search']['time_from'], 'size' => 16, 'maxlength' => 16), false);
        $data_out['search_time_from'] .= scap_html::scap_select_calendar(array('inputField' => 'content[time_from]', 'ifFormat' => '%Y-%m-%d %H:%M', 'singleClick' => true, 'step' => 1, 'showsTime' => true), '.', ($data_in['get']['item_act'] = 'edit'));
        $data_out['search_time_to'] = scap_html::input_text(array('name' => 'search[time_to]', 'id' => 'content[time_to]', 'value' => $data_in['search']['time_to'], 'size' => 16, 'maxlength' => 16), false);
        $data_out['search_time_to'] .= scap_html::scap_select_calendar(array('inputField' => 'content[time_to]', 'ifFormat' => '%Y-%m-%d %H:%M', 'singleClick' => true, 'step' => 1, 'showsTime' => true), '.', ($data_in['get']['item_act'] = 'edit'));

        $data_out['btn_search'] = scap_html::input_submit(array('name' => 'button[search]', 'value' => '查询', 'title' => TEXT_TIP_BTN_SEARCH));
        $data_out['btn_reset'] = scap_html::input_button(array('name' => 'button[reset]', 'value' => '重置', 'onclick' =>"clearForm(form_index,'sel_page,sel_steps');", 'title' => TEXT_TIP_BTN_RESET));
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_calendar_file(); //引导时间控件
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'index.log.tpl', $data_out);
        //--------构造界面输出[end]----------
    }
    
    /**
     * 权限分配
     */
    public function assign_acl()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据

        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        $data_in['acl_items'] = array();// 模块acl定义列表
        $data_in['account_list'] = array();// 帐号列表数据
        //--------变量定义及声明[end]--------

        // 当前界面标题设置
        $data_def['title'] = '角色分配';// 当前界面标题设置
        $data_def['text_menu'] = '角色分配';

        //--------查询参数处理[start]--------
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)

        // 处理传入的查询参数数组:$_REQUEST['search']
        if (!empty($_REQUEST['search']))
        {
            foreach($_REQUEST['search'] as $k => $v)
            {
                // 解码已编码的查询参数
                $data_in['search'][$k] = trim(urldecode($v));
            }
        }

        // 具体处理不同参数的传递
        if (strlen($data_in['search']['account']) > 0)
        {
            $data_db['where'] .= " AND a_c_display_name LIKE '%{$data_in['search']['account']}%' OR a_c_login_id LIKE '%{$data_in['search']['account']}%'";
            $data_in['extra_vars']['search[account]'] = $data_in['search']['account'];
        }

        $data_in['extra_vars']['search[module]'] = $data_in['search']['module'];
        //--------查询参数处理[end]----------

        //--------分页/步长处理-此处为横向分页和步长[start]--------
        $data_def['steps_options'] = array(20, 50, 100, 200, 500);// 设置分页步长选项
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

        $data_in['split_page']['total'] = count($data_in['acl_items']);// 横向条目总数
        $data_in['split_page']['pages'] = ceil($data_in['split_page']['total']/$data_in['split_page']['steps']);// 横向页面总数
        //--------分页/步长处理[end]--------

        //--------排序参数处理[start]--------
        $data_in['taxis'] = array();// 排序参数输入数据
        switch($_REQUEST['order'])
        {
            case 'a_c_display_name':
                $data_in['taxis']['order'] = $_REQUEST['order'];
                break;
            default:
                $data_in['taxis']['order'] = 'a_c_display_name';
        }
        $data_in['extra_vars']['order'] = $data_in['taxis']['order'];

        switch($_REQUEST['sort'])// 排序方式处理:升序/降序
        {
            case 'ASC':
            case 'DESC':
                $data_in['taxis']['sort'] = $_REQUEST['sort'];
                break;
            default:
                $data_in['taxis']['sort'] = 'DESC';// 默认排序方式
                break;
        }
        $data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
        //--------排序参数处理[end]--------

        // 将url参数数组进行编码处理
        $data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);

        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'assign_acl':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = $_POST;
                }
                elseif ($this->current_event_result === true)
                {
                    if($_POST['button']['btn_save'])
                    {
                        scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name));
                    }
                }
                break;
        }

        //--------数据表查询操作[start]--------
        // 构造查询所需参数
        $data_db['query'] = array(
        		'sql' => "SELECT a_s_id, a_c_login_id, a_c_display_name, a_s_status FROM scap_accounts WHERE ((a_c_note != '#role#' OR (a_c_note is null)) AND (a_s_id != '10001') AND (a_s_id != '10002') {$data_db['where']})",
        		'order' => $data_in['taxis']['order'],
        		'sort' => $data_in['taxis']['sort'],
        		'start' => $data_in['split_page']['start'],
        		'steps' => $data_in['split_page']['steps'],
        );
        
        // 执行查询, 获取帐号列表
        $data_db['account_list'] = scap_entity::query($data_db['query']);
        
        // 获取模板账号列表
        $data_db['acl_template_list'] = scap_get_account_list("(1=1) AND a_c_note = '#role#'");
        //--------数据表查询操作[end]--------

        //--------模版赋值[start]--------
        if (count($data_db['account_list']) > 0)
        {
	        $i = 0;
	        foreach($data_db['account_list'] as $k => $v)
	        {
	        	$data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
	        	$data_out['data_list'][$i]['account_id'] = $v['a_s_id']; // 帐户显示
	        	$data_out['data_list'][$i]['account_login_id'] = $v['a_c_login_id']; // 帐户显示
	        	$data_out['data_list'][$i]['account_name'] = $v['a_c_display_name']; // 帐户显示
	
	        	$i ++;
	        }
        }
        
        if (count($data_db['acl_template_list']) > 0)
        {
	        $j = 0;
	        foreach($data_db['acl_template_list'] as $k => $v)
	        {
	        	$data_out['acl_template_list'][$j]['account_id'] = $v['a_s_id']; // 帐户显示
	        	$data_out['acl_template_list'][$j]['account_name'] = $v['a_c_display_name']; // 帐户显示
	        
	        	$j ++;
	        }
        }
        else
        {
        	$data_out['acl_template_list'][0]['account_name'] = '请设置角色权限模板';
        }
        
        $data_out['account_filter'] = scap_html::input_text( array(
                                        'id' => 'account_filter', 
                                        'name' => 'account_filter',
                                        'style' => 'width:300px;',
                                        'maxlength' => 30,
                                    ),
                                    false,
                                    false,
                                    true,
                                    false
                                );
        
        // [分页功能输出]
        $data_out['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
        $data_out['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
        $data_out['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
        $data_out['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
        $data_out['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);

        // [查询项目输出]
        $data_out['search_account'] = scap_html::input_text(array('onmouseover' => scap_html::scap_wz_tooltip('输入对应系统ID即可查询，如果留空，则不对该项进行筛选。', array('BALLOON' => "'true'")), 'name' => 'search[account]', 'id' => 'search_login_id' , 'value' => $data_in['search']['account'], 'size' => 25, 'maxlength' => $this->elements_maxlength['account']['display_name']));

        $data_out['btn_search'] = scap_html::input_submit(array('name' => 'button[search]', 'value' => '查询', 'title' => TEXT_TIP_BTN_SEARCH));
        $data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '保存设置', 'title' => TEXT_TIP_BTN_SAVE), false, true);
        //--------模版赋值[end]----------

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_basic', 'clearform.js'));
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->load_jquery_plugin(array('bgiframe/jquery.bgiframe.js', 'autocomplete/jquery.autocomplete.js', 'autocomplete/jquery.autocomplete.css'));// 加载autocomplete插件

        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'assign.acl.tpl', $data_out);
        //--------构造界面输出[end]----------
    }
    
    /**
     * 导入SQL
     * - 支持以分号(;)作为分割符，但不解析 ;#! 的行（不需要分割的语句请以次为标识，如trigger中的语句）
     */
    public function import_sql()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据

        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据

        $data_def['title'] = '导入SQL';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        //--------GET参数处理[end]--------
        
        $list = array();
	
    	if ($handle = opendir(SCAP_PATH_ROOT))
    	{
    	    $i = 0;
    		while(false !== ($file = readdir($handle)))
    		{
    			if (is_dir($file) && strncmp('module_', $file, strlen('module_')) == 0)
    			{
    				$info = scap_get_module_local_info($file);
    				if (!empty($info))// 只列出已设置模块信息的模块
    				{
    				    $path = $file . '/setup';
    				    if($hd[$i] = opendir($path))
    				    {
        				    while(false !== ($subfile = readdir($hd[$i])))
                    		{
                    			if ($subfile && preg_match("/.*\.sql$/", $subfile))
                    			{
                    			    $file_m_time = date('Y-m-d H:i:s', filemtime($file.'/setup/'.$subfile));
                					$list[$file][] = array('name' => $subfile, 'modify_time' => $file_m_time);
                    			}
                    		}
    				    }
    				}
    			}
    			
    			$i++;
    		}
    		closedir($handle);
    	}
    	
        $i = 0;
        foreach($list as $k => $v)
        {
            $data_out['data_list'][$i]['module_name'] = $k;
            
            $j = 0;
            foreach($v as $vv)
            {
                $data_out['data_list'][$i]['file_list'][$j]['file_name'] = $vv['name'];
                $data_out['data_list'][$i]['file_list'][$j]['file_path'] = $k . '/setup/' . $vv['name'];
                $data_out['data_list'][$i]['file_list'][$j]['file_modify_time'] = $vv['modify_time'];
                
                $j ++;
            }

            $i ++;
        }
        
        $data_out['btn_import'] = scap_html::input_submit(array('style' => 'padding:12px 36px;', 'name' => 'button[btn_import]', 'value' => '导入', 'title' => '导入'), false, true);

        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        $this->set_current_menu_text('导入SQL');
        $this->output_html($data_def['title'], 'import.sql.tpl', $data_out, !$data_in['get']['nonav']);
        //--------构造界面输出[end]--------
    }
}