-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: firmetna_new_db
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
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260128221548','2026-02-02 10:19:40',261);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `bio` longtext DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `localisation` varchar(150) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `role_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@firmetna.com','$2y$13$h33lrpqnwcIdBe9.rzV.DuME1UxXcLHb23gnGK6UICT/CFQtGz5/y','ROLE_ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Actif',NULL),(2,'maryem@gmail.com','$2y$13$ryzOvEK1vTj.pcY6do7EWuQqS78qGUhmB4Zjp6reyfcPXJ83tM0bK','ROLE_CLIENT',NULL,NULL,NULL,'maryem','ouselati','+217 62538658',NULL,NULL,'2026-02-02 20:08:49','Actif','Client'),(3,'johnb@gmail.com','$2y$13$CB9Nw03YNxOczvme.rHX8OvWHSwPCbKLNTuQM9AfNRtbfdAiB01eS','ROLE_CLIENT',NULL,NULL,NULL,'john','bry','+217 93438772',NULL,NULL,'2026-02-02 20:11:21','Actif','Client'),(5,'nour@gmail.com','$2y$13$7e2MouK.HwOrnTPMkktZDOmzsapgWlpxGWjC4PAEtI9k3/2EXmyTe','ROLE_AGRICULTEUR',NULL,NULL,NULL,'nour','nou','+217 93438772',NULL,NULL,'2026-02-02 22:20:57','Actif','Agriculteur'),(6,'youmi@gmail.com','$2y$13$kPL7J1WnSXDHdIOinobm8..3tgU3Xy.J9UJehXy0lDpK71Y9whqBK','ROLE_USER',NULL,NULL,NULL,'youmi','ffffff','+217 93438772',NULL,NULL,'2026-02-03 20:36:41','Actif','Agriculteur'),(7,'youyou@gmail.com','$2y$13$4giuzTAWN4o0r02ylFmzFurGL3ed9KQimOR9/Js15Jqd4m4HfWbYC','ROLE_CLIENT',NULL,NULL,NULL,'youyou','rourou','+217 93438772',NULL,NULL,'2026-02-03 22:17:36','Actif','Client'),(8,'rome@gmail.com','$2y$13$RNRxVpFsWz20D3hOIEqjo.W90SwRjrYzUATv90ezVcepLWoWH8qcW','ROLE_CLIENT',NULL,NULL,NULL,'rome','retgh','+217 93438772',NULL,NULL,'2026-02-03 23:22:48','Actif','Client'),(9,'alex@gmail.com','$2y$13$aKXAt8XoAnJQg0MyEEg.jOfzyf7AbUgGdndmx9euw8hfpR89QtuT.','ROLE_AGRICULTEUR',NULL,NULL,NULL,'alex','byn','+217 93438772',NULL,NULL,'2026-02-04 12:47:05','Actif','Agriculteur'),(10,'ben@gmail.com','$2y$13$2D24.lD72ru/f9j61Gaw1OsuUT8GoKefye2WqVJr8CZYnRUe/eUpO','ROLE_AGRICULTEUR',NULL,NULL,NULL,'aysha','ben','+217 93438772',NULL,'/uploads/profiles/images-26-698332a061b7e.jpg','2026-02-04 12:50:56','Actif','Agriculteur'),(11,'jouri@gmail.com','$2y$13$gV886b6ZdAGGO/h3usC72eoda8WC.M5.3OYeOz26Z1Tu78BMG8BZa','ROLE_USER',NULL,NULL,NULL,'jouri','jourib',NULL,NULL,NULL,'2026-02-04 12:58:56','Actif','Agriculteur'),(12,'mark@gmail.com','$2y$13$6oCS1wQZh3xc2oggLL7H7.X8ra/y4gLU91jIUWvyGxulf1tUzc7Z2','ROLE_AGRICULTEUR',NULL,NULL,NULL,'mark','weslti','+217 93438772',NULL,NULL,'2026-02-04 19:27:03','Actif','Agriculteur');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-04 20:26:10
