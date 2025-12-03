-- AdminNeo 5.0.0 MySQL 8.0.35 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `wp_ccm_customers`;
CREATE TABLE `wp_ccm_customers` (
  `id` mediumint NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `cr_number` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_520_ci,
  `city` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `country` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'active',
  `name` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `dob` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cr_number` (`cr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_ccm_customers` (`id`, `first_name`, `last_name`, `email`, `phone`, `created_at`, `date_of_birth`, `gender`, `cr_number`, `address`, `city`, `country`, `status`, `name`, `dob`) VALUES
(1,	'John',	'Doe',	'johndoe@example.com',	'1234567890',	'2025-12-02 11:02:52',	'1990-01-01',	'Male',	'ABC124',	'vdjhgjf jbu',	'NYC',	'USA',	'active',	'John',	'0000-00-00'),
(3,	'',	'',	'belle07@example.com',	'0987654321',	'0000-00-00 00:00:00',	NULL,	'Female',	'NG416',	'fdyvjvfbko gjfugk',	'washington',	'India',	'active',	'Belle',	'1995-10-01');

-- 2025-12-03 03:49:24 UTC
