
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `area` (
  `area_code` varchar(7) NOT NULL,
  `areatype_code` varchar(1) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`area_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `areatype`
--

DROP TABLE IF EXISTS `areatype`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `areatype` (
  `areatype_code` varchar(1) NOT NULL,
  `areatype_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`areatype_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `data`
--

DROP TABLE IF EXISTS `data`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `data` (
  `series_id` varchar(30) NOT NULL,
  `year` varchar(4) NOT NULL,
  `period` varchar(3) NOT NULL,
  `value` varchar(12) NOT NULL,
  `footnote_codes` varchar(1) NOT NULL,
  PRIMARY KEY  (`series_id`,`year`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `datatype`
--

DROP TABLE IF EXISTS `datatype`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `datatype` (
  `datatype_code` varchar(2) NOT NULL,
  `datatype_name` varchar(100) NOT NULL,
  `footnote_code` varchar(1) NOT NULL,
  PRIMARY KEY  (`datatype_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `employment_data`
--

DROP TABLE IF EXISTS `employment_data`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `employment_data` (
  `data_id` int(11) NOT NULL auto_increment,
  `occugroup_code` varchar(6) default NULL,
  `occupation_code` varchar(6) default NULL,
  `area_code` varchar(7) default NULL,
  `areatype_code` varchar(1) default NULL,
  `industry_code` varchar(6) default NULL,
  `end_year` varchar(4) default NULL,
  `employment` varchar(12) default NULL,
  `hourly_mean` varchar(12) default NULL,
  `annual_mean` varchar(12) default NULL,
  `hourly_ten` varchar(12) default NULL,
  `hourly_twentyfive` varchar(12) default NULL,
  `hourly_median` varchar(12) default NULL,
  `hourly_seventyfive` varchar(12) default NULL,
  `hourly_ninety` varchar(12) default NULL,
  `annual_ten` varchar(12) default NULL,
  `annual_twentyfive` varchar(12) default NULL,
  `annual_median` varchar(12) default NULL,
  `annual_seventyfive` varchar(12) default NULL,
  `annual_ninety` varchar(12) default NULL,
  PRIMARY KEY  (`data_id`),
  KEY `data_index` (`occugroup_code`,`occupation_code`,`area_code`,`areatype_code`,`industry_code`,`end_year`,`employment`,`annual_mean`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `footnote`
--

DROP TABLE IF EXISTS `footnote`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `footnote` (
  `footnote_code` varchar(1) NOT NULL,
  `footnote_text` varchar(250) NOT NULL,
  PRIMARY KEY  (`footnote_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `industry` (
  `industry_code` varchar(6) NOT NULL,
  `industry_name` varchar(100) NOT NULL,
  `display_level` varchar(2) NOT NULL,
  `selectable` varchar(1) NOT NULL,
  `sort_sequence` varchar(5) NOT NULL,
  PRIMARY KEY  (`industry_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `industry_titles`
--

DROP TABLE IF EXISTS `industry_titles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `industry_titles` (
  `industry_code` varchar(6) NOT NULL,
  `industry_title` varchar(255) NOT NULL,
  PRIMARY KEY  (`industry_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `occugroup`
--

DROP TABLE IF EXISTS `occugroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `occugroup` (
  `occugroup_code` varchar(6) NOT NULL,
  `occugroup_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`occugroup_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `occupation`
--

DROP TABLE IF EXISTS `occupation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `occupation` (
  `occupation_code` varchar(6) NOT NULL,
  `occupation_name` varchar(100) NOT NULL,
  `display_level` varchar(1) NOT NULL,
  `selectable` varchar(1) NOT NULL,
  `sort_sequence` varchar(5) NOT NULL,
  PRIMARY KEY  (`occupation_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `occupation_definitions`
--

DROP TABLE IF EXISTS `occupation_definitions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `occupation_definitions` (
  `occ_code` varchar(10) NOT NULL,
  `occ_title` varchar(255) NOT NULL,
  `def` varchar(6000) NOT NULL,
  PRIMARY KEY  (`occ_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `release`
--

DROP TABLE IF EXISTS `release`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `release` (
  `release_date` varchar(7) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY  (`release_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `seasonal`
--

DROP TABLE IF EXISTS `seasonal`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `seasonal` (
  `seasonal` varchar(1) NOT NULL,
  `seasonal_text` varchar(30) NOT NULL,
  PRIMARY KEY  (`seasonal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sector`
--

DROP TABLE IF EXISTS `sector`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sector` (
  `sector_code` varchar(6) NOT NULL,
  `sector_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`sector_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `series`
--

DROP TABLE IF EXISTS `series`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `series` (
  `series_id` varchar(30) NOT NULL,
  `seasonal` varchar(1) NOT NULL,
  `areatype_code` varchar(1) NOT NULL,
  `area_code` varchar(7) NOT NULL,
  `industry_code` varchar(6) NOT NULL,
  `occupation_code` varchar(6) NOT NULL,
  `datatype_code` varchar(2) NOT NULL,
  `footnote_codes` varchar(10) NOT NULL,
  `begin_year` varchar(4) NOT NULL,
  `begin_period` varchar(3) NOT NULL,
  `end_year` varchar(4) NOT NULL,
  `end_period` varchar(3) NOT NULL,
  PRIMARY KEY  (`series_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `statemsa`
--

DROP TABLE IF EXISTS `statemsa`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `statemsa` (
  `state_code` varchar(2) NOT NULL,
  `msa_code` varchar(7) NOT NULL,
  `msa_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`state_code`,`msa_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-10-28 18:01:30
