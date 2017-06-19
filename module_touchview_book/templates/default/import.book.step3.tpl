<!--{<*
 * 书籍导入步骤3模板
 * create time: 2012-3-25 下午11:27:00
 * @version $Id: import.book.step3.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}
<script>
$(function() {
    $.fn.scap('show_system_info');
});
</script>

<form name="form_edit" method="post" enctype="multipart/form-data">
	<div style="width:100%;">
		<table class="usertable" width="100%">
			<tr class="set_width">
		        <td width="100px"></td>
		       	<td></td>
	    	</tr>
			
			<tr>
				<td>书籍使用媒体文件</td>
				<td>{<$upload_file>}</td>
			</tr>
		</table>
		
		<table width="100%">
			<tr>
				<td style="text-align:left;">{<$btn_save>}</td>
			</tr>
		</table>
	</div>
</form>
{</block>}