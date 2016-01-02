-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2015 �?04 �?08 �?08:58
-- 服务器版本: 5.6.11
-- PHP 版本: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yuefuling`
--
CREATE DATABASE IF NOT EXISTS `yuefuling` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yuefuling`;

-- --------------------------------------------------------

--
-- 表的结构 `yue_album`
--

CREATE TABLE IF NOT EXISTS `yue_album` (
  `album_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `album_title` varchar(255) NOT NULL,
  `album_cover` varchar(255) NOT NULL DEFAULT '',
  `time` varchar(50) NOT NULL DEFAULT '',
  `styles` varchar(200) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `yue_artist`
--

CREATE TABLE IF NOT EXISTS `yue_artist` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `area` varchar(100) NOT NULL DEFAULT '',
  `birth` varchar(50) NOT NULL DEFAULT '',
  `index_letter` char(5) NOT NULL,
  `area_code` char(7) NOT NULL,
  `type` char(5) NOT NULL,
  `shield_collect` tinyint(1) NOT NULL DEFAULT '0',
  `manual_collect` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `yue_song`
--

CREATE TABLE IF NOT EXISTS `yue_song` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
