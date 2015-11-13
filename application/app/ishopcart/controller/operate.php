<?php
$install['sql'][] = "CREATE TABLE `app_shopcart` (
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
) ENGINE=MyISAM;";

$install['sql'][] = "CREATE TABLE `app_shoporder` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000 ;";

$install['sql'][] = "CREATE TABLE `app_shopuser` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tell` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `home` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";


$uninstall['table'][] = 'app_shopcart';
$uninstall['table'][] = 'app_shoporder';
$uninstall['table'][] = 'app_shopuser';

?>