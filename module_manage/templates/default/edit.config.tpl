<!--{<*
 * description: configuration
 * create time: 2006-8-9 16:02:41
 * @version $Id: edit.config.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<script type="text/javascript">
$(document).ready(function(){
    load_system_info();// 加载系统信息
});
</script>

<div style="width:100%;">
	<form name="form_edit" method="post">
		<table width="800">
			<tr>
				<td style="text-align:left;">{<$btn_save>}</td>
			</tr>
		</table>
		
		<table class="usertable" width="800">
			<tr>
				<td class="title" >{<$config_module>}>{<$config_cat>}>{<$config_name>}</td>
	   		</tr>
			
			<tr>
				<td>{<$c_c_value>}</td>
			</tr>
		</table>
	
		<table width="800">
			<tr>
				<td style="text-align:left;">{<$btn_save>}</td>
			</tr>
		</table>
	</form>
</div>	