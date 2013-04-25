-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: mysql50-18.wc2:3306
-- Generation Time: Jun 15, 2012 at 12:01 PM
-- Server version: 5.0.77
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `574396_clientfunctions`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto_bill`
--

CREATE TABLE IF NOT EXISTS `auto_bill` (
  `anet_customer_id` varchar(15) NOT NULL COMMENT 'CIM Profile ID',
  `anet_payment_id` varchar(15) NOT NULL COMMENT 'CIM Payment ID',
  `platform_id` varchar(10) NOT NULL COMMENT 'SMS Platform Customer ID',
  `chargedate` date NOT NULL COMMENT 'Signup / Bill Date',
  `org` varchar(50) NOT NULL COMMENT 'The company name',
  `country` varchar(2) NOT NULL COMMENT 'SMS Platform Country Code',
  PRIMARY KEY  (`platform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
