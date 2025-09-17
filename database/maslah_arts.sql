-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 06:45 PM
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
-- Database: `maslax_arts`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE DATABASE masool;
USE masool;

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `social_links` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `social_links`, `bio`, `skills`, `profile_picture`, `created_at`) VALUES
(1, 'maslah', 'ali', 'admin@example.com', '$2y$10$eAQr.zjkKNX55zbpSStZluUXodwVt.0bMjLsJpri1hVrVcBQrHOC2', '+252 613667595', '{\"twitter\":\"\",\"facebook\":\"\",\"linkedin\":\"\"}', '', '', 'uploads/profile_pictures/file_68c18fad0ae685.29103011.jpeg', '2025-09-03 20:09:04');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `artist_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artist_categories`
--

CREATE TABLE `artist_categories` (
  `category_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist_categories`
--

INSERT INTO `artist_categories` (`category_id`, `code`, `title`, `parent_id`) VALUES
(1, 'painting', 'Painting', NULL),
(2, 'drawing', 'Drawing', NULL),
(3, 'photography', 'Photography', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `artwork_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `gallery_type` enum('student','teacher','artist') NOT NULL DEFAULT 'artist',
  `artist_id` int(11) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artwork_id`, `title`, `description`, `image_path`, `gallery_type`, `artist_id`, `creation_date`, `uploaded_at`) VALUES
(1, 'jkk', 'kllkn', 'uploads/artworks/file_68c19afe5aaa83.22032071.jpg', 'teacher', NULL, '2025-09-10', '2025-09-10 18:36:30'),
(2, 'masla arts students', 'active student', 'uploads/artworks/file_68c1a58b81e811.15901927.jpeg', 'student', NULL, '2025-09-10', '2025-09-10 19:21:31'),
(3, 'helo', 'small arts', 'uploads/artworks/file_68c2ca57f17d49.05510390.png', 'teacher', NULL, '2025-09-11', '2025-09-11 16:10:48'),
(4, 'nkcakwdn', 'akjnv', 'uploads/artworks/file_68c2ca6ed8a625.40478531.png', 'teacher', NULL, '2025-09-11', '2025-09-11 16:11:10'),
(6, 'student', 'wekjvn', 'uploads/artworks/file_68c2cadd9d3883.60035705.png', 'student', NULL, '2025-09-18', '2025-09-11 16:13:01'),
(7, 'kjkkn', 'kjn', 'uploads/artworks/file_68c2caf602bdc9.34942183.png', 'student', NULL, '2025-09-24', '2025-09-11 16:13:26'),
(8, 'hello', 'lMASK', 'uploads/artworks/file_68c6768ef33880.07261538.png', 'teacher', NULL, '2025-09-14', '2025-09-14 11:02:23');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `blog_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `thumbnail_image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `visibility` tinyint(4) NOT NULL DEFAULT 1,
  `allow_comments` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `name`, `email`, `phone`, `message`, `submitted_at`) VALUES
