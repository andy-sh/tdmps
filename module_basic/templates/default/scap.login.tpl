<!--{<*
 * description: 系统登录入口
 * create time: 2006-10-18 17:05:14
 * @version $Id: scap.login.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<div></div>
<div id="login_top">
	<h1>{<$text_login_title>}</h1>
</div>

<div id="login_main">
    <table id="systeminfotable" title="系统反馈信息" width="400" align="center" style="">
    {<section name=i loop=$sys_info  start=0>}		
    	<tr>
    		<td>{<$sys_info[i].icon>}{<$sys_info[i].text>}</td>
    	</tr>
    {</section>}
    </table>
	
	<div id="login_input">
		<table id="login_input" cellpadding="0" cellspacing="0">
			<tr>
                <td class="login_top_left">&nbsp;</td>
                <td class="login_top">&nbsp;</td>
                <td class="login_top_right">&nbsp;</td>
			</tr>
			
			<tr>
                <td class="login_left">&nbsp;</td>
                <td style="background-color:#E8EEFA;">
					<form name="login" method="post" action="">
                        <table cellpadding="0" cellspacing="0">
                			<!--设置表格列宽所用 -->
                            <tr height="30">
                            	<td width="80%" id="login_tip">系统用户登录 >></td>
                        		<td width="20%"></td>
                            </tr>
                			
                			<tr height="10">
                				<td></td>
                				<td rowspan="3">{<$image_key>}</td>
                			</tr>
                			
                        	<tr height="10">
                        		<td style="text-align:left;padding-left:10px;">登录名称：{<$input_username>}</td>
                    		</tr>
                    		
                    		<tr height="10">
                    			<td style="text-align:left;padding-left:10px;">登录口令：{<$input_password>}</td>
                    		</tr>
                    		
                    		<tr height="25">
                    			<td colspan="2" style="text-align:center;">{<$btn_login>}</td>
                    		</tr>
                    	</table>
                    </form>
                </td>
                <td class="login_right">&nbsp;</td>
              </tr>
              <tr>
                <td class="login_bottom_left">&nbsp;</td>
                <td class="login_bottom">&nbsp;</td>
                <td class="login_bottom_right">&nbsp;</td>
              </tr>
            </table>
 
	</div>
</div>