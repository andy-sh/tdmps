<!--{<*
 * description: 关联文件列表显示模板
 * create time: 2009-4-8-09:42:00
 * @version $Id: view.files.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
*>}-->
<div>
	<table width="100%" class="usertable" id="files_table" style="width:100%; table-layout:fixed;">
	    <tr class="title">
			<td style="text-align:center">名称</td>
			<td style="text-align:center" width="160px">类别</td>
			<td style="text-align:center" width="280px">信息</td>
			<td style="text-align:center" width="100px">操作</td>
		</tr>
{<section name=i loop=$file_list start=0>}
		<tr class="file_list" style="{<$file_list[i].row_color>}">
			<td style="text-align:left;overflow:hidden; text-overflow:ellipsis;">{<$file_list[i].sn|string_format:"%02d.">}{<$file_list[i].obl_name>}</td>
			<td style="text-align:left;overflow:hidden; text-overflow:ellipsis;">{<$file_list[i].obl_category>}</td>
			<td style="text-align:left;">{<$file_list[i].info>}</td>
			<td style="text-align:center;"><span class="file" id="file_{<$file_list[i].obl_sn>}"></span></td>
		</tr>
{<sectionelse>}
		<tr>
			<td colspan="4" style="text-align:center">没有相关数据</td>
		</tr>
{</section>}
	</table>
</div>