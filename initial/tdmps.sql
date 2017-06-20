-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `p_page_update_sort`;;
CREATE PROCEDURE `p_page_update_sort`(
	IN book_id char(40) # 所属book id
)
BEGIN
	SET @count=0;#!
	UPDATE touchview_page SET p_sort_sn =  (@count:=(@count+1))  WHERE b_id = book_id ORDER BY p_sort_sn, p_sn ASC;#!
END;;

DELIMITER ;

CREATE TABLE `g_app_log` (
  `al_object_id` varchar(40) NOT NULL,
  `al_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `al_sn` smallint(5) unsigned NOT NULL DEFAULT '0',
  `al_entity_id` varchar(40) DEFAULT NULL,
  `al_time` datetime DEFAULT NULL,
  `al_operator_id` varchar(40) DEFAULT NULL,
  `al_client_ip` varchar(40) DEFAULT NULL,
  `al_user_agent` varchar(255) DEFAULT NULL,
  `al_comment` text,
  PRIMARY KEY (`al_object_id`,`al_type`,`al_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `g_app_log` (`al_object_id`, `al_type`, `al_sn`, `al_entity_id`, `al_time`, `al_operator_id`, `al_client_ip`, `al_user_agent`, `al_comment`) VALUES
('E90165946E0E1CB5B999BDC2899ECFA7',	1,	1,	'88DA4EF044442E09CF697102322BD6A0',	'2017-06-20 11:53:41',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('7DFCC88651DD776DAF301BFA5FE6FF13',	1,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:54:23',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('A66102B9868A409FB6154F1D643B6FBC',	1,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:54:34',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('7DFCC88651DD776DAF301BFA5FE6FF13',	10,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:54:41',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	'移动【2】>【三体评论】。'),
('67E2160AE0D93E5DF96039D76BB1C277',	1,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:55:03',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('7DFCC88651DD776DAF301BFA5FE6FF13',	10,	2,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:56:08',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('7DFCC88651DD776DAF301BFA5FE6FF13',	10,	3,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:57:44',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('22934871BE3D3C54D717528FC93B4A4C',	1,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:58:13',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('22934871BE3D3C54D717528FC93B4A4C',	10,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:58:36',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('A66102B9868A409FB6154F1D643B6FBC',	10,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:59:16',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('A66102B9868A409FB6154F1D643B6FBC',	10,	2,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 11:59:54',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('A66102B9868A409FB6154F1D643B6FBC',	10,	3,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 12:00:12',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	'重命名【对质子的看法】>【《星际穿越》与《三体》】。'),
('A66102B9868A409FB6154F1D643B6FBC',	10,	4,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 12:00:25',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	'重命名【《星际穿越》与《三体》】>【星际穿越与三体】。'),
('67E2160AE0D93E5DF96039D76BB1C277',	10,	1,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 12:02:12',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	''),
('67E2160AE0D93E5DF96039D76BB1C277',	10,	2,	'DBA66B370FD38F5A7E55D0A79B626A75',	'2017-06-20 12:03:06',	'10001',	'127.0.0.1',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',	'');

CREATE TABLE `g_binary_data` (
  `bd_id` varchar(40) NOT NULL,
  `bd_storage_type` smallint(5) unsigned DEFAULT '0',
  `bd_entity_id` varchar(40) DEFAULT NULL,
  `bd_file_name` text,
  `bd_file_postfix` varchar(80) DEFAULT NULL,
  `bd_file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `bd_file_type` varchar(80) DEFAULT NULL,
  `bd_comment` text,
  `bd_upload_time` datetime DEFAULT NULL,
  `bd_upload_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`bd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_binary_data_content` (
  `bd_id` varchar(40) NOT NULL,
  `bdc_file_content` longblob,
  PRIMARY KEY (`bd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_attach_element` (
  `oae_object_id` varchar(40) NOT NULL,
  `oae_sn` smallint(5) unsigned NOT NULL,
  `oae_entity_id` varchar(40) DEFAULT NULL,
  `oae_category` varchar(40) DEFAULT NULL,
  `oae_name` text,
  `oae_value` text,
  `oae_logic_flag` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`oae_object_id`,`oae_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_binary_link` (
  `obl_object_id` varchar(40) NOT NULL,
  `obl_sn` smallint(5) unsigned NOT NULL DEFAULT '0',
  `obl_entity_id` varchar(40) DEFAULT NULL,
  `obl_name` text,
  `obl_category` varchar(40) DEFAULT NULL,
  `bd_id` varchar(40) DEFAULT NULL,
  `obl_logic_flag` smallint(5) unsigned NOT NULL DEFAULT '0',
  `obl_comment` text,
  PRIMARY KEY (`obl_object_id`,`obl_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_category_link` (
  `ocl_object_id` varchar(40) NOT NULL,
  `ocl_sn` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ocl_entity_id` varchar(40) DEFAULT NULL,
  `ocl_category_id` text,
  `ocl_name` text,
  PRIMARY KEY (`ocl_object_id`,`ocl_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_relation` (
  `or_primary_object_id` varchar(40) NOT NULL,
  `or_secondary_object_id` varchar(40) NOT NULL,
  `or_relation_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `or_primary_entity_id` varchar(40) DEFAULT NULL,
  `or_secondary_entity_id` varchar(40) DEFAULT NULL,
  `or_comment` text,
  PRIMARY KEY (`or_primary_object_id`,`or_secondary_object_id`,`or_relation_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_status` (
  `os_object_id` varchar(40) NOT NULL,
  `os_status_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `os_sn` smallint(5) unsigned NOT NULL DEFAULT '0',
  `os_entity_id` varchar(40) DEFAULT NULL,
  `os_status` smallint(5) unsigned DEFAULT '0',
  `os_trigger_time` datetime DEFAULT NULL,
  `os_trigger_id` varchar(40) DEFAULT NULL,
  `os_comment` text,
  PRIMARY KEY (`os_object_id`,`os_status_type`,`os_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `g_object_time_node` (
  `otn_object_id` varchar(40) NOT NULL,
  `otn_sn` smallint(5) unsigned NOT NULL,
  `otn_entity_id` varchar(40) DEFAULT NULL,
  `otn_name` text,
  `otn_category` varchar(40) DEFAULT NULL,
  `otn_time` datetime DEFAULT NULL,
  PRIMARY KEY (`otn_object_id`,`otn_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `scap_accounts` (
  `a_s_id` varchar(40) NOT NULL,
  `a_c_login_id` varchar(100) DEFAULT NULL,
  `a_s_password` varchar(50) DEFAULT NULL,
  `a_c_display_name` varchar(100) DEFAULT NULL,
  `a_s_status` tinyint(3) unsigned DEFAULT '0',
  `ag_s_id` varchar(50) DEFAULT NULL,
  `a_s_create_time` datetime DEFAULT NULL,
  `a_s_create_id` varchar(40) DEFAULT NULL,
  `a_s_lastedit_time` datetime DEFAULT NULL,
  `a_s_lastedit_id` varchar(40) DEFAULT NULL,
  `a_c_note` text,
  PRIMARY KEY (`a_s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `scap_accounts` (`a_s_id`, `a_c_login_id`, `a_s_password`, `a_c_display_name`, `a_s_status`, `ag_s_id`, `a_s_create_time`, `a_s_create_id`, `a_s_lastedit_time`, `a_s_lastedit_id`, `a_c_note`) VALUES
('10001',	'admin',	'6eeef33c08dbf3f48a1e7f0bb31537da',	'admin',	2,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

CREATE TABLE `scap_acl` (
  `acl_s_module` varchar(50) NOT NULL,
  `acl_s_account_id` varchar(40) NOT NULL,
  `acl_c_acl_code` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`acl_s_module`,`acl_s_account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `scap_acl` (`acl_s_module`, `acl_s_account_id`, `acl_c_acl_code`) VALUES
('module_manage',	'10001',	1),
('module_touchview_book',	'10001',	1);

CREATE TABLE `scap_app_log` (
  `al_object_id` varchar(40) NOT NULL,
  `al_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `al_sn` smallint(5) unsigned NOT NULL DEFAULT '0',
  `al_datetime` datetime DEFAULT NULL,
  `al_operator_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`al_object_id`,`al_type`,`al_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `scap_config` (
  `c_s_module` varchar(50) NOT NULL,
  `c_s_key` varchar(50) NOT NULL,
  `c_c_value` text,
  PRIMARY KEY (`c_s_module`,`c_s_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `scap_config` (`c_s_module`, `c_s_key`, `c_c_value`) VALUES
('module_manage',	'site_url',	'/itsm'),
('module_manage',	'auth_type',	'1'),
('module_manage',	'site_name',	'SCAP系统'),
('module_manage',	'default_url',	'module_basic.ui.welcome'),
('module_manage',	'flag_record_log',	'1'),
('module_manage',	'welcome',	'欢迎进入SCAP系统。'),
('module_manage',	'ldap_port',	'389'),
('module_manage',	'ldap_base_dn',	'ou=People,dc=youdomain,dc=com'),
('module_manage',	'ldap_host',	'localhost');

CREATE TABLE `scap_log` (
  `l_id` int(11) NOT NULL AUTO_INCREMENT,
  `l_time` datetime DEFAULT NULL,
  `l_module` varchar(50) DEFAULT NULL,
  `l_operator_type` tinyint(3) unsigned DEFAULT '0',
  `l_operator_info` varchar(50) DEFAULT NULL,
  `l_from` varchar(50) DEFAULT NULL,
  `l_act_type` tinyint(3) unsigned DEFAULT '0',
  `l_act_object_type` tinyint(3) unsigned DEFAULT '0',
  `l_act_object_info` varchar(50) DEFAULT NULL,
  `l_act_result` tinyint(3) unsigned DEFAULT '0',
  `l_note` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `scap_log` (`l_id`, `l_time`, `l_module`, `l_operator_type`, `l_operator_info`, `l_from`, `l_act_type`, `l_act_object_type`, `l_act_object_info`, `l_act_result`, `l_note`) VALUES
(1,	'2017-06-19 12:28:39',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	1,	1,	'',	1,	''),
(2,	'2017-06-20 11:43:42',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	1,	1,	'',	1,	''),
(3,	'2017-06-20 12:01:12',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	1,	1,	'',	1,	''),
(4,	'2017-06-20 12:13:18',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	2,	1,	'',	1,	''),
(5,	'2017-06-20 12:13:20',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	1,	1,	'',	2,	'口令错误'),
(6,	'2017-06-20 12:13:25',	'module_basic',	1,	'admin[admin]',	'127.0.0.1',	1,	1,	'',	1,	'');

CREATE TABLE `scap_module_list` (
  `ml_s_id` varchar(50) NOT NULL,
  `ml_s_status` tinyint(3) unsigned DEFAULT '0',
  `ml_c_order` tinyint(3) unsigned DEFAULT '0',
  `ml_c_version` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ml_s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `scap_module_list` (`ml_s_id`, `ml_s_status`, `ml_c_order`, `ml_c_version`) VALUES
('module_basic',	0,	100,	'1.2.6.tdmps.1'),
('module_manage',	2,	1,	'1.3.8'),
('module_g_00',	2,	100,	'1.2.4'),
('module_g_jquery',	2,	100,	'0.0.7'),
('module_g_form',	2,	100,	'0.2.2.tdmps.1'),
('module_g_image',	2,	100,	'0.4.4'),
('module_g_template',	2,	100,	'0.0.3'),
('module_g_tool',	2,	100,	'0.1.9'),
('module_g_yaml',	2,	100,	'0.2.5'),
('module_touchview_basic',	2,	100,	'1.0.2'),
('module_touchview_book',	2,	100,	'1.1.0'),
('module_touchview_page',	2,	100,	'1.1.0');

CREATE TABLE `touchview_book` (
  `b_sn` int(11) NOT NULL AUTO_INCREMENT,
  `b_id` varchar(40) DEFAULT NULL,
  `b_status` tinyint(3) unsigned DEFAULT '0',
  `b_sort_sn` smallint(6) DEFAULT '0',
  `b_name` text,
  `b_description` text,
  `b_config` text,
  PRIMARY KEY (`b_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `touchview_book` (`b_sn`, `b_id`, `b_status`, `b_sort_sn`, `b_name`, `b_description`, `b_config`) VALUES
(5,	'E90165946E0E1CB5B999BDC2899ECFA7',	1,	100,	'三体评论',	'演示用例',	'{\"tpl\":\"02\"}');

CREATE TABLE `touchview_page` (
  `p_sn` int(11) NOT NULL AUTO_INCREMENT,
  `p_id` varchar(40) DEFAULT NULL,
  `b_id` varchar(40) DEFAULT NULL,
  `p_type` smallint(5) unsigned DEFAULT '0',
  `p_parent_id` varchar(40) DEFAULT NULL,
  `p_sort_sn` smallint(6) DEFAULT '0',
  `p_name` text,
  `p_content` text,
  `p_config` text,
  PRIMARY KEY (`p_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `touchview_page` (`p_sn`, `p_id`, `b_id`, `p_type`, `p_parent_id`, `p_sort_sn`, `p_name`, `p_content`, `p_config`) VALUES
(9,	'7DFCC88651DD776DAF301BFA5FE6FF13',	'E90165946E0E1CB5B999BDC2899ECFA7',	2,	'E90165946E0E1CB5B999BDC2899ECFA7',	1,	'总论',	'<p>对中国科幻作家刘慈欣最常见的评价是：他以一己之力将中国科幻文学提高到世界级水平，然而这个评价中的使命感与恢宏感，与刘慈欣本人的随意、平常，甚至中庸形成对比。</p><p>2009年至2010年，刘慈欣陆续出版长篇科幻小说三部曲《三体》——《三体1：地球往事》《三体2：黑暗森林》《三体3：死神永生》。耗费他三年写成，一出版即引起科幻文学界注意，影响力逐渐发酵，终成为过去20年中国最著名且最畅销的长篇科幻读物：截至目前，《三体》三部曲总计售出超过40万套（每套3册，约120万册）， 电影改编权已售出，主流文学热情拥抱刘慈欣，他的知名度远远超出科幻文学领域。<br /></p><p><img src=\"https://static01.nyt.com/images/2014/05/27/t-magazine/cn-tc27liucixin-inline3/cn-tc27liucixin-inline3-articleLarge.png\" width=\"400\" height=\"250\" alt=\"\" /></p>',	'{\"tpl\":\"tpl-page-default\"}'),
(10,	'A66102B9868A409FB6154F1D643B6FBC',	'E90165946E0E1CB5B999BDC2899ECFA7',	2,	'E90165946E0E1CB5B999BDC2899ECFA7',	3,	'星际穿越与三体',	'<p>美国著名科幻作家大卫·布林(David Brin)在推特上称赞道： “ 生动、极具想象力并且依托于前沿科学，《三体》思考了我们时代的诸多重大问题。刘慈欣站在了无论任何语言的推测思索性小说的顶峰。刘宇昆流畅的翻译让它成为了任何对探索怀有真诚激情的人的必读物。”</p><p><br /></p><p>诸多普通读者也在亚马逊上慷慨打出五星的满分评价，当然其中不乏来自大陆的狂热粉丝。<br />就在《三体》英文版登陆美国的第二天，在太平洋的另一侧中国，一场科幻热潮正在迅速搅动漩涡，只不过这次掀起波澜的是一部好莱坞电影。</p><p><br /></p><p>11月12日，克里斯托弗 诺兰(Christopher Nolan)执导的科幻片《星际穿越》(Interstellar)在大陆公映，这部在上映前便被寄予厚望的作品，一出场便被诸多影迷及影评人誉为烧脑神作、有史以来最硬的科幻片。《星际穿越》继承了导演诺兰在诸多前作（《记忆迷宫》、《黑暗骑士》、《盗梦空间》等）中坚持的传统：现实主义美学、精巧复杂的剧作结构、饱满纠结的人物刻画，以及拒绝3D、CG的纯手工视觉奇观。影片讲述了人类濒临灭亡之际，一队宇航员为拯救人类，踏上一段穿越时空边境的星际之旅，最终以一种近乎机械降神式的方案解决困境，重建家园。<br /></p>',	'{\"tpl\":\"tpl-page-default\"}'),
(11,	'67E2160AE0D93E5DF96039D76BB1C277',	'E90165946E0E1CB5B999BDC2899ECFA7',	2,	'E90165946E0E1CB5B999BDC2899ECFA7',	4,	'黑暗森林法则',	'<p></p><p>黑暗森林法则，科幻小说作家刘慈欣在《三体II 黑暗森林》引入的法则。罗辑（三体2中的主人公）在人类当前的科技水平和社会状况下，受到叶文洁启发，从两条不证自明的基本公理出发，借由引入两个重要概念——猜疑链和技术爆炸，从理论上建立起的一套关于描述当前宇宙社会大图景的大体系的一门学科——宇宙社会学。黑暗森林法则为其结论。<br /></p><p>宇宙就是一座黑暗森林，每个文明都是带枪的猎人，像幽灵般潜行于林间，轻轻拨开挡路的树枝，竭力不让脚步发出一点儿声音，连呼吸都必须小心翼翼：他必须小心，因为林中到处都有与他一样潜行的猎人，如果他发现了别的生命，能做的只有一件事：开枪消灭之。在这片森林中，他人就是地狱，就是永恒的威胁，任何暴露自己存在的生命都将很快被消灭，这就是宇宙文明的图景，这就是对费米悖论的解释。</p><p><br /></p><p><strong>一旦被发现，能生存下来的是只有一方，或者都不能生存</strong><br /></p>',	'{\"tpl\":\"tpl-page-default\"}'),
(12,	'22934871BE3D3C54D717528FC93B4A4C',	'E90165946E0E1CB5B999BDC2899ECFA7',	1,	'7DFCC88651DD776DAF301BFA5FE6FF13',	2,	'',	'<p>2014年3月，《三体1》英文版开始在亚马逊预售，由美国专业的科幻出版社的Tor Books推出，将于10月正式上架。《三体2》与《三体3》亦在翻译制作中。</p><p><br /></p><p>《三体》讲述了地球文明与“三体文明”之间互相依存又对立的关系，其中最为人津津乐道的是提出 “黑暗森林法则”：宇宙如同一片黑暗的森林，每一个文明都是带枪的猎手，谁先暴露谁就先被灭掉。然而人类在暗黑宇宙中还是暴露了，作为一个整体，拯救世界的重任落在中国人身上。《三体》三部曲具有宇宙观，兼具历史反思与道德探索，同时又创造了一个新式的中国神话。</p><p><br /></p><p>刘慈欣《三体》的意义不仅在于文学创造力，更广泛的，它一定程度上改变了中国科幻文学的属性。从1900年代科幻文学进入中国那天起，就背负了“文以载道”的重责，负担了过多的社会功能，梁启超将“科学小说”视为新小说的一部分，以开民智；鲁迅则更看中科幻文学的科学教育功能。之后随中国政权更迭与社会流变，科幻文学开始在意识形态工具和科普工具之间摇摆。</p>',	'{\"tpl\":\"tpl-page-default\"}');

-- 2017-06-20 04:13:52
