-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 04, 2013 at 10:00 PM
-- Server version: 5.5.20
-- PHP Version: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `watsoncs`
--

-- --------------------------------------------------------

--
-- Table structure for table `csvinput`
--

CREATE TABLE IF NOT EXISTS `csvinput` (
  `auto_id` int(255) NOT NULL,
  `file_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `file_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `job_title` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `judgement_per_unit` int(11) NOT NULL,
  `max_judgement_per_worker` int(11) NOT NULL,
  `units_per_assignment` int(11) NOT NULL,
  `max_judgement_per_ip` int(11) NOT NULL,
  `cents_per_assignment` decimal(11,0) NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `csvinputdetails`
--

CREATE TABLE IF NOT EXISTS `csvinputdetails` (
  `auto_id` int(255) NOT NULL,
  `file_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `unit_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `relation_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `term1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `b1` int(255) NOT NULL,
  `e1` int(255) NOT NULL,
  `term2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `b2` int(255) NOT NULL,
  `e2` int(255) NOT NULL,
  `sentences` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
