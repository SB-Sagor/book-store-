-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2025 at 11:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `authentication`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(1, 'admin@gmail.com', '$2y$10$ZQ3enlf0SbehIzcZoEKBgOlL17jCHNbbFqCRc.hxI.VyWRgEBFCp6');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`) VALUES
(16, 'Humayun Ahmed'),
(36, 'A. Rahman'),
(37, 'S. Haque'),
(38, 'M. Karim	'),
(39, 'MICHAEL OMOYIBO '),
(40, 'a');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isbn` varchar(20) DEFAULT NULL,
  `isbn_raw` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `author_id`, `category_id`, `cover`, `file`, `created_at`, `isbn`, `isbn_raw`) VALUES
(77, 'Competitive Coding Mastery', 'Learn advanced problem-solving techniques for contests like Codeforces.', 37, 46, 'admin/uploads/covers/6891948738791_cp.jpg', 'admin/uploads/books/689194873879b_book.pdf', '2025-08-05 05:20:07', '9780987654321', '978-0-987654-32-1'),
(78, 'Embedded Systems Unlocked', 'Linux® is being adopted by an increasing number of embedded systems developers, who have been won over by its sophisticated scheduling and networking, its cost-free license, its open development model, and the support offered by rich and powerful programming tools. While there is a great deal of hype surrounding the use of Linux in embedded systems, there is not a lot of practical information. Building Embedded Linux Systems is the first in-depth, hard-core guide to putting together an embedded system based on the Linux kernel. ', 38, 47, 'admin/uploads/covers/6891956635b05_51hiep-B0mL._SY445_SX342_.jpg', 'admin/uploads/books/6891956635b09_esys.ir_Building.Embedded.Linux.Systems.2nd.Edition.pdf', '2025-08-05 05:23:50', '9781111111111', '978-1-111111-11-1'),
(79, 'The IoT Revolution in Healthcare', 'The evolution of healthcare technologies has undergone significant transformations over the past few decades, driven by advancements in digital innovations and the increasing integration of IoT (Internet of Things) technologies. Initially, healthcare was dominated by traditional practices that relied heavily on face-to-face consultations and paper-based record-keeping. As the digital age emerged, the introduction of electronic health records (EHRs) marked a pivotal point, allowing for improved data management and accessibility. This shift streamlined administrative processes and laid the groundwork for more sophisticated healthcare technologies, including body sensor networks and wearable devices.', 39, 45, 'admin/uploads/covers/6891961c3e293_71vo01WrrdL._SY522_.jpg', 'admin/uploads/books/6891961c3e297_IoT-Based-Biometric-Attendance-System-using-Fingerprint-Sensor.pdf', '2025-08-05 05:26:52', '9798314577141', '9798314577141');

-- --------------------------------------------------------

--
-- Table structure for table `book_replies`
--

CREATE TABLE `book_replies` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `reply_details` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_requests`
--

CREATE TABLE `book_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_title` varchar(255) NOT NULL,
  `request_details` text DEFAULT NULL,
  `status` enum('pending','fulfilled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isbn` varchar(20) DEFAULT NULL,
  `isbn_raw` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_requests`
--

INSERT INTO `book_requests` (`id`, `user_id`, `book_title`, `request_details`, `status`, `created_at`, `isbn`, `isbn_raw`) VALUES
(21, 7, 'Introduction to algorithms', 'by Thomas H Cormen (Author), Charles E Leiserson (Author).\r\nThe updated new edition of the classic Introduction to Algorithms is intended primarily for use in undergraduate or graduate courses in algorithms or data structures. Like the first edition, this text can also be used for self-study by technical professionals since it discusses engineering issues in algorithm design as well as the mathematical aspects.\r\nIn its new edition, Introduction to Algorithms continues to provide a comprehensive introduction to the modern study of algorithms. The revision has been updated to reflect changes in the years since the book\'s original publication.', 'pending', '2025-08-05 06:44:41', '0070131511', '0070131511');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(16, 'Database Management System'),
(30, 'cse'),
(31, 'kobita'),
(45, 'Technology'),
(46, 'Programming'),
(47, 'Engineering'),
(48, 'a');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `coins` int(11) DEFAULT 10,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `coins`, `avatar`) VALUES
(7, 'Sagor Hassan', 'sheikhsagor725@gmail.com', '$2y$10$Nwg9y5SCHAnqCDM204hjeuXsobsQP1Sruu5GOB6xubN4Vrwlb8hpu', 10, 'default-avatar.png'),
(8, 'd', 'd@example.com', '$2y$10$.rpVnHPRwTNUsm3qjaguLuX3kOb6uY56rpMsW6lN2/RHs.WNGkpQi', 10, 'default-avatar.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `book_replies`
--
ALTER TABLE `book_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `book_requests`
--
ALTER TABLE `book_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `book_replies`
--
ALTER TABLE `book_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_requests`
--
ALTER TABLE `book_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_replies`
--
ALTER TABLE `book_replies`
  ADD CONSTRAINT `book_replies_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `book_requests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
