/* Table structure for table `emails` */

DROP TABLE IF EXISTS `emails`;

CREATE TABLE `emails` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `email` varchar(255) CHARACTER SET utf8 NOT NULL,
    `checked` tinyint(1) NOT NULL DEFAULT '0',
    `valid` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/* Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(255) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `validts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `confirmed` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET unique_checks=0;
SET foreign_key_checks=0;
SET autocommit=0;

