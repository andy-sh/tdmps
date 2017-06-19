<!--{<*
 * description: 反馈信息显示
 * create time: 2009-4-7-10:22:51
 * @version $Id: feedback_info.tpl 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
*>}-->
<script type="text/javascript">
$(document).ready(function(){
	load_system_info();// 加载系统信息
});
</script>
<div style="text-align:left;font-size:14px;">
{<$feedback_info>}
</div>