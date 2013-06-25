CREATE TABLE IF NOT EXISTS `#__jtg_maps` (
	`id` int(2) NOT NULL AUTO_INCREMENT,
	`name` varchar(30) NOT NULL,
	`ordering` int(2) NOT NULL,
	`published` int(1) NOT NULL,
	`param` varchar(500),
	`script` varchar(300),
	`code` varchar(300),
	`checked_out` int(10) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jtg_cats` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`parent_id` INT( 10 ) NOT NULL DEFAULT '0',
	`title` varchar(30) NOT NULL,
	`description` varchar(255),
	`image` varchar(60),
	`ordering` int(10) NOT NULL,
	`published` int(10) NOT NULL,
	`checked_out` int(10) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM	 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jtg_config` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`gid` VARCHAR(150) NULL DEFAULT NULL,
	`apikey` varchar(100),
	`unit` varchar(20) NOT NULL,
	`type` varchar(30) NOT NULL,
	`max_size` int(10) NOT NULL,
	`terms` int(10) NOT NULL,
	`terms_id` int(10) NOT NULL,
	`sort` int(10) NOT NULL,
	`map_height` int(10) NOT NULL,
	`map_width` int(10) NOT NULL,
	`map_type` int(5) NOT NULL,
	`charts_width` int(10) NOT NULL,
	`charts_height` int(10) NOT NULL,
	`charts_linec` varchar(6) NOT NULL,
	`charts_bg` varchar(6) NOT NULL,
	`profile` varchar(5) NOT NULL DEFAULT '0',
	`template` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`comment_who` INT( 5 ) NOT NULL DEFAULT '1',
	`inform_autor` INT( 5 ) NOT NULL DEFAULT '0',
	`captcha` INT( 5 ) NOT NULL DEFAULT '0',
	`ordering` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`comments` INT( 1 ) NOT NULL,
	`access` INT( 2 ) NOT NULL DEFAULT '0',
	`map` varchar(10) NOT NULL DEFAULT 'osm',
	`approach` varchar(5) DEFAULT '0',
	`routingiconset` varchar(10) DEFAULT 'real',
	`usevote` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '1',
	`download` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '1',
	`gpsstore` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "uploads".DS',
	`gallery` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'straight',
	`showcharts` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '2',
	`level` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__jtg_config` (
	`id`,
	`gid`,
	`unit`,
	`type`,
	`max_size`,
	`terms`,
	`terms_id`,
	`sort`,
	`map_height`,
	`map_width`,
	`map_type`,
	`charts_width`,
	`charts_height`,
	`charts_linec`,
	`charts_bg`,
	`profile`,
	`template`,
	`comment_who`,
	`inform_autor`,
	`captcha`,
	`ordering`,
	`comments`,
	`access`,
	`map`,
	`approach`,
	`routingiconset`,
	`usevote`,
	`download`,
	`gpsstore`,
	`gallery`,
	`showcharts`,
	`level`
) VALUES (
	'1',
	NULL,
	'Kilometer',
	'jpg,jpeg,png,gif',
	200,
	0,
	0,
	5,
	500,
	800,
	0,
	750,
	180,
	'FF0000',
	'0000CC',
	0,
	'default',
	'default',
	1,
	0,
	'DESC',
	0,
	0,
	'osm',
	'no',
	'real',
	0,
	2,
	'JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "uploads".DS',
	'straight',
	'2',
	'OSM_LEVEL_1
OSM_LEVEL_2
OSM_LEVEL_3
OSM_LEVEL_4
OSM_LEVEL_5'
);

CREATE TABLE IF NOT EXISTS `#__jtg_files` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`uid` int(10) NOT NULL,
	`catid` varchar(255),
	`title` varchar(60) NOT NULL,
	`file` varchar(50),
	`terrain` varchar(255),
	`description` text,
	`published` int(10) NOT NULL DEFAULT '1',
	`date` date NOT NULL DEFAULT '0000-00-00',
	`hits` int(10) NOT NULL,
	`checked_out` int(10) NOT NULL,
	`start_n` varchar(20) NOT NULL,
	`start_e` varchar(20) NOT NULL,
	`distance` float(10,2),
	`ele_asc` int(10) NOT NULL,
	`ele_desc` int(10) NOT NULL,
	`level` int(5) NOT NULL,
	`access` INT(2) NOT NULL,
	`istrack` TINYINT(1) DEFAULT NULL,
	`iswp` TINYINT(1) DEFAULT NULL,
	`isroute` TINYINT(1) DEFAULT NULL,
	`iscache` TINYINT(1) DEFAULT NULL,
	`vote` FLOAT(5,3) UNSIGNED ZEROFILL DEFAULT '0.000',
	`hidden` int(1) UNSIGNED DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jtg_votes` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`trackid` int(10) NOT NULL,
	`rating` int(3) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jtg_terrains` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`title` varchar(30) NOT NULL,
	`published` int(5) NOT NULL,
	`checked_out` int(5) NOT NULL,
	`ordering` int(5) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__jtg_comments` (
	`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
	`tid` INT( 10 ) NOT NULL ,
	`user` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`email` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`homepage` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`published` INT( 5 ) NOT NULL ,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM;

CREATE TABLE IF NOT EXISTS `#__jtg_users` (
	`user_id` int(10) unsigned,
	`jtglat` FLOAT(20,15),
	`jtglon` FLOAT(20,15),
	`jtgvisible` VARCHAR(3),
PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


