<!--{<*
 * section menu
 * create time: 2011-12-15 下午07:55:49
 * @version $Id: book.structure.menu.section.tpl 113 2012-04-12 06:24:15Z liqt $
 * @author zhangzhengqi
*>}-->
<ul>
    <li><a href="#" onclick="create_page('{<$id>}', '{<$id>}', 1, 'first');">插入下级页面</a></li>
    <li><a href="#" onclick="create_page('{<$id>}', '{<$parent_id>}', 3, 'after');">插入同级页面</a></li>
    <li><a href="#" onclick="create_section('{<$id>}', '{<$id>}', 1, 'first');">插入下级章节</a></li>
    <li><a href="#" onclick="create_section('{<$id>}', '{<$parent_id>}', 3, 'after');">插入同级章节</a></li>
    <li><a href="#" onclick="rename_section('{<$id>}');">重命名</a></li>
    <li><a href="#" onclick="if (confirm('确认删除么?')) remove_section('{<$id>}');">删除</a></li>
    <li><a href="#" onclick="if (confirm('确认清空么?')) empty_section('{<$id>}');">清空</a></li>
    <li><a href="#" onclick="if (confirm('确认强制删除么?')) force_remove_section('{<$id>}');">强制删除</a></li>
</ul>