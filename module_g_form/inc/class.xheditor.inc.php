<?php
/**
 * 编辑器服务类
 * 
 * @package module_g_form
 * @subpackage model
 * @version $Id: class.xheditor.inc.php 220 2013-02-06 02:05:29Z liqt $
 * @creator liqt @ 2013-01-31 10:54:01 by caster0.0.2
 */
namespace scap\module\g_form;

/**
 * 编辑器xheditor服务类
 */
class xheditor
{
    /**
     * 语言种类：简体中文
     * @var string
     */
    const LANGUAGE_ZH_CN = 'zh-cn';
    
    /**
     * 语言种类：繁体中文
     * @var string
     */
    const LANGUAGE_ZH_TW = 'zh-tw';
    
    /**
     * 语言种类：英文
     * @var string
     */
    const LANGUAGE_EN = 'en';
    
    /**
     * 加载xheditor所必需的js/css等文件
     * 
     * @param string $language 语言类型，默认LANGUAGE_ZH_CN
     */
    public static function load_base_file($language = self::LANGUAGE_ZH_CN)
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/xheditor/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."xheditor-{$language}.min.js");
        \scap_ui::insert_head_js_file($url_lib_scap."xheditor.js");// 加载scap下的js封装
    }
    
    /**
     * 获取上传文件所需默认的链接
     * 
     * @return string
     */
    public static function get_default_upload_url()
    {
        return scap_get_url(array('module' => 'module_g_form', 'class' => 'ui_server', 'method' => 'upload_for_xheditor'), array());
    }
    
    /**
     * 为xheditor提供图片及其他文件上传接口
     * - 改方法需在ui类方法中被调用，供upLinkUrl、upImgUrl、upFlashUrl和upMediaUrl参数调用
     * - 基于官方的upload.php文件：0.9.6 (build 111027)
     * 
     * @see http://xheditor.com/demos/demo08.html
     * 
     * @param string $dir_save 存储的相对路径(相对与项目根路径),默认为 media/upload
     * @param string $allow_ext 允许的上传扩展名，默认为空(使用默认值'txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid')
     * @param int $byte_max_size 限制的最大字节数，默认值为0（使用默认大小2M）
     * @param int $dir_type 生成子目录类型（默认为1），1:按天存入目录 2:按月存入目录 3:按扩展名存目录
     * 
     * @return string xheditor所需返回值
     */
    public static function excute_upload($dir_save = 'media/upload', $allow_ext = '', $byte_max_size = 0, $dir_type = 1)
    {
        $data_def = array();
        $data_out = array();
        
        $data_out['err'] = '';
        $data_out['msg'] = '';
        
        $data_def['input_name'] = 'filedata';//表单文件域name
        $data_def['msg_type'] = 2;//返回上传参数的格式：1，只返回url，2，返回参数数组
        $data_def['immediate'] = 0;//立即上传模式，仅为演示用
        
        $data_def['path_save'] = SCAP_PATH_ROOT.$dir_save;//上传文件保存路径，结尾不要带/
        if(!is_dir($data_def['path_save']))
        {
            @mkdir($data_def['path_save'], 0777);
        }
        
        // path_url:使用类似 "/taost/media/20130131/201301311739305082.jpg"的路径
        $arr_url = explode("/base", dirname($_SERVER['PHP_SELF']));// 获取系统的url根路径
        $data_def['path_url'] = $arr_url[0].'/'.$dir_save;//文件url路径
        
        $data_def['dir_type'] = $dir_type;
        $data_def['max_size'] = empty($byte_max_size) ? 2097152 : $byte_max_size;//最大上传大小，默认是2M
        $data_def['allow_ext'] = empty($allow_ext) ? 'txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid' : $allow_ext;//上传扩展名
        $data_def['temp_path'] =  $data_def['path_save'].'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';// 文件名称
        $data_def['local_name'] = '';// 本地文件名称
        
        //HTML5上传
        if(isset($_SERVER['HTTP_CONTENT_DISPOSITION']) && preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info))
        {
            file_put_contents($data_def['temp_path'],file_get_contents("php://input"));
            $data_def['local_name']=urldecode($info[2]);
        }
        else //标准表单式上传
        {
            $upfile=@$_FILES[$data_def['input_name']];
            
            if(!isset($upfile))
            {
                $data_out['err']='文件域的name错误';
            }
            elseif(!empty($upfile['error']))
            {
                switch($upfile['error'])
                {
                    case '1':
                        $data_out['err'] = '文件大小超过了php.ini定义的upload_max_filesize值';
                        break;
                    case '2':
                        $data_out['err'] = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
                        break;
                    case '3':
                        $data_out['err'] = '文件上传不完全';
                        break;
                    case '4':
                        $data_out['err'] = '无文件上传';
                        break;
                    case '6':
                        $data_out['err'] = '缺少临时文件夹';
                        break;
                    case '7':
                        $data_out['err'] = '写文件失败';
                        break;
                    case '8':
                        $data_out['err'] = '上传被其它扩展中断';
                        break;
                    case '999':
                    default:
                        $data_out['err'] = '无有效错误代码';
                }
            }
            elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')
            {
                $data_out['err'] = '无文件上传';
            }
            else
            {
                move_uploaded_file($upfile['tmp_name'], $data_def['temp_path']);
                $data_def['local_name'] = $upfile['name'];
            }
        }
        
        if($data_out['err']=='')
        {
            $fileInfo = pathinfo($data_def['local_name']);
            $extension = $fileInfo['extension'];
            
            if(preg_match('/^('.str_replace(',','|',$data_def['allow_ext']).')$/i', $extension))
            {
                $bytes = filesize($data_def['temp_path']);
                if($bytes > $data_def['max_size'])
                {
                    $data_out['err']='请不要上传大小超过'.\scap\module\g_tool\string::get_readable_from_bytes($data_def['max_size']).'的文件';
                }
                else
                {
                    switch($data_def['dir_type'])
                    {
                        case 1:
                            $attachSubDir = 'day_'.date('ymd');
                            break;
                        case 2:
                            $attachSubDir = 'month_'.date('ym');
                            break;
                        case 3:
                            $attachSubDir = 'ext_'.$extension;
                            break;
                    }
                    $data_def['path_save'] = $data_def['path_save'].'/'.$attachSubDir;
                    $data_def['path_url'] = $data_def['path_url'].'/'.$attachSubDir;
                    
                    if(!is_dir($data_def['path_save']))
                    {
                        @mkdir($data_def['path_save'], 0777);
                        @fclose(fopen($data_def['path_save'].'/index.htm', 'w'));
                    }
                    
                    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
                    $newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
                    $data_def['path_save'] = $data_def['path_save'].'/'.$newFilename;
                    $data_def['path_url'] = $data_def['path_url'].'/'.$newFilename;
                    	
                    rename($data_def['temp_path'], $data_def['path_save']);
                    @chmod($data_def['path_save'], 0666);// 无执行权限
                    
                    $data_def['path_url']= self::jsonString($data_def['path_url']);
                    
                    if($data_def['immediate']=='1')
                    {
                        $data_def['path_url']='!'.$data_def['path_url'];
                    }
                    
                    if($data_def['msg_type']==1)
                    {
                        $data_out['msg']="'{$data_def['path_url']}'";
                    }
                    else
                    {
                        $data_out['msg']="{'url':'".$data_def['path_url']."','localname':'".self::jsonString($data_def['local_name'])."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
                    }
                }
            }
            else
            {
                $data_out['err']='上传文件扩展名必需为：'.$data_def['allow_ext'];
            }

            @unlink($data_def['temp_path']);
        }
        
        return "{'err':'".self::jsonString($data_out['err'])."','msg':".$data_out['msg']."}";
    }
    
    private static function jsonString($str)
    {
        return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
    }
}
?>