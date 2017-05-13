-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2016 at 09:07 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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

USE axis;

CREATE TABLE `book` (
  `book_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
   PRIMARY KEY (book_id),
  `IMSA_id` varchar(15) NOT NULL,
  `title` varchar(100) NOT NULL,
  `ISBN` varchar(50) NOT NULL,
  `cost` varchar(10),
  `fee` varchar(10),
  `dept_id` int(12) NOT NULL,
  FOREIGN KEY (dept_id) REFERENCES department(dept_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(dept_id),
  `name` varchar(50) NOT NULL,
  `dept_admin` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `stud_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(stud_id),
  `IMSA_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50),
  `owed` varchar(50),
  `parent_email` varchar(50),
  `parent_number` varchar(15)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teacher` (
  `id` int(12) UNSIGNED NOT NULL,
  PRIMARY KEY(id),
  `privilege` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `checking` (
	`book_id` int(8) NOT NULL,
	FOREIGN KEY (book_id) REFERENCES book(book_id),
	`book_title` varchar(100),
	`student_id` varchar(20) NOT NULL, 
	Foreign KEY (student_id) REFERENCES student(IMSA_id),
	PRIMARY KEY(book_id,student_id),
	`date_checkedout` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`date_checkedin` datetime,
	`year_checkedout` int(4),
	`semester_checkedout` varchar(10)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ---------------------------------------------------------
-- Really all just for show
CREATE TABLE 'records' (
	'department' varchar(30) NOT NULL,
	'book_id' INTEGER UNSIGNED NOT NULL,
	'book_name' varchar(100) NOT NULL,
	'student_name' varchar(50) NOT NULL,
	'student_id' INTEGER UNSIGNED NOT NULL,
	'teacher_name' varchar(30) NOT NULL,

	PRIMARY KEY (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
