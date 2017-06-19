<!--{<*
 * 书籍配置模板
 * create time: 2012-3-18 下午01:48:03
 * @version $Id: edit.book.config.tpl 158 2014-02-12 03:32:58Z liqt $
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
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>}</td>
                <td class="nav_right" style="text-align:right;"></td>
            </tr>
        </table>
        
        <table class="usertable" width="100%">
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="130px"></td>
                <td></td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">自动翻页开关</td>
                <td>{<$config_auto_flip_switch>}</td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">自动翻页等待时长</td>
                <td>
{<section name=i loop=$list_auto_flip_waiting start=0>}
					{<$list_auto_flip_waiting[i].item>}
{</section>}
				</td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">书籍数量上限</td>
                <td>{<$config_max_book_count>}</td>
            </tr>
            
            <tr>
                <td style="font-weight:bold;">每本书页数上限</td>
                <td>{<$config_max_page_count>}</td>
            </tr>
        </table>
        
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>}</td>
                <td class="nav_right" style="text-align:right;"></td>
            </tr>
        </table>
    </form>
</div>
{</block>}