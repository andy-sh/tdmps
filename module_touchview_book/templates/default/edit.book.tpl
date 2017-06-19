<!--{<*
 * 书籍编辑模板
 * create time: 2011-12-13 下午08:20:51
 * @version $Id: edit.book.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author zhangzhengqi
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}
<script>
$(function() {
	$.fn.scap('show_system_info');
    $(".tpl-list input[type='radio']").css("display","none");// 隐藏input radio组件
});
</script>
<style>
td.nav_right a{
    margin-left:0.73em;
}

.tpl-list label {
	height: 52px;
	width: 83px;
	display: inline-block;
	margin-right: 8px;
}

.tpl-list input[type="radio"]:checked+label {
	border: 3px solid red;
}
</style>

<div style="width:100%;">
    <form name="form_edit" method="post">
        <table width="100%">
            <tr>
                <td colspan=2 style="text-align:left;color:blue;">* 创建保存书籍后，点击“页面结构”按钮进行内容编辑。</td>
            </tr>
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>} {<$btn_close>} {<$link_structure>} {<$link_preview>}</td>
                <td class="nav_right" style="text-align:right;">{<$link_edit>}{<$link_view>}{<$link_remove>}{<$link_log>}</td>
            </tr>
        </table>
        
        <table class="usertable" width="100%">
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="100px"></td>
                <td></td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">书名</td>
                <td>{<$b_name>}</td>
            </tr>
            
            <tr>
				<td style="font-weight:bold;">状态</td>
	    		<td>{<$b_status>}</td>
	    	</tr>
            
            <tr>
				<td style="font-weight:bold;">序号</td>
	    		<td>{<$b_sort_sn>}(序号越小，位置越靠前)</td>
	    	</tr>
            
            <tr>
                <td style="font-weight:bold;">模板</td>
                <td class="tpl-list">
{<section name=i loop=$tpl_list start=0>}
					{<$tpl_list[i].item>}
{</section>}
                </td>
            </tr>
			
            <tr>
                <td style="font-weight:bold;">说明</td>
                <td>{<$b_description>}</td>
            </tr>
        </table>
        
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>} {<$btn_close>} {<$link_structure>} {<$link_preview>}</td>
                <td class="nav_right" style="text-align:right;">{<$link_edit>}{<$link_view>}{<$link_remove>}{<$link_log>}</td>
            </tr>
        </table>
    </form>
</div>
{</block>}