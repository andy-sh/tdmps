<!--{<*
 * description: 系统登录入口
 * create time: 2006-10-18 17:05:14
 * @version $Id: touchview.login.tpl 164 2014-02-17 04:40:21Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
*>}-->
<div id="login_main" class="container">
    <div id="login_logo">
        <div class="span-14 border" style="padding-top:90px; padding-bottom:40px; margin-top:-80px;">
            <div id="logo_image">{<$image_touchview>}</div>
            <div id="logo_title"></div>
        </div>
    </div>
	<div id="login_input">
	    <div class="span-10 last" style="margin-top:-80px; text-align:left;">
	    	<div style="padding-left:40px;">
	        <div class="alert-message error" {<if $flag_no_alert>}style="visibility:hidden;"{</if>}>
	            <ul>
{<section name=i loop=$sys_info  start=0>}      
                    <li>{<$sys_info[i].text>}</li>
{</section>}
                </ul>
	        </div>
	        <h1 id="login_tip">登录</h1>
            <form name="login" method="post" action="">
                <div id="username_field">
                    <label class="input_label">
						用户名
                    </label>                            
                    {<$input_username>}
                </div>
                <div id="password_field">
                    <label class="input_label">
						密码
                    </label>                            
                    {<$input_password>}
                </div>
                {<$btn_login>}
            </form>
            </div>
        </div>
	</div>
</div>