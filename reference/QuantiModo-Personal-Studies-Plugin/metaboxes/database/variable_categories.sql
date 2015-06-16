-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2014 at 01:48 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test2`
--

-- --------------------------------------------------------

--
-- Table structure for table `variable_categories`
--

CREATE TABLE IF NOT EXISTS `variable_categories` (
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(64) NOT NULL COMMENT 'Name of the category',
  `filling-value` double DEFAULT NULL,
  `maximum-value` double DEFAULT NULL,
  `minimum-value` double DEFAULT NULL,
  `duration-of-action` int(10) unsigned NOT NULL DEFAULT '86400',
  `onset-delay` int(10) unsigned NOT NULL DEFAULT '0',
  `combination-operation` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'How to combine values of this variable (for instance, to see a summary of the values over a month) 0 for sum OR 1 for mean',
  `updated` int(11) NOT NULL DEFAULT '1',
  `cause-only` tinyint(1) NOT NULL DEFAULT '0',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `filling-type` enum('none','value') DEFAULT 'none' COMMENT '0 -> No filling, 1 -> Use filling-value',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `variable_categories`
--

INSERT INTO `variable_categories` (`id`, `name`, `filling-value`, `maximum-value`, `minimum-value`, `duration-of-action`, `onset-delay`, `combination-operation`, `updated`, `cause-only`, `public`, `filling-type`) VALUES
(1, 'Mood', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(2, 'Physique', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(3, 'Physical Activity', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(4, 'Location', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(5, 'Miscellaneous', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(6, 'Sleep', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(7, 'Social Interactions', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(8, 'Vital Signs', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(9, 'Cognitive Performance', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(10, 'Symptoms', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(11, 'Nutrition', -1, NULL, NULL, 86400, 0, 1, 1, 1, 1, ''),
(12, 'Work', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(13, 'Medications', 0, NULL, 0, 86400, 1800, 1, 1, 1, 1, 'value'),
(14, 'Activity', -1, NULL, NULL, 86400, 0, 1, 1, 0, 1, ''),
(15, 'Foods', 0, NULL, 0, 259200, 1800, 1, 1, 1, 1, 'value'),
(17, 'Environment', -1, NULL, NULL, 10800, 0, 0, 1, 1, 1, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
