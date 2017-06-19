<!--{<*
 * 书籍导入步骤2模板
 * create time: 2012-3-25 下午05:08:28
 * @version $Id: import.book.step2.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}
<script>
$(function() {
    $.fn.scap('show_system_info');
});
</script>

<div style="width:100%;">
    <form name="form_edit" method="post">
        <table class="usertable" width="100%">
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="100px"></td>
                <td></td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">待导入书籍ID</td>
                <td>{<$b_id>}</td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">待导入书籍名称</td>
                <td>{<$b_name>}</td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">待导入书籍页码</td>
                <td>共 {<$page_count>} 页</td>
            </tr>
            
        </table>
        
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>}</td>
            </tr>
        </table>
    </form>
</div>
{</block>}