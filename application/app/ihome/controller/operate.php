<?php

$install['sql'][] = "CREATE TABLE IF NOT EXISTS `app_ihome_user` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `g` int(11) NOT NULL,
  `gold` int(11) NOT NULL,
  `hp` tinyint(3) NOT NULL default '100',
  `hp_max` tinyint(3) NOT NULL default '100',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM ;";

$uninstall['table'][] = 'app_ihome_user';

?>