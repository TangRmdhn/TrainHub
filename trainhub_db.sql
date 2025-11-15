-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 10:08 AM
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
-- Database: `trainhub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Chest','Back','Shoulders','Arms','Legs','Core','Cardio','Full Body') NOT NULL,
  `difficulty` enum('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  `equipment` varchar(100) DEFAULT 'None',
  `media_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `default_duration_seconds` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `name`, `category`, `difficulty`, `equipment`, `media_url`, `description`, `default_duration_seconds`, `created_at`) VALUES
(1, 'Bench Press', 'Chest', 'Intermediate', 'Barbell, Bench', NULL, 'Compound chest exercise targeting pectoralis major', NULL, '2025-11-15 08:09:43'),
(2, 'Incline Dumbbell Press', 'Chest', 'Intermediate', 'Dumbbells, Incline Bench', NULL, 'Upper chest development', NULL, '2025-11-15 08:09:43'),
(3, 'Push-ups', 'Chest', 'Beginner', 'None', NULL, 'Bodyweight chest exercise', NULL, '2025-11-15 08:09:43'),
(4, 'Cable Fly', 'Chest', 'Intermediate', 'Cable Machine', NULL, 'Isolation exercise for chest', NULL, '2025-11-15 08:09:43'),
(5, 'Dips', 'Chest', 'Intermediate', 'Dip Station', NULL, 'Compound movement for chest and triceps', NULL, '2025-11-15 08:09:43'),
(6, 'Deadlift', 'Back', 'Advanced', 'Barbell', NULL, 'Full body compound movement', NULL, '2025-11-15 08:09:43'),
(7, 'Pull-ups', 'Back', 'Intermediate', 'Pull-up Bar', NULL, 'Bodyweight back exercise', NULL, '2025-11-15 08:09:43'),
(8, 'Bent-Over Row', 'Back', 'Intermediate', 'Barbell', NULL, 'Targets lats and mid-back', NULL, '2025-11-15 08:09:43'),
(9, 'Lat Pulldown', 'Back', 'Beginner', 'Cable Machine', NULL, 'Machine-based back exercise', NULL, '2025-11-15 08:09:43'),
(10, 'T-Bar Row', 'Back', 'Intermediate', 'T-Bar, Barbell', NULL, 'Thickness builder for back', NULL, '2025-11-15 08:09:43'),
(11, 'Seated Cable Row', 'Back', 'Beginner', 'Cable Machine', NULL, 'Mid-back and lats', NULL, '2025-11-15 08:09:43'),
(12, 'Overhead Press', 'Shoulders', 'Intermediate', 'Barbell', NULL, 'Compound shoulder movement', NULL, '2025-11-15 08:09:43'),
(13, 'Dumbbell Lateral Raise', 'Shoulders', 'Beginner', 'Dumbbells', NULL, 'Isolation for side delts', NULL, '2025-11-15 08:09:43'),
(14, 'Front Raise', 'Shoulders', 'Beginner', 'Dumbbells', NULL, 'Front delt isolation', NULL, '2025-11-15 08:09:43'),
(15, 'Face Pull', 'Shoulders', 'Beginner', 'Cable Machine', NULL, 'Rear delt and upper back', NULL, '2025-11-15 08:09:43'),
(16, 'Arnold Press', 'Shoulders', 'Intermediate', 'Dumbbells', NULL, 'All three delt heads', NULL, '2025-11-15 08:09:43'),
(17, 'Barbell Curl', 'Arms', 'Beginner', 'Barbell', NULL, 'Bicep builder', NULL, '2025-11-15 08:09:43'),
(18, 'Tricep Dips', 'Arms', 'Intermediate', 'Dip Station', NULL, 'Tricep mass builder', NULL, '2025-11-15 08:09:43'),
(19, 'Hammer Curl', 'Arms', 'Beginner', 'Dumbbells', NULL, 'Brachialis and bicep', NULL, '2025-11-15 08:09:43'),
(20, 'Tricep Pushdown', 'Arms', 'Beginner', 'Cable Machine', NULL, 'Tricep isolation', NULL, '2025-11-15 08:09:43'),
(21, 'Concentration Curl', 'Arms', 'Beginner', 'Dumbbell', NULL, 'Bicep peak development', NULL, '2025-11-15 08:09:43'),
(22, 'Overhead Tricep Extension', 'Arms', 'Beginner', 'Dumbbell', NULL, 'Long head tricep', NULL, '2025-11-15 08:09:43'),
(23, 'Squat', 'Legs', 'Intermediate', 'Barbell, Rack', NULL, 'King of leg exercises', NULL, '2025-11-15 08:09:43'),
(24, 'Leg Press', 'Legs', 'Beginner', 'Leg Press Machine', NULL, 'Quad dominant exercise', NULL, '2025-11-15 08:09:43'),
(25, 'Romanian Deadlift', 'Legs', 'Intermediate', 'Barbell', NULL, 'Hamstring and glute focus', NULL, '2025-11-15 08:09:43'),
(26, 'Leg Curl', 'Legs', 'Beginner', 'Leg Curl Machine', NULL, 'Hamstring isolation', NULL, '2025-11-15 08:09:43'),
(27, 'Leg Extension', 'Legs', 'Beginner', 'Leg Extension Machine', NULL, 'Quad isolation', NULL, '2025-11-15 08:09:43'),
(28, 'Walking Lunges', 'Legs', 'Intermediate', 'Dumbbells', NULL, 'Unilateral leg work', NULL, '2025-11-15 08:09:43'),
(29, 'Calf Raise', 'Legs', 'Beginner', 'Calf Machine', NULL, 'Calf development', NULL, '2025-11-15 08:09:43'),
(30, 'Plank', 'Core', 'Beginner', 'None', NULL, 'Isometric core exercise', 60, '2025-11-15 08:09:43'),
(31, 'Russian Twist', 'Core', 'Intermediate', 'Medicine Ball', NULL, 'Oblique work', NULL, '2025-11-15 08:09:43'),
(32, 'Hanging Leg Raise', 'Core', 'Advanced', 'Pull-up Bar', NULL, 'Lower abs focus', NULL, '2025-11-15 08:09:43'),
(33, 'Cable Crunch', 'Core', 'Beginner', 'Cable Machine', NULL, 'Upper abs', NULL, '2025-11-15 08:09:43'),
(34, 'Mountain Climbers', 'Core', 'Intermediate', 'None', NULL, 'Dynamic core work', 45, '2025-11-15 08:09:43'),
(35, 'Dead Bug', 'Core', 'Beginner', 'None', NULL, 'Core stability', 60, '2025-11-15 08:09:43'),
(36, 'Treadmill Running', 'Cardio', 'Beginner', 'Treadmill', NULL, 'Steady state cardio', 1200, '2025-11-15 08:09:43'),
(37, 'Rowing Machine', 'Cardio', 'Intermediate', 'Rowing Machine', NULL, 'Full body cardio', 900, '2025-11-15 08:09:43'),
(38, 'Jumping Jacks', 'Cardio', 'Beginner', 'None', NULL, 'Bodyweight cardio', 60, '2025-11-15 08:09:43'),
(39, 'Burpees', 'Cardio', 'Intermediate', 'None', NULL, 'High intensity exercise', 45, '2025-11-15 08:09:43'),
(40, 'Jump Rope', 'Cardio', 'Beginner', 'Jump Rope', NULL, 'Cardio conditioning', 300, '2025-11-15 08:09:43'),
(41, 'Cycling', 'Cardio', 'Beginner', 'Stationary Bike', NULL, 'Low impact cardio', 1200, '2025-11-15 08:09:43'),
(42, 'Kettlebell Swing', 'Full Body', 'Intermediate', 'Kettlebell', NULL, 'Explosive hip drive', NULL, '2025-11-15 08:09:43'),
(43, 'Clean and Press', 'Full Body', 'Advanced', 'Barbell', NULL, 'Olympic lift variation', NULL, '2025-11-15 08:09:43'),
(44, 'Thrusters', 'Full Body', 'Intermediate', 'Dumbbells', NULL, 'Squat to press', NULL, '2025-11-15 08:09:43'),
(45, 'Man Makers', 'Full Body', 'Advanced', 'Dumbbells', NULL, 'Complex movement', NULL, '2025-11-15 08:09:43');

