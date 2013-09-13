--
-- TODO Obsolete: this file might be deleted (now included in install.sql)
--

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
