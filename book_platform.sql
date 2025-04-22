-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 05:56 PM
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
-- Database: `book_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `file_path`, `uploaded_by`, `cover_image`) VALUES
(15, 'dbms', 'sagor', 'cse', 'uploads/authentication.sql', 1, NULL);

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

--
-- Dumping data for table `book_replies`
--

INSERT INTO `book_replies` (`id`, `request_id`, `reply_details`, `timestamp`, `file_path`) VALUES
(26, 19, 'The book is described by its publisher as \"the leading algorithms text in universities worldwide as well as the standard reference for professionals\".', '2025-02-08 05:28:50', 'replies/Book Recommantation.pdf');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_requests`
--

INSERT INTO `book_requests` (`id`, `user_id`, `book_title`, `request_details`, `status`, `created_at`) VALUES
(19, 1, 'Introduction to Algorithms', 'To improve myself\r\n', 'pending', '2025-02-08 05:28:31'),
(20, 4, 'mathematics', 'i like the book', 'pending', '2025-02-09 18:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'sagor', 'sheikhsagor725@gmail.com', '$2y$10$JGGDCZiV1dq.v5pnWiVTgOuIKIB1lOnda.5vQ7f4hj3ItoVNUY4my'),
(2, 'sagor', 'sagorhassansb@gmail.com', '$2y$10$Hm/HPbNHpbFs7b.FCW1Gz.PjCjBQ4C4aWyXvQzu4UKa1n2nrM5qUO'),
(3, 'admin', 'admin@gmail.com', '$2y$10$smc7PeAteHB7pfN.uawExu8b2Ss9o7p2KYWacMQYZKr4unZE04WS2'),
(4, 'sagor', 'sbenhaid@kent.edu', '$2y$10$vBSKUERlXQ1429cdBn8cbe4ydMz6.k/x4IUGf5WyjUudjGm8WgmSG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `book_replies`
--
ALTER TABLE `book_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `book_requests`
--
ALTER TABLE `book_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `book_replies`
--
ALTER TABLE `book_replies`
  ADD CONSTRAINT `book_replies_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `book_requests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
