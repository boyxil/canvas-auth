CREATE TABLE `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `password` char(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `is_enabled` tinyint(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username_password` (`username`,`password`),
  KEY `email_username` (`email`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
