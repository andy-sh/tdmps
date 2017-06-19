<!--{<*
 * book结构化展现模板
 * create time: 2011-12-29 下午03:05:54
 * @version $Id: book.structure.tpl 160 2014-02-14 03:50:25Z liqt $
 * @author LiQintao
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}
<style>
.menu-book,.menu-section,.menu-page {
    cursor: pointer;
}
a.book {
    cursor: default;
}

a.section, a.page {
    cursor: move;
}

div#book_structure_tree {
	float:left;
	width: 300px;
}

div#page_show {
	width: 900px;
	float:left;
}
</style>

<script>
/**
 * current_id 当前id
 * parent_id 章节父id
 * position_type 位置类型，见class page定义
 * show_position 新节点显示的位置，"before", "after", "inside", "first", "last"
 */
function create_section(current_id, parent_id, position_type, show_position)
{
	$("#book_structure_tree").jstree("create", "#id-"+current_id, show_position, {"attr":{"p_type":"{<$smarty.const.TYPE_PAGE_SECTION>}", "parent_id": parent_id, "refer_id":current_id, 'position_type':position_type}});
}

/**
 * current_id 当前id
 * parent_id 页面父id
 * position_type 位置类型，见class page定义
 * show_position 新节点显示的位置，"before", "after", "inside", "first", "last"
 */
function create_page(current_id, parent_id, position_type, show_position)
{
	$("#book_structure_tree").jstree("create", "#id-"+current_id, show_position, {"attr":{"p_type":"{<$smarty.const.TYPE_PAGE_NORMAL>}", "parent_id":parent_id, "refer_id":current_id, 'position_type':position_type}}, false, true);
}

function remove_section(id)
{
	$("#book_structure_tree").jstree("remove", "#id-"+id);
}

function remove_page(id)
{
	$("#book_structure_tree").jstree("remove", "#id-"+id);
}

function rename_section(id)
{
	$("#book_structure_tree").jstree("rename", "#id-"+id);
}

// 清空章节
function empty_section(id)
{
	$.get( 	"?m=module_touchview_book.ui_book.empty_section",
			{"id" : id}, 
          	function(data)
			{
    	        $.fn.scap('show_system_info');
    	        $("#book_structure_tree").jstree("refresh");
   	        }
	);
}

// 强制删除章节(无论是否有子页面)
function force_remove_section(id)
{
	$.get( 	"?m=module_touchview_book.ui_book.force_remove_section",
			{"id" : id}, 
          	function(data)
			{
    	        $.fn.scap('show_system_info');
    	        $("#book_structure_tree").jstree("refresh");
   	        }
	);
}

// 渲染book/section/page操作菜单
function render_menu()
{
	// 加载book菜单
	$(".menu-book").each(function(){
		var handle = "#"+$(this).attr("id");
		$.get('{<$link_menu_book>}'+'{<$b_id>}', function(data){
			$(handle).fgmenu({
	            content:data,
	            flyOut: true
	        });
		});
	});

	// 加载section菜单
	$(".menu-section").each(function(){
		var handle = "#"+$(this).attr("id");
        var id = $(this).attr("id").split('-')[2];
        $.get('{<$link_menu_section>}'+id, function(data){
            $(handle).fgmenu({
                content:data,
                flyOut: true
            });
        });
	});

	// 加载page菜单
	$(".menu-page").each(function(){
		var handle = "#"+$(this).attr("id");
        var id = $(this).attr("id").split('-')[2];
        $.get('{<$link_menu_page>}'+id, function(data){
            $(handle).fgmenu({
                content:data,
                flyOut: true
            });
        });
	});
}

