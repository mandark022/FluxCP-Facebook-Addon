CREATE TABLE  `cp_fb_register` (
  `fb_userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` text NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `ip_address` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fb_userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;