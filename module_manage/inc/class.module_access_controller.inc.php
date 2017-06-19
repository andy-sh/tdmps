<?php
/**
 * description:系统管理访问控制类
 * create time: 2010-3-10 03:24:56
 * @version $Id: class.module_access_controller.inc.php 145 2013-08-22 05:43:43Z liqt $
 * @author FuYing
 */

class module_access_controller extends scap_module_access_controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function validate_access_point()
    {
        $result = true;
        
        switch($this->current_method_name)
        {
            case 'edit_account':
                $result = $this->access_edit_account();
                break;
            case 'index_account':
                $result = $this->access_index_account();
                break;
            case 'edit_config':
                $result = $this->access_edit_config();
                break;
            case 'index_config':
                $result = $this->access_index_config();
                break;
            case 'index_acl':
            case 'assign_acl':
                $result = $this->access_index_acl();
                break;
            case 'index_module':
                $result = $this->access_index_module();
                break;
            case 'index_log':
                $result = $this->access_index_log();
                break;
        }
        
        return $result;
    }
    
    /**
     * 编辑账户
     * @return bool
     */
    private function access_edit_account()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_ACCOUNT_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_ACCOUNT_EDIT);
        if ($result) return $result;
        
        return $result;
    }
    
    /**
     * 索引账户
     * @return bool
     */
    private function access_index_account()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_ACCOUNT_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_ACCOUNT_EDIT);
        if ($result) return $result;
        
        return $result;
    }
    
    /**
     * 编辑配置
     * @return bool
     */
    private function access_edit_config()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_CONFIG_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_CONFIG_EDIT);
        if ($result) return $result;
        
        return $result;
    }
    
    /**
     * 索引配置
     * @return bool
     */
    private function access_index_config()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_CONFIG_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_CONFIG_EDIT);
        if ($result) return $result;
        
        return $result;
    }

    /**
     * 索引访问权限设置
     * @return bool
     */
    private function access_index_acl()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_ACL_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_ACL_EDIT);
        if ($result) return $result;
        
        return $result;        
    }
    
    /**
     * 索引模块
     * @return bool
     */
    private function access_index_module()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;         
        
        // ACL_BIT_MANAGE_MODULE_EDIT权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_MODULE_EDIT);
        if ($result) return $result;
        
        return $result;      
    }

    /**
     * 索引操作日志
     * @return bool
     */    
    private function access_index_log()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        // admin帐户始终允许
        $result = scap_check_current_account_is_admin();
        if ($result) return $result;  

        // ACL_BIT_MANAGE_SUPER权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_SUPER);
        if ($result) return $result;        
        
        // ACL_BIT_MANAGE_LOG_VIEW权限位
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MANAGE_LOG_VIEW);
        if ($result) return $result;
        
        return $result;        
    }
}

?>