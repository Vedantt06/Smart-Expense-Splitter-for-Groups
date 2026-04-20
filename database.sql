-- Database: `smart_expense_db`
CREATE DATABASE IF NOT EXISTS `smart_expense_db`;
USE `smart_expense_db`;

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `groups`
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `group_members`
CREATE TABLE IF NOT EXISTS `group_members` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`, `user_id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `expenses`
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `paid_by` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`paid_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `expense_splits`
CREATE TABLE IF NOT EXISTS `expense_splits` (
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_owed` decimal(10,2) NOT NULL,
  PRIMARY KEY (`expense_id`, `user_id`),
  FOREIGN KEY (`expense_id`) REFERENCES `expenses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
