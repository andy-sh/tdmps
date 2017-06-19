<!--{<*
 * description: 系统权限索引(管理)
 * create time: 2006-12-9 11:28:55
 * @version $Id: index.acl.tpl 145 2013-08-22 05:43:43Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->

<script>
// 选择框批量选择操作函数
function check_all(bit)
{
    var con = "["+bit+"]";
    if ($("#check_"+bit).attr("checked"))
    {
        $(":checkbox[name$='["+bit+"]']").attr("checked", true);
    }
    else
    {
        $(":checkbox[name$='["+bit+"]']").attr("checked", false);
    }
}

$(function(){
    // 鼠标悬停着色
    $("tr.data").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});

    load_system_info();// 加载系统信息
});
</script>

<style>
tr.over td {
    background-color:#FFDEAD;
}
</style>
<!--鼠标悬停着色end-->

<div style="width:100%;">
    <form autocomplete="off" name="form_index" method="post">
        <table class="searchtable" width="100%">
            <caption>
                {<$btn_search>} {<$btn_save>}
            </caption>
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="10%"></td>
                <td width="23.3%"></td>
                <td width="10%"></td>
                <td width="23.3%"></td>
                <td width="10%"></td>
                <td width="23.3%"></td>
            </tr>
            <tr class="title">
                <td colspan="6">
                    <span class="tip">查询选项</span>
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">系统模块：</td>
                <td>{<$search_module>}</td>
                <td style="text-align:right;">帐户：</td>
                <td>{<$search_account>}</td>
                <td style="text-align:right;"></td>
                <td>{<$search_filter>}</td>
            </tr>
        </table>
        <br/>
    
        <table class="indextable" width="100%">
            <tr class="title">
                <td colspan="{<$count_col>}">
                    <span class="tip">查询结果</span>
                </td>
            </tr>
        
            <tr>
                <td colspan="{<$count_col>}">
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
                <td style="text-align:center;width:120px;">{<$head_account>}</td>
{<section name=i loop=$acl_def_list  start=0>}
                <td style="text-align:center">{<$acl_def_list[i].acl_name>}{<$acl_def_list[i].acl_name_checkbox>}</td>
{</section>}
            </tr>
        
{<section name=i loop=$data_list  start=0>}
            <tr class="data" style="{<$data_list[i].row_color>}">
                <td style="text-align:left;">{<$data_list[i].account>}</td>
    {<section name=j loop=$data_list[i].acl  start=0>}
                <td style="text-align:center;">{<$data_list[i].acl[j].check_box>}</td>
    {</section>}
            </tr>
{<sectionelse>}
            <tr>
                <td colspan="{<$count_col>}" style="text-align:center">没有相关数据。</td>
            </tr>
{</section>}
        </table>
    </form>
</div>