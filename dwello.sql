-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 22, 2026 at 12:43 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dwello`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-test@example.com|127.0.0.1', 'i:1;', 1769006541),
('laravel-cache-test@example.com|127.0.0.1:timer', 'i:1769006541;', 1769006541);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('property','roommate') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `blocked_by` bigint UNSIGNED DEFAULT NULL,
  `property_id` bigint UNSIGNED DEFAULT NULL,
  `started_by` bigint UNSIGNED DEFAULT NULL,
  `user_one_id` bigint UNSIGNED NOT NULL,
  `user_two_id` bigint UNSIGNED NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `type`, `status`, `blocked_by`, `property_id`, `started_by`, `user_one_id`, `user_two_id`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, 'roommate', 'accepted', NULL, NULL, NULL, 1, 2, '2026-01-20 18:36:48', '2025-12-21 00:57:26', '2026-01-20 18:36:48'),
(2, 'roommate', 'pending', NULL, NULL, NULL, 1, 7, '2025-12-21 01:13:48', '2025-12-21 00:57:57', '2025-12-21 01:13:48'),
(3, 'roommate', 'pending', NULL, NULL, NULL, 1, 18, '2025-12-21 01:04:53', '2025-12-21 00:58:17', '2025-12-21 01:04:53'),
(4, 'property', 'pending', NULL, 4, NULL, 1, 23, '2025-12-24 01:22:37', '2025-12-22 23:05:32', '2025-12-24 01:22:37'),
(5, 'property', 'accepted', NULL, 3, NULL, 1, 23, '2025-12-30 09:44:59', '2025-12-22 23:24:42', '2026-01-20 15:34:12'),
(6, 'property', 'pending', NULL, 1, NULL, 1, 23, NULL, '2025-12-24 01:32:47', '2025-12-24 01:32:47'),
(7, 'property', 'pending', NULL, 7, NULL, 1, 25, '2025-12-24 04:32:39', '2025-12-24 04:32:39', '2025-12-24 04:32:39'),
(8, 'property', 'pending', NULL, 8, NULL, 1, 25, '2025-12-29 12:18:45', '2025-12-24 05:09:21', '2025-12-29 12:18:45'),
(9, 'roommate', 'pending', NULL, NULL, NULL, 1, 6, '2025-12-24 05:39:02', '2025-12-24 05:39:02', '2025-12-24 05:39:02'),
(10, 'roommate', 'pending', NULL, NULL, NULL, 1, 12, '2025-12-29 12:18:20', '2025-12-24 05:44:03', '2025-12-29 12:18:20'),
(11, 'roommate', 'pending', NULL, NULL, NULL, 1, 14, '2025-12-29 10:28:17', '2025-12-24 05:44:12', '2025-12-29 10:28:17'),
(12, 'roommate', 'pending', NULL, NULL, 1, 1, 21, '2026-01-18 14:20:11', '2026-01-18 14:20:00', '2026-01-18 14:20:11'),
(18, 'property', 'pending', NULL, 9, 1, 1, 25, '2026-01-20 15:46:58', '2026-01-20 15:46:58', '2026-01-20 15:46:58'),
(19, 'property', 'pending', NULL, 6, 1, 1, 25, '2026-01-20 15:47:13', '2026-01-20 15:47:13', '2026-01-20 15:47:13'),
(20, 'property', 'pending', NULL, 13, 1, 1, 23, '2026-01-20 15:49:35', '2026-01-20 15:49:35', '2026-01-20 15:49:35'),
(21, 'roommate', 'accepted', NULL, NULL, 1, 1, 23, '2026-01-20 18:36:18', '2026-01-20 16:02:11', '2026-01-20 18:36:18'),
(22, 'roommate', 'pending', NULL, NULL, 1, 1, 16, '2026-01-21 14:04:45', '2026-01-21 14:04:45', '2026-01-21 14:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `roommate_profile_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` bigint UNSIGNED DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `property_id`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'hi', NULL, NULL, '2025-12-21 01:04:53', '2025-12-21 01:04:53'),
(2, 2, 1, 'hi', NULL, NULL, '2025-12-21 01:13:48', '2025-12-21 01:13:48'),
(3, 4, 1, 'hi', NULL, '2025-12-24 01:21:28', '2025-12-22 23:05:39', '2025-12-24 01:21:28'),
(4, 4, 1, 'how', NULL, '2025-12-24 01:21:28', '2025-12-22 23:05:45', '2025-12-24 01:21:28'),
(5, 4, 23, 'hi bro', NULL, '2025-12-24 01:16:50', '2025-12-22 23:07:59', '2025-12-24 01:16:50'),
(6, 5, 23, 'hi bro', NULL, '2025-12-24 01:16:44', '2025-12-24 00:57:01', '2025-12-24 01:16:44'),
(7, 4, 23, 'helloo', NULL, '2025-12-24 01:22:34', '2025-12-24 01:21:44', '2025-12-24 01:22:34'),
(8, 4, 1, 'hi', NULL, '2026-01-20 15:32:51', '2025-12-24 01:22:37', '2026-01-20 15:32:51'),
(9, 11, 1, 'hi', NULL, NULL, '2025-12-29 10:28:17', '2025-12-29 10:28:17'),
(10, 10, 1, 'Hey there! I’m really interested in the property and wanted to reach out to discuss a few details before moving forward. I work full-time during the week and usually keep a pretty regular routine, so I value clear communication and a comfortable living environment. I don’t mind shared spaces and I’m flexible with schedules, but I do prefer things to be respectful and organized. Let me know if the room is still available, what the move-in timeline looks like, and whether there’s a possibility to visit the place sometime this week. Looking forward to hearing from you soon!', NULL, NULL, '2025-12-29 10:31:30', '2025-12-29 10:31:30'),
(11, 10, 1, 'Hey there! I’m really interested in the property and wanted to reach out to discuss a few details before moving forward. I work full-time during the week and usually keep a pretty regular routine, so I value clear communication and a comfortable living environment. I don’t mind shared spaces and I’m flexible with schedules, but I do prefer things to be respectful and organized. Let me know if the room is still available, what the move-in timeline looks like, and whether there’s a possibility to visit the place sometime this week. Looking forward to hearing from you soon!', NULL, NULL, '2025-12-29 10:34:35', '2025-12-29 10:34:35'),
(12, 10, 1, '🌾🪔 The spirit of Pongal is loading… 🪔🌾\n\nA celebration of tradition, togetherness, and joy 💛✨\n\nGet ready for something special as we bring you\nKaanum Pongal 2026 — Coming Soon 👀🔥\n\nStay tuned for the vibes, the culture, and the celebration! 🥁🛕\n\n\n#Rotaract #RID3220 #RotaractMatale #UniteForGood\n#ConnectingLives #ClubService\n#KaanumPongal2026 #PongalVibes #PongalCelebrations #TamilCulture', NULL, NULL, '2025-12-29 10:34:57', '2025-12-29 10:34:57'),
(13, 10, 1, '🌾🪔 The spirit of Pongal is loading… 🪔🌾\n\nA celebration of tradition, togetherness, and joy 💛✨\n\nGet ready for something special as we bring you\nKaanum Pongal 2026 — Coming Soon 👀🔥\n\nStay tuned for the vibes, the culture, and the celebration! 🥁🛕\n\n\n#Rotaract #RID3220 #RotaractMatale #UniteForGood\n#ConnectingLives #ClubService\n#KaanumPongal2026 #PongalVibes #PongalCelebrations #TamilCulture', NULL, NULL, '2025-12-29 10:37:35', '2025-12-29 10:37:35'),
(14, 10, 1, '🌾🪔 The spirit of Pongal is loading… 🪔🌾\n\nA celebration of tradition, togetherness, and joy 💛✨\n\nGet ready for something special as we bring you\nKaanum Pongal 2026 — Coming Soon 👀🔥\n\nStay tuned for the vibes, the culture, and the celebration! 🥁🛕\n\n\n#Rotaract #RID3220 #RotaractMatale #UniteForGood\n#ConnectingLives #ClubService\n#KaanumPongal2026 #PongalVibes #PongalCelebrations #TamilCulture', NULL, NULL, '2025-12-29 10:38:37', '2025-12-29 10:38:37'),
(15, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:09:42', '2025-12-29 12:09:42'),
(16, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:10:27', '2025-12-29 12:10:27'),
(17, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:15:09', '2025-12-29 12:15:09'),
(18, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:15:23', '2025-12-29 12:15:23'),
(19, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:18:17', '2025-12-29 12:18:17'),
(20, 10, 1, 'hi', NULL, NULL, '2025-12-29 12:18:20', '2025-12-29 12:18:20'),
(21, 8, 1, 'hi', NULL, NULL, '2025-12-29 12:18:45', '2025-12-29 12:18:45'),
(22, 1, 1, 'hi sai', NULL, '2026-01-19 03:22:43', '2025-12-29 12:37:42', '2026-01-19 03:22:43'),
(23, 1, 1, 'hi', NULL, '2026-01-19 03:22:43', '2025-12-29 18:09:59', '2026-01-19 03:22:43'),
(24, 1, 1, 'hi bro', NULL, '2026-01-19 03:22:43', '2025-12-29 18:11:27', '2026-01-19 03:22:43'),
(25, 1, 1, 'Hey there! I’m really interested in the property and wanted to reach out to discuss a few details before moving forward. I work full-time during the week and usually keep a pretty regular routine, so I value clear communication and a comfortable living environment. I don’t mind shared spaces and I’m flexible with schedules, but I do prefer things to be respectful and organized. Let me know if the room is still available, what the move-in timeline looks like, and whether there’s a possibility to visit the place sometime this week. Looking forward to hearing from you soon!', NULL, '2026-01-19 03:22:43', '2025-12-30 09:42:05', '2026-01-19 03:22:43'),
(26, 1, 1, 'Hey there! I’m really interested in the property and wanted to reach out to discuss a few details before moving forward. I work full-time during the week and usually keep a pretty regular routine, so I value clear communication and a comfortable living environment. I don’t mind shared spaces and I’m flexible with schedules, but I do prefer things to be respectful and organized. Let me know if the room is still available, what the move-in timeline looks like, and whether there’s a possibility to visit the place sometime this week. Looking forward to hearing from you soon!', NULL, '2026-01-19 03:22:43', '2025-12-30 09:42:16', '2026-01-19 03:22:43'),
(27, 1, 1, 'Hey there! I’m really interested in the property and wanted to reach out to discuss a few details before moving forward. I work full-time during the week and usually keep a pretty regular routine, so I value clear communication and a comfortable living environment. I don’t mind shared spaces and I’m flexible with schedules, but I do prefer things to be respectful and organized. Let me know if the room is still available, what the move-in timeline looks like, and whether there’s a possibility to visit the place sometime this week. Looking forward to hearing from you soon!', NULL, '2026-01-19 03:22:43', '2025-12-30 09:42:32', '2026-01-19 03:22:43'),
(28, 1, 1, 'my email avi', NULL, '2026-01-19 03:22:43', '2025-12-30 09:43:18', '2026-01-19 03:22:43'),
(29, 1, 1, 'my email is avi@gmail.com', NULL, '2026-01-19 03:22:43', '2025-12-30 09:43:34', '2026-01-19 03:22:43'),
(30, 5, 1, 'HI BRO', NULL, '2026-01-19 03:24:18', '2025-12-30 09:44:59', '2026-01-19 03:24:18'),
(31, 1, 1, 'AVI@GMAIL.COM', NULL, '2026-01-19 03:22:43', '2025-12-30 09:50:44', '2026-01-19 03:22:43'),
(32, 12, 1, 'can we rent room together', NULL, NULL, '2026-01-18 14:20:11', '2026-01-18 14:20:11'),
(50, 21, 23, 'hi', NULL, '2026-01-20 18:36:15', '2026-01-20 18:35:36', '2026-01-20 18:36:15'),
(51, 21, 1, 'hi', NULL, NULL, '2026-01-20 18:36:18', '2026-01-20 18:36:18'),
(52, 1, 1, 'Shared a property: Luxury Room with AC', 8, NULL, '2026-01-20 18:36:48', '2026-01-20 18:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_22_063603_create_properties_table', 1),
(5, '2025_11_22_064431_create_roommate_profiles_table', 1),
(6, '2025_12_17_090334_add_compatibility_fields_to_roommate_profiles', 2),
(7, '2025_12_21_061655_create_conversations_table', 3),
(8, '2025_12_21_061657_create_messages_table', 3),
(9, '2025_12_21_064130_create_favorites_table', 4),
(10, '2025_12_23_040328_add_role_to_users_table', 5),
(11, '2025_12_23_074954_create_property_photos_table', 6),
(12, '2025_12_24_070034_create_reviews_table', 7),
(13, '2025_12_24_075242_add_indexes_for_performance', 8),
(14, '2025_12_29_154623_create_rejected_matches_table', 9),
(15, '2026_01_18_194346_add_status_and_started_by_to_conversations_table', 10),
(16, '2026_01_18_201950_add_blocked_by_to_conversations_table', 11),
(17, '2026_01_18_211351_add_property_id_to_messages_table', 12),
(18, '2026_01_18_213505_add_property_type_to_roommate_profiles', 13),
(19, '2026_01_19_074239_add_profile_fields_to_users_table', 14);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `bedrooms` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `bathrooms` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `property_type` enum('room','apartment','house') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'room',
  `available_from` date DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `user_id`, `title`, `description`, `city`, `address`, `monthly_rent`, `bedrooms`, `bathrooms`, `property_type`, `available_from`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 25, 'Affordable City Apartment', 'Great value 2-bedroom apartment in a convenient location. \n\nFeatures:\n- Close to public transport\n- Secure building\n- Basic furnishings included\n- Ideal for small families or working professionals\n\nWalking distance to markets and shops.', 'Colombo', 'Vauxhall Street, Colombo 02', 65000.00, 2, 1, 'apartment', '2025-12-29', 6.9271000, 79.8471000, '2025-12-24 05:12:06', '2025-12-24 05:12:06'),
(2, 25, 'Cozy Family House', 'Charming single-story house in a quiet neighborhood. \n\nIncludes:\n- Small private garden\n- 2 Spacious bedrooms\n- Kitchen with pantry\n- Parking for 1 vehicle\n\nPerfect for a peaceful lifestyle reasonably close to the city.', 'Nugegoda', 'Chapel Road, Nugegoda', 45000.00, 2, 1, 'house', '2025-12-24', 6.8742000, 79.8906000, '2025-12-24 05:12:07', '2025-12-24 05:12:07'),
(3, 25, 'Student Room near Campus', 'Single room available for male student. \n\nFeatures:\n- Walking distance to SLIIT/CINEC\n- Desk and chair provided\n- Shared bathroom\n- All bills included in rent\n\nBudget friendly choice for focused students.', 'Malabe', 'New Kandy Road, Malabe', 15000.00, 1, 1, 'room', '2025-12-31', 6.9041000, 79.9547000, '2025-12-24 05:12:08', '2025-12-24 05:12:08'),
(4, 25, 'Compact Annex for Rent', 'Separate entrance annex suitable for a couple or single person. \n\nFeatures:\n- 1 Bedroom with attached bath\n- Small kitchenette area\n- Key money 3 months\n- Quiet residential area\n\nVery affordable.', 'Battaramulla', 'Koswatta, Battaramulla', 25000.00, 1, 1, 'room', '2026-01-24', 6.8906000, 79.9238000, '2025-12-24 05:12:09', '2025-12-24 05:12:09'),
(5, 25, 'Modern Apartment Dehiwala', 'Newly painted 2 bedroom apartment on the 3rd floor. \n\n- Sea breeze and good ventilation\n- Tiled floors\n- Close to Galle Road\n- Secure parking\n\nGreat balance of comfort and price.', 'Dehiwala', 'Hill Street, Dehiwala', 55000.00, 2, 1, 'apartment', '2025-12-26', 6.8511000, 79.8659000, '2025-12-24 05:12:10', '2025-12-24 05:12:10'),
(6, 25, 'Shared Room for Working Girls', 'Bed space available in a large room shared with 2 others. \n\n- Safe environment\n- Cooking facilities available\n- Close to garment factories\n- Water and electricity shared\n\nSuper budget option.', 'Ratmalana', 'Borupana Road, Ratmalana', 8000.00, 1, 1, 'room', '2025-12-24', 6.8195000, 79.8837000, '2025-12-24 05:12:11', '2025-12-24 05:12:11'),
(7, 25, 'Spacious House in Kottawa', '3 Bedroom house with large hall and dining area. \n\n- 15 mins to Highway entrance\n- Well water and tap line\n- Pet friendly\n- Fenced land\n\nIdeal for a large family on a budget.', 'Kottawa', 'High Level Road, Kottawa', 35000.00, 3, 2, 'house', '2026-01-03', 6.8412000, 79.9654000, '2025-12-24 05:12:11', '2025-12-24 05:12:11'),
(8, 25, 'Luxury Room with AC', 'Private room with AC and attached bathroom in a luxury house. \n\n- Furnished with queen bed and wardrobe\n- Hot water\n- Parking available\n- Meals can be arranged\n\nPremium comfort for a single executive.', 'Colombo', 'Thimbirigasyaya, Colombo 05', 40000.00, 1, 1, 'room', '2025-12-24', 6.8969000, 79.8686000, '2025-12-24 05:12:13', '2025-12-24 05:12:13'),
(9, 25, 'Furnished Flat in Wellawatte', '2 Bedroom flat available for long term rent. \n\n- Marine Drive view\n- Fully tiled\n- 2nd Floor (No Lift)\n- 5 mins to market/railway station\n\nReasonable rent for the area.', 'Wellawatte', 'Frances Road, Colombo 06', 85000.00, 2, 2, 'apartment', '2025-12-29', 6.8744000, 79.8601000, '2025-12-24 05:12:13', '2025-12-24 05:12:13'),
(13, 23, 'littile house', 'new building', 'Colombo', '102', 150000.00, 1, 1, 'room', '2026-01-30', 6.8886895, 79.8701233, '2026-01-20 15:49:10', '2026-01-20 15:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `property_photos`
--

CREATE TABLE `property_photos` (
  `id` bigint UNSIGNED NOT NULL,
  `property_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_photos`
--

INSERT INTO `property_photos` (`id`, `property_id`, `path`, `created_at`, `updated_at`) VALUES
(1, 1, 'properties/demo/demo_1_0_4df3ad464c752de4a7595737f63ada95.jpg', '2025-12-24 05:12:06', '2025-12-24 05:12:06'),
(2, 1, 'properties/demo/demo_1_1_cb4be4ce09bf49335bc37570236274e8.jpg', '2025-12-24 05:12:07', '2025-12-24 05:12:07'),
(3, 2, 'properties/demo/demo_2_0_1ab31cf4412b868f8a6d88197611e3d3.jpg', '2025-12-24 05:12:07', '2025-12-24 05:12:07'),
(4, 2, 'properties/demo/demo_2_1_4102238e5aab54ec22b0956b72e18014.jpg', '2025-12-24 05:12:08', '2025-12-24 05:12:08'),
(5, 3, 'properties/demo/demo_3_0_213239fee959685624db9fee07ba96fd.jpg', '2025-12-24 05:12:08', '2025-12-24 05:12:08'),
(6, 3, 'properties/demo/demo_3_1_e6b61bc715581902a0554ed74d740448.jpg', '2025-12-24 05:12:09', '2025-12-24 05:12:09'),
(7, 4, 'properties/demo/demo_4_0_4ef0a68dd72f46c3d49726200c25fb52.jpg', '2025-12-24 05:12:09', '2025-12-24 05:12:09'),
(8, 5, 'properties/demo/demo_5_0_f04e4f5957093d95f9fd120337204191.jpg', '2025-12-24 05:12:10', '2025-12-24 05:12:10'),
(9, 5, 'properties/demo/demo_5_1_cb61e872d6b772fc87c56220413d0d06.jpg', '2025-12-24 05:12:11', '2025-12-24 05:12:11'),
(10, 7, 'properties/demo/demo_7_0_997317fa724ba9e716fc6915a18beb0d.jpg', '2025-12-24 05:12:12', '2025-12-24 05:12:12'),
(11, 8, 'properties/demo/demo_8_0_37bc9afddc21a4d5be7213753cdae93c.jpg', '2025-12-24 05:12:13', '2025-12-24 05:12:13'),
(12, 9, 'properties/demo/demo_9_0_4df3ad464c752de4a7595737f63ada95.jpg', '2025-12-24 05:12:14', '2025-12-24 05:12:14'),
(13, 9, 'properties/demo/demo_9_1_f04e4f5957093d95f9fd120337204191.jpg', '2025-12-24 05:12:14', '2025-12-24 05:12:14'),
(16, 13, 'properties/13/BTAiaZaAXLLqDb7B5X7K4zltHEAeMdufLsvFqlAR.png', '2026-01-20 15:49:10', '2026-01-20 15:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `rejected_matches`
--

CREATE TABLE `rejected_matches` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `rejected_user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rejected_matches`
--

INSERT INTO `rejected_matches` (`id`, `user_id`, `rejected_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 14, '2025-12-29 12:36:27', '2025-12-29 12:36:27'),
(2, 1, 21, '2025-12-30 09:46:23', '2025-12-30 09:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `property_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `property_id`, `user_id`, `rating`, `comment`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, 'Great place, test review.', 'approved', '2025-12-24 01:32:47', '2025-12-24 01:32:47'),
(2, 4, 1, 4, 'very nice place', 'rejected', '2025-12-24 01:36:45', '2025-12-24 02:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `roommate_profiles`
--

CREATE TABLE `roommate_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` tinyint UNSIGNED DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_min` decimal(10,2) DEFAULT NULL,
  `budget_max` decimal(10,2) DEFAULT NULL,
  `preferred_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_property_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `is_smoker` tinyint(1) NOT NULL DEFAULT '0',
  `has_pets` tinyint(1) NOT NULL DEFAULT '0',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pref_no_smoker` tinyint(1) NOT NULL DEFAULT '0',
  `pref_pets_ok` tinyint(1) NOT NULL DEFAULT '1',
  `pref_same_gender_only` tinyint(1) NOT NULL DEFAULT '0',
  `pref_visitors_ok` tinyint(1) NOT NULL DEFAULT '1',
  `pref_substance_free_required` tinyint(1) NOT NULL DEFAULT '0',
  `uses_substances` tinyint(1) NOT NULL DEFAULT '0',
  `cleanliness` tinyint UNSIGNED DEFAULT NULL,
  `noise_tolerance` tinyint UNSIGNED DEFAULT NULL,
  `sleep_schedule` tinyint UNSIGNED DEFAULT NULL,
  `study_focus` tinyint UNSIGNED DEFAULT NULL,
  `social_level` tinyint UNSIGNED DEFAULT NULL,
  `schedule_type` enum('morning','night','mixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation_field` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roommate_profiles`
--

INSERT INTO `roommate_profiles` (`id`, `user_id`, `display_name`, `age`, `gender`, `budget_min`, `budget_max`, `preferred_city`, `preferred_property_type`, `preferred_location`, `move_in_date`, `is_smoker`, `has_pets`, `bio`, `created_at`, `updated_at`, `pref_no_smoker`, `pref_pets_ok`, `pref_same_gender_only`, `pref_visitors_ok`, `pref_substance_free_required`, `uses_substances`, `cleanliness`, `noise_tolerance`, `sleep_schedule`, `study_focus`, `social_level`, `schedule_type`, `occupation_field`) VALUES
(1, 1, 'avithrantest', 22, 'male', 10000.00, 30000.00, NULL, NULL, 'Dehiwala', '2026-01-31', 0, 1, 'im a student', '2025-12-17 03:47:18', '2026-01-19 03:22:01', 0, 1, 1, 1, 0, 0, 3, 3, 3, 3, 1, 'night', 'student'),
(2, 2, 'Sai', 22, 'male', 15000.00, 20000.00, 'colombo', NULL, 'dehiwala', '2025-12-25', 0, 0, 'hi im bad', '2025-12-17 03:51:41', '2025-12-17 03:51:41', 0, 1, 0, 1, 0, 0, 2, 4, 4, 3, 4, 'night', 'student'),
(3, 3, 'Fidel Osinski I', 24, 'male', 15000.00, 54000.00, 'Maligawatta', NULL, 'Maligawatta', '2026-02-02', 0, 0, 'Soup? Pennyworth only of beautiful Soup? Beau--ootiful Soo--oop! Soo--oop of the way I want to get.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 1, 0, 0, 1, 0, 1, 3, 3, 2, 4, 5, 'night', 'Cement Mason and Concrete Finisher'),
(4, 4, 'Ms. Karolann Koch', 28, 'female', 16000.00, 44000.00, 'Wellawatte', NULL, 'Wellawatte', '2026-02-13', 0, 0, 'And how odd the directions will look! ALICE\'S RIGHT FOOT, ESQ. HEARTHRUG, NEAR THE FENDER, (WITH.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 0, 0, 1, 1, 0, 0, 2, 1, 5, 1, 3, 'morning', 'Special Force'),
(5, 5, 'Octavia Carroll', 33, 'female', 28000.00, 53000.00, 'Wellawatte', NULL, 'Wellawatte', '2026-01-04', 0, 0, 'NOT!\' cried the Mouse, sharply and very soon found an opportunity of showing off a little startled.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 1, 0, 0, 1, 1, 0, 4, 2, 4, 3, 2, 'morning', 'Aircraft Engine Specialist'),
(6, 6, 'Mr. Gino Dach', 31, 'male', 16000.00, 52000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-01-12', 0, 0, 'But I\'ve got to the whiting,\' said Alice, feeling very curious to see if there were any tears. No.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 0, 1, 1, 0, 0, 0, 4, 4, 2, 4, 2, 'morning', 'Packer and Packager'),
(7, 7, 'Jarrett Bernier', 32, 'male', 19000.00, 38000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-02-14', 1, 0, 'YET,\' she said to herself, \'in my going out altogether, like a telescope.\' And so it was perfectly.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 0, 0, 0, 1, 0, 0, 3, 3, 5, 3, 5, 'night', 'Aircraft Structure Assemblers'),
(8, 8, 'Briana Marquardt', 32, 'female', 20000.00, 37000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-01-13', 1, 0, 'I\'ll tell you his history,\' As they walked off together. Alice laughed so much into the wood to.', '2025-12-20 00:12:35', '2025-12-20 00:12:35', 1, 1, 1, 1, 0, 1, 3, 1, 2, 5, 2, 'morning', 'Annealing Machine Operator'),
(9, 9, 'Mrs. Eryn Nitzsche', 27, 'female', 29000.00, 54000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-02-08', 0, 0, 'I should have liked teaching it tricks very much, if--if I\'d only been the right thing to.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 1, 0, 0, 1, 0, 0, 2, 3, 5, 3, 3, 'morning', 'Letterpress Setters Operator'),
(10, 10, 'Rosalia Renner', 27, 'female', 22000.00, 42000.00, 'Wellawatte', NULL, 'Wellawatte', '2026-01-18', 1, 0, 'Please, Ma\'am, is this New Zealand or Australia?\' (and she tried to say to this: so she tried.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 1, 0, 0, 1, 0, 0, 4, 2, 4, 1, 2, 'night', 'Rigger'),
(11, 11, 'Prof. Hulda Boehm', 30, 'female', 26000.00, 39000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-01-24', 0, 0, 'THAT direction,\' the Cat in a trembling voice, \'Let us get to twenty at that rate! However, the.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 0, 1, 1, 1, 1, 0, 2, 1, 4, 5, 4, 'mixed', 'Prepress Technician'),
(12, 12, 'Mr. Marlin Nader PhD', 24, 'male', 19000.00, 58000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-02-16', 0, 0, 'Why, there\'s hardly room for this, and after a pause: \'the reason is, that there\'s any one left.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 1, 0, 0, 1, 1, 0, 5, 1, 2, 2, 4, 'mixed', 'Lodging Manager'),
(13, 13, 'Felicity Ullrich', 31, 'female', 15000.00, 43000.00, 'Maligawatta', NULL, 'Maligawatta', '2026-02-02', 1, 0, 'Alice as it spoke. \'As wet as ever,\' said Alice in a day or two: wouldn\'t it be murder to leave.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 1, 0, 0, 0, 0, 0, 2, 4, 3, 5, 5, 'mixed', 'Nursery Manager'),
(14, 14, 'Raymundo Thiel', 21, 'male', 16000.00, 55000.00, 'Wellawatte', NULL, 'Wellawatte', '2025-12-29', 1, 1, 'She had not noticed before, and she heard the Rabbit whispered in a low voice, \'Your Majesty must.', '2025-12-20 00:12:36', '2025-12-20 00:12:36', 1, 0, 0, 1, 1, 0, 2, 1, 5, 5, 2, 'morning', 'Home Health Aide'),
(15, 15, 'Lilliana Satterfield Jr.', 21, 'female', 29000.00, 37000.00, 'Wellawatte', NULL, 'Wellawatte', '2025-12-31', 1, 0, 'THE KING AND QUEEN OF HEARTS. Alice was so long since she had hurt the poor animal\'s feelings. \'I.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 0, 0, 0, 0, 0, 5, 1, 2, 4, 3, 'morning', 'Grips'),
(16, 16, 'Ned Williamson', 26, 'male', 27000.00, 47000.00, 'Wellawatte', NULL, 'Wellawatte', '2026-02-18', 1, 0, 'Queen: so she felt that it led into a pig,\' Alice quietly said, just as she spoke, but no result.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 0, 1, 1, 0, 0, 5, 4, 2, 4, 4, 'morning', 'Gas Processing Plant Operator'),
(17, 17, 'Pamela Kuhlman', 25, 'female', 30000.00, 46000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-01-19', 1, 0, 'Alice think it so quickly that the pebbles were all talking at once, while all the creatures order.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 1, 0, 1, 0, 0, 5, 3, 1, 2, 5, 'morning', 'Pressure Vessel Inspector'),
(18, 18, 'Mr. Charlie Schinner', 31, 'male', 26000.00, 40000.00, 'Maligawatta', NULL, 'Maligawatta', '2026-02-01', 0, 0, 'They had a bone in his turn; and both creatures hid their faces in their mouths--and they\'re all.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 1, 0, 0, 0, 0, 3, 4, 3, 1, 3, 'night', 'Manufacturing Sales Representative'),
(19, 19, 'Aryanna Yost', 29, 'female', 27000.00, 50000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-02-02', 0, 0, 'Long Tale They were just beginning to write this down on the second verse of the earth. Let me.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 1, 0, 0, 0, 0, 4, 3, 1, 2, 2, 'night', 'Interpreter OR Translator'),
(20, 20, 'Halie Bernier', 33, 'female', 20000.00, 60000.00, 'Maligawatta', NULL, 'Maligawatta', '2025-12-27', 0, 0, 'Alice, \'and those twelve creatures,\' (she was obliged to have no sort of mixed flavour of.', '2025-12-20 00:12:37', '2025-12-20 00:12:37', 1, 0, 0, 1, 0, 0, 4, 2, 1, 2, 2, 'morning', 'Etcher'),
(21, 21, 'Koby Mitchell', 27, 'male', 29000.00, 50000.00, 'Dehiwala', NULL, 'Dehiwala', '2026-02-16', 0, 0, 'She had just begun to repeat it, when a sharp hiss made her next remark. \'Then the Dormouse.', '2025-12-20 00:12:38', '2025-12-20 00:12:38', 1, 0, 0, 1, 0, 0, 2, 3, 4, 4, 3, 'night', 'Clinical Psychologist'),
(22, 22, 'Ms. Nelda Schuppe', 31, 'female', 26000.00, 46000.00, 'Maligawatta', NULL, 'Maligawatta', '2025-12-28', 0, 1, 'I do it again and again.\' \'You are old, Father William,\' the young lady to see some meaning in it.', '2025-12-20 00:12:38', '2025-12-20 00:12:38', 1, 0, 0, 1, 1, 0, 4, 3, 4, 4, 3, 'morning', 'Mathematical Technician');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0rI3A75RxvVCBg6pmekYIH1isc0FAmwD3D8WO6GP', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0J6WWR2aGJPcUtYUU9OU0hJb2lTeGVFenJObWh1NHlBRHRqU3l4eCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yb29tbWF0ZXMiO3M6NToicm91dGUiO3M6MTU6InJvb21tYXRlcy5pbmRleCI7fX0=', 1769005717),
('j060ysQsJpKPAa9sqv5BUOtcy0KhCXHVwM0cIXgJ', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR0Z6c0VXMklmYlJIY1o0ejRRRmNiUmhaSWwwVlJ1R0N0RlBuVVR1UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9wZXJ0aWVzIjtzOjU6InJvdXRlIjtzOjE2OiJwcm9wZXJ0aWVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1769007585);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `profile_photo_path`, `phone_number`) VALUES
(1, 'avithrantest', 'avi@gmail.com', 'seeker', NULL, '$2y$12$J8AvVOAsa9DC81kh7hFNROS9kMgQt9f4qzStYyzazkEhPmgdxh5gS', 'vEuIP4V1bevtV4lmNwQ4Cd7JdU6dHVoej4WerLl7RatJz1Trkt8G1P1hl3SN', '2025-12-17 03:43:43', '2026-01-20 18:19:42', 'profile-photos/45rggydjYKtNZvO7wLA4XsD7X9GwYhxBYm6BPKBB.png', NULL),
(2, 'Sai', 'sai@gmail.com', 'seeker', NULL, '$2y$12$eOiDXGffZN7SQOuq6xjMKOflv/064SrqUG2zkgK2q4ebibzj5ii2u', NULL, '2025-12-17 03:50:17', '2026-01-19 03:22:58', NULL, NULL),
(3, 'Fidel Osinski I', 'profile_maligawatta_0_980@example.com', NULL, NULL, '$2y$12$icP4NYausYhJppHxP.YC8eQ5YJg5myAlAskFH9z9W1Zb.CW31/f/y', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(4, 'Ms. Karolann Koch', 'profile_wellawatte_1_168@example.com', NULL, NULL, '$2y$12$pIq9arCBGvi0AIv70VTl8uPUsw75uZUtOUkIWUjaahXvIbzynHsFC', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(5, 'Octavia Carroll', 'profile_wellawatte_2_934@example.com', NULL, NULL, '$2y$12$bv/Sy2eNz.FUJN5yaUr3D.97UXJJ2HmLNZtc.BzMzdZAs5QegfeMi', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(6, 'Mr. Gino Dach', 'profile_dehiwala_3_449@example.com', NULL, NULL, '$2y$12$F7c1T7gMfi7IbZEbLFLXKefdT5PbQMjt1vwd0jpNJS2M.J8.mQPYO', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(7, 'Jarrett Bernier', 'profile_dehiwala_4_130@example.com', NULL, NULL, '$2y$12$EHECj9pSvPtzxy19hhRCc.iUdhSh9GIp6FKKGBm57Dst/AkkaT.ki', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(8, 'Briana Marquardt', 'profile_dehiwala_5_826@example.com', NULL, NULL, '$2y$12$nOFYP9/QoMfTSZILZXExheOLaYd/VoL.T/xgZrWjx6Ym8/9jdu60u', NULL, '2025-12-20 00:12:35', '2025-12-20 00:12:35', NULL, NULL),
(9, 'Mrs. Eryn Nitzsche', 'profile_dehiwala_6_189@example.com', NULL, NULL, '$2y$12$sx1lHmLtj803m3vo3yG43.vcBKU75CyHjvp5vFxPpq1B/gBobuTpe', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(10, 'Rosalia Renner', 'profile_wellawatte_7_453@example.com', NULL, NULL, '$2y$12$CwlIOFLovysSGDqDx08LE.Z4C0x4MtxOFCMJ5C5IZY0lAhNJ8iXye', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(11, 'Prof. Hulda Boehm', 'profile_dehiwala_8_347@example.com', NULL, NULL, '$2y$12$2Q91dEUzg2z/1qwZel3OruXQzAlBaTSh.WanEMM9zEWzZQ4S9BHV6', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(12, 'Mr. Marlin Nader PhD', 'profile_dehiwala_9_565@example.com', NULL, NULL, '$2y$12$oF8ZaO4XHR.GP3gkS7ObDeZUgXychStgXA57ME1xY645JKKkJtoKy', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(13, 'Felicity Ullrich', 'profile_maligawatta_10_223@example.com', NULL, NULL, '$2y$12$S9w3FVNCnlUM1l/0bogrzuyWej4aDbWWO5orR44YKx0tOAd2It0OO', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(14, 'Raymundo Thiel', 'profile_wellawatte_11_958@example.com', NULL, NULL, '$2y$12$SVzCcf.eF/UPJXD.qPxHAOsrksNs1NI7IOckY8bjRAZvTDVlRm8Xm', NULL, '2025-12-20 00:12:36', '2025-12-20 00:12:36', NULL, NULL),
(15, 'Lilliana Satterfield Jr.', 'profile_wellawatte_12_788@example.com', NULL, NULL, '$2y$12$IgyRD2NtELL/8fsFmAj24uPkbRmCTjylJroOgl6QRDwdTaaiyXyv6', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(16, 'Ned Williamson', 'profile_wellawatte_13_967@example.com', NULL, NULL, '$2y$12$o6c40xOvITNDMo/g5baIeeeSo7M91P9JpnYRwFNUcrRko6u0ng4rO', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(17, 'Pamela Kuhlman', 'profile_dehiwala_14_755@example.com', NULL, NULL, '$2y$12$nZdbzdcLUBGtJ6AUgikLUunl2TQaEI16n22XHmjo0L45Btm1m7e6u', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(18, 'Mr. Charlie Schinner', 'profile_maligawatta_15_839@example.com', NULL, NULL, '$2y$12$az9WG1Fkc8QM/Y/vu1Tn5evqwpamrDahgxbra4EBHqlmNVENLAyB2', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(19, 'Aryanna Yost', 'profile_dehiwala_16_957@example.com', NULL, NULL, '$2y$12$ILgxDI1DVGQaiyQYszj4/uUGsEtQm1.Iil0i8hqnJYDFrzn7cEsXy', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(20, 'Halie Bernier', 'profile_maligawatta_17_653@example.com', NULL, NULL, '$2y$12$ihRyY3yLEFgYTFzm2VaiUednDcKq2xBbiny1tMobtCpqLpeVV9W7O', NULL, '2025-12-20 00:12:37', '2025-12-20 00:12:37', NULL, NULL),
(21, 'Koby Mitchell', 'profile_dehiwala_18_703@example.com', NULL, NULL, '$2y$12$iQwys2ZZ/au1pFNdok.qEOogjHjh4Y698timVPX4Ia5bFGs5.W4e2', NULL, '2025-12-20 00:12:38', '2025-12-20 00:12:38', NULL, NULL),
(22, 'Ms. Nelda Schuppe', 'profile_maligawatta_19_535@example.com', NULL, NULL, '$2y$12$sXkZeoKyN5HBHOeAUa6/muBVjEHzwL1dBz/AGuMTbSxjGfeZKvh62', NULL, '2025-12-20 00:12:38', '2025-12-20 00:12:38', NULL, NULL),
(23, 'avi.landlord', 'avi123@gmail.com', 'landlord', NULL, '$2y$12$6RuSbNSNhuEMgIdaTxhdguQcdAEs6uh//wb/rGt0Msfenxk10mGE2', NULL, '2025-12-22 22:42:17', '2026-01-20 15:45:57', NULL, '0784847434983'),
(24, 'Admin User', 'admin@dwello.com', 'landlord', NULL, '$2y$12$qIl1XEih8O5aWm2Cd7LBs.A3kcHihqEzgkGjPNuKLAHd7XOptbraq', NULL, '2025-12-24 01:40:59', '2025-12-24 01:40:59', NULL, NULL),
(25, 'Dwello Estates', 'landlord@dwello.com', 'landlord', '2025-12-24 04:28:18', '$2y$12$sx.JN0SzfxlABit13/DBdukmFwb/WayJxk4RfuB6jOq/WtQEA0CSK', NULL, '2025-12-24 04:28:18', '2025-12-24 04:28:18', NULL, NULL),
(26, 'ashok', 'ashok@gmail.com', 'landlord', NULL, '$2y$12$uAc.Td.Vrqsui86kokjLTO3QZiKNh6EfE9jTU.t3TtK/Oz2qzEeTa', NULL, '2025-12-29 09:02:48', '2025-12-29 09:03:24', NULL, NULL),
(27, 'sheron', 'sheron@gmail.com', 'seeker', NULL, '$2y$12$F3NoS/K.iEQS2guI1WvzMODTOn0WJwd5bdC7rQC3DYTNW0zgZDO6W', NULL, '2025-12-29 09:04:25', '2025-12-29 09:04:29', NULL, NULL),
(28, 'sai', 'sai123@gmail.com', NULL, NULL, '$2y$12$km4.QMsZnXIrkuD9amPZIuWt6uhnolNfWQLbpladEc2wnQ3yCUKJa', NULL, '2025-12-29 09:06:52', '2025-12-29 09:06:52', NULL, NULL),
(30, 'new', 'new@gmail.com', 'landlord', NULL, '$2y$12$kGXVyrkDlhEnuqWvxLi.z.G2Tpy7V8ZNg7on57BB6NH1YfNXuE2E6', NULL, '2026-01-19 02:37:17', '2026-01-19 02:37:30', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conversations_property_unique` (`type`,`property_id`,`user_one_id`,`user_two_id`),
  ADD KEY `conversations_type_user_one_id_user_two_id_index` (`type`,`user_one_id`,`user_two_id`),
  ADD KEY `conversations_user_one_id_last_message_at_index` (`user_one_id`,`last_message_at`),
  ADD KEY `conversations_user_two_id_last_message_at_index` (`user_two_id`,`last_message_at`),
  ADD KEY `conversations_property_id_index` (`property_id`),
  ADD KEY `conversations_started_by_foreign` (`started_by`),
  ADD KEY `conversations_blocked_by_foreign` (`blocked_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorites_user_id_roommate_profile_id_unique` (`user_id`,`roommate_profile_id`),
  ADD KEY `favorites_roommate_profile_id_foreign` (`roommate_profile_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_conversation_id_id_index` (`conversation_id`,`id`),
  ADD KEY `messages_property_id_foreign` (`property_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `properties_user_id_foreign` (`user_id`),
  ADD KEY `properties_city_index` (`city`),
  ADD KEY `properties_monthly_rent_index` (`monthly_rent`),
  ADD KEY `properties_property_type_index` (`property_type`);

--
-- Indexes for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_photos_property_id_foreign` (`property_id`);

--
-- Indexes for table `rejected_matches`
--
ALTER TABLE `rejected_matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rejected_matches_user_id_rejected_user_id_unique` (`user_id`,`rejected_user_id`),
  ADD KEY `rejected_matches_rejected_user_id_foreign` (`rejected_user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_property_id_user_id_unique` (`property_id`,`user_id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_property_id_status_index` (`property_id`,`status`),
  ADD KEY `reviews_status_index` (`status`);

--
-- Indexes for table `roommate_profiles`
--
ALTER TABLE `roommate_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roommate_profiles_user_id_foreign` (`user_id`),
  ADD KEY `roommate_profiles_preferred_city_index` (`preferred_city`),
  ADD KEY `roommate_profiles_budget_min_index` (`budget_min`),
  ADD KEY `roommate_profiles_budget_max_index` (`budget_max`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `property_photos`
--
ALTER TABLE `property_photos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `rejected_matches`
--
ALTER TABLE `rejected_matches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roommate_profiles`
--
ALTER TABLE `roommate_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_blocked_by_foreign` FOREIGN KEY (`blocked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conversations_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_started_by_foreign` FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_user_one_id_foreign` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_user_two_id_foreign` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_roommate_profile_id_foreign` FOREIGN KEY (`roommate_profile_id`) REFERENCES `roommate_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD CONSTRAINT `property_photos_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rejected_matches`
--
ALTER TABLE `rejected_matches`
  ADD CONSTRAINT `rejected_matches_rejected_user_id_foreign` FOREIGN KEY (`rejected_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rejected_matches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roommate_profiles`
--
ALTER TABLE `roommate_profiles`
  ADD CONSTRAINT `roommate_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
