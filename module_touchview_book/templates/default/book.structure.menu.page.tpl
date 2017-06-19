<!--{<*
 * page menu
 * create time: 2011-12-21 下午04:30:03
 * @version $Id: book.structure.menu.page.tpl 113 2012-04-12 06:24:15Z liqt $
 * @author zhangzhengqi
*>}-->
<ul>
	<li><a href="#" onclick="create_page('{<$id>}', '{<$parent_id>}', 3, 'after');">向下插入页面</a></li>
    <li><a href="#" onclick="create_page('{<$id>}', '{<$parent_id>}', 4, 'before');">向上插入页面</a></li>
    <li><a href="#" onclick="create_section('{<$id>}', '{<$parent_id>}', 3, 'after');">向下插入章节</a></li>
    <li><a href="#" onclick="create_section('{<$id>}', '{<$parent_id>}', 4, 'before');">向上插入章节</a></li>
    <li><a href="#" onclick="if (confirm('确认删除么?')) remove_page('{<$id>}');">删除</a></li>
</ul>