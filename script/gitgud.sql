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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business`
--

LOCK TABLES `business` WRITE;
/*!40000 ALTER TABLE `business` DISABLE KEYS */;
INSERT INTO `business` VALUES (74,21,'Sample','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Ayala','Sample','9999999999','4christiana@ptct.net','uploads/business/permit_680215b8c75249.82323349.png','Approved','2025-04-18 09:04:56','2025-04-18 15:10:32','uploads/business/logo_680215b8c893f2.74300861.png','680215b8c9eca',NULL,'Available');
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
) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (324,21,47,NULL,'',1,'2025-04-18 15:56:26','2025-04-18 15:56:26',100.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (24,'BBQ','2025-04-18 09:44:54','2025-04-18 09:44:54',125),(25,'Drinks','2025-04-18 09:45:01','2025-04-18 10:54:12',125),(46,'Sweets','2025-04-18 10:51:01','2025-04-18 10:51:01',126),(49,'lol','2025-04-18 12:17:32','2025-04-18 12:17:32',125);
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (82,21,108,125,'Order ID 0108: Ready to pickup!','Read','2025-04-18 15:57:10');
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
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_hours`
--

LOCK TABLES `operating_hours` WRITE;
/*!40000 ALTER TABLE `operating_hours` DISABLE KEYS */;
INSERT INTO `operating_hours` VALUES (73,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','07:00 AM','07:00 PM','2025-04-18 09:04:56',74);
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
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (211,146,48,1,50.00,50.00,'2025-04-18 15:22:45','Medium, Lemon',''),(212,146,47,1,100.00,100.00,'2025-04-18 15:22:45',NULL,''),(213,147,51,1,100.00,100.00,'2025-04-18 15:22:45',NULL,''),(214,148,49,1,135.00,135.00,'2025-04-18 15:27:49',NULL,''),(215,149,48,1,50.00,50.00,'2025-04-18 15:54:24','Large, Orange',''),(216,150,48,1,50.00,50.00,'2025-04-18 15:54:54','Medium, Orange',''),(217,151,48,1,50.00,50.00,'2025-04-18 15:55:23','Medium, Orange',''),(218,152,49,1,135.00,135.00,'2025-04-18 15:56:07',NULL,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_stalls`
--

LOCK TABLES `order_stalls` WRITE;
/*!40000 ALTER TABLE `order_stalls` DISABLE KEYS */;
INSERT INTO `order_stalls` VALUES (146,103,125,150.00,'Completed','2025-04-18 15:22:45',1,NULL,'2025-04-18 15:24:41'),(147,103,126,100.00,'Completed','2025-04-18 15:22:45',1,NULL,'2025-04-18 15:24:17'),(148,104,125,135.00,'Preparing','2025-04-18 15:27:49',2,NULL,'2025-04-18 15:28:11'),(149,105,125,50.00,'Preparing','2025-04-18 15:54:24',3,NULL,'2025-04-18 15:54:24'),(150,106,125,50.00,'Preparing','2025-04-18 15:54:54',4,NULL,'2025-04-18 15:54:54'),(151,107,125,50.00,'Preparing','2025-04-18 15:55:23',5,NULL,'2025-04-18 15:55:23'),(152,108,125,135.00,'Ready','2025-04-18 15:56:07',6,NULL,'2025-04-18 15:57:10');
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
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (103,24,250.00,'GCash','Dine In','2025-04-18 15:22:45'),(104,24,135.00,'Cash','Dine In','2025-04-18 15:27:49'),(105,24,50.00,'GCash','Dine In','2025-04-18 15:54:24'),(106,24,50.00,'GCash','Dine In','2025-04-18 15:54:54'),(107,24,50.00,'GCash','Dine In','2025-04-18 15:55:23'),(108,21,135.00,'GCash','Dine In','2025-04-18 15:56:07');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
INSERT INTO `product_variations` VALUES (46,48,'Size'),(47,48,'Flavor');
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (47,125,24,'Food 1','68021f4e22c70','Sample',100.00,0.00,NULL,NULL,'uploads/22107eb3587be0a5a55d83d19619115a.jpg','2025-04-18 09:45:50'),(48,125,25,'Beverage 1','6802201b460f0','Sample',25.00,0.00,NULL,NULL,'tmp/68021fa4eb16c.jpg','2025-04-18 09:49:15'),(49,125,24,'Food 2','680220485c346','Sample',150.00,10.00,'2025-04-18','2025-04-19','uploads/608292f94567a8b2952fe7e8eeeebb77.jpg','2025-04-18 09:50:00'),(51,126,46,'leche flan','68022eb9d11ab','sample',100.00,0.00,NULL,NULL,'uploads/5a52bf65691fc38d85048924171ca5e2.jpg','2025-04-18 10:51:37');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (7,24,74,'report park 1','Rejected','2025-04-18 16:29:10');
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
INSERT INTO `stall_categories` VALUES (125,7),(125,8),(126,6);
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_invitations`
--

LOCK TABLES `stall_invitations` WRITE;
/*!40000 ALTER TABLE `stall_invitations` DISABLE KEYS */;
INSERT INTO `stall_invitations` VALUES (19,22,74,'68021c79d15c6','2025-04-25 11:33:45',1744968825,0),(20,23,74,'68021c79d8837','2025-04-25 11:33:45',1744968825,0),(21,24,74,'6802438ee124d','2025-04-25 14:20:30',1744978830,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_operating_hours`
--

LOCK TABLES `stall_operating_hours` WRITE;
/*!40000 ALTER TABLE `stall_operating_hours` DISABLE KEYS */;
INSERT INTO `stall_operating_hours` VALUES (113,125,'Monday, Tuesday, Wednesday','07:00 AM','03:00 PM'),(114,125,'Thursday, Friday','07:00 AM','07:00 PM'),(117,126,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','08:00 AM','05:30 PM');
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
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_payment_methods`
--

LOCK TABLES `stall_payment_methods` WRITE;
/*!40000 ALTER TABLE `stall_payment_methods` DISABLE KEYS */;
INSERT INTO `stall_payment_methods` VALUES (115,125,'Cash'),(116,125,'GCash'),(121,126,'Cash'),(122,126,'GCash');
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
INSERT INTO `stall_reports` VALUES (4,24,125,'Report 1','Pending','2025-04-18 15:07:01'),(5,24,125,'report stall 1','Pending','2025-04-18 16:22:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stalls`
--

LOCK TABLES `stalls` WRITE;
/*!40000 ALTER TABLE `stalls` DISABLE KEYS */;
INSERT INTO `stalls` VALUES (125,22,'Stall 1','Sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_68021d3d529a14.85209057.png','2025-04-12 09:37:01','2025-04-18 09:55:45',74,'Available'),(126,23,'Stall 2','Sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_68021d7959f436.59288094.png','2025-04-18 09:38:01','2025-04-18 09:39:46',74,'Available');
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
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES (101,48,101,98),(102,48,102,96),(103,48,103,99),(104,48,104,97),(105,48,105,96),(108,49,NULL,97),(110,51,NULL,95),(112,47,NULL,92);
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
INSERT INTO `stored_categories` VALUES (6,'Snacks','uploads/categories/3d43b5816213b46616e178174f2c2dbb.jpg','2025-04-18 09:12:55'),(7,'BBQ','uploads/categories/644249e452bebdfb527574cf30ff1ba9.jpg','2025-04-18 09:20:01'),(8,'Beverages','uploads/categories/a3f6ad8072456f5212151375a1195e87.jpg','2025-04-18 09:20:18'),(9,'Desserts','uploads/categories/293c2f255dec9ed26615592bb5e2deb8.jpg','2025-04-18 09:21:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (20,'Haliluddin','Naila','drucy59@ptct.net','female','9000000000','$2y$10$cTzi63vMTeteEe3xZTQCEePQF6D4KlIRcwTfZaP8VLF/RqTsdCtoi','2007-04-17','Active','Admin','assets/images/profile.jpg','f3406446da73eb4add94ff7f65609068fdb4d424ee3f71a5ae3124a06487c34dc7c3e0121e466dc3166c9d8d00bc3d3ad87c8e5a42985de1819e1c5cb6e0a329e995ae0f5e1538c1cb9ac3caf1804b9f4c2ecc9e1e9d','2025-04-18 08:13:24','2025-04-18 08:14:39','Taji'),(21,'Alvarez','April','4christiana@ptct.net','female','9999999999','$2y$10$Q49FP1F1Deiudqj3Yw/.yeSKxArRzDjEkuD1YEYhZ7Zo4dk6qTxNO','2007-04-17','Active','Park Owner','assets/images/profile.jpg','e91a4a68aa6edb53698a56be81e0be30f2101143364074094d6d1bbe7174b8da85f9d2d45557d1f76494f188efa0713caff2074924e42a1e551f27e5514a7de22cfc8cc70c0be649bf12cdcc4ff840a0b39852629853','2025-04-18 08:16:43','2025-04-18 08:57:39','Rose'),(22,'Luzon','Alfaith','leann3@ptct.net','female','9888888888','$2y$10$Dx6MBL/U2aBTpIfq.vDAlufaot8NSGtfZhRK/YtPchcwPHtVBOhy.','2007-04-17','Active','Stall Owner','assets/images/profile.jpg','c4aecdb1b4a32d5e49efec72ef705c77af9dc9b243949dbcb8a96d716b094ff9e04e5e2a2968b80785647f03eea3a9d5a81eea76c00b41ea401010886d0bdf82461e99dac40f25d16e3910d195cd3d38425a9dbb2605','2025-04-18 08:20:23','2025-04-18 09:37:01','Mae'),(23,'Alejo','Ayana','5895marga@ptct.net','female','9111111111','$2y$10$biWEYHe5z07I71iJJ/aJuuR5huILjwEzzYCNoUTrgfGLiwX36/DjC','2007-04-17','Active','Stall Owner','assets/images/profile.jpg','ab77446bf48c94a15767e3c7f65a595ce1f3c28cd9cafe22682db07d9b8fa1a3953af99063e023dcd708e90d445312221eba0d2fc402e3b67b55214f6a96b78f92c6f3086511d860ce2f2584b018ec230ed8f1ce0db5','2025-04-18 08:21:50','2025-04-18 09:38:01','Jade'),(24,'Casino','Athena','amalitawealthy@ptct.net','female','9333333333','$2y$10$qLtqePpoxk4EmroYiehCTu8vx2mZE8PwivZblI9rFlXjJtk4X9sza','2007-04-17','Active','Customer','assets/images/profile.jpg','080b041c49b7083423669d29c8752581210e922bc25fbda288d3f4c7448bd2762494b876d2cff252ee145b34335343e1cb074c13d641516c12de85b7632e44f3adf4462edf2fcec83683a7a732aaa932d7a5bd61280f','2025-04-18 08:24:02','2025-04-18 08:24:02','Maia');
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
INSERT INTO `variation_options` VALUES (101,46,'Small',0.00,0.00,'tmp/68021fcba9214.jpg'),(102,46,'Medium',0.00,0.00,'tmp/68021fcee159e.jpg'),(103,46,'Large',0.00,0.00,'tmp/68021fd18ae1c.jpg'),(104,47,'Lemon',0.00,0.00,'tmp/68021ff334e97.jpg'),(105,47,'Orange',0.00,0.00,'tmp/68022005a7395.jpg');
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
INSERT INTO `verification` VALUES (20,'680209a454de7','2025-04-19 10:13:24',1,'1744964004'),(21,'68020a6c1909d','2025-04-19 16:16:44',1,'1744964204'),(22,'68020b47e69cb','2025-04-19 16:20:23',1,'1744964423'),(23,'68020b9e99df4','2025-04-19 16:21:50',1,'1744964510'),(24,'68020c229d6d0','2025-04-19 16:24:02',1,'1744964642');
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

-- Dump completed on 2025-04-19  0:54:45
