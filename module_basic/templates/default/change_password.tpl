<!--{<*
 * description: 更改帐户口令界面
 * create time: 2006-12-24 16:19:35
 * @version $Id: change_password.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<script type="text/javascript">
$(function(){
    load_system_info();// 加载系统信息
});
</script>

<div style="width:100%;">
	<form name="form_edit" method="post">
    	<table width="100%">
    		<tr>
    			<td style="text-align:left;">{<$btn_save>}</td>
    		</tr>
    	</table>
	
    	<table class="usertable" width="100%">
        	<!--设置表格列宽所用 -->
	        <tr class="set_width">
	        	<td width="100px"></td>
	        	<td></td>
	        </tr>
        	
    		<tr>
        		<td class="title" style="text-align:center;" colspan="2">口令更改</td>
        	</tr>
    		
    		<tr>
    			<td style="font-weight:bold;">现在口令</td>
    			<td>{<$pw_now>}</td>
    		</tr>
    		
    		<tr>
    			<td style="font-weight:bold;">新口令</td>
        		<td>{<$pw_new>}</td>
        	</tr>
        	
        	<tr>
    			<td style="font-weight:bold;">确认新口令</td>
        		<td>{<$pw_new_confirm>}</td>
    		</tr>
        </table>
	
		<table width="100%">
    		<tr>
    			<td style="text-align:left;">{<$btn_save>}</td>
    		</tr>
    	</table>
	</form>
</div>