$(function(){
	$.fn.scap('show_system_info');

	$("#book_structure_tree").jstree({
		"plugins":[ "themes", "html_data", "cookies", "ui", "crrm", "types", "dnd"],
		"ui":
		{
			 "select_limit" : 1
		},
		"html_data":
		{
            "ajax":
             {
                 "url":"index.php",
                 "data":function (n){
		              var id = '';
		              if (n.attr)
		              {
		            	  id =  n.attr("id").split('-')[1];
		              }
                      return {
					      "m":"module_touchview_book.ui_book.load_structure_html",
						  "b_id":"{<$b_id>}",//book id
						  "id":id,//对象id
						  "p_type":n.attr ? n.attr("p_type") : 0,//page type
						  "entity_id":n.attr ? n.attr("entity_id") : 0 //实体id
					  };
                  },
				  "complete":function(html){
                	  render_menu();
    			  }
             }
		},
		"themes":
		{ 
			 "theme":"classic",
			 "icons" : false // 不显示默认图标
		},
		"types":
		{
			"types":// 类型标识默认是在每个li的rel中定义的
			{
    			"book":
                {
                   "valid_children" : ["section", "page"],
                   "start_drag" : false,
                   "move_node" : false,
                   "delete_node" : false,
                   "hover_node" : false, 
                   "remove" : false
                },
    			"section":
    			{
    			   "valid_children" : ["section", "page"]
    			},
    			"page":
    			{
    			   "valid_children" : "none" // 不能再有子节点
    			}
			}
		}
     })
     .bind("create.jstree", function (e, data) {
        $.ajax({
		  type:"GET",
		  url:"?m=module_touchview_book.ui_book.create_node",
		  data:{
	               "b_id":"{<$b_id>}",//book id
                   "p_type" : data.rslt.obj.attr("p_type"),//页面类型
                   "parent_id" : data.rslt.obj.attr("parent_id"), // 父id
                   "refer_id" : data.rslt.obj.attr("refer_id"),//顺序参考id
                   "position_type" : data.rslt.obj.attr("position_type"),//位置类型
                   "name" : data.rslt.name//节点名称
                }, 
          success: function(msg){
	           $.fn.scap('show_system_info');
	           data.inst.refresh();// 刷新
           }
        });
     })
     .bind("remove.jstree", function (e, data) {
    	$.ajax({
            type:"GET",
            url:"?m=module_touchview_book.ui_book.remove_node",
            data:{
    		         "name": data.rslt.obj.attr("alt"),//节点描述
                     "id" : data.rslt.obj.attr("id").replace("id-","") // 要删除节点id
                  }, 
            success: function(msg){
                 $.fn.scap('show_system_info');
                 data.inst.refresh();// 刷新
             }
        });
     })
	 .bind("rename.jstree", function (e, data) {
    	$.ajax({
            type:"GET",
            url:"?m=module_touchview_book.ui_book.rename_node",
            data:{
    		         "name": data.rslt.old_name,//节点描述
                     "id" : data.rslt.obj.attr("id").replace("id-",""), // 节点id
                     "new_name" : data.rslt.new_name // 修改后的名称
                  }, 
            success: function(msg){
                 $.fn.scap('show_system_info');
                 data.inst.refresh();// 刷新
             }
        });
     })
	 .bind("move_node.jstree", function (e, data) {
    	 data.rslt.o.each(function (i) {
             $.ajax({
                 async : false,// 同步请求
                 type: 'GET',
                 url: "?m=module_touchview_book.ui_book.move_node",
                 data : { 
                     "id" : $(this).attr("id").replace("id-",""),// 移动的条目对象id
                     "name" : $(this).attr("alt"),// 条目名称
                     "parent_id" : data.rslt.np.attr("id").replace("id-",""), //新的父节点id
                     "parent_name" : data.rslt.np.attr("alt"), // 新的父节点名称
                     "refer_id" : data.rslt.r.attr("id").replace("id-",""), // 位置参考id
                     "position" : data.rslt.p // the position to move to (may be a string - "last", "first", "before", "after") 
                 },
                 success : function (r) {
                	 
                 }
             });
         });
    	 $.fn.scap('show_system_info');
         data.inst.refresh();// 刷新
     })
     .bind("select_node.jstree", function (e, data) {
         var id = data.rslt.obj.attr("id").replace("id-","");
         if (id == '{<$b_id>}') return;// 如果是根节点，则忽略
         var url = '?m=module_touchview_page.ui_page.edit&p_id=' + id;
         $('#show').attr('src', url);
     });

});
</script>
<div style="min-width: 1230px;">
	<div style="text-align: left;color: green;">
	操作提示：*左键点击节点图标出现可操作菜单 *左键点击+号展开下级内容 *左键点击章节/页面名称编辑当前页面内容 *左键拖动章节/页面名称可重新排序
	</div>
    <div id="book_structure_tree"></div>
    <div id="page_show">
    	<iframe id="show" name="show" width="930" height="610"  frameborder=0 scrolling="auto" src=""></iframe>
    </div>
	<div class="clear"></div>
</div>
{</block>}