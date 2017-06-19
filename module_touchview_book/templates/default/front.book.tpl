{<*<!--
 * 前端单个书籍页面
 * create time: 2012-1-16 上午11:33:38
 * @version $Id: front.book.tpl 162 2014-02-17 05:51:29Z liqt $
 * @author LiQintao
-->*>}
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=1000">
		<meta http-equiv="X-UA-Compatible" content="chrome=1">
		
		<title>{<$b_name>}</title>
		
		<link type="text/css" href="module_touchview_book/templates/default/css/book.main.css" rel="stylesheet" media="screen" />
		
		<script type="text/javascript" src="{<$url_jquery>}"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/third/jquery.json-2.2.min.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/third/browserdetect.js"></script>
		
		<script>
		  	var flag_prompt_gcf = false;
			var SERVER_VARIABLES = {
            	BOOK_ID: "{<$b_id>}",
                TEMPLATE: "{<$path_template>}",
                FORCE_UPDATE: {<$force_update>},
                AUTO_FLIP_SWITCH: {<$config_auto_flip_switch>},
                AUTO_FLIP_WAITING: {<$config_auto_flip_waiting>},
                PAGES_COUNT:  {<$pages_count>},
                BOOK_NAME:  "{<$b_name>}"
            };
        </script>
	</head>
	
	<body class="{<$body_class>}">
    	<!--[if IE]>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
    
        <style>
         .chromeFrameInstallDefaultStyle {
           width: 100%; /* default is 800px */
           border: 5px solid blue;
           z-index: 9999999;
         }
        </style>
    
        <div id="prompt">
         <!-- if IE without GCF, prompt goes here -->
        </div>
     
        <script>
        	if (BrowserDetect.browser == 'Explorer')
        	{
             	window.attachEvent("onload", function() {
               		CFInstall.check({
                 		mode: "inline", // the default
                 		node: "prompt"
               		});
             		if (!CFInstall.isAvailable())
             		{
             			flag_prompt_gcf = true;
             		}
             	});
        	}
        </script>
  		<![endif]-->
		
		<!-- 加载动画显示 -->
    	<div id="preloader">
    		<div class="contents">
    			<canvas class="animation"></canvas>
    			<div class="progress">{<*预加载进度条*>}
    				<div class="fill"></div>
    			</div>
    		</div>
    	</div>
    	
    	<header>
    		<a class="book-name go-home" href="?m=module_touchview_book.ui_book_front.book&view=home&b_id={<$b_id>}">&lt;&lt;{<$b_name>}&gt;&gt;</a>
    		<a class="book-cover go-home" href="?m=module_touchview_book.ui_book_front.book&view=home&b_id={<$b_id>}">封&nbsp;面</a>
    		<a class="book-toc" href="?m=module_touchview_book.ui_book_front.book&view=tot&b_id={<$b_id>}">目&nbsp;录</a>
    		<a class="book-list" href="?m=module_touchview_book.ui_book_front.book&view=credits&b_id={<$b_id>}">选&nbsp;书</a>
    		<a class="book-update" href="?m=module_touchview_book.ui_book_front.book&view=home&force_update=1&b_id={<$b_id>}">更&nbsp;新</a>
		</header>
		
		<div id="book">
			<div id="shadow">{<*书本周边阴影*>}
				<div class="shadow-left"></div>
				<div class="shadow-right"></div>
			</div>
			
    		<div id="front-cover">{<*封面页面素材*>}
    			<canvas id="front-cover-image" width="830" height="520"></canvas>
    		</div>
    		
    		<div id="back-cover">{<*封底页面素材*>}
      			<img src="{<$path_template>}back-cover.jpg" data-src-flipped="{<$path_template>}back-cover-flipped.jpg" width="830" height="520">
      		</div>
      		
      		<div id="page-shadow-overlay"></div>{<*封面内页阴影层*>}
      		
    		<div id="pages">{<*加载所有page的容器*>}
{<if $is_article>}
				<section class="{<$current_page.class>}">
					<div class="page">
	{<if $current_page.number == 1>}
						<div class="page-title">
							<h2>{<$current_page.title>}</h2>
		{<if $current_page.subtitle>}
							<h3>{<$current_page.subtitle>}</h3>
		{</if>}
						</div>
	{</if>}
						{<$current_page.content>}
					</div>
				</section>
{</if>}
			</div>
    		
    		<div id="left-page">{<*左边页面素材*>}
    			<img src="{<$path_template>}left-page.jpg" data-src-flipped="{<$path_template>}left-page-flipped.jpg" width="830" height="520">
    		</div>
    		
    		<div id="right-page">{<*右边页面素材*>}
    			<div id="paperstack">{<*所剩纸张厚度效果*>}
    				<div class="paper s1"></div>
    				<div class="paper s2"></div>
    				<div class="paper s3"></div>
    				<div class="paper s4"></div>
    				<div class="paper s5"></div>
    				<div class="paper s6"></div>
    				<div class="paper s7"></div>
    				<div class="shadow"></div>
    			</div>
    			<img src="{<$path_template>}right-page.jpg" width="830" height="520">
    		</div>
    	</div><!--  -->
    	
		<div id="credits">
			<div class="header">
				<h2><span>书架</span></h2>
				<hr>
			</div>
			<ul>
{<section name=i loop=$book_list start=0>}
				<li class="{<$book_list[i].class>}">
					<a href="{<$book_list[i].link>}">
						<div class="medium-book">
							<h3>{<$book_list[i].name>}</h3>
						</div>
					</a>
				</li>
{</section>}
			</ul>
		</div>
		
		<div id="table-of-contents">{<*书籍目录展现*>}
			<div class="center">
				<div class="header">
					<a class="go-back" href="/">返回</a>
					<h2><span>"{<$b_name>}"目录</span></h2>
					<hr>
				</div>
				<ul>
{<section name=i loop=$toc_list start=0>}
    				<li class="{<$toc_list[i].class>}">
    					<a href="{<$toc_list[i].link>}" data-article="{<$toc_list[i].article>}">
    						<div class="medium-book">
    							<p>章节{<$toc_list[i].chapter_counter>}</p>
    						</div>
    						<h3>{<$toc_list[i].title>}</h3>
    					</a>
    				</li>
{</section>}
				</ul>
			</div>
		</div>
				
		<footer>		
			<span id="product-info">
				触控式数字媒体发布系统(TDMPS) 1.0
			</span>

			<span id="company-info">
				<!-- ©2012 上海热信信息技术有限公司（电话：021-60490765，18616860997）-->
			</span>
		</footer>
		
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.preloader.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.history.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.storage.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.pageflip.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.paperstack.js"></script>
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.navigation.js"></script>
<!--		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.chapternav.js"></script>-->
		<script type="text/javascript" src="module_touchview_book/templates/default/js/book.tableofthings.js"></script>
		
		<script>
			if (!flag_prompt_gcf) TT.initialize();
		</script>
	</body>
</html>