ALTER TABLE `#__jtg_cats` ADD `usepace` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ordering`;

ALTER TABLE `#__jtg_config` ADD `charts_linec_pace` VARCHAR(6) NOT NULL AFTER `charts_linec`, ADD `charts_linec_speed` VARCHAR(6) NOT NULL AFTER `charts_linec_pace`, ADD `charts_linec_heartbeat` VARCHAR(6) NOT NULL AFTER `charts_linec_speed`;
