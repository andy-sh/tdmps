<!--{<*
 * 
 * create time: 2010-12-10 09:24:08
 * @version $Id: assign.acl.js.tpl 145 2013-08-22 05:43:43Z liqt $
 * @author Gao Xiang
*>}-->
<script type="text/javascript">
function uncheckall(){
    $(".account_check").removeAttr('checked');
    $("#checked_count").html($(".account_check:checked").length);
    $(".account_check").parent().removeClass('checked');
    $('li.account_item').css('background', '#DDE');
    $('li.account_item label').css('color', '#000');
    $('li.account_item.checked').css('background', '#666FFF');
    $('li.account_item.checked label').css('color', '#FFF');
    $("#checked_count").css('font-size', '1.8em');
    $("#checked_count").animate({'fontSize':'1em'}, 300);
}

$(function(){
    
    load_system_info();// 加载系统信息
    
    $('.account_check').each(function(){
        $(this).change(function(){
            $(this).parent().toggleClass('checked');
            $('li.account_item').css('background', '#DDE');
            $('li.account_item label').css('color', '#000');
            $('li.account_item.checked').css('background', '#666FFF');
            $('li.account_item.checked label').css('color', '#FFF');
        });
    });
    
    $("#checked_count").html($(".account_check:checked").length);
    
    $(".account_check").each(function(){
        $(this).change(function(){
            $("#checked_count").html($(".account_check:checked").length);
            $("#checked_count").css('font-size', '1.8em');
            $("#checked_count").animate({'fontSize':'1em'}, 300);
        });
    });
    
    $("#search_login_id").keyup(function(){
        query = $(this).val();
        if(query == '')
        {
            $("li.account_item").show(600);
        }
        else
        {
            $("li.account_item").each(function(){
                var match_name = $(this).children("label").children("span.account_name").html().match(query);
                var match_login_id = $(this).children("label").children("span.account_login_id").html().match(query);
                if(match_name)
                {
                    $(this).show(600);
                }
                else if(match_login_id)
                {
                    $(this).show(600);
                }
                else
                {
                    $(this).hide(600);
                }
            });
        }
    });
});
</script>