<!--{<*
 * 页面内容编辑模板(采用传统编辑器)
 * create time: 2014-2-12 下午03:04:56
 * @version $Id: edit.page.tpl 161 2014-02-14 03:55:01Z liqt $
 * @author LiQintao
*>}-->
{<extends file='layout.tpl'>}

{<block page_head_content append>}
<style>
textarea#content {
    width: 715px;
    height: 440px;
}
</style>

<script>
var current_tpl_page = '{<$tpl_page>}';// 当前页面模板
var drag = drag || {};

// 获取文件名称
function get_file_name(url)
{
	return url.substring(url.lastIndexOf('/')+1);
}

// page内容保存
function save_content()
{
	var save_content = $('#content').val();
	log(save_content);
	$.post("?m=module_touchview_page.ui_page.save_page_content&p_id={<$p_id>}&tpl="+current_tpl_page, {content: save_content}, function (data) {
		$.fn.scap('show_system_info');
		$('#button_save').attr("disabled", false);
	});
}

// 设置当前页面模板
function set_tpl_page(tpl)
{
	if (tpl == current_tpl_page)
	{
		return;
	}
	current_tpl_page = tpl;
	$(".page-content").removeClass('tpl-page-default');
	$(".page-content").removeClass('tpl-page-two-column');
	$(".page-content").addClass(tpl);
	reader_tpl_icon();
}

// 渲染当前模板图标
function reader_tpl_icon()
{
	if (current_tpl_page == 'tpl-page-default')
	{
		$("span#one-column").removeClass();
		$("span#two-column").removeClass();
		$("span#one-column").addClass('active');
		$("span#two-column").addClass('noactive');
	}
	else if (current_tpl_page == 'tpl-page-two-column')
	{
		$("span#one-column").removeClass();
		$("span#two-column").removeClass();
		$("span#one-column").addClass('noactive');
		$("span#two-column").addClass('active');
	}
}

$(function(){
	var editor_bar = 'Cut,Copy,Paste,Pastetext,|,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,|,Align,List,Outdent,Indent,|,Link,Unlink,Anchor,|,Img,Media,|,Source';
	$('#content').scap('xheditor', 473, false, {
		tools:editor_bar, 
		forcePtag: true,
		internalScript: true,
		cleanPaste: 0,
		loadCSS:['{<$url_book_css>}book.reset.css', '{<$url_book_css>}book.page_edit_xheditor.css']
	});
	
	$('#button_save').click(function(e){
		$('#button_save').attr("disabled", true);
		save_content();
	});

});
</script>
{</block>}

{<block page_body>}
<section class="edit">
    <div class="page">
    	<div class='page-title'>{<$top_page_name>}</div>
       	<div class='page-content' style="margin-top:-33px;">
            <textarea id="content">{<$p_content>}</textarea>
        </div>
    	<span class="pageNumber">{<$p_sort_sn>}</span>
	</div>
</section>
<div id="toolbox">
	<div style="">
		<a href="{<$url_prev>}">上一页</a>
		<a href="{<$url_next>}">下一页</a>
	</div>
  	<div style="padding-top:40px;">
		<button style="padding: 2px 20px;" type="button" id="button_save">保存</button>
	</div>
</div>
{</block>}