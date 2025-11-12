-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 08:20 PM
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
-- Database: `hobbyverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `hobbies`
--

CREATE TABLE `hobbies` (
  `hobby_id` int(11) NOT NULL,
  `hobby_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hobbies`
--

INSERT INTO `hobbies` (`hobby_id`, `hobby_name`, `description`) VALUES
(1, 'Painting', 'Explore colors and creativity'),
(2, 'Gardening', 'Grow your own plants'),
(3, 'Photography', 'Capture beautiful moments'),
(4, 'Crochet', 'The yarn is calling, and You must go.'),
(5, 'Sports', 'For the love of the game.'),
(6, 'Cars', 'Heaven for car lovers'),
(7, 'Reading', 'For those who find friends in books');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `hobby_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `hobby_id`, `product_name`, `price`, `description`, `image`) VALUES
(5, 1, 'Acrylic Paint Set', 500.00, '12 color set for painting', 'https://i.pinimg.com/1200x/fc/6d/2b/fc6d2bdf816547d384845bdab12f0bcc.jpg'),
(6, 1, 'Canvas Board', 200.00, 'Perfect for acrylic painting', 'https://i.pinimg.com/1200x/f9/b3/12/f9b312a63d08b6b951be63a0b2a20724.jpg'),
(7, 2, 'Flower Seeds Pack', 100.00, 'Grow seasonal flowers', 'https://i.pinimg.com/736x/1f/bf/fc/1fbffc8177f508a5c2019d5fb3dc5611.jpg'),
(8, 4, 'Crochet Gift Box', 500.00, 'Cute little crochet clip and rose coaster', 'https://i.pinimg.com/736x/6d/69/4f/6d694f3636ecc22d79dc21e09325cb0b.jpg'),
(9, 4, 'Crochet Sunflower Box', 1000.00, 'A sunflower pot,gift card and a cute pouch', 'https://i.pinimg.com/1200x/c6/8e/b9/c68eb94bd7b4077dba075bf2d343b5e9.jpg'),
(10, 4, 'Crochet flower Bouquet', 500.00, 'Flowers that will always stay with your loved ones', 'https://i.pinimg.com/736x/ce/4d/86/ce4d86fb71d86d0dcd681433792c5af4.jpg'),
(11, 1, 'Customized cover sketchbook', 500.00, 'Customized Embroided cover sketchbook', 'https://i.pinimg.com/1200x/f6/98/43/f69843a6b648357f80e901f42eae332b.jpg'),
(12, 1, 'Mini paint set', 1000.00, 'Pocket friendly paint set to take wherever you go', 'https://i.pinimg.com/1200x/53/9f/f4/539ff425e3dd2dcdeb6af8fcd06021a7.jpg'),
(13, 5, 'never stop- Tshirt ', 300.00, 'never stop quote tshirt', 'https://i.pinimg.com/736x/69/8e/d2/698ed221a0f3bd5ed4671535b9e8df52.jpg'),
(14, 5, 'Graphic tshirt', 350.00, 'real skater graphic tshirt', 'https://i.pinimg.com/1200x/dc/0f/ed/dc0fedccbe5e7525d5bd4844f5c7bbdd.jpg'),
(15, 5, 'Womens basketball jersey', 1200.00, 'basketball jersey Gender : women \r\n Colour : blue', 'https://i.pinimg.com/1200x/e6/03/7e/e6037e86a112f51765afd41d867ee4b3.jpg'),
(16, 5, 'Lakers basketball tshirt', 800.00, 'Gender : unisex\r\n colour: yellow', 'https://i.pinimg.com/1200x/3b/dd/fe/3bddfe492be20a3d4b9b360bc82bc0cd.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`hobby_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `hobby_id` (`hobby_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `hobby_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`hobby_id`) REFERENCES `hobbies` (`hobby_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
