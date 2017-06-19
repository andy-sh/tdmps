/**
 * description: js根文件
 * create time: 2006-12-22 11:45:59
 * @version $Id: home.js 100 2013-05-09 02:50:00Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */
// 自动将页面的第一个表单(form)的第一个非readonly和非disabled的input输入框/密码框置为输入焦点
function focusFirst()
{
	if (document.forms.length > 0 && document.forms[0].elements.length > 0)
	{
		for(var i=0; i<document.forms[0].elements.length;i++)
		{
			if ((document.forms[0].elements[i].type == "text" || document.forms[0].elements[i].type == "password")&& document.forms[0].elements[i].readOnly != true && document.forms[0].elements[i].disabled != true)
			{
				document.forms[0].elements[i].focus();
				break;
			}
		}
	}
}

//自适应宽度辅助计算
function resize_div()
{
	var flag_show_menu = $('#leftcolumn').length;
    $('#rightcolumn').css("width",$("#container").width()-flag_show_menu*163+"px");
}

// 生成系统菜单
function gen_side_menu()
{
	$('#side_menu').slidermenu({width:'148', isTitleLink:false, act:'click', accordion:false, speed:'normal', borderRadius: '5'});
    
    $('#side_menu_common').slidermenu({width:'148', isTitleLink:true, act:'click', accordion:false, speed:'normal', borderRadius: '5'});
    
    $('#side_menu').css('visibility', 'visible');
}

var GB_ROOT_DIR = "base/library/greybox/";// 该句应该放在greybox文件之前