<?php
/**
 * 图片上传UI
 * create time: 2012-02-26 00:34:17
 * @version $Id: class.ui_image_upload.inc.php 4 2012-07-21 07:04:47Z liqt $
 * @author phoenix.x.gao@gmail.com
 */

/**
 * 图片上传UI类
 */
class ui_image_upload extends scap_module_ui
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 图片上传应用
     * 支持多图上传,默认上传路径是 media/
     * 
     */
    public function upload_image()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)
        
        $data_def['title'] = '上传图片';// 当前界面标题设置
        $data_def['text_menu'] = '';
        
        $data_def['url_plupload'] = $GLOBALS['scap']['info']['site_url'].'/module_g_image/inc/third/plupload/';
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        
        $data_in['get']['path'] = isset($_GET['path']) ? $_GET['path'] : 'media/';
        
        //--------GET参数处理[end]--------
        
        //--------构造界面输出[start]--------
        
        $data_out['url_upload'] = scap_get_url(array('module' => 'module_g_image', 'class' => 'ui_image_upload', 'method' => 'ajax_upload'), array('path' => $data_in['get']['path']));
        $data_out['url_flash'] = $data_def['url_plupload'].'plupload.flash.swf';
        $data_out['url_silverlight'] = $data_def['url_plupload'].'plupload.silverlight.xap';
        
        scap_module_ui::load_css_file($data_def['url_plupload'].'jquery.plupload.queue/css/jquery.plupload.queue.css');
        scap_module_ui::load_js_file($data_def['url_plupload'].'plupload.full.js');
        scap_module_ui::load_js_file($data_def['url_plupload'].'i18n/zh-cn.js');
        scap_module_ui::load_js_file($data_def['url_plupload'].'jquery.plupload.queue/jquery.plupload.queue.js');
        
        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'upload_image.tpl', $data_out, false);
        //--------构造界面输出[end]----------
    }

    /**
     * 图片上传ajax应用
     */
    public function ajax_upload()
    {
        // 获取GET参数
        $data_in['get']['path'] = $_GET['path'];
        
        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // ********** 设置[start] *********
        // 设置上传目录
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $targetDir = SCAP_PATH_ROOT . $data_in['get']['path'];
        
        $cleanupTargetDir = true; // 删除过期临时文件
        $maxFileAge = 5 * 3600; // 临时文件寿命
        
        // 5 分钟执行时间限制
        @set_time_limit(5 * 60);
        
        // 取消注释模拟上传时间
        // usleep(5000);

        // ********** 设置[end] *********
        
        // 获取参数
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        
        // 为了安全处理文件名
        $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
        
        // 当chunking关闭时 确认文件名的唯一性
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
        
            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;
        
            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }
        
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        
        // 如不存在目录，则创建
        if (!file_exists($targetDir))
            @mkdir($targetDir);
        
        // 删除过期临时文件
        if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        
                // 如果临时文件创建时间大于设定的临时文件寿命，并且不是正在上传的文件，则删除之
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                    @unlink($tmpfilePath);
                }
            }
        
            closedir($dir);
        } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            
        
        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        
        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];
        
        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");
        
                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");
        
                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
        
                fclose($in);
                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        
        // 检查文件是否上传完
        if (!$chunks || $chunk == $chunks - 1) {
            // 去掉临时文件的.part后缀
            rename("{$filePath}.part", $filePath);
        }
        
        
        // Return JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
    
}
?>