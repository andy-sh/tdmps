<?php
/**
 * 支持字符串作为模板来源的插件
 * create time: 2010-9-1 13:04:19
 * @version $Id: resource.text.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */
function smarty_resource_text_source($tpl_name, &$tpl_source, &$smarty_obj)
{
    $tpl_source = $tpl_name;
    return true;
}

function smarty_resource_text_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
    $tpl_timestamp = time();
    return true;
}

function smarty_resource_text_secure($tpl_name, &$smarty_obj)
{
    return true;
}

function smarty_resource_text_trusted($tpl_name, &$smarty_obj)
{}

?>