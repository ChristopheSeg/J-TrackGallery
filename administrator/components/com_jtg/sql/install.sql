
CREATE TABLE IF NOT EXISTS `#__jtg_maps` (
	`id` int(2) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
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
	`max_geoim_height` int(10) NOT NULL,
	`max_thumb_height` int(10) NOT NULL, 
	`terms` int(10) NOT NULL,
	`terms_id` int(10) NOT NULL,
	`sort` int(10) NOT NULL,
	`map_height` varchar(10) NOT NULL,
	`map_width` varchar(10) NOT NULL,
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
	`approach` varchar(5) DEFAULT '0',
	`routingiconset` varchar(10) DEFAULT 'real',
	`usevote` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '1',
	`download` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '1',
	`gpsstore` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "uploads". DIRECTORY_SEPARATOR',
	`gallery` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'straight',
	`showcharts` TINYINT(1) UNSIGNED ZEROFILL DEFAULT '2',
	`level` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

TRUNCATE `#__jtg_config`;

INSERT  IGNORE INTO `#__jtg_config` (`id`, `gid`, `apikey`, `unit`, `type`, `max_size`, `max_thumb_height`,
`max_geoim_height`, `terms`, `terms_id`, `sort`, `map_height`, `map_width`, `charts_width`, 
`charts_height`, `charts_linec`, `charts_bg`, `profile`, `template`, `comment_who`, 
`inform_autor`, `captcha`, `ordering`, `comments`, `access`, `approach`, 
`routingiconset`, `usevote`, `download`, `gpsstore`, `gallery`, `showcharts`, 
`level`) VALUES
(1, NULL, NULL, 'Kilometer', 'jpg,jpeg,png,gif', 200, 220, 
400, 0, 0, 5, '500px', '100%', '100%', 
'180px', 'FF0000', '0000CC', '0', 'default', 0, 
1, 0, 'DESC', 0, 0, 'no', 
'real', 0, 2, 'JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "uploads". DIRECTORY_SEPARATOR', 'straight', 2, 
'COM_JTG_LEVEL_1\nCOM_JTG_LEVEL_2\nCOM_JTG_LEVEL_3\nCOM_JTG_LEVEL_4\nCOM_JTG_LEVEL_5');

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
('COM_JTG_MAP_GOOGLE_SAT', 5, 1, 'OpenLayers.Layer.Google( "{name}", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLE_STREET', 6, 1, 'OpenLayers.Layer.Google( "{name}",  {numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLE_HYBRID', 7, 1, 'OpenLayers.Layer.Google( "{name}",  {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLE_PHYSICAL', 8, 1, 'OpenLayers.Layer.Google("{name}", {type: google.maps.MapTypeId.TERRAIN} )', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_BING_AERIAL', 9, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Aerial", wrapDateLine: true })', '', 'var BingApiKey =  &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_BING_ROAD', 10, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Road", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_BING_HYBRID', 11, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "AerialWithLabels", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_FRENCH_IGN_GEOPORTAL', 12, 1, 'OpenLayers.Layer.WMTS({ 		name: "{name}", 		url: "http://gpp3-wxs.ign.fr/"+ ign_api_key + "/wmts", 		layer: "GEOGRAPHICALGRIDSYSTEMS.MAPS", 		matrixSet: "PM",         projection: new OpenLayers.Projection("EPSG:900913"),         units: "m", 		theme: null, 		style: "normal", 		numZoomLevels: 19, 		attribution: "Map base: ©IGN  Terms of Service" 	})', '', 'var ign_api_key = &quot;10gwaoqj0yqvlfi94vw12rns&quot;;');

INSERT IGNORE INTO `#__jtg_cats` (`id`, `parent_id`, `title`, `description`, `image`, `ordering`, `published`, `checked_out`) VALUES
    (1, 0, 'dummy','','',0,1,0),
    (2, 0, 'dummy','','',0,1,0),
    (3, 0, 'dummy','','',0,1,0),
    (4, 0, 'dummy','','',0,1,0),
    (5, 0, 'dummy','','',0,1,0),
    (6, 0, 'dummy','','',0,1,0),
    (7, 0, 'dummy','','',0,1,0),
    (8, 0, 'dummy','','',0,1,0),
    (9, 0, 'dummy','','',0,1,0),
    (12, 0, 'COM_JTG_CAT_TREKKING', 'COM_JTG_CAT_TREKKING_DESCRIPTION', 'hiking.png', 0, 1, 0),
    (19, 0, 'COM_JTG_CAT_MOUNTAIN_BIKE', 'COM_JTG_CAT_MOUNTAIN_BIKE_DESCRIPTION', 'mountainbiking-3.png', 0, 1, 0),
    (17, 0, 'COM_JTG_CAT_HORSE_RIDING', 'COM_JTG_CAT_HORSE_RIDING_DESCRIPTION', 'horseriding.png', 0, 1, 0),
    (10, 0, 'COM_JTG_CAT_CAR', 'COM_JTG_CAT_CAR_DESCRIPTION', 'sportscar.png', 7, 1, 0),
    (11, 0, 'COM_JTG_CAT_CAR_44', 'COM_JTG_CAT_CAR_44_DESCRIPTION', 'fourbyfour.png', 1, 1, 0),
    (13, 0, 'COM_JTG_CAT_BIKE', 'COM_JTG_CAT_BIKE_DESCRIPTION', 'cycling.png', 5, 1, 0),
    (14, 0, 'COM_JTG_CAT_MOTORBIKE', 'COM_JTG_CAT_MOTORBIKE_DESCRIPTION', 'motorbike.png', 6, 1, 0),
    (15, 0, 'COM_JTG_CAT_PEDESTRIAN', 'COM_JTG_CAT_PEDESTRIAN_DESCRIPTION', 'hiking.png', 3, 1, 0),
    (16, 0, 'COM_JTG_CAT_GEOCACHE', 'COM_JTG_CAT_GEOCACHE_DESCRIPTION', 'geocachinginternational.png', 4, 1, 0),
    (20, 0, 'COM_JTG_CAT_SNOWSHOEING', 'COM_JTG_CAT_SNOWSHOEING_DESCRIPTION', 'snowshoeing.png', 0, 1, 0),
    (21, 0, 'COM_JTG_CAT_TRAIL', 'COM_JTG_CAT_TRAIL_DESCRIPTION', 'hiking.png', 0, 1, 0)
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

INSERT IGNORE INTO `#__jtg_files` (`id`, `uid`, `catid`, `title`, `file`, `terrain`, `description`, `published`, `date`, `hits`, `checked_out`, `start_n`, `start_e`, `distance`, `ele_asc`, `ele_desc`, `level`, `access`, `istrack`, `iswp`, `isroute`, `iscache`, `vote`, `hidden`) VALUES
(1, 430, '19', '-sample- Woodhead Reconnaissance', 'sample_woodhead_reconnaissance.gpx', '2', '<p>This tracks by has been provided by Richard from RSInfotech <a href=&#34;http://www.rsinfotech.co.uk/&#34;>http://www.rsinfotech.co.uk/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-09-22', 26, 0, '53.4947858175', '-1.8294689814', 5.94, 357, 284, 1, 0, 1, 0, 0, NULL, 0.000, 0),
(2, 430, '15', '-sample- Circuit de Bavay', 'sample_bavay.gpx', '2', '<p>This tracks by has been provided by Arnaud  from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-09-23', 18, 0, '50.297820', '3.792730', 21.92, 254, 254, 1, 0, 1, 0, 0, 0, 0.000, 0),
(3, 430, '15', '-sample- Circuit Honnelles-Belgique', 'sample_honnelles_belgique.gpx', '2', '<p>This tracks by has been provided by Arnaud  from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-09-23', 12, 0, '50.364941', '3.775907', 21.29, 256, 256, 1, 0, 1, 0, 0, 0, 0.000, 0),
(4, 430, '15', '-sample- Circuit Vandegie sur Ecaillon', 'sample_vandegie_sur_ecaillon.gpx', '2', '<p>This tracks by has been provided by Arnaud  from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-09-23', 16, 0, '50.262709', '3.511753', 26.79, 829, 799, 2, 0, 1, 0, 0, 0, 0.000, 0),
(5, 430, '15', '-sample- Via Alpina: Alzarej', 'sample_via_alpina_alzarej.gpx', '2', '', 1, '2013-10-02', 12, 0, '44.205368', '-1.269891', 1720.70, 2408, 1382, 1, 0, 1, 0, 0, NULL, 0.000, 0),
(13, 430, '12', '-sample- Trek Valroc Secteur 3', 'sample_trek_valroc_3.gpx', '', '<p>This tracks by has been provided by Pascal from the French Alpin Club:  club alpin français de l\'ouest dijonnais <a href=&#34;http://valroc.net&#34;>http://valroc.net</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-03', 1, 0, '47.510856846', '4.529040931', 34.99, 915, 844, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(12, 430, '12', '-sample- Trek Valroc Secteur 2', 'sample_trek_valroc_2.gpx', '', '<p>This tracks by has been provided by Pascal from the French Alpin Club:  club alpin français de l\'ouest dijonnais <a href=&#34;http://valroc.net&#34;>http://valroc.net</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-03', 2, 0, '47.318670190', '4.598339923', 49.88, 1256, 1235, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(19, 430, '12', '-sample- Trek Valroc Secteur 1', 'sample_trek_valroc_1.gpx', '', '<p>This tracks by has been provided by Pascal from the French Alpin Club:  club alpin français de l\'ouest dijonnais <a href=&#34;http://valroc.net&#34;>http://valroc.net</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-08', 0, 0, '47.323145801', '5.028378814', 53.79, 2017, 1890, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(14, 430, '12', '-sample- Trek ValrocSecteur 4', 'sample_trek_valroc_4.gpx', '', '<p>This tracks by has been provided by Pascal from the French Alpin Club:  club alpin français de l\'ouest dijonnais <a href=&#34;http://valroc.net&#34;>http://valroc.net</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-04', 1, 0, '47.489614505', '4.685898861', 50.14, 1232, 1442, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(15, 430, '12', '-sample- Via Alpina Dobrci', 'sample_via_alpina_dobrci.gpx', '2', '<p>This tracks by has been provided by Henri from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-04', 0, 0, '46.417789', '14.212165', 8.07, 589, 779, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(16, 430, '12', '-sample- Via Alpina Presernova', 'sample_via_alpina_presernova.gpx', '2', '<p>This tracks by has been provided by Henri from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-04', 0, 0, '46.486000', '14.061904', 13.62, 1079, 475, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(17, 430, '12', '-sample- Via Alpina Roblekov', 'sample_via_alpina_roblekov.gpx', '2', '<p>This tracks by has been provided by Henri from the French Alpin Club:  Club Alpin de Lille <a href=&#34;http://clubalpinlille.fr/&#34;>http://clubalpinlille.fr/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-04', 3, 0, '46.431385', '14.174806', 7.26, 1561, 2068, 0, 0, 1, 0, 0, NULL, 0.000, 0),
(20, 430, '15', '-sample- North York Moors - Hutton-le-Hole', 'sample_north_york_moors.gpx', '', '<p>This tracks by has been provided by Richard from RSInfotech <a href=&#34;http://www.rsinfotech.co.uk/&#34;>http://www.rsinfotech.co.uk/</a></p>\r\n<p><em>It its only intended to be used as a sample file for testing and demonstrating J!Track Gallery component.</em></p>', 1, '2013-10-20', 0, 0, '54.30400945', '-0.917664888', 20.66, 352, 352, 0, 0, 1, 0, 0, NULL, 0.000, 0);
