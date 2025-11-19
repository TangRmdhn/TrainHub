-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 04:28 PM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `gender`, `age`, `weight`, `height`, `fitness_goal`, `fitness_level`, `equipment_access`, `days_per_week`, `minutes_per_session`, `injuries`, `created_at`) VALUES
(1, 'a', 'a@a.a', '$2y$10$g3w0YKKUWAibABNlhWPJ1.TWATT9kl4BwQN3GYNT2uM1m0hmjjgCq', 'Laki-laki', 20, 60.00, 170, 'Muscle Gain', 'Beginner', 'Home Dumbbell', 3, 30, '', '2025-11-15 12:45:45'),
(2, 'arsya', 'arsya@gmail.com', '$2y$10$CnqfI0yMk9jelXiuJ35YM.30pP/SVDb31S1cwKZibc3us23BS2Uky', 'Laki-laki', 70, 100.00, 18, 'Fat Loss', 'Beginner', 'Bodyweight Only', 5, 60, '', '2025-11-15 13:32:57'),
(3, 'admin', 'tophlvy1@gmail.com', '$2y$10$YybdCz8Xi6L24.88HXe/XuUJDY/vJlwn.7hg.kBLHL/l/MI3rQaUq', 'Laki-laki', 20, 60.00, 175, 'Muscle Gain', 'Beginner', 'Home Dumbbell', 3, 30, '', '2025-11-18 23:50:15'),
(4, 'indraprhmbd', 'arsya@abc.com', '$2y$10$du.wYuzOMgO9XatMivqgPujQqNf.gOTZpM0rKxoywr4AM.KJH5e9G', 'Laki-laki', 16, 80.00, 175, 'Endurance', 'Intermediate', 'Gym Lengkap', 6, 90, '', '2025-11-19 12:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_plans`
--

CREATE TABLE `user_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `finish_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `monday` longtext DEFAULT NULL,
  `tuesday` longtext DEFAULT NULL,
  `wednesday` longtext DEFAULT NULL,
  `thursday` longtext DEFAULT NULL,
  `friday` longtext DEFAULT NULL,
  `saturday` longtext DEFAULT NULL,
  `sunday` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_plans`
--

INSERT INTO `user_plans` (`id`, `user_id`, `plan_name`, `start_date`, `finish_date`, `created_at`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) VALUES
(3, 3, 'Home Dumbbell Muscle Gain - Beginner', '2025-11-19', '2025-12-16', '2025-11-19 07:16:25', '{\"week_number\":1,\"day_number\":1,\"day_name\":\"Senin\",\"session_title\":\"Full Body Dumbbell Workout\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Dumbbell Squats\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Bench Press\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Rows\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Shoulder Press\",\"sets\":\"3\",\"reps\":\"8-10\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Bicep Curls\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Tricep Extensions\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":2,\"day_name\":\"Selasa\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}', '{\"week_number\":1,\"day_number\":3,\"day_name\":\"Rabu\",\"session_title\":\"Full Body Dumbbell Workout\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Dumbbell Lunges\",\"sets\":\"3\",\"reps\":\"10-12 per leg\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Deadlifts\",\"sets\":\"1\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Flyes\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Lateral Raises\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Hammer Curls\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Overhead Tricep Extensions\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":4,\"day_name\":\"Kamis\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}', '{\"week_number\":1,\"day_number\":5,\"day_name\":\"Jumat\",\"session_title\":\"Full Body Dumbbell Workout\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Goblet Squats\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Incline Dumbbell Press\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Pullovers\",\"sets\":\"3\",\"reps\":\"10-12\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Arnold Press\",\"sets\":\"3\",\"reps\":\"8-10\",\"rest\":\"60s\"},{\"name\":\"Concentration Curls\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"},{\"name\":\"Close-Grip Dumbbell Press\",\"sets\":\"3\",\"reps\":\"12-15\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":6,\"day_name\":\"Sabtu\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}', '{\"week_number\":1,\"day_number\":7,\"day_name\":\"Minggu\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}'),
(6, 4, 'Endurance Booster - Intermediate', '2025-12-14', '2026-02-07', '2025-11-19 14:15:55', '{\"week_number\":1,\"day_number\":1,\"day_name\":\"Senin\",\"session_title\":\"Cardio & Core\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Running (Tempo Run)\",\"sets\":\"1\",\"reps\":\"30 minutes\",\"rest\":\"N\\/A\"},{\"name\":\"Plank\",\"sets\":\"3\",\"reps\":\"60 seconds hold\",\"rest\":\"30s\"},{\"name\":\"Russian Twists\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"},{\"name\":\"Bicycle Crunches\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"}]}', '{\"week_number\":1,\"day_number\":2,\"day_name\":\"Selasa\",\"session_title\":\"Leg Endurance\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Bodyweight Squats\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"45s\"},{\"name\":\"Walking Lunges\",\"sets\":\"3\",\"reps\":\"15 per leg\",\"rest\":\"45s\"},{\"name\":\"Calf Raises\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"},{\"name\":\"Box Jumps\",\"sets\":\"3\",\"reps\":\"10\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":3,\"day_name\":\"Rabu\",\"session_title\":\"Upper Body Endurance\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Push-Ups\",\"sets\":\"3\",\"reps\":\"As Many Reps as Possible (AMRAP)\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Rows\",\"sets\":\"3\",\"reps\":\"15\",\"rest\":\"45s\"},{\"name\":\"Overhead Press (Dumbbell)\",\"sets\":\"3\",\"reps\":\"15\",\"rest\":\"45s\"},{\"name\":\"Bicep Curls (Dumbbell)\",\"sets\":\"3\",\"reps\":\"15\",\"rest\":\"45s\"},{\"name\":\"Triceps Dips\",\"sets\":\"3\",\"reps\":\"AMRAP\",\"rest\":\"45s\"}]}', '{\"week_number\":1,\"day_number\":4,\"day_name\":\"Kamis\",\"session_title\":\"Active Recovery\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Light Cardio (Cycling\\/Swimming)\",\"sets\":\"1\",\"reps\":\"30 minutes\",\"rest\":\"N\\/A\"},{\"name\":\"Foam Rolling\",\"sets\":\"1\",\"reps\":\"15 minutes\",\"rest\":\"N\\/A\"},{\"name\":\"Stretching\",\"sets\":\"1\",\"reps\":\"15 minutes\",\"rest\":\"N\\/A\"}]}', '{\"week_number\":1,\"day_number\":5,\"day_name\":\"Jumat\",\"session_title\":\"Full Body Circuit\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Burpees\",\"sets\":\"3\",\"reps\":\"10\",\"rest\":\"60s\"},{\"name\":\"Mountain Climbers\",\"sets\":\"3\",\"reps\":\"30 seconds\",\"rest\":\"60s\"},{\"name\":\"Jumping Jacks\",\"sets\":\"3\",\"reps\":\"30 seconds\",\"rest\":\"60s\"},{\"name\":\"Squat Jumps\",\"sets\":\"3\",\"reps\":\"10\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":6,\"day_name\":\"Sabtu\",\"session_title\":\"Long Run\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Running (Continuous)\",\"sets\":\"1\",\"reps\":\"45-60 minutes\",\"rest\":\"N\\/A\"}]}', '{\"week_number\":1,\"day_number\":7,\"day_name\":\"Minggu\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}'),
(7, 4, 'Endurance Training - Intermediate', '2025-11-19', '2025-12-16', '2025-11-19 14:20:17', '{\"week_number\":1,\"day_number\":1,\"day_name\":\"Senin\",\"session_title\":\"Cardio & Core\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Treadmill Running\",\"sets\":\"1\",\"reps\":\"45 minutes\",\"rest\":null},{\"name\":\"Plank\",\"sets\":\"3\",\"reps\":\"60 seconds\",\"rest\":\"30s\"},{\"name\":\"Russian Twists\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"}]}', '{\"week_number\":1,\"day_number\":2,\"day_name\":\"Selasa\",\"session_title\":\"Lower Body Endurance\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Barbell Squats\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"60s\"},{\"name\":\"Leg Press\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"60s\"},{\"name\":\"Hamstring Curls\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"60s\"},{\"name\":\"Calf Raises\",\"sets\":\"4\",\"reps\":\"20-25\",\"rest\":\"45s\"}]}', '{\"week_number\":1,\"day_number\":3,\"day_name\":\"Rabu\",\"session_title\":\"Upper Body Endurance\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Bench Press\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"60s\"},{\"name\":\"Pull-ups (or Lat Pulldowns)\",\"sets\":\"3\",\"reps\":\"As many as possible \\/ 15-20\",\"rest\":\"60s\"},{\"name\":\"Overhead Press\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"60s\"},{\"name\":\"Bicep Curls\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"45s\"},{\"name\":\"Triceps Extensions\",\"sets\":\"3\",\"reps\":\"15-20\",\"rest\":\"45s\"}]}', '{\"week_number\":1,\"day_number\":4,\"day_name\":\"Kamis\",\"session_title\":\"Cardio & Core\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Cycling\",\"sets\":\"1\",\"reps\":\"45 minutes\",\"rest\":null},{\"name\":\"Crunches\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"},{\"name\":\"Leg Raises\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"30s\"}]}', '{\"week_number\":1,\"day_number\":5,\"day_name\":\"Jumat\",\"session_title\":\"Full Body Circuit\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Burpees\",\"sets\":\"3\",\"reps\":\"15\",\"rest\":\"60s\"},{\"name\":\"Kettlebell Swings\",\"sets\":\"3\",\"reps\":\"20\",\"rest\":\"60s\"},{\"name\":\"Dumbbell Lunges\",\"sets\":\"3\",\"reps\":\"12 per leg\",\"rest\":\"60s\"},{\"name\":\"Push-ups\",\"sets\":\"3\",\"reps\":\"15\",\"rest\":\"60s\"}]}', '{\"week_number\":1,\"day_number\":6,\"day_name\":\"Sabtu\",\"session_title\":\"Active Recovery\",\"is_off_day\":false,\"exercises\":[{\"name\":\"Light Jogging\",\"sets\":\"1\",\"reps\":\"30 minutes\",\"rest\":null},{\"name\":\"Foam Rolling\",\"sets\":\"1\",\"reps\":\"15 minutes\",\"rest\":null}]}', '{\"week_number\":1,\"day_number\":7,\"day_name\":\"Minggu\",\"session_title\":\"Rest Day\",\"is_off_day\":true,\"exercises\":[]}');

-- --------------------------------------------------------

--
-- Table structure for table `workout_logs`
--

CREATE TABLE `workout_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_logs`
--

INSERT INTO `workout_logs` (`id`, `user_id`, `plan_id`, `date`, `status`, `created_at`) VALUES
(1, 4, 7, '2025-11-19', 'completed', '2025-11-19 14:57:24'),
(2, 4, 7, '2025-11-20', 'completed', '2025-11-19 14:58:08'),
(3, 4, 7, '2025-11-21', 'completed', '2025-11-19 14:58:11'),
(4, 4, 7, '2025-11-29', 'completed', '2025-11-19 15:16:57'),
(5, 4, 7, '2025-11-30', 'completed', '2025-11-19 15:17:01'),
(6, 4, 7, '2025-11-23', 'completed', '2025-11-19 15:17:05'),
(7, 4, 7, '2025-11-28', 'completed', '2025-11-19 15:17:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`);

--
-- Indexes for table `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_log` (`user_id`,`plan_id`,`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_plans`
--
ALTER TABLE `user_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD CONSTRAINT `user_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
