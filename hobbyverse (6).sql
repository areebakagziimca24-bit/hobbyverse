-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 08:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `hobby_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `hobby_image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hobbies`
--

INSERT INTO `hobbies` (`hobby_id`, `hobby_name`, `description`, `hobby_image`) VALUES
(1, 'Painting', 'Explore colors and creativity', NULL),
(2, 'Gardening', 'Grow your own plants', NULL),
(3, 'Photography', 'Capture beautiful moments', NULL),
(4, 'Crochet', 'The yarn is calling, and you must go', NULL),
(5, 'Sports', 'For the love of the game', NULL),
(6, 'Cars', 'Heaven for car lovers', NULL),
(7, 'Reading', 'For those who find friends in books', NULL),
(8, 'Cooking', 'Explore flavors and create delicious meals', NULL),
(9, 'Musical Instruments', 'Create magical sounds with musical instruments', NULL),
(10, 'Fitness', 'Stay active and healthy with workouts and exercise', NULL),
(11, 'Pets', 'Everything your furry friends need', NULL),
(12, 'Calligraphy', 'The art of beautiful writing and lettering', NULL),
(13, 'DIY Crafts', 'Create handmade crafts using creativity and tools', NULL),
(14, 'Technology', 'Explore gadgets and modern accessories', NULL),
(15, 'Collectibles', 'Unique items for collectors and enthusiasts', NULL),
(16, 'Digital Art', 'Learn and create digital illustrations, concept art, and graphic designs using tablets and software.', 'https://i.pinimg.com/564x/1c/8f/7f/1c8f7fbf4e22d2bf17e21a51cc5df24d.jpg'),
(17, 'Resin Art', 'Create beautiful resin crafts including coasters, jewelry, and decor items.', 'https://i.pinimg.com/564x/47/f2/cc/47f2ccdb0dbf50708a8bffaf77de9e1d.jpg'),
(18, 'DIY Home Decor', 'Craft and design custom home decor pieces using affordable materials.', 'https://i.pinimg.com/564x/cf/23/3f/cf233f8657bf78490d71247a3f2d1dc2.jpg'),
(19, 'Candle Making', 'Make scented, decorative, and soy candles at home.', 'https://i.pinimg.com/564x/1a/b0/e4/1ab0e44c0dd9923d6ff461fe1aaea911.jpg'),
(20, 'Jewelry Making', 'Design handcrafted necklaces, bracelets, earrings, and accessories.', 'https://i.pinimg.com/564x/0b/c7/22/0bc7223a0b5e90e42a7dfcbd321b63e3.jpg'),
(21, '3D Printing', 'Explore designing and printing custom 3D objects and prototypes.', 'https://i.pinimg.com/564x/4b/1d/83/4b1d83da8cb0ef6510836422b2b1f566.jpg'),
(22, 'Astronomy', 'Learn stargazing, telescope usage, and deep-sky observation.', 'https://i.pinimg.com/564x/0e/96/5c/0e965c982a46f2848982f8cc5d4ef41f.jpg'),
(23, 'Drone Photography', 'Capture professional aerial photos and videos using drones.', 'https://i.pinimg.com/564x/b8/2f/86/b82f864c2c97496a9b92e1157596400b.jpg'),
(24, 'Video Editing', 'Edit cinematic videos, reels, and short films using editing tools.', 'https://i.pinimg.com/564x/7a/78/5e/7a785e839b5cbb61f9e8e2d5ae049631.jpg'),
(25, 'Anime & K-Pop Collectibles', 'Collect official figures, posters, merch, and albums.', 'https://i.pinimg.com/564x/e6/ba/b7/e6bab7cd9a3e7924a36b1a3b812cd92f.jpg'),
(26, 'Makeup & Beauty', 'Learn makeup artistry, skincare routines, and product styling.', 'https://i.pinimg.com/564x/cc/5e/c8/cc5ec8df44e45a24c0e59b44281a28e4.jpg'),
(27, 'Advanced Cooking', 'Master various cuisines and high-level cooking techniques.', 'https://i.pinimg.com/564x/13/7f/12/137f12efae27324b34b3f670e323a998.jpg'),
(28, 'Smart Gardening', 'Grow plants indoors using hydroponics and smart tools.', 'https://i.pinimg.com/564x/bd/eb/25/bdeb25f2afdd0f7450cdaff4a737abf6.jpg'),
(29, 'Outdoor Camping', 'Explore camping gear, survival kits, and outdoor adventures.', 'https://i.pinimg.com/564x/93/65/87/93658751d895f1d1bb157ccd44fcbb40.jpg'),
(30, 'Handmade Soap Making', 'Create natural and scented soaps with custom designs.', 'https://i.pinimg.com/564x/59/e1/c6/59e1c6e6a3e055852da5c81b5dd8f635.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `payment_method` enum('cod','upi','card') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_name`, `email`, `phone`, `address`, `city`, `payment_method`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 'areebakagzi', 'areebakagzi@gmail.com', '9016236095', '11/2008', 'surat', 'cod', 499.00, 'pending', '2025-11-25 14:00:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 1, 499.00, 499.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `hobby_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `hobby_id`, `product_name`, `price`, `description`, `image`, `stock`, `created_at`) VALUES
(1, 1, 'Acrylic Paint Tube Set', 499.00, '24-color premium acrylic paint set suitable for canvas & paper.', 'https://i.pinimg.com/736x/6e/92/4b/6e924b11472edbba7d6851124c596cb8.jpg', 11, '2025-11-25 13:59:48'),
(2, 1, 'Canvas Board Pack (Pack of 3)', 350.00, 'High-quality primed canvases perfect for beginners & artists.', 'https://i.pinimg.com/736x/60/3e/71/603e7174f0d27bc39b29c2f9b499d4dc.jpg', 15, '2025-11-25 13:59:48'),
(3, 1, 'Professional Paint Brush Set', 299.00, '10-piece fine detail paint brushes for acrylic & watercolor.', 'https://i.pinimg.com/736x/45/d8/4d/45d84d2ab1a826ffba7ddfbaf0c8228d.jpg', 20, '2025-11-25 13:59:48'),
(4, 1, 'Tabletop Wooden Easel', 699.00, 'Portable mini easel for painting, sketching, and display.', 'https://i.pinimg.com/736x/2e/37/ec/2e37ecbe5978b8fcf6a4a7bb8ca54097.jpg', 8, '2025-11-25 13:59:48'),
(5, 1, 'Watercolor Palette Kit', 420.00, '36-shade watercolors with palette and brush.', 'https://i.pinimg.com/736x/46/1d/9c/461d9ccaa14aa48940f19fee30ed3f3f.jpg', 18, '2025-11-25 13:59:48'),
(6, 1, 'Sketching & Shading Pencil Set', 250.00, '12-pencil graphite set for drawing and shading.', 'https://i.pinimg.com/736x/df/26/27/df2627375cb70b4d868deea84597b614.jpg', 25, '2025-11-25 13:59:48'),
(7, 2, 'Flower Seed Mix Pack', 120.00, 'Seasonal mixed flower seeds.', 'https://i.pinimg.com/736x/2a/7c/d7/2a7cd7d36a9d86c55b3e2f86b1fcf3e0.jpg', 20, '2025-11-25 16:22:30'),
(8, 2, 'Mini Watering Can', 260.00, 'Soft-flow watering can for plants.', 'https://i.pinimg.com/736x/20/dd/de/20dddec3482f5d2b3d27d9b1d3245c89.jpg', 15, '2025-11-25 16:22:30'),
(9, 2, 'Organic Fertilizer', 300.00, 'Plant-safe nutrient-rich fertilizer.', 'https://i.pinimg.com/736x/f2/bd/dc/f2bddcbf0173c64b9d8f67c527969b5e.jpg', 10, '2025-11-25 16:22:30'),
(10, 2, 'Clay Pot Set (3 Pack)', 450.00, 'Durable clay pots for home gardening.', 'https://i.pinimg.com/736x/8b/76/5b/8b765bfc2a955f192d08bc7569b0ce3e.jpg', 12, '2025-11-25 16:22:30'),
(11, 2, 'Hand Rake & Tools Kit', 350.00, 'Essential gardening tool kit.', 'https://i.pinimg.com/736x/f3/60/7c/f3607cd8e5abf8e82675d5797c97fd88.jpg', 10, '2025-11-25 16:22:30'),
(12, 2, 'Indoor Plant Sprayer', 180.00, 'Fine mist sprayer for delicate plants.', 'https://i.pinimg.com/736x/62/70/0a/62700a50199bfa68e2db279b1091d651.jpg', 14, '2025-11-25 16:22:30'),
(13, 3, 'Tripod Stand', 900.00, 'Lightweight aluminum tripod.', 'https://i.pinimg.com/736x/1e/25/88/1e25887e885e1f78bcbf68748d2a4e98.jpg', 8, '2025-11-25 16:23:19'),
(14, 3, 'Camera Cleaning Kit', 350.00, 'Soft brush and lens wipes.', 'https://i.pinimg.com/736x/41/0e/68/410e68cbc84e0c6a859fafa9834b5672.jpg', 20, '2025-11-25 16:23:19'),
(15, 3, 'Lens Protector', 480.00, 'Ultra-clear UV filter for lenses.', 'https://i.pinimg.com/736x/9b/5f/7c/9b5f7c73bebaf526aa9c7b519081ed0a.jpg', 12, '2025-11-25 16:23:19'),
(16, 3, 'Mini LED Light', 520.00, 'Portable LED lighting for photography.', 'https://i.pinimg.com/736x/89/e3/21/89e3210307d53bc1454c661d6c977a84.jpg', 10, '2025-11-25 16:23:19'),
(17, 3, 'SD Card 64GB', 700.00, 'High-speed memory card.', 'https://i.pinimg.com/736x/28/f4/fb/28f4fbf770e27c90544e3363a76cddae.jpg', 15, '2025-11-25 16:23:19'),
(18, 3, 'Camera Strap', 260.00, 'Adjustable padded camera strap.', 'https://i.pinimg.com/736x/17/30/38/173038a486cdee024aac246d106fc660.jpg', 14, '2025-11-25 16:23:19'),
(19, 4, 'Wool Yarn Bundle', 550.00, 'Soft premium wool for crocheting.', 'https://i.pinimg.com/736x/7a/72/8d/7a728dc22beff2d7fba40c97c7c49118.jpg', 18, '2025-11-25 16:23:30'),
(20, 4, 'Crochet Hook Set', 300.00, '8-piece metal crochet hook set.', 'https://i.pinimg.com/736x/18/f5/58/18f5580f962a3f1cdd192d08bc7569b0.jpg', 15, '2025-11-25 16:23:30'),
(21, 4, 'DIY Crochet Flower Kit', 700.00, 'Complete set to make crochet flowers.', 'https://i.pinimg.com/736x/ef/25/72/ef2572b74ba704e72f34c5bb1d5b59da.jpg', 10, '2025-11-25 16:23:30'),
(22, 4, 'Mini Yarn Bag', 250.00, 'Portable yarn storage bag.', 'https://i.pinimg.com/736x/92/3e/71/923e719a2d6e9f0c7cbb18ce87140fe5.jpg', 20, '2025-11-25 16:23:30'),
(23, 4, 'Stitch Marker Pack', 150.00, 'Colorful stitch markers for crochet.', 'https://i.pinimg.com/736x/33/2e/c1/332ec10417b735de57ef8558d6f21a78.jpg', 25, '2025-11-25 16:23:30'),
(24, 4, 'Crochet Pattern Book', 600.00, 'Beginner-friendly crochet patterns.', 'https://i.pinimg.com/736x/2f/64/4c/2f644c57443e7fd799a1a3d5b5b34975.jpg', 12, '2025-11-25 16:23:30'),
(25, 5, 'Football', 900.00, 'Professional size 5 football.', 'https://i.pinimg.com/736x/d0/20/2c/d0202c2064767998df1ebe19cc2c8d45.jpg', 10, '2025-11-25 16:23:41'),
(26, 5, 'Sports Water Bottle', 260.00, 'Leak-proof BPA-free bottle.', 'https://i.pinimg.com/736x/d0/0a/3e/d00a3e7668e1f4c9c8e57bd6525f364a.jpg', 25, '2025-11-25 16:23:41'),
(27, 5, 'Running Shoes', 1800.00, 'Lightweight running shoes.', 'https://i.pinimg.com/736x/17/ff/8d/17ff8dc90f45b52fba0a8023fa029e61.jpg', 6, '2025-11-25 16:23:41'),
(28, 5, 'Badminton Racket', 950.00, 'Aluminum badminton racket.', 'https://i.pinimg.com/736x/ce/63/c0/ce63c01b4f49e7ce3e13a17bac3dbe8a.jpg', 10, '2025-11-25 16:23:41'),
(29, 5, 'Skipping Rope', 150.00, 'Adjustable lightweight rope.', 'https://i.pinimg.com/736x/41/1e/ea/411eeaa14fb9050bc0f25c5e81c14f10.jpg', 30, '2025-11-25 16:23:41'),
(30, 5, 'Cricket Tennis Ball Pack', 220.00, 'Pack of 3 cricket balls.', 'https://i.pinimg.com/736x/7b/ea/6c/7bea6c4922ce9cba07039e89165b0def.jpg', 20, '2025-11-25 16:23:41'),
(31, 6, 'Mini Car Model', 550.00, 'Collectible metal car model.', 'https://i.pinimg.com/736x/af/3b/f5/af3bf52c31c8ebeaefad0132e29156c7.jpg', 15, '2025-11-25 16:23:52'),
(32, 6, 'Car Air Freshener', 180.00, 'Long-lasting fresh scent.', 'https://i.pinimg.com/736x/02/f1/0c/02f10c6bb8aeb2b1126eacec5c5e8f11.jpg', 30, '2025-11-25 16:23:52'),
(33, 6, 'Dashboard Toy', 220.00, 'Cute dashboard bobblehead toy.', 'https://i.pinimg.com/736x/30/7d/b4/307db4b2e976e8f0f65d92a0b2ecfac1.jpg', 25, '2025-11-25 16:23:52'),
(34, 6, 'Car Cleaning Kit', 750.00, 'Microfiber cloth + cleaner.', 'https://i.pinimg.com/736x/a6/74/8e/a6748e65b30a40c282f6bcdfa2db4aa0.jpg', 10, '2025-11-25 16:23:52'),
(35, 6, 'LED Car Light Strips', 650.00, 'Interior RGB ambient lights.', 'https://i.pinimg.com/736x/79/50/ed/7950edfad236d1ffdbded2aa7c23678c.jpg', 12, '2025-11-25 16:23:52'),
(36, 6, 'Car Keychain', 120.00, 'Premium metallic keychain.', 'https://i.pinimg.com/736x/dc/46/74/dc4674c77a5e0f30c7c6e996e173600f.jpg', 50, '2025-11-25 16:23:52'),
(37, 7, 'Classic Novel Set', 900.00, 'Set of 3 timeless classic novels.', 'https://i.pinimg.com/736x/4d/04/77/4d0477e03c48baccd07f4e5d2df88563.jpg', 12, '2025-11-25 16:24:03'),
(38, 7, 'Reading Lamp', 450.00, 'Warm LED light for night reading.', 'https://i.pinimg.com/736x/5c/0e/9e/5c0e9ec51a6cd04e2a5a81d8bbcf0bd8.jpg', 20, '2025-11-25 16:24:03'),
(39, 7, 'Magnetic Bookmark Pack', 180.00, 'Cute magnetic bookmarks.', 'https://i.pinimg.com/736x/19/cc/44/19cc442ff1e6f3e2e3a6b6f66a7d89e2.jpg', 18, '2025-11-25 16:24:03'),
(40, 7, 'Book Stand', 350.00, 'Portable book holder stand.', 'https://i.pinimg.com/736x/78/52/13/785213c1b64a3bd1dcd8e762bf6ea09d.jpg', 10, '2025-11-25 16:24:03'),
(41, 7, 'Notebook & Pen Set', 300.00, 'Hardcover notebook with pen.', 'https://i.pinimg.com/736x/1f/3c/d2/1f3cd2ad4bb262ba2b43f5201fa89f30.jpg', 25, '2025-11-25 16:24:03'),
(42, 7, 'Premium Journal', 600.00, 'Soft-touch leather journal.', 'https://i.pinimg.com/736x/44/ef/5d/44ef5df0d4354da3a2e2c58e445b3f55.jpg', 15, '2025-11-25 16:24:03'),
(43, 8, 'Stainless Steel Pan Set', 1500.00, 'Durable non-stick cookware set for everyday cooking.', 'https://i.pinimg.com/564x/af/cc/35/afcc3548e2aae60f2e7ddc5c11e84b27.jpg', 10, '2025-11-25 17:05:16'),
(44, 8, 'Herb & Spice Grinder', 450.00, 'Manual grinder ideal for fresh spices.', 'https://i.pinimg.com/564x/60/2d/00/602d005e4cfe1e6e1b4cfbb748b3eaf5.jpg', 15, '2025-11-25 17:05:16'),
(45, 8, 'Ceramic Mixing Bowl Set', 700.00, 'Microwave-safe colorful mixing bowls.', 'https://i.pinimg.com/564x/10/8d/10/108d106964e1f87debbd897d05e4a2af.jpg', 12, '2025-11-25 17:05:16'),
(46, 8, 'Chef Apron Premium', 300.00, 'Waterproof and heat-resistant apron.', 'https://i.pinimg.com/564x/2b/90/ed/2b90ed4b7ff628f6ba674fbec6ff157c.jpg', 20, '2025-11-25 17:05:16'),
(47, 8, 'Kitchen Measuring Cups', 250.00, 'High precision stainless measurement set.', 'https://i.pinimg.com/564x/15/13/d4/1513d43b12075b1219777084ff57c4b5.jpg', 25, '2025-11-25 17:05:16'),
(48, 8, 'Oil Sprayer Bottle', 200.00, 'Reusable sprayer for healthy, oil-controlled cooking.', 'https://i.pinimg.com/564x/44/09/99/440999d03c2103560e3f4a5843c12c94.jpg', 18, '2025-11-25 17:05:16'),
(49, 9, 'Beginner Drum Sticks', 300.00, 'Durable wooden drum sticks.', 'https://i.pinimg.com/564x/3a/0d/52/3a0d52b0b144e2aef88d61b51d5f81da.jpg', 25, '2025-11-25 17:05:27'),
(50, 9, 'Violin Practice Mute', 200.00, 'Reduces noise for indoor practice.', 'https://i.pinimg.com/564x/39/e1/a4/39e1a42e3d315919aa7a06adf4ae09f8.jpg', 30, '2025-11-25 17:05:27'),
(51, 9, 'Guitar Wall Mount', 350.00, 'Safe and strong wooden guitar mount.', 'https://i.pinimg.com/564x/89/cd/f0/89cdf023bdd218e8a3d8a0cb9f01e2c6.jpg', 15, '2025-11-25 17:05:27'),
(52, 9, 'Keyboard Stand X-Frame', 900.00, 'Heavy-duty stand for digital pianos.', 'https://i.pinimg.com/564x/c4/fd/56/c4fd56bccc4caacbd6837d9193c2ac9c.jpg', 10, '2025-11-25 17:05:27'),
(53, 9, 'Microphone Pop Filter', 450.00, 'Ideal for singing and voice-over.', 'https://i.pinimg.com/564x/54/82/28/548228b08d35e8fc3e3b71f6d6c9fcb4.jpg', 18, '2025-11-25 17:05:27'),
(54, 9, 'Bass Guitar Strap', 500.00, 'Comfortable padded strap for long playing.', 'https://i.pinimg.com/564x/91/a9/48/91a94840be2257b5c68e7f1d649ad038.jpg', 14, '2025-11-25 17:05:27'),
(55, 10, 'Foam Roller', 400.00, 'Perfect for muscle recovery & stretching.', 'https://i.pinimg.com/564x/ed/0a/0b/ed0a0b1d56641e6dc6a3573b5511b47f.jpg', 20, '2025-11-25 17:05:45'),
(56, 10, 'Hand Grip Strengthener', 250.00, 'Increase finger and forearm strength.', 'https://i.pinimg.com/564x/68/2a/9a/682a9a890cb54d3935980596afe9c087.jpg', 30, '2025-11-25 17:05:45'),
(57, 10, 'Sports Water Bottle', 300.00, 'BPA-free durable hydration bottle.', 'https://i.pinimg.com/564x/ca/87/64/ca8764b6e290b4aea9a6c72ca5.js', 40, '2025-11-25 17:05:45'),
(58, 10, 'Gym Gloves', 350.00, 'Anti-slip padded workout gloves.', 'https://i.pinimg.com/564x/0b/26/55/0b2655ad6b079f8c3d3233cd0c893d87.jpg', 15, '2025-11-25 17:05:45'),
(59, 10, 'Resistance Tube Kit', 550.00, 'Set of 5 heavy-duty resistance tubes.', 'https://i.pinimg.com/564x/4b/ca/35/4bca35e8cdb8a1c7f02caeaa8e2a1a3c.jpg', 12, '2025-11-25 17:05:45'),
(60, 10, 'Push-up Board', 1000.00, 'Full-body multifunction push-up trainer.', 'https://i.pinimg.com/564x/54/e5/8d/54e58d55b8bb9807e21ae9c7f9f6a65e.jpg', 10, '2025-11-25 17:05:45'),
(61, 11, 'Pet Nail Clipper', 300.00, 'Safe and easy nail trimming tool.', 'https://i.pinimg.com/564x/6b/44/58/6b4458b0b760797faf2a7af38d5bb5b3.jpg', 20, '2025-11-25 17:05:55'),
(62, 11, 'Pet Blanket', 450.00, 'Soft and warm fleece blanket.', 'https://i.pinimg.com/564x/f6/5f/2c/f65f2c5e928958f75aace0bbd10b1918.jpg', 16, '2025-11-25 17:05:55'),
(63, 11, 'Cat Litter Scoop', 150.00, 'Durable scooper for cat hygiene.', 'https://i.pinimg.com/564x/df/b0/93/dfb0935bc7c2ab416f2b32e2dbf2c9a7.jpg', 30, '2025-11-25 17:05:55'),
(64, 11, 'Dog Rope Toy', 250.00, 'Strong chew-resistant rope toy.', 'https://i.pinimg.com/564x/5c/fb/63/5cfb63c54adb5c9c8e29c9091cf07b88.jpg', 25, '2025-11-25 17:05:55'),
(65, 11, 'Pet Travel Bowl', 200.00, 'Foldable silicone pet food bowl.', 'https://i.pinimg.com/564x/3b/73/8f/3b738f864f07e6722d6ceb31a8d82d78.jpg', 18, '2025-11-25 17:05:55'),
(66, 11, 'Pet Shampoo Brush', 320.00, 'Soft bristles for deep cleaning.', 'https://i.pinimg.com/564x/0b/1a/06/0b1a06065b5556d4a67708e5f5f70023.jpg', 15, '2025-11-25 17:05:55'),
(67, 12, 'Lettering Practice Sheets', 250.00, 'Reusable practice sheet pack.', 'https://i.pinimg.com/564x/a2/41/43/a24143be074b5a0fd893111f5d3bd8ed.jpg', 30, '2025-11-25 17:06:28'),
(68, 12, 'Brush Calligraphy Pen', 350.00, 'Soft brush pen for artists.', 'https://i.pinimg.com/564x/2b/9c/13/2b9c137c83493563b1dfbf81d31ba55d.jpg', 20, '2025-11-25 17:06:28'),
(69, 12, 'Ink Dip Glass Pen', 500.00, 'Elegant handmade writing pen.', 'https://i.pinimg.com/564x/80/7e/42/807e42d9b8cddc00b581aba3f1003ea1.jpg', 12, '2025-11-25 17:06:28'),
(70, 12, 'Gold Foil Pen', 450.00, 'Metallic pen for premium lettering.', 'https://i.pinimg.com/564x/0e/af/f2/0eaff2fe65c79e0acb93d5b9d5e3ddfb.jpg', 18, '2025-11-25 17:06:28'),
(71, 12, 'A5 Art Notebook', 300.00, 'High-GSM premium paper notebook.', 'https://i.pinimg.com/564x/dc/c6/d6/dcc6d6ad584673449cf34c78b3d9fc0e.jpg', 22, '2025-11-25 17:06:28'),
(72, 12, 'Ink Cartridge Pack', 200.00, 'Set of 6 ink refills.', 'https://i.pinimg.com/564x/48/1d/ca/481dcac663e321ea32a5c81b4784ca1a.jpg', 25, '2025-11-25 17:06:28'),
(73, 13, 'Craft Cutter Tool', 350.00, 'Sharp & safe cutter for craft work.', 'https://i.pinimg.com/564x/b4/fa/d0/b4fad0b508698a509a68e2db279b109b.jpg', 22, '2025-11-25 17:06:28'),
(74, 13, 'Mini Glue Sticks', 150.00, '10-piece pack for glue guns.', 'https://i.pinimg.com/564x/0e/b2/60/0eb260a1b2b2714daaf8e9cb33bd583a.jpg', 40, '2025-11-25 17:06:28'),
(75, 13, 'Decorative Washi Tapes', 200.00, 'Set of 8 colorful tapes.', 'https://i.pinimg.com/564x/3d/14/df/3d14df1a6274953b4e6421a83090e8e3.jpg', 30, '2025-11-25 17:06:28'),
(76, 13, 'Beginner DIY Craft Kit', 800.00, 'Everything you need for crafting.', 'https://i.pinimg.com/564x/a7/04/ce/a704ced6de1643190cfb5f27eca04ac2.jpg', 10, '2025-11-25 17:06:28'),
(77, 13, 'Ribbon Roll Pack', 250.00, 'Premium fabric ribbons for projects.', 'https://i.pinimg.com/564x/99/50/76/9950763de7ab8f3aa7238d4df8857d1d.jpg', 25, '2025-11-25 17:06:28'),
(78, 13, 'DIY Charm Making Set', 600.00, 'Create your own accessories.', 'https://i.pinimg.com/564x/f5/1f/6f/f51f6f8b120e81dfd9c3c25ef7fb85a8.jpg', 12, '2025-11-25 17:06:28'),
(79, 14, 'Portable SSD 500GB', 2500.00, 'High-speed external SSD.', 'https://i.pinimg.com/564x/43/9b/03/439b03c2b88ea66bb4411a275df0c2c6.jpg', 10, '2025-11-25 17:06:29'),
(80, 14, 'USB RGB Light Strip', 300.00, 'LED strip for desk setup.', 'https://i.pinimg.com/564x/03/8c/96/038c9664ac3a793bcee66475d27be27b.jpg', 25, '2025-11-25 17:06:29'),
(81, 14, 'Laptop Stand Adjustable', 700.00, 'Ergonomic aluminum stand.', 'https://i.pinimg.com/564x/fb/18/51/fb185190dcf3db784befa5b13d033450.jpg', 12, '2025-11-25 17:06:29'),
(82, 14, 'Wireless Charging Pad', 650.00, 'Fast charging compatible pad.', 'https://i.pinimg.com/564x/ae/3a/25/ae3a25d2a0ef2a3d60dbbda3dbd1caba.jpg', 18, '2025-11-25 17:06:29'),
(83, 14, 'Noise Cancelling Headphones', 1500.00, 'Premium immersive sound.', 'https://i.pinimg.com/564x/0c/4a/b1/0c4ab1c8e65c0eb9c5530dddfd1b2fbd.jpg', 8, '2025-11-25 17:06:29'),
(84, 14, 'Bluetooth Speaker', 900.00, 'Portable speaker with deep bass.', 'https://i.pinimg.com/564x/70/0d/ca/700dcad9d3e0144e8848f29b3580bb93.jpg', 14, '2025-11-25 17:06:29'),
(85, 15, 'Mini Action Figure', 600.00, 'Detailed collector’s figure.', 'https://i.pinimg.com/564x/ba/65/f4/ba65f450f2c82a2ce9e1a8d09efb52e5.jpg', 10, '2025-11-25 17:06:29'),
(86, 15, 'Vintage Coin Set', 800.00, 'Rare collectible coins.', 'https://i.pinimg.com/564x/48/ce/8a/48ce8a97a5dc8155f85b2e1fa53232c1.jpg', 8, '2025-11-25 17:06:29'),
(87, 15, 'Anime Badge Pack', 250.00, 'Set of 5 premium badges.', 'https://i.pinimg.com/564x/2e/d1/43/2ed14398cbaad48bd10cffd618dce6f0.jpg', 25, '2025-11-25 17:06:29'),
(88, 15, 'Miniature Car Model', 900.00, 'Metal diecast collectible car.', 'https://i.pinimg.com/564x/97/97/1d/97971dfdbbb0c84f0aa1e4fc12369cc2.jpg', 12, '2025-11-25 17:06:29'),
(89, 15, 'Vintage Stamp Pack', 300.00, 'Assorted rare stamps.', 'https://i.pinimg.com/564x/af/5d/56/af5d5605bdbd59550fea10e25e7c3063.jpg', 30, '2025-11-25 17:06:29'),
(90, 15, 'Collector Display Stand', 450.00, 'Perfect for showcasing collectibles.', 'https://i.pinimg.com/564x/39/62/1d/39621d581a6a4e6987097cb449b656e4.jpg', 18, '2025-11-25 17:06:29'),
(91, 16, 'Digital Drawing Tablet', 3500.00, 'Perfect digital tablet for beginners and artists.', 'https://i.pinimg.com/564x/59/34/9f/59349ffd3dfdff9a0d1b3c2f43df2cdf.jpg', 10, '2025-11-25 19:23:39'),
(92, 16, 'Stylus Pen for Tablets', 800.00, 'Smooth pressure-sensitive stylus for sketching.', 'https://i.pinimg.com/564x/23/ab/bf/23abbfd6bf9e33dc8f29c3c2fc3a7ca1.jpg', 15, '2025-11-25 19:23:39'),
(93, 16, 'Digital Art Brush Pack', 500.00, 'Professional brush set for Procreate & Photoshop.', 'https://i.pinimg.com/564x/6e/88/9a/6e889a3a4cc52a587cddfbf8cbd233d2.jpg', 20, '2025-11-25 19:23:39'),
(94, 17, 'Resin Starter Kit', 1200.00, 'Complete beginner kit for resin crafts.', 'https://i.pinimg.com/564x/47/0f/18/470f186b2141c57a35e7b2433e8950ab.jpg', 10, '2025-11-25 19:23:39'),
(95, 17, 'Silicone Resin Molds Set', 600.00, 'High-quality reusable resin molds.', 'https://i.pinimg.com/564x/4f/89/c7/4f89c7d542fc830e5eac6602d37171f0.jpg', 20, '2025-11-25 19:23:39'),
(96, 17, 'Resin Glitter Pack', 300.00, 'Colorful glitter powders for resin decoration.', 'https://i.pinimg.com/564x/d6/1d/a5/d61da5e40790d6bf86a1b6d6b5195e51.jpg', 25, '2025-11-25 19:23:39'),
(97, 18, 'Macrame Wall Hanging Kit', 900.00, 'DIY macrame decor kit with cotton ropes.', 'https://i.pinimg.com/564x/01/4e/20/014e208bee2c3cd5aa1a2c9a43b455b4.jpg', 12, '2025-11-25 19:23:39'),
(98, 18, 'DIY Wooden Shelf Kit', 1500.00, 'Build your own wooden decorative shelf.', 'https://i.pinimg.com/564x/a5/ab/e8/a5abe8c96ee0f2f06b28ec8e8d984693.jpg', 8, '2025-11-25 19:23:39'),
(99, 18, 'Decorative Light Garland', 500.00, 'Warm LED lights for home decoration.', 'https://i.pinimg.com/564x/4b/53/dd/4b53dd2ddda03f8d8f9afb9a2b805d2e.jpg', 18, '2025-11-25 19:23:39'),
(100, 19, 'Candle Making Starter Kit', 1100.00, 'All essentials for homemade candles.', 'https://i.pinimg.com/564x/1a/bf/8e/1abf8e63e67c2d53295d85f1e4b4a054.jpg', 10, '2025-11-25 19:23:39'),
(101, 19, 'Scented Oils Pack', 450.00, 'Premium fragrance oils for candle making.', 'https://i.pinimg.com/564x/28/54/4b/28544b7d9920e0f50b0b7a8bcbfb5b58.jpg', 20, '2025-11-25 19:23:39'),
(102, 19, 'Color Wax Dyes Set', 300.00, 'Vibrant dye blocks for colored candles.', 'https://i.pinimg.com/564x/81/81/c8/8181c8c467363b5e50c21965c1dc98cc.jpg', 25, '2025-11-25 19:23:39'),
(103, 20, 'Bead Jewelry Kit', 700.00, 'Colorful beads with string & tools.', 'https://i.pinimg.com/564x/09/8a/7b/098a7b8eabda6c111a239f61c2d603b0.jpg', 18, '2025-11-25 19:23:39'),
(104, 20, 'Jewelry Pliers Set', 500.00, 'Essential tools for jewelry crafting.', 'https://i.pinimg.com/564x/a9/87/ea/a987ea7f8e8d0b1a5a3e3115ad4bbd0d.jpg', 15, '2025-11-25 19:23:39'),
(105, 20, 'Metal Charm Pack', 350.00, 'Gold & silver plated charms.', 'https://i.pinimg.com/564x/f8/41/8f/f8418fce35b2969f0de12f2e7a451a28.jpg', 30, '2025-11-25 19:23:39'),
(106, 21, 'PLA Filament Pack', 900.00, 'High-quality PLA filament for 3D printers.', 'https://i.pinimg.com/564x/4b/1d/e3/4b1de38f400ac282d047d82e7141e350.jpg', 10, '2025-11-25 19:23:40'),
(107, 21, '3D Printer Nozzle Kit', 450.00, 'Durable multipack brass nozzles.', 'https://i.pinimg.com/564x/e8/f0/23/e8f0236a40c0dbce3f5c964d4ce5725f.jpg', 20, '2025-11-25 19:23:40'),
(108, 21, '3D Print Cleaning Tools', 300.00, 'For model finishing & support removal.', 'https://i.pinimg.com/564x/33/a6/90/33a690a4ca02143fae601eb2d6468d4e.jpg', 25, '2025-11-25 19:23:40'),
(109, 22, 'Beginner Telescope', 3500.00, 'Perfect for moon & star observation.', 'https://i.pinimg.com/564x/0e/96/5c/0e965c982a46f288d4b7e64f679b4eda.jpg', 6, '2025-11-25 19:23:40'),
(110, 22, 'Star Map Poster', 300.00, 'High-quality astronomy wall art.', 'https://i.pinimg.com/564x/83/3e/6c/833e6c960b993a1cd58dd5a597240fdf.jpg', 20, '2025-11-25 19:23:40'),
(111, 22, 'Astronomy Guide Book', 450.00, 'Learn stargazing & constellations.', 'https://i.pinimg.com/564x/ab/23/4b/ab234b8eba695bf447bda8e9b1f6e9f1.jpg', 15, '2025-11-25 19:23:40'),
(112, 23, 'Drone Propeller Guards', 600.00, 'Protect your drone during flight.', 'https://i.pinimg.com/564x/b8/2f/86/b82f86a2c97496b3d39b857c5fb32f86.jpg', 20, '2025-11-25 19:23:40'),
(113, 23, '4K Drone Camera Filters', 900.00, 'Enhance photo quality with ND filters.', 'https://i.pinimg.com/564x/f3/94/d7/f394d7bb9fe62a4f9e0b7b8fb327c52f.jpg', 10, '2025-11-25 19:23:40'),
(114, 23, 'Drone Carrying Case', 1200.00, 'Shockproof travel case for drones.', 'https://i.pinimg.com/564x/78/7e/7a/787e7ad88d83625c6de681f94c37b2f8.jpg', 12, '2025-11-25 19:23:40'),
(115, 24, 'Green Screen Backdrop', 900.00, 'Professional chroma cloth for editing.', 'https://i.pinimg.com/564x/ab/01/fe/ab01fee71bb5bac5a0e3527a0b8d0ca0.jpg', 10, '2025-11-25 19:23:40'),
(116, 24, 'Ring Light Kit', 1500.00, 'Perfect lighting setup for videos.', 'https://i.pinimg.com/564x/75/64/7a/75647a785e7a785e8395cb5b6623393e.jpg', 6, '2025-11-25 19:23:40'),
(117, 24, 'Audio Microphone Set', 1100.00, 'High-quality mic for clean audio.', 'https://i.pinimg.com/564x/1b/f3/6f/1bf36f41563feecb8fa4c388d4821eef.jpg', 8, '2025-11-25 19:23:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `phone`, `password`, `role`, `city`, `address`, `created_at`) VALUES
(1, 'Demo User', 'user@example.com', '9999999999', '$2y$10$abcdefghijklmnopqrstuvC9Xv2Yh3VqJ5Vb0sR5Aq0tR8T1Xb1z', 'user', 'Sample City', 'Sample address for demo user', '2025-11-25 13:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 1, 6, '2025-11-25 14:00:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`hobby_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_products_hobby` (`hobby_id`),
  ADD KEY `idx_products_price` (`price`),
  ADD KEY `idx_products_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_product` (`user_id`,`product_id`),
  ADD KEY `fk_wishlist_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `hobby_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_hobby` FOREIGN KEY (`hobby_id`) REFERENCES `hobbies` (`hobby_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
