-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2013 at 11:34 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jinga`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `commentid` int(11) NOT NULL AUTO_INCREMENT,
  `noteid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `textcomment` text NOT NULL,
  PRIMARY KEY (`commentid`),
  KEY `userid` (`userid`),
  KEY `noteid` (`noteid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `eventid` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  PRIMARY KEY (`eventid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventid`, `refid`, `startdate`, `enddate`) VALUES
(1, 1, '2013-05-28 15:35:33', '2013-05-28 22:35:37'),
(2, 2, '2013-05-28 15:37:20', '2013-05-28 20:37:23'),
(3, 3, '2013-05-28 15:44:31', '2013-05-28 21:44:35'),
(4, 4, '2013-05-28 16:04:28', '2013-05-28 21:04:32'),
(5, 5, '2013-05-28 16:26:53', '2013-05-28 21:26:57'),
(6, 6, '2013-05-28 16:34:25', '2013-05-28 16:34:28'),
(7, 7, '2013-05-28 16:40:30', '2013-05-28 21:40:34'),
(8, 8, '2013-05-28 16:41:27', '2013-05-28 22:41:30'),
(9, 9, '2013-05-28 16:41:27', '2013-05-28 22:41:30'),
(10, 10, '2013-05-28 16:43:50', '2013-05-28 22:43:53'),
(11, 11, '2013-05-28 16:48:42', '2013-05-28 21:48:45'),
(12, 12, '2013-05-28 16:53:44', '2013-05-28 21:53:47'),
(13, 13, '2013-05-28 16:54:18', '2013-05-28 22:54:23'),
(14, 14, '2013-05-28 16:58:06', '2013-05-28 21:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

CREATE TABLE IF NOT EXISTS `filters` (
  `filterid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `status` varchar(30) NOT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `radius` int(10) NOT NULL,
  `feid` int(11) NOT NULL,
  PRIMARY KEY (`filterid`),
  KEY `userid` (`userid`),
  KEY `feid` (`feid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`filterid`, `userid`, `status`, `latitude`, `longitude`, `radius`, `feid`) VALUES
(1, 2, 'Filter1', 40.693687, -73.985619, 60, 1),
(2, 1, 'Test3', 40.693466, -73.985191, 60, 3),
(3, 2, 'FilterC', 44.744778, -69.680779, 40, 5),
(4, 2, 'Qwery', 45.621696, -71.106140, 60, 7),
(5, 1, 'Test', 40.636852, -76.302757, 60, 9),
(6, 1, 'U1', 40.580528, -74.113693, 60, 11);

-- --------------------------------------------------------

--
-- Table structure for table `filter_events`
--

CREATE TABLE IF NOT EXISTS `filter_events` (
  `feid` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  PRIMARY KEY (`feid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `filter_events`
--

INSERT INTO `filter_events` (`feid`, `refid`, `startdate`, `enddate`) VALUES
(1, 1, '2013-05-28 15:36:51', '2013-05-28 20:36:54'),
(2, 1, '2013-05-29 15:36:51', '2013-05-29 20:36:54'),
(3, 3, '2013-05-28 15:38:09', '2013-05-28 22:38:15'),
(4, 3, '2013-05-29 15:38:09', '2013-05-29 22:38:15'),
(5, 5, '2013-05-28 16:36:19', '2013-05-28 16:36:22'),
(6, 5, '2013-05-29 16:36:19', '2013-05-29 16:36:22'),
(7, 7, '2013-05-28 16:48:09', '2013-05-28 22:48:12'),
(8, 7, '2013-05-29 16:48:09', '2013-05-29 22:48:12'),
(9, 9, '2013-05-28 16:57:17', '2013-05-28 22:57:20'),
(10, 9, '2013-05-29 16:57:17', '2013-05-29 22:57:20'),
(11, 11, '2013-05-28 16:58:45', '2013-05-28 21:58:49'),
(12, 11, '2013-05-29 16:58:45', '2013-05-29 21:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `filter_tags`
--

CREATE TABLE IF NOT EXISTS `filter_tags` (
  `filterid` int(11) NOT NULL,
  `tagid` int(11) NOT NULL,
  KEY `filterid` (`filterid`),
  KEY `tagid` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `friendship`
--

CREATE TABLE IF NOT EXISTS `friendship` (
  `userid` int(11) NOT NULL,
  `friendid` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  KEY `userid` (`userid`),
  KEY `friendid` (`friendid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friendship`
--

INSERT INTO `friendship` (`userid`, `friendid`, `status`) VALUES
(2, 1, 1),
(1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `noteid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `content` varchar(200) NOT NULL,
  `url` varchar(2000) DEFAULT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `radius` int(10) NOT NULL,
  `eventid` int(11) NOT NULL,
  `postedtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `boolcomment` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`noteid`),
  KEY `eventid` (`eventid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`noteid`, `userid`, `content`, `url`, `latitude`, `longitude`, `radius`, `eventid`, `postedtime`, `boolcomment`) VALUES
(1, 1, 'Test1', '', 40.693676, -73.985435, 60, 1, '2013-05-28 19:35:45', 0),
(2, 2, 'Test2', '', 40.694798, -73.984947, 60, 2, '2013-05-28 19:37:31', 0),
(3, 2, 'Outoffilter', '', 39.859097, -75.344162, 50, 3, '2013-05-28 19:44:42', 0),
(4, 1, 'Content', '', 39.078789, -75.805511, 60, 4, '2013-05-28 20:04:41', 0),
(5, 1, 'Test3', '', 40.693581, -73.985710, 50, 5, '2013-05-28 20:27:06', 0),
(6, 1, 'Test5', '', 40.484501, -73.866501, 60, 6, '2013-05-28 20:34:37', 0),
(7, 2, 'Blue', '', 44.690109, -69.521446, 40, 7, '2013-05-28 20:40:43', 0),
(8, 2, 'Red', '', 44.724293, -69.702751, 60, 8, '2013-05-28 20:41:38', 0),
(9, 2, 'Red', '', 44.724293, -69.702751, 60, 9, '2013-05-28 20:41:52', 0),
(10, 1, 'Yellow', '', 40.687664, -73.978493, 60, 10, '2013-05-28 20:44:02', 0),
(11, 2, 'Lock', '', 45.623158, -71.034843, 60, 11, '2013-05-28 20:48:55', 0),
(12, 1, 'Black', '', 40.379971, -74.245529, 50, 12, '2013-05-28 20:53:54', 0),
(13, 1, 'Out', '', 39.956039, -74.643822, 50, 13, '2013-05-28 20:54:29', 0),
(14, 1, 'NoteOut', '', 40.826221, -75.190353, 60, 14, '2013-05-28 20:58:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notes_tags`
--

CREATE TABLE IF NOT EXISTS `notes_tags` (
  `noteid` int(11) NOT NULL,
  `tagid` int(11) NOT NULL,
  KEY `noteid` (`noteid`),
  KEY `tagid` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(20) NOT NULL,
  PRIMARY KEY (`tagid`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `firstname`, `lastname`, `latitude`, `longitude`, `lastupdate`) VALUES
(1, 'john1', '123', 'John', 'Smith', 40.603497, -76.354942, '2013-05-28 20:56:33'),
(2, 'susan2', '123', 'Susan', 'Smith', 45.411465, -70.185982, '2013-05-28 20:49:25');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`noteid`) REFERENCES `notes` (`noteid`);

--
-- Constraints for table `filters`
--
ALTER TABLE `filters`
  ADD CONSTRAINT `filters_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `filters_ibfk_2` FOREIGN KEY (`feid`) REFERENCES `filter_events` (`feid`);

--
-- Constraints for table `filter_tags`
--
ALTER TABLE `filter_tags`
  ADD CONSTRAINT `filter_tags_ibfk_1` FOREIGN KEY (`filterid`) REFERENCES `filters` (`filterid`),
  ADD CONSTRAINT `filter_tags_ibfk_2` FOREIGN KEY (`tagid`) REFERENCES `tags` (`tagid`);

--
-- Constraints for table `friendship`
--
ALTER TABLE `friendship`
  ADD CONSTRAINT `friendship_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `friendship_ibfk_2` FOREIGN KEY (`friendid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`eventid`) REFERENCES `events` (`eventid`);

--
-- Constraints for table `notes_tags`
--
ALTER TABLE `notes_tags`
  ADD CONSTRAINT `notes_tags_ibfk_1` FOREIGN KEY (`noteid`) REFERENCES `notes` (`noteid`),
  ADD CONSTRAINT `notes_tags_ibfk_2` FOREIGN KEY (`tagid`) REFERENCES `tags` (`tagid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
