/**
 * page相关存储过程
 * create time: 2011-12-28 下午01:42:18
 * @version $Id: db.procedure.sql 162 2014-02-17 05:51:29Z liqt $
 * @author LiQintao
 */
DROP PROCEDURE IF EXISTS p_page_update_sort;

CREATE PROCEDURE p_page_update_sort(
	IN book_id char(40) # 所属book id
)
BEGIN
	SET @count=0;#!
	UPDATE touchview_page SET p_sort_sn =  (@count:=(@count+1))  WHERE b_id = book_id ORDER BY p_sort_sn, p_sn ASC;#!
END;