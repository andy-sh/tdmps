<!--{<*
 * description: 编辑上传文件模板
 * create time: 2009-4-8-14:06:00
 * @version $Id: edit.file.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
*>}-->
<script type="text/javascript">
$(document).ready(function(){
	load_system_info();// 加载系统信息
	
	$("#upload_file").change(function(){
		var temp1 = $("#upload_file").val().substring($("#upload_file").val().lastIndexOf("\\") + 1);
		var temp2 = temp1.substring(temp1.lastIndexOf("."), 0);
		$("#obl_name").val(temp2);
	});
});
</script>

<form name="form_edit" method="post" enctype="multipart/form-data">
	<div>
		<table class="usertable" width="99%">
			<tr class="set_width">
		        <td width="20%"></td>
		       	<td width="80%"></td>
	    	</tr>
			
			<tr>
				<td rowspan="2">文件名称</td>
				<td>{<$obl_name>}</td>
			</tr>
			
			<tr>
				<td>{<$upload_file>}</td>
			</tr>
			
			<tr>
				<td>类别</td>
				<td>{<$obl_category>}</td>
			</tr>
			
			<tr>
				<td>说明</td>
				<td>{<$obl_comment>}</td>
			</tr>
		</table>
		
		<table width="100%">
			<tr>
				<td style="text-align:left;">{<$btn_save>} {<$btn_remove>} {<$btn_cancel>}</td>
			</tr>
			<tr>
				<td style="text-align:center;color:red;">[ 注：上传附件的最大尺寸为 MB ]</td>
			</tr>
		</table>
	</div>
</form>