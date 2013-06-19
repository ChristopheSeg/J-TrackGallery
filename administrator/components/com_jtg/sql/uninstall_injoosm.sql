DROP TABLE IF EXISTS `#__osm_maps`;

DROP TABLE IF EXISTS `#__osm_cats`;

DROP TABLE IF EXISTS `#__osm_config`;

DROP TABLE IF EXISTS `#__osm_files`;

DROP TABLE IF EXISTS `#__osm_votes`;

DROP TABLE IF EXISTS `#__osm_terrain`;

DROP TABLE IF EXISTS `#__osm_comments`;

ALTER IGNORE TABLE `#__users`
	DROP COLUMN osmlat,
	DROP COLUMN osmlon,
	DROP COLUMN osmvisible
;
