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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business`
--

LOCK TABLES `business` WRITE;
/*!40000 ALTER TABLE `business` DISABLE KEYS */;
INSERT INTO `business` VALUES (54,1,'Food Park 1','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Sample','Sample','9056321314','aprilalvarez@gmail.com','uploads/business/permit_67b687b2592e83.97306540.jpg','Approved','2025-02-20 01:38:58','2025-02-20 01:42:48','uploads/business/logo_67b687b259a9a1.03508045.jpg','67b687b25c070'),(55,3,'Food Park 2','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Sample','Sample','9554638281','tomatoregional@soscandia.org','uploads/business/permit_67b68a840e8426.72563824.jpg','Pending Approval','2025-02-20 01:51:00','2025-02-20 01:51:00','uploads/business/logo_67b68a840fac60.70426255.jpg','67b68a8412bfd'),(56,3,'Naila Food Park','Food Park','Mindanao, Zamboanga Del Sur, Zamboanga City','Sample','Sample','9554638281','tomatoregional@soscandia.org','uploads/business/permit_67b9fd3cc50e00.99547616.jpg','Approved','2025-02-22 16:37:16','2025-02-22 16:37:34','uploads/business/logo_67b9fd3cc54451.26936633.jpg','67b9fd3ccc928');
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
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`variation_option_id`) REFERENCES `variation_options` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (255,3,29,44,'',1,'2025-03-14 16:51:23','2025-03-14 16:51:23',110.00),(256,3,29,47,'',1,'2025-03-14 16:51:23','2025-03-14 16:51:23',110.00);
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
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (12,'Category 1','2025-02-22 01:26:05','2025-02-22 01:26:05',111),(13,'Alfaith Category','2025-02-22 13:26:55','2025-02-22 13:26:55',110),(14,'Category 4','2025-03-12 02:27:22','2025-03-12 02:27:22',111),(15,'Nice','2025-03-17 18:16:56','2025-03-17 18:16:56',111),(16,'Nope','2025-03-17 18:21:48','2025-03-17 18:21:48',111);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (19,34,NULL,'Stock In',100,'Restock','2025-03-12 02:28:55'),(20,34,NULL,'Stock In',100,'Restock','2025-03-12 02:29:06'),(21,34,NULL,'Stock Out',22,'Spoilage','2025-03-12 02:29:22'),(22,34,NULL,'Stock Out',170,'Expired','2025-03-12 23:55:05'),(23,34,NULL,'Stock Out',5,'Spoilage','2025-03-12 23:55:19'),(24,34,NULL,'Stock Out',5,'Spoilage','2025-03-12 23:55:25'),(25,34,NULL,'Stock In',5,'Restock','2025-03-12 23:55:44'),(26,34,NULL,'Stock In',1,'Restock','2025-03-12 23:56:04'),(27,29,45,'Stock In',100,'Restock','2025-03-13 00:46:06'),(28,30,NULL,'Stock In',100,'Restock','2025-03-13 00:48:37'),(29,30,NULL,'Stock Out',99,'Spoilage','2025-03-13 02:38:29'),(30,30,NULL,'Stock In',2,'Restock','2025-03-13 02:39:22'),(31,29,45,'Stock Out',5,'Spoilage','2025-03-13 02:40:30'),(32,29,48,'Stock In',2,'Restock','2025-03-13 02:40:45'),(34,29,47,'Stock In',1,'Restock','2025-03-13 13:30:36'),(35,30,NULL,'Stock In',3,'Restock','2025-03-13 13:31:04'),(36,30,NULL,'Stock In',6,'Restock','2025-03-13 13:33:05'),(37,33,NULL,'Stock In',3,'Restock','2025-03-13 13:33:47'),(38,29,44,'Stock In',10,'Restock','2025-03-17 17:47:15'),(39,29,46,'Stock In',100,'Restock','2025-03-17 17:47:46');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
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
  CONSTRAINT `operating_hours_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_hours`
--

LOCK TABLES `operating_hours` WRITE;
/*!40000 ALTER TABLE `operating_hours` DISABLE KEYS */;
INSERT INTO `operating_hours` VALUES (37,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','07:00 AM','07:00 PM','2025-02-20 01:38:58',54),(38,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','07:00 AM','07:00 PM','2025-02-20 01:51:00',55),(39,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','01:00 AM','01:00 PM','2025-02-22 16:37:16',56);
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
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (170,113,29,1,220.00,220.00,'2025-03-14 18:08:16','Option 1, Option 1 lol','');
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
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_stalls`
--

