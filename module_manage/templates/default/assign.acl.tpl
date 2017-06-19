<!--{<*
 * description:  
 * create time: 2010-12-06 13:48:55
 * @version $Id: assign.acl.tpl 145 2013-08-22 05:43:43Z liqt $
 * @author Gao Xiang
*>}-->
{<include file="assign.acl.js.tpl">}

<style>
.account_check {
    position: absolute;
    margin-top: 6px;
    margin-left: 90px;
}

span.account_name {
    float: left;
    line-height: 18px;
}

span.account_login_id {
    float: left;
    clear: left;
    line-height: 18px;
}

li.account_item {
    float: left;
    display: block;
    width: 110px;
    height: 45px;
    background: #DDE;
    margin: 5px 5px 0 0;
    overflow: hidden;
    cursor: pointer;
}

li .account {
    cursor: pointer;
    display: block;
    height: 100%;
    margin-left: 5px;
    margin-top: 3px;
}
</style>
<div style="text-align:left;">
    <form name="acl_assign" method="post" enctype="multipart/form-data">
        <table class="searchtable" width="100%">
            <caption>
                {<$btn_search>} {<$btn_save>}
            </caption>
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="10%"></td>
                <td width="23%"></td>
                <td width="20%"></td>
                <td width="23%"></td>
                <td width="10%"></td>
                <td width="24%"></td>
            </tr>
            <tr class="title">
                <td colspan="6">
                    <span class="tip">查询选项</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
    角色权限模板：
        <select name="acl_template_account_id">
            <option value="">-</option>
    {<section name=j loop=$acl_template_list  start=0>}
            <option value="{<$acl_template_list[j].account_id>}">{<$acl_template_list[j].account_name>}</option>
    {</section>}
        </select>
                </td>
                <td colspan="2">
    账户：{<$search_account>}
                </td>
                <td colspan="2">
        <span style="color:#F00;">已选中<span id="checked_count"></span>个账户</span>  <span><a class="scap_button" onclick="uncheckall();" href="#">取消所选</a></span>
                </td>
            </tr>
        </table>    
        
        <table class="indextable" width="100%">
            <!--设置表格列宽所用 -->
            <tr class="set_width">
                <td width="10%"></td>
                <td width="23%"></td>
                <td width="20%"></td>
                <td width="23%"></td>
                <td width="10%"></td>
                <td width="24%"></td>
            </tr>
            <tr class="title">
                <td colspan="6">
                    <span class="tip">查询结果</span>
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
            <tr>
                <td colspan="6">
                    <ul id="account_list" style="list-style-type:none;">
                {<section name=i loop=$data_list  start=0>}
                        <li class="account_item">
                            <input type="checkbox" name="acl_account_id[{<$data_list[i].account_id>}]" id="{<$data_list[i].account_id>}" class="account_check" />
                            <label class="account" for="{<$data_list[i].account_id>}">
                                <span class="account_name">{<$data_list[i].account_name>}</span>
                                <span class="account_login_id">[{<$data_list[i].account_login_id>}]</span>
                            </label>
                        </li>
                {<sectionelse>}
                        <li style="text-align:center">没有相关数据。</li>
                {</section>}
                    </ul>
                </td>
            </tr>
        </table>   
        
        <div class="clear"></div>
    </form>
</div>
