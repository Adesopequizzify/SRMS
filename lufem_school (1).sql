-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 23, 2024 at 09:20 AM
-- Server version: 5.7.34
-- PHP Version: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lufem_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year` varchar(9) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year`, `created_at`) VALUES
(1, '2023-2024', '2024-11-23 06:26:53'),
(2, '2024-2025', '2024-11-23 06:26:53'),
(3, '2025-2026', '2024-11-23 06:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-11-21 09:10:14'),
(2, 'muiz.dev.io@gmail.com', '$2y$10$GlFS9baWhOzF8QT3Z6teZ.GS8BPV3aiJIOoJrOq7CnCc9APi7O3oO', '2024-11-21 09:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `department` enum('Multimedia Technology','Business Informatics','Software Engineering') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grade_thresholds` json NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `department`, `created_at`, `grade_thresholds`) VALUES
(6, 'Mechanical measurement', 'MEC112', 'Software Engineering', '2024-11-21 21:40:16', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(5, 'Engineering measurements', 'Mec 11', 'Business Informatics', '2024-11-21 21:39:27', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(4, 'Mechanical engineering measurement', 'CTE 112', 'Business Informatics', '2024-11-21 21:38:17', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(7, 'Engineering measurements t', 'Nmr', 'Business Informatics', '2024-11-22 19:33:40', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(8, 'Jdub', 'Nmryu', 'Multimedia Technology', '2024-11-22 19:38:04', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(9, 'Engineering measurements i', 'Hjk-22', 'Multimedia Technology', '2024-11-23 08:00:06', '{\"A\": 70, \"B\": 60, \"C\": 50, \"D\": 40, \"E\": 30, \"F\": 20}'),
(10, 'Mechanical engineering measurementk', 'Nmkl', 'Business Informatics', '2024-11-23 08:10:52', '{\"A\": 90, \"B\": 80, \"C\": 70, \"D\": 60, \"E\": 50, \"F\": 20}');

-- --------------------------------------------------------

--
-- Table structure for table `course_registrations`
--

CREATE TABLE `course_registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `academic_year_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_registrations`
--

INSERT INTO `course_registrations` (`id`, `student_id`, `course_id`, `created_at`, `academic_year_id`, `session_id`) VALUES
(2, 7, 5, '2024-11-23 03:51:11', 0, 0),
(3, 7, 4, '2024-11-23 03:51:11', 0, 0),
(4, 7, 7, '2024-11-23 03:51:11', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `overall_results`
--

CREATE TABLE `overall_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `gpa` float NOT NULL,
  `final_remark` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `academic_year_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `overall_results`
--

INSERT INTO `overall_results` (`id`, `student_id`, `gpa`, `final_remark`, `created_at`, `academic_year_id`) VALUES
(1, 3, 0, 'Fail', '2024-11-23 04:33:53', 0),
(2, 7, 1.67, 'Third Class', '2024-11-23 04:34:40', 0);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `score` float NOT NULL,
  `grade` char(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `academic_year_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `course_id`, `score`, `grade`, `created_at`, `academic_year_id`, `session_id`) VALUES
(1, 3, 6, 3, 'F', '2024-11-23 04:33:53', 0, 0),
(2, 7, 4, 36, 'E', '2024-11-23 04:34:40', 0, 0),
(3, 7, 5, 39, 'E', '2024-11-23 04:34:40', 0, 0),
(4, 7, 7, 58, 'C', '2024-11-23 04:34:40', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `name`, `created_at`) VALUES
(1, 'First Semester', '2024-11-23 06:26:53'),
(2, 'Second Semester', '2024-11-23 06:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `matric_number` varchar(20) NOT NULL,
  `department` enum('Multimedia Technology','Business Informatics','Software Engineering') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `gender`, `matric_number`, `department`, `created_at`) VALUES
(4, 'Hello', 'Muiz', 'Female', 'Hisbsb7262', 'Software Engineering', '2024-11-22 19:34:45'),
(3, 'Adesope', 'Muiz', 'Female', 'F/ND/23/3450051', 'Software Engineering', '2024-11-21 21:37:46'),
(5, 'Hello', 'Adesope', 'Male', '63638383gs', 'Multimedia Technology', '2024-11-22 19:38:29'),
(6, 'Fenee', 'Holla', 'Female', 'F/ND/23/3450054', 'Software Engineering', '2024-11-23 03:48:45'),
(7, 'Gehtude', 'Mary', 'Female', 'F/ND/23/3450055', 'Business Informatics', '2024-11-23 03:50:39'),
(8, 'Haercles', 'Yigfff', 'Male', 'F/ND/23/3450059', 'Business Informatics', '2024-11-23 08:09:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year` (`year`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`student_id`,`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_academic_year` (`academic_year_id`),
  ADD KEY `fk_session` (`session_id`);

--
-- Indexes for table `overall_results`
--
ALTER TABLE `overall_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `fk_overall_academic_year` (`academic_year_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_result` (`student_id`,`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_result_academic_year` (`academic_year_id`),
  ADD KEY `fk_result_session` (`session_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matric_number` (`matric_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_registrations`
--
ALTER TABLE `course_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `overall_results`
--
ALTER TABLE `overall_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
