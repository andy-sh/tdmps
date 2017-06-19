<!--{<*
 * description:
 * create time: 2011-1-12 16:45:53
 * @version $Id: import.sql.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author Gao Xiang
*>}-->
<script type="text/javascript">
$(function(){
	load_system_info();// 加载系统信息
});
</script>
<style type="text/css">
#module_list td, #module_list th {border:1px solid #BBB; padding:0 4px;line-height:22px;}

.sql_list td:hover {background: #EEEEFF;}
</style>
<form method="post">
<table id="module_list" style="width:100%; table-layout:fixed; border-collapse: collapse;">
	<tr>
		<th width="240px;">模块</th>
        <th>SQL文件</th>
    </tr>
	{<section name=i loop=$data_list start=0>}
            <tr class="data" style="{<$data_list[i].row_color>}">
                <td style="text-align:left;" valign="top">{<$data_list[i].module_name>}</td>
                <td style="text-align:left;">
                	<table class="sql_list" style="width:100%;table-layout:fixed;">
                			{<section name=j loop=$data_list[i].file_list start=0>}
                                    <tr>
                                        <td style="width:20px; text-align:left; border:0px;"><input id="{<$data_list[i].file_list[j].file_path>}" name="files[{<$data_list[i].file_list[j].file_path>}]" type="checkbox" /></td>
                                        <td style="text-align:left; border:0px;"><label style="display:block;" for="{<$data_list[i].file_list[j].file_path>}">{<$data_list[i].file_list[j].file_name>}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#F00">[修改时间：{<$data_list[i].file_list[j].file_modify_time>}]</span></label></td>
                                    </tr>
                            {</section>}
                	</table>
                </td>
            </tr>
    {</section>}
</table>
{<$btn_import>}
</form>