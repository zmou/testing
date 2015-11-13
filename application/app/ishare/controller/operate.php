<?php

$install['sql'][] = 'CREATE TABLE IF NOT EXISTS `app_fav` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `desp` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `type` tinyint(1) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `music_url` varchar(255) NOT NULL,
  `video_domain` varchar(255) NOT NULL,
  `video_player` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;';

$uninstall['table'][] = 'app_fav';


?>