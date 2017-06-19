<!--{<*
 * description: 应用配置数据索引
 * create time: 2006-12-6 21:31:34
 * @version $Id: index.config.tpl 4 2012-07-18 06:40:23Z liqt $
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
		<table class="searchtable" width="100%">
			<caption>
				{<$btn_search>} {<$btn_reset>}
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
				<td>{<$search_config_module>}</td>
				<td style="text-align:right;"></td>
				<td></td>
				<td style="text-align:right;"></td>
				<td></td>
			</tr>
		</table>
		<br/>
	
	    <table class="indextable" width="100%">
			<!--设置表格列宽所用 -->
	        <tr class="set_width">
	        	<td width="15%"></td>
	        	<td width="15%"></td>
	        	<td width="15%"></td>
	        	<td width="47%"></td>
				<td width="8%"></td>
	        </tr>
			
			<tr class="title">
				<td colspan="5">
					<span class="tip">查询结果</span>
				</td>
			</tr>
			
	    	<tr class="head">
	    		<td style="text-align:center">{<$head_module>}</td>
				<td style="text-align:center">{<$head_cat>}</td>
	    		<td style="text-align:center;">{<$head_name>}</td>
	    		<td style="text-align:center">{<$head_value>}</td>
	    		<td style="text-align:center">{<$head_op>}</td>
	    	</tr>
	    		
	{<section name=i loop=$data_list  start=0>}
	    	<tr class="data" style="{<$data_list[i].row_color>}">
	    		<td style="text-align:left;">{<$data_list[i].config_module>}</td>
				<td style="text-align:left;">{<$data_list[i].config_cat>}</td>
	    		<td style="text-align:left;">{<$data_list[i].config_name>}</td>
	    		<td style="text-align:left;">{<$data_list[i].c_c_value>}</td>
	    		<td style="text-align:center">{<$data_list[i].op>}</td>
	    	</tr>
	{<sectionelse>}
			<tr>
				<td colspan="5" style="text-align:center">{<$text_no_data>}</td>
			</tr>
	{</section>}
	    </table>
	</form>
</div>	