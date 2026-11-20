-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Nov 2026 pada 05.05
-- Versi server: 10.4.32-MariaDB-log
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barengin_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `host_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `participant_level` enum('beginner','all_levels','experienced') NOT NULL DEFAULT 'all_levels',
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'Asia/Jakarta',
  `venue_name` varchar(150) NOT NULL,
  `address` varchar(255) NOT NULL,
  `area` varchar(120) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `country_code` char(2) NOT NULL DEFAULT 'ID',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `capacity` tinyint(3) UNSIGNED NOT NULL,
  `budget_amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `currency` char(3) NOT NULL DEFAULT 'IDR',
  `cover_image_path` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel `activities`
--

INSERT INTO `activities` (`id`, `host_id`, `category_id`, `title`, `description`, `participant_level`, `starts_at`, `ends_at`, `timezone`, `venue_name`, `address`, `area`, `city`, `province`, `country_code`, `latitude`, `longitude`, `capacity`, `budget_amount`, `currency`, `cover_image_path`, `status`, `published_at`, `completed_at`, `cancelled_at`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 5, 'Mabar Roblox', 'Horror gaskan', 'all_levels', '2026-11-20 15:00:00', '2026-11-20 17:00:00', 'Asia/Jakarta', 'Rumah masing masing', 'tanya mak', 'lah ndak tau', 'bebas', 'Jawa Barat', 'ID', NULL, NULL, 5, 0, 'IDR', NULL, 'published', '2026-11-18 19:59:23', NULL, NULL, NULL, '2026-11-18 19:59:23', '2026-11-18 19:59:23'),
(2, 3, 3, 'Codinh', 'testtinh code', 'all_levels', '2026-11-20 15:00:00', '2026-11-20 17:00:00', 'Asia/Jakarta', 'utb', 'soetta', 'gatau', 'bandung', 'Jawa Barat', 'ID', NULL, NULL, 5, 0, 'IDR', NULL, 'published', '2026-11-18 20:15:09', NULL, NULL, NULL, '2026-11-18 20:15:08', '2026-11-18 20:15:09'),
(3, 2, 1, 'tetstgsys', 'hsusbdvdvbshs', 'experienced', '2026-11-20 15:00:00', '2026-11-20 17:00:00', 'Asia/Jakarta', 'gsysbejns', 'whshsbisbaja', 'Soekarno Hatta', 'Bandung', 'Jawa Barat', 'ID', -6.8413451, 107.5178381, 5, 84845448, 'IDR', NULL, 'cancelled', '2026-11-18 20:19:15', NULL, '2026-11-18 20:19:22', 'Dibatalkan oleh Host.', '2026-11-18 20:19:15', '2026-11-18 20:19:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_favorites`
--

CREATE TABLE `activity_favorites` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_participants`
--

CREATE TABLE `activity_participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('host','member') NOT NULL DEFAULT 'member',
  `status` enum('joined','left','removed') NOT NULL DEFAULT 'joined',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `left_at` timestamp NULL DEFAULT NULL,
  `removed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `removal_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity_participants`
--

INSERT INTO `activity_participants` (`id`, `activity_id`, `user_id`, `role`, `status`, `joined_at`, `left_at`, `removed_by`, `removal_note`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'host', 'joined', '2026-11-18 19:59:23', NULL, NULL, NULL, '2026-11-18 19:59:23', '2026-11-18 19:59:23'),
(2, 2, 3, 'host', 'joined', '2026-11-18 20:15:08', NULL, NULL, NULL, '2026-11-18 20:15:08', '2026-11-18 20:15:08'),
(3, 2, 2, 'member', 'joined', '2026-11-18 20:16:57', NULL, NULL, NULL, '2026-11-18 20:16:57', '2026-11-18 20:16:57'),
(4, 3, 2, 'host', 'joined', '2026-11-18 20:19:15', NULL, NULL, NULL, '2026-11-18 20:19:15', '2026-11-18 20:19:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon_key` varchar(80) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon_key`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sports & Fitness', 'sports-fitness', 'Badminton, gym, jogging, cycling, and other sports.', 'sports', 1, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50'),
(2, 'Hangout & Social', 'hangout-social', 'Coffee, food, board games, and casual meetups.', 'coffee', 2, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50'),
(3, 'Study & Productivity', 'study-productivity', 'Group study, coworking, and discussion sessions.', 'book-open', 3, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50'),
(4, 'Art & Exploration', 'art-exploration', 'Museum visits, photography, city walks, and exploration.', 'camera', 4, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50'),
(5, 'Creative & Hobby', 'creative-hobby', 'Music, crafting, gaming, and creative workshops.', 'palette', 5, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50'),
(6, 'Community & Social Impact', 'community-social-impact', 'Volunteering, community events, and social activities.', 'users', 6, 1, '2026-11-14 03:24:50', '2026-11-14 03:24:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `join_requests`
--

CREATE TABLE `join_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `decision_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `join_requests`
--

INSERT INTO `join_requests` (`id`, `activity_id`, `user_id`, `message`, `status`, `reviewed_by`, `reviewed_at`, `decision_note`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'ikut', 'rejected', 2, '2026-11-18 20:19:28', NULL, '2026-11-18 19:59:36', '2026-11-18 20:19:28'),
(2, 2, 2, 'testing', 'accepted', 3, '2026-11-18 20:16:57', NULL, '2026-11-18 20:15:38', '2026-11-18 20:16:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_11_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_11_18_000002_add_cancelled_status_to_join_requests_table', 2),
(3, '2026_11_18_081531_change_join_requests_unique_index_to_regular_index', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'Postman', 'd1889a6d8d9c9dcbc69fef706dd866a8866b797dacb38b5fd75cd78175059f19', '[\"*\"]', NULL, NULL, '2026-11-13 21:28:37', '2026-11-13 21:28:37'),
(2, 'App\\Models\\User', 1, 'Postman', 'b0efae6c896f61c2e8fec47ab95312a8ad3ee608e072e67e6ff83aed76b2806b', '[\"*\"]', NULL, NULL, '2026-11-13 21:29:39', '2026-11-13 21:29:39'),
(3, 'App\\Models\\User', 1, 'Postman', '2fac1f86b2841c6cf8b52ef92290c6ff0f799542dc4ffe445cb3f56625bf2c6c', '[\"*\"]', NULL, NULL, '2026-11-13 21:31:40', '2026-11-13 21:31:40'),
(4, 'App\\Models\\User', 1, 'Postman', '83a9cc1ae8f41242b709572db41b0d250b24e8829f389a8d56a28bd5ba48d538', '[\"*\"]', NULL, NULL, '2026-11-13 21:32:34', '2026-11-13 21:32:34'),
(5, 'App\\Models\\User', 1, 'Postman', '673ad7f06a87578426fda6e354792ec41c9e5bc3adc8e8df525286f1f1906ebc', '[\"*\"]', NULL, NULL, '2026-11-13 21:35:38', '2026-11-13 21:35:38'),
(7, 'App\\Models\\User', 1, 'Postman', '65063decc22d5f44f2a6c8e802a00f9581524704a9e2170c27d094d21868e58b', '[\"*\"]', NULL, NULL, '2026-11-13 21:40:22', '2026-11-13 21:40:22'),
(8, 'App\\Models\\User', 1, 'Postman', '36876755c28742ecb25a2e08e7ad272ee0d474799bd556e9cd3481ecf7f0c2d3', '[\"*\"]', '2026-11-13 21:43:39', NULL, '2026-11-13 21:42:29', '2026-11-13 21:43:39'),
(9, 'App\\Models\\User', 1, 'Barengin Flutter', 'a0ac034fdb754e5d491602fb4dea2c86821c0990a3d34c3cc54d149ccc371abd', '[\"*\"]', '2026-11-14 00:14:17', NULL, '2026-11-13 23:31:53', '2026-11-14 00:14:17'),
(15, 'App\\Models\\User', 3, 'Barengin Flutter', '0480f29aab4990e0dfe59ed39e084f5f9b0b3bdf64e97f2bf0b7cb9bc02bf675', '[\"*\"]', '2026-11-18 20:16:58', NULL, '2026-11-17 20:40:53', '2026-11-18 20:16:58'),
(16, 'App\\Models\\User', 4, 'PowerShell Integration Test', '24805e4aacfcc7233b2e7a9f2a30b839cc3cccc40f772968859efa658026d15a', '[\"*\"]', NULL, NULL, '2026-11-18 02:52:00', '2026-11-18 02:52:00'),
(17, 'App\\Models\\User', 2, 'Barengin Flutter', '040acd27761dd3bd3043f73bc85d16df825f8a8b92447dcfbed84345b86533c5', '[\"*\"]', '2026-11-18 18:55:33', NULL, '2026-11-18 07:32:18', '2026-11-18 18:55:33'),
(18, 'App\\Models\\User', 2, 'Barengin Flutter', '1305ed646456512ebc2bf3fe522c5211ccdbd57c5c59c821c522baefb60cf3d8', '[\"*\"]', '2026-11-18 20:19:41', NULL, '2026-11-18 19:43:45', '2026-11-18 20:19:41'),
(19, 'App\\Models\\User', 2, 'Barengin Flutter', '4b9c418d2d78397a30685474784b43f5fa3ec0ff3399d6f5908ce08ced2720b0', '[\"*\"]', NULL, NULL, '2026-11-18 19:43:49', '2026-11-18 19:43:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `adult_confirmed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `terms_accepted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `privacy_accepted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `adult_confirmed_at`, `terms_accepted_at`, `privacy_accepted_at`, `is_active`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Adika', 'adika_test001', 'adika.test001@example.com', NULL, '$2y$12$SzyKkuIMSXWq1l/ylJtBJeNltrCpKfcDa3g26ZjdQ7byCEwfiLcwu', '2026-11-13 21:28:36', '2026-11-13 21:28:36', '2026-11-13 21:28:36', 1, '2026-11-13 23:31:52', NULL, '2026-11-13 21:28:36', '2026-11-13 23:33:03', NULL),
(2, 'Adika Pamungkas', 'Al_Pmngks', 'adikapamungkasputra@gmail.com', NULL, '$2y$12$S5ja9u.Ca44PMvp4CO53hO.Gs.5Z.nz16wqJN7lgJlSwMK.dCCL1a', '2026-11-17 20:07:36', '2026-11-17 20:07:36', '2026-11-17 20:07:36', 1, '2026-11-18 19:43:49', NULL, '2026-11-17 20:07:37', '2026-11-18 19:43:49', NULL),
(3, 'Max Devon', 'ToTheMax', 'maxdevon@gmail.com', NULL, '$2y$12$ohWAeEZv4hYltv8uNrkZZ.3eNzmCnM31MIStBywJRwTrsrr5rVOSa', '2026-11-17 20:11:57', '2026-11-17 20:11:57', '2026-11-17 20:11:57', 1, '2026-11-17 20:40:53', NULL, '2026-11-17 20:11:57', '2026-11-17 20:40:53', NULL),
(4, 'Integration Test', 'integration_test01', 'integration01@barengin.test', NULL, '$2y$12$pJrVRpuyx.yTjFQlfNHFYux9gHv0mMhAuBarxQ60eahxyBVNTKF9S', '2026-11-18 02:52:00', '2026-11-18 02:52:00', '2026-11-18 02:52:00', 1, NULL, NULL, '2026-11-18 02:52:00', '2026-11-18 02:52:00', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_blocks`
--

CREATE TABLE `user_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blocker_id` bigint(20) UNSIGNED NOT NULL,
  `blocked_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_interests`
--

CREATE TABLE `user_interests` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_interests`
--

INSERT INTO `user_interests` (`user_id`, `category_id`, `created_at`) VALUES
(1, 1, '2026-11-13 23:33:04'),
(1, 3, '2026-11-13 23:33:04'),
(1, 6, '2026-11-13 23:33:04'),
(2, 1, '2026-11-18 07:14:16'),
(2, 2, '2026-11-18 07:14:16'),
(2, 4, '2026-11-18 07:14:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `display_name` varchar(80) NOT NULL,
  `bio` varchar(500) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `area` varchar(120) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `country_code` char(2) NOT NULL DEFAULT 'ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `display_name`, `bio`, `avatar_path`, `area`, `city`, `province`, `country_code`, `created_at`, `updated_at`) VALUES
(1, 'Adika', NULL, NULL, NULL, NULL, NULL, 'ID', '2026-11-13 21:28:37', '2026-11-13 23:33:03'),
(2, 'Adika Pamungkas', 'aku mau makan', 'avatars/lzorlCVX6ObSZ1HswAPY604SmWxVmSmY7v3yVKCK.jpg', 'Soekarno Hatta', 'Bandung', NULL, 'ID', '2026-11-17 20:07:38', '2026-11-18 07:14:15'),
(3, 'Max Devon', NULL, NULL, NULL, NULL, NULL, 'ID', '2026-11-17 20:11:57', '2026-11-17 20:11:57'),
(4, 'Integration Test', NULL, NULL, NULL, NULL, NULL, 'ID', '2026-11-18 02:52:00', '2026-11-18 02:52:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_reports`
--

CREATE TABLE `user_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `reported_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` enum('inappropriate_behavior','misleading_activity','harassment_or_threat','spam_or_scam','other') NOT NULL,
  `details` varchar(1000) DEFAULT NULL,
  `status` enum('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activities_host_status` (`host_id`,`status`,`starts_at`),
  ADD KEY `idx_activities_category_status_date` (`category_id`,`status`,`starts_at`),
  ADD KEY `idx_activities_city_area_status` (`city`,`area`,`status`),
  ADD KEY `idx_activities_status_date` (`status`,`starts_at`),
  ADD KEY `idx_activities_budget` (`budget_amount`);
ALTER TABLE `activities` ADD FULLTEXT KEY `ft_activities_title_description` (`title`,`description`);

--
-- Indeks untuk tabel `activity_favorites`
--
ALTER TABLE `activity_favorites`
  ADD PRIMARY KEY (`user_id`,`activity_id`),
  ADD KEY `idx_activity_favorites_activity` (`activity_id`);

--
-- Indeks untuk tabel `activity_participants`
--
ALTER TABLE `activity_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_activity_participants_activity_user` (`activity_id`,`user_id`),
  ADD KEY `idx_activity_participants_user_status` (`user_id`,`status`),
  ADD KEY `idx_activity_participants_activity_status` (`activity_id`,`status`),
  ADD KEY `fk_activity_participants_removed_by` (`removed_by`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_name` (`name`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`),
  ADD KEY `idx_categories_active_order` (`is_active`,`sort_order`);

--
-- Indeks untuk tabel `join_requests`
--
ALTER TABLE `join_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_join_requests_user_status_date` (`user_id`,`status`,`created_at`),
  ADD KEY `idx_join_requests_activity_status` (`activity_id`,`status`),
  ADD KEY `idx_join_requests_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_join_requests_activity_user` (`activity_id`,`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD KEY `idx_users_active` (`is_active`),
  ADD KEY `idx_users_deleted_at` (`deleted_at`);

--
-- Indeks untuk tabel `user_blocks`
--
ALTER TABLE `user_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_blocks_pair` (`blocker_id`,`blocked_user_id`),
  ADD KEY `idx_user_blocks_blocked` (`blocked_user_id`);

--
-- Indeks untuk tabel `user_interests`
--
ALTER TABLE `user_interests`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `idx_user_interests_category` (`category_id`);

--
-- Indeks untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_user_profiles_city_area` (`city`,`area`);

--
-- Indeks untuk tabel `user_reports`
--
ALTER TABLE `user_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_reports_reporter_date` (`reporter_id`,`created_at`),
  ADD KEY `idx_user_reports_reported_user` (`reported_user_id`,`status`),
  ADD KEY `idx_user_reports_activity` (`activity_id`,`status`),
  ADD KEY `fk_user_reports_reviewer` (`reviewed_by`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `activity_participants`
--
ALTER TABLE `activity_participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `join_requests`
--
ALTER TABLE `join_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `user_blocks`
--
ALTER TABLE `user_blocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_reports`
--
ALTER TABLE `user_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_activities_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activities_host` FOREIGN KEY (`host_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `activity_favorites`
--
ALTER TABLE `activity_favorites`
  ADD CONSTRAINT `fk_activity_favorites_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `activity_participants`
--
ALTER TABLE `activity_participants`
  ADD CONSTRAINT `fk_activity_participants_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_participants_removed_by` FOREIGN KEY (`removed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_participants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `join_requests`
--
ALTER TABLE `join_requests`
  ADD CONSTRAINT `fk_join_requests_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_join_requests_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_join_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_blocks`
--
ALTER TABLE `user_blocks`
  ADD CONSTRAINT `fk_user_blocks_blocked` FOREIGN KEY (`blocked_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_blocks_blocker` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_interests`
--
ALTER TABLE `user_interests`
  ADD CONSTRAINT `fk_user_interests_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_interests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_reports`
--
ALTER TABLE `user_reports`
  ADD CONSTRAINT `fk_user_reports_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_reports_reported_user` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_reports_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_reports_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
