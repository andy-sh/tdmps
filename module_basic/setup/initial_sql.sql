/**
 * description: scap系统初时数据表
 * create time: 2008-7-8-上午10:34:57
 * @version $Id: initial_sql.sql 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `scap_accounts` */

CREATE TABLE `scap_accounts` (
  `a_s_id` varchar(40) NOT NULL,
  `a_c_login_id` varchar(100) default NULL,
  `a_s_password` varchar(50) default NULL,
  `a_c_display_name` varchar(100) default NULL,
  `a_s_status` tinyint(3) unsigned default '0',
  `ag_s_id` varchar(50) default NULL,
  `a_s_create_time` datetime default NULL,
  `a_s_create_id` varchar(40) default NULL,
  `a_s_lastedit_time` datetime default NULL,
  `a_s_lastedit_id` varchar(40) default NULL,
  `a_c_note` text,
  PRIMARY KEY  (`a_s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_accounts` */

insert  into `scap_accounts`(`a_s_id`,`a_c_login_id`,`a_s_password`,`a_c_display_name`,`a_s_status`,`ag_s_id`,`a_s_create_time`,`a_s_create_id`,`a_s_lastedit_time`,`a_s_lastedit_id`,`a_c_note`) values ('10001','admin','d41d8cd98f00b204e9800998ecf8427e','admin',2,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `scap_acl` */

CREATE TABLE `scap_acl` (
  `acl_s_module` varchar(50) NOT NULL,
  `acl_s_account_id` varchar(40) NOT NULL,
  `acl_c_acl_code` bigint(20) unsigned default '0',
  PRIMARY KEY  (`acl_s_module`,`acl_s_account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_acl` */

insert  into `scap_acl`(`acl_s_module`,`acl_s_account_id`,`acl_c_acl_code`) values ('module_manage','10001',1);

/*Table structure for table `scap_app_log` */

CREATE TABLE `scap_app_log` (
  `al_object_id` varchar(40) NOT NULL,
  `al_type` smallint(5) unsigned NOT NULL default '0',
  `al_sn` smallint(5) unsigned NOT NULL default '0',
  `al_datetime` datetime default NULL,
  `al_operator_id` varchar(40) default NULL,
  PRIMARY KEY  (`al_object_id`,`al_type`,`al_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_app_log` */

/*Table structure for table `scap_config` */

CREATE TABLE `scap_config` (
  `c_s_module` varchar(50) NOT NULL,
  `c_s_key` varchar(50) NOT NULL,
  `c_c_value` text,
  PRIMARY KEY  (`c_s_module`,`c_s_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_config` */

insert  into `scap_config`(`c_s_module`,`c_s_key`,`c_c_value`) values ('module_manage','site_url','/itsm');

/*Table structure for table `scap_log` */

CREATE TABLE `scap_log` (
  `l_id` int(11) NOT NULL auto_increment,               
  `l_time` datetime default NULL,                       
  `l_module` varchar(50) default NULL,                  
  `l_operator_type` tinyint(3) unsigned default '0',    
  `l_operator_info` varchar(50) default NULL,           
  `l_from` varchar(50) default NULL,                    
  `l_act_type` tinyint(3) unsigned default '0',         
  `l_act_object_type` tinyint(3) unsigned default '0',  
  `l_act_object_info` varchar(50) default NULL,         
  `l_act_result` tinyint(3) unsigned default '0',       
  `l_note` varchar(50) default NULL,                    
   PRIMARY KEY  (`l_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_log` */

/*Table structure for table `scap_module_list` */

CREATE TABLE `scap_module_list` (
  `ml_s_id` varchar(50) NOT NULL,
  `ml_s_status` tinyint(3) unsigned default '0',
  `ml_c_order` tinyint(3) unsigned default '0',
  `ml_c_version` varchar(50) default NULL,
  PRIMARY KEY  (`ml_s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `scap_module_list` */

insert  into `scap_module_list`(`ml_s_id`,`ml_s_status`,`ml_c_order`,`ml_c_version`) values ('module_basic',0,100,'1.00'),('module_manage',2,1,'1.00');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;