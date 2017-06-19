{<*
 * 应用的默认模板
 * 与老版本样式兼容(core <= 2.0.1)
 * @version $Id: app.default.tpl 217 2013-02-05 09:57:26Z liqt $
 * @creator LiQintao @ 2013-2-5 下午02:14:38
*>}
{<extends file='layout.tpl'>}
{<block page_body>}
{<if $flag_has_navbar>}
<div id="container"><!--页面层容器>>>-->
    <div id="status"><!--状态栏>>>-->
        <span id="logo" style="float:left">{<$nav_logo>}</span>
        <div id="user_info" style="float:right;margin-top:19px;margin-right:12px;line-height:18px;">{<$nav_user_info>}</div>
    </div><!--<<<状态栏-->
    
    <div id="mainbox"><!-- main box -->
{<if $flag_show_menu>}            
        <div id="leftcolumn"><!--左边栏>>>-->
            <div id="side_menu" style="visibility:hidden;">
{<section name=i loop=$nav_app_list  start=0>}
                <div class="trigger {<$nav_app_list[i].class>}">{<$nav_app_list[i].show>}</div>
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
            <div id="app_title">{<$nav_current_method_title>}</div>
            <div id="app_content">
{</if>}{<*flag_has_navbar*>}
            {<block page_content_app>}应用内容{</block>}
{<if $flag_has_navbar>}
            </div>
        </div><!--<<<右边栏-->
    </div><!-- main box -->
    <div class="clear"></div>   
    <div class="spacer"></div>
</div><!--<<<页面层容器-->

<div id="bottom">
     <a style="color:black;text-decoration: underline;" href='http://hotide.cn' target='_blank'>上海热信信息技术有限公司</a>
</div>
{</if>}{<*flag_has_navbar*>}
{</block>}