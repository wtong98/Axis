-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2016 at 09:07 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--
create DATABASE axis
USE axis;

-- ---------------------------------------------------------
-- Really all just for show
CREATE TABLE records (
	department varchar(30) NOT NULL,
	book_id INTEGER UNSIGNED NOT NULL,
	book_name varchar(100) NOT NULL,
	student_name varchar(50) NOT NULL,
	student_id INTEGER UNSIGNED NOT NULL,
	teacher_name varchar(30) NOT NULL,

	PRIMARY KEY (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
