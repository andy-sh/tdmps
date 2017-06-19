// $Id: view.system_info.tpl 4 2012-07-18 06:40:23Z liqt $
{<section name=i loop=$sys_info  start=0>}
$.jnotify('{<$sys_info[i].icon>}{<$sys_info[i].text>}', 5000);
{</section>}