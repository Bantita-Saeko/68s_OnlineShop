-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 09:32 AM
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
-- Database: `online_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(12, 15, 2, 1, '2025-09-27 07:00:19'),
(14, 15, 15, 2, '2025-09-27 07:03:47'),
(35, 22, 29, 1, '2025-10-10 07:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'อิเล็กทรอนิกส์'),
(2, 'เครื่องเขียน'),
(3, 'เสื้อผ้า'),
(7, 'เครื่องใช้ไฟฟ้า'),
(10, 'ตุ๊กตา'),
(11, 'อาหารจานหลัก'),
(12, 'เครื่องดื่มเย็น'),
(14, 'ของว่าง'),
(15, 'ของหวาน'),
(16, 'เครื่องใช้ไฟฟ้า');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_date`, `status`) VALUES
(1, NULL, 834.00, '2025-08-07 03:38:44', 'cancelled'),
(2, 22, 1163.00, '2025-09-25 04:15:34', 'completed'),
(3, 22, 199.00, '2025-09-27 11:38:16', 'processing'),
(4, 22, 625.00, '2025-09-27 12:16:41', 'completed'),
(5, 22, 5979.00, '2025-09-27 12:56:54', 'completed'),
(6, 22, 79.00, '2025-10-07 13:38:35', 'pending'),
(7, NULL, 339.00, '2025-10-10 06:52:00', 'shipped');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 599.00),
(2, 1, 2, 2, 35.00),
(3, 1, 3, 1, 199.00),
(4, 2, 5, 1, 79.00),
(5, 2, 15, 5, 199.00),
(6, 2, 1, 1, 89.00),
(7, 3, 15, 1, 199.00),
(8, 4, 1, 1, 89.00),
(9, 4, 2, 1, 129.00),
(10, 4, 5, 1, 79.00),
(11, 4, 15, 1, 199.00),
(12, 4, 3, 1, 129.00),
(13, 5, 25, 10, 250.00),
(14, 5, 22, 10, 250.00),
(15, 5, 29, 11, 89.00),
(17, 7, 29, 1, 89.00),
(18, 7, 25, 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock`, `image`, `category_id`, `created_at`) VALUES
(1, 'Milk toast', 'ขนมปังนุ่มราดนม โรยผักชีฝรั่ง เพิ่มความหอมสดชื่นเล็ก ๆ', 89.00, 10, 'product_1758439936.jpg', 15, '2025-08-07 03:38:44'),
(2, 'Egg Bagel', 'แฮมเบอร์เกอร์ที่เสิร์ฟพร้อมไข่ แฮม และสลัด ท็อปปิ้งด้วย แผ่นชีส', 129.00, 10, 'product_1758438646.jpg', 14, '2025-08-07 03:38:44'),
(3, 'Bacon Bagel', 'เบเกิลที่มีท็อปปิ้งเป็นเบคอน ชีส และก็ไข่ดาว', 129.00, 10, 'product_1758438586.jpg', 14, '2025-08-07 03:38:44'),
(5, 'Strawberry Milk', 'สตอเบอรรี่ผสมนมสดและทอปปิ้งด้วยครีม', 79.00, 0, 'product_1758439068.jpg', 12, '2025-09-18 01:58:44'),
(15, 'Curry with Latte', 'แกงกะหรี่ เสิร์ฟพร้อมลาเต้ร้อนฟองนม', 199.00, 3, 'product_1758439227.jpg', 11, '2025-09-18 04:19:40'),
(16, 'Mango & Cream Stack Pancakes', 'แพนเค้กหนานุ่ม 3 ชั้น ราดด้วยวิปครีมฟูฟ่อง ท็อปด้วยมะม่วงสุกหั่นเต๋าชิ้นโต, ถั่ว, ใบสะระแหน่ และดอกเดซี่เล็กๆ', 180.00, 10, 'product_1758975929.jpg', 15, '2025-09-27 12:25:29'),
(17, 'Shine Muscat Grape Pancakes & Matcha Drink Set', 'แพนเค้กหนานุ่ม 3 ชั้นสลับชั้นด้วยวิปครีม ท็อปด้วยองุ่นไซมัสคัส (Shine Muscat) ลูกใหญ่, บลูเบอร์รี่, และเลมอนฝาน เสิร์ฟคู่กับเครื่องดื่มชาเขียวเย็นแก้วใส', 200.00, 10, 'product_1758975963.jpg', 15, '2025-09-27 12:26:03'),
(18, 'Strawberry & Berry Cream Stack Pancakes', 'แพนเค้กหนานุ่ม 2 ชั้น ราดวิปครีมก้อนใหญ่ ท็อปด้วยสตรอว์เบอร์รี่สดลูกใหญ่หั่นครึ่ง และราสเบอร์รี่ เสิร์ฟบนจานสีชมพู พร้อมเครื่องดื่มกาแฟนมสตรอว์เบอร์รี่เย็น', 10.00, 10, 'product_1758975980.jpg', 15, '2025-09-27 12:26:20'),
(19, 'Chocolate Lava Berry Pancakes', 'แพนเค้กช็อกโกแลต 3 ชั้นราดด้วยซอสช็อกโกแลตเข้มข้นเยิ้มๆ ท็อปด้วยสตรอว์เบอร์รี่, ราสเบอร์รี่, บลูเบอร์รี่ และช็อกโกแลตบาร์', 190.00, 10, 'product_1758976002.jpg', 15, '2025-09-27 12:26:42'),
(20, 'Pan-Fried Soup Dumplings (Sheng Jian Bao) & Savory Steamed Egg', 'เสี่ยวหลงเปาหรือเกี๊ยวซุป (คล้ายเสี่ยวหลงเปาแต่ทอดกระทะ) โรยงาดำและต้นหอม เสิร์ฟพร้อมไข่ตุ๋นเนื้อเนียนนุ่มราดซอสสีส้ม/น้ำมันพริกเผาเบาๆ ในถ้วยแบ่งช่อง', 220.00, 10, 'product_1758976044.jpg', 14, '2025-09-27 12:27:24'),
(21, 'Pan-Fried Dumplings (Jiaozi) & Shrimp Noodle Soup Set', 'เกี๊ยวน้ำทอดกระทะ (Jiaozi) วางบนแผ่นไข่เจียว โรยงาดำและต้นหอม เสิร์ฟคู่กับก๋วยเตี๋ยวเส้นเล็กในซุปใส พร้อมกุ้งตัวโตๆ และไข่คน/ไข่ฝอยในถ้วยแบ่งช่อง', 160.00, 15, 'product_1758976088.jpg', 14, '2025-09-27 12:28:08'),
(22, 'Grilled Sausages & Beef Rice Bowl Set', 'ไส้กรอกหมู/ไก่ย่างหรือทอดสไตล์เกาหลี (ฮอทดอก) ผ่าลายและโรยงา เสิร์ฟคู่กับข้าวหรือโจ๊ก/ซุปในถ้วย ท็อปด้วยเนื้อวัวหั่นเต๋า, ไข่ดาว และผัก', 250.00, 2, 'product_1758976123.jpg', 14, '2025-09-27 12:28:43'),
(23, 'Crispy Fried Chicken & Spicy Noodle Soup Set', 'ไก่ทอดกรอบชิ้นใหญ่ (แบบไก่ไม่มีกระดูก) เสิร์ฟคู่กับก๋วยเตี๋ยวเส้นเล็กในซุปสีแดง/ซุปเผ็ด ท็อปด้วยถั่วงอก, ต้นหอม, และฟองเต้าหู้หรือไข่ฝอยชิ้นใหญ่ พร้อมน้ำผลไม้สีส้มในแก้วใส', 180.00, 10, 'product_1758976161.jpg', 14, '2025-09-27 12:29:21'),
(24, 'Shrimp Rosé Rigatoni Pasta', 'พาสต้า Rigatoni เส้นใหญ่ทรงท่อ คลุกเคล้าใน ซอสโรเซ่ (Rosé Sauce - ซอสครีมผสมมะเขือเทศ) รสชาติเข้มข้น หอมมัน ใส่กุ้งและโรยหน้าด้วยพาร์เมซานชีส เสิร์ฟพร้อมเครื่องดื่มน้ำมะนาว/มะนาวโซดาเย็น', 280.00, 10, 'product_1758976224.jpg', 11, '2025-09-27 12:30:24'),
(25, 'Korean Style Half-and-Half Fried Chicken Set', 'ไก่ทอดสไตล์เกาหลี สองรสชาติ: ครึ่งหนึ่งเป็นไก่ทอดกรอบราดซอสมัสตาร์ด อีกครึ่งหนึ่งเป็นไก่ทอดคลุกซอสเผ็ดหวานโรยงาขาว เสิร์ฟพร้อมน้ำจิ้มมัสตาร์ดแยกถ้วยและเครื่องดื่มมะนาวโซดาเย็น', 250.00, 3, 'product_1758976247.jpg', 11, '2025-09-27 12:30:47'),
(26, 'Assorted Kimbap, Latte & Peach Soda Set', 'คิมบับ (Kimbap) หรือข้าวห่อสาหร่ายสไตล์เกาหลีหั่นชิ้น ไส้ผักรวม, ไข่, แฮม/ไส้กรอก และเนื้อสัตว์ เสิร์ฟในถาดกระดาษ พร้อม ลาเต้ร้อน และ น้ำพีชโซดา กระป๋อง', 160.00, 10, 'product_1758976275.jpg', 11, '2025-09-27 12:31:15'),
(27, 'Glazed Meatballs & Popcorn Chicken Set', 'ลูกชิ้นทอด เสียบไม้ ราดด้วยซอสหวาน/ซอสเทอริยากิรสเข้มข้น เสิร์ฟคู่กับ ไก่ป๊อป (Popcorn Chicken) ทอดกรอบเล็กๆ พร้อมน้ำจิ้มรสชาติเปรี้ยวหวาน และเครื่องดื่มสมูทตี้/น้ำผลไม้เย็น', 150.00, 10, 'product_1758976305.jpg', 14, '2025-09-27 12:31:45'),
(28, 'Passion Fruit & Golden Cherry Soda', 'เครื่องดื่ม เสาวรสโซดา หอมหวานอมเปรี้ยว สดชื่นซ่าส์ มีเม็ดเสาวรสลอยอยู่ด้านล่าง ท็อปด้วย เชอร์รี่สีเหลือง หรือลูกพุดทราจีน (Jujube) แช่อิ่ม 1 ลูก', 79.00, 10, 'product_1758976591.jpg', 12, '2025-09-27 12:36:31'),
(29, 'Orange Passion Fruit Ade / Soda', 'เครื่องดื่ม ส้ม-เสาวรสโซดา หรือ Ade สีส้มสดใส เปรี้ยวอมหวาน ท็อปด้วยชิ้นเนื้อส้มและเลมอนฝาน แต่งด้วยใบสะระแหน่เล็กน้อย มีรสชาติของเสาวรสที่ก้นแก้ว', 89.00, 4, 'product_1758976618.jpg', 12, '2025-09-27 12:36:58'),
(30, 'Sparkling Strawberry Thyme', 'เครื่องดื่มโซดาสีชมพูอ่อนใส รสสตรอว์เบอร์รี่ สดชื่นหวานอมเปรี้ยว มีสตรอว์เบอร์รี่สดหั่นซีกแช่อยู่ และแต่งด้วยใบ ไทม์ (Thyme) ช่วยเพิ่มกลิ่นหอมสมุนไพรเบาๆ ที่ขอบแก้วโรยน้ำตาล', 79.00, 10, 'product_1758976658.jpg', 12, '2025-09-27 12:37:38'),
(31, 'Butterfly Pea & Lemon Layered Soda', 'เครื่องดื่ม อัญชันมะนาวโซดา แบบแยกชั้น (Layered) โดยมีชั้นน้ำมะนาวสีเหลืองอ่อนอยู่ด้านล่าง และชั้นน้ำอัญชันสีม่วงอยู่ด้านบน เมื่อผสมกันจะเปลี่ยนเป็นสีม่วงอมชมพูสวยงาม แต่งด้วยเลมอนฝานและใบไม้สีเขียว', 89.00, 10, 'product_1758976711.jpg', 12, '2025-09-27 12:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL COMMENT 'คะแนนรีวิว 1.0 - 5.0',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(2, 3, 22, 4.5, '', '2025-09-27 14:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `shipping_status` enum('not_shipped','shipped','delivered') DEFAULT 'not_shipped'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `address`, `city`, `postal_code`, `phone`, `shipping_status`) VALUES
(1, 1, '123 ถนนหลัก เขตเมือง', 'กรุงเทพมหานคร', '10100', '0812345678', 'shipped'),
(2, 2, 'gtgt', 'Nakhon Pathom', '73000', '343543543534', 'shipped'),
(3, 3, '153', 'Nakhon Pathom', '232', '343543543534', 'not_shipped'),
(4, 4, '69/1', 'Nakhon Pathom', '73000', '0949836248', 'delivered'),
(5, 5, '69/1', 'Nakhon Pathom', '73000', '0949836248', 'shipped'),
(6, 6, 'gtgt', 'Nakhon Pathom', '73000', '343543543534', 'not_shipped'),
(7, 7, '69/1', 'Nakhon Pathom', '73000', '0949836248', 'delivered');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin1', 'admin_pass', 'admin1@example.com', 'Admin One', 'admin', '2025-08-07 03:38:43'),
(4, 'admin', '$2y$10$KHcz4OzXW4p0e.Ps/qFICev7sbmqrPmLYi4vkjM2sH/Lx53tlYttK', 'admin@gmail.com', 'บัณฑิตา แซ่โก้', 'admin', '2025-08-07 05:04:48'),
(15, 'bantitas', '$2y$10$sQvr5f4ConPjt.b44heOW.Tvej6iEme.EaX9p2G0kz5hYJFr8ojpS', 'admins@gmail.com', 'Bantita Saeko', 'admin', '2025-09-04 04:47:38'),
(21, 'Ken', '$2y$10$hbnSgYC2NZFFFQiBiWYctOn5ycqQRr4wz7PQPGZ5/gyh15BbI8Cnq', 'user@gmail.com', 'Seksun Hlamwanna', 'member', '2025-09-11 03:23:07'),
(22, 'Ink', '$2y$10$YjkshipQRNn3u4yHvodTQunDFzfFs1MRStWz5uyigrmKaRZamX1MK', 'user1@gmail.com', 'Bantita Saeko', 'member', '2025-09-11 03:32:18'),
(23, 'Ink1', '$2y$10$yu5dWOY.ROJcJFagmR/QUOycnvu1mLrogGEHY1CFW2dtFlS3wgreG', 'user2@gmail.com', 'Bantita Saeko', 'member', '2025-10-10 06:46:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
