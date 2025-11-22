-- Migration: Normalize Users and Plans Tables

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Create user_profiles table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gender` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `fitness_goal` varchar(50) DEFAULT NULL,
  `fitness_level` varchar(50) DEFAULT NULL,
  `equipment_access` varchar(50) DEFAULT NULL,
  `days_per_week` int(11) DEFAULT 3,
  `minutes_per_session` int(11) DEFAULT 60,
  `injuries` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_profile_user` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Migrate Data from users to user_profiles
-- --------------------------------------------------------
INSERT INTO `user_profiles` (user_id, gender, age, weight, height, fitness_goal, fitness_level, equipment_access, days_per_week, minutes_per_session, injuries)
SELECT id, gender, age, weight, height, fitness_goal, fitness_level, equipment_access, days_per_week, minutes_per_session, injuries
FROM `users`
WHERE fitness_goal IS NOT NULL; -- Only migrate if profile data exists

-- --------------------------------------------------------
-- 3. Clean up users table
-- --------------------------------------------------------
ALTER TABLE `users`
  DROP COLUMN `gender`,
  DROP COLUMN `age`,
  DROP COLUMN `weight`,
  DROP COLUMN `height`,
  DROP COLUMN `fitness_goal`,
  DROP COLUMN `fitness_level`,
  DROP COLUMN `equipment_access`,
  DROP COLUMN `days_per_week`,
  DROP COLUMN `minutes_per_session`,
  DROP COLUMN `injuries`;

-- --------------------------------------------------------
-- 4. Modify user_plans table
-- --------------------------------------------------------
-- Add coach_note and drop JSON columns
ALTER TABLE `user_plans`
  ADD COLUMN `coach_note` TEXT DEFAULT NULL AFTER `finish_date`,
  DROP COLUMN `monday`,
  DROP COLUMN `tuesday`,
  DROP COLUMN `wednesday`,
  DROP COLUMN `thursday`,
  DROP COLUMN `friday`,
  DROP COLUMN `saturday`,
  DROP COLUMN `sunday`;

-- --------------------------------------------------------
-- 5. Create plan_days table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plan_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL COMMENT '1=Monday, 7=Sunday',
  `day_title` varchar(100) DEFAULT NULL,
  `is_off` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_plan_days_plan` (`plan_id`),
  CONSTRAINT `plan_days_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `user_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Create plan_exercises table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `plan_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sets` varchar(50) DEFAULT NULL,
  `reps` varchar(50) DEFAULT NULL,
  `rest` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_exercises_day` (`day_id`),
  CONSTRAINT `plan_exercises_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `plan_days` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
