-- MySQL dump 10.13  Distrib 5.5.55, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dhcpwebadmin
-- ------------------------------------------------------
-- Server version	5.5.55-0+deb8u1

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
-- Current Database: `dhcpwebadmin`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `dhcpwebadmin` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dhcpwebadmin`;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `acao` varchar(50) NOT NULL,
  `data` datetime NOT NULL,
  `ip` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_global`
--

DROP TABLE IF EXISTS `config_global`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_global` (
  `config_global` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_global`
--

LOCK TABLES `config_global` WRITE;
/*!40000 ALTER TABLE `config_global` DISABLE KEYS */;
/*!40000 ALTER TABLE `config_global` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pools`
--

DROP TABLE IF EXISTS `pools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pools` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `network` varchar(15) NOT NULL,
  `mascara` varchar(15) NOT NULL,
  `broadcast` varchar(15) NOT NULL,
  `gateway` varchar(15) NOT NULL,
  `lease_time` int(8) DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `wins` varchar(50) DEFAULT NULL,
  `dns` varchar(63) DEFAULT NULL,
  `wpad` varchar(50) DEFAULT NULL,
  `dominio` varchar(50) DEFAULT NULL,
  `ativo` varchar(3) DEFAULT NULL,
  `option66` varchar(15) DEFAULT NULL,
  `option150` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  UNIQUE KEY `network` (`network`),
  UNIQUE KEY `gateway` (`gateway`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pools`
--

LOCK TABLES `pools` WRITE;
/*!40000 ALTER TABLE `pools` DISABLE KEYS */;
/*!40000 ALTER TABLE `pools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `range_ips`
--

DROP TABLE IF EXISTS `range_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `range_ips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_pool` int(10) NOT NULL,
  `ip_inicio` varchar(15) NOT NULL,
  `ip_final` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_inicio` (`ip_inicio`),
  UNIQUE KEY `ip_final` (`ip_final`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `range_ips`
--

LOCK TABLES `range_ips` WRITE;
/*!40000 ALTER TABLE `range_ips` DISABLE KEYS */;
/*!40000 ALTER TABLE `range_ips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `static_hosts`
--

DROP TABLE IF EXISTS `static_hosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static_hosts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_pool` int(10) NOT NULL,
  `mac` varchar(17) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `data` datetime NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac` (`mac`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `static_hosts`
--

LOCK TABLES `static_hosts` WRITE;
/*!40000 ALTER TABLE `static_hosts` DISABLE KEYS */;
/*!40000 ALTER TABLE `static_hosts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `nome_completo` varchar(30) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `email` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-04-23 14:24:20
