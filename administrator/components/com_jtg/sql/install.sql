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
	`map_height` varchar(10) NOT NULL,
	`map_width` varchar(10) NOT NULL,
	`map_type` int(5) NOT NULL,
	`charts_width` varchar(10) NOT NULL,
	`charts_height` varchar(10) NOT NULL,
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
	`gpsstore` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . "uploads".DS',
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
	'500px',
	'100%',
	0,
	'100%',
	'180px',
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
	'JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . "uploads".DS',
	'straight',
	'2',
	'COM_JTG_LEVEL_1
COM_JTG_LEVEL_2
COM_JTG_LEVEL_3
COM_JTG_LEVEL_4
COM_JTG_LEVEL_5'
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


INSERT IGNORE INTO `#__jtg_maps` (`name`, `ordering`, `published`, `param`, `script`, `code`) VALUES
('COM_JTG_MAP_MAPNIK', 1, 1, 'OpenLayers.Layer.OSM.Mapnik("{name}")', '', ''),
('COM_JTG_MAP_OSM_HIKE_AND_BIKE', 2, 1, 'OpenLayers.Layer.TMS("{name}", "http://toolserver.org/tiles/hikebike/", { type: "png", getURL: osm_getTileURL, displayOutsideMaxExtent: true, isBaseLayer: true, attribution: "Map Data © OpenStreetMap contributors", transitionEffect: "resize" })', '', ''),
('COM_JTG_MAP_CYCLEMAP', 3, 1, 'OpenLayers.Layer.OSM.CycleMap("{name}")', '', ''),
('COM_JTG_MAP_NONAME', 4, 0, 'OpenLayers.Layer.OSM("{name}", [ "http://a.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png", "http://b.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png", "http://c.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png"], {displayOutsideMaxExtent: true, wrapDateLine: true, numZoomLevels: 19, layerCode: "N"})', '', 'var nonamekey = &quot;PASTE_YOUR_KEY_HERE&quot;;'),
('COM_JTG_MAP_GOOGLESAT', 5, 1, 'OpenLayers.Layer.Google( "{name}", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLESTREET', 6, 1, 'OpenLayers.Layer.Google( "{name}",  {numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLEHYBRID', 7, 1, 'OpenLayers.Layer.Google( "{name}",  {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLEPHYSICAL', 8, 1, 'OpenLayers.Layer.Google("{name}", {type: google.maps.MapTypeId.TERRAIN} )', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_VE_AERIAL', 9, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Aerial", wrapDateLine: true })', '', 'var BingApiKey =  &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_VE_ROAD', 10, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Road", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_VE_HYBRID', 11, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "AerialWithLabels", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('Own Server', 99, 0, 'OpenLayers.Layer.OSM(&quot;{name}&quot;, &quot;http://server/${z}/${x}/${y}.png&quot; ,{ &quot;type&quot;:&quot;png&quot;})', '', '');

<!-- Dummy record where used in injooosm have been maintained for compatibility in #__jtg_cats -->  
<!-- however the usefulness of these dummy records is not clear --> 

INSERT IGNORE INTO `#__jtg_cats` 
    (`id`,`parent_id`,`title`,`description`,`image`,`ordering`,`published`,`checked_out`) 
VALUES 
    ('1','0','dummy','','','0','1','0'),
    ('2','0','dummy','','','0','1','0'),
    ('3','0','dummy','','','0','1','0'),
    ('4','0','dummy','','','0','1','0'),
    ('5','0','dummy','','','0','1','0'),
    ('6','0','dummy','','','0','1','0'),
    ('7','0','dummy','','','0','1','0'),
    ('8','0','dummy','','','0','1','0'),
    ('9','0','dummy','','','0','1','0'),
    ('10','0','COM_JTG_CAT_CAR','','car.jpg','7','1','0'),
    ('11','10','COM_JTG_CAT_CAR_FOCUS','','focus.jpg','1','1','0'),
    ('12','10','COM_JTG_CAT_CAR_VECTRA','','vectra.jpg','2','1','0'),
    ('13','0','COM_JTG_CAT_BIKE','','vectra.jpg','5','1','0'),
    ('14','0','COM_JTG_CAT_MOTORBIKE','','bike.jpg','6','1','0'),
    ('15','0','COM_JTG_CAT_PEDESTRIAN','','pedestrian.jpg','3','1','0'),
    ('16','0','COM_JTG_CAT_GEOCACHE','','geocaching.jpg','4','1','0')
;

DELETE FROM `#__jtg_cats` WHERE title = 'dummy';

INSERT IGNORE INTO `#__jtg_terrains` 
    (`id`,`title`,`published`,`checked_out`,`ordering`) 
VALUES 
    ('1','COM_JTG_TERRAIN_STREET','1','0','0'),
    ('2','COM_JTG_PUBLIC','1','0','0'),
    ('3','COM_JTG_TERRAIN_FARM_TRACK','1','0','0'),
    ('4','COM_JTG_PRIVATE','1','0','0'),
    ('5','COM_JTG_ADMINISTRATORS','1','0','0');


