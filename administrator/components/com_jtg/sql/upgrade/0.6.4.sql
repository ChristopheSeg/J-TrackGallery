ALTER TABLE `#__jtg_cats` 
    CHANGE `parent` `parent_id` INT( 10 ) NOT NULL DEFAULT '0';

ALTER TABLE `crl05_osm_config` CHANGE `gid` `gid` VARCHAR( 150 ) NULL DEFAULT NULL;

