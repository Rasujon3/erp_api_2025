-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: rapid_erp_2025
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `row_id` bigint DEFAULT NULL,
  `data` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `draft` tinyint(1) NOT NULL DEFAULT '0',
  `drafted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (10,'US','United States',0,0,NULL,1,NULL,NULL,NULL,NULL),(11,'CA','Canada',0,0,NULL,1,NULL,NULL,NULL,NULL),(12,'GB','United Kingdom',0,0,NULL,1,NULL,NULL,NULL,NULL),(13,'AU','Australia',0,0,NULL,1,NULL,NULL,NULL,NULL),(14,'DE','Germany',0,0,NULL,1,NULL,NULL,NULL,NULL),(15,'FR','France',0,0,NULL,1,NULL,NULL,NULL,NULL),(16,'IT','Italy',0,0,NULL,1,NULL,NULL,NULL,NULL),(17,'ES','Spain',0,0,NULL,1,NULL,NULL,NULL,NULL),(18,'IN','India',0,0,NULL,1,NULL,NULL,NULL,NULL),(19,'CN','China',0,0,NULL,1,NULL,NULL,NULL,NULL),(20,'JP','Japan',0,0,NULL,1,NULL,NULL,NULL,NULL),(21,'BR','Brazil',0,0,NULL,1,NULL,NULL,NULL,NULL),(22,'MX','Mexico',0,0,NULL,1,NULL,NULL,NULL,NULL),(23,'RU','Russia',0,0,NULL,1,NULL,NULL,NULL,NULL),(24,'ZA','South Africa',0,0,NULL,1,NULL,NULL,NULL,NULL),(25,'KR','South Korea',0,0,NULL,1,NULL,NULL,NULL,NULL),(26,'NG','Nigeria',0,0,NULL,1,NULL,NULL,NULL,NULL),(27,'AR','Argentina',0,0,NULL,1,NULL,NULL,NULL,NULL),(28,'SA','Saudi Arabia',0,0,NULL,1,NULL,NULL,NULL,NULL),(29,'EG','Egypt',0,0,NULL,1,NULL,NULL,NULL,NULL),(30,'PK','Pakistan',0,0,NULL,1,NULL,NULL,NULL,NULL),(31,'ID','Indonesia',0,0,NULL,1,NULL,NULL,NULL,NULL),(32,'TR','Turkey',0,0,NULL,1,NULL,NULL,NULL,NULL),(33,'KR','South Korea',0,0,NULL,1,NULL,NULL,NULL,NULL),(34,'TH','Thailand',0,0,NULL,1,NULL,NULL,NULL,NULL),(35,'VN','Vietnam',0,0,NULL,1,NULL,NULL,NULL,NULL),(36,'PH','Philippines',0,0,NULL,1,NULL,NULL,NULL,NULL),(37,'MY','Malaysia',0,0,NULL,1,NULL,NULL,NULL,NULL),(38,'SG','Singapore',0,0,NULL,1,NULL,NULL,NULL,NULL),(39,'NZ','New Zealand',0,0,NULL,1,NULL,NULL,NULL,NULL),(40,'PE','Peru',0,0,NULL,1,NULL,NULL,NULL,NULL),(41,'CL','Chile',0,0,NULL,1,NULL,NULL,NULL,NULL),(42,'CO','Colombia',0,0,NULL,1,NULL,NULL,NULL,NULL),(43,'PE','Peru',0,0,NULL,1,NULL,NULL,NULL,NULL),(44,'KE','Kenya',0,0,NULL,1,NULL,NULL,NULL,NULL),(45,'UA','Ukraine',0,0,NULL,1,NULL,NULL,NULL,NULL),(46,'KW','Kuwait',0,0,NULL,1,NULL,NULL,NULL,NULL),(47,'IQ','Iraq',0,0,NULL,1,NULL,NULL,NULL,NULL),(48,'SY','Syria',0,0,NULL,1,NULL,NULL,NULL,NULL),(49,'JO','Jordan',0,0,NULL,1,NULL,NULL,NULL,NULL),(50,'LB','Lebanon',0,0,NULL,1,NULL,NULL,NULL,NULL),(51,'OM','Oman',0,0,NULL,1,NULL,NULL,NULL,NULL),(52,'QA','Qatar',0,0,NULL,1,NULL,NULL,NULL,NULL),(53,'BH','Bahrain',0,0,NULL,1,NULL,NULL,NULL,NULL),(54,'YE','Yemen',0,0,NULL,1,NULL,NULL,NULL,NULL),(55,'AZ','Azerbaijan',0,0,NULL,1,NULL,NULL,NULL,NULL),(56,'UZ','Uzbekistan',0,0,NULL,1,NULL,NULL,NULL,NULL),(57,'AM','Armenia',0,0,NULL,1,NULL,NULL,NULL,NULL),(58,'GE','Georgia',0,0,NULL,1,NULL,NULL,NULL,NULL),(59,'MK','North Macedonia',0,0,NULL,1,NULL,NULL,NULL,NULL),(60,'RS','Serbia',0,0,NULL,1,NULL,NULL,NULL,NULL),(61,'AL','Albania',0,0,NULL,1,NULL,NULL,NULL,NULL),(62,'BG','Bulgaria',0,0,NULL,1,NULL,NULL,NULL,NULL),(63,'RO','Romania',0,0,NULL,1,NULL,NULL,NULL,NULL),(64,'HR','Croatia',0,0,NULL,1,NULL,NULL,NULL,NULL),(65,'SK','Slovakia',0,0,NULL,1,NULL,NULL,NULL,NULL),(66,'SI','Slovenia',0,0,NULL,1,NULL,NULL,NULL,NULL),(67,'CZ','Czech Republic',0,0,NULL,1,NULL,NULL,NULL,NULL),(68,'HU','Hungary',0,0,NULL,1,NULL,NULL,NULL,NULL),(69,'EE','Estonia',0,0,NULL,1,NULL,NULL,NULL,NULL),(70,'LV','Latvia',0,0,NULL,1,NULL,NULL,NULL,NULL),(71,'LT','Lithuania',0,0,NULL,1,NULL,NULL,NULL,NULL),(72,'PL','Poland',0,0,NULL,1,NULL,NULL,NULL,NULL),(73,'NO','Norway',0,0,NULL,1,NULL,NULL,NULL,NULL),(74,'SE','Sweden',0,0,NULL,1,NULL,NULL,NULL,NULL),(75,'FI','Finland',0,0,NULL,1,NULL,NULL,NULL,NULL),(76,'IS','Iceland',0,0,NULL,1,NULL,NULL,NULL,NULL),(77,'DK','Denmark',0,0,NULL,1,NULL,NULL,NULL,NULL),(78,'AT','Austria',0,0,NULL,1,NULL,NULL,NULL,NULL),(79,'CH','Switzerland',0,0,NULL,1,NULL,NULL,NULL,NULL),(80,'LU','Luxembourg',0,0,NULL,1,NULL,NULL,NULL,NULL),(81,'BE','Belgium',0,0,NULL,1,NULL,NULL,NULL,NULL),(82,'NL','Netherlands',0,0,NULL,1,NULL,NULL,NULL,NULL),(83,'PT','Portugal',0,0,NULL,1,NULL,NULL,NULL,NULL),(84,'IE','Ireland',0,0,NULL,1,NULL,NULL,NULL,NULL),(85,'MT','Malta',0,0,NULL,1,NULL,NULL,NULL,NULL),(86,'CY','Cyprus',0,0,NULL,1,NULL,NULL,NULL,NULL),(87,'GT','Guatemala',0,0,NULL,1,NULL,NULL,NULL,NULL),(88,'HN','Honduras',0,0,NULL,1,NULL,NULL,NULL,NULL),(89,'NI','Nicaragua',0,0,NULL,1,NULL,NULL,NULL,NULL),(90,'CR','Costa Rica',0,0,NULL,1,NULL,NULL,NULL,NULL),(91,'PA','Panama',0,0,NULL,1,NULL,NULL,NULL,NULL),(92,'DO','Dominican Republic',0,0,NULL,1,NULL,NULL,NULL,NULL),(93,'HT','Haiti',0,0,NULL,1,NULL,NULL,NULL,NULL),(94,'JM','Jamaica',0,0,NULL,1,NULL,NULL,NULL,NULL),(95,'BS','Bahamas',0,0,NULL,1,NULL,NULL,NULL,NULL),(96,'CU','Cuba',0,0,NULL,1,NULL,NULL,NULL,NULL),(97,'TR','Turkey',0,0,NULL,1,NULL,NULL,NULL,NULL),(98,'BY','Belarus',0,0,NULL,1,NULL,NULL,NULL,NULL),(99,'BG','Bulgaria',0,0,NULL,1,NULL,NULL,NULL,NULL),(100,'MD','Moldova',0,0,NULL,1,NULL,NULL,NULL,NULL),(101,'AM','Armenia',0,0,NULL,1,NULL,NULL,NULL,NULL),(102,'KY','Cayman Islands',0,0,NULL,1,NULL,NULL,NULL,NULL),(103,'GG','Guernsey',0,0,NULL,1,NULL,NULL,NULL,NULL),(104,'JE','Jersey',0,0,NULL,1,NULL,NULL,NULL,NULL),(105,'IM','Isle of Man',0,0,NULL,1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_21_101747_create_country_table',1),(5,'2025_01_22_173406_create_assets_name',1),(6,'2025_01_28_175217_create_activity_logs_table',1),(7,'2025_02_03_205021_create_personal_access_tokens_table',1),(8,'2025_02_04_064035_add_otp_fields_to_users_table',1),(9,'2025_02_04_122651_create_permission_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_module_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_users','User Management','Users','View Users','api','2025-02-04 06:30:39','2025-02-04 06:30:39'),(2,'edit_users','User Management','Users','Edit Users','api','2025-02-04 06:30:39','2025-02-04 06:30:39'),(3,'delete_users','User Management','Users','Delete Users','api','2025-02-04 06:30:39','2025-02-04 06:30:39');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'API Token','2956b02a61e87df36e2115aa889b01d3e42c1c6bb8ce50d8f6e7b2dccd0486da','[\"*\"]',NULL,NULL,'2025-02-04 06:35:36','2025-02-04 06:35:36'),(2,'App\\Models\\User',1,'API Token','ff7ee6526488dbc805dfcdb4ce3572fdd797e956ed9586b1320c08020344999a','[\"*\"]','2025-02-04 06:44:49',NULL,'2025-02-04 06:35:54','2025-02-04 06:44:49'),(3,'App\\Models\\User',1,'API Token','e1d22323433935ef467a959b76c0f78a62fad71eef0f7d9a195d03a8de76c63d','[\"*\"]',NULL,NULL,'2025-02-04 06:44:54','2025-02-04 06:44:54'),(4,'App\\Models\\User',1,'API Token','d17bea1af04816f7e79adb113de93d023c81f7844e26b141fe6df8254be61bf7','[\"*\"]','2025-02-04 07:05:30',NULL,'2025-02-04 06:45:07','2025-02-04 07:05:30'),(5,'App\\Models\\User',1,'API Token','3e10584675311e22eee0380c4b64c53241d4cdaa4ed544bef2e77d9923b4a112','[\"*\"]','2025-02-04 11:17:38',NULL,'2025-02-04 07:05:36','2025-02-04 11:17:38'),(6,'App\\Models\\User',2,'API Token','cbb0597e3e6b243c1e0b9420b13348648bb64e8b74de82b8a63bd53e60f56c15','[\"*\"]',NULL,NULL,'2025-02-04 11:24:12','2025-02-04 11:24:12'),(7,'App\\Models\\User',1,'API Token','90910579c0bb5f8e408850f3a83d800714ff74177e0f9fe19b02395701a77314','[\"*\"]','2025-02-04 13:25:05',NULL,'2025-02-04 11:30:12','2025-02-04 13:25:05'),(8,'App\\Models\\User',1,'API Token','77a3227be2f67ab675e95d7d97d5b3a20cc1f1f12a5f752e2202ab9edfa26c01','[\"*\"]','2025-02-04 13:26:40',NULL,'2025-02-04 11:35:58','2025-02-04 13:26:40');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(1,2),(2,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','api','2025-02-04 06:32:09','2025-02-04 06:32:09'),(2,'editor','api','2025-02-04 06:32:10','2025-02-04 06:32:10');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@rapid.com','admin',NULL,'$2y$12$kPz6r59TcGT5O.QOz3Mwk.qxJqybaD/17pjPNHxgcBFRR18z85C66',NULL,1,NULL,'2025-02-04 06:35:36','2025-02-04 11:35:58'),(2,'Admin01','admin01@rapid.com','admin01',NULL,'$2y$12$YOXGfGU20wwBsu8dF0g1XO.BNrM/CWG10oEDvVtBpn4f0jBetYU6q',NULL,0,NULL,'2025-02-04 11:24:12','2025-02-04 11:24:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-05 14:37:59
