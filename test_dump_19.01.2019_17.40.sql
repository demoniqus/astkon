USE `ck54269_astkon`;
-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: ck54269_astkon
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
  `article_name` varchar(255) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` VALUES (1,'Гвоздь железный, марка \"Fe2O3\"',10,'<b>01234567899876543210</b>',_binary '\0',7),(14,'Доска строганая `Europa`',4,'76465475778797',_binary '\0',12),(15,'Цемент \'CERESIT\' 25кг',12,'76465475778797',_binary '\0',12),(16,'Лист оцинкованный, 1200*600*1.5',10,'',_binary '\0',12),(17,'Лист оцинкованный, 1200*600*2.0',10,'',_binary '\0',1),(18,'Лист гофрированный 1000*500*1.0',10,'',_binary '\0',1),(19,'Лист оцинкованный 1200*600*1.0',10,NULL,_binary '\0',1),(20,'Лист оцинкованный 1000*500*1.0',10,NULL,_binary '\0',1),(21,'Лист оцинкованный 1000*500*1.5',10,NULL,_binary '\0',1),(22,'Лист оцинкованный 1000*500*2.0',10,NULL,_binary '\0',1),(23,'Труба пластиковая 1000*100',10,NULL,_binary '\0',1),(24,'Труба пластиковая 1000*200',10,NULL,_binary '\0',1),(25,'Труба пластиковая 2000*100',10,NULL,_binary '\0',1),(26,'Труба пластиковая 2000*200',10,NULL,_binary '\0',1),(27,'Труба пластиковая 3000*100',10,NULL,_binary '\0',1),(28,'Труба пластиковая 3000*200',10,NULL,_binary '\0',1),(29,'Труба пластиковая 4000*100',10,NULL,_binary '\0',1),(30,'Труба пластиковая 4000*200',10,NULL,_binary '\0',1),(31,'Цемент \'CERESIT\' M150 25КГ',12,NULL,_binary '\0',1),(32,'Цемент \'CERESIT\' M150 40КГ',12,NULL,_binary '\0',1),(33,'Цемент \'CERESIT\' M150 50КГ',12,NULL,_binary '\0',1),(34,'Цемент \'CERESIT\' M250 25КГ',12,NULL,_binary '\0',1),(35,'Цемент \'CERESIT\' M250 40КГ',12,NULL,_binary '\0',1),(36,'Цемент \'CERESIT\' M250 50КГ',12,NULL,_binary '\0',1),(37,'Цемент \'CERESIT\' M350 25КГ',12,NULL,_binary '\0',1),(38,'Цемент \'CERESIT\' M350 40КГ',12,NULL,_binary '\0',1),(39,'Цемент \'CERESIT\' M350 50КГ',12,NULL,_binary '\0',1),(40,'Гипс \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(41,'Гипс \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(42,'Гипс \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(43,'Гипс \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(44,'Гипс \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(45,'Гипс \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(46,'Гипс \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(47,'Гипс \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(48,'Гипс \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(49,'Штукатурка \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(50,'Штукатурка \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(51,'Штукатурка \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(52,'Штукатурка \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(53,'Штукатурка \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(54,'Штукатурка \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(55,'Штукатурка \'CERESIT\'  25КГ',12,NULL,_binary '\0',1),(56,'Штукатурка \'CERESIT\'  40КГ',12,NULL,_binary '\0',1),(57,'Штукатурка \'CERESIT\'  50КГ',12,NULL,_binary '\0',1),(58,'Цемент \'ASIA\' M150 25КГ',12,NULL,_binary '\0',1),(59,'Цемент \'ASIA\' M150 40КГ',12,NULL,_binary '\0',1),(60,'Цемент \'ASIA\' M150 50КГ',12,NULL,_binary '\0',1),(61,'Цемент \'ASIA\' M250 25КГ',12,NULL,_binary '\0',1),(62,'Цемент \'ASIA\' M250 40КГ',12,NULL,_binary '\0',1),(63,'Цемент \'ASIA\' M250 50КГ',12,NULL,_binary '\0',1),(64,'Цемент \'ASIA\' M350 25КГ',12,NULL,_binary '\0',1),(65,'Цемент \'ASIA\' M350 40КГ',12,NULL,_binary '\0',1),(66,'Цемент \'ASIA\' M350 50КГ',12,NULL,_binary '\0',1),(67,'Гипс \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(68,'Гипс \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(69,'Гипс \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(70,'Гипс \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(71,'Гипс \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(72,'Гипс \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(73,'Гипс \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(74,'Гипс \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(75,'Гипс \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(76,'Штукатурка \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(77,'Штукатурка \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(78,'Штукатурка \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(79,'Штукатурка \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(80,'Штукатурка \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(81,'Штукатурка \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(82,'Штукатурка \'ASIA\'  25КГ',12,NULL,_binary '\0',1),(83,'Штукатурка \'ASIA\'  40КГ',12,NULL,_binary '\0',1),(84,'Штукатурка \'ASIA\'  50КГ',12,NULL,_binary '\0',1),(103,'Дрель ручная, 380В',10,'',_binary '\0',19),(104,'Перфоратор Bosch, 2.7Дж',10,'',_binary '\0',19),(105,'Лобзик Makita, 14.4V',10,'',_binary '\0',19),(106,'Молоток Dexp, 800г',10,'',_binary '\0',20),(107,'Молоток Dexp, 400г',10,'',_binary '\0',20),(108,'Молоток Dexp, 400г',10,'',_binary '\0',20),(109,'Шуруповерт',10,'',_binary '\0',19),(110,'Лобзик Metabo, 800Вт',10,'346576764',_binary '\0',19),(111,'Бур для перфоратора, D20',10,'544034',_binary '\0',22);
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_balance`
--

