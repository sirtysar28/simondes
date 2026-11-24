-- MySQL dump 10.13  Distrib 9.6.0, for macos14.8 (x86_64)
--
-- Host: localhost    Database: simondes
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  KEY `activity_logs_user_id_action_index` (`user_id`,`action`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `fund_transactions`
--

DROP TABLE IF EXISTS `fund_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fund_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `village_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spending_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `funding_source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint unsigned NOT NULL,
  `vendor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidence_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fund_transactions_user_id_foreign` (`user_id`),
  KEY `fund_transactions_village_id_date_index` (`village_id`,`date`),
  KEY `fund_transactions_category_index` (`category`),
  CONSTRAINT `fund_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fund_transactions_village_id_foreign` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fund_transactions`
--

LOCK TABLES `fund_transactions` WRITE;
/*!40000 ALTER TABLE `fund_transactions` DISABLE KEYS */;
INSERT INTO `fund_transactions` VALUES (1,1,'2026-06-02','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',470005000,'CV Simulasi Jaya','SPJ/2026/001/A','https://example.com/bukti/2026/1a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(2,1,'2026-10-02','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',300495000,'Koperasi Desa Simulasi','SPJ/2026/001/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(3,2,'2026-06-03','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',490196000,'CV Simulasi Jaya','SPJ/2026/002/A','https://example.com/bukti/2026/2a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(4,2,'2026-10-03','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',313404000,'Koperasi Desa Simulasi','SPJ/2026/002/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(5,3,'2026-06-04','Pembinaan Kemasyarakatan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',410652000,'CV Simulasi Jaya','SPJ/2026/003/A','https://example.com/bukti/2026/3a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(6,3,'2026-10-04','Pemberdayaan Masyarakat','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',262548000,'Koperasi Desa Simulasi','SPJ/2026/003/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(7,4,'2026-06-05','Pemberdayaan Masyarakat','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',500688000,'CV Simulasi Jaya','SPJ/2026/004/A','https://example.com/bukti/2026/4a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(8,4,'2026-10-05','Penanggulangan Bencana','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',320112000,'Koperasi Desa Simulasi','SPJ/2026/004/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(9,5,'2026-06-06','Penanggulangan Bencana','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',796050000,'CV Simulasi Jaya','SPJ/2026/005/A','https://example.com/bukti/2026/5a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(10,5,'2026-10-06','Penyelenggaraan Pemerintahan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',508950000,'Koperasi Desa Simulasi','SPJ/2026/005/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(11,6,'2026-06-07','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',295240000,'CV Simulasi Jaya','SPJ/2026/006/A','https://example.com/bukti/2026/6a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(12,6,'2026-10-07','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',188760000,'Koperasi Desa Simulasi','SPJ/2026/006/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(13,7,'2026-06-08','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',488000000,'CV Simulasi Jaya','SPJ/2026/007/A','https://example.com/bukti/2026/7a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(14,7,'2026-10-08','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',312000000,'Koperasi Desa Simulasi','SPJ/2026/007/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(15,8,'2026-06-09','Pembinaan Kemasyarakatan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',428769000,'CV Simulasi Jaya','SPJ/2026/008/A','https://example.com/bukti/2026/8a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(16,8,'2026-10-09','Pemberdayaan Masyarakat','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',274131000,'Koperasi Desa Simulasi','SPJ/2026/008/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(17,9,'2026-06-10','Pemberdayaan Masyarakat','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',421022000,'CV Simulasi Jaya','SPJ/2026/009/A','https://example.com/bukti/2026/9a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(18,9,'2026-10-10','Penanggulangan Bencana','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',269178000,'Koperasi Desa Simulasi','SPJ/2026/009/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(19,10,'2026-06-11','Penanggulangan Bencana','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',545584000,'CV Simulasi Jaya','SPJ/2026/010/A','https://example.com/bukti/2026/10a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(20,10,'2026-10-11','Penyelenggaraan Pemerintahan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',348816000,'Koperasi Desa Simulasi','SPJ/2026/010/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(21,11,'2026-06-12','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',572423999,'CV Simulasi Jaya','SPJ/2026/011/A','https://example.com/bukti/2026/11a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(22,11,'2026-10-12','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',365976000,'Koperasi Desa Simulasi','SPJ/2026/011/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(23,12,'2026-06-13','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2026',245952000,'CV Simulasi Jaya','SPJ/2026/012/A','https://example.com/bukti/2026/12a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(24,12,'2026-10-13','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2026',157248000,'Koperasi Desa Simulasi','SPJ/2026/012/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(25,13,'2025-06-02','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',490440000,'CV Simulasi Jaya','SPJ/2025/001/A','https://example.com/bukti/2025/1a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(26,13,'2025-10-02','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',313560000,'Koperasi Desa Simulasi','SPJ/2025/001/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(27,14,'2025-06-03','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',515206000,'CV Simulasi Jaya','SPJ/2025/002/A','https://example.com/bukti/2025/2a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(28,14,'2025-10-03','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',329394000,'Koperasi Desa Simulasi','SPJ/2025/002/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(29,15,'2025-06-04','Pembinaan Kemasyarakatan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',426207000,'CV Simulasi Jaya','SPJ/2025/003/A','https://example.com/bukti/2025/3a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(30,15,'2025-10-04','Pemberdayaan Masyarakat','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',272493000,'Koperasi Desa Simulasi','SPJ/2025/003/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(31,16,'2025-06-05','Pemberdayaan Masyarakat','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',523868000,'CV Simulasi Jaya','SPJ/2025/004/A','https://example.com/bukti/2025/4a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(32,16,'2025-10-05','Penanggulangan Bencana','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',334932000,'Koperasi Desa Simulasi','SPJ/2025/004/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(33,17,'2025-06-06','Penanggulangan Bencana','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',823500000,'CV Simulasi Jaya','SPJ/2025/005/A','https://example.com/bukti/2025/5a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(34,17,'2025-10-06','Penyelenggaraan Pemerintahan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',526500000,'Koperasi Desa Simulasi','SPJ/2025/005/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(35,18,'2025-06-07','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',308660000,'CV Simulasi Jaya','SPJ/2025/006/A','https://example.com/bukti/2025/6a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(36,18,'2025-10-07','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',197340000,'Koperasi Desa Simulasi','SPJ/2025/006/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(37,19,'2025-06-08','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',507520000,'CV Simulasi Jaya','SPJ/2025/007/A','https://example.com/bukti/2025/7a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(38,19,'2025-10-08','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',324480000,'Koperasi Desa Simulasi','SPJ/2025/007/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(39,20,'2025-06-09','Pembinaan Kemasyarakatan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',450424000,'CV Simulasi Jaya','SPJ/2025/008/A','https://example.com/bukti/2025/8a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(40,20,'2025-10-09','Pemberdayaan Masyarakat','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',287976000,'Koperasi Desa Simulasi','SPJ/2025/008/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(41,21,'2025-06-10','Pemberdayaan Masyarakat','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',438712000,'CV Simulasi Jaya','SPJ/2025/009/A','https://example.com/bukti/2025/9a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(42,21,'2025-10-10','Penanggulangan Bencana','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',280488000,'Koperasi Desa Simulasi','SPJ/2025/009/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(43,22,'2025-06-11','Penanggulangan Bencana','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',571814000,'CV Simulasi Jaya','SPJ/2025/010/A','https://example.com/bukti/2025/10a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(44,22,'2025-10-11','Penyelenggaraan Pemerintahan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',365586000,'Koperasi Desa Simulasi','SPJ/2025/010/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(45,23,'2025-06-12','Penyelenggaraan Pemerintahan','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',593468999,'CV Simulasi Jaya','SPJ/2025/011/A','https://example.com/bukti/2025/11a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(46,23,'2025-10-12','Pembangunan Desa','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',379431000,'Koperasi Desa Simulasi','SPJ/2025/011/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(47,24,'2025-06-13','Pembangunan Desa','Barang dan Jasa','Dana Desa (DD)','Kegiatan simulasi tahap I TA 2025',256932000,'CV Simulasi Jaya','SPJ/2025/012/A','https://example.com/bukti/2025/12a',1,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(48,24,'2025-10-13','Pembinaan Kemasyarakatan','Belanja Modal','Dana Desa (DD)','Kegiatan simulasi tahap II TA 2025',164268000,'Koperasi Desa Simulasi','SPJ/2025/012/B',NULL,1,'2026-11-21 18:25:32','2026-11-21 18:25:32');
/*!40000 ALTER TABLE `fund_transactions` ENABLE KEYS */;
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
  `attempts` smallint unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_22_000001_create_villages_table',1),(5,'2026_11_22_000002_create_fund_transactions_table',1),(6,'2026_11_22_000003_create_settings_and_activity_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
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
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator DPMD','admin@simondes.id','2026-11-21 18:25:32','$2y$12$SzE2jhmQUq6AehOjjIkumuGY5lOsZAsIS//wUCXFRZUL2UilZJTH.',NULL,'2026-11-21 18:25:32','2026-11-21 18:25:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `villages`
--

DROP TABLE IF EXISTS `villages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `villages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint unsigned NOT NULL,
  `budget` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `villages_code_year_unique` (`code`,`year`),
  KEY `villages_year_district_index` (`year`,`district`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `villages`
--

LOCK TABLES `villages` WRITE;
/*!40000 ALTER TABLE `villages` DISABLE KEYS */;
INSERT INTO `villages` VALUES (1,'HAL-001-2026','Desa Simulasi 01','Jailolo',2026,1150000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(2,'HAL-002-2026','Desa Simulasi 02','Jailolo',2026,980000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(3,'HAL-003-2026','Desa Simulasi 03','Jailolo Selatan',2026,1320000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(4,'HAL-004-2026','Desa Simulasi 04','Jailolo Selatan',2026,1080000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(5,'HAL-005-2026','Desa Simulasi 05','Sahu',2026,1450000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(6,'HAL-006-2026','Desa Simulasi 06','Sahu',2026,1100000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(7,'HAL-007-2026','Desa Simulasi 07','Sahu Timur',2026,1250000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(8,'HAL-008-2026','Desa Simulasi 08','Sahu Timur',2026,990000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(9,'HAL-009-2026','Desa Simulasi 09','Ibu',2026,1190000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(10,'HAL-010-2026','Desa Simulasi 10','Ibu',2026,1040000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(11,'HAL-011-2026','Desa Simulasi 11','Ibu Selatan',2026,1360000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(12,'HAL-012-2026','Desa Simulasi 12','Ibu Selatan',2026,1120000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(13,'HAL-001-2025','Desa Simulasi 01','Jailolo',2025,1200000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(14,'HAL-002-2025','Desa Simulasi 02','Jailolo',2025,1030000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(15,'HAL-003-2025','Desa Simulasi 03','Jailolo Selatan',2025,1370000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(16,'HAL-004-2025','Desa Simulasi 04','Jailolo Selatan',2025,1130000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(17,'HAL-005-2025','Desa Simulasi 05','Sahu',2025,1500000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(18,'HAL-006-2025','Desa Simulasi 06','Sahu',2025,1150000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(19,'HAL-007-2025','Desa Simulasi 07','Sahu Timur',2025,1300000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(20,'HAL-008-2025','Desa Simulasi 08','Sahu Timur',2025,1040000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(21,'HAL-009-2025','Desa Simulasi 09','Ibu',2025,1240000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(22,'HAL-010-2025','Desa Simulasi 10','Ibu',2025,1090000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(23,'HAL-011-2025','Desa Simulasi 11','Ibu Selatan',2025,1410000000,'2026-11-21 18:25:32','2026-11-21 18:25:32'),(24,'HAL-012-2025','Desa Simulasi 12','Ibu Selatan',2025,1170000000,'2026-11-21 18:25:32','2026-11-21 18:25:32');
/*!40000 ALTER TABLE `villages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-22  8:31:39
