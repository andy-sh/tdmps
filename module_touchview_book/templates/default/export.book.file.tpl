<!--{<*
 * 导出书籍文件列表模板
 * create time: 2012-3-25 上午11:59:42
 * @version $Id: export.book.file.tpl 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
*>}-->
{<extends file='app.default.tpl'>}
{<block page_content_app>}

<ul>
    <li>{<$link_data_file>}</li>
    <li>{<$link_media_file>}</li>
</ul>

{</block>}