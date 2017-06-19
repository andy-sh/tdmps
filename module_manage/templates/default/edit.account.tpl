<!--{<*
 * description: 编辑帐户信息
 * create time: 2006-11-26 22:37:38
 * @version $Id: edit.account.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<script type="text/javascript">
$(document).ready(function(){
    load_system_info();// 加载系统信息
});
</script>

<div style="width:100%;">
    <form name="form_edit" method="post">
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>} {<$btn_close>}</td>
                <td class="nav_right" style="text-align:right;">{<$link_edit>} {<$link_view>} {<$link_remove>} {<$link_log>}</td>
            </tr>
        </table>
	
        <table class="usertable" width="100%">
    	    <!--设置表格列宽所用 -->
            <tr class="set_width">
        	    <td width="15%"></td>
        	    <td width="18%"></td>
        	    <td width="15%"></td>
        	    <td width="18%"></td>
        	    <td width="15%"></td>
        	    <td width="19%"></td>
            </tr>
    	
		    <tr>
    		    <td class="title" style="text-align:center;" colspan="6">基本信息</td>
    	    </tr>
		
		    <tr>
			    <td style="font-weight:bold;">登录名称</td>
    		    <td colspan="2">{<$a_c_login_id>}</td>
			    <td style="font-weight:bold;">状态</td>
    		    <td colspan="2">{<$a_s_status>}</td>
		    </tr>
		
		    <tr>
			    <td style="font-weight:bold;">显示名称</td>
    		    <td colspan="2">{<$a_c_display_name>}</td>
			    <td style="font-weight:bold;"></td>
    		    <td colspan="2"></td>
		    </tr>
{<if $flag_show_set_pw>}
		    <tr>
    		    <td class="title" style="text-align:center;" colspan="6">口令设置{<$tip_password>}</td>
    	    </tr>
		
		    <tr>
			    <td style="font-weight:bold;">新口令</td>
    		    <td colspan="2">{<$a_new_password>}</td>
			    <td style="font-weight:bold;">确认新口令</td>
    		    <td colspan="2">{<$a_confirm_new_password>} {<$btn_set_pw>}</td>
		    </tr>
{</if>}
		    <tr>
			    <td style="font-weight:bold;">备注说明</td>
			    <td colspan="5">{<$a_c_note>}</td>
		    </tr>
        </table>
		
        <table width="100%">
            <tr>
                <td class="nav_left" style="text-align:left;">{<$btn_save>} {<$btn_close>}</td>
                <td class="nav_right" style="text-align:right;">{<$link_edit>} {<$link_view>} {<$link_remove>} {<$link_log>}</td>
            </tr>
        </table>
    </form>
</div>
