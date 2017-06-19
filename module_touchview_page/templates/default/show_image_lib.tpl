<!--{<*
 * 显示图库页面模板
 * create time: 2011-11-22 上午10:50:23
 * @version $Id: show_image_lib.tpl 144 2012-06-14 08:49:50Z liqt $
 * @author LiQintao
*>}-->
<script>
$(function(){
	$('#{<$config.ul_id>}').jCarouselLite({
			scroll:{<$config.steps>}, 
			vertical:{<$config.vertical>}, 
			mouseWheel:true,
			visible:{<$config.size>},
			btnNext: ".next",
		    btnPrev: ".prev",
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
	width: 95%;
	height: 98%;
	border: 1px solid grey;
}

a.prev, a.next {
	text-decoration: none;
}
</style>
<span style="float:left;">
	<a class="prev" href="#">&lt;&lt;</a>
	<br/><br/>
	<a class="next" href="#">&gt;&gt;</a>
</span>
<div id="{<$config.ul_id>}">
	<ul >
{<section name=i loop=$data_list start=0>}
       	<li>
    		<img class="item_image_lib" src="{<$data_list[i].url>}" alt="{<$data_list[i].name>}"/>
        </li>
{</section>}
	</ul>
</div>