-- --------------------------------------------------------

--
-- Stand-in structure for view `upcoming_workouts`
-- (See below for the actual view)
--
CREATE TABLE `upcoming_workouts` (
`schedule_id` int(11)
,`user_id` int(11)
,`username` varchar(50)
,`plan_name` varchar(150)
,`scheduled_date` date
,`is_completed` tinyint(1)
,`days_until` int(7)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'demo_user', 'demo@trainhub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-15 08:09:43', '2025-11-15 08:09:43'),
(2, 'testuser', 'test@test.com', '$2y$12$8aKUweccnFHL9TcjM2v9xu/6Hmm.n5Csse7lqNpLl..TYGn4nUQfq', '2025-11-15 08:26:42', '2025-11-15 08:26:42'),
(3, 'testuserrr', 'testt@test.com', '$2y$12$8iT37OA4Lmj29HWRYssqguaKpSm5bFs90ivyVATovEQdfxwrPT9ju', '2025-11-15 08:29:39', '2025-11-15 08:29:39'),
(4, 'testuser224', 'test507@trainhub.com', '$2y$12$nEIs2iUigHeAPXH4mZFCx.SYwIR2VWZQZ1s7Hpf.FNw8e4hC3b0IC', '2025-11-15 08:45:09', '2025-11-15 08:45:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_schedules`
--

CREATE TABLE `user_schedules` (
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `workout_notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_schedules`
--

INSERT INTO `user_schedules` (`schedule_id`, `user_id`, `plan_id`, `scheduled_date`, `is_completed`, `workout_notes`, `completed_at`, `created_at`) VALUES
(1, 1, 1, '2025-11-15', 0, NULL, NULL, '2025-11-15 08:09:43'),
(2, 1, 1, '2025-11-17', 0, NULL, NULL, '2025-11-15 08:09:43'),
(3, 1, 2, '2025-11-14', 1, NULL, NULL, '2025-11-15 08:09:43');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_workout_stats`
-- (See below for the actual view)
--
CREATE TABLE `user_workout_stats` (
`user_id` int(11)
,`username` varchar(50)
,`total_plans` bigint(21)
,`total_schedules` bigint(21)
,`completed_workouts` decimal(22,0)
,`last_workout_date` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `plan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(150) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`plan_id`, `user_id`, `plan_name`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '3-Day Full Body Beginner', 'Perfect for those starting their fitness journey', '2025-11-15 08:09:43', '2025-11-15 08:09:43'),
(2, 1, '4-Day Upper/Lower Split', 'Intermediate program for muscle building', '2025-11-15 08:09:43', '2025-11-15 08:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plan_items`
--

CREATE TABLE `workout_plan_items` (
  `item_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `day_index` int(11) NOT NULL,
  `day_title` varchar(100) NOT NULL,
  `exercise_name` varchar(100) NOT NULL,
  `exercise_order` int(11) DEFAULT 0,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_plan_items`
--

INSERT INTO `workout_plan_items` (`item_id`, `plan_id`, `day_index`, `day_title`, `exercise_name`, `exercise_order`, `sets`, `reps`, `duration_seconds`) VALUES
(1, 1, 0, 'Day 1: Full Body A', 'Squat', 0, 3, 10, NULL),
(2, 1, 0, 'Day 1: Full Body A', 'Bench Press', 1, 3, 10, NULL),
(3, 1, 0, 'Day 1: Full Body A', 'Bent-Over Row', 2, 3, 10, NULL),
(4, 1, 0, 'Day 1: Full Body A', 'Plank', 3, 3, NULL, 60),
(5, 1, 1, 'Day 2: Rest', '', 0, NULL, NULL, NULL),
(6, 1, 2, 'Day 3: Full Body B', 'Deadlift', 0, 3, 8, NULL),
(7, 1, 2, 'Day 3: Full Body B', 'Overhead Press', 1, 3, 10, NULL),
(8, 1, 2, 'Day 3: Full Body B', 'Pull-ups', 2, 3, 8, NULL),
(9, 1, 2, 'Day 3: Full Body B', 'Russian Twist', 3, 3, 15, NULL),
(10, 2, 0, 'Day 1: Upper Body A', 'Bench Press', 0, 4, 8, NULL),
(11, 2, 0, 'Day 1: Upper Body A', 'Bent-Over Row', 1, 4, 8, NULL),
(12, 2, 0, 'Day 1: Upper Body A', 'Overhead Press', 2, 3, 10, NULL),
(13, 2, 0, 'Day 1: Upper Body A', 'Barbell Curl', 3, 3, 12, NULL),
(14, 2, 1, 'Day 2: Lower Body A', 'Squat', 0, 4, 8, NULL),
(15, 2, 1, 'Day 2: Lower Body A', 'Romanian Deadlift', 1, 3, 10, NULL),
(16, 2, 1, 'Day 2: Lower Body A', 'Leg Extension', 2, 3, 12, NULL),
(17, 2, 1, 'Day 2: Lower Body A', 'Calf Raise', 3, 4, 15, NULL),
(18, 2, 2, 'Day 3: Rest', '', 0, NULL, NULL, NULL),
(19, 2, 3, 'Day 4: Upper Body B', 'Incline Dumbbell Press', 0, 4, 10, NULL),
(20, 2, 3, 'Day 4: Upper Body B', 'Pull-ups', 1, 4, 8, NULL),
(21, 2, 3, 'Day 4: Upper Body B', 'Dumbbell Lateral Raise', 2, 3, 12, NULL),
(22, 2, 3, 'Day 4: Upper Body B', 'Tricep Pushdown', 3, 3, 12, NULL),
(23, 2, 4, 'Day 5: Lower Body B', 'Leg Press', 0, 4, 12, NULL),
(24, 2, 4, 'Day 5: Lower Body B', 'Leg Curl', 1, 3, 12, NULL),
(25, 2, 4, 'Day 5: Lower Body B', 'Walking Lunges', 2, 3, 10, NULL),
(26, 2, 4, 'Day 5: Lower Body B', 'Plank', 3, 3, NULL, 60);

-- --------------------------------------------------------

--
-- Structure for view `upcoming_workouts`
--
DROP TABLE IF EXISTS `upcoming_workouts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `upcoming_workouts`  AS SELECT `us`.`schedule_id` AS `schedule_id`, `us`.`user_id` AS `user_id`, `u`.`username` AS `username`, `wp`.`plan_name` AS `plan_name`, `us`.`scheduled_date` AS `scheduled_date`, `us`.`is_completed` AS `is_completed`, to_days(`us`.`scheduled_date`) - to_days(curdate()) AS `days_until` FROM ((`user_schedules` `us` join `users` `u` on(`us`.`user_id` = `u`.`id`)) join `workout_plans` `wp` on(`us`.`plan_id` = `wp`.`plan_id`)) WHERE `us`.`scheduled_date` >= curdate() AND `us`.`is_completed` = 0 ORDER BY `us`.`scheduled_date` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `user_workout_stats`
--
DROP TABLE IF EXISTS `user_workout_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_workout_stats`  AS SELECT `u`.`id` AS `user_id`, `u`.`username` AS `username`, count(distinct `wp`.`plan_id`) AS `total_plans`, count(distinct `us`.`schedule_id`) AS `total_schedules`, sum(case when `us`.`is_completed` = 1 then 1 else 0 end) AS `completed_workouts`, max(`us`.`completed_at`) AS `last_workout_date` FROM ((`users` `u` left join `workout_plans` `wp` on(`u`.`id` = `wp`.`user_id`)) left join `user_schedules` `us` on(`u`.`id` = `us`.`user_id`)) GROUP BY `u`.`id`, `u`.`username` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_difficulty` (`difficulty`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `user_schedules`
--
ALTER TABLE `user_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`scheduled_date`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_scheduled_date` (`scheduled_date`),
  ADD KEY `idx_is_completed` (`is_completed`),
  ADD KEY `idx_user_date_completed` (`user_id`,`scheduled_date`,`is_completed`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `workout_plan_items`
--
ALTER TABLE `workout_plan_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_plan_id` (`plan_id`),
  ADD KEY `idx_day_index` (`day_index`),
  ADD KEY `idx_plan_day` (`plan_id`,`day_index`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_schedules`
--
ALTER TABLE `user_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workout_plan_items`
--
ALTER TABLE `workout_plan_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_schedules`
--
ALTER TABLE `user_schedules`
  ADD CONSTRAINT `user_schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_schedules_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`plan_id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD CONSTRAINT `workout_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plan_items`
--
ALTER TABLE `workout_plan_items`
  ADD CONSTRAINT `workout_plan_items_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`plan_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
