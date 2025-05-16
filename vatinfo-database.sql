-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 17, 2022 at 04:12 PM
-- Server version: 8.0.28
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `VATINFO`
--

-- --------------------------------------------------------

--
-- Table structure for table `vatinfo`
--

CREATE TABLE `vatinfo` (
  `id` int NOT NULL,
  `ident` varchar(30) NOT NULL,
  `cid` varchar(30) NOT NULL,
  `metar` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vatinfo`
--
ALTER TABLE `vatinfo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vatinfo`
--
ALTER TABLE `vatinfo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;