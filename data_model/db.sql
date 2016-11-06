-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `tramap`;
CREATE DATABASE `tramap` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tramap`;

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sport_category_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `event_time_id` int(11) NOT NULL,
  `event_status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`event_status_id`),
  KEY `fk_events_Category_idx` (`sport_category_id`),
  KEY `fk_events_user_identity1_idx` (`owner_id`),
  KEY `fk_event_location1_idx` (`location_id`),
  KEY `fk_event_event_time1_idx` (`event_time_id`),
  KEY `fk_event_event_status1_idx` (`event_status_id`),
  CONSTRAINT `fk_event_event_status1` FOREIGN KEY (`event_status_id`) REFERENCES `event_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_event_time1` FOREIGN KEY (`event_time_id`) REFERENCES `event_time` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_location1` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_Category` FOREIGN KEY (`sport_category_id`) REFERENCES `sport_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_user_identity1` FOREIGN KEY (`owner_id`) REFERENCES `user_identity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `event_status`;
CREATE TABLE `event_status` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `event_time`;
CREATE TABLE `event_time` (
  `id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `position` varchar(45) DEFAULT NULL,
  `event_track` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sport_category`;
CREATE TABLE `sport_category` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_identity`;
CREATE TABLE `user_identity` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `role` enum('admin','user') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_identity` (`id`, `name`, `email`, `role`) VALUES
(1,	'Admin Adminovič',	'test@tramap.com',	'admin');

DROP TABLE IF EXISTS `user_login`;
CREATE TABLE `user_login` (
  `user_id` int(11) NOT NULL,
  `login` varchar(64) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `status` char(10) NOT NULL,
  `failed_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `fk_user_login_user_identity1_idx` (`user_id`),
  KEY `fk_user_login_user_login_status1_idx` (`status`),
  CONSTRAINT `fk_user_login_user_identity1` FOREIGN KEY (`user_id`) REFERENCES `user_identity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`status`) REFERENCES `user_login_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_login` (`user_id`, `login`, `password`, `status`, `failed_attempts`) VALUES
(1,	'admin',	'password to be reset',	'ACTIVE',	0);

DROP TABLE IF EXISTS `user_login_status`;
CREATE TABLE `user_login_status` (
  `id` char(10) NOT NULL,
  `description` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_login_status` (`id`, `description`) VALUES
('ACTIVE',	'Účet aktivován'),
('BLOCKED',	'Účet zablokován'),
('PENDING',	'Účet vytvořen, čeká se na aktivaci');

-- 2016-11-06 13:01:07
