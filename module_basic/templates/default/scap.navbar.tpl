<!--{<*
 * description: 系统的布局导航模版
 * create time: 2006-10-31 11:21:33
 * @version $Id: scap.navbar.tpl 164 2014-02-17 04:40:21Z liqt $
 * @author LiQintao
*>}-->
        <div id="container"><!--页面层容器>>>-->
            <div id="status"><!--状态栏>>>-->
            	<span id="logo" style="float:left">
            		{<*$logo_image*>}
            		{<$logo_title>}
            	</span>
            	<div id="user_info" style="float:right;margin-top:19px;margin-right:12px;line-height:18px;">
            	{<$nav_user_info>}
{<if $flag_show_switch_org>}
            	   【当前公司】<span style="color:blue;">{<$select_org>}</span>
{</if>}
        		</div>
        	
            </div><!--<<<状态栏-->
            
            <div id="mainbox"><!-- main box -->
{<if $flag_show_menu>}            
    		<div id="leftcolumn"><!--左边栏>>>-->
    			<div id="side_menu" style="visibility:hidden;">
{<section name=i loop=$nav_app_list  start=0>}
    					<div class="trigger {<$nav_app_list[i].class>}">
                				{<$nav_app_list[i].show>}
                		</div>
                		<div class="menu">
                			<ul>
    {<section name=j loop=$nav_app_menu[i]  start=0>}
                            		<li class="{<$nav_app_menu[i][j].class>}"><a href="{<$nav_app_menu[i][j].url>}">{<$nav_app_menu[i][j].text>}</a></li>
    {</section>}
                			</ul>
                		</div>
{</section>}
    			</div>
    			
    		</div><!--<<<左边栏-->
{</if>}	
		    <div id="rightcolumn"><!--右边栏>>>-->
    			<div id="app_title">
    				{<$nav_current_method_title>}
    			</div>
		
			    <div id="app_content">