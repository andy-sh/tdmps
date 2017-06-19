<?php
/**
 * description: module_g_00数据表
 * create time: 2008-12-15-下午03:28:23
 * @version $Id: db.tables.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

// 通用应用日志表
$scap_db_tables['g_app_log'] = array(
	'al_object_id' => array('al_object_id', 'C', '40', 'KEY'),	//对象id
	'al_type' => array('al_type', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'),	//所代表的类型,如是录入时间/最近编辑时间等.
	'al_sn' => array('al_sn', 'I2', 'UNSIGNED','DEFAULT' => 0, 'KEY'),	//序号
	'al_entity_id' => array('al_entity_id', 'C', '40'),// 实体id
	'al_time' => array('al_time', 'T'),	//操作时间
	'al_operator_id' => array('al_operator_id', 'C', '40'),	//执行操作的系统帐户ID
	'al_client_ip' => array('al_client_ip', 'C', '40'),	// 客户端ip地址
	'al_user_agent' => array('al_user_agent', 'C', '255'),	// 客户端信息
	'al_comment' => array('al_comment', 'X'),	// 备注说明
);
$scap_db_table_options['g_app_log'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 实体对象关系表
$scap_db_tables['g_object_relation'] = array(
	'or_primary_object_id' => array('or_primary_object_id', 'C', '40', 'KEY'), //主对象id
	'or_secondary_object_id' => array('or_secondary_object_id', 'C', '40', 'KEY'), //次对象id
	'or_relation_type' => array('or_relation_type', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'),//关系类型
	'or_primary_entity_id' => array('or_primary_entity_id', 'C', '40'),	//主对象所属实体的id
	'or_secondary_entity_id' => array('or_secondary_entity_id', 'C', '40'),	//次对象所属实体id
    'or_comment' => array('or_comment', 'X'),   // 备注说明
);
$scap_db_table_options['g_object_relation'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 实体对象状态表
$scap_db_tables['g_object_status'] = array(
	'os_object_id' => array('os_object_id', 'C', '40', 'KEY'), //对象id
	'os_status_type' => array('os_status_type', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'), //所表示状态的类型
	'os_sn' => array('os_sn', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'),//序号
	'os_entity_id' => array('os_entity_id', 'C', '40'),	// 所属实体id
	'os_status' => array('os_status', 'I2', 'UNSIGNED', 'DEFAULT' => 0),//状态值
	'os_trigger_time' => array('os_trigger_time', 'T'),	//状态触发时间
	'os_trigger_id' => array('os_trigger_id', 'C', '40'),	//触发者系统id
	'os_comment' => array('os_comment', 'X'),	// 备注说明
);
$scap_db_table_options['g_object_status'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 二进制文件表
$scap_db_tables['g_binary_data'] = array(
	'bd_id' => array('bd_id','C','40','KEY'), //文件内部ID
	'bd_storage_type' => array('bd_storage_type', 'I2', 'UNSIGNED', 'DEFAULT'=> 0), //文件存储类型:db / file system
	'bd_entity_id' => array('bd_entity_id', 'C', '40'), //对应的实体ID
	'bd_file_name' => array('bd_file_name', 'X'), //文件名称
	'bd_file_postfix' => array('bd_file_postfix', 'C', '80'), //文件后缀名称
	'bd_file_size' => array('bd_file_size', 'I4', 'UNSIGNED', 'NOTNULL', 'DEFAULT' => 0), //文件大小,单位为字节?
	'bd_file_type' => array('bd_file_type', 'C', '80'), //文件类型名称
	'bd_comment' => array('bd_comment', 'X'), //文件备注
	'bd_upload_time' => array('bd_upload_time', 'T'), //上传时间
	'bd_upload_id' => array('bd_upload_id', 'C', '40'), //上传账号id
);
$scap_db_table_options['g_binary_data'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 二进制文件内容 
$scap_db_tables['g_binary_data_content'] = array(
	'bd_id' => array('bd_id','C','40','KEY'), //文件内部ID
	'bdc_file_content' => array('bdc_file_content', 'B'), //文件内容
);
$scap_db_table_options['g_binary_data_content'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=MyISAM');

// 实体对象与文件关联表
$scap_db_tables['g_object_binary_link'] = array(
	'obl_object_id' => array('obl_object_id','C','40','KEY'), //实体对象id
	'obl_sn' => array('obl_sn', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'), //关联序号
	'obl_entity_id' => array('obl_entity_id', 'C', '40'), //对应实体id
	'obl_name' => array('obl_name', X), //名称
	'obl_category' => array('obl_category', 'C', '40'), //分类id
	'bd_id' => array('bd_id', 'C', 40), //二进制id
	'obl_logic_flag' => array('obl_logic_flag', 'I2', 'UNSIGNED', 'NOTNULL', 'DEFAULT' => 0), //逻辑标志为:为bo ui层所用:比如必填、只读标记等
	'obl_comment' => array('obl_comment', 'X') //备注说明
);
$scap_db_table_options['g_object_binary_link'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 实体对象与类别关联表
$scap_db_tables['g_object_category_link'] = array(
	'ocl_object_id' => array('ocl_object_id', 'C', '40', 'KEY'), //对象ID
	'ocl_sn' => array('ocl_sn', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'), //序号
	'ocl_entity_id' => array('ocl_entity_id', 'C', '40'), //所属实体id
	'ocl_category_id' => array('ocl_category_id', 'X'), //类别id
	'ocl_name' => array('ocl_name', 'X')	//连接名称
);
$scap_db_table_options['g_object_category_link'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 实体对象附加元素(k=>v形式的)表
$scap_db_tables['g_object_attach_element'] = array(
	'oae_object_id' => array('oae_object_id', 'C', '40', 'KEY'), //实体对象id
	'oae_sn' => array('oae_sn', 'I2', 'UNSIGNED', 'DEAFAULT' => 0, 'KEY'), //序号
	'oae_entity_id' => array('oae_entity_id', 'C', '40'), //实体id
	'oae_category' => array('oae_category', 'C', '40'), //类别
	'oae_name' => array('oae_name', 'X'), //附加要素名称
	'oae_value' => array('oae_value', 'X'), //附加要素值
	'oae_logic_flag' => array('oae_logic_flag', 'I2', 'UNSIGNED', 'NOTNULL', 'DEFAULT' => 0) //附加要素的逻辑标志,为bo ui层所用:比如必填、只读标记等
);
$scap_db_table_options['g_object_attach_element'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 时间节点表
$scap_db_tables['g_object_time_node'] = array(
	'otn_object_id' => array('otn_object_id', 'C', '40', 'KEY'), //实体对象id
	'otn_sn' => array('otn_sn', 'I2', 'UNSIGNED', 'KEY'), //序号
	'otn_entity_id' => array('otn_entity_id', 'C', '40'), //对应实体id
	'otn_name' => array('otn_name', 'X'), //名称
	'otn_category' => array('otn_category', 'C', '40'), //时间类别
	'otn_time' => array('otn_time', 'T') //时间
);
$scap_db_table_options['g_object_time_node'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');
?>