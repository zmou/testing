CREATE TABLE IF NOT EXISTS `u2_app` (
  `id` int(11) NOT NULL auto_increment,
  `aid` varchar(64) NOT NULL,
  `u2_title` varchar(255) NOT NULL,
  `u2_desc` varchar(255) NOT NULL,
  `u2_folder` varchar(255) NOT NULL,
  `u2_url` varchar(255) NOT NULL,
  `u2_icon` varchar(32) NOT NULL,
  `u2_left_nav` tinyint(3) NOT NULL,
  `u2_is_system` tinyint(1) NOT NULL,
  `u2_has_widgets` tinyint(1) NOT NULL,
  `u2_time` datetime NOT NULL,
  `u2_order` int(11) default '0',
  `u2_is_active` tinyint(1) NOT NULL,
  UNIQUE KEY `aid` (`aid`),
  KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_cate` (
  `id` int(11) NOT NULL auto_increment,
  `u2_cate_desc` varchar(255) NOT NULL,
  `u2_cate_num` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `u2_cate_num` (`u2_cate_num`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_comment` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `time` datetime NOT NULL,
  `step` int(11) NOT NULL,
  `dig` int(11) NOT NULL default '0',
  `rcount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_comment_reply` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_comment_vote` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_content` (
  `id` int(11) NOT NULL auto_increment,
  `u2_title` varchar(255) NOT NULL,
  `u2_pic` varchar(255) default NULL,
  `u2_desp` mediumtext NOT NULL,
  `u2_uid` int(11) NOT NULL,
  `u2_nickname` varchar(255) NOT NULL,
  `u2_is_active` tinyint(1) NOT NULL default '0',
  `u2_addtime` datetime NOT NULL,
  `u2_hit` int(11) NOT NULL,
  `u2_cate` bigint(20) NOT NULL,
  `u2_annex_1` tinytext collate utf8_unicode_ci,
  `u2_annex_2` tinytext collate utf8_unicode_ci,
  `u2_annex_3` tinytext collate utf8_unicode_ci,
  `u2_annex_4` tinytext collate utf8_unicode_ci,
  `u2_annex_5` tinytext collate utf8_unicode_ci,
  KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_crontab` (
  `id` int(11) NOT NULL auto_increment,
  `app_name` varchar(255) NOT NULL COMMENT '0=core,1=app',
  `page_name` varchar(255) NOT NULL COMMENT '动作触发页面名称',
  `function_name` varchar(255) NOT NULL,
  `crontime` datetime NOT NULL,
  `type` tinyint(1) NOT NULL default '0' COMMENT '0=minute,1=hour,2=day,3=week,4=month,5=always',
  `last_exetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_fans` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid1` int(11) NOT NULL default '0',
  `u2_uid2` int(11) NOT NULL default '0',
  `is_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `u2_uid1` (`u2_uid1`,`u2_uid2`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_invite` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_invite_code` varchar(255) NOT NULL,
  `u2_is_use` tinyint(1) NOT NULL,
  `u2_is_copied` tinyint(1) NOT NULL,
  `u2_date` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_manager` (
  `id` int(11) NOT NULL auto_increment,
  `u2_img` varchar(255) NOT NULL,
  `u2_type` varchar(64) NOT NULL,
  `u2_desc` varchar(255) NOT NULL,
  `u2_uid` int(11) NOT NULL,
  `u2_table` varchar(255) NOT NULL,
  `u2_tid` int(11) NOT NULL,
  `u2_state` varchar(64) NOT NULL,
  `u2_doid` int(11) NOT NULL default '0',
  `u2_key` varchar(255) NOT NULL default 'is_active',
  `u2_done` text,
  KEY `id` (`id`)
) ENGINE=MyISAM ;


CREATE TABLE IF NOT EXISTS `u2_meta_field` (
  `id` int(11) NOT NULL auto_increment,
  `u2_en_name` varchar(32) NOT NULL default '',
  `u2_cn_name` varchar(64) NOT NULL default '',
  `u2_type` varchar(32) NOT NULL default '',
  `u2_is_active` tinyint(1) NOT NULL default '0',
  `u2_is_unique` tinyint(1) NOT NULL default '0',
  `u2_not_null` tinyint(1) NOT NULL default '0',
  `u2_in_search` tinyint(1) NOT NULL default '0',
  `u2_cannot_modify` tinyint(1) NOT NULL default '0',
  `u2_can_share` tinyint(1) NOT NULL default '0',
  `u2_cate_id` int(11) NOT NULL default '0',
  `u2_desp` tinytext collate utf8_unicode_ci,
  `u2_select` text NOT NULL,
  `u2_in_tag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `u2_en_name` (`u2_en_name`,`u2_cate_id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_mini_feed` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_action` varchar(255) NOT NULL,
  `u2_desp` text character set utf8 collate utf8_unicode_ci,
  `u2_type` tinyint(1) default '0',
  `u2_img` varchar(255) default NULL,
  `u2_time` datetime NOT NULL,
  `u2_app_aid` varchar(32) NOT NULL,
  `u2_reskey` varchar(255) default NULL,
  KEY `id` (`id`),
  KEY `u2_reskey` (`u2_reskey`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_notice` (
  `id` int(11) NOT NULL auto_increment,
  `u2_notice_title` varchar(255) NOT NULL,
  `u2_notice_content` varchar(255) default NULL,
  `u2_app_aid` varchar(255) NOT NULL,
  `u2_time` datetime NOT NULL,
  `u2_is_read` tinyint(1) NOT NULL default '0',
  `u2_uid` int(11) NOT NULL,
  `u2_reskey` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `u2_reskey` (`u2_reskey`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_online` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_sid` varchar(255) NOT NULL,
  `u2_stay_time` datetime NOT NULL,
  `u2_stay_location` varchar(255) NOT NULL,
  UNIQUE KEY `u2_uid` (`u2_uid`),
  KEY `id` (`id`,`u2_stay_time`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_page` (
  `id` int(11) NOT NULL auto_increment,
  `u2_is_system` tinyint(1) NOT NULL default '0',
  `u2_in_tab` tinyint(1) NOT NULL default '1',
  `u2_tag` varchar(255) NOT NULL,
  `u2_data` text NOT NULL,
  `u2_link` varchar(255) default NULL,
  `u2_order` tinyint(3) NOT NULL default '0',
  UNIQUE KEY `u2_tag` (`u2_tag`),
  KEY `id` (`id`),
  KEY `u2_order` (`u2_order`)
) ENGINE=MyISAM ;

INSERT INTO `u2_page` (`id`, `u2_is_system`, `u2_in_tab`, `u2_tag`, `u2_data`, `u2_link`, `u2_order`) VALUES
(1, 0, 1, '首页', 'a:4:{s:4:"type";s:1:"1";s:6:"layout";s:3:"2-1";s:7:"widgets";a:2:{i:1;a:0:{}i:0;a:0:{}}s:3:"old";a:1:{i:0;i:10;}}', NULL, 0),
(2, 1, 1, '文章内容页', 'a:3:{s:4:"type";i:3;s:7:"widgets";a:1:{i:1;a:1:{i:0;i:22;}}s:6:"layout";s:1:"2";}', NULL, 2),
(3, 1, 1, '文章列表页', 'a:3:{s:4:"type";i:2;s:7:"widgets";a:1:{i:1;a:1:{i:0;i:23;}}s:6:"layout";s:1:"2";}', NULL, 3);

CREATE TABLE IF NOT EXISTS `u2_pm` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_sid` int(11) NOT NULL,
  `sname` varchar(32) NOT NULL,
  `uname` varchar(32) NOT NULL,
  `u2_pm_title` varchar(64) NOT NULL,
  `u2_pm_info` text NOT NULL,
  `u2_is_read` tinyint(4) NOT NULL default '0',
  `u2_u_del` tinyint(4) NOT NULL default '0',
  `u2_s_del` tinyint(4) NOT NULL default '0',
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `u2_post` (
  `id` int(11) NOT NULL auto_increment,
  `u2_keyword` varchar(255) NOT NULL default '',
  `u2_parent_id` int(11) NOT NULL default '0',
  `u2_title` varchar(255) NOT NULL default '',
  `u2_desp` mediumtext NOT NULL,
  `u2_uid` int(11) NOT NULL default '0',
  `u2_nickname` varchar(32) NOT NULL default '',
  `u2_city` varchar(32) NOT NULL default '',
  `u2_date` date NOT NULL default '0000-00-00',
  `u2_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `u2_last_post_uid` int(11) NOT NULL default '0',
  `u2_last_post_nickname` varchar(32) NOT NULL default '',
  `u2_last_post_city` varchar(32) NOT NULL default '',
  `u2_last_post_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `u2_last_post_date` date NOT NULL default '0000-00-00',
  `u2_hit` int(11) NOT NULL default '0',
  `u2_reply` int(11) NOT NULL default '0',
  `u2_is_active` tinyint(1) NOT NULL default '1',
  `u2_top_level` tinyint(1) NOT NULL default '0',
  `u2_is_selected` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_recharge_card` (
  `id` int(11) NOT NULL auto_increment,
  `u2_card_no` varchar(255) NOT NULL,
  `u2_is_use` tinyint(1) NOT NULL,
  `u2_is_copied` tinyint(1) NOT NULL,
  `u2_date` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_restore` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_app_name` varchar(255) NOT NULL,
  `u2_app_id` int(11) NOT NULL,
  `u2_title` varchar(255) NOT NULL,
  `u2_desp` text NOT NULL,
  `u2_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_snotice` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `desp` tinytext NOT NULL,
  `start_time` date NOT NULL,
  `end_time` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_space_visit` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_vid` int(11) NOT NULL,
  `u2_stay_time` datetime NOT NULL,
  UNIQUE KEY `u2_uid` (`u2_uid`,`u2_vid`),
  KEY `id` (`id`),
  KEY `u2_stay_time` (`u2_stay_time`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_upgrade` (
  `id` int(11) NOT NULL auto_increment,
  `sid` int(11) NOT NULL,
  `is_max` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_user` (
  `id` int(11) NOT NULL auto_increment,
  `u2_email` varchar(128) NOT NULL default '',
  `u2_password` varchar(32) NOT NULL default '',
  `u2_joindate` date NOT NULL default '0000-00-00',
  `u2_nickname` varchar(32) NOT NULL default '',
  `u2_true_name` varchar(32) NOT NULL,
  `u2_sex` tinyint(1) NOT NULL default '0',
  `u2_msn` varchar(64) NOT NULL,
  `u2_qq` varchar(32) NOT NULL,
  `u2_mobile` varchar(64) NOT NULL,
  `u2_city` varchar(64) NOT NULL,
  `u2_address` tinytext NOT NULL,
  `u2_zipcode` varchar(32) NOT NULL,
  `u2_isactive` tinyint(1) NOT NULL default '0',
  `u2_pincode` varchar(32) NOT NULL default '0',
  `u2_level` tinyint(2) NOT NULL default '1',
  `u2_desp` varchar(255) NOT NULL default '',
  `u2_miniblog` varchar(255) default NULL,
  `u2_username` varchar(36) NOT NULL default '',
  `u2_ismailok` tinyint(1) NOT NULL default '1',
  `u2_searchtext` text NOT NULL,
  `u2_karma` int(11) NOT NULL default '0',
  `u2_snotice` int(11) NOT NULL default '0',
  `u2_blog_rss` varchar(255) NOT NULL default '',
  `u2_blog_rss_validated` tinyint(1) NOT NULL default '0',
  `u2_blog_rss_added` tinyint(1) NOT NULL default '0',
  `u2_space_hit` int(11) NOT NULL,
  `online_date` date default NULL,
  `online_today` int(11) NOT NULL default '0',
  `onlinetime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `u2_email` (`u2_email`),
  UNIQUE KEY `u2_nickname` (`u2_nickname`),
  KEY `u2_joindate` (`u2_joindate`),
  FULLTEXT KEY `u2_searchtext` (`u2_searchtext`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_user_pic` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_pid` int(11) NOT NULL default '0',
  `u2_pic_name` varchar(64) character set utf8 NOT NULL,
  `u2_time` datetime NOT NULL,
  `u2_is_active` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_vote` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_vote_count` (
  `id` int(11) NOT NULL auto_increment,
  `wid` int(11) NOT NULL,
  `key` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `wid` (`wid`,`key`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_vote_value` (
  `id` int(11) NOT NULL auto_increment,
  `wid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` text NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `wid` (`wid`,`uid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_wall` (
  `id` int(11) NOT NULL auto_increment,
  `u2_uid` int(11) NOT NULL,
  `u2_guest_uid` int(11) NOT NULL,
  `u2_content` tinytext NOT NULL,
  `u2_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_widget` (
  `id` int(11) NOT NULL auto_increment,
  `u2_aid` varchar(255) NOT NULL,
  `u2_folder` varchar(255) NOT NULL,
  `u2_name` varchar(255) NOT NULL,
  `u2_desc` varchar(255) NOT NULL,
  `u2_stats` varchar(255) NOT NULL,
  `u2_is_active` tinyint(1) NOT NULL default '1',
  KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_widget_instance` (
  `id` int(11) NOT NULL auto_increment,
  `u2_wid` int(11) NOT NULL,
  `u2_aid` varchar(255) NOT NULL,
  `u2_folder` varchar(255) NOT NULL,
  `u2_data` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `w2_form` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `state` varchar(255) default NULL,
  `item_name` varchar(255) NOT NULL,
  `is_main_app` tinyint(1) NOT NULL,
  `has_comment` tinyint(1) NOT NULL,
  `has_digg` tinyint(1) NOT NULL,
  `has_fav` tinyint(1) NOT NULL,
  `has_pro_con` tinyint(1) NOT NULL,
  `has_star` tinyint(1) NOT NULL,
  `uid` int(11) NOT NULL,
  `timeline` datetime NOT NULL,
  `order` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `w2_item` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `type` char(10) NOT NULL default 'line',
  `label` varchar(255) default NULL,
  `size` varchar(10) NOT NULL default '1',
  `type_values` text,
  `is_core` tinyint(1) NOT NULL default '0',
  `is_required` tinyint(1) NOT NULL default '0',
  `is_unique` tinyint(1) NOT NULL default '0',
  `is_searchable` tinyint(1) NOT NULL default '0',
  `view_level` int(11) NOT NULL default '0',
  `default_value` varchar(255) default NULL,
  `instruction` varchar(255) default NULL,
  `custom_css` varchar(255) default NULL,
  `display_order` tinyint(2) NOT NULL default '0',
  `timeline` datetime NOT NULL,
  `key` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_statistics` (
`id` INT NOT NULL AUTO_INCREMENT ,
`uid` INT NOT NULL ,
`ip` VARCHAR( 255 ) NOT NULL ,
`ref` VARCHAR( 255 ) NOT NULL ,
`uri` VARCHAR( 255 ) NOT NULL ,
`agent` VARCHAR( 255 ) NOT NULL ,
`date` DATE NOT NULL ,
PRIMARY KEY ( `id` ) 
) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `u2_statistics_res` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_app_view` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mid` (`mid`,`cid`,`uid`),
  KEY `mid_2` (`mid`,`cid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_rate` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `rate` tinyint(1) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`,`mid`,`cid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_karma_record` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `reason` tinytext NOT NULL,
  `admin_uid` int(11) NOT NULL,
  `app` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_brands` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `subname` varchar(255) default NULL,
  `url` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `orders` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_cate` (
  `id` int(11) NOT NULL auto_increment,
  `cate_desc` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `cate_type` int(11) NOT NULL default '0',
  `orders` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cate_num` (`pid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_items` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `hit` int(11) NOT NULL default '0',
  `cate` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `brands` int(11) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `price` float NOT NULL,
  `market_price` float NOT NULL,
  `pro_price` float NOT NULL,
  `is_pro` tinyint(1) NOT NULL default '0',
  `leave` int(11) NOT NULL,
  `alarm` int(11) NOT NULL,
  `carriage` tinyint(4) NOT NULL default '0',
  `new` tinyint(4) NOT NULL default '0',
  `good` tinyint(4) NOT NULL default '0',
  `hot` tinyint(4) NOT NULL default '0',
  `pic` varchar(255) NOT NULL,
  `snap` varchar(255) NOT NULL,
  `muti-pic` text NOT NULL,
  `desp` text NOT NULL,
  `is_active` tinyint(1) NOT NULL default '0',
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_relate_tags` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cid` (`cid`,`tid`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_replys` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `info` text NOT NULL,
  `time` datetime NOT NULL,
  `ruid` int(11) NOT NULL default '0',
  `rinfo` text,
  `rtime` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `extra` text,
  `orders` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `u2_shop_wishlist` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1=keep;2=wishlist',
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`,`cid`,`type`)
) ENGINE=MyISAM ;




INSERT INTO `u2_app` (`id`, `aid`, `u2_title`, `u2_desc`, `u2_folder`, `u2_url`, `u2_icon`, `u2_left_nav`, `u2_is_system`, `u2_has_widgets`, `u2_time`, `u2_order`, `u2_is_active`) VALUES
(1, 'system', '系统', '系统插件', 'System', '', '', 0, 0, 17, '2009-03-25 16:33:04', 0, 1),
(2, 'ibank', '社区银行', '可以在这里存取款,还可以通过网上银行兑换金币', 'ibank', '', '/static/icon/coins.png', 1, 0, 0, '2009-03-25 16:33:21', 0, 1),
(3, 'ifeedig', '悦读', '社会化阅读工具', 'ifeedig', '', '/static/icon/feedig_icon.gif', 1, 0, 0, '2009-03-25 16:33:23', 0, 1),
(4, 'iforum', '讨论区', '关于社区的一切都可以在这里讨论哦', 'iforum', '', '/static/icon/iforum.gif', 1, 0, 6, '2009-03-25 16:33:25', 0, 1),
(5, 'iinvite', '邀请好友', '邀请好友', 'iinvite', '', '/static/icon/user.png', 0, 0, 0, '2009-03-25 16:33:29', 0, 1),
(6, 'ishopcart', '购物车', '购物车', 'ishopcart', '', '/static/icon/cart.gif', 1, 0, 0, '2009-03-25 16:33:34', 0, 1);



CREATE TABLE IF NOT EXISTS `app_ibank_account` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `g_count` int(11) NOT NULL,
  `gold_count` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `app_ihome_user` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `g` int(11) NOT NULL,
  `gold` int(11) NOT NULL,
  `hp` tinyint(3) NOT NULL default '100',
  `hp_max` tinyint(3) NOT NULL default '100',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM ;

CREATE TABLE `app_feed` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `feed` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `img` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `app_feed_dig` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `app_feed_item` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `fid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desp` text NOT NULL,
  `time` datetime NOT NULL,
  `author` varchar(255) default NULL,
  `link` varchar(255) NOT NULL,
  `state` varchar(255) default NULL,
  `unistring` varchar(255) NOT NULL,
  `dig` int(11) NOT NULL default '0',
  `hit` int(11) NOT NULL default '0',
  `admin_uid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `app_feed_recommend` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `feed` varchar(255) NOT NULL,
  `timeline` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `feed` (`feed`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `app_iforum_cate` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL,
  `key` tinyint(1) NOT NULL,
  `desp` varchar(255) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `fid` (`fid`,`key`),
  KEY `id` (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `app_iforum_posts` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL,
  `floor` int(11) NOT NULL default '0',
  `type` tinyint(1) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desp` text  NOT NULL,
  `uid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `last_uid` int(11) NOT NULL,
  `last_post_time` datetime NOT NULL,
  `top_level` tinyint(1) NOT NULL default '0',
  `is_selected` tinyint(1) NOT NULL default '0',
  `hit` int(11) NOT NULL,
  `reply` int(11) NOT NULL,
  `del_uid` int(11) NOT NULL default '0',
  `is_active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `app_iforum_status` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `desp` tinytext collate utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `admin_uids` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM;

INSERT INTO `app_iforum_status` (`id` ,`name` ,`desp` ,`is_active` ,`admin_uids` )VALUES (NULL , '讨论区', '讨论区', '1', '');


CREATE TABLE `app_shopcart` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desp` varchar(255) NOT NULL,
  `num` int(11) NOT NULL,
  `money` float NOT NULL,
  `date` datetime NOT NULL,
  `folder` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `app_shoporder` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `toname` varchar(255) NOT NULL,
  `totell` varchar(255) NOT NULL,
  `tocode` varchar(255) NOT NULL,
  `tohome` varchar(255) NOT NULL,
  `need` varchar(255) NULL,
  `carry_type` int(11) NOT NULL,
  `pack_type` int(11) NOT NULL,
  `ware` text NOT NULL,
  `money` float NOT NULL,
  `stint` float NOT NULL,
  `time` int(11) NOT NULL,
  `enter` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10000 ;

CREATE TABLE `app_shopuser` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tell` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `home` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

