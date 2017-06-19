<!--{<*
 * description: 查看对象日志模板
 * create time: 2008-12-21-下午04:22:19
 * @version $Id: view.object_log.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
*>}-->
 
<form name="form_index" method="post">
	<table class="indextable" width="100%">
		<!--设置表格列宽所用 -->
        <tr class="set_width">
			<td width="18%"></td>
        	<td width="8%"></td>
        	<td width="12%"></td>
			<td width="17%"></td>
        	<td width="23%"></td>
			<td width="22%"></td>
        </tr>
		
		<tr class="title">
			<td colspan="6">
				<span class="tip">日志结果</span>
			</td>
		</tr>
		
		<tr>
			<td colspan="6">
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
			<td style="text-align:center">{<$head_time>}</td>
			<td style="text-align:center">{<$head_type>}</td>
			<td style="text-align:center">{<$head_operator_id>}</td>
			<td style="text-align:center">{<$head_client_ip>}</td>
			<td style="text-align:center">{<$head_user_agent>}</td>
			<td style="text-align:center">{<$head_comment>}</td>
		</tr>
		
{<section name=i loop=$data_list start=0>}
		<tr style="{<$data_list[i].row_color>}">
			<td style="text-align:center">{<$data_list[i].al_time>}</td>
			<td style="text-align:left;">{<$data_list[i].al_type>}</td>
			<td style="text-align:left;">{<$data_list[i].al_operator_id>}</td>
			<td style="text-align:left;">{<$data_list[i].al_client_ip>}</td>
			<td style="text-align:left;">{<$data_list[i].al_user_agent>}</td>
			<td style="text-align:left;">{<$data_list[i].al_comment>}</td>
		</tr>
{<sectionelse>}

		<tr>
			<td colspan="6" style="text-align:center">{<$text_no_data>}</td>
		</tr>
{</section>}
	</table>
</form>