<!--{<*
 * description: 系统日志索引
 * create time: 2008-8-28-上午10:07:39
 * @version $Id: index.log.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author Sunqt
*>}-->

<!--鼠标悬停着色start-->
<script type="text/javascript">
$(document).ready(function(){
    $("tr.data").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});

//    // autocomplete
//    $("#search_name").autocomplete("index.php?m=module_g_00.ui.search_contact&c_entity_id=B8DEABB0DF3C4F2E8F9BBCC9C479C858", {
//        width: 260,
//        selectFirst: true,
//        formatItem: function(data, i, n, value) {
//            return value.split(",")[2] + "[" + value.split(",")[1] + "," + value.split(",")[3] + "]";
//        },
//        formatResult: function(data, value) {
//            return value.split(",")[2];
//        }
//    });
//    
//    // autocomplete
//    $("#search_login_id").autocomplete("index.php?m=module_g_00.ui.search_contact&c_entity_id=B8DEABB0DF3C4F2E8F9BBCC9C479C858", {
//        width: 260,
//        selectFirst: true,
//        formatItem: function(data, i, n, value) {
//            return value.split(",")[1] + "[" + value.split(",")[2] + "," + value.split(",")[3] + "]";
//        },
//        formatResult: function(data, value) {
//            return value.split(",")[1];
//        }
//    });
   
    load_system_info();// 加载系统信息
});
</script>

<style type="text/css">
tr.over td {
    background-color:#FFDEAD;
}
</style>
<!--鼠标悬停着色end-->

<div style="width:100%;">
    <form autocomplete="off" name="form_index" method="post">
        <table class="searchtable" width="100%">
            <caption>
                {<$btn_search>} {<$btn_reset>}
            </caption>
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="10%"></td>
                <td width="23%"></td>
                <td width="10%"></td>
                <td width="23%"></td>
                <td width="10%"></td>
                <td width="24%"></td>
            </tr>
            <tr class="title">
                <td colspan="6">
                    <span class="tip">查询选项</span>
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">起止时间:</td>
                <td colspan="3">{<$search_time_from>}-{<$search_time_to>}</td>
                <td style="text-align:right;">操 作 者:</td>
                <td>{<$search_operator_info>}</td>
            </tr>
            <tr>
                <td style="text-align:right;">操作来源IP:</td>
                <td>{<$search_from>}</td>
                <td style="text-align:right;">操作类型:</td>
                <td>{<$search_act_type>}</td>
                <td style="text-align:right;">动作结果:</td>
                <td>{<$search_act_result>}</td>
            </tr>
        </table>
        <br/>
        
        <table class="indextable" width="100%">
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="20%"></td>
                <td width="22%"></td>
                <td width="14%"></td>
                <td width="12%"></td>
                <td width="12%"></td>
                <td width="20%"></td>
            </tr>
            <tr class="title">
                <td colspan="6">
                    <span class="tip">查询结果</span>
                </td>
            </tr>
        
            <tr>
                <td colspan="6">
                    <table width=100% cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="20%" style="text-align:left;">{<$index_page_prev>} {<$index_pages_select>} {<$index_steps_select>}</td>
                            <td width="60%" style="text-align:center;">{<$index_page_tip>}</td>
                            <td width="20%" style="text-align:right;">{<$index_page_next>}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        
            <tr class="head">
                <td style="text-align:center">{<$head_time>}</td>
                <td style="text-align:center">{<$head_operator>}</td>
                <td style="text-align:center">{<$head_from>}</td>
                <td style="text-align:center">{<$head_act_type>}</td>
                <td style="text-align:center">{<$head_act_result>}</td>
                <td style="text-align:center">{<$head_note>}</td>
            </tr>
        
{<section name=i loop=$data_list start=0>}
            <tr class="data" style="{<$data_list[i].row_color>}">
                <td style="text-align:center;">{<$data_list[i].l_time>}</td>
                <td style="text-align:left;">{<$data_list[i].l_operator_type>}:{<$data_list[i].l_operator_info>}</td>
                <td style="text-align:left;">{<$data_list[i].l_from>}</td>
                <td style="text-align:center;">{<$data_list[i].l_act_type>} {<$data_list[i].l_act_object_type>} {<$data_list[i].l_act_object_info>}</td>
                <td style="text-align:center;">{<$data_list[i].l_act_result>}</td>
                <td style="text-align:left;">{<$data_list[i].l_note>}</td>
            </tr>
{<sectionelse>}
            <tr>
                <td colspan="6" style="text-align:center">{<$text_no_data>}</td>
            </tr>
{</section>}    
        </table>
    </form>
</div>