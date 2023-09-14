-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: smartcargo
-- ------------------------------------------------------
-- Server version	5.5.24-0ubuntu0.12.04.1

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
-- Table structure for table `acl_menu`
--

DROP TABLE IF EXISTS `acl_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_menu` (
  `ID_MENU` int(11) NOT NULL AUTO_INCREMENT,
  `MENU_IDN` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `LINK` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `PARENT` int(11) NOT NULL,
  `AUTHENTICATE` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `MENU_ENG` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `FLAG` int(11) NOT NULL,
  PRIMARY KEY (`ID_MENU`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_menu`
--

LOCK TABLES `acl_menu` WRITE;
/*!40000 ALTER TABLE `acl_menu` DISABLE KEYS */;
INSERT INTO `acl_menu` VALUES (1,'Administrasi','#',0,'1','Administration',0),(2,'Data Pengguna','users/listview',1,'1','User Data',0),(3,'Aktivasi Member','member/activationlist',1,'1','Member Activation',0),(4,'Data Member','member/listview',1,'1','Member data',0),(5,'Permintaan','#',0,'1245','Request',0),(6,'Ocean Going Delivery Request','delivery_request_og/listview',5,'1245','Ocean Going Delivery Request',0),(7,'Data Truck','trucking/listview',9,'1345','Trucking Data',0),(8,'Data Driver','driver/listview',9,'1345','Drivers Data',0),(9,'Trucking','#',0,'1345','Trucking',0),(10,'Registrasi','#',0,'0','Register',0);
/*!40000 ALTER TABLE `acl_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `administrator`
--

DROP TABLE IF EXISTS `administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `roles` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_administrator_users1` (`member_id`),
  CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `fk_administrator_member1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrator`
--

LOCK TABLES `administrator` WRITE;
/*!40000 ALTER TABLE `administrator` DISABLE KEYS */;
INSERT INTO `administrator` VALUES (1,1,'ALL'),(2,16,'CARTOS'),(3,27,'CDS'),(4,33,'BEACUKAI');
/*!40000 ALTER TABLE `administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berkas_edi_pkk`
--

DROP TABLE IF EXISTS `berkas_edi_pkk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berkas_edi_pkk` (
  `id` int(11) NOT NULL,
  `pkk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_berkas_edi_pkk_pkk1` (`pkk_id`),
  CONSTRAINT `fk_berkas_edi_pkk_pkk1` FOREIGN KEY (`pkk_id`) REFERENCES `pkk` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berkas_edi_pkk`
--

LOCK TABLES `berkas_edi_pkk` WRITE;
/*!40000 ALTER TABLE `berkas_edi_pkk` DISABLE KEYS */;
/*!40000 ALTER TABLE `berkas_edi_pkk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cara_pembayaran`
--

DROP TABLE IF EXISTS `cara_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cara_pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_cara_pembayaran` varchar(15) DEFAULT NULL,
  `cara_pembayaran` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cara_pembayaran`
--

LOCK TABLES `cara_pembayaran` WRITE;
/*!40000 ALTER TABLE `cara_pembayaran` DISABLE KEYS */;
INSERT INTO `cara_pembayaran` VALUES (1,'CP_MANDIRI','Mandiri Click Pay'),(2,'OTHER_NOT_CP','Pembayaran Via Teller, ATM, Internet Banking');
/*!40000 ALTER TABLE `cara_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier`
--

DROP TABLE IF EXISTS `carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `kapal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carier_member1` (`member_id`),
  KEY `fk_carier_kapal1` (`kapal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier`
--

LOCK TABLES `carrier` WRITE;
/*!40000 ALTER TABLE `carrier` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` varchar(45000) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_sessions`
--

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
INSERT INTO `ci_sessions` VALUES ('528afc3d58433a0f78b0daa88183ddd1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',1593661224,'a:2:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"vrlak\";}'),('5937075804663723af8f203337ebaf34','172.16.254.128','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/',1593664719,'a:3:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"lxlft\";s:14:\"demo.sc8d7gads\";O:8:\"stdClass\":18:{s:5:\"token\";s:13:\"5efd63874585e\";s:9:\"logged_in\";b:1;s:2:\"id\";s:2:\"16\";s:8:\"username\";s:6:\"carter\";s:9:\"member_id\";s:2:\"16\";s:12:\"nama_lengkap\";s:3:\"IKT\";s:10:\"email_user\";N;s:4:\"npwp\";N;s:15:\"nama_perusahaan\";s:32:\"PT. Indonesia Kendaraan Terminal\";s:6:\"alamat\";N;s:7:\"telepon\";N;s:16:\"email_perusahaan\";N;s:20:\"freight_forwarder_id\";N;s:19:\"trucking_company_id\";N;s:16:\"shipping_line_id\";N;s:17:\"shipping_agent_id\";N;s:16:\"administrator_id\";s:1:\"2\";s:5:\"roles\";s:6:\"CARTOS\";}}'),('742f6c0dd5fba07cec125eeee459cf33','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/',1593664309,'a:2:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"tkzkp\";}'),('86c79a38e9e090f8e83dc5cb0ee31bb5','36.89.48.202','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',1593654134,'a:3:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"kbgap\";s:14:\"demo.sc8d7gads\";O:8:\"stdClass\":18:{s:5:\"token\";s:13:\"5efd3b86c96d5\";s:9:\"logged_in\";b:1;s:2:\"id\";s:2:\"16\";s:8:\"username\";s:6:\"carter\";s:9:\"member_id\";s:2:\"16\";s:12:\"nama_lengkap\";s:3:\"IKT\";s:10:\"email_user\";N;s:4:\"npwp\";N;s:15:\"nama_perusahaan\";s:32:\"PT. Indonesia Kendaraan Terminal\";s:6:\"alamat\";N;s:7:\"telepon\";N;s:16:\"email_perusahaan\";N;s:20:\"freight_forwarder_id\";N;s:19:\"trucking_company_id\";N;s:16:\"shipping_line_id\";N;s:17:\"shipping_agent_id\";N;s:16:\"administrator_id\";s:1:\"2\";s:5:\"roles\";s:6:\"CARTOS\";}}'),('08ff66ed5136e0caa03fa1d4416ecca2','172.16.254.128','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0',1593662566,'a:2:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"jusrv\";}'),('76f05446792a9c0a38165827146554eb','36.89.48.202','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',1593667152,'a:3:{s:9:\"user_data\";s:0:\"\";s:11:\"captcha_key\";s:5:\"qwppt\";s:14:\"demo.sc8d7gads\";O:8:\"stdClass\":18:{s:5:\"token\";s:13:\"5efd5a9dc451a\";s:9:\"logged_in\";b:1;s:2:\"id\";s:2:\"16\";s:8:\"username\";s:6:\"carter\";s:9:\"member_id\";s:2:\"16\";s:12:\"nama_lengkap\";s:3:\"IKT\";s:10:\"email_user\";N;s:4:\"npwp\";N;s:15:\"nama_perusahaan\";s:32:\"PT. Indonesia Kendaraan Terminal\";s:6:\"alamat\";N;s:7:\"telepon\";N;s:16:\"email_perusahaan\";N;s:20:\"freight_forwarder_id\";N;s:19:\"trucking_company_id\";N;s:16:\"shipping_line_id\";N;s:17:\"shipping_agent_id\";N;s:16:\"administrator_id\";s:1:\"2\";s:5:\"roles\";s:6:\"CARTOS\";}}');
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coreor_detail`
--

DROP TABLE IF EXISTS `coreor_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coreor_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coreor_header_id` int(11) NOT NULL,
  `bl_number` varchar(50) DEFAULT NULL,
  `call_sign` varchar(20) DEFAULT NULL,
  `vessel_name` varchar(20) DEFAULT NULL,
  `voyage` varchar(20) DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `port_of_loading` varchar(10) DEFAULT NULL,
  `port_of_destination` varchar(10) DEFAULT NULL,
  `consignee` varchar(50) DEFAULT NULL,
  `do_number` varchar(50) DEFAULT NULL,
  `do_expired` date DEFAULT NULL,
  `ff_npwp` varchar(10) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bl_number` (`do_number`,`bl_number`),
  KEY `fk_coreor_info_coreor_header1` (`coreor_header_id`),
  KEY `idx_do_number` (`do_number`),
  CONSTRAINT `fk_tinfo_tshipper1` FOREIGN KEY (`coreor_header_id`) REFERENCES `coreor_header` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreor_detail`
--

LOCK TABLES `coreor_detail` WRITE;
/*!40000 ALTER TABLE `coreor_detail` DISABLE KEYS */;
INSERT INTO `coreor_detail` VALUES (1,1,'-','YBMK','SINAR PADANG. MV*','2510','2010-11-17','SGSIN','IDJKT','PT SAMUDERA INDONESIA','HLCUATL130801540','2016-11-30','',1),(4,1,'5009649','5BSR2','MEDCORAL.MV','1220','2013-10-01','IDTGP','IDJKT','PT JAKARTA OKE','5009649','2013-12-31',NULL,1),(5,2,'EGLV560600338008','VQGH8','EVER PRIDE','0249-20','2016-09-17','VNSGN','IDTPP','PT. UNILEVER INDONESIA TBK','EGLV560600338008','2016-09-20',NULL,1),(10,3,'EGLV062600024261','D5AQ4','EM ANDROS','0687-002','2016-09-23','TWKHH','IDTPP','PT. LAUTAN LUAS TBK','EGLV062600024261','2016-10-06',NULL,1);
/*!40000 ALTER TABLE `coreor_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coreor_header`
--

DROP TABLE IF EXISTS `coreor_header`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coreor_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_identification` varchar(20) DEFAULT NULL COMMENT 'Shipping Line Code',
  `recipient` varchar(20) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `process_date` timestamp NULL DEFAULT NULL,
  `process_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreor_header`
--

LOCK TABLES `coreor_header` WRITE;
/*!40000 ALTER TABLE `coreor_header` DISABLE KEYS */;
INSERT INTO `coreor_header` VALUES (1,'HLC','TER3','2013-09-20','2013-09-20 02:13:50','coreor-uat.xml'),(2,'EVG','TER3','0000-00-00','0000-00-00 00:00:00','EGLV560600338008 UNILEVER.txt'),(3,'EVG','TER3','2016-09-22','2016-09-21 17:00:00','EGLV062600024261.Trial_1.txt');
/*!40000 ALTER TABLE `coreor_header` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coreor_line`
--

DROP TABLE IF EXISTS `coreor_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coreor_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coreor_detail_id` int(11) DEFAULT NULL,
  `container_number` varchar(20) DEFAULT NULL,
  `container_size` varchar(10) DEFAULT NULL,
  `container_type` varchar(10) DEFAULT NULL,
  `seal_number` varchar(50) DEFAULT NULL,
  `iso_code` varchar(20) DEFAULT NULL,
  `commodity` varchar(50) DEFAULT NULL,
  `hazard` tinyint(1) DEFAULT NULL COMMENT '0 = Non Hazard, 1 = Hazard',
  `requested_flag` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `stowage` varchar(8) DEFAULT NULL,
  `sp2_number` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DETALI` (`container_number`,`coreor_detail_id`),
  KEY `fk_tdetail_tinfo1` (`coreor_detail_id`),
  KEY `idx_search_cont` (`container_number`),
  CONSTRAINT `fk_tdetail_tinfo1` FOREIGN KEY (`coreor_detail_id`) REFERENCES `coreor_detail` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreor_line`
--

LOCK TABLES `coreor_line` WRITE;
/*!40000 ALTER TABLE `coreor_line` DISABLE KEYS */;
INSERT INTO `coreor_line` VALUES (1,1,'WHLU2171235','22','31','','2231','FRESH FRUIT',NULL,0,1,NULL,NULL),(2,1,'TEGU2951308','22','DRY','','22G1','HEAVY MACHINERY',NULL,0,1,NULL,NULL),(3,4,'MWCU5694553','22','R1',NULL,'22R1','GENERAL CARGO',0,0,1,NULL,NULL),(4,4,'MSKU6861758','43','10',NULL,'4310','RAW COTTON',0,0,1,NULL,NULL),(5,5,'EITU0119987','20','DRY','KL3246829','22G1','ANTI-CAKING AGENT.POLYBAGS  960   BAGS  .  4510627',0,1,1,NULL,'1474525'),(6,5,'EMCU6061802','20','DRY','KL3246825','22G1','ANTI-CAKING AGENT.POLYBAGS  960   BAGS  .  4510627',0,1,1,NULL,'1474517'),(7,10,'CARU9999677','40','DRY','EMCGTA5764','42G1','6 X 12 IODINE 1000 MIN. ACTIVATED CARBON',0,NULL,1,NULL,NULL),(8,10,'OCGU8040064','40','DRY','EMCGTA7144','42G1','6 X 12 IODINE 1000 MIN. ACTIVATED CARBON',NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `coreor_line` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `freight_forwarder`
--

DROP TABLE IF EXISTS `freight_forwarder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freight_forwarder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_freight_forwarder_users1` (`member_id`),
  CONSTRAINT `freight_forwarder_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `freight_forwarder`
--

LOCK TABLES `freight_forwarder` WRITE;
/*!40000 ALTER TABLE `freight_forwarder` DISABLE KEYS */;
INSERT INTO `freight_forwarder` VALUES (1,2),(2,10),(3,11),(4,12),(5,15),(6,26),(7,29),(8,30),(9,31);
/*!40000 ALTER TABLE `freight_forwarder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kapal`
--

DROP TABLE IF EXISTS `kapal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kapal` (
  `id` int(11) NOT NULL,
  `shiping_line_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kapal_shiping_line1` (`shiping_line_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kapal`
--

LOCK TABLES `kapal` WRITE;
/*!40000 ALTER TABLE `kapal` DISABLE KEYS */;
/*!40000 ALTER TABLE `kapal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kode_lokal_freight_forwarder`
--

DROP TABLE IF EXISTS `kode_lokal_freight_forwarder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kode_lokal_freight_forwarder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freight_forwarder_id` int(11) NOT NULL,
  `terminal_petikemas_id` int(11) NOT NULL,
  `kode_pelanggan` varchar(45) DEFAULT NULL,
  `nomor_pelanggan` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mapping_pbm_freight_forwarder1` (`freight_forwarder_id`),
  KEY `fk_mapping_pbm_terminal_petikemas1` (`terminal_petikemas_id`),
  CONSTRAINT `fk_mapping_pbm_freight_forwarder1` FOREIGN KEY (`freight_forwarder_id`) REFERENCES `freight_forwarder` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_mapping_pbm_terminal_petikemas1` FOREIGN KEY (`terminal_petikemas_id`) REFERENCES `terminal_petikemas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kode_lokal_freight_forwarder`
--

LOCK TABLES `kode_lokal_freight_forwarder` WRITE;
/*!40000 ALTER TABLE `kode_lokal_freight_forwarder` DISABLE KEYS */;
INSERT INTO `kode_lokal_freight_forwarder` VALUES (1,1,1,'BSS','15400139'),(2,4,1,'','');
/*!40000 ALTER TABLE `kode_lokal_freight_forwarder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_group`
--

DROP TABLE IF EXISTS `m_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `m_group` (
  `id_group` varchar(1) COLLATE latin1_general_ci NOT NULL,
  `group_name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `group_order` int(11) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_group`
--

LOCK TABLES `m_group` WRITE;
/*!40000 ALTER TABLE `m_group` DISABLE KEYS */;
INSERT INTO `m_group` VALUES ('','cargo_owner_plus_trucking_comp',0),('1','admin',99),('2','freight_forwader',99),('3','trucking_company',99),('4','cargo_owner',99),('5','freight_forwader_trucking_comp',99);
/*!40000 ALTER TABLE `m_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `npwp` char(20) DEFAULT NULL,
  `nama_perusahaan` varchar(45) DEFAULT NULL,
  `alamat` varchar(200) DEFAULT NULL,
  `telepon` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `waktu_bergabung` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `diperiksa` tinyint(1) DEFAULT NULL,
  `waktu_approved` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `npwp_UNIQUE` (`npwp`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,NULL,'Integrasi Logistik Cipta Solusi, PT','Jl. Yos Sudarso',NULL,'djati@ilcs.co.id',NULL,'2013-09-25 08:30:14',1,1,'2013-09-25 08:30:14'),(2,'01.301.264.6-038.000','Puninar Jaya, PT','Jl. Raya Cakung Cilincing Km. 1,5, Daerah Khusus Ibukota Jakarta 13910, Indonesia','02155555','djati@ilcs.co.id','','2013-09-25 03:38:43',1,NULL,NULL),(3,'01.301.264.6-039.000','Puninar Jaya PT','Test Trucking','0213966948326','djati@ilcs.co.id','','2013-09-25 14:04:28',1,NULL,NULL),(4,'01.301.264.6-039.001','PT. Mat Soleh Transport','Jl. Test','0213966948326','djati@ilcs.co.id','056342','2013-09-27 09:15:14',1,NULL,NULL),(5,'434365436','asdgdsagsdagsad','sdagadgsdag','23462624','test@kampret.net','6224','2013-09-27 09:20:18',1,NULL,NULL),(6,'2352135','dadsgdsag','asdgsdagsdag','099865432','djati@ilcs.co.id','232346236','2013-09-27 09:21:31',1,NULL,NULL),(8,'4236642642','adsgsagdsg','asdgdagdsag','3426326342','djati@ilcs.co.id','346264326','2013-09-27 09:23:58',1,NULL,NULL),(9,'01.301.264.6-039.005','Mat Solar Transport','Test','0213966948326','djati@ilcs.co.id','32525','2013-09-27 09:25:22',1,NULL,NULL),(10,'007','angin ribut','entah berantah','007','dagelan@yahoo.com','007','2013-10-03 07:19:42',1,NULL,NULL),(11,'0123456789','ilcs','sehat','08564356','akmal@ilcs.co.id','','2013-10-10 12:29:52',1,NULL,NULL),(12,'02176540','funny\'s forwarding','jalan mane aje lu mau dah... bebas ...','07890909090','evanirosari@gmail.com','09876','2013-11-15 10:11:50',1,NULL,NULL),(13,'0987654','funny\'s trucking company','alamat masih blm ditentukan .\n\nterimakasih','09876543211','evanirosari@gmail.com','098765','2013-11-15 10:17:03',1,NULL,NULL),(14,'034567843','funny\'s trucking company','JALAN jalannnn','09876543212','evanirosari@gmail.com','098765','2013-11-15 10:20:47',1,NULL,NULL),(15,'356647767585858599','PT. Cargo','JL. Cargo','045678383399','cargo@yahoo.com','','2014-01-09 02:39:13',1,NULL,NULL),(16,NULL,'PT. Indonesia Kendaraan Terminal',NULL,NULL,NULL,NULL,'2014-03-05 12:17:51',1,1,'2014-03-05 12:17:51'),(24,NULL,'US21A0101',NULL,NULL,NULL,NULL,'2014-03-12 13:28:58',1,NULL,NULL),(25,NULL,'US21A0101',NULL,NULL,NULL,NULL,'2014-03-12 13:28:58',1,NULL,NULL),(26,'55','aa','','55','aa@aa.aa','','2014-04-15 02:23:58',1,NULL,NULL),(27,'123456789','CDS','ILCS','0898999','nurdin@ilcs.co.id','12312123','2014-10-31 04:32:49',1,0,'2014-10-31 04:32:49'),(28,'234567899876543','ertyui','sdfghj','0987654','sdfghj@dfghj.com','','2015-12-16 06:52:55',1,NULL,NULL),(29,'987654323456','PT Prima mandiri','','0811997768','iansindoro@yahoo.com','','2015-12-16 06:57:01',1,NULL,NULL),(30,'020914479012000','PT. Simba Logistik','Grha Paramita Building 11 Floor\nJl. Denpasar Raya Blok D2 Kav.8\nKuningan - Jakarta 12040 Indonesia','0212522110','hendra@simbalogistics.co.id','0212522164','2016-08-31 07:09:12',1,NULL,NULL),(31,'01.123.456.789','PT. Agility','Wisma Aldiron, Pancoran, Jakarta','021123456','rizka@ilcs.co.id',NULL,'2016-08-01 07:09:12',1,1,'2016-08-01 07:10:12'),(32,'01.541.497.2-007.000','PT. CAKRA BAHANA','PERKANTORAN PURI MUTIARA BLOK A NO. 75 JL. GRIYA UTAMA SUNTER AGUNG JAKARTA UTARA. 14350','02165310586','SYAIFUL@CAKRABAHANA.COM','02165310585','2016-09-26 08:30:32',1,NULL,NULL),(33,NULL,'Bea Cukai',NULL,NULL,NULL,NULL,'2014-03-05 12:17:51',1,1,'2014-03-05 12:17:51');
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota`
--

DROP TABLE IF EXISTS `nota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `nomor_faktur_pajak` char(20) DEFAULT NULL,
  `kode_uper` varchar(45) DEFAULT NULL,
  `kd_cabang` varchar(3) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `administrasi` double DEFAULT NULL,
  `ppn` double DEFAULT NULL,
  `ppn_subsidi` double DEFAULT NULL,
  `debet` double DEFAULT NULL,
  `kredit` double DEFAULT NULL,
  `materai` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `waktu_pelunasan` timestamp NULL DEFAULT NULL,
  `tanggal_terbit` timestamp NULL DEFAULT NULL,
  `flag_lunas` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_nota_UNIQUE` (`nomor_faktur_pajak`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota`
--

LOCK TABLES `nota` WRITE;
/*!40000 ALTER TABLE `nota` DISABLE KEYS */;
INSERT INTO `nota` VALUES (1,2,'0100101361000065','011301556551',NULL,'IDR',0,30000,16698600,0,0,183684600,0,166986000,'2015-03-10 10:29:37','2013-11-20 05:26:58',1),(2,31,'0100331666906462','011301556551',NULL,'IDR',0,35000,62160,0,0,683760,0,586600,'2016-09-19 03:29:37','2016-09-19 05:26:58',1),(3,31,'6677','011301556551',NULL,'IDR',NULL,35000,268600,NULL,NULL,2954600,NULL,2651000,'2016-09-27 09:57:14','2016-09-27 09:57:14',NULL);
/*!40000 ALTER TABLE `nota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_line`
--

DROP TABLE IF EXISTS `nota_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nota_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nota_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `size_cont` varchar(45) DEFAULT NULL,
  `sty_name` varchar(45) DEFAULT NULL,
  `status_cont` varchar(45) DEFAULT NULL,
  `tarif` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `uraian` varchar(100) DEFAULT NULL,
  `total_hari` int(11) DEFAULT NULL,
  `hazard` tinyint(1) DEFAULT NULL,
  `ei` varchar(45) DEFAULT NULL,
  `oi` varchar(45) DEFAULT NULL,
  `crane` varchar(45) DEFAULT NULL,
  `plug_in` varchar(45) DEFAULT NULL,
  `plug_out` varchar(45) DEFAULT NULL,
  `jumlah_jam` varchar(45) DEFAULT NULL,
  `tanggal_awal` varchar(45) DEFAULT NULL,
  `tanggal_akhir` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rincian_nota_nota1` (`nota_id`),
  CONSTRAINT `fk_rincian_nota_nota1` FOREIGN KEY (`nota_id`) REFERENCES `nota` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_line`
--

LOCK TABLES `nota_line` WRITE;
/*!40000 ALTER TABLE `nota_line` DISABLE KEYS */;
INSERT INTO `nota_line` VALUES (1,1,2,'20','','',2000,4000,'KEBERSIHAN',0,0,'','','','','','','',''),(2,1,1,'20','DRY','FCL',75000,75000,'LIFT ON',0,0,'','','','','','','',''),(3,1,1,'20','RFR','FCL',187500,187500,'LIFT ON',0,0,'','','','','','','',''),(4,1,1,'20','DRY','FCL',454400,3180800,'PENUMPUKAN MASA I-2',7,0,'','','','','','','2013-10-13T00:00:00+07:00','2013-10-19T00:00:00+07:00'),(5,1,1,'20','RFR','FCL',125800,880600,'PENUMPUKAN MASA I-2',7,0,'','','','','','','2013-10-13T00:00:00+07:00','2013-10-19T00:00:00+07:00'),(6,1,1,'20','DRY','FCL',681600,18403200,'PENUMPUKAN MASA II',27,0,'','','','','','','2013-10-19T00:00:00+07:00','2013-11-15T00:00:00+07:00'),(7,1,1,'20','RFR','FCL',188700,5094900,'PENUMPUKAN MASA II',27,0,'','','','','','','2013-10-19T00:00:00+07:00','2013-11-15T00:00:00+07:00'),(8,2,1,'20','DRY','FCL',187500,187500,'LIFT ON',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,2,1,'20','DRY','FCL',27200,0,'PENUMPUKAN MASA I-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,2,1,'20','DRY','FCL',81600,81600,'PENUMPUKAN MASA I-2',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,2,1,'20','DRY','FCL',65000,65000,'PEMULIHAN BIAYA OPERASI',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,2,1,'20','DRY','FCL',187500,187500,'LIFT ON',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,2,1,'20','DRY','FCL',27200,0,'PENUMPUKAN MASA I-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,2,1,'20','DRY','FCL',65000,65000,'PEMULIHAN BIAYA OPERASI',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,3,1,'40','DRY','FCL',281300,281300,'LIFT ON',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,3,1,'40','DRY','FCL',54400,0,'PENUMPUKAN MASA I-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,3,1,'40','DRY','FCL',163200,163200,'PENUMPUKAN MASA I-2',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,3,1,'40','DRY','FCL',326400,326400,'PENUMPUKAN MASA II-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,3,1,'40','DRY','FCL',489600,489600,'PENUMPUKAN MASA II-2',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,3,1,'40','DRY','FCL',65000,65000,'PEMULIHAN BIAYA OPERASI',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,3,1,'40','DRY','FCL',281300,281300,'LIFT ON',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,3,1,'40','DRY','FCL',54400,0,'PENUMPUKAN MASA I-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,3,1,'40','DRY','FCL',163200,163200,'PENUMPUKAN MASA I-2',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,3,1,'40','DRY','FCL',326400,326400,'PENUMPUKAN MASA II-1',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,3,1,'40','DRY','FCL',489600,489600,'PENUMPUKAN MASA II-2',1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,3,1,'40','DRY','FCL',65000,65000,'PEMULIHAN BIAYA OPERASI',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `nota_line` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ocean_going_delivery_request`
--

DROP TABLE IF EXISTS `ocean_going_delivery_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocean_going_delivery_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freight_forwarder_id` int(11) NOT NULL,
  `terminal_petikemas_id` int(11) NOT NULL,
  `nota_id` int(11) DEFAULT NULL,
  `nomor_request_ilcs` varchar(45) DEFAULT NULL,
  `nomor_request_inhouse` varchar(45) DEFAULT NULL,
  `nomor_do` varchar(45) DEFAULT NULL,
  `expired_do` date DEFAULT NULL,
  `nomor_sppb` varchar(45) DEFAULT NULL,
  `tanggal_sppb` varchar(45) DEFAULT NULL,
  `nomor_bl` varchar(45) DEFAULT NULL,
  `nomor_ukk` varchar(45) DEFAULT NULL,
  `consignee` varchar(45) DEFAULT NULL,
  `port_of_loading` varchar(45) NOT NULL,
  `port_of_discharge` varchar(45) NOT NULL,
  `kode_shipping_line` varchar(10) NOT NULL,
  `voyage` varchar(45) DEFAULT NULL,
  `call_sign` varchar(45) DEFAULT NULL,
  `tanggal_datang` date NOT NULL,
  `shipping_agent` varchar(45) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `waktu_input` datetime NOT NULL,
  `status_kirim` tinyint(4) NOT NULL DEFAULT '0',
  `rencana_ambil` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ocean_going_delivery_request_freight_forwarder1` (`freight_forwarder_id`),
  KEY `fk_ocean_going_delivery_request_terminal_petikemas1` (`terminal_petikemas_id`),
  KEY `nota_id` (`nota_id`),
  CONSTRAINT `fk_ocean_going_delivery_request_freight_forwarder1` FOREIGN KEY (`freight_forwarder_id`) REFERENCES `freight_forwarder` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ocean_going_delivery_request_terminal_petikemas1` FOREIGN KEY (`terminal_petikemas_id`) REFERENCES `terminal_petikemas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocean_going_delivery_request`
--

LOCK TABLES `ocean_going_delivery_request` WRITE;
/*!40000 ALTER TABLE `ocean_going_delivery_request` DISABLE KEYS */;
INSERT INTO `ocean_going_delivery_request` VALUES (1,1,1,1,NULL,'REQ0110268501','HLCUATL130801540','2013-11-30','123456789','2013-11-14','-',NULL,'PT SAMUDERA INDONESIA','SGSIN','IDJKT','HLC','2510','YBMK','2010-11-17',NULL,NULL,'2013-11-14 18:59:15',1,'2013-11-15'),(5,9,1,NULL,NULL,NULL,'EGLV560600338008','2016-09-20','01',NULL,NULL,NULL,NULL,'','','',NULL,NULL,'2016-09-17',NULL,NULL,'2010-11-16 00:00:00',0,NULL),(6,1,1,2,NULL,'REQ011026852','EGLV560600338008','2016-09-20','000008/WBC.08/KPP/MP.01/2016','2016-09-19','EGLV560600338008',NULL,'PT. UNILEVER INDONESIA TBK','VNSGN','IDTPP','EVG','0249-20','VQGH8','2016-09-17',NULL,NULL,'2016-09-19 14:48:02',1,'2016-09-19'),(7,1,1,NULL,NULL,NULL,'EGLV062600024261','2016-10-06',NULL,NULL,'EGLV062600024261',NULL,'PT. LAUTAN LUAS TBK','TWKHH','IDTPP','','0687-002W',NULL,'2016-09-23',NULL,NULL,'2016-09-26 00:00:00',0,NULL);
/*!40000 ALTER TABLE `ocean_going_delivery_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ocean_going_delivery_request_line`
--

DROP TABLE IF EXISTS `ocean_going_delivery_request_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocean_going_delivery_request_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ocean_going_delivery_request_id` int(11) NOT NULL,
  `coreor_line_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ocean_going_delivery_request_line_ocean_going_delivery_req1` (`ocean_going_delivery_request_id`),
  KEY `fk_ocean_going_delivery_request_line_tdetail1` (`coreor_line_id`),
  CONSTRAINT `ocean_going_delivery_request_line_ibfk_1` FOREIGN KEY (`ocean_going_delivery_request_id`) REFERENCES `ocean_going_delivery_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ocean_going_delivery_request_line_ibfk_2` FOREIGN KEY (`coreor_line_id`) REFERENCES `coreor_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocean_going_delivery_request_line`
--

LOCK TABLES `ocean_going_delivery_request_line` WRITE;
/*!40000 ALTER TABLE `ocean_going_delivery_request_line` DISABLE KEYS */;
INSERT INTO `ocean_going_delivery_request_line` VALUES (1,1,1),(9,6,5),(10,6,6);
/*!40000 ALTER TABLE `ocean_going_delivery_request_line` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ocean_going_delivery_truck_assignment`
--

DROP TABLE IF EXISTS `ocean_going_delivery_truck_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocean_going_delivery_truck_assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trucking_company_id` int(11) DEFAULT NULL,
  `ocean_going_delivery_request_line_id` int(11) NOT NULL,
  `truck_id` int(11) DEFAULT NULL,
  `nama_supir` varchar(45) DEFAULT NULL,
  `nomor_handphone` varchar(45) DEFAULT NULL,
  `security_code` varchar(45) DEFAULT NULL,
  `tanggal_expired` timestamp NULL DEFAULT NULL,
  `invalid` tinyint(1) DEFAULT NULL,
  `invalid_reason` varchar(45) DEFAULT NULL,
  `waktu_input` datetime NOT NULL,
  `waktu_assign` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_delivery_truck_assignment_ocean_going_delivery_request_line1` (`ocean_going_delivery_request_line_id`),
  KEY `fk_delivery_truck_assignment_trucking_company1` (`trucking_company_id`),
  CONSTRAINT `fk_delivery_truck_assignment_ocean_going_delivery_request_line1` FOREIGN KEY (`ocean_going_delivery_request_line_id`) REFERENCES `ocean_going_delivery_request_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_truck_assignment_trucking_company1` FOREIGN KEY (`trucking_company_id`) REFERENCES `trucking_company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocean_going_delivery_truck_assignment`
--

LOCK TABLES `ocean_going_delivery_truck_assignment` WRITE;
/*!40000 ALTER TABLE `ocean_going_delivery_truck_assignment` DISABLE KEYS */;
INSERT INTO `ocean_going_delivery_truck_assignment` VALUES (1,1,1,0,'sumanto','+628080808','qgqzx',NULL,NULL,NULL,'2015-03-10 17:30:49','2016-09-27 21:29:25'),(3,7,9,4,'Handiko',NULL,'myfjt','2016-09-19 15:38:29',NULL,NULL,'2016-09-19 15:38:29',NULL),(4,7,10,2,'Suryono',NULL,'agnoe','2016-09-19 15:38:29',NULL,NULL,'2016-09-19 15:38:29',NULL);
/*!40000 ALTER TABLE `ocean_going_delivery_truck_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pelabuhan`
--

DROP TABLE IF EXISTS `pelabuhan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pelabuhan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_pelabuhan` char(4) DEFAULT NULL,
  `nama_pelabuhan` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pelabuhan`
--

LOCK TABLES `pelabuhan` WRITE;
/*!40000 ALTER TABLE `pelabuhan` DISABLE KEYS */;
INSERT INTO `pelabuhan` VALUES (1,'TPK','Tanjung Priok, Jakarta');
/*!40000 ALTER TABLE `pelabuhan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pkk`
--

DROP TABLE IF EXISTS `pkk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pkk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kapal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkk_kapal1` (`kapal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pkk`
--

LOCK TABLES `pkk` WRITE;
/*!40000 ALTER TABLE `pkk` DISABLE KEYS */;
/*!40000 ALTER TABLE `pkk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_agent`
--

DROP TABLE IF EXISTS `shipping_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_agency_member1` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_agent`
--

LOCK TABLES `shipping_agent` WRITE;
/*!40000 ALTER TABLE `shipping_agent` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_agent_kapal`
--

DROP TABLE IF EXISTS `shipping_agent_kapal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_agent_kapal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_agent_id` int(11) NOT NULL,
  `kapal_id` int(11) NOT NULL,
  `pelabuhan_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_agent_kapal_shipping_agent1` (`shipping_agent_id`),
  KEY `fk_shipping_agent_kapal_kapal1` (`kapal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_agent_kapal`
--

LOCK TABLES `shipping_agent_kapal` WRITE;
/*!40000 ALTER TABLE `shipping_agent_kapal` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_agent_kapal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_line`
--

DROP TABLE IF EXISTS `shipping_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shiping_line_member1` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_line`
--

LOCK TABLES `shipping_line` WRITE;
/*!40000 ALTER TABLE `shipping_line` DISABLE KEYS */;
INSERT INTO `shipping_line` VALUES (1,12),(5,24);
/*!40000 ALTER TABLE `shipping_line` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supir_truck`
--

DROP TABLE IF EXISTS `supir_truck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supir_truck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trucking_company_id` int(11) NOT NULL,
  `nama_supir` varchar(45) DEFAULT NULL,
  `nomor_handphone` varchar(45) DEFAULT NULL,
  `plat_nomor` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_supir_truck_trucking_company1` (`trucking_company_id`),
  CONSTRAINT `fk_supir_truck_trucking_company1` FOREIGN KEY (`trucking_company_id`) REFERENCES `trucking_company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supir_truck`
--

LOCK TABLES `supir_truck` WRITE;
/*!40000 ALTER TABLE `supir_truck` DISABLE KEYS */;
INSERT INTO `supir_truck` VALUES (2,7,'Suryono','082129876','B9629UEM'),(4,7,'Handiko','08212283876','B9206UIV'),(5,1,'Brekele','085711611076','B4946US'),(6,1,'sumanto','08080808','B3GO'),(7,1,'THOMAS','08112334','B123OKE'),(8,1,'BUDI','08455555','B6621HK');
/*!40000 ALTER TABLE `supir_truck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ter3_iso_container`
--

DROP TABLE IF EXISTS `ter3_iso_container`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ter3_iso_container` (
  `iso_code` char(4) NOT NULL,
  `size` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `width` varchar(45) DEFAULT NULL,
  `height` varchar(45) DEFAULT NULL,
  `description` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`iso_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ter3_iso_container`
--

LOCK TABLES `ter3_iso_container` WRITE;
/*!40000 ALTER TABLE `ter3_iso_container` DISABLE KEYS */;
/*!40000 ALTER TABLE `ter3_iso_container` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terminal_petikemas`
--

DROP TABLE IF EXISTS `terminal_petikemas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terminal_petikemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pelabuhan_id` int(11) NOT NULL,
  `kode_terminal_petikemas` char(4) DEFAULT NULL,
  `nama_terminal_petikemas` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_terminal_petikemas_cabang_pelabuhan1` (`pelabuhan_id`),
  CONSTRAINT `fk_terminal_petikemas_cabang_pelabuhan1` FOREIGN KEY (`pelabuhan_id`) REFERENCES `pelabuhan` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terminal_petikemas`
--

LOCK TABLES `terminal_petikemas` WRITE;
/*!40000 ALTER TABLE `terminal_petikemas` DISABLE KEYS */;
INSERT INTO `terminal_petikemas` VALUES (1,1,'TER3','PT. Mustika Alam Lestari - T300 Terminal');
/*!40000 ALTER TABLE `terminal_petikemas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trucking_company`
--

DROP TABLE IF EXISTS `trucking_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trucking_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trucking_company_users1` (`member_id`),
  CONSTRAINT `trucking_company_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trucking_company`
--

LOCK TABLES `trucking_company` WRITE;
/*!40000 ALTER TABLE `trucking_company` DISABLE KEYS */;
INSERT INTO `trucking_company` VALUES (1,3),(2,5),(3,6),(4,8),(5,9),(6,14),(7,31);
/*!40000 ALTER TABLE `trucking_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(45) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `telepon` varchar(45) DEFAULT NULL,
  `handphone` varchar(45) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `fk_users_member1` (`member_id`),
  CONSTRAINT `fk_users_member1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin',1,'Administrator','ac43724f16e9241d990427ab7c8f4228','djati@ilcs.co.id',NULL,'+628711611076',1),(2,'puniff',2,'Test Name','e761b94e0fd355241522996752d83e0e','djati@ilcs.co.id','02155555','085711611076',1),(3,'punitruck',3,'Puninar Trucking','bb7fd5f0171d89e29491b39929c6abb6','djati@ilcs.co.id','02155555','085711611076',1),(4,'matsoleh',4,'Mat Soleh Transport','ac43724f16e9241d990427ab7c8f4228','djati@ilcs.co.id','92398523','085711611076',1),(5,'kampret',5,'sadgdsagdsag','c4ca4238a0b923820dcc509a6f75849b','test@kampret.net','33246326342','436327436',1),(6,'agdsgadsg',6,'asdgdsagdsa','c4ca4238a0b923820dcc509a6f75849b','djati@ilcs.co.id','646146','234623462436',1),(8,'235325325',8,'asdgsdagsdag','c4ca4238a0b923820dcc509a6f75849b','djati@ilcs.co.id','12363216326','3216326',1),(9,'matsolar',9,'Mat Solar Transport','ac43724f16e9241d990427ab7c8f4228','djati@ilcs.co.id','09264269','085711611076',1),(10,'ujang',10,'Ujang Bingung','dcb8aa378ac4a3de5d12f90080f464e0','dagelan@yahoo.com','007','007',1),(11,'akmal',11,'akmal full','272874d450b7f8381b1174133ac62b40','akmal@ilcs.co.id','','081234567890',1),(12,'funny',12,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','08985433333','085369197970',1),(13,'f4n1',13,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','0987656543','08123444555',1),(14,'funtrucking',14,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','098765432','098765433',1),(15,'cargo1',15,'Cargo Trading','826b09e6df05cae64874f5956e4705a1','cargo@yahoo.com','','081277683738',1),(16,'carter',16,'IKT','cdb0f5a1314ef6b2729ee6911215150a',NULL,NULL,NULL,1),(23,'US21A0101',24,'US21A0101','8b6b9e3c6da6c18453d8a024f14f1afe','s@s.com','3','3',1),(25,'aa',26,'aa','4124bc0a9335c27f086f24ba207a4912','aa@aa.aa','55','55',1),(28,'cds',27,'CDS','5f4dcc3b5aa765d61d8327deb882cf99','nurdin@ilcs.co.id','08989999','085715076906',1),(29,'rangga',16,'Rangga','4c56d4c4a4681688475651f6e2b03a40',NULL,NULL,NULL,1),(30,'zxcvbnm',28,'asdfghjk','02c75fb22c75b23dc963c7eb91a062cc','sdfghj@dfghj.com','8765435','0987654398',1),(31,'ian',29,'ian','02c75fb22c75b23dc963c7eb91a062cc','iansindoro@yahoo.com','','0811996678',1),(32,'Simba',30,'Hendra Wirawan','f7235a61fdc3adc78d866fd8085d44db','hendra@simbalogistics.co.id','0212522110','081514864560',1),(33,'beacukai',33,'Bea Cukai','cdb0f5a1314ef6b2729ee6911215150a',NULL,NULL,NULL,1),(34,'ikt',16,'IKT','cdb0f5a1314ef6b2729ee6911215150a',NULL,NULL,NULL,1),(35,'ikt4beacukai',33,'IKT','cdb0f5a1314ef6b2729ee6911215150a',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_cargo`
--

DROP TABLE IF EXISTS `users_cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_cargo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(45) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `telepon` varchar(45) DEFAULT NULL,
  `handphone` varchar(45) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `id_group` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `fk_users_member1` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_cargo`
--

LOCK TABLES `users_cargo` WRITE;
/*!40000 ALTER TABLE `users_cargo` DISABLE KEYS */;
INSERT INTO `users_cargo` VALUES (1,'admin',1,'Administrator','21232f297a57a5a743894a0e4a801fc3','djati@ilcs.co.id',NULL,'+628711611076',1,'1'),(2,'puniff',2,'Puniff Freight Forwader','e761b94e0fd355241522996752d83e0e','djati@ilcs.co.id','02155555','085711611076',1,'2'),(3,'punitruck',3,'Puninar Trucking','bb7fd5f0171d89e29491b39929c6abb6','djati@ilcs.co.id','02155555','085711611076',1,'3'),(4,'matsoleh',4,'Mat Soleh Transport','ac43724f16e9241d990427ab7c8f4228','djati@ilcs.co.id','92398523','085711611076',1,'3'),(9,'matsolar',9,'Mat Solar Transport','ac43724f16e9241d990427ab7c8f4228','djati@ilcs.co.id','09264269','085711611076',1,'3'),(10,'ujang',10,'Ujang Bingung','dcb8aa378ac4a3de5d12f90080f464e0','dagelan@yahoo.com','007','007',1,''),(11,'akmal',11,'akmal full','272874d450b7f8381b1174133ac62b40','akmal@ilcs.co.id','','081234567890',1,''),(12,'funny',12,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','08985433333','085369197970',1,''),(13,'f4n1',13,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','0987656543','08123444555',1,''),(14,'funtrucking',14,'evanirosari','2a48bd51323b8362d52efc77e10fa043','evanirosari@gmail.com','098765432','098765433',1,''),(15,'cargo1',15,'Cargo Owner','39398936ff034b676651d60a1e81bf99','cargo@yahoo.com','','081277683738',1,'4'),(16,'fftruck',16,'FF Trucking Company','915a0589afa97d9831e146218c042290',NULL,NULL,NULL,1,'5'),(23,'US21A0101',24,'US21A0101','8b6b9e3c6da6c18453d8a024f14f1afe','s@s.com','3','3',1,''),(28,'cds',27,'CDS','5f4dcc3b5aa765d61d8327deb882cf99','nurdin@ilcs.co.id','08989999','085715076906',1,''),(29,'rangga',16,'Rangga','4c56d4c4a4681688475651f6e2b03a40',NULL,NULL,NULL,1,''),(31,'ian',29,'ian','02c75fb22c75b23dc963c7eb91a062cc','iansindoro@yahoo.com','','0811996678',1,''),(32,'Simba',30,'Hendra Wirawan','f7235a61fdc3adc78d866fd8085d44db','hendra@simbalogistics.co.id','0212522110','081514864560',1,''),(33,'agility',31,'karno','a9604d3b37863be2d295e1510315ac9c','karno@karno.com','0213333',NULL,1,''),(34,'unilever',10,'PT Unilever','d0edcde21ef4e7126ab0a9fb7c3b03ce','rizka@ilcs.co.id','02188833300','08931333728',1,''),(35,'SQ',32,'SYAIFUL QURBANI','6117ce60134a9a35c9fcfd1914f973aa','SYAIFUL@CAKRABAHANA.COM','02165310586','0811958515',1,NULL);
/*!40000 ALTER TABLE `users_cargo` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-07-02 13:14:13
