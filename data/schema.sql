SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blob` blob,
  `number` int(11) unsigned zerofill NOT NULL,
  `text` varchar(255) NOT NULL,
  `enum` enum('foo') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `metadata_types` (
  `tinyint` tinyint(4) NOT NULL,
  `smallint` smallint(6) NOT NULL,
  `mediumint` mediumint(9) NOT NULL,
  `int` int(11) NOT NULL,
  `bigint` bigint(20) NOT NULL,
  `decimal` decimal(10,0) NOT NULL,
  `float` float NOT NULL,
  `double` double NOT NULL,
  `real` double NOT NULL,
  `bit` bit(1) NOT NULL,
  `boolean` tinyint(1) NOT NULL,
  `serial` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `datetime` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time` time NOT NULL,
  `year` year(4) NOT NULL,
  `char` char(5) NOT NULL,
  `varchar` varchar(55) NOT NULL,
  `tinytext` tinytext NOT NULL,
  `text` text NOT NULL,
  `mediumtext` mediumtext NOT NULL,
  `longtext` longtext NOT NULL,
  `binary` binary(1) NOT NULL,
  `varbinary` varbinary(50) NOT NULL,
  `tinyblob` tinyblob NOT NULL,
  `mediumblob` mediumblob NOT NULL,
  `blob` blob NOT NULL,
  `longblob` longblob NOT NULL,
  `enum` enum('foo') NOT NULL,
  `set` set('foo') NOT NULL,
  `geometry` geometry NOT NULL,
  `point` point NOT NULL,
  `linestring` linestring NOT NULL,
  `polygon` polygon NOT NULL,
  `multipoint` multipoint NOT NULL,
  `multilinestring` multilinestring NOT NULL,
  `multipolygon` multipolygon NOT NULL,
  `geometrycollection` geometrycollection NOT NULL,
  PRIMARY KEY (`tinyint`),
  UNIQUE KEY `serial` (`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `select` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) CHARACTER SET utf16 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `select` (`id`, `text`) VALUES
(1, 'foo'),
(2, 'bar'),
(3, 'baz');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
