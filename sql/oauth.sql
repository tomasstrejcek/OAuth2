SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `oauth_access_token`;
CREATE TABLE `oauth_access_token` (
  `access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `client_id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`access_token`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `oauth_access_token_ibfk_4` FOREIGN KEY (`client_id`) REFERENCES `oauth_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `oauth_access_token_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `oauth_access_token_scope`;
CREATE TABLE `oauth_access_token_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `scope_name` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`),
  KEY `scope_name` (`scope_name`),
  CONSTRAINT `oauth_access_token_scope_ibfk_1` FOREIGN KEY (`access_token`) REFERENCES `oauth_access_token` (`access_token`) ON DELETE CASCADE,
  CONSTRAINT `oauth_access_token_scope_ibfk_2` FOREIGN KEY (`scope_name`) REFERENCES `oauth_scope` (`name`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `oauth_authorization_code`;
CREATE TABLE `oauth_authorization_code` (
  `authorization_code` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `client_id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`authorization_code`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `oauth_authorization_code_ibfk_5` FOREIGN KEY (`client_id`) REFERENCES `oauth_client` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `oauth_authorization_code_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `oauth_authorization_code_scope`;
CREATE TABLE `oauth_authorization_code_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authorization_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `scope_name` varchar(80) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `authorization_code` (`authorization_code`),
  KEY `scope_name` (`scope_name`),
  CONSTRAINT `oauth_authorization_code_scope_ibfk_1` FOREIGN KEY (`authorization_code`) REFERENCES `oauth_authorization_code` (`authorization_code`) ON DELETE CASCADE,
  CONSTRAINT `oauth_authorization_code_scope_ibfk_2` FOREIGN KEY (`scope_name`) REFERENCES `oauth_scope` (`name`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `oauth_client`;
CREATE TABLE `oauth_client` (
  `id` binary(16) NOT NULL,
  `secret` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `redirect_url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `oauth_client` (`id`, `secret`, `redirect_url`) VALUES
('d3a213ad-d142-11',	'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12',	'http://localhost/OAuth2/');

DROP TABLE IF EXISTS `oauth_refresh_token`;
CREATE TABLE `oauth_refresh_token` (
  `refresh_token` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `client_id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`refresh_token`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `oauth_refresh_token_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `oauth_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `oauth_scope`;
CREATE TABLE `oauth_scope` (
  `name` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `oauth_scope` (`name`, `description`) VALUES
('notebooks',	'All user\'s notebooks'),
('user_profile',	'User\'s private profile information');

DROP TABLE IF EXISTS `oauth_user`;
CREATE TABLE `oauth_user` (
  `id` binary(16) NOT NULL COMMENT 'UUID',
  `username` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `password` char(64) COLLATE utf8_czech_ci NOT NULL COMMENT 'HMAC sha256 hashed password ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
