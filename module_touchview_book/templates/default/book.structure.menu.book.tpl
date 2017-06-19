<!--{<*
 * book menu
 * create time: 2011-12-15 下午07:57:36
 * @version $Id: book.structure.menu.book.tpl 113 2012-04-12 06:24:15Z liqt $
*>}-->
<ul>
    <li><a href="#" onclick="create_section('{<$id>}', '{<$id>}', 1, 'first');">插入章节</a></li>
    <li><a href="#" onclick="create_page('{<$id>}', '{<$id>}', 1, 'first');">插入页面</a></li>
    <li><a href="#" onclick="window.open('{<$link_book_view>}');">书籍信息</a></li>
    <li><a href="#" onclick="window.open('{<$link_book_preview>}');">预览</a></li>
    <li><a href="#" onclick="window.open('{<$link_export_book_file>}');">导出书籍</a></li>
</ul>