(9, 'عبدالله عبدالولي ادم', 'admin@mail.com', '+111111111444', 'dkams lkmnklw', '2025-09-07 20:53:23'),
(10, 'batuulo', 'batuulo@gmail.com', '+252 38346773', 'zmlksmzlcmkd', '2025-09-07 20:53:36'),
(12, 'abdullahi', 'abdalapoi223@gmail.com', '+252 613667595', 'mmkmllmlmlml;', '2025-09-08 11:04:54'),
(14, 'abdullahi', 'abdalapoi223@gmail.com', '+252 613667595', 'hello i need to contact with you', '2025-09-10 19:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `full_description` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `level` varchar(50) NOT NULL,
  `language` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `featured` tinyint(4) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` int(11) NOT NULL DEFAULT 0,
  `duration_hours` int(11) NOT NULL DEFAULT 0,
  `cover_image` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `instructor_name` varchar(255) DEFAULT NULL,
  `instructor_image` varchar(255) DEFAULT NULL,
  `instructor_bio` text DEFAULT NULL,
  `instructor_social_facebook` varchar(255) DEFAULT NULL,
  `instructor_social_instagram` varchar(255) DEFAULT NULL,
  `instructor_social_twitter` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `title`, `short_description`, `full_description`, `category_id`, `level`, `language`, `status`, `featured`, `price`, `discount`, `duration_hours`, `cover_image`, `video_path`, `instructor_name`, `instructor_image`, `instructor_bio`, `instructor_social_facebook`, `instructor_social_instagram`, `instructor_social_twitter`, `created_at`) VALUES
(8, 'Modern Calligraphy for Beginners', 'Learn the basics of modern calligraphy', 'This comprehensive course will teach you all the fundamentals of modern calligraphy, from basic strokes to creating beautiful letterforms. Perfect for complete beginners!', 4, 'Beginner', 'English', 1, 1, 49.99, 10, 12, NULL, NULL, 'Sophia Williams', NULL, 'Professional calligrapher with 8 years of experience in modern calligraphy and hand lettering. She has worked with numerous clients on wedding invitations, corporate events, and personal projects.', NULL, NULL, NULL, '2025-09-17 10:28:40'),
(9, 'Oil Painting Masterclass', 'Advanced techniques for oil painting', 'Dive deep into the world of oil painting with this masterclass. Learn advanced techniques for color mixing, brushwork, and composition from an award-winning artist.', 8, 'Advanced', 'English', 1, 1, 89.99, 0, 24, NULL, NULL, 'Robert Chen', NULL, 'Award-winning oil painter with exhibitions worldwide. Robert has been teaching oil painting for over 15 years and specializes in landscape and portrait painting.', NULL, NULL, NULL, '2025-09-17 10:28:40'),
(10, 'Digital Painting Fundamentals', 'Learn the basics of digital art creation', 'This course covers all the essential skills needed to create stunning digital paintings. Perfect for artists transitioning from traditional to digital media.', 5, 'Beginner', 'English', 1, 1, 59.99, 15, 16, NULL, NULL, 'Emma Rodriguez', NULL, 'Digital artist and illustrator with 10+ years of experience. Emma has worked on projects for major publishers and game studios, specializing in character design and environment art.', NULL, NULL, NULL, '2025-09-17 10:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `category_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_categories`
--

INSERT INTO `course_categories` (`category_id`, `code`, `title`, `parent_id`, `icon_path`) VALUES
(1, 'DIG_ART', 'Digital Art', NULL, 'uploads/category_icons/file_68c85433014277.64971704.png'),
(2, 'TRAD_ART', 'Traditional Art', NULL, 'uploads/category_icons/file_68c85463d0b598.31551841.png'),
(3, 'PHOTO', 'Photography', NULL, 'uploads/category_icons/file_68c85459eb74d4.97108488.png'),
(4, 'CALIG', 'Calligraphy', NULL, 'uploads/category_icons/file_68c85479612435.09821894.png'),
(5, 'DIG_PAINT', 'Digital Painting', 1, 'uploads/category_icons/file_68c8543bf0c643.47417734.png'),
(6, 'VECTOR', 'Vector Art', 1, 'uploads/category_icons/file_68c8544608eb21.24924500.png'),
(7, 'WTR_CLR', 'Watercolor', 2, 'uploads/category_icons/file_68c854700aa367.83958862.png'),
(8, 'OIL', 'Oil Painting', 2, 'uploads/category_icons/file_68c8545107d076.75439225.png');

-- --------------------------------------------------------

--
-- Table structure for table `course_images`
--

CREATE TABLE `course_images` (
  `image_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_lessons`
--

CREATE TABLE `course_lessons` (
  `lesson_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `type` enum('video','pdf','text','other') DEFAULT 'video',
  `icon_class` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `video_path` varchar(500) DEFAULT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_outcomes`
--

CREATE TABLE `course_outcomes` (
  `outcome_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `icon_path` varchar(500) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_reviews`
--

CREATE TABLE `course_reviews` (
  `review_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_email` varchar(150) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `avatar_image` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(4) NOT NULL DEFAULT 1,
  `is_featured` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_sections`
--

CREATE TABLE `course_sections` (
  `section_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_similar`
--

CREATE TABLE `course_similar` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `similar_course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_upcoming` tinyint(4) NOT NULL DEFAULT 1,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `location_addr` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `banner_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `image_path`, `is_upcoming`, `start_datetime`, `end_datetime`, `location_name`, `location_addr`, `created_at`, `banner_path`) VALUES
(10, 'hello', 'wan ku salamay', 'uploads/event_images/file_68c127c705a7f1.71260611.jpeg', 1, '2025-09-05 10:27:00', '2025-09-26 10:24:00', 'km4', 'hodan', '2025-09-10 10:24:55', NULL),
(11, 'xdfsx', 'danfz', 'uploads/event_images/file_68c1316b66add6.25876470.jpeg', 1, '2025-09-04 11:07:00', '2025-09-11 11:06:00', 'afbd', 'zDFb', '2025-09-10 11:06:03', 'uploads/event_banners/file_68c1316b66fff1.79979713.jpeg'),
(14, 'hello', 'hello', 'uploads/event_images/1757510398_file_68c117d472b686.14922848.jpg', 0, '2025-09-10 16:22:00', '2025-10-07 16:21:00', 'km4', 'km4', '2025-09-10 16:19:58', NULL),
(15, 'szkdvn', 'sdv,j', 'uploads/event_images/1757519914_file_68c04f798fa6e2.43431020.png', 0, '2025-09-10 18:59:00', '2025-09-11 19:58:00', 'kaf', 'zdkgj', '2025-09-10 18:58:34', NULL),
(16, 'zvkl', 'dvzlk', 'uploads/event_images/1757519966_1757509773_file_68c117d472b686.14922848.jpg', 0, '2025-09-10 18:01:00', '2025-09-18 20:59:00', 'dsvzm', 'ksdzj', '2025-09-10 18:59:26', NULL),
(17, 's,vd', 'eklv', 'uploads/event_images/1757520016_file_68c0535954c056.29881209.png', 1, '2025-09-10 19:01:00', '2025-09-11 19:02:00', 'kdzf', 'slskdv', '2025-09-10 19:00:16', 'uploads/event_banners/1757520016_file_68c0535954c056.29881209.png'),
(18, 'Hello', 'welcome to maslah arts', 'uploads/event_images/1757521195_1757509773_file_68c117d472b686.14922848.jpg', 1, '2025-09-10 19:22:00', '2025-09-18 20:20:00', 'km4', 'km4', '2025-09-10 19:19:55', 'uploads/event_banners/1757521195_1757520016_file_68c0535954c056.29881209.png');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token`, `verification_code`, `expires_at`, `created_at`, `is_used`) VALUES
(1, 1, 'a07cd1bbb45b8f2070b12cbe09e214eb7db705ac8b14016ea779c10cdd539fd76a98e8c9b6e08f6c5614f61b331cf1b5f9d4', '781276', '2025-09-03 19:19:44', '2025-09-03 17:18:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL,
  `generated_at` datetime DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'assets/image/icon.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_image`) VALUES
(14, 'abdullahi', 'abdalapoi223@gmail.com', '$2y$10$FmU74zt8tNC7Jouxn1ZQ6efzL/BOrsgyKFz7.lX9nkoC4NDGkX5.K', '2025-09-10 17:26:38', 'uploads/users/file_68c18a9e7d8614.99605130.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workshops`
--

CREATE TABLE `workshops` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `instructor_name` varchar(100) NOT NULL,
  `instructor_role` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `difficulty` enum('BEGINNER','INTERMEDIATE','ADVANCED') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `badge_type` enum('trending','popular','limited','new','none') DEFAULT 'none',
  `status` enum('upcoming','ongoing','past') DEFAULT 'upcoming',
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshops`
--

INSERT INTO `workshops` (`id`, `title`, `description`, `instructor_name`, `instructor_role`, `category`, `difficulty`, `price`, `rating`, `badge_type`, `status`, `thumbnail_url`, `created_at`) VALUES
(11, 'Drawing Fundamentals Masterclass', 'Build a solid foundation in drawing with this comprehensive workshop covering line, form, shading, and perspective. Perfect for beginners or those looking to refine their core skills.', 'Maslah Abdi Daahir', 'Maslah Arts Founder', 'Drawing', 'INTERMEDIATE', 122.00, 5.0, 'trending', 'past', 'uploads/68c99a550806e.jpg', '2025-09-16 17:11:49'),
(12, 'hjhj', 'kjkl', 'lknkl', 'kjnk', 'Painting', 'INTERMEDIATE', 78.00, 4.0, 'new', 'ongoing', 'uploads/68c9a150285fd.jpg', '2025-09-16 17:41:36'),
(23, 'hello', 'lijhi', 'Maslah Abdi Daahir', 'Maslah Arts Founder', 'Painting', 'BEGINNER', 56.00, 4.0, 'popular', 'upcoming', 'uploads/68c9a8fda013a.jpg', '2025-09-16 18:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `workshops_request`
--

CREATE TABLE `workshops_request` (
  `workshop_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `skill_level` varchar(150) NOT NULL,
  `workshop_name` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshops_request`
--

INSERT INTO `workshops_request` (`workshop_id`, `name`, `email`, `phone`, `skill_level`, `workshop_name`, `message`, `requested_at`) VALUES
(31, 'عبدالله عبدالولي ادم', 'batuulo@gmail.com', '+111111111', 'Beginner', 'Expressive Watercolor Techniques', 'hoiuhohioo', '2025-09-07 10:37:18'),
(32, 'batuulo', 'batuulo@gmail.com', '+252 613667595', 'Intermediate', 'Drawing Fundamentals Masterclass', 'Asc', '2025-09-07 10:37:45'),
(33, 'ali', 'admin@example.com', '+111111111', 'Intermediate', 'Drawing Fundamentals Masterclass', 'hello world', '2025-09-07 10:38:30'),
(34, 'batuulo', 'abdalapoi223@gmail.com', '+252 613667595', 'Beginner', 'Expressive Watercolor Techniques', 'nscksnclcmecl;evm', '2025-09-07 17:39:09'),
(37, 'abdullahi', 'abdalapoi223@gmail.com', '+252 613667595', 'Beginner', 'Drawing Fundamentals Masterclass', 'asxasxxwe', '2025-09-16 18:42:27'),
(38, 'عبدالله عبدالولي ادم', 'abdalapoi223@gmail.com', '+252 613667595', 'Intermediate', 'Expressive Watercolor Techniques', ',mzn', '2025-09-16 18:50:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`artist_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `artist_categories`
--
ALTER TABLE `artist_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artwork_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blog_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `idx_courses_category` (`category_id`),
  ADD KEY `idx_courses_status` (`status`),
  ADD KEY `idx_courses_featured` (`featured`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `course_images`
--
ALTER TABLE `course_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `idx_lessons_section` (`section_id`);

--
-- Indexes for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  ADD PRIMARY KEY (`outcome_id`),
  ADD KEY `idx_outcomes_course` (`course_id`);

--
-- Indexes for table `course_reviews`
--
ALTER TABLE `course_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_course_rating` (`course_id`,`rating`),
  ADD KEY `idx_approved_featured` (`is_approved`,`is_featured`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `idx_sections_course` (`course_id`);

--
-- Indexes for table `course_similar`
--
ALTER TABLE `course_similar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_similar_relation` (`course_id`,`similar_course_id`),
  ADD KEY `idx_similar_course` (`course_id`),
  ADD KEY `idx_similar_similar` (`similar_course_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_index` (`user_id`),
  ADD KEY `token_index` (`token`),
  ADD KEY `expires_at_index` (`expires_at`),
  ADD KEY `idx_is_used` (`is_used`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `workshops`
--
ALTER TABLE `workshops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workshops_request`
--
ALTER TABLE `workshops_request`
  ADD PRIMARY KEY (`workshop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `artist_categories`
--
ALTER TABLE `artist_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artwork_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_images`
--
ALTER TABLE `course_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  MODIFY `outcome_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `course_reviews`
--
ALTER TABLE `course_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_sections`
--
ALTER TABLE `course_sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `course_similar`
--
ALTER TABLE `course_similar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workshops`
--
ALTER TABLE `workshops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `workshops_request`
--
ALTER TABLE `workshops_request`
  MODIFY `workshop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artists`
--
ALTER TABLE `artists`
  ADD CONSTRAINT `artists_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `artist_categories` (`category_id`);

--
-- Constraints for table `artist_categories`
--
ALTER TABLE `artist_categories`
  ADD CONSTRAINT `artist_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `artist_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`artist_id`) ON DELETE SET NULL;

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD CONSTRAINT `course_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `course_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `course_images`
--
ALTER TABLE `course_images`
  ADD CONSTRAINT `course_images_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`section_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  ADD CONSTRAINT `course_outcomes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_reviews`
--
ALTER TABLE `course_reviews`
  ADD CONSTRAINT `course_reviews_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD CONSTRAINT `course_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_similar`
--
ALTER TABLE `course_similar`
  ADD CONSTRAINT `course_similar_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_similar_ibfk_2` FOREIGN KEY (`similar_course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
