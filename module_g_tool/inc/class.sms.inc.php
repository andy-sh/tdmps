<?php
/**
 * 短信发送类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.sms.inc.php 241 2013-02-17 09:12:24Z shengyj $
 * @creator liqt @ 2013-01-15 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

use scap\module\g_tool\config;

/**
 * 文件处理类
 */
class sms
{
    /**
     * 供应商id:上海助通信息科技有限公司
     * 
     * @var string
     */
    const PROVIDER_ZHUTONG = 'zhutong';
    
    /**
     * 供应商id:逸动信息
     * 
     * @var string
     */
    const PROVIDER_YIDONG = 'yidong';
    
    /**
     * 发送一个短信到指定手机
     * 
     * @param string $mobile 手机号码
     * @param string $content 短信内容
     * @param string $text_signature 短信签名内容，默认为NULL，使用配置签名内容
     */
    public static function send($mobile, $content, $text_signature = NULL)
    {
        // 自动加载模块配置文件
        config::autoload_files(SCAP_PATH_ROOT.'module_g_tool/config/');
        
        if (is_null($text_signature))
        {
            $text_signature = config::get('sms', 'signature');
        }
        
        // 附加签名
        $content = sprintf('%s【%s】', $content, $text_signature);
        
        $content = urlencode($content);
        
        $provider = config::get('sms', 'default_provider');
        
        if (empty($provider))
        {
            return NULL;
        }
        
        $rest_api = sprintf(
                config::get("sms.$provider", 'url'), // url配置：参数约定：用户名，密码，手机号，发送内容
                config::get("sms.$provider", 'account'), 
                config::get("sms.$provider", 'password'),
                $mobile,
                $content
        );
        
        return @file_get_contents($rest_api);
    }
}
?>