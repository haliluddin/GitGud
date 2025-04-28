-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gitgud
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `business`
--

DROP TABLE IF EXISTS `business`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `region_province_city` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `street_building_house` varchar(100) NOT NULL,
  `business_phone` varchar(20) NOT NULL,
  `business_email` varchar(100) NOT NULL,
  `business_permit` varchar(255) NOT NULL,
  `business_status` enum('Approved','Rejected','Pending Approval') NOT NULL DEFAULT 'Pending Approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `business_logo` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `status` enum('Available','Unavailable') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business`
--

LOCK TABLES `business` WRITE;
/*!40000 ALTER TABLE `business` DISABLE KEYS */;
INSERT INTO `business` VALUES (77,30,'Park 1','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Ayala','sample','9111111111','corrimaroon@chefalicious.com','uploads/business/permit_680dfa01c0ece6.65903726.pdf','Approved','2025-04-27 09:33:53','2025-04-27 09:38:29','uploads/business/logo_680dfa01c16827.97126412.png','680dfa01c254c',NULL,'Available'),(78,30,'park 2','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Bolong','sample','9111111111','corrimaroon@chefalicious.com','uploads/business/permit_680e045129a8c6.56503631.png','Approved','2025-04-27 10:17:53','2025-04-27 10:19:40','uploads/business/logo_680e04512b3995.01671733.png','680e04512c9d2',NULL,'Unavailable');
/*!40000 ALTER TABLE `business` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_option_id` int(11) DEFAULT NULL,
  `request` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `variation_option_id` (`variation_option_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`variation_option_id`) REFERENCES `variation_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=370 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stall_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (51,'drinks','2025-04-27 10:15:49','2025-04-27 10:15:49',131);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deactivation`
--

DROP TABLE IF EXISTS `deactivation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deactivation` (
  `user_id` int(11) unsigned NOT NULL,
  `deactivated_until` date NOT NULL,
  `deactivation_reason` varchar(255) NOT NULL,
  `status` enum('Active','Deactivated') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `deactivation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deactivation`
--

LOCK TABLES `deactivation` WRITE;
/*!40000 ALTER TABLE `deactivation` DISABLE KEYS */;
/*!40000 ALTER TABLE `deactivation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `variation_option_id` int(11) DEFAULT NULL,
  `type` enum('Stock In','Stock Out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `variation_option_id` (`variation_option_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`variation_option_id`) REFERENCES `variation_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (51,53,NULL,'Stock In',100,'Restock','2025-04-27 10:51:36');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_views`
--

DROP TABLE IF EXISTS `menu_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_menu_views_stall` (`stall_id`),
  KEY `idx_menu_views_user` (`user_id`),
  CONSTRAINT `fk_menu_views_stall` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_menu_views_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_views`
--

LOCK TABLES `menu_views` WRITE;
/*!40000 ALTER TABLE `menu_views` DISABLE KEYS */;
INSERT INTO `menu_views` VALUES (5,131,32,'2025-04-28 00:36:04'),(6,131,32,'2025-04-28 00:46:08'),(7,131,32,'2025-04-28 00:46:44'),(8,131,29,'2025-04-28 01:17:08'),(9,131,29,'2025-04-28 01:17:11'),(10,131,29,'2025-04-28 01:36:37'),(11,131,29,'2025-04-28 01:37:37'),(12,131,29,'2025-04-28 01:42:16'),(13,131,29,'2025-04-28 01:43:16'),(14,131,29,'2025-04-28 01:44:35'),(15,131,29,'2025-04-28 01:49:08'),(16,131,29,'2025-04-28 01:49:37'),(17,131,29,'2025-04-28 02:05:43'),(18,131,29,'2025-04-28 02:06:33'),(19,131,32,'2025-04-28 02:07:16'),(20,131,32,'2025-04-28 02:07:43'),(21,131,32,'2025-04-28 02:16:48'),(22,131,32,'2025-04-28 02:21:40'),(23,131,32,'2025-04-28 02:22:14'),(24,131,32,'2025-04-28 02:22:17'),(25,131,32,'2025-04-28 02:22:29'),(26,131,32,'2025-04-28 02:27:55'),(27,131,32,'2025-04-28 02:28:19'),(28,131,32,'2025-04-28 02:31:35');
/*!40000 ALTER TABLE `menu_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `order_id` int(11) NOT NULL,
  `stall_id` int(10) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (104,30,117,131,'Order ID 0117: Preparing Order','Unread','2025-04-27 10:33:32'),(105,30,117,131,'Order ID 0117: Payment Confirmed!','Unread','2025-04-27 10:33:32'),(112,30,117,131,'Order ID 0117: Ready to pickup!','Unread','2025-04-27 12:05:17'),(123,32,124,131,'Order ID 0124: Preparing Order','Unread','2025-04-28 00:04:44'),(124,32,124,131,'Order ID 0124: Payment Confirmed!','Unread','2025-04-28 00:04:44'),(125,32,125,131,'Order ID 0125: Preparing Order','Unread','2025-04-28 02:31:00'),(126,32,125,131,'Order ID 0125: Payment Confirmed!','Unread','2025-04-28 02:31:01');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_hours`
--

DROP TABLE IF EXISTS `operating_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operating_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `days` varchar(255) DEFAULT NULL,
  `open_time` varchar(10) DEFAULT NULL,
  `close_time` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `business_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_id` (`business_id`),
  CONSTRAINT `operating_hours_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_hours`
--

LOCK TABLES `operating_hours` WRITE;
/*!40000 ALTER TABLE `operating_hours` DISABLE KEYS */;
INSERT INTO `operating_hours` VALUES (78,'Monday, Wednesday, Friday','07:00 AM','07:00 PM','2025-04-27 09:33:53',77),(79,'Tuesday, Thursday, Saturday','12:00 PM','12:00 AM','2025-04-27 09:33:53',77),(80,'Monday, Tuesday, Wednesday, Thursday, Friday','01:00 AM','01:00 PM','2025-04-27 10:17:53',78);
/*!40000 ALTER TABLE `operating_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_stall_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `variations` varchar(255) DEFAULT NULL,
  `request` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_stall_id` (`order_stall_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_stall_id`) REFERENCES `order_stalls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (231,162,52,2,100.00,200.00,'2025-04-27 10:31:51',NULL,''),(232,163,52,1,100.00,100.00,'2025-04-27 10:42:15',NULL,''),(234,164,54,1,122.00,122.00,'2025-04-27 10:56:47',NULL,''),(235,164,53,1,100.00,100.00,'2025-04-27 10:56:47',NULL,''),(236,164,55,1,111.00,111.00,'2025-04-27 10:56:47',NULL,''),(237,164,52,1,100.00,100.00,'2025-04-27 10:56:47',NULL,''),(238,165,52,7,100.00,700.00,'2025-04-27 11:02:21',NULL,''),(239,166,53,4,100.00,400.00,'2025-04-27 11:03:54',NULL,''),(240,167,53,6,100.00,600.00,'2025-04-27 16:11:53',NULL,''),(241,168,53,2,100.00,200.00,'2025-04-27 16:15:41',NULL,''),(242,169,53,1,100.00,100.00,'2025-04-28 00:04:24',NULL,''),(243,170,57,3,1111.00,3333.00,'2025-04-28 02:31:01',NULL,'');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_stalls`
--

DROP TABLE IF EXISTS `order_stalls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_stalls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `stall_id` int(10) unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` enum('Pending','Preparing','Ready','Completed','Canceled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `queue_number` int(11) DEFAULT NULL,
  `cancellation_reason` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `order_stalls_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_stalls_ibfk_2` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_stalls`
--

LOCK TABLES `order_stalls` WRITE;
/*!40000 ALTER TABLE `order_stalls` DISABLE KEYS */;
INSERT INTO `order_stalls` VALUES (162,117,131,200.00,'Completed','2025-04-27 10:31:51',1,NULL,'2025-04-27 12:05:22'),(163,118,131,100.00,'Completed','2025-04-27 10:42:15',2,NULL,'2025-04-27 12:05:29'),(164,119,131,544.00,'Ready','2025-04-27 10:56:47',3,NULL,'2025-04-27 16:12:26'),(165,120,131,700.00,'Completed','2025-04-27 11:02:21',4,NULL,'2025-04-27 14:50:38'),(166,121,131,400.00,'Canceled','2025-04-27 11:03:54',NULL,'Need to modify order','2025-04-27 11:04:40'),(167,122,131,600.00,'Ready','2025-04-27 16:11:53',1,NULL,'2025-04-27 16:13:40'),(168,123,131,200.00,'Preparing','2025-04-27 16:15:41',2,NULL,'2025-04-27 16:15:58'),(169,124,131,100.00,'Preparing','2025-04-28 00:04:24',3,NULL,'2025-04-28 00:04:44'),(170,125,131,3333.00,'Preparing','2025-04-28 02:31:00',4,NULL,'2025-04-28 02:31:00');
/*!40000 ALTER TABLE `order_stalls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','GCash') NOT NULL,
  `order_type` enum('Dine In','Take Out') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (117,30,200.00,'Cash','Dine In','2025-04-27 10:31:51'),(118,32,100.00,'Cash','Dine In','2025-04-27 10:42:15'),(119,32,544.00,'Cash','Dine In','2025-04-27 10:56:47'),(120,32,700.00,'GCash','Take Out','2025-04-27 11:02:21'),(121,32,400.00,'Cash','Dine In','2025-04-27 11:03:54'),(122,32,600.00,'Cash','Dine In','2025-04-27 16:11:53'),(123,32,200.00,'Cash','Dine In','2025-04-27 16:15:41'),(124,32,100.00,'Cash','Dine In','2025-04-28 00:04:24'),(125,32,3333.00,'GCash','Dine In','2025-04-28 02:31:00');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `park_first_opening`
--

DROP TABLE IF EXISTS `park_first_opening`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `park_first_opening` (
  `park_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `park_first_opening`
--

LOCK TABLES `park_first_opening` WRITE;
/*!40000 ALTER TABLE `park_first_opening` DISABLE KEYS */;
INSERT INTO `park_first_opening` VALUES (76),(78);
/*!40000 ALTER TABLE `park_first_opening` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 24 hour),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variations`
--

DROP TABLE IF EXISTS `product_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_id` int(10) unsigned NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `stall_id` (`stall_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (52,131,51,'lemon juice','680e03ef52fe8','sample',100.00,0.00,NULL,NULL,'uploads/4507fdad6d96271af132d1f007863f09.jpg','2025-04-27 10:16:15'),(53,131,51,'gfff','680e0c28bace5','ssss',100.00,0.00,NULL,NULL,'uploads/3d43b5816213b46616e178174f2c2dbb.jpg','2025-04-27 10:51:20'),(54,131,51,'chicken','680e0c688cdf0','sasms',122.00,0.00,NULL,NULL,'uploads/608292f94567a8b2952fe7e8eeeebb77.jpg','2025-04-27 10:52:24'),(55,131,51,'leche flan','680e0c8268e12','1nssms',111.00,0.00,NULL,NULL,'uploads/5a52bf65691fc38d85048924171ca5e2.jpg','2025-04-27 10:52:50'),(57,131,51,'kdkdkd','680ecf2328cb9','dkdkd',1111.00,0.00,NULL,NULL,'tmp/680ecf1225469.jpg','2025-04-28 00:43:15');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rating_helpful`
--

DROP TABLE IF EXISTS `rating_helpful`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rating_helpful` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating_id` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vote` (`rating_id`,`user_id`),
  CONSTRAINT `rating_helpful_ibfk_1` FOREIGN KEY (`rating_id`) REFERENCES `ratings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rating_helpful`
--

LOCK TABLES `rating_helpful` WRITE;
/*!40000 ALTER TABLE `rating_helpful` DISABLE KEYS */;
/*!40000 ALTER TABLE `rating_helpful` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `order_stall_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variations` varchar(255) DEFAULT NULL,
  `rating_value` tinyint(1) NOT NULL CHECK (`rating_value` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seller_response` text DEFAULT NULL,
  `response_at` timestamp NULL DEFAULT NULL,
  `deletion_requested` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_order_stall` (`order_stall_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_ratings_order_stall` FOREIGN KEY (`order_stall_id`) REFERENCES `order_stalls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ratings_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (63,32,165,52,NULL,5,'kddkdkd','2025-04-28 02:07:34',NULL,NULL,0);
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reported_by` int(10) unsigned NOT NULL,
  `reported_park` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Resolved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  KEY `reported_park` (`reported_park`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_park`) REFERENCES `business` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_categories`
--

DROP TABLE IF EXISTS `stall_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_categories` (
  `stall_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`stall_id`,`category_id`),
  KEY `idx_sc_category` (`category_id`),
  CONSTRAINT `fk_sc_category` FOREIGN KEY (`category_id`) REFERENCES `stored_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sc_stall` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_categories`
--

LOCK TABLES `stall_categories` WRITE;
/*!40000 ALTER TABLE `stall_categories` DISABLE KEYS */;
INSERT INTO `stall_categories` VALUES (130,7),(131,9),(132,9);
/*!40000 ALTER TABLE `stall_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_invitations`
--

DROP TABLE IF EXISTS `stall_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `invitation_token` varchar(255) NOT NULL,
  `token_expiration` datetime NOT NULL,
  `last_sent` int(11) NOT NULL,
  `is_used` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_invitations`
--

LOCK TABLES `stall_invitations` WRITE;
/*!40000 ALTER TABLE `stall_invitations` DISABLE KEYS */;
INSERT INTO `stall_invitations` VALUES (25,31,77,'680dfb75ca60b','2025-05-04 11:40:05',1745746805,0),(26,32,77,'680dfb75cf945','2025-05-04 11:40:05',1745746805,0),(27,31,78,'680e04d87faf5','2025-05-04 12:20:08',1745749208,0);
/*!40000 ALTER TABLE `stall_invitations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_likes`
--

DROP TABLE IF EXISTS `stall_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `stall_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `stall_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stall_likes_ibfk_2` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_likes`
--

LOCK TABLES `stall_likes` WRITE;
/*!40000 ALTER TABLE `stall_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `stall_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_operating_hours`
--

DROP TABLE IF EXISTS `stall_operating_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_operating_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_id` int(10) unsigned NOT NULL,
  `days` varchar(255) NOT NULL,
  `open_time` varchar(10) NOT NULL,
  `close_time` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `stall_operating_hours_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_operating_hours`
--

LOCK TABLES `stall_operating_hours` WRITE;
/*!40000 ALTER TABLE `stall_operating_hours` DISABLE KEYS */;
INSERT INTO `stall_operating_hours` VALUES (121,130,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','01:00 AM','01:00 PM'),(122,131,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','07:00 AM','07:00 PM'),(123,132,'Monday, Tuesday, Wednesday, Thursday, Friday','01:00 AM','01:00 PM');
/*!40000 ALTER TABLE `stall_operating_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_payment_methods`
--

DROP TABLE IF EXISTS `stall_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_id` int(10) unsigned NOT NULL,
  `method` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `stall_payment_methods_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_payment_methods`
--

LOCK TABLES `stall_payment_methods` WRITE;
/*!40000 ALTER TABLE `stall_payment_methods` DISABLE KEYS */;
INSERT INTO `stall_payment_methods` VALUES (127,130,'Cash'),(128,130,'GCash'),(129,131,'Cash'),(130,131,'GCash'),(131,132,'Cash'),(132,132,'GCash');
/*!40000 ALTER TABLE `stall_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_reports`
--

DROP TABLE IF EXISTS `stall_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reported_by` int(10) unsigned NOT NULL,
  `reported_stall` int(10) unsigned NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Resolved','Rejected') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  KEY `reported_stall` (`reported_stall`),
  CONSTRAINT `stall_reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stall_reports_ibfk_2` FOREIGN KEY (`reported_stall`) REFERENCES `stalls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_reports`
--

LOCK TABLES `stall_reports` WRITE;
/*!40000 ALTER TABLE `stall_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `stall_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stalls`
--

DROP TABLE IF EXISTS `stalls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stalls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `park_id` int(11) NOT NULL,
  `status` enum('Available','Unavailable') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `park_id` (`park_id`),
  CONSTRAINT `stalls_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stalls_ibfk_3` FOREIGN KEY (`park_id`) REFERENCES `business` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stalls`
--

LOCK TABLES `stalls` WRITE;
/*!40000 ALTER TABLE `stalls` DISABLE KEYS */;
INSERT INTO `stalls` VALUES (130,31,'Stall 1','sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_680dfbb77226b9.10004367.png','2025-04-27 09:41:11','2025-04-27 09:41:11',77,'Available'),(131,32,'Stall 2','sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_680dfbf7b8e590.69581739.png','2025-04-27 09:42:15','2025-04-27 09:42:15',77,'Available'),(132,31,'ksksks','msmsms','hhh@gmail.com','9555544444','sample','uploads/business/stall_680e050f53a067.76413456.png','2025-04-27 10:21:03','2025-04-27 10:21:03',78,'Available');
/*!40000 ALTER TABLE `stalls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `variation_option_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `variation_option_id` (`variation_option_id`),
  CONSTRAINT `stocks_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stocks_ibfk_2` FOREIGN KEY (`variation_option_id`) REFERENCES `variation_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES (113,52,NULL,89),(114,53,NULL,86),(115,54,NULL,9),(119,55,NULL,110),(120,57,NULL,97);
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stored_categories`
--

DROP TABLE IF EXISTS `stored_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stored_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stored_categories_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stored_categories`
--

LOCK TABLES `stored_categories` WRITE;
/*!40000 ALTER TABLE `stored_categories` DISABLE KEYS */;
INSERT INTO `stored_categories` VALUES (6,'Snacks','uploads/categories/localhost-127-0-0-1-erd-phpMyAdmin-5-2-1.png','2025-04-18 09:12:55'),(7,'BBQ','uploads/categories/644249e452bebdfb527574cf30ff1ba9.jpg','2025-04-18 09:20:01'),(8,'Beverages','uploads/categories/a3f6ad8072456f5212151375a1195e87.jpg','2025-04-18 09:20:18'),(9,'Desserts','uploads/categories/293c2f255dec9ed26615592bb5e2deb8.jpg','2025-04-18 09:21:00');
/*!40000 ALTER TABLE `stored_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` char(255) NOT NULL,
  `birth_date` date NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `role` enum('Customer','Park Owner','Stall Owner','Admin') NOT NULL DEFAULT 'Customer',
  `profile_img` varchar(255) DEFAULT 'assets/images/profile.jpg',
  `user_session` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `middle_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `user_session` (`user_session`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (29,'Haliluddin','Naila','heidie802@ptct.net','female','9000000000','$2y$10$Scy8iQloSfxfC9LNQ77slO18ZBJGg5hNIXsZbiODDHxA6DfVoZCvi','2007-04-26','Active','Admin','assets/images/profile.jpg','1cd7976ef2b396f437a2227670ba8d6439e0440073173f3e2e1af6538f483ca68e228f58659238adab4f0285c3c2500307ffe4c3a4507af5c21547c4fb22fe688bda6af418ee115d008891a81417495f6650895c38f6','2025-04-27 09:24:00','2025-04-27 09:25:02','Taji'),(30,'Jade','Ayana','corrimaroon@chefalicious.com','female','9111111111','$2y$10$JsKkDqeV7KTNkaSE6ufOVenmZ2aFKbnw9eMXHsZ5efujGHvr9vAfG','2007-04-26','Active','Park Owner','assets/images/profile.jpg','34a65ecb76f91842d62686f4d996dea7a69e3b4e00a1e2fe383fe7cc35e82c6037a6047aa418a94f584dea7db562081a28a8f539855f5da33af63f8fc6cff543858f11eeb930fad19585dc929a0a59c776f4170ba1e8','2025-04-27 09:26:03','2025-04-27 09:33:53',''),(31,'Mae','Alfaith','purepurple@chefalicious.com','female','9222222222','$2y$10$XdKp1EJjMs3kCQuF4Y9flewtTSgZE4sNcU9eqZdIsFlCKiuFAwINC','2007-04-26','Active','Stall Owner','assets/images/profile.jpg','d15c6f3d07f7553d2b55b22ccc48f13eaf53ecbd9ea8babd98ab2062d94bd314f06247835f6127cd9df33e00b820d8e59320b2b5be007f83ab558bfcf15fa281367c9b64abee62b188e217ac9b107cfc5016cf831e45','2025-04-27 09:27:11','2025-04-27 09:41:11',''),(32,'Rose','April','1591elated@chefalicious.com','female','9333333333','$2y$10$XxJx7S6rx2J0CEwHubQFnuaT5mmX6XvUqdtQjPS8/BtfRfhv9alv.','2007-04-26','Active','Stall Owner','assets/images/profile.jpg','c1cf0451bc55f11191d65b9e05db6ccca2d18abeaaf381bc85d5045c9d2f76c04efbe2a06eb3f67b821bf83ceb6742e8c6f7c2fb12f471a9eb014d2c1402a2120392ff344f3174646305c41601c0427c129b66b0f1e9','2025-04-27 09:28:16','2025-04-27 09:42:15',''),(33,'Maia','Athena','94cheerful@chefalicious.com','female','9222224344','$2y$10$nL4U01u4APYjlYWz0C6DcuG5kri.r/w7xQGORqgPtHSNmfJCWiQ9C','2007-04-26','Active','Customer','assets/images/profile.jpg','f175d311301f6975f58ce96be7f572a02806f0d0d90d4fa0262a233cb08eda527de99eb09d26c3d91b9d2c2c9a4658964dedd3efa036db50afd8d837af9f5a245ff687c6f40d5eed8d280afa886ded923cdeba59499f','2025-04-27 09:29:55','2025-04-27 09:29:55','');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variation_options`
--

DROP TABLE IF EXISTS `variation_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variation_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variation_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `add_price` decimal(10,2) DEFAULT 0.00,
  `subtract_price` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `variation_id` (`variation_id`),
  CONSTRAINT `variation_options_ibfk_1` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variation_options`
--

LOCK TABLES `variation_options` WRITE;
/*!40000 ALTER TABLE `variation_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `variation_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verification`
--

DROP TABLE IF EXISTS `verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verification` (
  `user_id` int(10) unsigned NOT NULL,
  `verification_token` varchar(255) NOT NULL,
  `token_expiration` datetime NOT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `last_sent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verification`
--

LOCK TABLES `verification` WRITE;
/*!40000 ALTER TABLE `verification` DISABLE KEYS */;
INSERT INTO `verification` VALUES (29,'680df7b0944fb','2025-04-28 11:24:00',1,'1745745840'),(30,'680df82bdd58d','2025-04-28 17:26:03',1,'1745745963'),(31,'680df86f98730','2025-04-28 17:27:11',1,'1745746031'),(32,'680df8b111000','2025-04-28 17:28:17',1,'1745746097'),(33,'680df913d1872','2025-04-28 17:29:55',1,'1745746195');
/*!40000 ALTER TABLE `verification` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-28 10:57:06
