<!--{<*
 * 页面内容编辑模板
 * create time: 2012-2-27 下午03:04:56
 * @version $Id: edit.page_content.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
*>}-->
{<extends file='layout.tpl'>}

{<block page_head_content append>}
<script type="text/javascript" src="{<$url_aloha>}lib/aloha.js"
		data-aloha-plugins="
		common/format,
		common/list,
		common/characterpicker,
		common/horizontalruler,
		common/commands,
		common/undo,
		common/highlighteditables
"></script>

<script>
var Aloha = window.Aloha || ( window.Aloha = {} );
var current_tpl_page = '{<$tpl_page>}';// 当前页面模板
var drag = drag || {};

Aloha.settings = {
	locale: 'zh',
	logLevels: {'error': true, 'warn': true, 'info': true, 'debug': false},
	plugins: {
		format: {
			config: ['b', 'i', 'p', 'p_no_indent', 'sub', 'sup', 'del', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'removeFormat'],
			color: ['red', 'blue', 'green', 'yellow'],
			fontsize: [12, 14, 16, 18, 22, 26],
			editables : {
				'img' : []
			},
			removeFormats : ['strong', 'em', 'b', 'i', 'cite', 'code', 'abbr', 'del', 'sub', 'sup', 'div']
		}
	}
};

Aloha.ready(function() {
	var $ = Aloha.jQuery;
	$('.page-content').aloha();
});

// 获取文件名称
function get_file_name(url)
{
	return url.substring(url.lastIndexOf('/')+1);
}

// page内容保存
function save_content()
{
	var save_content = $('.page-content').html();
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

//加载image列表
function load_imagelist()
{
	$("div#imagelist").load("?m=module_touchview_page.ui_page.show_image_lib&path={<$path_image>}&inline=1");
}

$(function(){
	$(".page-content").addClass(current_tpl_page);
	reader_tpl_icon();
	
	$('#button_save').click(function(e){
		$('#button_save').attr("disabled", true);
		save_content();
	});

	// 双击修改图片大小
	$(".page-content img").live('dblclick', function (e) {
		cusrez(this, e);
	});
	
	load_imagelist();

	$(".upload-image").colorbox({
		iframe:true, width:"80%", height:"80%", overlayClose:false,
		onClosed: function (){
			load_imagelist();// 更新图库
		}
	});

	$("#imagelist").bind("dragstart", function(e) {
		var dt = e.originalEvent.dataTransfer;
		dt.effectAllowed= 'move';
		drag.image = e.target.getAttribute('src');// 设置当前图片src
		drag.image_name = get_file_name(drag.image);
//        console.log('dragstart:'+drag.image);
        return true;
	});

	$('.page-content').bind('dragenter', function(e) {
		e.preventDefault();
		return true;
	});

	$('.page-content').bind('dragover', function(e) {
		return false;
	});
	
	$(".page-content").bind("drop", function(e){
		var dt = e.originalEvent.dataTransfer;
		if (drag.image)// 如果是从图库拖出的图片则处理
		{
			var image = "<img src='"+drag.image+"'/>";
			$(e.originalEvent.target).append(image);
//			console.log('append:'+image);
			drag.image = null;
			e.preventDefault();
		}
		else// 其他拖拽内容均忽略，包括内部区域拖拽
		{
//			console.log('invalid:'+dt.getData('text/html'));
			e.preventDefault();
		}
	});

	// 垃圾箱事件
	$('#dustbin').bind('dragover', function(e) {
		return false;
	});
	
	$('#dustbin').bind('dragenter', function(e) {
		if (drag.image)
		{
			$("div#dustbin_message").append('放手删除图片。');
	           $("div#dustbin_message").fadeIn();
	           $("div#dustbin_message").fadeOut(4000, function(){
	              $("div#dustbin_message").empty();
	           });
			return true;
		}
		else
		{
			e.preventDefault();
			return true;
		}
	});

	$("#dustbin").bind("drop", function(e){
		var dt = e.originalEvent.dataTransfer;
		if (drag.image)
		{
			console.log('name:'+ drag.image_name);

			// 删除图库图片
			$.post(
				       "?m=module_g_image.ui_image_server.delete_image&path={<$path_image>}"+drag.image_name,
				       {},
				       function(result)
				       {
					       var msg = '';
					       var step = 5;// 轮巡组件显示步长
					       if (result)
					       {
						       // 当前图片展示的图片数量
						       var current_item = parseInt($("#imagelist li img").length/3);
						       msg = '删除成功。';
//						       console.log('count:'+current_item);
							   if (current_item <= step)// 如果剩余图片小于当前展示步长，则重新加载组件
							   {
								   load_imagelist();
							   }
							   else
							   {
    						       // 删除对应图库图片
    						       $("img[class='item_image_lib'][src$='"+drag.image_name+"']").closest('li').remove();
							   }
					       }
					       else
					       {
							   msg = '删除失败。';
					       }
				           $("div#dustbin_message").append(msg);
				           $("div#dustbin_message").fadeIn();
				           $("div#dustbin_message").fadeOut(2000, function(){
				              $("div#dustbin_message").empty();
				           });
				       }
			);
			
		}
		
		e.preventDefault();
	});
});
</script>
{</block>}

{<block page_body>}
<section class="edit">
    <div class="page">
    	<div class='page-title'>{<$top_page_name>}</div>
       	<div class='page-content'>{<$p_content>}</div>
    	<span class="pageNumber">{<$p_sort_sn>}</span>
	</div>
</section>
<div id="toolbox">
	<div style="">
		<a href="{<$url_prev>}">上一页</a>
		<a href="{<$url_next>}">下一页</a>
	</div>
    <div style="padding-top: 10px; padding-bottom: 5px;font-size:14px;font-weight: bold;">页面布局</div>
	<div>
    	<span title='单列布局' id="one-column" class="noactive" onclick="set_tpl_page('tpl-page-default');"></span>
    	<span title='双列布局' id="two-column" class="noactive" onclick="set_tpl_page('tpl-page-two-column');"></span>
  	</div>
  	<div style="padding-top:40px;">
		<button style="padding: 2px 20px;" type="button" id="button_save">保存</button>
		<div style="margin-top:6px;color: green;">
		操作提示：
			<ul>
				<li style="padding-top:5px;">* 使用图片：左键拖动下部图片栏某一图片到页面内即可使用该图片。</li>
				<li style="padding-top:5px;">* 编辑图片大小：双击页面内图片即可拖动改变图片大小。</li>
				<li style="padding-top:5px;">* 在图片栏区域内滚动鼠标中键，可实现上下翻页。</li>
				<li style="padding-top:5px;">* 将图片栏图片左键拖动到垃圾桶，即可删除该图片。</li>
			</ul>
		</div>
	</div>
</div>
<div id="imagelist"></div>
<div id="imageupload">
	<div style="text-align: center;">
		<a class="upload-image" href="?m=module_g_image.ui_image_upload.upload_image&path={<$path_image>}">上传图片</a>
	</div>
	<div id="dustbin">
		<div id="dustbin_message" style="display:none;color:red;font-size:12px;"></div>
	</div>
</div>
{</block>}