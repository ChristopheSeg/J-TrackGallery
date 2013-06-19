INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'1',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'2',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'3',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'4',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'5',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'6',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'7',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'8',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'9',
	'0','dummy','','','0','1','0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'10',
	'0',
	'COM_JTG_CAT_CAR',
	'',
	'car.jpg',
	'7',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'11',
	'10',
	'COM_JTG_CAT_CAR_FOCUS',
	'',
	'focus.jpg',
	'1',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'12',
	'10',
	'COM_JTG_CAT_CAR_VECTRA',
	'',
	'vectra.jpg',
	'2',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'13',
	'0',
	'COM_JTG_CAT_BIKE',
	'',
	'bike.jpg',
	'5',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'14',
	'0',
	'COM_JTG_CAT_MOTORBIKE',
	'',
	'motorbike.jpg',
	'6',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'15',
	'0',
	'COM_JTG_CAT_PEDESTRIAN',
	'',
	'pedestrian.jpg',
	'3',
	'1',
	'0'
);

INSERT IGNORE INTO `#__jtg_cats` (
	`id`,
	`parent_id`,
	`title`,
	`description`,
	`image`,
	`ordering`,
	`published`,
	`checked_out`
) VALUES (
	'16',
	'0',
	'COM_JTG_CAT_GEOCACHE',
	'',
	'geocaching.jpg',
	'4',
	'1',
	'0'
);

DELETE FROM `#__jtg_cats` WHERE title = 'dummy';
