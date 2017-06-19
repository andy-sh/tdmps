<!--{<*
 * description: 系统帐户索引
 * create time: 2006-11-13 11:01:09
 * @version $Id: index.account.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<!--鼠标悬停着色start-->
<script type="text/javascript">
$(function(){
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
	    <table class="searchtable" style="width:100%;">
		    <caption>
			    {<$btn_search>} {<$btn_reset>} <b>{<$link_add_account>}</b>
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
			    <td style="text-align:right;">登录名称：</td>
			    <td>{<$search_login_id>}</td>
			    <td style="text-align:right;">显示名称：</td>
			    <td>{<$search_display_name>}</td>
			    <td style="text-align:right;">状态：</td>
			    <td>{<$search_status>}</td>
		    </tr>
	    </table>
	    <br/>

        <table class="indextable" style="width:100%;">
		    <!--设置表格列宽所用 -->
            <tr class="set_width">
        	    <td width="10%"></td>
        	    <td width="40%"></td>
        	    <td width="40%"></td>
        	    <td width="10%"></td>
            </tr>
		    <tr class="title">
			    <td colspan="4">
				    <span class="tip">查询结果</span>
			    </td>
		    </tr>
		
		    <tr>
			    <td colspan="4">
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
    		    <td style="text-align:center">{<$head_op>}</td>
    		    <td style="text-align:center">{<$head_login_id>}</td>
    		    <td style="text-align:center">{<$head_display_name>}</td>
    		    <td style="text-align:center">{<$head_status>}</td>
    	    </tr>
    		
{<section name=i loop=$data_list  start=0>}
    	    <tr class="data" style="{<$data_list[i].row_color>}">
    		    <td style="text-align:center;font-weight:bold;color:blue;">{<$data_list[i].sn|string_format:"%02d.">}{<$data_list[i].op>}</td>
    		    <td style="text-align:left;">{<$data_list[i].a_c_login_id>}</td>
    		    <td style="text-align:left;">{<$data_list[i].a_c_display_name>}</td>
    		    <td style="text-align:center;">{<$data_list[i].a_s_status>}</td>
    	    </tr>
{<sectionelse>}
		    <tr>
			    <td colspan="4" style="text-align:center">{<$text_no_data>}</td>
		    </tr>
{</section>}
        </table>
    </form>
</div>	