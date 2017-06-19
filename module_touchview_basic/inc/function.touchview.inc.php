<?php
/**
 * touchview函数库
 * create time: 2012-3-22 下午04:45:29
 * @version $Id: function.touchview.inc.php 103 2012-03-26 14:38:32Z liqt $
 * @author LiQintao
 */

/**
 * 删除文件夹（不为空也可删除）
 * 
 * @param string $dir 文件夹地址
 * 
 * @return bool
 */
function touchview_rmdir($dir)
{
    if (!file_exists($dir))
    {
        return true;
    }
    
    if (!is_dir($dir) || is_link($dir))
    {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item)
    {
        if ($item == '.' || $item == '..')
        {
            continue;
        }
        
        if (!touchview_rmdir($dir . "/" . $item))
        {
            chmod($dir . "/" . $item, 0777);
            if (!touchview_rmdir($dir . "/" . $item))
            {
                return false;
            }
        };
    }
    return rmdir($dir);
}

/**
 * 打包指定路径到zip文件
 * use:touchview_zip('/folder/to/compress/', './compressed.zip');
 * 
 * @param string $source 待压缩路径
 * @param string $destination 压缩后存储路径
 * 
 * @return bool
 */
function touchview_zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source))
    {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE))
    {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file)
        {
            if (basename($file) == '.' || basename($file) == '..')
            {
                continue;
            }
            $file = str_replace('\\', '/', realpath($file));
            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            elseif (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    elseif (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

/**
 * 将html中的图片转存至本地，并替换img链接至本地 media/
 * @param string $html
 *
 * @return string
 */
function touchview_transfer_image_to_local($html)
{
    require_once SCAP_PATH_ROOT.'module_touchview_basic/inc/third/simple_html_dom.php';
    
    $result = '';
    
    if (empty($html))
    {
        return $result;
    }

    $html_dom = str_get_html($html);
    
    foreach(@$html_dom->find('img') as $element)
    {
        if (stripos($element->src, "media/") === 0 || stripos($element->src, "{$GLOBALS['scap']['info']['site_url']}/media/") === 0)
        {
            continue;
        }

        $temp_content = file_get_contents($element->src);
        if ($temp_content == false)
        {
            continue;
        }

        $temp_id = scap_get_guid().".".pathinfo($element->src, PATHINFO_EXTENSION);
        $temp_image_name = SCAP_PATH_ROOT."media/{$temp_id}";
        
        file_put_contents($temp_image_name, $temp_content);

        $element->src = "media/{$temp_id}";
    }
    
    return $html_dom;
}
?>