<?php


$install['sql'][] = "CREATE TABLE IF NOT EXISTS `app_iinvite_emails` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `no_in_site` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM ;";

$uninstall['table'][] = 'app_iinvite_emails';


?>