LOCK TABLES `order_stalls` WRITE;
/*!40000 ALTER TABLE `order_stalls` DISABLE KEYS */;
INSERT INTO `order_stalls` VALUES (113,75,111,220.00,'Pending','2025-03-14 18:08:16',NULL,NULL,'2025-03-14 18:08:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (75,3,220.00,'Cash','Dine In','2025-03-14 18:08:16');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
INSERT INTO `product_variations` VALUES (18,28,'Variation 1'),(19,29,'Variation 1'),(20,29,'Variation 2'),(21,31,'Variation 1');
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (27,110,13,'Food 1','67b927c5e75d4','Sample',99.99,0.00,NULL,NULL,'uploads/images (1).jpg','2025-02-22 01:26:29'),(28,111,16,'Food 2','67b9284f3aea4','Sample',100.00,0.00,NULL,NULL,'uploads/images.jpg','2025-02-22 01:28:47'),(29,111,12,'Food 3','67b928ad21506','Sample',100.00,0.00,NULL,NULL,'uploads/images (1).jpg','2025-02-22 01:30:21'),(30,111,12,'Product name','67c595417d486','New product',54.99,NULL,NULL,NULL,'uploads/images (1).jpg','2025-03-03 11:40:49'),(31,111,12,'Sample','67c595aa3fe62','Sample',100.00,0.00,NULL,NULL,'uploads/file-name-dsc-0070jpg-file-size-27mb-2835249-bytes-date-taken-20020815-104827-image-size-3008-x-1960-pixels-resolution-300-x-300-dpi-2CTP67B.jpg','2025-03-03 11:42:34'),(32,111,12,'p1','67c595c142c46','low',0.98,0.00,NULL,NULL,'uploads/images.jpg','2025-03-03 11:42:57'),(33,111,12,'ugly bitch','67c596078ca2e','jejemon',33.00,0.00,NULL,NULL,'uploads/download.jpg','2025-03-03 11:44:07'),(34,111,14,'11','67d0f15578b90','sdd',100.00,0.00,NULL,NULL,'uploads/Screenshot (4).png','2025-03-12 02:28:37'),(35,111,14,'hello','67d2142ef2508','dello',180.00,10.00,'2025-03-13','2025-03-14','uploads/Screenshot (9).png','2025-03-12 23:09:35');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stall_categories`
--

DROP TABLE IF EXISTS `stall_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stall_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stall_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stall_id` (`stall_id`),
  CONSTRAINT `stall_categories_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_categories`
--

LOCK TABLES `stall_categories` WRITE;
/*!40000 ALTER TABLE `stall_categories` DISABLE KEYS */;
INSERT INTO `stall_categories` VALUES (36,110,'Drinks'),(37,111,'Drinks'),(38,112,'Seafood');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_invitations`
--

LOCK TABLES `stall_invitations` WRITE;
/*!40000 ALTER TABLE `stall_invitations` DISABLE KEYS */;
INSERT INTO `stall_invitations` VALUES (1,4,54,'67d8c0dc14802','2025-03-25 01:39:56',1742258396,0),(2,5,54,'67d8c146710f3','2025-03-25 01:41:42',1742258502,0);
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
  CONSTRAINT `stall_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `stall_likes_ibfk_2` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_likes`
--

LOCK TABLES `stall_likes` WRITE;
/*!40000 ALTER TABLE `stall_likes` DISABLE KEYS */;
INSERT INTO `stall_likes` VALUES (19,3,110,'2025-03-14 12:25:21');
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
  CONSTRAINT `stall_operating_hours_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_operating_hours`
--

LOCK TABLES `stall_operating_hours` WRITE;
/*!40000 ALTER TABLE `stall_operating_hours` DISABLE KEYS */;
INSERT INTO `stall_operating_hours` VALUES (43,110,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','07:00 AM','07:00 PM'),(44,111,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday','01:00 AM','01:00 PM'),(45,112,'Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday','07:00 AM','11:00 PM');
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
  CONSTRAINT `stall_payment_methods_ibfk_1` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stall_payment_methods`
--

LOCK TABLES `stall_payment_methods` WRITE;
/*!40000 ALTER TABLE `stall_payment_methods` DISABLE KEYS */;
INSERT INTO `stall_payment_methods` VALUES (32,110,'Cash'),(33,110,'GCash'),(34,111,'Cash'),(35,111,'GCash'),(36,112,'Cash'),(37,112,'GCash');
/*!40000 ALTER TABLE `stall_payment_methods` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `park_id` (`park_id`),
  CONSTRAINT `stalls_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stalls_ibfk_3` FOREIGN KEY (`park_id`) REFERENCES `business` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stalls`
--

LOCK TABLES `stalls` WRITE;
/*!40000 ALTER TABLE `stalls` DISABLE KEYS */;
INSERT INTO `stalls` VALUES (110,2,'Food Park 1 Stall 1','Sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_67b689279c8e93.44325948.jpg','2025-02-20 01:45:11','2025-02-20 01:45:11',54),(111,3,'Naila Stall','Sample','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_67b9277ad8b046.85376747.jpg','2025-02-22 01:25:14','2025-02-22 01:25:14',54),(112,1,'Hello','Hello','hnailataji@gmail.com','9554638281','Sample','uploads/business/stall_67c69e5460d097.76294152.jpg','2025-03-04 06:31:48','2025-03-04 06:31:48',56);
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
INSERT INTO `stocks` VALUES (39,29,44,10),(40,29,47,1),(41,30,NULL,1),(42,33,NULL,3),(43,29,46,100);
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `user_session` (`user_session`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alvarez','April','aprilalvarez@gmail.com','male','9056321314','$2y$10$8qKZSYay9R/pERywKXLfaOJWqYkQ5qAJspd41TAqGO7EJGVQOhtr6','2003-12-04','Active','Stall Owner','assets/images/profile.jpg','$2y$10$8qKZSYay9R/pERywKXLfaOJWqYkQ5qAJspd41TAqGO7EJGVQOhtr6','2025-01-31 10:31:43','2025-03-04 06:31:48'),(2,'Luzon','Alfaith','alfaithluzon@gmail.com','male','9123456789','$2y$10$8qKZSYay9R/pERywKXLfaOJWqYkQ5qAJspd41TAqGO7EJGVQOhtr6\r\n','2003-12-04','Active','Stall Owner','assets/images/profile.jpg','$2y$10$8qKZSYay9R/pERywKXLfaOJWqYkQ5qAJspd41TAqGO7EJGVQOhtr1','2025-02-10 01:57:20','2025-02-24 02:53:37'),(3,'Haliluddin','Naila','tomatoregional@soscandia.org','male','9554638281','$2y$10$8qKZSYay9R/pERywKXLfaOJWqYkQ5qAJspd41TAqGO7EJGVQOhtr6','2003-12-04','Active','Park Owner','assets/images/profile.jpg','c7b8409f0f64251c23625859f9982068667d64c0a768bdace4034f7975a900496727629247e450d1f849214bfff0a426ebbf7af9868a5d0f90bc98d209b5173961bc3c5d3ea35ea8779dc3f97952654e55d36bb7b05d','2025-01-26 15:50:02','2025-02-22 16:37:16'),(4,'V','Fly','flyiov@e-record.com','male','9159159192','$2y$10$LmQc2MAJWOF.1OfD/3jEfen4nRF7HNJAelHrX5IYoRzDafaUWNaMW','2000-02-22','Active','Customer','assets/images/profile.jpg','6c996b8a20949672d05921a0c6cb340b91502175e0061e9018f65f967c8f14cde8e330b36dcd5915385cbb317b456f963c63d8ce62ad61961c5682f3ba4f9eec6a474b9088e47b075fd51f14190f9f2a4c97fc16f583','2025-03-18 00:04:31','2025-03-18 00:04:31'),(5,'Take','They','theytakeover@indigobook.com','male','9234923942','$2y$10$0tFpnuiEY9oH0u4Xjnh0auakRbxFSHsXknnWCY2JNmzZmP02.Xy/2','2002-02-22','Active','Customer','assets/images/profile.jpg','de2b88522a3822b37f503a61a2f24da2dca5c23def639f3308582cffeea3e27c83f38308896d7e0d1841f42d005ba5a0e7d8034e089792048e4e1155981ad376eca64d572762731b1abb580e6aff790a73ae9fee4ec0','2025-03-18 00:41:16','2025-03-18 00:41:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variation_options`
--

LOCK TABLES `variation_options` WRITE;
/*!40000 ALTER TABLE `variation_options` DISABLE KEYS */;
INSERT INTO `variation_options` VALUES (44,19,'Option 1',10.00,0.00,'uploads/images (1).jpg'),(45,19,'Option 2',0.00,10.00,'uploads/images (1).jpg'),(46,19,'Option 3',0.00,0.00,'uploads/images (1).jpg'),(47,20,'Option 1 lol',10.00,0.00,'uploads/images (1).jpg'),(48,20,'Option 2',0.00,10.00,'uploads/images (1).jpg'),(49,20,'Option 3',0.00,0.00,'uploads/images (1).jpg'),(50,21,'vn 1',0.00,0.00,'uploads/images (1).jpg'),(51,21,'vn 2',0.00,0.00,'uploads/file-name-dsc-0070jpg-file-size-27mb-2835249-bytes-date-taken-20020815-104827-image-size-3008-x-1960-pixels-resolution-300-x-300-dpi-2CTP67B.jpg'),(52,21,'vn 3',0.00,0.00,'uploads/file-name-dsc-0070jpg-file-size-27mb-2835249-bytes-date-taken-20020815-104827-image-size-3008-x-1960-pixels-resolution-300-x-300-dpi-2CTP67B.jpg');
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
INSERT INTO `verification` VALUES (1,'679494f216b71','2025-01-26 08:38:26',1,'1737790706'),(2,'6796581b7f52d','2025-01-27 16:43:23',1,'1737906203'),(3,'679659aa92ce1','2025-01-27 16:50:02',1,'1737906602'),(4,'67d8b89012d80','2025-03-19 01:04:32',1,'1742256272'),(5,'67d8c12ce7981','2025-03-19 01:41:16',1,'1742258476');
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

-- Dump completed on 2025-03-18 19:07:42
