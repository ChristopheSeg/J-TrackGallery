ALTER TABLE `#__jtg_config` CHANGE `gpsstore` `gpsstore` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT `JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "uploaded_tracks". DIRECTORY_SEPARATOR`
ALTER TABLE `#__jtg_config` ADD `max_thumb_height` INT( 10 ) NOT NULL AFTER `max_size` ;
ALTER TABLE `#__jtg_config` ADD `max_geoim_height` INT( 10 ) NOT NULL AFTER `max_size` ;