-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2024 at 07:19 AM
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
-- Database: `as_rms`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_item_table`
--

CREATE TABLE `order_item_table` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(250) NOT NULL,
  `product_quantity` int(4) NOT NULL,
  `product_rate` decimal(12,2) NOT NULL,
  `product_amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_item_table`
--

INSERT INTO `order_item_table` (`order_item_id`, `order_id`, `product_name`, `product_quantity`, `product_rate`, `product_amount`) VALUES
(1, 1, 'Cold Drink', 2, 40.00, 80.00),
(2, 2, 'Veg Biryani', 3, 100.00, 300.00),
(3, 1, 'Almond Malai Kulfi', 2, 100.00, 200.00),
(6, 5, 'Finger Chips', 2, 60.00, 120.00),
(7, 6, 'Almond Malai Kulfi', 2, 100.00, 200.00),
(8, 7, 'Veg Biryani', 3, 100.00, 300.00),
(9, 8, 'Cold Drink', 4, 40.00, 160.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_table`
--

CREATE TABLE `order_table` (
  `order_id` int(11) NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `order_table` varchar(250) NOT NULL,
  `order_gross_amount` decimal(12,2) NOT NULL,
  `order_tax_amount` decimal(12,2) NOT NULL,
  `order_net_amount` decimal(12,2) NOT NULL,
  `order_date` date NOT NULL,
  `order_time` time NOT NULL,
  `order_waiter` varchar(250) NOT NULL,
  `order_cashier` varchar(250) NOT NULL,
  `order_status` enum('In Process','Completed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_table`
--

INSERT INTO `order_table` (`order_id`, `order_number`, `order_table`, `order_gross_amount`, `order_tax_amount`, `order_net_amount`, `order_date`, `order_time`, `order_waiter`, `order_cashier`, `order_status`) VALUES
(1, '100000', 'Table 1', 280.00, 16.80, 296.80, '2021-03-16', '06:38:46', 'Master', 'Master', 'Completed'),
(2, '100001', 'Table 3', 300.00, 18.00, 318.00, '2021-03-16', '06:39:30', 'Master', 'Master', 'Completed'),
(5, '100002', 'Table 1', 120.00, 7.20, 127.20, '2021-03-16', '06:45:59', 'Master', 'Master', 'Completed'),
(6, '100003', 'Table 2', 200.00, 12.00, 212.00, '2021-03-16', '06:47:28', 'Master', 'Master', 'Completed'),
(7, '100004', 'Table 3', 300.00, 18.00, 318.00, '2021-03-16', '06:47:04', 'Master', 'Master', 'Completed'),
(8, '100005', 'Table 4', 160.00, 9.60, 169.60, '2021-03-16', '06:46:39', 'Rakesh Sharma', 'Master', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_tax_table`
--

CREATE TABLE `order_tax_table` (
  `order_tax_table_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_tax_name` varchar(200) NOT NULL,
  `order_tax_percentage` decimal(4,2) NOT NULL,
  `order_tax_amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_tax_table`
--

INSERT INTO `order_tax_table` (`order_tax_table_id`, `order_id`, `order_tax_name`, `order_tax_percentage`, `order_tax_amount`) VALUES
(19, 8, 'SGST', 3.50, 5.60),
(20, 8, 'CGST', 2.50, 4.00),
(21, 7, 'SGST', 3.50, 10.50),
(22, 7, 'CGST', 2.50, 7.50),
(23, 6, 'SGST', 3.50, 7.00),
(24, 6, 'CGST', 2.50, 5.00),
(25, 5, 'SGST', 3.50, 4.20),
(26, 5, 'CGST', 2.50, 3.00),
(27, 2, 'SGST', 3.50, 10.50),
(28, 2, 'CGST', 2.50, 7.50),
(29, 1, 'SGST', 3.50, 9.80),
(30, 1, 'CGST', 2.50, 7.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_category_table`
--

CREATE TABLE `product_category_table` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `category_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_category_table`
--

INSERT INTO `product_category_table` (`category_id`, `category_name`, `category_status`) VALUES
(1, 'Dessert', 'Enable'),
(2, 'Starter', 'Enable'),
(3, 'Beverage', 'Enable'),
(4, 'Non Veg', 'Enable'),
(5, 'Veg', 'Enable'),
(6, 'Non Beverage', 'Disable');

-- --------------------------------------------------------

--
-- Table structure for table `product_table`
--

CREATE TABLE `product_table` (
  `product_id` int(11) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `product_name` varchar(250) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_table`
--

INSERT INTO `product_table` (`product_id`, `category_name`, `product_name`, `product_price`, `product_status`) VALUES
(1, 'Beverage', 'Cold Drink', 40.00, 'Enable'),
(2, 'Dessert', 'Almond Malai Kulfi', 100.00, 'Enable'),
(3, 'Non Veg', 'Chicken Noodles', 60.00, 'Disable'),
(4, 'Starter', 'Finger Chips', 60.00, 'Enable'),
(6, 'Veg', 'Veg Biryani', 100.00, 'Enable');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_table`
--

CREATE TABLE `restaurant_table` (
  `restaurant_id` int(11) NOT NULL,
  `restaurant_name` varchar(250) NOT NULL,
  `restaurant_tag_line` varchar(300) NOT NULL,
  `restaurant_address` text NOT NULL,
  `restaurant_contact_no` varchar(30) NOT NULL,
  `restaurant_email` varchar(250) NOT NULL,
  `restaurant_currency` varchar(10) NOT NULL,
  `restaurant_timezone` varchar(250) NOT NULL,
  `restaurant_logo` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `restaurant_table`
--

INSERT INTO `restaurant_table` (`restaurant_id`, `restaurant_name`, `restaurant_tag_line`, `restaurant_address`, `restaurant_contact_no`, `restaurant_email`, `restaurant_currency`, `restaurant_timezone`, `restaurant_logo`) VALUES
(1, 'Hungry Foods', 'Tasty Food, Too Yummy', '2nd Level, Tarjum Building, Opp. More Supermarket, Land Mark-Jadhavar School, Manaji Nagar, Narhe, Pune-41.', '9527710456', 'shohebkazi456@gmail.com', 'INR', 'Asia/Kolkata', 'images/restaurant9491.logowik.com.webp');

-- --------------------------------------------------------

--
-- Table structure for table `table_data`
--

CREATE TABLE `table_data` (
  `table_id` int(11) NOT NULL,
  `table_name` varchar(250) NOT NULL,
  `table_capacity` int(3) NOT NULL,
  `table_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `table_data`
--

INSERT INTO `table_data` (`table_id`, `table_name`, `table_capacity`, `table_status`) VALUES
(1, 'Table 1', 2, 'Enable'),
(2, 'Table 2', 2, 'Enable'),
(3, 'Table 3', 4, 'Enable'),
(4, 'Table 4', 4, 'Enable'),
(6, 'Table 5', 5, 'Disable');

-- --------------------------------------------------------

--
-- Table structure for table `tax_table`
--

CREATE TABLE `tax_table` (
  `tax_id` int(11) NOT NULL,
  `tax_name` varchar(250) NOT NULL,
  `tax_percentage` decimal(4,2) NOT NULL,
  `tax_status` enum('Enable','Disable') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tax_table`
--

INSERT INTO `tax_table` (`tax_id`, `tax_name`, `tax_percentage`, `tax_status`) VALUES
(1, 'SGST', 3.50, 'Enable'),
(2, 'CGST', 2.50, 'Enable'),
(4, 'TAX1', 2.50, 'Disable');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(250) NOT NULL,
  `user_contact_no` varchar(30) NOT NULL,
  `user_email` varchar(30) NOT NULL,
  `user_password` varchar(250) NOT NULL,
  `user_profile` varchar(250) NOT NULL,
  `user_type` enum('Master','Waiter','Cashier') NOT NULL,
  `user_status` enum('Enable','Disable') NOT NULL,
  `user_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`user_id`, `user_name`, `user_contact_no`, `user_email`, `user_password`, `user_profile`, `user_type`, `user_status`, `user_created_on`) VALUES
(1, 'Shoheb Kazi', '9527710456', 'shohebkazi456@gmail.com', 'shoheb123', 'images/new.png', 'Master', 'Enable', '2021-03-16 10:44:07'),
(2, 'Rakesh Sharma', '7412589632', 'rakesh@gmail.com', 'rakesh123', 'images/p1.jpg', 'Waiter', 'Enable', '2021-03-16 11:02:28'),
(3, 'John Smith', '8456223655', 'johnsmith@gmail.com', 'john123', 'images/p2.jpeg', 'Cashier', 'Enable', '2021-03-16 11:03:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_item_table`
--
ALTER TABLE `order_item_table`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `order_table`
--
ALTER TABLE `order_table`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_tax_table`
--
ALTER TABLE `order_tax_table`
  ADD PRIMARY KEY (`order_tax_table_id`);

--
-- Indexes for table `product_category_table`
--
ALTER TABLE `product_category_table`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_table`
--
ALTER TABLE `product_table`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `restaurant_table`
--
ALTER TABLE `restaurant_table`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- Indexes for table `table_data`
--
ALTER TABLE `table_data`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `tax_table`
--
ALTER TABLE `tax_table`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_item_table`
--
ALTER TABLE `order_item_table`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_table`
--
ALTER TABLE `order_table`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_tax_table`
--
ALTER TABLE `order_tax_table`
  MODIFY `order_tax_table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_category_table`
--
ALTER TABLE `product_category_table`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_table`
--
ALTER TABLE `product_table`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurant_table`
--
ALTER TABLE `restaurant_table`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `table_data`
--
ALTER TABLE `table_data`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tax_table`
--
ALTER TABLE `tax_table`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
