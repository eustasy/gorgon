-- phpMyAdmin SQL Dump
-- version 4.6.6deb1+deb.cihar.com~xenial.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2018 at 06:46 PM
-- Server version: 10.1.32-MariaDB-1~xenial
-- PHP Version: 7.1.16-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Gorgon`
--

-- --------------------------------------------------------

--
-- Table structure for table `Issues`
--

CREATE TABLE `Issues` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `Number` int(10) NOT NULL,
  `Created At` int(21) NOT NULL,
  `Updated At` int(21) NOT NULL,
  `Modified At` int(21) NOT NULL,
  `Closed At` int(21) NOT NULL,
  `State` varchar(12) NOT NULL,
  `Assignee` varchar(256) NOT NULL,
  `Karma Total` int(16) DEFAULT NULL,
  `Karma Open` int(16) DEFAULT NULL,
  `Cash Total` decimal(16,2) DEFAULT NULL,
  `Cash Open` decimal(16,2) DEFAULT NULL,
  `Title` varchar(256) NOT NULL,
  `Comments` int(10) NOT NULL,
  `Description` varchar(2048) NOT NULL,
  `Milestone` varchar(256) NOT NULL,
  `Labels` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Meta`
--

CREATE TABLE `Meta` (
  `Name` varchar(256) NOT NULL,
  `Updated` int(16) NOT NULL,
  `APIQueries` int(11) NOT NULL,
  `Affected` int(11) NOT NULL,
  `Total` int(11) NOT NULL,
  `Percentage` float(4,1) NOT NULL,
  `WorkItems` int(11) DEFAULT NULL,
  `Data1` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Repositories`
--

CREATE TABLE `Repositories` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `Outdated` tinyint(1) NOT NULL DEFAULT '1',
  `Updated At` int(21) DEFAULT NULL,
  `Modified At` int(21) DEFAULT NULL,
  `Size` int(16) DEFAULT NULL,
  `Popularity` int(16) DEFAULT NULL,
  `Homepage` varchar(256) DEFAULT NULL,
  `Description` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `repositories_community`
--

CREATE TABLE `repositories_community` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `License_GitHub_Name` varchar(32) NOT NULL,
  `License_GitHub_Link` varchar(256) NOT NULL,
  `License_Detected_Name` varchar(32) NOT NULL,
  `License_Detected_Color` varchar(32) NOT NULL,
  `License_Detected_Year` int(4) DEFAULT NULL,
  `CoC_GitHub_Name` varchar(32) NOT NULL,
  `CoC_GitHub_Link` varchar(256) NOT NULL,
  `CoC_Detected_Name` varchar(64) NOT NULL,
  `CoC_Detected_Color` varchar(32) NOT NULL,
  `Contributing` varchar(256) NOT NULL,
  `IssueTemplate` varchar(256) NOT NULL,
  `PullTemplate` varchar(256) NOT NULL,
  `ReadMe` varchar(256) NOT NULL,
  `Affected` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `repositories_labels`
--

CREATE TABLE `repositories_labels` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `Valid` int(11) NOT NULL,
  `Invalid` int(11) NOT NULL,
  `Missing` int(11) NOT NULL,
  `Affected` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `repositories_with-unreleased-commits`
--

CREATE TABLE `repositories_with-unreleased-commits` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `ReleaseString` varchar(64) DEFAULT NULL,
  `ReleaseVersion` varchar(32) DEFAULT NULL,
  `ReleaseSemVer` tinyint(1) NOT NULL,
  `ReleaseTime` int(16) DEFAULT NULL,
  `CommitsSince` int(11) NOT NULL,
  `CommitsColor` varchar(32) NOT NULL,
  `Affected` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `repositories_without-normalized-builds`
--

CREATE TABLE `repositories_without-normalized-builds` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `VersionString` varchar(32) DEFAULT NULL,
  `VersionColor` varchar(32) DEFAULT NULL,
  `state` varchar(32) DEFAULT NULL,
  `Affected` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `RepositoriesStats`
--

CREATE TABLE `RepositoriesStats` (
  `Organisation` varchar(256) NOT NULL,
  `Repository` varchar(256) NOT NULL,
  `Outdated` tinyint(1) NOT NULL DEFAULT '1',
  `Updated At` int(21) DEFAULT NULL,
  `Karma Total` int(16) DEFAULT NULL,
  `Karma Open` int(16) DEFAULT NULL,
  `Cash Total` decimal(16,2) DEFAULT NULL,
  `Cash Open` decimal(16,2) DEFAULT NULL,
  `Issues Total` int(16) DEFAULT NULL,
  `Issues Open` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Issues`
--
ALTER TABLE `Issues`
  ADD PRIMARY KEY (`Organisation`,`Repository`,`Number`),
  ADD KEY `State` (`State`),
  ADD KEY `Assignee` (`Assignee`),
  ADD KEY `Closed At` (`Closed At`),
  ADD KEY `Labels` (`Labels`(767)),
  ADD KEY `Cash Open` (`Cash Open`),
  ADD KEY `Cash Total` (`Cash Total`),
  ADD KEY `Karma Open` (`Karma Open`),
  ADD KEY `Karma Total` (`Karma Total`),
  ADD KEY `Modified At` (`Modified At`);

--
-- Indexes for table `Meta`
--
ALTER TABLE `Meta`
  ADD PRIMARY KEY (`Name`);

--
-- Indexes for table `Repositories`
--
ALTER TABLE `Repositories`
  ADD PRIMARY KEY (`Organisation`,`Repository`),
  ADD KEY `Changed` (`Outdated`),
  ADD KEY `Updated` (`Updated At`),
  ADD KEY `Popularity` (`Popularity`),
  ADD KEY `Organisation` (`Organisation`),
  ADD KEY `Repository` (`Repository`),
  ADD KEY `Modified At` (`Modified At`),
  ADD KEY `Size` (`Size`);

--
-- Indexes for table `repositories_community`
--
ALTER TABLE `repositories_community`
  ADD PRIMARY KEY (`Organisation`,`Repository`);

--
-- Indexes for table `repositories_labels`
--
ALTER TABLE `repositories_labels`
  ADD PRIMARY KEY (`Organisation`,`Repository`);

--
-- Indexes for table `repositories_with-unreleased-commits`
--
ALTER TABLE `repositories_with-unreleased-commits`
  ADD PRIMARY KEY (`Organisation`,`Repository`);

--
-- Indexes for table `repositories_without-normalized-builds`
--
ALTER TABLE `repositories_without-normalized-builds`
  ADD PRIMARY KEY (`Organisation`,`Repository`);

--
-- Indexes for table `RepositoriesStats`
--
ALTER TABLE `RepositoriesStats`
  ADD PRIMARY KEY (`Organisation`,`Repository`),
  ADD KEY `Organisation` (`Organisation`),
  ADD KEY `Repository` (`Repository`),
  ADD KEY `Updated At` (`Updated At`),
  ADD KEY `Open Issues` (`Issues Open`),
  ADD KEY `Cash Open` (`Cash Open`),
  ADD KEY `Cash Total` (`Cash Total`),
  ADD KEY `Karma Open` (`Karma Open`),
  ADD KEY `Karma Total` (`Karma Total`),
  ADD KEY `Total Issues` (`Issues Total`),
  ADD KEY `Outdated` (`Outdated`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
