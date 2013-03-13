-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 12, 2013 at 12:01 PM
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
  `job_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `file_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `job_title` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `judgement_per_unit` int(11) NOT NULL,
  `max_judgement_per_worker` int(11) NOT NULL,
  `units_per_assignment` int(11) NOT NULL,
  `max_judgement_per_ip` int(11) NOT NULL,
  `cents_per_assignment` decimal(11,0) NOT NULL,
  `comments` longtext NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `csvinput`
--

INSERT INTO `csvinput` (`auto_id`, `job_id`, `created_date`, `file_name`, `job_title`, `judgement_per_unit`, `max_judgement_per_worker`, `units_per_assignment`, `max_judgement_per_ip`, `cents_per_assignment`, `comments`) VALUES
(1, '3292879846', '2013-03-05 00:23:45', 'job-sentences.csv', 'Find the relation between the two terms', 15, 10, 1, 10, 10, ''),
(2, '3299829653', '2013-03-05 00:33:09', 'job-sentences.csv', 'Find the relation between the two terms', 15, 10, 1, 10, 10, ''),
(3, '3952870717', '2013-03-05 15:51:17', 'job-sentences.csv', 'Find relations', 1, 2, 3, 4, 5, ''),
(4, '1893416898', '2013-03-06 10:35:35', 'w21-contra-0.1.txt.filterbykb.sample-all.csv', 'Defind relations between two highlighted keywords', 20, 5, 1, 10, 15, 'This job will run for one week '),
(5, '2541316868', '2013-03-06 10:48:51', 'Chang-cause.csv', 'Defind relations between two highlighted keywords', 5, 2, 1, 4, 20, 'It will be expired after one week'),
(6, '9532497953', '2013-03-06 11:00:30', 'Chang-prevent.csv', 'How to relate the two keywords', 8, 8, 1, 8, 18, 'Run for three days'),
(7, '1609608770', '2013-03-06 13:48:08', 'Chang-cause.csv', 'test', 15, 10, 1, 10, 1, ''),
(8, '1894673645', '2013-03-06 16:40:27', 'Chang-cause.csv', 'Defind relations', 10, 10, 1, 10, 5, ''),
(9, '3759651405', '2013-03-06 17:31:31', 'w21-diagnose-0.1.txt.filterbykb.sample.csv', 'Find the relation between two terms', 10, 5, 1, 5, 0, ''),
(10, '4420482127', '2013-03-08 14:07:31', '', '', 0, 0, 0, 0, 0, ''),
(11, '1258179336', '2013-03-11 10:19:00', 'w21-contra-0.1.txt.filterbykb.sample.csv', 'fine', 0, 0, 0, 0, 0, ''),
(12, '3641101180', '2013-03-11 10:20:29', 'w21-contra-0.1.txt.filterbykb.sample.csv', 'find term relations', 6, 6, 1, 6, 5, 'run for two days');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
