ALTER TABLE `#__jtg_config` ADD `max_images` INT( 10 ) NOT NULL DEFAULT '10';

ALTER TABLE `#__jtg_config` ADD `gallery_code` VARCHAR( 200 ) NOT NULL DEFAULT '' AFTER `gallery` ;
