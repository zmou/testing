<?php

$install['sql'][] = "CREATE TABLE `app_feed` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `feed` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `img` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$install['sql'][] = "CREATE TABLE `app_feed_dig` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$install['sql'][] = "CREATE TABLE `app_feed_item` (
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
) ENGINE=MyISAM";

$install['sql'][] = "CREATE TABLE `app_feed_recommend` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `feed` varchar(255) NOT NULL,
  `timeline` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `feed` (`feed`)
) ENGINE=MyISAM";


$uninstall['table'][] = 'app_feed';
$uninstall['table'][] = 'app_feed_dig';
$uninstall['table'][] = 'app_feed_item';
$uninstall['table'][] = 'app_feed_recommend';
?>