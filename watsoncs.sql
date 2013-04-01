-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2013 at 09:45 AM
-- Server version: 5.1.66
-- PHP Version: 5.3.3

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
-- Table structure for table `batches_for_cf`
--

CREATE TABLE IF NOT EXISTS `batches_for_cf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL COMMENT 'id of the file table, which holds the location of the file',
  `filter_named` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'names of the filters, comma seperated',
  `batch_size` int(11) NOT NULL COMMENT 'number of sentences',
  `comment` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'This is a free format comment field, as a possibility to add some reminders of reason of upload, or content of file',
  `created_by` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'User who converted the file',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cfinput`
--

CREATE TABLE IF NOT EXISTS `cfinput` (
  `auto_id` int(255) NOT NULL,
  `job_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `job_title` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `file_name` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type_of_units` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `template` varchar(255) NOT NULL,
  `max_judgements_per_worker` int(11) NOT NULL,
  `max_judgements_per_ip` int(11) NOT NULL,
  `units_per_assignment` int(11) NOT NULL,
  `units_per_job` int(11) NOT NULL,
  `judgements_per_unit` int(11) NOT NULL,
  `judgements_per_assignment` int(11) NOT NULL,
  `judgements_per_job` int(255) NOT NULL,
  `payment_per_unit` decimal(10,2) NOT NULL,
  `payment_per_assignment` decimal(10,2) NOT NULL,
  `total_payment_per_unit` decimal(10,2) NOT NULL,
  `total_payment_per_job` decimal(10,2) NOT NULL,
  `job_comments` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `job_judgements_made` int(255) NOT NULL,
  `job_completion` double(10,2) NOT NULL,
  `run_time` double(10,2) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cfinput`
--

INSERT INTO `cfinput` (`auto_id`, `job_id`, `job_title`, `created_date`, `file_name`, `type_of_units`, `template`, `max_judgements_per_worker`, `max_judgements_per_ip`, `units_per_assignment`, `units_per_job`, `judgements_per_unit`, `judgements_per_assignment`, `judgements_per_job`, `payment_per_unit`, `payment_per_assignment`, `total_payment_per_unit`, `total_payment_per_job`, `job_comments`, `job_judgements_made`, `job_completion`, `run_time`, `status`) VALUES
(1, '176454', 'Find the relation between the two terms', '2013-03-12 21:59:54', 'Chang-location.xlsx', 'NA', 'With definitions and with extra questions', 5, 5, 2, 102, 6, 12, 1218, '1.00', '2.00', '6.00', '1218.00', 'Run for one week', 0, 0.00, 14.47, 'Paused'),
(2, '176479', 'Find term relations', '2013-03-12 23:58:38', 'Chang-treat.xlsx', 'NA', 'With definitions but without extra questions', 6, 6, 1, 184, 5, 5, 920, '2.00', '2.00', '10.00', '1840.00', '', 0, 0.00, 14.38, 'Canceled'),
(3, '176655', 'Defind relations between two highlighted keywords', '2013-03-13 09:56:52', '70-Sentences-ChangSet5.xlsx', 'NA', 'Without definitions but with extra questions', 8, 8, 1, 109, 5, 5, 545, '5.00', '5.00', '25.00', '2725.00', '', 0, 0.00, 13.97, 'Paused'),
(4, '176657', 'Defind relations', '2013-03-13 10:10:15', '70-Sentences-ChangSet6.xlsx', 'NA', 'Without definitions and without extra questions', 10, 10, 1, 103, 8, 8, 824, '2.00', '2.00', '16.00', '1648.00', '', 0, 0.00, 13.96, 'Running'),
(5, '176680', 'Defind relations', '2013-03-13 14:56:06', '50-1-sentences.csv', 'NA', 'Without definitions but with extra questions', 2, 2, 1, 1599, 5, 5, 7995, '1.00', '1.00', '5.00', '7995.00', '', 0, 0.00, 13.76, 'Running'),
(6, '176681', 'Find the relation between the two terms', '2013-03-13 15:01:54', 'w21-cause-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 200, 5, 5, 1000, '20.00', '20.00', '100.00', '20000.00', '', 0, 0.00, 13.76, 'Running'),
(7, '177022', 'Defind relations', '2013-03-14 17:58:04', 'w21-treat-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 200, 5, 5, 1000, '2.00', '2.00', '10.00', '2000.00', '', 0, 0.00, 12.63, 'Running'),
(8, '177051', 'Find the Relations between two Highlighted terms', '2013-03-14 20:31:05', 'w21-treat-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 200, 5, 5, 1000, '1.00', '1.00', '5.00', '1000.00', '', 0, 0.00, 12.53, 'Running'),
(9, '177053', 'Find relations', '2013-03-14 20:53:18', 'w21-symptom-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 200, 5, 5, 1000, '2.00', '2.00', '10.00', '2000.00', '', 0, 0.00, 12.51, 'Running'),
(10, '177055', 'Find the relation between two terms', '2013-03-14 21:01:54', 'w21-location-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 10, 5, 40, 5, 25, 1000, '20.00', '100.00', '100.00', '20000.00', '', 0, 0.00, 12.51, 'Running'),
(11, '177056', 'Find term relations', '2013-03-14 21:03:59', 'w21-diagnose-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 10, 1, 200, 15, 15, 3000, '5.00', '5.00', '75.00', '15000.00', '', 0, 0.00, 12.51, 'Running'),
(12, '177250', 'Find relations', '2013-03-15 10:59:55', 'w21-cause-0.1.txt.filterbykb.sample.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 200, 5, 5, 1000, '1.00', '1.00', '5.00', '1000.00', '', 0, 0.00, 11.93, 'Running'),
(13, '177742', 'Test to find relations', '2013-03-17 21:03:56', '', 'NA', 'With definitions and with extra questions', 5, 5, 1, 0, 5, 5, 0, '1.00', '1.00', '5.00', '0.00', '', 0, 0.00, 9.51, 'Paused'),
(14, '179912', 'test', '2013-03-26 17:18:43', 'w21-prevent-0.1.txt.filterbykb.sample-all.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 315, 5, 5, 1575, '20.00', '20.00', '1.46', '461.00', '', 0, 0.00, 0.66, 'Running'),
(15, '180152', 'Morning Test', '2013-03-27 05:05:00', 'w21-diagnose-0.1.txt.filterbykb.sample-all.csv', 'NA', 'Without definitions but with extra questions', 5, 5, 1, 328, 5, 5, 1640, '30.00', '30.00', '2.20', '720.04', '', 0, 0.00, 0.17, 'Canceled'),
(16, '180164', 'Find the relation between the two terms', '2013-03-27 08:12:28', 'w21-treat-0.1.txt.filterbykb.sample-all.csv', 'NA', 'With definitions and with extra questions', 5, 5, 1, 292, 5, 5, 1460, '20.00', '20.00', '1.46', '427.34', '', 0, 0.00, 0.04, 'Paused');

-- --------------------------------------------------------

--
-- Table structure for table `file_storage`
--

CREATE TABLE IF NOT EXISTS `file_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `storage_path` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mime_type` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filesize` int(11) NOT NULL,
  `createdby` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `file_storage`
--

INSERT INTO `file_storage` (`id`, `original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`, `created`) VALUES
(1, 'index.php', '/var/www/files/2013/03/25/5150bbbc1bec4_index.php', '0', 6680, '0', '2013-03-25 21:03:56'),
(2, 'jquery.html', '/var/www/files/2013/03/26/5151590825fdf_jquery.html', '0', 31477, '0', '2013-03-26 08:15:04'),
(3, 'w21-cause-0.1.txt.filterbykb.sample-all.csv', '/var/www/files/2013/03/26/5151604fc6d8a_w21-cause-0.1.txt.filterbykb.sample-all.csv', '0', 99601, '0', '2013-03-26 08:46:07'),
(4, 'index.php', '/var/www/files/2013/03/26/5151987394905_index.php', 'application/octet-st', 7066, 'hui', '2013-03-26 12:45:39'),
(5, 'w21-treat-0.1.txt.filterbykb.sample-all.csv', '/var/www/files/2013/03/27/5152e27b452b8_w21-treat-0.1.txt.filterbykb.sample-all.csv', 'application/vnd.ms-excel', 91028, 'hui', '2013-03-27 12:13:47'),
(6, 'w21-symptom-0.1.txt.filterbykb.sample-all.csv', '/var/www/files/2013/03/27/5152f01fe765e_w21-symptom-0.1.txt.filterbykb.sample-all.csv', 'application/vnd.ms-excel', 94041, 'hui', '2013-03-27 13:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `filtered_file`
--

CREATE TABLE IF NOT EXISTS `filtered_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL COMMENT 'id of the file table, which holds the location of the file',
  `processing_file_id` int(11) NOT NULL COMMENT 'id of the file table, which holds the location of the file',
  `sentence_length` char(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'short/long',
  `relation_location` char(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'RBA/ROA/NOR',
  `special_cases` char(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'SC/ABB/NOSC/NOABB/NOSPC',
  `comment` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'This is a free format comment field, as a possibility to add some reminders of reason of upload, or content of file',
  `created_by` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'User who converted the file',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `processing_file`
--

CREATE TABLE IF NOT EXISTS `processing_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileid` int(11) NOT NULL COMMENT 'id of the file table, which holds the location of the file',
  `lines` int(11) NOT NULL COMMENT 'Number of sentences in the file, must be calculated when uploaded',
  `comment` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'This is a free format comment field, as a possibility to add some reminders of reason of upload, or content of file',
  `createdby` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'User who converted the file',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `raw_file`
--

CREATE TABLE IF NOT EXISTS `raw_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seedrelationname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fileid` int(11) NOT NULL COMMENT 'id of the file table, which holds the location of the file',
  `lines` int(11) NOT NULL COMMENT 'Number of lines in the file, must be calculated when uploaded',
  `comment` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'This is a free format comment field, as a possibility to add some reminders of reason of upload, or content of file',
  `createdby` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'User who uploaded the file',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `raw_file`
--

INSERT INTO `raw_file` (`id`, `seedrelationname`, `fileid`, `lines`, `comment`, `createdby`, `created`) VALUES
(1, 'test file', 1, 167, 'this is realy a pá¸§p file', 'manfred', '2013-03-25 21:03:56'),
(2, 'fake file', 2, 451, 'this is a fake file', 'manfred', '2013-03-26 08:15:04'),
(3, 'cause test', 3, 321, 'just test', 'hui', '2013-03-26 08:46:07'),
(4, 'test', 4, 188, 'test', 'hui', '2013-03-26 12:45:39'),
(5, 'treat', 5, 294, 'test', 'hui', '2013-03-27 12:13:47'),
(6, 'Test afternoon', 6, 286, 'Test', 'hui', '2013-03-27 13:11:59');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
