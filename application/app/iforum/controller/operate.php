<?php
$install['sql'][] = "CREATE TABLE IF NOT EXISTS `app_iforum_cate` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL,
  `key` tinyint(1) NOT NULL,
  `desp` varchar(255) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `fid` (`fid`,`key`),
  KEY `id` (`id`)
) ENGINE=MyISAM ;";

$install['sql'][] = "CREATE TABLE IF NOT EXISTS `app_iforum_posts` (
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
) ENGINE=MyISAM ;";

$install['sql'][] = "CREATE TABLE IF NOT EXISTS `app_iforum_status` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `desp` tinytext collate utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `admin_uids` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM;";


$install['sql'][] = "INSERT INTO `app_iforum_status` (`id` ,`name` ,`desp` ,`is_active` ,`admin_uids` )VALUES (NULL , '讨论区', '讨论区', '1', '');";
$uninstall['table'][] = 'app_iforum_cate';
$uninstall['table'][] = 'app_iforum_posts';
$uninstall['table'][] = 'app_iforum_status';

$widgets['Hit'] = '最多点击';
$widgets['Newsetup'] = '最新创建';
$widgets['Originate'] = '我发起的';
$widgets['Restore'] = '我回复的';
$widgets['NewRestore'] = '最新回复的';
$widgets['Search'] = '搜索文章';
?>