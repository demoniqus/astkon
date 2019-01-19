-- USE `astkon`;
-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: astkon
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.18.04.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `id_article` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_name` varchar(300) NOT NULL,
  `id_measure` int(11) unsigned NOT NULL,
  `vendor_code` varchar(50) DEFAULT NULL COMMENT 'Код производителя',
  `is_archive` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Элемент помещен в архив',
  `id_article_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_article`),
  KEY `fk_article_vendor_code_idx` (`vendor_code`),
  KEY `fk_article_measure_idx` (`id_measure`),
  KEY `fk_article__acticle_category_idx` (`id_article_category`),
  FULLTEXT KEY `fk_article_article_name_idx` (`article_name`),
  CONSTRAINT `fk_article__article_category` FOREIGN KEY (`id_article_category`) REFERENCES `article_category` (`id_article_category`),
  CONSTRAINT `fk_article__measure` FOREIGN KEY (`id_measure`) REFERENCES `measure` (`id_measure`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_balance`
--

DROP TABLE IF EXISTS `article_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_balance` (
  `id_article_balance` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` bigint(20) unsigned NOT NULL,
  `id_user_group` int(10) unsigned NOT NULL,
  `balance` decimal(30,15) NOT NULL DEFAULT '0.000000000000000',
  PRIMARY KEY (`id_article_balance`),
  UNIQUE KEY `article__user_group_idx` (`id_article`,`id_user_group`),
  KEY `fk_article_balance__article_idx` (`id_article`),
  KEY `fk_article_balance__user_group_idx` (`id_user_group`),
  CONSTRAINT `fk_article_balance__article` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  CONSTRAINT `fk_article_balance__user_group` FOREIGN KEY (`id_user_group`) REFERENCES `user_group` (`id_user_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_balance`
--

LOCK TABLES `article_balance` WRITE;
/*!40000 ALTER TABLE `article_balance` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_balance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_category`
--

DROP TABLE IF EXISTS `article_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_category` (
  `id_article_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `is_writeoff` bit(1) NOT NULL DEFAULT b'1',
  `is_saleable` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id_article_category`),
  UNIQUE KEY `category_name_UNIQUE` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_category`
--

LOCK TABLES `article_category` WRITE;
/*!40000 ALTER TABLE `article_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `build_object`
--

DROP TABLE IF EXISTS `build_object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `build_object` (
  `id_build_object` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `build_object_name` varchar(500) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id_build_object`),
  UNIQUE KEY `object_name_UNIQUE` (`build_object_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `build_object`
--

LOCK TABLES `build_object` WRITE;
/*!40000 ALTER TABLE `build_object` DISABLE KEYS */;
/*!40000 ALTER TABLE `build_object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_balance_method`
--

DROP TABLE IF EXISTS `change_balance_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_balance_method` (
  `id_change_balance_method` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Операции могут менять состояние запаса двумя способами - в момент фиксации (инвентаризация и поступление - они засчитываются на запасе только после закрытия документа) и в момент создания (расход, списание, пользование - эти операции сразу резервируют артикулы)',
  `method_name` varchar(30) NOT NULL,
  PRIMARY KEY (`id_change_balance_method`),
  UNIQUE KEY `method_name_UNIQUE` (`method_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_balance_method`
--

LOCK TABLES `change_balance_method` WRITE;
/*!40000 ALTER TABLE `change_balance_method` DISABLE KEYS */;
INSERT INTO `change_balance_method` VALUES (1,'in_creation'),(2,'in_fixation');
/*!40000 ALTER TABLE `change_balance_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `measure`
--

DROP TABLE IF EXISTS `measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `measure` (
  `id_measure` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `measure_name` varchar(50) NOT NULL,
  `is_split` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Признак делимости величины',
  `precision` int(10) unsigned DEFAULT NULL COMMENT 'Точность деления величины',
  PRIMARY KEY (`id_measure`),
  UNIQUE KEY `measure_name_UNIQUE` (`measure_name`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `measure`
--

LOCK TABLES `measure` WRITE;
/*!40000 ALTER TABLE `measure` DISABLE KEYS */;
INSERT INTO `measure` VALUES (1,'мм<sup>3</sup>',_binary '\0',0),(2,'см<sup>3</sup>',_binary '\0',0),(3,'дм<sup>3</sup>',_binary '',3),(4,'м<sup>3</sup>',_binary '',3),(5,'т',_binary '',3),(6,'кг',_binary '',8),(7,'г',_binary '',9),(8,'мг',_binary '',9),(9,'л',_binary '',3),(10,'шт',_binary '\0',0),(11,'ящ',_binary '\0',0),(12,'меш',_binary '\0',0),(13,'кор',_binary '\0',0),(14,'км',_binary '',3),(15,'м',_binary '',3),(16,'дм',_binary '',3),(17,'см',_binary '',3),(18,'мм',_binary '',3),(62,'кв.мм.',_binary '\0',NULL);
/*!40000 ALTER TABLE `measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation`
--

DROP TABLE IF EXISTS `operation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operation` (
  `id_operation` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания документа',
  `id_operation_type` int(10) unsigned NOT NULL,
  `operation_info` json NOT NULL,
  `id_operation_state` int(10) unsigned NOT NULL,
  `fix_datetime` datetime DEFAULT NULL COMMENT 'Дата изменения статуса операции на "Зафиксировано"',
  `id_user_group` int(10) unsigned NOT NULL,
  `linked_data` json DEFAULT NULL COMMENT 'Информация о дополнительных элементах, связанных с операцией, например пользователями, за которыми временно зарезервирован инструмент, или объекты, на которые израсходованы материалы.\nИнформация раскладывается по наименованим сущностей, к которым она принадлежит',
  PRIMARY KEY (`id_operation`),
  KEY `fk_operation_operation_type_idx` (`id_operation_type`),
  KEY `fk_operation_operation_state_idx` (`id_operation_state`),
  KEY `fk_operation__user_group_idx` (`id_user_group`),
  CONSTRAINT `fk_operation__operation_state` FOREIGN KEY (`id_operation_state`) REFERENCES `operation_state` (`id_operation_state`),
  CONSTRAINT `fk_operation__operation_type` FOREIGN KEY (`id_operation_type`) REFERENCES `operation_type` (`id_operation_type`),
  CONSTRAINT `fk_operation__user_group` FOREIGN KEY (`id_user_group`) REFERENCES `user_group` (`id_user_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation`
--

LOCK TABLES `operation` WRITE;
/*!40000 ALTER TABLE `operation` DISABLE KEYS */;
/*!40000 ALTER TABLE `operation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_item`
--

DROP TABLE IF EXISTS `operation_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operation_item` (
  `id_operation_item` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` bigint(20) unsigned NOT NULL,
  `id_operation` bigint(20) unsigned NOT NULL,
  `operation_count` decimal(30,15) NOT NULL DEFAULT '0.000000000000000' COMMENT 'Количество в операции',
  `consignment_balance` decimal(30,15) NOT NULL DEFAULT '0.000000000000000' COMMENT 'Остаток от данной партии',
  `operation_item_info` json DEFAULT NULL COMMENT 'Информация об операции',
  PRIMARY KEY (`id_operation_item`),
  KEY `fk_operation_item_article_idx` (`id_article`),
  KEY `fk_operation_item_operation_idx` (`id_operation`),
  CONSTRAINT `fk_operation_item_article` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  CONSTRAINT `fk_operation_item_operation` FOREIGN KEY (`id_operation`) REFERENCES `operation` (`id_operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_item`
--

LOCK TABLES `operation_item` WRITE;
/*!40000 ALTER TABLE `operation_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `operation_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_state`
--

DROP TABLE IF EXISTS `operation_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operation_state` (
  `id_operation_state` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state_name` varchar(30) NOT NULL,
  `state_label` varchar(30) DEFAULT NULL,
  `state_comment` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_operation_state`),
  UNIQUE KEY `state_name_UNIQUE` (`state_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_state`
--

LOCK TABLES `operation_state` WRITE;
/*!40000 ALTER TABLE `operation_state` DISABLE KEYS */;
INSERT INTO `operation_state` VALUES (1,'new','Новый','Операция еще не зафиксирована и не отражается на состоянии текущего запаса'),(2,'fixed','Зафиксирован','Операция зафиксирована и отражена в текущем запасе');
/*!40000 ALTER TABLE `operation_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_type`
--

DROP TABLE IF EXISTS `operation_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operation_type` (
  `id_operation_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operation_name` varchar(25) NOT NULL,
  `operation_label` varchar(30) NOT NULL,
  `id_change_balance_method` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_operation_type`),
  UNIQUE KEY `operation_name_UNIQUE` (`operation_name`),
  KEY `fk_operation_type__change_balance_method_idx` (`id_change_balance_method`),
  CONSTRAINT `fk_operation_type__change_balance_method` FOREIGN KEY (`id_change_balance_method`) REFERENCES `change_balance_method` (`id_change_balance_method`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_type`
--

LOCK TABLES `operation_type` WRITE;
/*!40000 ALTER TABLE `operation_type` DISABLE KEYS */;
INSERT INTO `operation_type` VALUES (1,'Income','Приход',2),(2,'WriteOff','Списание',1),(3,'Reserving','Временное пользование',1),(4,'Sale','Расход',1),(5,'Inventory','Инвентаризация',2);
/*!40000 ALTER TABLE `operation_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `people` (
  `id_people` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `people_name` varchar(200) NOT NULL,
  `post_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_people`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
/*!40000 ALTER TABLE `people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(45) NOT NULL,
  `config` json DEFAULT NULL COMMENT 'Настройки пользователя',
  `has_account` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Флаг наличия аккаунта в системе',
  `id_user_group` int(10) unsigned NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `is_admin` bit(1) NOT NULL DEFAULT b'0',
  `is_delete` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Флаг удаленного пользователя',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `fk_user__user_group_idx` (`id_user_group`),
  CONSTRAINT `fk_user__user_group` FOREIGN KEY (`id_user_group`) REFERENCES `user_group` (`id_user_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `id_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_group_name` varchar(100) NOT NULL,
  `comment` varchar(3500) DEFAULT NULL,
  PRIMARY KEY (`id_user_group`),
  UNIQUE KEY `user_group_name_UNIQUE` (`user_group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-01-19 16:24:00
