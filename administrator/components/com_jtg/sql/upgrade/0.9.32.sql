ALTER TABLE `#__jtg_comments`
MODIFY `email` varchar(80);
ALTER TABLE `#__jtg_comments`
MODIFY `homepage` varchar(255);
ALTER TABLE `#__jtg_comments`
ADD IF NOT EXISTS `uid` int(10);
