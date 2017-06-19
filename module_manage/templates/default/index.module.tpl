<!--{<*
 * description: 模块管理模版
 * create time: 2006-12-13 10:11:14
 * @version $Id: index.module.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->

<!--鼠标悬停着色start-->
<script type="text/javascript">
$(document).ready(function(){
    $("tr.data").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
    
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
	
	<form name="form_index" method="post">
	    <table class="indextable" width="100%">
			<!--设置表格列宽所用 -->
	        <tr class="set_width">
	        	<td width="18%"></td>
	        	<td width="18%"></td>
	        	<td width="8%"></td>
	        	<td width="8%"></td>
				<td width="8%"></td>
	        	<td width="8%"></td>
				<td width="8%"></td>
	        	<td width="8%"></td>
				<td width="8%"></td>
	        	<td width="8%"></td>
	        </tr>
			
	    	<tr class="head">
	    		<td style="text-align:center" rowspan="2">{<$head_id>}</td>
	    		<td style="text-align:center" rowspan="2">{<$head_name>}</td>
				<td style="text-align:center" rowspan="2">{<$head_property>}</td>
				<td style="text-align:center" colspan="2">{<$head_version>}</td>
				<td style="text-align:center" colspan="2">{<$head_status>}</td>
				<td style="text-align:center" rowspan="2">{<$head_active>}</td>
				<td style="text-align:center" rowspan="2">{<$head_order>} <br/> {<$btn_order>}</td>
	    		<td style="text-align:center" rowspan="2">{<$head_op>}</td>
			</tr>
			
			<tr class="head">
				<td style="text-align:center">{<$head_version_local>}</td>
				<td style="text-align:center">{<$head_version_register>}</td>
	    		<td style="text-align:center">{<$head_status_local>}</td>
				<td style="text-align:center">{<$head_status_register>}</td>
	    	</tr>
	    		
	{<section name=i loop=$data_list  start=0>}
	    	<tr class="data" style="{<$data_list[i].row_color>}">
	    		<td style="text-align:left;">{<$data_list[i].module_id>}</td>
	    		<td style="text-align:left;">{<$data_list[i].module_name>}</td>
				<td style="text-align:left;">{<$data_list[i].module_property>}</td>
				<td style="text-align:left;">{<$data_list[i].module_version_local>}</td>
				<td style="text-align:left;">{<$data_list[i].module_version_register>}</td>
				<td style="text-align:left;">{<$data_list[i].module_status_local>}</td>
				<td style="text-align:left;">{<$data_list[i].module_status_register>}</td>
	    		<td style="text-align:left;">{<$data_list[i].module_status_active>}</td>
				<td style="text-align:left;">{<$data_list[i].module_order>}</td>
	    		<td>{<$data_list[i].op>}</td>
	    	</tr>
	{<sectionelse>}
			<tr>
				<td colspan="10" style="text-align:center">{<$text_no_data>}</td>
			</tr>
	{</section>}
	    </table>
	</form>
</div>