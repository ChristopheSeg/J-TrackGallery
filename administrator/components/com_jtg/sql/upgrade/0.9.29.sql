CREATE TABLE IF NOT EXISTS `#__jtg_photos` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `trackID` int(10) NOT NULL,
        `title` varchar(256),
        `filename` varchar(50) NOT NULL,
        `lat` float(24),
        `lon` float(24),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`trackID`) REFERENCES `#__jtg_files(id)`
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

