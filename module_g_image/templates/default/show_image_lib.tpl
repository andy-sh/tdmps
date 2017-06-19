<!--{<*
 * 显示图库页面模板
 * create time: 2011-11-22 上午10:50:23
 * @version $Id: show_image_lib.tpl 4 2012-07-21 07:04:47Z liqt $
 * @author LiQintao
*>}-->
<script type="text/javascript">
$(function(){
	$('#{<$config.ul_id>}').jCarouselLite({
			scroll:{<$config.steps>}, 
			vertical:{<$config.vertical>}, 
			mouseWheel:true,
			visible:{<$config.size>},
			speed:200
		});
});
</script>

<style>
#{<$config.ul_id>} ul {
	list-style:none;
}

#{<$config.ul_id>} ul li {
	width: {<$config.width>}px;
	height:{<$config.height>}px;
	display:block;
}

#{<$config.ul_id>} ul li img {
	width: 100%;
	height: 100%;
}

</style>

<div id="{<$config.ul_id>}">
	<ul >
{<section name=i loop=$data_list start=0>}
       	<li>
    		<img class="item_image_lib" src="{<$data_list[i].url>}" alt="{<$data_list[i].name>}"/>
        </li>
{</section>}
	</ul>
</div>
