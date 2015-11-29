ALTER TABLE `#__jtg_files` ADD `isroundtrip` TINYINT(1) NOT NULL DEFAULT '0' AFTER `iscache`;

ALTER TABLE `#__jtg_cats` ADD `default_map` INT(2) NULL DEFAULT NULL AFTER `published`, ADD `default_overlays` TEXT 
NULL DEFAULT NULL AFTER `default_map`;

ALTER TABLE `#__jtg_files` ADD `default_map` INT(2) NULL DEFAULT NULL AFTER `published`, ADD `default_overlays` TEXT 
NULL DEFAULT NULL;
