<!--{<*
 * 书籍索引模板
 * create time: 2011-12-26 下午03:33:38
 * @version $Id: index.book.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}

<script>
$(function() {
	$.fn.scap('show_system_info');
});
</script>

<style type="text/css">
/*鼠标悬停着色*/
tr.over td {
	background-color:#FFDEAD;
}
</style>

<div style="width:100%;">
	<form autocomplete="off" name="form_index" method="post">
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
	        	<td width="23.4%"></td>
	        </tr>
			<tr class="title">
				<td colspan="6">
					<span class="tip">查询选项</span>
				</td>
			</tr>
			<tr>
				<td style="" colspan="6">
				    名称：{<$search_name>}
				</td>
			</tr>
		</table>
		<br/>
		
		<table class="indextable" width="100%">
			<!--设置表格列宽所用 -->
	        <tr class="set_width">
                <td width="25%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="45%"></td>
            </tr>
			
			<tr class="title">
				<td colspan="5">
					<span class="tip">查询结果</span>
				</td>
			</tr>
			
			<tr>
				<td colspan="5">
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
				<td style="text-align:center">{<$head_b_name>}</td>
				<td style="text-align:center">{<$head_b_sort_sn>}</td>
				<td style="text-align:center">{<$head_b_status>}</td>
				<td style="text-align:center">{<$head_tpl>}</td>
				<td style="text-align:center">{<$head_b_description>}</td>
			</tr>
			
{<section name=i loop=$data_list start=0>}
			<tr class="data" style="{<$data_list[i].row_color>}">
				<td style="text-align:left;"><span style="font-weight:bold;color:blue;">{<$data_list[i].sn|string_format:"%02d.">}</span>{<$data_list[i].b_name>}</td>
				<td style="text-align:left;">{<$data_list[i].b_sort_sn>}</td>
				<td style="text-align:left;">{<$data_list[i].b_status>}</td>
				<td style="text-align:left;">{<$data_list[i].tpl>}</td>
				<td style="text-align:left;">{<$data_list[i].b_description>}</td>
			</tr>
{<sectionelse>}
			<tr>
				<td colspan="5" style="text-align:center">没有相关数据。</td>
			</tr>
{</section>}
		</table>
	</form>
</div>
{</block>}