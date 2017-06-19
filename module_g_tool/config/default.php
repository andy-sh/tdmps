<?php
/**
 * g_tool默认配置文件
 * 
 * @category module_g_tool
 * @package config
 * @version $Id: default.php 699 2013-08-06 09:47:03Z fuy $
 * @creator liqt @ 2013-07-16 09:46:00
 */
use scap\module\g_tool\config;
use scap\module\g_tool\sms;

config::set('sms', 'default_provider', sms::PROVIDER_ZHUTONG, 1);// 默认短信供应商
config::set('sms', 'signature', "SCAP", 1);// 短信结尾签名

// 短信供应商参数配置：sms::PROVIDER_YIDONG
config::set('sms.'.sms::PROVIDER_YIDONG, 'account', "ckang", 1);
config::set('sms.'.sms::PROVIDER_YIDONG, 'password', "ckang_1q2w3e4r", 1);
// url配置：参数约定：用户名，密码，手机号，发送内容
config::set('sms.'.sms::PROVIDER_YIDONG, 'url', "http://114.80.208.222:8080/NOSmsPlatform/server/SMServer.htm?types=send&account=%s&password=%s&destmobile=%s&msgText=%s", 1);

// 短信供应商参数配置：sms::PROVIDER_ZHUTONG
config::set('sms.'.sms::PROVIDER_ZHUTONG, 'account', "cou", 1);
config::set('sms.'.sms::PROVIDER_ZHUTONG, 'password', "mars_cou", 1);
// url配置：参数约定：用户名，密码，手机号，发送内容
config::set('sms.'.sms::PROVIDER_ZHUTONG, 'url', "http://www.ztsms.cn:8800/sendXSms.do?username=%s&password=%s&mobile=%s&content=%s&dstime=&productid=48661&xh=", 1);
?>