LOCK TABLES `article_balance` WRITE;
/*!40000 ALTER TABLE `article_balance` DISABLE KEYS */;
INSERT INTO `article_balance` VALUES (1,1,10,91944.000000000000000),(2,14,10,102.091000000000000),(3,15,10,433.000000000000000),(4,16,10,467.000000000000000),(5,17,10,499.000000000000000),(6,18,10,500.000000000000000),(7,19,10,800.000000000000000),(8,20,10,400.000000000000000),(9,21,10,321.000000000000000),(10,22,10,123.000000000000000),(11,30,10,900.000000000000000),(12,31,10,900.000000000000000),(13,32,10,1212.000000000000000),(14,47,10,1495.000000000000000),(15,83,10,450.000000000000000),(16,84,10,220.000000000000000),(17,82,10,300.000000000000000),(18,1,11,21999.000000000000000),(19,14,11,99.000000000000000),(20,15,11,1000.000000000000000),(21,16,11,1000.000000000000000),(22,24,11,1000.000000000000000),(23,25,11,1000.000000000000000),(24,26,11,2000.000000000000000),(25,109,10,0.000000000000000),(26,75,10,192.000000000000000);
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_category`
--

LOCK TABLES `article_category` WRITE;
/*!40000 ALTER TABLE `article_category` DISABLE KEYS */;
INSERT INTO `article_category` VALUES (1,'',_binary '',_binary ''),(4,'Ручной инструмент',_binary '\0',_binary ''),(5,'Рабочий инвентарь',_binary '',_binary '\0'),(6,'Садовый инвентарь',_binary '\0',_binary ''),(7,'Крепеж',_binary '\0',_binary ''),(8,'Сухие смеси, внутренние работы',_binary '\0',_binary ''),(9,'Сухие смеси, внешние работы',_binary '\0',_binary ''),(10,'Акриловые краски',_binary '\0',_binary ''),(11,'Масляные краски',_binary '\0',_binary ''),(12,'Пиломатериалы',_binary '\0',_binary ''),(19,'Электроинструмент ручной',_binary '\0',_binary ''),(20,'Инструмент ручной',_binary '\0',_binary ''),(21,'Малярный инструмент',_binary '',_binary '\0'),(22,'Расходники',_binary '\0',_binary '');
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
  `build_object_name` varchar(255) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id_build_object`),
  UNIQUE KEY `object_name_UNIQUE` (`build_object_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `build_object`
--

LOCK TABLES `build_object` WRITE;
/*!40000 ALTER TABLE `build_object` DISABLE KEYS */;
INSERT INTO `build_object` VALUES (1,'Объект 1','Тестовый объект \'1\"'),(2,'Объект 2',''),(3,'Объект 3','Тестовый объект \\\'3333\"\\'),(4,'Арт-Сплайн','Россия, Московская область, Подольск, Комсомольская улица, 1. +7 (495) 923-26-29, +7 (495) 923-29-26. пн-сб 9:00–20:00. http://art-spline.ru'),(5,'Бункер','Адрес: 142103, Россия, Московская область, Подольск, Рощинская улица, 20\nТелефон:+7 (49675) 4-01-74, +7 (963) 660-56-60, +7 (499) 390-96-23\nЧасы работы:пн-пт 9:00–18:00; сб 9:00–14:00\nСайт:http://buncer.ru'),(6,'ПромПодъем, ООО','Адрес: 142115, Подольск, ул. Правды, 28\nТелефон:+7 (4967) 57-46-57\nСайт:http://prompodjem.ru/'),(7,'Бетон Подолье, ООО','Адрес: Подольск, ул. Плещеевская, 7\nТелефон:+7 (495) 744-67-88\nЧасы работы:пн-пт 08:00-20:00\nСайт:http://beton-pd.ru/'),(8,'ЛедТехника, ООО','Адрес: 142124, Подольск, ул. Станционная, 2\nТелефон:+7 (499) 390-79-12\nЧасы работы:пн-пт 08:30-17:00\nСайт:http://www.ledtekhnika.ru'),(9,'Астэк-Мт','Адрес: Россия, Москва, поселение Рязановское, посёлок фабрики имени 1 Мая, 28с1\nТелефон:+7 (495) 921-36-80\nЧасы работы:пн-пт 9:00–18:00\nСайт:http://www.astek-mt.ru'),(10,'Akur-Sv Симферопольское ш 22','Адрес: Москва, Симферопольское ш., 22\nТелефон:+7 (495) 979-86-62\nЧасы работы:пн-пт 9:00–19:00\nСайт:http://www.akur-sv.ru'),(11,'Akur-SV','Адрес: Москва, Щербинка г., Симферопольское ш., 22, промзона\nТелефон:+7 (499) 399-32-03, +7 (926) 319-42-08, +7 (926) 834-99-95\nЧасы работы:ежедневно, 9:00–21:00\nСайт:http://akur-sv.ru'),(12,'Bars','Адрес: Московская обл., Подольск г., Домодедовское ш., 41\nТелефон:+7 (495) 737-74-65, +7 (495) 308-07-33\nЧасы работы:пн-пт 8:00–17:00\nСайт:http://www.staldveri.ru'),(13,'Exotic','Адрес: Московская обл., Подольск, ул. Силикатная, 5а\nТелефон:+7 (915) 188-24-09\nЧасы работы:ежедневно, 10:00–20:00\nСайт:http://exoticaroma.ru'),(14,'GreenEco','Адрес: Россия, Московская область, Подольск, улица Лобачёва, 6\nТелефон:+7 (495) 664-95-83\nЧасы работы:пн-пт 9:00–18:00\nСайт:http://www.greenecorus.com'),(15,'Itergroup','Адрес: Московская обл., городской округ Подольск, Макарово дер.\nТелефон:+7 (495) 669-53-92, +7 (926) 005-83-38, +7 (495) 646-96-45\nЧасы работы:пн-пт 9:00–18:00\nСайт:http://www.itergroup.ru'),(16,'Laser Craft','Адрес: Московская обл., Подольск г., ул. Бронницкая, 3\nТелефон:+7 (495) 988-15-25\nЧасы работы:пн-пт 8:00–17:00\nСайт:http://www.disc.ru'),(17,'RusDeck','Адрес: Московская обл., Подольск, ул. Комсомольская, 1\nТелефон:+7 (495) 849-46-47\nЧасы работы:пн-сб 8:00–20:00\nСайт:http://rusdeck.ru'),(18,'Vivatex','Адрес: Россия, Московская область, Подольск, Комсомольская улица, 1\nТелефон:+7 (4967) 58-36-38, +7 (495) 580-64-28\nЧасы работы:пн-пт 9:30–18:30; сб 11:00–18:00\nСайт:http://www.vivatex.ru'),(19,'АБК-Спец','Адрес: Россия, Московская область, Подольск, улица Лобачёва, 20\nТелефон:+7 (906) 733-28-22\nЧасы работы:пн-пт 9:00–17:00\nСайт:http://www.perchatki.su'),(20,'Азбука Камня','Адрес: Россия, Московская область, Подольск, Большая Серпуховская улица, 43\nТелефон:+7 (499) 390-21-86, +7 (499) 704-39-92\nЧасы работы:пн-сб 9:00–18:00; вс 9:00–17:00\nСайт:http://manufacture.azbuka-kamnya.ru'),(21,'Академия инструмента','Адрес: Московская обл., Подольск г., ул. Железнодорожная, 1\nТелефон:+7 (496) 768-00-37\nЧасы работы:пн-чт 9:00–18:00; пт 9:00–17:00\nСайт:http://academy59.ru'),(22,'Бетонный завод РБУ БетонБаза','Адрес: Москва, Щербинка г., ул. Южная, промзона\nТелефон:+7 (499) 390-46-77, +7 (965) 127-55-36\nЧасы работы:ежедневно, 08:00-21:00\nСайт:http://betonsherbinka.ru');
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
  `operation_info` mediumtext NOT NULL,
  `id_operation_state` int(10) unsigned NOT NULL,
  `fix_datetime` datetime DEFAULT NULL COMMENT 'Дата изменения статуса операции на "Зафиксировано"',
  `id_user_group` int(10) unsigned NOT NULL,
  `linked_data` mediumtext COMMENT 'Информация о дополнительных элементах, связанных с операцией, например пользователями, за которыми временно зарезервирован инструмент, или объекты, на которые израсходованы материалы.\nИнформация раскладывается по наименованим сущностей, к которым она принадлежит',
  PRIMARY KEY (`id_operation`),
  KEY `fk_operation_operation_type_idx` (`id_operation_type`),
  KEY `fk_operation_operation_state_idx` (`id_operation_state`),
  KEY `fk_operation__user_group_idx` (`id_user_group`),
  CONSTRAINT `fk_operation__operation_state` FOREIGN KEY (`id_operation_state`) REFERENCES `operation_state` (`id_operation_state`),
  CONSTRAINT `fk_operation__operation_type` FOREIGN KEY (`id_operation_type`) REFERENCES `operation_type` (`id_operation_type`),
  CONSTRAINT `fk_operation__user_group` FOREIGN KEY (`id_user_group`) REFERENCES `user_group` (`id_user_group`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation`
--

LOCK TABLES `operation` WRITE;
/*!40000 ALTER TABLE `operation` DISABLE KEYS */;
INSERT INTO `operation` VALUES (1,'2019-01-09 19:20:50',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-09 20:15:51\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:18:16\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:20:44\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:23:41\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:26:16\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-09 20:31:14',10,NULL),(2,'2019-01-09 19:39:24',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-09 19:58:24\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 19:58:50\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 19:59:12\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:00:47\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:01:52\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:02:55\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:05:08\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:05:23\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:05:41\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:06:26\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 20:06:53\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-09 20:54:55',10,NULL),(3,'2019-01-09 20:43:21',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-09 20:43:26',10,NULL),(4,'2019-01-09 21:21:18',4,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-09 21:27:16\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:27:42\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:28:54\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:29:56\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:31:01\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:31:12\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:32:29\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:33:38\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-09 21:35:56\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-10 16:51:59\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-10 16:53:17\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-10 16:54:08\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-15 19:30:34',10,'{\"BuildObject\": [1, 5]}'),(5,'2019-01-10 09:11:26',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 9, \"caption\": \"Дмитрий 2\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 9, \"caption\": \"Дмитрий 2\"}}',2,'2019-01-10 09:11:32',11,NULL),(7,'2019-01-10 11:43:46',3,'{\"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-10 17:34:01\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-10 17:34:39\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-10 17:35:12\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',1,NULL,10,'{\"User\": [9]}'),(8,'2019-01-10 15:58:34',1,'{\"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',1,NULL,10,NULL),(9,'2019-01-10 18:22:45',4,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-10 18:25:09',10,'{\"BuildObject\": [4]}'),(10,'2019-01-10 19:27:49',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-10 19:27:56',10,NULL),(13,'2019-01-10 19:42:33',2,'{\"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',1,NULL,10,NULL),(14,'2019-01-12 14:28:31',3,'{\"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',1,NULL,10,'{\"User\": [7]}'),(15,'2019-01-12 14:31:02',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-12 14:31:51',10,NULL),(16,'2019-01-12 14:51:47',5,'{\"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-12 14:52:01\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-12 14:52:24\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',1,NULL,10,NULL),(17,'2019-01-14 14:43:20',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 10, \"caption\": \"Антон\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 10, \"caption\": \"Антон\"}}',2,'2019-01-14 14:43:45',10,NULL),(18,'2019-01-14 14:44:44',3,'{\"edited\": {\"items\": [{\"id_user\": 8, \"datetime\": \"2019-01-17 14:55:54\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-17 14:56:11\", \"user_name\": \"Дмитрий\"}, {\"id_user\": 8, \"datetime\": \"2019-01-17 15:08:18\", \"user_name\": \"Дмитрий\"}], \"label\": \"Изменен\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 10, \"caption\": \"Антон\"}}',1,NULL,10,'{\"User\": [9]}'),(19,'2019-01-17 14:44:37',1,'{\"fixer\": {\"label\": \"Перевел документ в статус \'Зафиксирован\'\", \"value\": 8, \"caption\": \"Дмитрий\"}, \"creator\": {\"label\": \"Создал документ\", \"value\": 8, \"caption\": \"Дмитрий\"}}',2,'2019-01-17 14:44:38',10,NULL),(21,'2019-01-17 17:07:13',2,'{\"creator\": {\"label\": \"Создал документ\", \"value\": 9, \"caption\": \"Дмитрий_2\"}, \"change_type\": {\"label\": \"Изменен тип документа\", \"value\": 9, \"caption\": \"Пользователем Дмитрий_2 тип документа изменен с \\\"Временное пользование\\\" на \\\"Списание\\\"\", \"datetime\": \"2019-01-17 17:07:20\"}}',1,NULL,11,'{\"User\": [9]}');
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
  `operation_item_info` mediumtext COMMENT 'Информация об операции',
  PRIMARY KEY (`id_operation_item`),
  KEY `fk_operation_item_article_idx` (`id_article`),
  KEY `fk_operation_item_operation_idx` (`id_operation`),
  CONSTRAINT `fk_operation_item_article` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`),
  CONSTRAINT `fk_operation_item_operation` FOREIGN KEY (`id_operation`) REFERENCES `operation` (`id_operation`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_item`
--

LOCK TABLES `operation_item` WRITE;
/*!40000 ALTER TABLE `operation_item` DISABLE KEYS */;
INSERT INTO `operation_item` VALUES (130,30,2,900.000000000000000,900.000000000000000,NULL),(131,31,2,900.000000000000000,900.000000000000000,NULL),(132,32,2,1300.000000000000000,1300.000000000000000,NULL),(133,47,2,1500.000000000000000,1500.000000000000000,NULL),(134,83,2,450.000000000000000,450.000000000000000,NULL),(135,84,2,220.000000000000000,220.000000000000000,NULL),(136,82,2,300.000000000000000,300.000000000000000,NULL),(207,1,1,80000.000000000000000,80000.000000000000000,NULL),(208,14,1,9.300000000000000,9.300000000000000,NULL),(209,15,1,100.000000000000000,100.000000000000000,NULL),(210,16,1,500.000000000000000,500.000000000000000,NULL),(211,17,1,500.000000000000000,500.000000000000000,NULL),(212,18,1,500.000000000000000,500.000000000000000,NULL),(213,19,1,800.000000000000000,800.000000000000000,NULL),(214,20,1,400.000000000000000,400.000000000000000,NULL),(215,21,1,321.000000000000000,321.000000000000000,NULL),(216,22,1,123.000000000000000,123.000000000000000,NULL),(217,1,3,8000.000000000000000,8000.000000000000000,NULL),(220,1,5,22000.000000000000000,22000.000000000000000,NULL),(221,14,5,100.000000000000000,100.000000000000000,NULL),(222,15,5,1000.000000000000000,1000.000000000000000,NULL),(223,16,5,1000.000000000000000,1000.000000000000000,NULL),(224,24,5,1000.000000000000000,1000.000000000000000,NULL),(225,25,5,1000.000000000000000,1000.000000000000000,NULL),(226,26,5,2000.000000000000000,2000.000000000000000,NULL),(230,16,8,800.000000000000000,800.000000000000000,NULL),(231,17,8,900.000000000000000,900.000000000000000,NULL),(232,20,8,1000.000000000000000,1000.000000000000000,NULL),(233,21,8,1100.000000000000000,1100.000000000000000,NULL),(234,25,8,1200.000000000000000,1200.000000000000000,NULL),(237,1,4,55.000000000000000,0.000000000000000,NULL),(240,16,7,33.000000000000000,0.000000000000000,NULL),(243,1,9,1.000000000000000,0.000000000000000,NULL),(244,17,9,1.000000000000000,0.000000000000000,NULL),(288,14,10,91.000000000000000,91.000000000000000,NULL),(291,14,13,3.859000000000000,0.000000000000000,NULL),(292,32,14,88.000000000000000,0.000000000000000,NULL),(295,14,15,5.950000000000000,5.950000000000000,NULL),(296,15,15,333.000000000000000,333.000000000000000,NULL),(303,1,16,0.000000000000000,0.000000000000000,NULL),(304,14,16,9.000000000000000,9.000000000000000,NULL),(305,15,16,5.000000000000000,5.000000000000000,NULL),(306,20,16,0.000000000000000,0.000000000000000,NULL),(307,109,17,1.000000000000000,1.000000000000000,NULL),(309,1,19,4000.000000000000000,4000.000000000000000,NULL),(310,75,19,200.000000000000000,200.000000000000000,NULL),(316,47,18,5.000000000000000,0.000000000000000,NULL),(317,109,18,1.000000000000000,0.000000000000000,NULL),(318,75,18,8.000000000000000,0.000000000000000,NULL),(319,1,21,1.000000000000000,0.000000000000000,NULL),(320,14,21,1.000000000000000,0.000000000000000,NULL);
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
  `state_comment` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
INSERT INTO `people` VALUES (1,'Авдей Иванов','Директор (генеральный директор, управляющий) предприятия'),(2,'Аверкий Иванов','Финансовый директор (заместитель директора по финансам)'),(3,'Авксентий Иванов','Главный бухгалтер'),(4,'Агафон Иванов','Главный диспетчер'),(5,'Александр Иванов','Главный инженер'),(6,'Алексей Иванов','Главный конструктор'),(7,'Альберт Иванов','Главный металлург'),(8,'Альвиан Иванов','Главный метролог'),(9,'Анатолий Иванов','Главный механик'),(10,'Андрей Иванов','Главный сварщик'),(11,'Антон Иванов','Главный специалист по защите информации'),(12,'Антонин Иванов','Главный технолог'),(13,'Анфим Иванов','Главный энергетик'),(14,'Аристарх (имя) Иванов','Директор (начальник) вычислительного (информационно-вычислительного) центра'),(15,'Аркадий Иванов','Директор гостиницы'),(16,'Арсений Иванов','Директор котельной'),(17,'Артём (имя) Иванов','Директор по связям с инвесторами'),(18,'Артур Иванов','Директор типографии'),(19,'Архипп Иванов','Заведующая машинописным бюро'),(20,'Афанасий Иванов','Заведующий архивом'),(21,'Савва Иванов','Заведующий бюро пропусков'),(22,'Савелий Иванов','Заведующий жилым корпусом пансионата (гостиницы)'),(23,'Самуил (имя) Иванов','Заведующий камерой хранения'),(24,'Святополк Иванов','Заведующий канцелярией'),(25,'Святослав Иванов','Заведующий копировально-множительным бюро'),(26,'Севастьян Иванов','Заведующий научно-технической библиотекой'),(27,'Семён Иванов','Заведующий общежитием'),(28,'Сергей Иванов','Заведующий производством (шеф-повар)'),(29,'Сильвестр Иванов','Заведующий складом'),(30,'Сильвестр (имя) Иванов','Заведующий столовой'),(31,'Созон Иванов','Заведующий фотолабораторией'),(32,'Спиридон Иванов','Заведующий хозяйством'),(33,'Станислав Иванов','Заведующий экспедицией'),(34,'Степан Иванов','Заместитель директора по капитальному строительству'),(35,'Гавриил Иванов','Заместитель директора по коммерческим вопросам'),(36,'Галактион Иванов','Заместитель директора по связям с общественностью'),(37,'Геласий Иванов','Заместитель директора по управлению персоналом'),(38,'Геннадий Иванов','Корпоративный секретарь акционерного общества'),(39,'Георгий Иванов','Мастер контрольный (участка, цеха)'),(40,'Герасим Иванов','Мастер участка'),(41,'Герман Иванов','Менеджер'),(42,'Глеб Иванов','Менеджер по персоналу'),(43,'Гордей Иванов','Менеджер по рекламе'),(44,'Григорий Иванов','Менеджер по связям с инвесторами'),(45,'Авдей Петров','Менеджер по связям с общественностью'),(46,'Аверкий Петров','Начальник автоколонны'),(47,'Авксентий Петров','Начальник гаража'),(48,'Агафон Петров','Начальник (заведующий) мастерской'),(49,'Александр Петров','Начальник инструментального отдела'),(50,'Алексей Петров','Начальник исследовательской лаборатории'),(51,'Альберт Петров','Начальник производственной лаборатории (по контролю производства)'),(52,'Альвиан Петров','Начальник лаборатории (бюро) по организации труда и управления производством'),(53,'Анатолий Петров','Начальник лаборатории (бюро) социологии труда'),(54,'Андрей Петров','Начальник лаборатории (бюро) технико-экономических исследований'),(55,'Антон Петров','Начальник нормативно-исследовательской лаборатории по труду'),(56,'Антонин Петров','Начальник отдела автоматизации и механизации производственных процессов'),(57,'Анфим Петров','Начальник отдела автоматизированной системы управления производством (АСУП)'),(58,'Аристарх (имя) Петров','Начальник отдела информации'),(59,'Аркадий Петров','Начальник отдела кадров'),(60,'Арсений Петров','Начальник отдела капитального строительства'),(61,'Артём (имя) Петров','Начальник отдела комплектации оборудования'),(62,'Артур Петров','Начальник отдела контроля качества'),(63,'Архипп Петров','Начальник отдела маркетинга'),(64,'Афанасий Петров','Начальник отдела материально-технического снабжения'),(65,'Савва Петров','Начальник отдела организации и оплаты труда'),(66,'Савелий Петров','Начальник отдела охраны окружающей среды'),(67,'Самуил (имя) Петров','Начальник отдела охраны труда'),(68,'Святополк Петров','Начальник отдела патентной и изобретательской работы'),(69,'Святослав Петров','Начальник отдела подготовки кадров'),(70,'Севастьян Петров','Начальник отдела по связям с инвесторами'),(71,'Семён Петров','Начальник отдела (лаборатории, сектора) по защите информации'),(72,'Сергей Петров','Начальник отдела по связям с общественностью'),(73,'Сильвестр Петров','Начальник отдела сбыта'),(74,'Сильвестр (имя) Петров','Начальник отдела социального развития'),(75,'Созон Петров','Начальник отдела стандартизации'),(76,'Спиридон Петров','Начальник планово-экономического отдела'),(77,'Станислав Петров','Начальник производственного отдела'),(78,'Степан Петров','Начальник ремонтного цеха'),(79,'Гавриил Петров','Начальник смены'),(80,'Галактион Петров','Начальник технического отдела'),(81,'Геласий Петров','Начальник финансового отдела'),(82,'Геннадий Петров','Начальник хозяйственного отдела'),(83,'Георгий Петров','Начальник центральной заводской лаборатории'),(84,'Герасим Петров','Начальник цеха (участка)'),(85,'Герман Петров','Начальник цеха опытного производства'),(86,'Глеб Петров','Начальник юридического отдела'),(87,'Гордей Петров','Производитель работ (прораб)'),(88,'Григорий Петров','Руководитель группы по инвентаризации строений и сооружений'),(89,'Авдей Сидоров','Управляющий отделением (фермой, сельскохозяйственным участком)'),(90,'Аверкий Сидоров','Администратор'),(91,'Авксентий Сидоров','Аналитик'),(92,'Агафон Сидоров','Аудитор'),(93,'Александр Сидоров','Аукционист'),(94,'Алексей Сидоров','Биржевой маклер'),(95,'Альберт Сидоров','Брокер'),(96,'Альвиан Сидоров','Брокер торговый'),(97,'Анатолий Сидоров','Бухгалтер'),(98,'Андрей Сидоров','Бухгалтер-ревизор'),(99,'Антон Сидоров','Дилер'),(100,'Антонин Сидоров','Диспетчер'),(101,'Анфим Сидоров','Документовед'),(102,'Аристарх (имя) Сидоров','Инженер'),(103,'Аркадий Сидоров','Инженер-конструктор (конструктор)'),(104,'Арсений Сидоров','Инженер-лаборант'),(105,'Артём (имя) Сидоров','Инженер по автоматизации и механизации производственных процессов'),(106,'Артур Сидоров','Инженер по автоматизированным системам управления производством'),(107,'Архипп Сидоров','Инженер по защите информации'),(108,'Афанасий Сидоров','Инженер по инвентаризации строений и сооружений'),(109,'Савва Сидоров','Инженер по инструменту'),(110,'Савелий Сидоров','Инженер по качеству'),(111,'Самуил (имя) Сидоров','Инженер по комплектации оборудования'),(112,'Святополк Сидоров','Инженер по метрологии'),(113,'Святослав Сидоров','Инженер по надзору за строительством'),(114,'Севастьян Сидоров','Инженер по наладке и испытаниям'),(115,'Семён Сидоров','Инженер по научно-технической информации'),(116,'Сергей Сидоров','Инженер по нормированию труда'),(117,'Сильвестр Сидоров','Инженер по организации труда'),(118,'Сильвестр (имя) Сидоров','Инженер по организации управления производством'),(119,'Созон Сидоров','Инженер по охране окружающей среды (эколог)'),(120,'Спиридон Сидоров','Инженер по охране труда'),(121,'Станислав Сидоров','Инженер по патентной и изобретательской работе'),(122,'Степан Сидоров','Инженер по подготовке кадров'),(123,'Гавриил Сидоров','Инженер по подготовке производства'),(124,'Галактион Сидоров','Инженер по ремонту'),(125,'Геласий Сидоров','Инженер по стандартизации'),(126,'Геннадий Сидоров','Инженер-программист (программист)'),(127,'Георгий Сидоров','Инженер-технолог (технолог)'),(128,'Герасим Сидоров','Инженер-электроник (электроник)'),(129,'Герман Сидоров','Инженер-энергетик (энергетик)'),(130,'Глеб Сидоров','Переводчик синхронный'),(131,'Гордей Сидоров','Профконсультант'),(132,'Григорий Сидоров','Психолог');
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
  `config` mediumtext COMMENT 'Настройки пользователя',
  `has_account` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Флаг наличия аккаунта в системе',
  `id_user_group` int(10) unsigned NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `is_admin` bit(1) NOT NULL DEFAULT b'0',
  `is_delete` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Флаг удаленного пользователя',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `fk_user__user_group_idx` (`id_user_group`),
  CONSTRAINT `fk_user__user_group` FOREIGN KEY (`id_user_group`) REFERENCES `user_group` (`id_user_group`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (7,'astkonadmin','*E6CC90B878B948C35E92B003C792C46C58C4AF40',NULL,_binary '',9,'SuperAdmin',_binary '',_binary '\0'),(8,'testuser1','*E6CC90B878B948C35E92B003C792C46C58C4AF40',NULL,_binary '',10,'Дмитрий',_binary '\0',_binary '\0'),(9,'testuser2','*E6CC90B878B948C35E92B003C792C46C58C4AF40',NULL,_binary '',11,'Дмитрий_2',_binary '\0',_binary '\0'),(10,'Антон','*9583D21B1A48C787DB0A94D64F73ADFBF5E127CE',NULL,_binary '',10,'Антон',_binary '\0',_binary '\0'),(11,'testuser3','*E6CC90B878B948C35E92B003C792C46C58C4AF40',NULL,_binary '',2,'testuser 3',_binary '\0',_binary '\0');
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
  `comment` text,
  PRIMARY KEY (`id_user_group`),
  UNIQUE KEY `user_group_name_UNIQUE` (`user_group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` VALUES (1,'Бригада 1',''),(2,'Бригада 2',''),(9,'Администраторы','Группа пользователей, имеющих административные права'),(10,'Тестовая группа 1','Проверка создания группы через форму редактирования.'),(11,'Тестовая группа 2','Проверка состояния запаса для различных групп');
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

-- Dump completed on 2019-01-19 17:41:58
