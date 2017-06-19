<?php
/**
 * description:系统管理权限定义
 * create time: 2010-3-18 02:27:25
 * @version $Id: define.module_acl.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author FuYing
 */

define('ACL_BIT_MANAGE_ACCOUNT_EDIT', 1); //账户管理编辑权限
define('ACL_BIT_MANAGE_CONFIG_EDIT', 2); //参数管理编辑权限
define('ACL_BIT_MANAGE_ACL_EDIT', 3); //权限管理编辑权限
define('ACL_BIT_MANAGE_MODULE_EDIT', 4); //模块管理编辑权限
define('ACL_BIT_MANAGE_LOG_VIEW', 5); //日志管理查看权限
define('ACL_BIT_MANAGE_SUPER', 6); //超级权限

/**
 * 本模块权限定义
 */
$GLOBALS['scap']['custom_acl']['module_manage'] = array(
        ACL_BIT_MODULE => array('acl_name' => '模块', 'acl_module' => 'module_manage', 'acl_comment' => "该模块的总体访问权限"),
        ACL_BIT_MANAGE_ACCOUNT_EDIT => array('acl_name' => '账户管理', 'acl_module' => 'module_manage', 'acl_comment' => ""),
        ACL_BIT_MANAGE_CONFIG_EDIT => array('acl_name' => '参数管理', 'acl_module' => 'module_manage', 'acl_comment' => ""),
        ACL_BIT_MANAGE_ACL_EDIT => array('acl_name' => '权限管理', 'acl_module' => 'module_manage', 'acl_comment' => ""),
        ACL_BIT_MANAGE_MODULE_EDIT => array('acl_name' => '模块管理', 'acl_module' => 'module_manage', 'acl_comment' => ""),
        ACL_BIT_MANAGE_LOG_VIEW => array('acl_name' => '日志管理', 'acl_module' => 'module_manage', 'acl_comment' => ""),
        ACL_BIT_MANAGE_SUPER => array('acl_name' => '超级权限', 'acl_module' => 'module_manage', 'acl_comment' => ""),
    );
?>