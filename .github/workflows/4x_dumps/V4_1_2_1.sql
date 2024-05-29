-- MySQL dump 10.19  Distrib 10.2.44-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ohrm4121
-- ------------------------------------------------------
-- Server version	10.2.44-MariaDB-1:10.2.44+maria~bionic

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
-- Table structure for table `abstract_display_field`
--

DROP TABLE IF EXISTS `abstract_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abstract_display_field` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_alias` varchar(255) DEFAULT NULL,
  `is_sortable` varchar(10) NOT NULL,
  `sort_order` varchar(255) DEFAULT NULL,
  `sort_field` varchar(255) DEFAULT NULL,
  `element_type` varchar(255) NOT NULL,
  `element_property` text NOT NULL,
  `width` varchar(255) NOT NULL,
  `is_exportable` varchar(10) DEFAULT NULL,
  `text_alignment_style` varchar(20) DEFAULT NULL,
  `is_value_list` tinyint(1) NOT NULL,
  `display_field_group_id` bigint(20) unsigned DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL,
  `is_meta` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abstract_display_field`
--

LOCK TABLES `abstract_display_field` WRITE;
/*!40000 ALTER TABLE `abstract_display_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `abstract_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_config`
--

DROP TABLE IF EXISTS `hs_hr_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_config` (
  `key` varchar(100) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_config`
--

LOCK TABLES `hs_hr_config` WRITE;
/*!40000 ALTER TABLE `hs_hr_config` DISABLE KEYS */;
INSERT INTO `hs_hr_config` VALUES ('admin.default_workshift_end_time','17:00'),('admin.default_workshift_start_time','09:00'),('admin.localization.default_date_format','Y-m-d'),('admin.localization.default_language',''),('admin.localization.use_browser_language','No'),('admin.product_type','os'),('attendanceEmpEditSubmitted','No'),('attendanceSupEditSubmitted','No'),('authentication.default_required_password_strength','medium'),('authentication.enforce_password_strength','on'),('authentication.status','Enable'),('authorize_user_role_manager_class','BasicUserRoleManager'),('beacon.activation_acceptance_status','off'),('beacon.activiation_status','off'),('beacon.company_name',''),('beacon.flash_period','120'),('beacon.lock','unlocked'),('beacon.next_flash_time','0000-00-00'),('beacon.uuid','0'),('csrf_secret','ffa3dca60428146c981279be7734a37e6b0f989f30a396f39e5479854f5b034a8bdb6cc0e1391d98a0ed3d5ad9b1f1edcffd125a355703'),('domain.name','localhost'),('hsp_accrued_last_updated','0000-00-00'),('hsp_current_plan','0'),('hsp_used_last_updated','0000-00-00'),('include_supervisor_chain','No'),('instance.identifier','T0hSTV9kZXZpQGFkbWluLmNvbV8yMDI0LTA1LTA5OTU='),('ldap_domain_name',''),('ldap_port',''),('ldap_server',''),('ldap_status',''),('leave.entitlement_consumption_algorithm','FIFOEntitlementConsumptionStrategy'),('leave.include_pending_leave_in_balance','1'),('leave.leavePeriodStatus','1'),('leave.work_schedule_implementation','BasicWorkSchedule'),('openId.provider.added','on'),('open_source_integrations','<xml>\n<integrations>\n</integrations>\n</xml>\n'),('pim_show_deprecated_fields','0'),('report.mysql_group_concat_max_len','2048'),('showSIN','0'),('showSSN','0'),('showTaxExemptions','0'),('themeName','default'),('timesheet_period_and_start_date','<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>'),('timesheet_period_set','No'),('timesheet_time_format','1');
/*!40000 ALTER TABLE `hs_hr_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_country`
--

DROP TABLE IF EXISTS `hs_hr_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_country` (
  `cou_code` char(2) NOT NULL DEFAULT '',
  `name` varchar(80) NOT NULL DEFAULT '',
  `cou_name` varchar(80) NOT NULL DEFAULT '',
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`cou_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_country`
--

LOCK TABLES `hs_hr_country` WRITE;
/*!40000 ALTER TABLE `hs_hr_country` DISABLE KEYS */;
INSERT INTO `hs_hr_country` VALUES ('AD','ANDORRA','Andorra','AND',20),('AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE',784),('AF','AFGHANISTAN','Afghanistan','AFG',4),('AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG',28),('AI','ANGUILLA','Anguilla','AIA',660),('AL','ALBANIA','Albania','ALB',8),('AM','ARMENIA','Armenia','ARM',51),('AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT',530),('AO','ANGOLA','Angola','AGO',24),('AQ','ANTARCTICA','Antarctica',NULL,NULL),('AR','ARGENTINA','Argentina','ARG',32),('AS','AMERICAN SAMOA','American Samoa','ASM',16),('AT','AUSTRIA','Austria','AUT',40),('AU','AUSTRALIA','Australia','AUS',36),('AW','ARUBA','Aruba','ABW',533),('AZ','AZERBAIJAN','Azerbaijan','AZE',31),('BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH',70),('BB','BARBADOS','Barbados','BRB',52),('BD','BANGLADESH','Bangladesh','BGD',50),('BE','BELGIUM','Belgium','BEL',56),('BF','BURKINA FASO','Burkina Faso','BFA',854),('BG','BULGARIA','Bulgaria','BGR',100),('BH','BAHRAIN','Bahrain','BHR',48),('BI','BURUNDI','Burundi','BDI',108),('BJ','BENIN','Benin','BEN',204),('BM','BERMUDA','Bermuda','BMU',60),('BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN',96),('BO','BOLIVIA','Bolivia','BOL',68),('BR','BRAZIL','Brazil','BRA',76),('BS','BAHAMAS','Bahamas','BHS',44),('BT','BHUTAN','Bhutan','BTN',64),('BV','BOUVET ISLAND','Bouvet Island',NULL,NULL),('BW','BOTSWANA','Botswana','BWA',72),('BY','BELARUS','Belarus','BLR',112),('BZ','BELIZE','Belize','BLZ',84),('CA','CANADA','Canada','CAN',124),('CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL),('CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD',180),('CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF',140),('CG','CONGO','Congo','COG',178),('CH','SWITZERLAND','Switzerland','CHE',756),('CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV',384),('CK','COOK ISLANDS','Cook Islands','COK',184),('CL','CHILE','Chile','CHL',152),('CM','CAMEROON','Cameroon','CMR',120),('CN','CHINA','China','CHN',156),('CO','COLOMBIA','Colombia','COL',170),('CR','COSTA RICA','Costa Rica','CRI',188),('CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL),('CU','CUBA','Cuba','CUB',192),('CV','CAPE VERDE','Cape Verde','CPV',132),('CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL),('CY','CYPRUS','Cyprus','CYP',196),('CZ','CZECH REPUBLIC','Czech Republic','CZE',203),('DE','GERMANY','Germany','DEU',276),('DJ','DJIBOUTI','Djibouti','DJI',262),('DK','DENMARK','Denmark','DNK',208),('DM','DOMINICA','Dominica','DMA',212),('DO','DOMINICAN REPUBLIC','Dominican Republic','DOM',214),('DZ','ALGERIA','Algeria','DZA',12),('EC','ECUADOR','Ecuador','ECU',218),('EE','ESTONIA','Estonia','EST',233),('EG','EGYPT','Egypt','EGY',818),('EH','WESTERN SAHARA','Western Sahara','ESH',732),('ER','ERITREA','Eritrea','ERI',232),('ES','SPAIN','Spain','ESP',724),('ET','ETHIOPIA','Ethiopia','ETH',231),('FI','FINLAND','Finland','FIN',246),('FJ','FIJI','Fiji','FJI',242),('FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK',238),('FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM',583),('FO','FAROE ISLANDS','Faroe Islands','FRO',234),('FR','FRANCE','France','FRA',250),('GA','GABON','Gabon','GAB',266),('GB','UNITED KINGDOM','United Kingdom','GBR',826),('GD','GRENADA','Grenada','GRD',308),('GE','GEORGIA','Georgia','GEO',268),('GF','FRENCH GUIANA','French Guiana','GUF',254),('GH','GHANA','Ghana','GHA',288),('GI','GIBRALTAR','Gibraltar','GIB',292),('GL','GREENLAND','Greenland','GRL',304),('GM','GAMBIA','Gambia','GMB',270),('GN','GUINEA','Guinea','GIN',324),('GP','GUADELOUPE','Guadeloupe','GLP',312),('GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ',226),('GR','GREECE','Greece','GRC',300),('GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL),('GT','GUATEMALA','Guatemala','GTM',320),('GU','GUAM','Guam','GUM',316),('GW','GUINEA-BISSAU','Guinea-Bissau','GNB',624),('GY','GUYANA','Guyana','GUY',328),('HK','HONG KONG','Hong Kong','HKG',344),('HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL),('HN','HONDURAS','Honduras','HND',340),('HR','CROATIA','Croatia','HRV',191),('HT','HAITI','Haiti','HTI',332),('HU','HUNGARY','Hungary','HUN',348),('ID','INDONESIA','Indonesia','IDN',360),('IE','IRELAND','Ireland','IRL',372),('IL','ISRAEL','Israel','ISR',376),('IN','INDIA','India','IND',356),('IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL),('IQ','IRAQ','Iraq','IRQ',368),('IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN',364),('IS','ICELAND','Iceland','ISL',352),('IT','ITALY','Italy','ITA',380),('JM','JAMAICA','Jamaica','JAM',388),('JO','JORDAN','Jordan','JOR',400),('JP','JAPAN','Japan','JPN',392),('KE','KENYA','Kenya','KEN',404),('KG','KYRGYZSTAN','Kyrgyzstan','KGZ',417),('KH','CAMBODIA','Cambodia','KHM',116),('KI','KIRIBATI','Kiribati','KIR',296),('KM','COMOROS','Comoros','COM',174),('KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA',659),('KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK',408),('KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR',410),('KW','KUWAIT','Kuwait','KWT',414),('KY','CAYMAN ISLANDS','Cayman Islands','CYM',136),('KZ','KAZAKHSTAN','Kazakhstan','KAZ',398),('LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO',418),('LB','LEBANON','Lebanon','LBN',422),('LC','SAINT LUCIA','Saint Lucia','LCA',662),('LI','LIECHTENSTEIN','Liechtenstein','LIE',438),('LK','SRI LANKA','Sri Lanka','LKA',144),('LR','LIBERIA','Liberia','LBR',430),('LS','LESOTHO','Lesotho','LSO',426),('LT','LITHUANIA','Lithuania','LTU',440),('LU','LUXEMBOURG','Luxembourg','LUX',442),('LV','LATVIA','Latvia','LVA',428),('LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY',434),('MA','MOROCCO','Morocco','MAR',504),('MC','MONACO','Monaco','MCO',492),('MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA',498),('MG','MADAGASCAR','Madagascar','MDG',450),('MH','MARSHALL ISLANDS','Marshall Islands','MHL',584),('MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD',807),('ML','MALI','Mali','MLI',466),('MM','MYANMAR','Myanmar','MMR',104),('MN','MONGOLIA','Mongolia','MNG',496),('MO','MACAO','Macao','MAC',446),('MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP',580),('MQ','MARTINIQUE','Martinique','MTQ',474),('MR','MAURITANIA','Mauritania','MRT',478),('MS','MONTSERRAT','Montserrat','MSR',500),('MT','MALTA','Malta','MLT',470),('MU','MAURITIUS','Mauritius','MUS',480),('MV','MALDIVES','Maldives','MDV',462),('MW','MALAWI','Malawi','MWI',454),('MX','MEXICO','Mexico','MEX',484),('MY','MALAYSIA','Malaysia','MYS',458),('MZ','MOZAMBIQUE','Mozambique','MOZ',508),('NA','NAMIBIA','Namibia','NAM',516),('NC','NEW CALEDONIA','New Caledonia','NCL',540),('NE','NIGER','Niger','NER',562),('NF','NORFOLK ISLAND','Norfolk Island','NFK',574),('NG','NIGERIA','Nigeria','NGA',566),('NI','NICARAGUA','Nicaragua','NIC',558),('NL','NETHERLANDS','Netherlands','NLD',528),('NO','NORWAY','Norway','NOR',578),('NP','NEPAL','Nepal','NPL',524),('NR','NAURU','Nauru','NRU',520),('NU','NIUE','Niue','NIU',570),('NZ','NEW ZEALAND','New Zealand','NZL',554),('OM','OMAN','Oman','OMN',512),('PA','PANAMA','Panama','PAN',591),('PE','PERU','Peru','PER',604),('PF','FRENCH POLYNESIA','French Polynesia','PYF',258),('PG','PAPUA NEW GUINEA','Papua New Guinea','PNG',598),('PH','PHILIPPINES','Philippines','PHL',608),('PK','PAKISTAN','Pakistan','PAK',586),('PL','POLAND','Poland','POL',616),('PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM',666),('PN','PITCAIRN','Pitcairn','PCN',612),('PR','PUERTO RICO','Puerto Rico','PRI',630),('PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL),('PT','PORTUGAL','Portugal','PRT',620),('PW','PALAU','Palau','PLW',585),('PY','PARAGUAY','Paraguay','PRY',600),('QA','QATAR','Qatar','QAT',634),('RE','REUNION','Reunion','REU',638),('RO','ROMANIA','Romania','ROM',642),('RU','RUSSIAN FEDERATION','Russian Federation','RUS',643),('RW','RWANDA','Rwanda','RWA',646),('SA','SAUDI ARABIA','Saudi Arabia','SAU',682),('SB','SOLOMON ISLANDS','Solomon Islands','SLB',90),('SC','SEYCHELLES','Seychelles','SYC',690),('SD','SUDAN','Sudan','SDN',736),('SE','SWEDEN','Sweden','SWE',752),('SG','SINGAPORE','Singapore','SGP',702),('SH','SAINT HELENA','Saint Helena','SHN',654),('SI','SLOVENIA','Slovenia','SVN',705),('SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM',744),('SK','SLOVAKIA','Slovakia','SVK',703),('SL','SIERRA LEONE','Sierra Leone','SLE',694),('SM','SAN MARINO','San Marino','SMR',674),('SN','SENEGAL','Senegal','SEN',686),('SO','SOMALIA','Somalia','SOM',706),('SR','SURINAME','Suriname','SUR',740),('ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP',678),('SV','EL SALVADOR','El Salvador','SLV',222),('SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR',760),('SZ','SWAZILAND','Swaziland','SWZ',748),('TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA',796),('TD','CHAD','Chad','TCD',148),('TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL),('TG','TOGO','Togo','TGO',768),('TH','THAILAND','Thailand','THA',764),('TJ','TAJIKISTAN','Tajikistan','TJK',762),('TK','TOKELAU','Tokelau','TKL',772),('TL','TIMOR-LESTE','Timor-Leste',NULL,NULL),('TM','TURKMENISTAN','Turkmenistan','TKM',795),('TN','TUNISIA','Tunisia','TUN',788),('TO','TONGA','Tonga','TON',776),('TR','TURKEY','Turkey','TUR',792),('TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO',780),('TV','TUVALU','Tuvalu','TUV',798),('TW','TAIWAN, PROVINCE OF CHINA','Taiwan','TWN',158),('TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA',834),('UA','UKRAINE','Ukraine','UKR',804),('UG','UGANDA','Uganda','UGA',800),('UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL),('US','UNITED STATES','United States','USA',840),('UY','URUGUAY','Uruguay','URY',858),('UZ','UZBEKISTAN','Uzbekistan','UZB',860),('VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT',336),('VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT',670),('VE','VENEZUELA','Venezuela','VEN',862),('VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB',92),('VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR',850),('VN','VIET NAM','Viet Nam','VNM',704),('VU','VANUATU','Vanuatu','VUT',548),('WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF',876),('WS','SAMOA','Samoa','WSM',882),('YE','YEMEN','Yemen','YEM',887),('YT','MAYOTTE','Mayotte',NULL,NULL),('ZA','SOUTH AFRICA','South Africa','ZAF',710),('ZM','ZAMBIA','Zambia','ZMB',894),('ZW','ZIMBABWE','Zimbabwe','ZWE',716);
/*!40000 ALTER TABLE `hs_hr_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_currency_type`
--

DROP TABLE IF EXISTS `hs_hr_currency_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_currency_type` (
  `code` int(11) NOT NULL DEFAULT 0,
  `currency_id` char(3) NOT NULL DEFAULT '',
  `currency_name` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_currency_type`
--

LOCK TABLES `hs_hr_currency_type` WRITE;
/*!40000 ALTER TABLE `hs_hr_currency_type` DISABLE KEYS */;
INSERT INTO `hs_hr_currency_type` VALUES (3,'AED','Utd. Arab Emir. Dirham'),(4,'AFN','Afghanistan Afghani'),(5,'ALL','Albanian Lek'),(6,'ANG','NL Antillian Guilder'),(7,'AOR','Angolan New Kwanza'),(177,'ARP','Argentina Pesos'),(8,'ARS','Argentine Peso'),(10,'AUD','Australian Dollar'),(11,'AWG','Aruban Florin'),(12,'BBD','Barbados Dollar'),(13,'BDT','Bangladeshi Taka'),(15,'BGL','Bulgarian Lev'),(16,'BHD','Bahraini Dinar'),(17,'BIF','Burundi Franc'),(18,'BMD','Bermudian Dollar'),(19,'BND','Brunei Dollar'),(20,'BOB','Bolivian Boliviano'),(21,'BRL','Brazilian Real'),(22,'BSD','Bahamian Dollar'),(23,'BTN','Bhutan Ngultrum'),(24,'BWP','Botswana Pula'),(25,'BZD','Belize Dollar'),(26,'CAD','Canadian Dollar'),(27,'CHF','Swiss Franc'),(28,'CLP','Chilean Peso'),(29,'CNY','Chinese Yuan Renminbi'),(30,'COP','Colombian Peso'),(31,'CRC','Costa Rican Colon'),(32,'CUP','Cuban Peso'),(33,'CVE','Cape Verde Escudo'),(34,'CYP','Cyprus Pound'),(171,'CZK','Czech Koruna'),(37,'DJF','Djibouti Franc'),(38,'DKK','Danish Krona'),(39,'DOP','Dominican Peso'),(40,'DZD','Algerian Dinar'),(41,'ECS','Ecuador Sucre'),(43,'EEK','Estonian Krona'),(44,'EGP','Egyptian Pound'),(46,'ETB','Ethiopian Birr'),(42,'EUR','Euro'),(48,'FJD','Fiji Dollar'),(49,'FKP','Falkland Islands Pound'),(51,'GBP','Pound Sterling'),(52,'GHC','Ghanaian Cedi'),(53,'GIP','Gibraltar Pound'),(54,'GMD','Gambian Dalasi'),(55,'GNF','Guinea Franc'),(57,'GTQ','Guatemalan Quetzal'),(58,'GYD','Guyanan Dollar'),(59,'HKD','Hong Kong Dollar'),(60,'HNL','Honduran Lempira'),(61,'HRK','Croatian Kuna'),(62,'HTG','Haitian Gourde'),(63,'HUF','Hungarian Forint'),(64,'IDR','Indonesian Rupiah'),(66,'ILS','Israeli New Shekel'),(67,'INR','Indian Rupee'),(68,'IQD','Iraqi Dinar'),(69,'IRR','Iranian Rial'),(70,'ISK','Iceland Krona'),(72,'JMD','Jamaican Dollar'),(73,'JOD','Jordanian Dinar'),(74,'JPY','Japanese Yen'),(75,'KES','Kenyan Shilling'),(76,'KHR','Kampuchean Riel'),(77,'KMF','Comoros Franc'),(78,'KPW','North Korean Won'),(79,'KRW','Korean Won'),(80,'KWD','Kuwaiti Dinar'),(81,'KYD','Cayman Islands Dollar'),(82,'KZT','Kazakhstan Tenge'),(83,'LAK','Lao Kip'),(84,'LBP','Lebanese Pound'),(85,'LKR','Sri Lanka Rupee'),(86,'LRD','Liberian Dollar'),(87,'LSL','Lesotho Loti'),(88,'LTL','Lithuanian Litas'),(90,'LVL','Latvian Lats'),(91,'LYD','Libyan Dinar'),(92,'MAD','Moroccan Dirham'),(93,'MGF','Malagasy Franc'),(94,'MMK','Myanmar Kyat'),(95,'MNT','Mongolian Tugrik'),(96,'MOP','Macau Pataca'),(97,'MRO','Mauritanian Ouguiya'),(98,'MTL','Maltese Lira'),(99,'MUR','Mauritius Rupee'),(100,'MVR','Maldive Rufiyaa'),(101,'MWK','Malawi Kwacha'),(102,'MXN','Mexican New Peso'),(172,'MXP','Mexican Peso'),(103,'MYR','Malaysian Ringgit'),(104,'MZM','Mozambique Metical'),(105,'NAD','Namibia Dollar'),(106,'NGN','Nigerian Naira'),(107,'NIO','Nicaraguan Cordoba Oro'),(109,'NOK','Norwegian Krona'),(110,'NPR','Nepalese Rupee'),(111,'NZD','New Zealand Dollar'),(112,'OMR','Omani Rial'),(113,'PAB','Panamanian Balboa'),(114,'PEN','Peruvian Nuevo Sol'),(115,'PGK','Papua New Guinea Kina'),(116,'PHP','Philippine Peso'),(117,'PKR','Pakistan Rupee'),(118,'PLN','Polish Zloty'),(120,'PYG','Paraguay Guarani'),(121,'QAR','Qatari Rial'),(122,'ROL','Romanian Leu'),(123,'RUB','Russian Rouble'),(180,'RUR','Russia Rubles'),(173,'SAR','Saudi Arabia Riyal'),(125,'SBD','Solomon Islands Dollar'),(126,'SCR','Seychelles Rupee'),(127,'SDD','Sudanese Dinar'),(128,'SDP','Sudanese Pound'),(129,'SEK','Swedish Krona'),(131,'SGD','Singapore Dollar'),(132,'SHP','St. Helena Pound'),(130,'SKK','Slovak Koruna'),(135,'SLL','Sierra Leone Leone'),(136,'SOS','Somali Shilling'),(137,'SRD','Surinamese Dollar'),(138,'STD','Sao Tome/Principe Dobra'),(139,'SVC','El Salvador Colon'),(140,'SYP','Syrian Pound'),(141,'SZL','Swaziland Lilangeni'),(142,'THB','Thai Baht'),(143,'TND','Tunisian Dinar'),(144,'TOP','Tongan Pa\'anga'),(145,'TRL','Turkish Lira'),(146,'TTD','Trinidad/Tobago Dollar'),(147,'TWD','Taiwan Dollar'),(148,'TZS','Tanzanian Shilling'),(149,'UAH','Ukraine Hryvnia'),(150,'UGX','Uganda Shilling'),(151,'USD','United States Dollar'),(152,'UYP','Uruguayan Peso'),(153,'VEB','Venezuelan Bolivar'),(154,'VND','Vietnamese Dong'),(155,'VUV','Vanuatu Vatu'),(156,'WST','Samoan Tala'),(158,'XAF','CFA Franc BEAC'),(159,'XAG','Silver (oz.)'),(160,'XAU','Gold (oz.)'),(161,'XCD','Eastern Caribbean Dollars'),(179,'XDR','IMF Special Drawing Right'),(162,'XOF','CFA Franc BCEAO'),(163,'XPD','Palladium (oz.)'),(164,'XPF','CFP Franc'),(165,'XPT','Platinum (oz.)'),(166,'YER','Yemeni Riyal'),(167,'YUM','Yugoslavian Dinar'),(175,'YUN','Yugoslav Dinar'),(168,'ZAR','South African Rand'),(176,'ZMK','Zambian Kwacha'),(169,'ZRN','New Zaire'),(170,'ZWD','Zimbabwe Dollar');
/*!40000 ALTER TABLE `hs_hr_currency_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_custom_export`
--

DROP TABLE IF EXISTS `hs_hr_custom_export`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_custom_export` (
  `export_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `fields` text DEFAULT NULL,
  `headings` text DEFAULT NULL,
  PRIMARY KEY (`export_id`),
  KEY `emp_number` (`export_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_custom_export`
--

LOCK TABLES `hs_hr_custom_export` WRITE;
/*!40000 ALTER TABLE `hs_hr_custom_export` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_custom_export` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_custom_fields`
--

DROP TABLE IF EXISTS `hs_hr_custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_custom_fields` (
  `field_num` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` int(11) NOT NULL,
  `screen` varchar(100) DEFAULT NULL,
  `extra_data` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`field_num`),
  KEY `emp_number` (`field_num`),
  KEY `screen` (`screen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_custom_fields`
--

LOCK TABLES `hs_hr_custom_fields` WRITE;
/*!40000 ALTER TABLE `hs_hr_custom_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_custom_import`
--

DROP TABLE IF EXISTS `hs_hr_custom_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_custom_import` (
  `import_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `fields` text DEFAULT NULL,
  `has_heading` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`import_id`),
  KEY `emp_number` (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_custom_import`
--

LOCK TABLES `hs_hr_custom_import` WRITE;
/*!40000 ALTER TABLE `hs_hr_custom_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_custom_import` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_district`
--

DROP TABLE IF EXISTS `hs_hr_district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_district` (
  `district_code` varchar(13) NOT NULL DEFAULT '',
  `district_name` varchar(50) DEFAULT NULL,
  `province_code` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`district_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_district`
--

LOCK TABLES `hs_hr_district` WRITE;
/*!40000 ALTER TABLE `hs_hr_district` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_district` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_attachment`
--

DROP TABLE IF EXISTS `hs_hr_emp_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_attachment` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `eattach_id` int(11) NOT NULL DEFAULT 0,
  `eattach_desc` varchar(200) DEFAULT NULL,
  `eattach_filename` varchar(100) DEFAULT NULL,
  `eattach_size` int(11) DEFAULT 0,
  `eattach_attachment` mediumblob DEFAULT NULL,
  `eattach_type` varchar(200) DEFAULT NULL,
  `screen` varchar(100) DEFAULT '',
  `attached_by` int(11) DEFAULT NULL,
  `attached_by_name` varchar(200) DEFAULT NULL,
  `attached_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`emp_number`,`eattach_id`),
  KEY `screen` (`screen`),
  CONSTRAINT `hs_hr_emp_attachment_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_attachment`
--

LOCK TABLES `hs_hr_emp_attachment` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_basicsalary`
--

DROP TABLE IF EXISTS `hs_hr_emp_basicsalary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_basicsalary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `sal_grd_code` int(11) DEFAULT NULL,
  `currency_id` varchar(6) NOT NULL DEFAULT '',
  `ebsal_basic_salary` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `payperiod_code` varchar(13) DEFAULT NULL,
  `salary_component` varchar(100) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sal_grd_code` (`sal_grd_code`),
  KEY `currency_id` (`currency_id`),
  KEY `emp_number` (`emp_number`),
  KEY `payperiod_code` (`payperiod_code`),
  CONSTRAINT `hs_hr_emp_basicsalary_ibfk_1` FOREIGN KEY (`sal_grd_code`) REFERENCES `ohrm_pay_grade` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_basicsalary_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `hs_hr_currency_type` (`currency_id`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_basicsalary_ibfk_3` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_basicsalary_ibfk_4` FOREIGN KEY (`payperiod_code`) REFERENCES `hs_hr_payperiod` (`payperiod_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_basicsalary`
--

LOCK TABLES `hs_hr_emp_basicsalary` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_basicsalary` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_basicsalary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_children`
--

DROP TABLE IF EXISTS `hs_hr_emp_children`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_children` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `ec_seqno` decimal(2,0) NOT NULL DEFAULT 0,
  `ec_name` varchar(100) DEFAULT '',
  `ec_date_of_birth` date DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`ec_seqno`),
  CONSTRAINT `hs_hr_emp_children_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_children`
--

LOCK TABLES `hs_hr_emp_children` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_children` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_children` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_contract_extend`
--

DROP TABLE IF EXISTS `hs_hr_emp_contract_extend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_contract_extend` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `econ_extend_id` decimal(10,0) NOT NULL DEFAULT 0,
  `econ_extend_start_date` datetime DEFAULT NULL,
  `econ_extend_end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`econ_extend_id`),
  CONSTRAINT `hs_hr_emp_contract_extend_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_contract_extend`
--

LOCK TABLES `hs_hr_emp_contract_extend` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_contract_extend` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_contract_extend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_dependents`
--

DROP TABLE IF EXISTS `hs_hr_emp_dependents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_dependents` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `ed_seqno` decimal(2,0) NOT NULL DEFAULT 0,
  `ed_name` varchar(100) DEFAULT '',
  `ed_relationship_type` enum('child','other') DEFAULT NULL,
  `ed_relationship` varchar(100) DEFAULT '',
  `ed_date_of_birth` date DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`ed_seqno`),
  CONSTRAINT `hs_hr_emp_dependents_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_dependents`
--

LOCK TABLES `hs_hr_emp_dependents` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_dependents` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_dependents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_directdebit`
--

DROP TABLE IF EXISTS `hs_hr_emp_directdebit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_directdebit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salary_id` int(11) NOT NULL,
  `dd_routing_num` int(9) NOT NULL,
  `dd_account` varchar(100) NOT NULL DEFAULT '',
  `dd_amount` decimal(11,2) NOT NULL,
  `dd_account_type` varchar(20) NOT NULL DEFAULT '' COMMENT 'CHECKING, SAVINGS',
  `dd_transaction_type` varchar(20) NOT NULL DEFAULT '' COMMENT 'BLANK, PERC, FLAT, FLATMINUS',
  PRIMARY KEY (`id`),
  KEY `salary_id` (`salary_id`),
  CONSTRAINT `hs_hr_emp_directdebit_ibfk_1` FOREIGN KEY (`salary_id`) REFERENCES `hs_hr_emp_basicsalary` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_directdebit`
--

LOCK TABLES `hs_hr_emp_directdebit` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_directdebit` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_directdebit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_emergency_contacts`
--

DROP TABLE IF EXISTS `hs_hr_emp_emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_emergency_contacts` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `eec_seqno` decimal(2,0) NOT NULL DEFAULT 0,
  `eec_name` varchar(100) DEFAULT '',
  `eec_relationship` varchar(100) DEFAULT '',
  `eec_home_no` varchar(100) DEFAULT '',
  `eec_mobile_no` varchar(100) DEFAULT '',
  `eec_office_no` varchar(100) DEFAULT '',
  PRIMARY KEY (`emp_number`,`eec_seqno`),
  CONSTRAINT `hs_hr_emp_emergency_contacts_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_emergency_contacts`
--

LOCK TABLES `hs_hr_emp_emergency_contacts` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_emergency_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_emergency_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_history_of_ealier_pos`
--

DROP TABLE IF EXISTS `hs_hr_emp_history_of_ealier_pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_history_of_ealier_pos` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `emp_seqno` decimal(2,0) NOT NULL DEFAULT 0,
  `ehoep_job_title` varchar(100) DEFAULT '',
  `ehoep_years` varchar(100) DEFAULT '',
  PRIMARY KEY (`emp_number`,`emp_seqno`),
  CONSTRAINT `hs_hr_emp_history_of_ealier_pos_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_history_of_ealier_pos`
--

LOCK TABLES `hs_hr_emp_history_of_ealier_pos` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_history_of_ealier_pos` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_history_of_ealier_pos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_language`
--

DROP TABLE IF EXISTS `hs_hr_emp_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_language` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `lang_id` int(11) NOT NULL,
  `fluency` smallint(6) NOT NULL DEFAULT 0,
  `competency` smallint(6) DEFAULT 0,
  `comments` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`lang_id`,`fluency`),
  KEY `lang_id` (`lang_id`),
  CONSTRAINT `hs_hr_emp_language_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_language_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `ohrm_language` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_language`
--

LOCK TABLES `hs_hr_emp_language` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_language` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_locations`
--

DROP TABLE IF EXISTS `hs_hr_emp_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_locations` (
  `emp_number` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`emp_number`,`location_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `hs_hr_emp_locations_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `ohrm_location` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_locations_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_locations`
--

LOCK TABLES `hs_hr_emp_locations` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_member_detail`
--

DROP TABLE IF EXISTS `hs_hr_emp_member_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_member_detail` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `membship_code` int(6) NOT NULL DEFAULT 0,
  `ememb_subscript_ownership` varchar(20) DEFAULT NULL,
  `ememb_subscript_amount` decimal(15,2) DEFAULT NULL,
  `ememb_subs_currency` varchar(20) DEFAULT NULL,
  `ememb_commence_date` date DEFAULT NULL,
  `ememb_renewal_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `membship_code` (`membship_code`),
  KEY `emp_number` (`emp_number`),
  CONSTRAINT `hs_hr_emp_member_detail_ibfk_1` FOREIGN KEY (`membship_code`) REFERENCES `ohrm_membership` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_member_detail_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_member_detail`
--

LOCK TABLES `hs_hr_emp_member_detail` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_member_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_member_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_passport`
--

DROP TABLE IF EXISTS `hs_hr_emp_passport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_passport` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `ep_seqno` decimal(2,0) NOT NULL DEFAULT 0,
  `ep_passport_num` varchar(100) NOT NULL DEFAULT '',
  `ep_passportissueddate` datetime DEFAULT NULL,
  `ep_passportexpiredate` datetime DEFAULT NULL,
  `ep_comments` varchar(255) DEFAULT NULL,
  `ep_passport_type_flg` smallint(6) DEFAULT NULL,
  `ep_i9_status` varchar(100) DEFAULT '',
  `ep_i9_review_date` date DEFAULT NULL,
  `cou_code` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`ep_seqno`),
  CONSTRAINT `hs_hr_emp_passport_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_passport`
--

LOCK TABLES `hs_hr_emp_passport` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_passport` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_passport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_picture`
--

DROP TABLE IF EXISTS `hs_hr_emp_picture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_picture` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `epic_picture` mediumblob DEFAULT NULL,
  `epic_filename` varchar(100) DEFAULT NULL,
  `epic_type` varchar(50) DEFAULT NULL,
  `epic_file_size` varchar(20) DEFAULT NULL,
  `epic_file_width` varchar(20) DEFAULT NULL,
  `epic_file_height` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`emp_number`),
  CONSTRAINT `hs_hr_emp_picture_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_picture`
--

LOCK TABLES `hs_hr_emp_picture` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_picture` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_picture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_reportto`
--

DROP TABLE IF EXISTS `hs_hr_emp_reportto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_reportto` (
  `erep_sup_emp_number` int(7) NOT NULL DEFAULT 0,
  `erep_sub_emp_number` int(7) NOT NULL DEFAULT 0,
  `erep_reporting_mode` int(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`erep_sup_emp_number`,`erep_sub_emp_number`,`erep_reporting_mode`),
  KEY `erep_sub_emp_number` (`erep_sub_emp_number`),
  KEY `erep_reporting_mode` (`erep_reporting_mode`),
  CONSTRAINT `hs_hr_emp_reportto_ibfk_1` FOREIGN KEY (`erep_sup_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_reportto_ibfk_2` FOREIGN KEY (`erep_sub_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_reportto_ibfk_3` FOREIGN KEY (`erep_reporting_mode`) REFERENCES `ohrm_emp_reporting_method` (`reporting_method_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_reportto`
--

LOCK TABLES `hs_hr_emp_reportto` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_reportto` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_reportto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_skill`
--

DROP TABLE IF EXISTS `hs_hr_emp_skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_skill` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `skill_id` int(11) NOT NULL,
  `years_of_exp` decimal(2,0) DEFAULT NULL,
  `comments` varchar(100) NOT NULL DEFAULT '',
  KEY `emp_number` (`emp_number`),
  KEY `skill_id` (`skill_id`),
  CONSTRAINT `hs_hr_emp_skill_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_emp_skill_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `ohrm_skill` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_skill`
--

LOCK TABLES `hs_hr_emp_skill` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_skill` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_us_tax`
--

DROP TABLE IF EXISTS `hs_hr_emp_us_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_us_tax` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `tax_federal_status` varchar(13) DEFAULT NULL,
  `tax_federal_exceptions` int(2) DEFAULT 0,
  `tax_state` varchar(13) DEFAULT NULL,
  `tax_state_status` varchar(13) DEFAULT NULL,
  `tax_state_exceptions` int(2) DEFAULT 0,
  `tax_unemp_state` varchar(13) DEFAULT NULL,
  `tax_work_state` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`emp_number`),
  CONSTRAINT `hs_hr_emp_us_tax_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_us_tax`
--

LOCK TABLES `hs_hr_emp_us_tax` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_us_tax` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_us_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_emp_work_experience`
--

DROP TABLE IF EXISTS `hs_hr_emp_work_experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_emp_work_experience` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `eexp_seqno` decimal(10,0) NOT NULL DEFAULT 0,
  `eexp_employer` varchar(100) DEFAULT NULL,
  `eexp_jobtit` varchar(120) DEFAULT NULL,
  `eexp_from_date` datetime DEFAULT NULL,
  `eexp_to_date` datetime DEFAULT NULL,
  `eexp_comments` varchar(200) DEFAULT NULL,
  `eexp_internal` int(1) DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`eexp_seqno`),
  CONSTRAINT `hs_hr_emp_work_experience_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_emp_work_experience`
--

LOCK TABLES `hs_hr_emp_work_experience` WRITE;
/*!40000 ALTER TABLE `hs_hr_emp_work_experience` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_emp_work_experience` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_employee`
--

DROP TABLE IF EXISTS `hs_hr_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_employee` (
  `emp_number` int(7) NOT NULL DEFAULT 0,
  `employee_id` varchar(50) DEFAULT NULL,
  `emp_lastname` varchar(100) NOT NULL DEFAULT '',
  `emp_firstname` varchar(100) NOT NULL DEFAULT '',
  `emp_middle_name` varchar(100) NOT NULL DEFAULT '',
  `emp_nick_name` varchar(100) DEFAULT '',
  `emp_smoker` smallint(6) DEFAULT 0,
  `ethnic_race_code` varchar(13) DEFAULT NULL,
  `emp_birthday` date DEFAULT NULL,
  `nation_code` int(4) DEFAULT NULL,
  `emp_gender` smallint(6) DEFAULT NULL,
  `emp_marital_status` varchar(20) DEFAULT NULL,
  `emp_ssn_num` varchar(100) CHARACTER SET latin1 DEFAULT '',
  `emp_sin_num` varchar(100) DEFAULT '',
  `emp_other_id` varchar(100) DEFAULT '',
  `emp_dri_lice_num` varchar(100) DEFAULT '',
  `emp_dri_lice_exp_date` date DEFAULT NULL,
  `emp_military_service` varchar(100) DEFAULT '',
  `emp_status` int(13) DEFAULT NULL,
  `job_title_code` int(7) DEFAULT NULL,
  `eeo_cat_code` int(11) DEFAULT NULL,
  `work_station` int(6) DEFAULT NULL,
  `emp_street1` varchar(100) DEFAULT '',
  `emp_street2` varchar(100) DEFAULT '',
  `city_code` varchar(100) DEFAULT '',
  `coun_code` varchar(100) DEFAULT '',
  `provin_code` varchar(100) DEFAULT '',
  `emp_zipcode` varchar(20) DEFAULT NULL,
  `emp_hm_telephone` varchar(50) DEFAULT NULL,
  `emp_mobile` varchar(50) DEFAULT NULL,
  `emp_work_telephone` varchar(50) DEFAULT NULL,
  `emp_work_email` varchar(50) DEFAULT NULL,
  `sal_grd_code` varchar(13) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `emp_oth_email` varchar(50) DEFAULT NULL,
  `termination_id` int(4) DEFAULT NULL,
  `custom1` varchar(250) DEFAULT NULL,
  `custom2` varchar(250) DEFAULT NULL,
  `custom3` varchar(250) DEFAULT NULL,
  `custom4` varchar(250) DEFAULT NULL,
  `custom5` varchar(250) DEFAULT NULL,
  `custom6` varchar(250) DEFAULT NULL,
  `custom7` varchar(250) DEFAULT NULL,
  `custom8` varchar(250) DEFAULT NULL,
  `custom9` varchar(250) DEFAULT NULL,
  `custom10` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`emp_number`),
  KEY `work_station` (`work_station`),
  KEY `nation_code` (`nation_code`),
  KEY `job_title_code` (`job_title_code`),
  KEY `emp_status` (`emp_status`),
  KEY `eeo_cat_code` (`eeo_cat_code`),
  KEY `termination_id` (`termination_id`),
  CONSTRAINT `hs_hr_employee_ibfk_1` FOREIGN KEY (`work_station`) REFERENCES `ohrm_subunit` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hs_hr_employee_ibfk_2` FOREIGN KEY (`nation_code`) REFERENCES `ohrm_nationality` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hs_hr_employee_ibfk_3` FOREIGN KEY (`job_title_code`) REFERENCES `ohrm_job_title` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hs_hr_employee_ibfk_4` FOREIGN KEY (`emp_status`) REFERENCES `ohrm_employment_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hs_hr_employee_ibfk_5` FOREIGN KEY (`eeo_cat_code`) REFERENCES `ohrm_job_category` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hs_hr_employee_ibfk_6` FOREIGN KEY (`termination_id`) REFERENCES `ohrm_emp_termination` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_employee`
--

LOCK TABLES `hs_hr_employee` WRITE;
/*!40000 ALTER TABLE `hs_hr_employee` DISABLE KEYS */;
INSERT INTO `hs_hr_employee` VALUES (1,'0001','Admin','Devi','','',0,NULL,NULL,NULL,NULL,NULL,'','','','',NULL,'',NULL,NULL,NULL,NULL,'','','','','',NULL,NULL,NULL,'','devi@admin.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `hs_hr_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_jobtit_empstat`
--

DROP TABLE IF EXISTS `hs_hr_jobtit_empstat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_jobtit_empstat` (
  `jobtit_code` int(7) NOT NULL,
  `estat_code` int(13) NOT NULL,
  PRIMARY KEY (`jobtit_code`,`estat_code`),
  KEY `estat_code` (`estat_code`),
  CONSTRAINT `hs_hr_jobtit_empstat_ibfk_1` FOREIGN KEY (`jobtit_code`) REFERENCES `ohrm_job_title` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hs_hr_jobtit_empstat_ibfk_2` FOREIGN KEY (`estat_code`) REFERENCES `ohrm_employment_status` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_jobtit_empstat`
--

LOCK TABLES `hs_hr_jobtit_empstat` WRITE;
/*!40000 ALTER TABLE `hs_hr_jobtit_empstat` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_jobtit_empstat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_mailnotifications`
--

DROP TABLE IF EXISTS `hs_hr_mailnotifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_mailnotifications` (
  `user_id` int(20) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `notification_type_id` (`notification_type_id`),
  CONSTRAINT `hs_hr_mailnotifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_mailnotifications`
--

LOCK TABLES `hs_hr_mailnotifications` WRITE;
/*!40000 ALTER TABLE `hs_hr_mailnotifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_mailnotifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_module`
--

DROP TABLE IF EXISTS `hs_hr_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_module` (
  `mod_id` varchar(36) NOT NULL DEFAULT '',
  `name` varchar(45) DEFAULT NULL,
  `owner` varchar(45) DEFAULT NULL,
  `owner_email` varchar(100) DEFAULT NULL,
  `version` varchar(36) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_module`
--

LOCK TABLES `hs_hr_module` WRITE;
/*!40000 ALTER TABLE `hs_hr_module` DISABLE KEYS */;
INSERT INTO `hs_hr_module` VALUES ('MOD001','Admin','OrangeHRM','info@orangehrm.com','VER001','HR Admin'),('MOD002','PIM','OrangeHRM','info@orangehrm.com','VER001','HR Functions'),('MOD004','Report','OrangeHRM','info@orangehrm.com','VER001','Reporting'),('MOD005','Leave','OrangeHRM','info@orangehrm.com','VER001','Leave Tracking'),('MOD006','Time','OrangeHRM','info@orangehrm.com','VER001','Time Tracking'),('MOD007','Benefits','OrangeHRM','info@orangehrm.com','VER001','Benefits Tracking'),('MOD008','Recruitment','OrangeHRM','info@orangehrm.com','VER001','Recruitment'),('MOD009','Performance','OrangeHRM','info@orangehrm.com','VER001','Performance');
/*!40000 ALTER TABLE `hs_hr_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_pay_period`
--

DROP TABLE IF EXISTS `hs_hr_pay_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_pay_period` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `close_date` date NOT NULL,
  `check_date` date NOT NULL,
  `timesheet_aproval_due_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_pay_period`
--

LOCK TABLES `hs_hr_pay_period` WRITE;
/*!40000 ALTER TABLE `hs_hr_pay_period` DISABLE KEYS */;
/*!40000 ALTER TABLE `hs_hr_pay_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_payperiod`
--

DROP TABLE IF EXISTS `hs_hr_payperiod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_payperiod` (
  `payperiod_code` varchar(13) NOT NULL DEFAULT '',
  `payperiod_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`payperiod_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_payperiod`
--

LOCK TABLES `hs_hr_payperiod` WRITE;
/*!40000 ALTER TABLE `hs_hr_payperiod` DISABLE KEYS */;
INSERT INTO `hs_hr_payperiod` VALUES ('1','Weekly'),('2','Bi Weekly'),('3','Semi Monthly'),('4','Monthly'),('5','Monthly on first pay of month.'),('6','Hourly');
/*!40000 ALTER TABLE `hs_hr_payperiod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_province`
--

DROP TABLE IF EXISTS `hs_hr_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_province` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province_name` varchar(40) NOT NULL DEFAULT '',
  `province_code` char(2) NOT NULL DEFAULT '',
  `cou_code` char(2) NOT NULL DEFAULT 'us',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_province`
--

LOCK TABLES `hs_hr_province` WRITE;
/*!40000 ALTER TABLE `hs_hr_province` DISABLE KEYS */;
INSERT INTO `hs_hr_province` VALUES (1,'Alaska','AK','US'),(2,'Alabama','AL','US'),(3,'American Samoa','AS','US'),(4,'Arizona','AZ','US'),(5,'Arkansas','AR','US'),(6,'California','CA','US'),(7,'Colorado','CO','US'),(8,'Connecticut','CT','US'),(9,'Delaware','DE','US'),(10,'District of Columbia','DC','US'),(11,'Federated States of Micronesia','FM','US'),(12,'Florida','FL','US'),(13,'Georgia','GA','US'),(14,'Guam','GU','US'),(15,'Hawaii','HI','US'),(16,'Idaho','ID','US'),(17,'Illinois','IL','US'),(18,'Indiana','IN','US'),(19,'Iowa','IA','US'),(20,'Kansas','KS','US'),(21,'Kentucky','KY','US'),(22,'Louisiana','LA','US'),(23,'Maine','ME','US'),(24,'Marshall Islands','MH','US'),(25,'Maryland','MD','US'),(26,'Massachusetts','MA','US'),(27,'Michigan','MI','US'),(28,'Minnesota','MN','US'),(29,'Mississippi','MS','US'),(30,'Missouri','MO','US'),(31,'Montana','MT','US'),(32,'Nebraska','NE','US'),(33,'Nevada','NV','US'),(34,'New Hampshire','NH','US'),(35,'New Jersey','NJ','US'),(36,'New Mexico','NM','US'),(37,'New York','NY','US'),(38,'North Carolina','NC','US'),(39,'North Dakota','ND','US'),(40,'Northern Mariana Islands','MP','US'),(41,'Ohio','OH','US'),(42,'Oklahoma','OK','US'),(43,'Oregon','OR','US'),(44,'Palau','PW','US'),(45,'Pennsylvania','PA','US'),(46,'Puerto Rico','PR','US'),(47,'Rhode Island','RI','US'),(48,'South Carolina','SC','US'),(49,'South Dakota','SD','US'),(50,'Tennessee','TN','US'),(51,'Texas','TX','US'),(52,'Utah','UT','US'),(53,'Vermont','VT','US'),(54,'Virgin Islands','VI','US'),(55,'Virginia','VA','US'),(56,'Washington','WA','US'),(57,'West Virginia','WV','US'),(58,'Wisconsin','WI','US'),(59,'Wyoming','WY','US'),(60,'Armed Forces Africa','AE','US'),(61,'Armed Forces Americas (except Canada)','AA','US'),(62,'Armed Forces Canada','AE','US'),(63,'Armed Forces Europe','AE','US'),(64,'Armed Forces Middle East','AE','US'),(65,'Armed Forces Pacific','AP','US');
/*!40000 ALTER TABLE `hs_hr_province` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hs_hr_unique_id`
--

DROP TABLE IF EXISTS `hs_hr_unique_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hs_hr_unique_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_id` int(10) unsigned NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_field` (`table_name`,`field_name`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hs_hr_unique_id`
--

LOCK TABLES `hs_hr_unique_id` WRITE;
/*!40000 ALTER TABLE `hs_hr_unique_id` DISABLE KEYS */;
INSERT INTO `hs_hr_unique_id` VALUES (1,1,'hs_hr_employee','emp_number'),(2,9,'hs_hr_module','mod_id'),(3,0,'hs_hr_leave','leave_id'),(4,0,'hs_hr_leavetype','leave_type_id'),(5,0,'hs_hr_leave_requests','leave_request_id'),(6,0,'hs_hr_custom_export','export_id'),(7,0,'hs_hr_custom_import','import_id'),(8,0,'hs_hr_pay_period','id'),(9,0,'hs_hr_kpi','id'),(10,0,'hs_hr_performance_review','id'),(11,2,'ohrm_emp_reporting_method','reporting_method_id'),(12,0,'ohrm_timesheet','timesheet_id'),(13,0,'ohrm_timesheet_action_log','timesheet_action_log_id'),(14,0,'ohrm_timesheet_item','timesheet_item_id'),(15,0,'ohrm_attendance_record','id'),(16,0,'ohrm_job_vacancy','id'),(17,0,'ohrm_job_candidate','id'),(18,106,'ohrm_workflow_state_machine','id'),(19,0,'ohrm_job_candidate_attachment','id'),(20,0,'ohrm_job_vacancy_attachment','id'),(21,0,'ohrm_job_candidate_vacancy','id'),(22,0,'ohrm_job_candidate_history','id'),(23,0,'ohrm_job_interview','id');
/*!40000 ALTER TABLE `hs_hr_unique_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_advanced_report`
--

DROP TABLE IF EXISTS `ohrm_advanced_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_advanced_report` (
  `id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `definition` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_advanced_report`
--

LOCK TABLES `ohrm_advanced_report` WRITE;
/*!40000 ALTER TABLE `ohrm_advanced_report` DISABLE KEYS */;
INSERT INTO `ohrm_advanced_report` VALUES (1,'Leave Entitlements and Usage Report','\n<report>\n    <settings>\n        <csv>\n            <include_group_header>1</include_group_header>\n            <include_header>1</include_header>\n        </csv>\n    </settings>\n<filter_fields>\n	<input_field type=\"text\" name=\"empNumber\" label=\"Employee Number\"></input_field>\n	<input_field type=\"text\" name=\"fromDate\" label=\"From\"></input_field>\n        <input_field type=\"text\" name=\"toDate\" label=\"To\"></input_field>\n        <input_field type=\"text\" name=\"asOfDate\" label=\"AsOf\"></input_field>\n</filter_fields> \n\n<sub_report type=\"sql\" name=\"mainTable\">       \n    <query>FROM ohrm_leave_type WHERE (deleted = 0) OR (SELECT count(l.id) FROM ohrm_leave l WHERE l.status = 3 AND l.leave_type_id = ohrm_leave_type.id) > 0 ORDER BY ohrm_leave_type.id</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n        <display_group name=\"leavetype\" type=\"one\" display=\"true\">\n            <group_header></group_header>\n            <fields>\n                <field display=\"false\">\n                    <field_name>ohrm_leave_type.id</field_name>\n                    <field_alias>leaveTypeId</field_alias>\n                    <display_name>Leave Type ID</display_name>\n                    <width>1</width>	\n                </field>   \n                <field display=\"false\">\n                    <field_name>ohrm_leave_type.exclude_in_reports_if_no_entitlement</field_name>\n                    <field_alias>exclude_if_no_entitlement</field_alias>\n                    <display_name>Exclude</display_name>\n                    <width>1</width>	\n                </field>  \n                <field display=\"false\">\n                    <field_name>ohrm_leave_type.deleted</field_name>\n                    <field_alias>leave_type_deleted</field_alias>\n                    <display_name>Leave Type Deleted</display_name>\n                    <width>1</width>	\n                </field>  \n                <field display=\"true\">\n                    <field_name>ohrm_leave_type.name</field_name>\n                    <field_alias>leaveType</field_alias>\n                    <display_name>Leave Type</display_name>\n                    <width>160</width>	\n                </field>s                                                                                                     \n            </fields>\n        </display_group>\n    </display_groups> \n</sub_report>\n\n<sub_report type=\"sql\" name=\"entitlementsTotal\">\n                    <query>\n\nFROM (\nSELECT ohrm_leave_entitlement.id as id, \n       ohrm_leave_entitlement.leave_type_id as leave_type_id,\n       ohrm_leave_entitlement.no_of_days as no_of_days,\n       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,\n       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken\n       \nFROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON\n    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id\n    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND \n    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )\n\nWHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.emp_number,empNumber} AND \n    $X{IN,ohrm_leave_entitlement.leave_type_id,leaveTypeId} AND\n    (\n      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR\n      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR \n      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) \n    )\n    \nGROUP BY ohrm_leave_entitlement.id\n) AS A\n\nGROUP BY A.leave_type_id\nORDER BY A.leave_type_id\n\n</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n            <display_group name=\"g2\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>A.leave_type_id</field_name>\n                        <field_alias>leaveTypeId</field_alias>\n                        <display_name>Leave Type ID</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(A.no_of_days) - sum(A.scheduled) - sum(A.taken)</field_name>\n                        <field_alias>entitlement_total</field_alias>\n                        <display_name>Leave Entitlements (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveEntitlements?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n</sub_report>\n\n<sub_report type=\"sql\" name=\"pendingQuery\">\n<query>\nFROM ohrm_leave_type LEFT JOIN \nohrm_leave ON ohrm_leave_type.id = ohrm_leave.leave_type_id AND\n$X{=,ohrm_leave.emp_number,empNumber} AND\nohrm_leave.status = 1 AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nWHERE\nohrm_leave_type.deleted = 0 AND\n$X{IN,ohrm_leave_type.id,leaveTypeId}\n\nGROUP BY ohrm_leave_type.id\nORDER BY ohrm_leave_type.id\n</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n            <display_group name=\"g6\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave_type.id</field_name>\n                        <field_alias>leaveTypeId</field_alias>\n                        <display_name>Leave Type ID</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>pending</field_alias>\n                        <display_name>Leave Pending Approval (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=1&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n    </sub_report>\n\n<sub_report type=\"sql\" name=\"scheduledQuery\">\n<query>\nFROM ohrm_leave_type LEFT JOIN \nohrm_leave ON ohrm_leave_type.id = ohrm_leave.leave_type_id AND\n$X{=,ohrm_leave.emp_number,empNumber} AND\nohrm_leave.status = 2 AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nWHERE\nohrm_leave_type.deleted = 0 AND\n$X{IN,ohrm_leave_type.id,leaveTypeId}\n\nGROUP BY ohrm_leave_type.id\nORDER BY ohrm_leave_type.id\n</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n            <display_group name=\"g5\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave_type.id</field_name>\n                        <field_alias>leaveTypeId</field_alias>\n                        <display_name>Leave Type ID</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>scheduled</field_alias>\n                        <display_name>Leave Scheduled (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=2&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n    </sub_report>\n\n<sub_report type=\"sql\" name=\"takenQuery\">\n<query>\nFROM ohrm_leave WHERE $X{=,emp_number,empNumber} AND\nstatus = 3 AND\n$X{IN,ohrm_leave.leave_type_id,leaveTypeId} AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nGROUP BY leave_type_id\nORDER BY ohrm_leave.leave_type_id\n</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n            <display_group name=\"g4\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave.leave_type_id</field_name>\n                        <field_alias>leaveTypeId</field_alias>\n                        <display_name>Leave Type ID</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>taken</field_alias>\n                        <display_name>Leave Taken (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=3&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n    </sub_report>\n\n<sub_report type=\"sql\" name=\"unused\">       \n    <query>FROM ohrm_leave_type WHERE deleted = 0 AND $X{IN,ohrm_leave_type.id,leaveTypeId} ORDER BY ohrm_leave_type.id</query>\n    <id_field>leaveTypeId</id_field>\n    <display_groups>\n        <display_group name=\"unused\" type=\"one\" display=\"true\">\n            <group_header></group_header>\n            <fields>\n                <field display=\"false\">\n                    <field_name>ohrm_leave_type.id</field_name>\n                    <field_alias>leaveTypeId</field_alias>\n                    <display_name>Leave Type ID</display_name>\n                    <width>1</width>	\n                </field>   \n                <field display=\"true\">\n                    <field_name>ohrm_leave_type.name</field_name>\n                    <field_alias>unused</field_alias>\n                    <display_name>Leave Balance (Days)</display_name>\n                    <width>160</width>	\n                    <align>right</align>\n                </field>                                                                                                     \n            </fields>\n        </display_group>\n    </display_groups> \n</sub_report>\n\n\n    <join>             \n        <join_by sub_report=\"mainTable\" id=\"leaveTypeId\"></join_by>              \n        <join_by sub_report=\"entitlementsTotal\" id=\"leaveTypeId\"></join_by> \n        <join_by sub_report=\"pendingQuery\" id=\"leaveTypeId\"></join_by>  \n        <join_by sub_report=\"scheduledQuery\" id=\"leaveTypeId\"></join_by>  \n        <join_by sub_report=\"takenQuery\" id=\"leaveTypeId\"></join_by>  \n        <join_by sub_report=\"unused\" id=\"leaveTypeId\"></join_by>  \n\n    </join>\n    <page_limit>100</page_limit>        \n</report>'),(2,'Leave Entitlements and Usage Report','\n<report>\n    <settings>\n        <csv>\n            <include_group_header>1</include_group_header>\n            <include_header>1</include_header>\n        </csv>\n    </settings>\n<filter_fields>\n	<input_field type=\"text\" name=\"leaveType\" label=\"Leave Type\"></input_field>\n	<input_field type=\"text\" name=\"fromDate\" label=\"From\"></input_field>\n        <input_field type=\"text\" name=\"toDate\" label=\"To\"></input_field>\n        <input_field type=\"text\" name=\"asOfDate\" label=\"AsOf\"></input_field>\n        <input_field type=\"text\" name=\"emp_numbers\" label=\"employees\"></input_field>\n        <input_field type=\"text\" name=\"job_title\" label=\"Job Title\"></input_field>\n        <input_field type=\"text\" name=\"location\" label=\"Location\"></input_field>\n        <input_field type=\"text\" name=\"sub_unit\" label=\"Sub Unit\"></input_field>\n        <input_field type=\"text\" name=\"terminated\" label=\"Terminated\"></input_field>\n</filter_fields> \n\n<sub_report type=\"sql\" name=\"mainTable\">       \n    <query>FROM hs_hr_employee \n    LEFT JOIN hs_hr_emp_locations ON hs_hr_employee.emp_number = hs_hr_emp_locations.emp_number\n    WHERE $X{IN,hs_hr_employee.emp_number,emp_numbers} \n    AND $X{=,hs_hr_employee.job_title_code,job_title}\n    AND $X{IN,hs_hr_employee.work_station,sub_unit}\n    AND $X{IN,hs_hr_emp_locations.location_id,location}\n    AND $X{IS NULL,hs_hr_employee.termination_id,terminated}\n    ORDER BY hs_hr_employee.emp_lastname</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n        <display_group name=\"personalDetails\" type=\"one\" display=\"true\">\n            <group_header></group_header>\n            <fields>\n                <field display=\"false\">\n                    <field_name>hs_hr_employee.emp_number</field_name>\n                    <field_alias>empNumber</field_alias>\n                    <display_name>Employee Number</display_name>\n                    <width>1</width>	\n                </field>                \n                <field display=\"false\">\n                    <field_name>hs_hr_employee.termination_id</field_name>\n                    <field_alias>termination_id</field_alias>\n                    <display_name>Termination ID</display_name>\n                    <width>1</width>	\n                </field>   \n                <field display=\"true\">\n                    <field_name>CONCAT(hs_hr_employee.emp_firstname, \' \', hs_hr_employee.emp_lastname)</field_name>\n                    <field_alias>employeeName</field_alias>\n                    <display_name>Employee</display_name>\n                    <width>150</width>\n                </field>                                                                                               \n            </fields>\n        </display_group>\n    </display_groups> \n</sub_report>\n\n<sub_report type=\"sql\" name=\"entitlementsTotal\">\n                    <query>\n\nFROM (\nSELECT ohrm_leave_entitlement.id as id, \n       ohrm_leave_entitlement.emp_number as emp_number,\n       ohrm_leave_entitlement.no_of_days as no_of_days,\n       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,\n       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken\n       \nFROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON\n    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id\n    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND \n    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )\n\nWHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.leave_type_id,leaveType}\n    AND $X{IN,ohrm_leave_entitlement.emp_number,empNumber} AND\n    (\n      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR\n      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR \n      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) \n    )\n    \nGROUP BY ohrm_leave_entitlement.id\n) AS A\n\nGROUP BY A.emp_number\nORDER BY A.emp_number\n\n</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n            <display_group name=\"g2\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>A.emp_number</field_name>\n                        <field_alias>empNumber</field_alias>\n                        <display_name>Emp Number</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(A.no_of_days) - sum(A.scheduled) - sum(A.taken)</field_name>\n                        <field_alias>entitlement_total</field_alias>\n                        <display_name>Leave Entitlements (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveEntitlements?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n</sub_report>\n\n<sub_report type=\"sql\" name=\"pendingQuery\">\n<query>\nFROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND\nstatus = 1 AND\n$X{IN,ohrm_leave.emp_number,empNumber} AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nGROUP BY emp_number\nORDER BY ohrm_leave.emp_number\n</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n            <display_group name=\"g6\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave.emp_number</field_name>\n                        <field_alias>empNumber</field_alias>\n                        <display_name>Emp Number</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>pending</field_alias>\n                        <display_name>Leave Pending Approval (Days)</display_name>\n                        <width>121</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=1&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n</sub_report>\n\n\n<sub_report type=\"sql\" name=\"scheduledQuery\">\n<query>\nFROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND\nstatus = 2 AND\n$X{IN,ohrm_leave.emp_number,empNumber} AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nGROUP BY emp_number\nORDER BY ohrm_leave.emp_number\n</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n            <display_group name=\"g5\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave.emp_number</field_name>\n                        <field_alias>empNumber</field_alias>\n                        <display_name>Emp Number</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>scheduled</field_alias>\n                        <display_name>Leave Scheduled (Days)</display_name>\n                        <width>121</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=2&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n</sub_report>\n\n<sub_report type=\"sql\" name=\"takenQuery\">\n<query>\nFROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND\nstatus = 3 AND\n$X{IN,ohrm_leave.emp_number,empNumber} AND\n$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}\nGROUP BY emp_number\nORDER BY ohrm_leave.emp_number\n</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n            <display_group name=\"g4\" type=\"one\" display=\"true\">\n                <group_header></group_header>\n                <fields>\n                    <field display=\"false\">\n                        <field_name>ohrm_leave.emp_number</field_name>\n                        <field_alias>empNumber</field_alias>\n                        <display_name>Emp Number</display_name>\n                        <width>1</width>\n                    </field>                                \n                    <field display=\"true\">\n                        <field_name>sum(length_days)</field_name>\n                        <field_alias>taken</field_alias>\n                        <display_name>Leave Taken (Days)</display_name>\n                        <width>120</width>\n                        <align>right</align>\n                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=3&amp;stddate=1</link>\n                    </field>                                \n                </fields>\n            </display_group>\n    </display_groups>\n</sub_report>\n<sub_report type=\"sql\" name=\"unused\">       \n    <query>FROM hs_hr_employee WHERE $X{IN,hs_hr_employee.emp_number,empNumber} ORDER BY hs_hr_employee.emp_number</query>\n    <id_field>empNumber</id_field>\n    <display_groups>\n        <display_group name=\"unused\" type=\"one\" display=\"true\">\n            <group_header></group_header>\n            <fields>    \n                <field display=\"false\">\n                    <field_name>hs_hr_employee.emp_number</field_name>\n                    <field_alias>empNumber</field_alias>\n                    <display_name>Employee Number</display_name>\n                    <width>1</width>	\n                </field>                \n                <field display=\"true\">\n                    <field_name>hs_hr_employee.emp_firstname</field_name>\n                    <field_alias>unused</field_alias>\n                    <display_name>Leave Balance (Days)</display_name>\n                    <width>150</width>\n                    <align>right</align>\n                </field> \n                                                                                               \n            </fields>\n        </display_group>\n    </display_groups> \n</sub_report>\n    <join>             \n        <join_by sub_report=\"mainTable\" id=\"empNumber\"></join_by>            \n        <join_by sub_report=\"entitlementsTotal\" id=\"empNumber\"></join_by> \n        <join_by sub_report=\"pendingQuery\" id=\"empNumber\"></join_by>\n        <join_by sub_report=\"scheduledQuery\" id=\"empNumber\"></join_by>\n        <join_by sub_report=\"takenQuery\" id=\"empNumber\"></join_by> \n        <join_by sub_report=\"unused\" id=\"empNumber\"></join_by>  \n    </join>\n    <page_limit>20</page_limit>       \n</report>');
/*!40000 ALTER TABLE `ohrm_advanced_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_attendance_record`
--

DROP TABLE IF EXISTS `ohrm_attendance_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_attendance_record` (
  `id` bigint(20) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  `punch_in_utc_time` datetime DEFAULT NULL,
  `punch_in_note` varchar(255) DEFAULT NULL,
  `punch_in_time_offset` varchar(255) DEFAULT NULL,
  `punch_in_user_time` datetime DEFAULT NULL,
  `punch_out_utc_time` datetime DEFAULT NULL,
  `punch_out_note` varchar(255) DEFAULT NULL,
  `punch_out_time_offset` varchar(255) DEFAULT NULL,
  `punch_out_user_time` datetime DEFAULT NULL,
  `state` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_id_state` (`employee_id`,`state`),
  KEY `emp_id_time` (`employee_id`,`punch_in_utc_time`,`punch_out_utc_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_attendance_record`
--

LOCK TABLES `ohrm_attendance_record` WRITE;
/*!40000 ALTER TABLE `ohrm_attendance_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_attendance_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_auth_provider_extra_details`
--

DROP TABLE IF EXISTS `ohrm_auth_provider_extra_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_auth_provider_extra_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) NOT NULL,
  `provider_type` int(11) DEFAULT NULL,
  `client_id` text DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `developer_key` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `ohrm_auth_provider_extra_details_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `ohrm_openid_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_auth_provider_extra_details`
--

LOCK TABLES `ohrm_auth_provider_extra_details` WRITE;
/*!40000 ALTER TABLE `ohrm_auth_provider_extra_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_auth_provider_extra_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_available_group_field`
--

DROP TABLE IF EXISTS `ohrm_available_group_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_available_group_field` (
  `report_group_id` bigint(20) NOT NULL,
  `group_field_id` bigint(20) NOT NULL,
  PRIMARY KEY (`report_group_id`,`group_field_id`),
  KEY `report_group_id` (`report_group_id`),
  KEY `group_field_id` (`group_field_id`),
  CONSTRAINT `ohrm_available_group_field_ibfk_1` FOREIGN KEY (`group_field_id`) REFERENCES `ohrm_group_field` (`group_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_available_group_field`
--

LOCK TABLES `ohrm_available_group_field` WRITE;
/*!40000 ALTER TABLE `ohrm_available_group_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_available_group_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_beacon_notification`
--

DROP TABLE IF EXISTS `ohrm_beacon_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_beacon_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `expiry_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `definition` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_beacon_notification`
--

LOCK TABLES `ohrm_beacon_notification` WRITE;
/*!40000 ALTER TABLE `ohrm_beacon_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_beacon_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_composite_display_field`
--

DROP TABLE IF EXISTS `ohrm_composite_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_composite_display_field` (
  `composite_display_field_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_alias` varchar(255) DEFAULT NULL,
  `is_sortable` varchar(10) NOT NULL,
  `sort_order` varchar(255) DEFAULT NULL,
  `sort_field` varchar(255) DEFAULT NULL,
  `element_type` varchar(255) NOT NULL,
  `element_property` varchar(1000) NOT NULL,
  `width` varchar(255) NOT NULL,
  `is_exportable` varchar(10) DEFAULT NULL,
  `text_alignment_style` varchar(20) DEFAULT NULL,
  `is_value_list` tinyint(1) NOT NULL DEFAULT 0,
  `display_field_group_id` int(10) unsigned DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `is_meta` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`composite_display_field_id`),
  KEY `report_group_id` (`report_group_id`),
  KEY `display_field_group_id` (`display_field_group_id`),
  CONSTRAINT `ohrm_composite_display_field_ibfk_1` FOREIGN KEY (`report_group_id`) REFERENCES `ohrm_report_group` (`report_group_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_composite_display_field_ibfk_2` FOREIGN KEY (`display_field_group_id`) REFERENCES `ohrm_display_field_group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_composite_display_field`
--

LOCK TABLES `ohrm_composite_display_field` WRITE;
/*!40000 ALTER TABLE `ohrm_composite_display_field` DISABLE KEYS */;
INSERT INTO `ohrm_composite_display_field` VALUES (1,1,'IF(hs_hr_employee.termination_id IS NULL, CONCAT(hs_hr_employee.emp_firstname, \" \" ,hs_hr_employee.emp_lastname), CONCAT(hs_hr_employee.emp_firstname, \" \" ,hs_hr_employee.emp_lastname, \" (Past Employee)\"))','Employee Name','employeeName','false',NULL,NULL,'label','<xml><getter>employeeName</getter></xml>','300','0',NULL,0,NULL,'Deleted Employee',0,0),(2,1,'CONCAT(ohrm_customer.name, \" - \" ,ohrm_project.name)','Project Name','projectname','false',NULL,NULL,'label','<xml><getter>projectname</getter></xml>','300','0',NULL,0,NULL,NULL,0,0);
/*!40000 ALTER TABLE `ohrm_composite_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_customer`
--

DROP TABLE IF EXISTS `ohrm_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_customer`
--

LOCK TABLES `ohrm_customer` WRITE;
/*!40000 ALTER TABLE `ohrm_customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_data_group`
--

DROP TABLE IF EXISTS `ohrm_data_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_data_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `can_read` tinyint(4) DEFAULT NULL,
  `can_create` tinyint(4) DEFAULT NULL,
  `can_update` tinyint(4) DEFAULT NULL,
  `can_delete` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_data_group`
--

LOCK TABLES `ohrm_data_group` WRITE;
/*!40000 ALTER TABLE `ohrm_data_group` DISABLE KEYS */;
INSERT INTO `ohrm_data_group` VALUES (1,'personal_information','PIM - Personal Details',1,NULL,1,NULL),(2,'personal_attachment','PIM - Personal Details - Attachments',1,1,1,1),(3,'personal_custom_fields','PIM - Personal Details - Custom Fields',1,NULL,1,NULL),(4,'contact_details','PIM - Contact Details',1,NULL,1,NULL),(5,'contact_attachment','PIM - Contact Details - Attachments',1,1,1,1),(6,'contact_custom_fields','PIM - Contact Details - Custom Fields',1,NULL,1,NULL),(7,'emergency_contacts','PIM - Emergency Contacts',1,1,1,1),(8,'emergency_attachment','PIM - Emergency Contacts - Attachments',1,1,1,1),(9,'emergency_custom_fields','PIM - Emergency Contacts - Custom Fields',1,NULL,1,NULL),(10,'dependents','PIM - Dependents',1,1,1,1),(11,'dependents_attachment','PIM - Dependents - Attachments',1,1,1,1),(12,'dependents_custom_fields','PIM - Dependents - Custom Fields',1,NULL,1,NULL),(13,'immigration','PIM - Immigration',1,1,1,1),(14,'immigration_attachment','PIM - Immigration - Attachments',1,1,1,1),(15,'immigration_custom_fields','PIM - Immigration - Custom Fields',1,NULL,1,NULL),(16,'job_details','PIM - Job',1,NULL,1,NULL),(17,'job_attachment','PIM - Job - Attachments',1,1,1,1),(18,'job_custom_fields','PIM - Job - Custom Fields',1,NULL,1,NULL),(19,'salary_details','PIM - Salary',1,1,1,1),(20,'salary_attachment','PIM - Salary - Attachments',1,1,1,1),(21,'salary_custom_fields','PIM - Salary - Custom Fields',1,NULL,1,NULL),(22,'tax_exemptions','PIM - Tax Exemptions',1,NULL,1,NULL),(23,'tax_attachment','PIM - Tax Exemptions - Attachments',1,1,1,1),(24,'tax_custom_fields','PIM - Tax Exemptions - Custom Fields',1,NULL,1,NULL),(25,'supervisor','PIM - Employee Supervisors',1,1,1,1),(26,'subordinates','PIM - Employee Subordinates',1,1,1,1),(27,'report-to_attachment','PIM - Employee Supervisors/Subordinates - Attachment',1,1,1,1),(28,'report-to_custom_fields','PIM - Employee Supervisors/Subordinates - Custom Fields',1,NULL,1,NULL),(29,'qualification_work','PIM - Qualifications - Work Experience',1,1,1,1),(30,'qualification_education','PIM - Qualifications - Education',1,1,1,1),(31,'qualification_skills','PIM - Qualifications - Skills',1,1,1,1),(32,'qualification_languages','PIM - Qualifications - Languages',1,1,1,1),(33,'qualification_license','PIM - Qualifications - License',1,1,1,1),(34,'qualifications_attachment','PIM - Qualifications - Attachments',1,1,1,1),(35,'qualifications_custom_fields','PIM - Qualifications - Custom Fields',1,NULL,1,NULL),(36,'membership','PIM - Membership',1,1,1,1),(37,'membership_attachment','PIM - Membership - Attachments',1,1,1,1),(38,'membership_custom_fields','PIM - Membership - Custom Fields',1,NULL,1,NULL),(39,'photograph','PIM - Employee Photograph',1,NULL,1,1),(40,'leave_entitlements','Leave - Leave Entitlements',1,1,1,1),(41,'leave_entitlements_usage_report','Leave - Leave Entitlements and Usage Report',1,NULL,NULL,NULL),(42,'job_titles','Admin - Job Titles',1,1,1,1),(43,'pay_grades','Admin - Pay Grades',1,1,1,1),(44,'time_customers','Time - Project Info - Customers',1,1,1,1),(45,'time_projects','Time - Project Info - Projects',1,1,1,1),(46,'pim_reports','PIM - Reports',1,1,1,1),(47,'attendance_configuration','Time - Attendance Configuration',1,0,1,0),(48,'attendance_records','Time - Attendance Records',1,0,0,0),(49,'time_project_reports','Time - Project Reports',1,0,0,0),(50,'time_employee_reports','Time - Employee Reports',1,0,0,0),(51,'attendance_summary','Time - Attendance Summary',1,0,0,0),(52,'leave_period','Leave - Leave Period',1,0,1,0),(53,'leave_types','Leave - Leave Types',1,1,1,1),(54,'work_week','Leave - Work Week',1,0,1,0),(55,'holidays','Leave - Holidays',1,1,1,1),(56,'recruitment_vacancies','Recruitment - Vacancies',1,1,1,1),(57,'recruitment_candidates','Recruitment - Candidates',1,1,1,1),(58,'time_employee_timesheets','Time - Employee Timesheets',1,0,0,0),(59,'leave_list','Leave - Leave List',1,0,0,0),(60,'leave_list_comments','Leave - Leave List - Comments',0,1,0,0);
/*!40000 ALTER TABLE `ohrm_data_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_data_group_screen`
--

DROP TABLE IF EXISTS `ohrm_data_group_screen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_data_group_screen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_group_id` int(11) DEFAULT NULL,
  `screen_id` int(11) DEFAULT NULL,
  `permission` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_group_id` (`data_group_id`),
  KEY `screen_id` (`screen_id`),
  CONSTRAINT `ohrm_data_group_screen_ibfk_1` FOREIGN KEY (`data_group_id`) REFERENCES `ohrm_data_group` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_data_group_screen_ibfk_2` FOREIGN KEY (`screen_id`) REFERENCES `ohrm_screen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_data_group_screen`
--

LOCK TABLES `ohrm_data_group_screen` WRITE;
/*!40000 ALTER TABLE `ohrm_data_group_screen` DISABLE KEYS */;
INSERT INTO `ohrm_data_group_screen` VALUES (1,40,69,1),(2,40,72,2),(3,40,73,3),(4,40,71,4),(5,41,78,1),(6,42,23,1),(7,42,80,1),(8,42,80,2),(9,42,80,3),(10,42,81,4),(11,43,24,1),(12,43,82,1),(13,43,82,2),(14,43,82,3),(15,43,83,4),(16,43,84,3),(17,43,85,3),(18,42,74,1),(19,43,74,1),(20,44,36,1),(21,44,86,2),(22,44,86,3),(23,44,87,4),(24,45,37,1),(25,45,88,1),(26,45,88,2),(27,45,88,3),(28,45,89,4),(29,45,90,2),(30,45,90,3),(31,45,91,2),(32,45,91,3),(33,46,45,1),(34,46,45,4),(35,46,92,2),(36,46,92,3),(37,46,93,1),(38,47,56,1),(39,47,56,3),(40,48,55,1),(41,49,57,1),(42,49,102,1),(43,50,58,1),(44,51,59,1),(45,51,101,1),(46,52,47,1),(47,52,47,3),(48,53,7,1),(49,53,8,1),(50,53,8,2),(51,53,8,3),(52,53,9,2),(53,53,10,4),(54,54,14,1),(55,54,14,3),(56,55,11,1),(57,55,12,2),(58,55,12,3),(59,55,13,4),(60,56,61,1),(61,56,94,1),(62,56,94,2),(63,56,94,3),(64,56,95,4),(65,57,60,1),(66,57,96,1),(67,57,96,2),(68,57,96,3),(69,57,97,4),(70,56,76,1),(71,57,76,1),(72,58,52,1),(73,59,16,1),(74,59,98,1),(75,59,99,1);
/*!40000 ALTER TABLE `ohrm_data_group_screen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_datapoint`
--

DROP TABLE IF EXISTS `ohrm_datapoint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_datapoint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `datapoint_type_id` int(11) NOT NULL,
  `definition` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datapoint_type_id` (`datapoint_type_id`),
  CONSTRAINT `ohrm_datapoint_ibfk_1` FOREIGN KEY (`datapoint_type_id`) REFERENCES `ohrm_datapoint_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_datapoint`
--

LOCK TABLES `ohrm_datapoint` WRITE;
/*!40000 ALTER TABLE `ohrm_datapoint` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_datapoint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_datapoint_type`
--

DROP TABLE IF EXISTS `ohrm_datapoint_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_datapoint_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `action_class` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_datapoint_type`
--

LOCK TABLES `ohrm_datapoint_type` WRITE;
/*!40000 ALTER TABLE `ohrm_datapoint_type` DISABLE KEYS */;
INSERT INTO `ohrm_datapoint_type` VALUES (1,'config','configDatapointProcessor'),(2,'count','countDatapointProcessor'),(3,'session','sessionDatapointProcessor'),(4,'organization','OrganizationDataProcessor');
/*!40000 ALTER TABLE `ohrm_datapoint_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_display_field`
--

DROP TABLE IF EXISTS `ohrm_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_display_field` (
  `display_field_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_alias` varchar(255) DEFAULT NULL,
  `is_sortable` varchar(10) NOT NULL,
  `sort_order` varchar(255) DEFAULT NULL,
  `sort_field` varchar(255) DEFAULT NULL,
  `element_type` varchar(255) NOT NULL,
  `element_property` varchar(1000) NOT NULL,
  `width` varchar(255) NOT NULL,
  `is_exportable` varchar(10) DEFAULT NULL,
  `text_alignment_style` varchar(20) DEFAULT NULL,
  `is_value_list` tinyint(1) NOT NULL DEFAULT 0,
  `display_field_group_id` int(10) unsigned DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `is_meta` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`display_field_id`),
  KEY `report_group_id` (`report_group_id`),
  KEY `display_field_group_id` (`display_field_group_id`),
  CONSTRAINT `ohrm_display_field_ibfk_1` FOREIGN KEY (`report_group_id`) REFERENCES `ohrm_report_group` (`report_group_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_display_field_ibfk_2` FOREIGN KEY (`display_field_group_id`) REFERENCES `ohrm_display_field_group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_display_field`
--

LOCK TABLES `ohrm_display_field` WRITE;
/*!40000 ALTER TABLE `ohrm_display_field` DISABLE KEYS */;
INSERT INTO `ohrm_display_field` VALUES (1,1,'ohrm_project.name','Project Name','projectname','false',NULL,NULL,'label','<xml><getter>projectname</getter></xml>','200','0',NULL,0,NULL,NULL,0,0),(2,1,'ohrm_project_activity.name','Activity Name','activityname','false',NULL,NULL,'link','<xml><labelGetter>activityname</labelGetter><placeholderGetters><id>activity_id</id><total>totalduration</total><projectId>projectId</projectId><from>fromDate</from><to>toDate</to><approved>onlyIncludeApprovedTimesheets</approved></placeholderGetters><urlPattern>../../displayProjectActivityDetailsReport?reportId=3#activityId={id}#total={total}#from={from}#to={to}#projectId={projectId}#onlyIncludeApprovedTimesheets={approved}</urlPattern></xml>','200','0',NULL,0,NULL,NULL,0,0),(3,1,'ohrm_project_activity.project_id','Project Id',NULL,'false',NULL,NULL,'label','<xml><getter>project_id</getter></xml>','75','0','right',0,NULL,NULL,0,1),(4,1,'ohrm_project_activity.activity_id','Activity Id',NULL,'false',NULL,NULL,'label','<xml><getter>activity_id</getter></xml>','75','0','right',0,NULL,NULL,0,1),(5,1,'ohrm_timesheet_item.duration','Time (hours)',NULL,'false',NULL,NULL,'label','<xml><getter>duration</getter></xml>','75','0','right',0,NULL,NULL,0,0),(6,1,'hs_hr_employee.emp_firstname','Employee First Name',NULL,'false',NULL,NULL,'label','<xml><getter>emp_firstname</getter></xml>','200','0',NULL,0,NULL,NULL,0,0),(7,1,'hs_hr_employee.emp_lastname','Employee Last Name',NULL,'false',NULL,NULL,'label','<xml><getter>emp_lastname</getter></xml>','200','0',NULL,0,NULL,NULL,0,0),(8,1,'ohrm_project_activity.name','Activity Name','activityname','false',NULL,NULL,'label','<xml><getter>activityname</getter></xml>','200','0',NULL,0,NULL,NULL,0,0),(9,3,'hs_hr_employee.employee_id','Employee Id','employeeId','false',NULL,NULL,'label','<xml><getter>employeeId</getter></xml>','100','0',NULL,0,1,'---',0,0),(10,3,'hs_hr_employee.emp_lastname','Employee Last Name','employeeLastname','false',NULL,NULL,'label','<xml><getter>employeeLastname</getter></xml>','200','0',NULL,0,1,'---',0,0),(11,3,'hs_hr_employee.emp_firstname','Employee First Name','employeeFirstname','false',NULL,NULL,'label','<xml><getter>employeeFirstname</getter></xml>','200','0',NULL,0,1,'---',0,0),(12,3,'hs_hr_employee.emp_middle_name','Employee Middle Name','employeeMiddlename','false',NULL,NULL,'label','<xml><getter>employeeMiddlename</getter></xml>','200','0',NULL,0,1,'---',0,0),(13,3,'hs_hr_employee.emp_birthday','Date of Birth','empBirthday','false',NULL,NULL,'labelDate','<xml><getter>empBirthday</getter></xml>','100','0',NULL,0,1,'---',0,0),(14,3,'ohrm_nationality.name','Nationality','nationality','false',NULL,NULL,'label','<xml><getter>nationality</getter></xml>','200','0',NULL,0,1,'---',0,0),(15,3,'CASE hs_hr_employee.emp_gender WHEN 1 THEN \"Male\" WHEN 2 THEN \"Female\" WHEN 3 THEN \"Other\" END','Gender','empGender','false',NULL,NULL,'label','<xml><getter>empGender</getter></xml>','80','0',NULL,0,1,'---',0,0),(17,3,'hs_hr_employee.emp_marital_status','Marital Status','maritalStatus','false',NULL,NULL,'label','<xml><getter>maritalStatus</getter></xml>','100','0',NULL,0,1,'---',0,0),(18,3,'hs_hr_employee.emp_dri_lice_num','Driver License Number','driversLicenseNumber','false',NULL,NULL,'label','<xml><getter>driversLicenseNumber</getter></xml>','240','0',NULL,0,1,'---',0,0),(19,3,'hs_hr_employee.emp_dri_lice_exp_date','License Expiry Date','licenseExpiryDate','false',NULL,NULL,'labelDate','<xml><getter>licenseExpiryDate</getter></xml>','135','0',NULL,0,1,'---',0,0),(20,3,'CONCAT_WS(\", \", NULLIF(hs_hr_employee.emp_street1, \"\"), NULLIF(hs_hr_employee.emp_street2, \"\"), NULLIF(hs_hr_employee.city_code, \"\"), NULLIF(hs_hr_employee.provin_code,\"\"), NULLIF(hs_hr_employee.emp_zipcode,\"\"), NULLIF(hs_hr_country.cou_name,\"\"))','Address','address','false',NULL,NULL,'label','<xml><getter>address</getter></xml>','200','0',NULL,0,2,'---',0,0),(21,3,'hs_hr_employee.emp_hm_telephone','Home Telephone','homeTelephone','false',NULL,NULL,'label','<xml><getter>homeTelephone</getter></xml>','130','0',NULL,0,2,'---',0,0),(22,3,'hs_hr_employee.emp_mobile','Mobile','mobile','false',NULL,NULL,'label','<xml><getter>mobile</getter></xml>','100','0',NULL,0,2,'---',0,0),(23,3,'hs_hr_employee.emp_work_telephone','Work Telephone','workTelephone','false',NULL,NULL,'label','<xml><getter>workTelephone</getter></xml>','100','0',NULL,0,2,'---',0,0),(24,3,'hs_hr_employee.emp_work_email','Work Email','workEmail','false',NULL,NULL,'label','<xml><getter>workEmail</getter></xml>','200','0',NULL,0,2,'---',0,0),(25,3,'hs_hr_employee.emp_oth_email','Other Email','otherEmail','false',NULL,NULL,'label','<xml><getter>otherEmail</getter></xml>','200','0',NULL,0,2,'---',0,0),(26,3,'hs_hr_emp_emergency_contacts.eec_name','Name','ecname','false',NULL,NULL,'label','<xml><getter>ecname</getter></xml>','200','0',NULL,1,3,'---',0,0),(27,3,'hs_hr_emp_emergency_contacts.eec_home_no','Home Telephone','ecHomeTelephone','false',NULL,NULL,'label','<xml><getter>ecHomeTelephone</getter></xml>','130','0',NULL,1,3,'---',0,0),(28,3,'hs_hr_emp_emergency_contacts.eec_office_no','Work Telephone','ecWorkTelephone','false',NULL,NULL,'label','<xml><getter>ecWorkTelephone</getter></xml>','100','0',NULL,1,3,'---',0,0),(29,3,'hs_hr_emp_emergency_contacts.eec_relationship','Relationship','ecRelationship','false',NULL,NULL,'label','<xml><getter>ecRelationship</getter></xml>','200','0',NULL,1,3,'---',0,0),(30,3,'hs_hr_emp_emergency_contacts.eec_mobile_no','Mobile','ecMobile','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,3,'---',0,0),(31,3,'hs_hr_emp_dependents.ed_name','Name','dependentName','false',NULL,NULL,'label','<xml><getter>dependentName</getter></xml>','200','0',NULL,1,4,'---',0,0),(32,3,'IF (hs_hr_emp_dependents.ed_relationship_type = \'other\', hs_hr_emp_dependents.ed_relationship, hs_hr_emp_dependents.ed_relationship_type)','Relationship','dependentRelationship','false',NULL,NULL,'label','<xml><getter>dependentRelationship</getter></xml>','200','0',NULL,1,4,'---',0,0),(33,3,'hs_hr_emp_dependents.ed_date_of_birth','Date of Birth','dependentDateofBirth','false',NULL,NULL,'labelDate','<xml><getter>dependentDateofBirth</getter></xml>','100','0',NULL,1,4,'---',0,0),(35,3,'ohrm_membership.name','Membership','name','false',NULL,NULL,'label','<xml><getter>name</getter></xml>','200','0',NULL,1,15,'---',0,0),(36,3,'hs_hr_emp_member_detail.ememb_subscript_ownership','Subscription Paid By','subscriptionPaidBy','false',NULL,NULL,'label','<xml><getter>subscriptionPaidBy</getter></xml>','200','0',NULL,1,15,'---',0,0),(37,3,'hs_hr_emp_member_detail.ememb_subscript_amount','Subscription Amount','subscriptionAmount','false',NULL,NULL,'label','<xml><getter>subscriptionAmount</getter></xml>','200','0',NULL,1,15,'---',0,0),(38,3,'hs_hr_emp_member_detail.ememb_subs_currency','Currency','membershipCurrency','false',NULL,NULL,'label','<xml><getter>membershipCurrency</getter></xml>','200','0',NULL,1,15,'---',0,0),(39,3,'hs_hr_emp_member_detail.ememb_commence_date','Subscription Commence Date','subscriptionCommenceDate','false',NULL,NULL,'labelDate','<xml><getter>subscriptionCommenceDate</getter></xml>','200','0',NULL,1,15,'---',0,0),(40,3,'hs_hr_emp_member_detail.ememb_renewal_date','Subscription Renewal Date','subscriptionRenewalDate','false',NULL,NULL,'labelDate','<xml><getter>subscriptionRenewalDate</getter></xml>','200','0',NULL,1,15,'---',0,0),(41,3,'hs_hr_emp_work_experience.eexp_employer','Company','expCompany','false',NULL,NULL,'label','<xml><getter>expCompany</getter></xml>','200','0',NULL,1,10,'---',0,0),(42,3,'hs_hr_emp_work_experience.eexp_jobtit','Job Title','expJobTitle','false',NULL,NULL,'label','<xml><getter>expJobTitle</getter></xml>','200','0',NULL,1,10,'---',0,0),(43,3,'hs_hr_emp_work_experience.eexp_from_date','From','expFrom','false',NULL,NULL,'labelDate','<xml><getter>expFrom</getter></xml>','100','0',NULL,1,10,'---',0,0),(44,3,'hs_hr_emp_work_experience.eexp_to_date','To','expTo','false',NULL,NULL,'labelDate','<xml><getter>expTo</getter></xml>','100','0',NULL,1,10,'---',0,0),(45,3,'hs_hr_emp_work_experience.eexp_comments','Comment','expComment','false',NULL,NULL,'label','<xml><getter>expComment</getter></xml>','200','0',NULL,1,10,'---',0,0),(47,3,'ohrm_education.name','Level','eduProgram','false',NULL,NULL,'label','<xml><getter>eduProgram</getter></xml>','200','0',NULL,1,11,'---',0,0),(48,3,'ohrm_emp_education.year','Year','eduYear','false',NULL,NULL,'label','<xml><getter>eduYear</getter></xml>','100','0',NULL,1,11,'---',0,0),(49,3,'ohrm_emp_education.score','Score','eduGPAOrScore','false',NULL,NULL,'label','<xml><getter>eduGPAOrScore</getter></xml>','80','0',NULL,1,11,'---',0,0),(52,3,'ohrm_skill.name','Skill','skill','false',NULL,NULL,'label','<xml><getter>skill</getter></xml>','200','0',NULL,1,12,'---',0,0),(53,3,'hs_hr_emp_skill.years_of_exp','Years of Experience','skillYearsOfExperience','false',NULL,NULL,'label','<xml><getter>skillYearsOfExperience</getter></xml>','135','0',NULL,1,12,'---',0,0),(54,3,'hs_hr_emp_skill.comments','Comments','skillComments','false',NULL,NULL,'label','<xml><getter>skillComments</getter></xml>','200','0',NULL,1,12,'---',0,0),(55,3,'ohrm_language.name','Language','langName','false',NULL,NULL,'label','<xml><getter>langName</getter></xml>','200','0',NULL,1,13,'---',0,0),(57,3,'CASE hs_hr_emp_language.competency WHEN 1 THEN \"Poor\" WHEN 2 THEN \"Basic\" WHEN 3 THEN \"Good\" WHEN 4 THEN \"Mother Tongue\" END','Competency','langCompetency','false',NULL,NULL,'label','<xml><getter>langCompetency</getter></xml>','130','0',NULL,1,13,'---',0,0),(58,3,'hs_hr_emp_language.comments','Comments','langComments','false',NULL,NULL,'label','<xml><getter>langComments</getter></xml>','200','0',NULL,1,13,'---',0,0),(59,3,'ohrm_license.name','License Type','empLicenseType','false',NULL,NULL,'label','<xml><getter>empLicenseType</getter></xml>','200','0',NULL,1,14,'---',0,0),(60,3,'ohrm_emp_license.license_issued_date','Issued Date','empLicenseIssuedDate','false',NULL,NULL,'labelDate','<xml><getter>empLicenseIssuedDate</getter></xml>','100','0',NULL,1,14,'---',0,0),(61,3,'ohrm_emp_license.license_expiry_date','Expiry Date','empLicenseExpiryDate','false',NULL,NULL,'labelDate','<xml><getter>empLicenseExpiryDate</getter></xml>','100','0',NULL,1,14,'---',0,0),(62,3,'supervisor.emp_firstname','First Name','supervisorFirstName','false',NULL,NULL,'label','<xml><getter>supervisorFirstName</getter></xml>','200','0',NULL,1,9,'---',0,0),(63,3,'subordinate.emp_firstname','First Name','subordinateFirstName','false',NULL,NULL,'label','<xml><getter>subordinateFirstName</getter></xml>','200','0',NULL,1,8,'---',0,0),(64,3,'supervisor.emp_lastname','Last Name','supervisorLastName','false',NULL,NULL,'label','<xml><getter>supervisorLastName</getter></xml>','200','0',NULL,1,9,'---',0,0),(65,3,'ohrm_pay_grade.name','Pay Grade','salPayGrade','false',NULL,NULL,'label','<xml><getter>salPayGrade</getter></xml>','200','0',NULL,1,7,'---',0,0),(66,3,'hs_hr_emp_basicsalary.salary_component','Salary Component','salSalaryComponent','false',NULL,NULL,'label','<xml><getter>salSalaryComponent</getter></xml>','200','0',NULL,1,7,'---',0,0),(67,3,'hs_hr_emp_basicsalary.ebsal_basic_salary','Amount','salAmount','false',NULL,NULL,'label','<xml><getter>salAmount</getter></xml>','200','0',NULL,1,7,'---',1,0),(68,3,'hs_hr_emp_basicsalary.comments','Comments','salComments','false',NULL,NULL,'label','<xml><getter>salComments</getter></xml>','200','0',NULL,1,7,'---',0,0),(69,3,'hs_hr_payperiod.payperiod_name','Pay Frequency','salPayFrequency','false',NULL,NULL,'label','<xml><getter>salPayFrequency</getter></xml>','200','0',NULL,1,7,'---',0,0),(70,3,'hs_hr_currency_type.currency_name','Currency','salCurrency','false',NULL,NULL,'label','<xml><getter>salCurrency</getter></xml>','200','0',NULL,1,7,'---',0,0),(71,3,'hs_hr_emp_directdebit.dd_account','Direct Deposit Account Number','ddAccountNumber','false',NULL,NULL,'label','<xml><getter>ddAccountNumber</getter></xml>','200','0',NULL,1,7,'---',0,0),(72,3,'hs_hr_emp_directdebit.dd_account_type','Direct Deposit Account Type','ddAccountType','false',NULL,NULL,'label','<xml><getter>ddAccountType</getter></xml>','200','0',NULL,1,7,'---',0,0),(73,3,'hs_hr_emp_directdebit.dd_routing_num','Direct Deposit Routing Number','ddRoutingNumber','false',NULL,NULL,'label','<xml><getter>ddRoutingNumber</getter></xml>','200','0',NULL,1,7,'---',0,0),(74,3,'hs_hr_emp_directdebit.dd_amount','Direct Deposit Amount','ddAmount','false',NULL,NULL,'label','<xml><getter>ddAmount</getter></xml>','200','0',NULL,1,7,'---',0,0),(75,3,'hs_hr_emp_contract_extend.econ_extend_start_date','Contract Start Date','empContStartDate','false',NULL,NULL,'labelDate','<xml><getter>empContStartDate</getter></xml>','200','0',NULL,1,6,'---',0,0),(76,3,'hs_hr_emp_contract_extend.econ_extend_end_date','Contract End Date','empContEndDate','false',NULL,NULL,'labelDate','<xml><getter>empContEndDate</getter></xml>','200','0',NULL,1,6,'---',0,0),(77,3,'ohrm_job_title.job_title','Job Title','empJobTitle','false',NULL,NULL,'label','<xml><getter>empJobTitle</getter></xml>','200','0',NULL,1,6,'---',0,0),(78,3,'ohrm_employment_status.name','Employment Status','empEmploymentStatus','false',NULL,NULL,'label','<xml><getter>empEmploymentStatus</getter></xml>','200','0',NULL,1,6,'---',0,0),(80,3,'ohrm_job_category.name','Job Category','empJobCategory','false',NULL,NULL,'label','<xml><getter>empJobCategory</getter></xml>','200','0',NULL,1,6,'---',0,0),(81,3,'hs_hr_employee.joined_date','Joined Date','empJoinedDate','false',NULL,NULL,'labelDate','<xml><getter>empJoinedDate</getter></xml>','100','0',NULL,1,6,'---',0,0),(82,3,'ohrm_subunit.name','Sub Unit','empSubUnit','false',NULL,NULL,'label','<xml><getter>empSubUnit</getter></xml>','200','0',NULL,1,6,'---',0,0),(83,3,'ohrm_location.name','Location','empLocation','false',NULL,NULL,'label','<xml><getter>empLocation</getter></xml>','200','0',NULL,1,6,'---',0,0),(84,3,'hs_hr_emp_passport.ep_passport_num','Number','empPassportNo','false',NULL,NULL,'label','<xml><getter>empPassportNo</getter></xml>','200','0',NULL,1,5,'---',0,0),(85,3,'hs_hr_emp_passport.ep_passportissueddate','Issued Date','empPassportIssuedDate','false',NULL,NULL,'labelDate','<xml><getter>empPassportIssuedDate</getter></xml>','100','0',NULL,1,5,'---',0,0),(86,3,'hs_hr_emp_passport.ep_passportexpiredate','Expiry Date','empPassportExpiryDate','false',NULL,NULL,'labelDate','<xml><getter>empPassportExpiryDate</getter></xml>','100','0',NULL,1,5,'---',0,0),(87,3,'hs_hr_emp_passport.ep_i9_status','Eligibility Status','empPassportEligibleStatus','false',NULL,NULL,'label','<xml><getter>empPassportEligibleStatus</getter></xml>','200','0',NULL,1,5,'---',0,0),(88,3,'hs_hr_emp_passport.cou_code','Issued By','empPassportIssuedBy','false',NULL,NULL,'label','<xml><getter>empPassportIssuedBy</getter></xml>','200','0',NULL,1,5,'---',0,0),(89,3,'hs_hr_emp_passport.ep_i9_review_date','Eligibility Review Date','empPassportEligibleReviewDate','false',NULL,NULL,'labelDate','<xml><getter>empPassportEligibleReviewDate</getter></xml>','200','0',NULL,1,5,'---',0,0),(90,3,'hs_hr_emp_passport.ep_comments','Comments','empPassportComments','false',NULL,NULL,'label','<xml><getter>empPassportComments</getter></xml>','200','0',NULL,1,5,'---',0,0),(91,3,'subordinate.emp_lastname','Last Name','subordinateLastName','false',NULL,NULL,'label','<xml><getter>subordinateLastName</getter></xml>','200','0',NULL,1,8,'---',0,0),(92,3,'CASE hs_hr_emp_language.fluency WHEN 1 THEN \"Writing\" WHEN 2 THEN \"Speaking\" WHEN 3 THEN \"Reading\" END','Fluency','langFluency','false',NULL,NULL,'label','<xml><getter>langFluency</getter></xml>','200','0',NULL,1,13,'---',0,0),(93,3,'supervisor_reporting_method.reporting_method_name','Reporting Method','supReportingMethod','false',NULL,NULL,'label','<xml><getter>supReportingMethod</getter></xml>','200','0',NULL,1,9,'---',0,0),(94,3,'subordinate_reporting_method.reporting_method_name','Reporting Method','subReportingMethod','false',NULL,NULL,'label','<xml><getter>subReportingMethod</getter></xml>','200','0',NULL,1,8,'---',0,0),(95,3,'CASE hs_hr_emp_passport.ep_passport_type_flg WHEN 1 THEN \"Passport\" WHEN 2 THEN \"Visa\" END','Document Type','documentType','false',NULL,NULL,'label','<xml><getter>documentType</getter></xml>','200','0',NULL,1,5,'---',0,0),(97,3,'hs_hr_employee.emp_other_id','Other Id','otherId','false',NULL,NULL,'label','<xml><getter>otherId</getter></xml>','100','0',NULL,0,1,'---',0,0),(98,3,'hs_hr_emp_emergency_contacts.eec_seqno','ecSeqNo','ecSeqNo','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,3,'---',0,1),(99,3,'hs_hr_emp_dependents.ed_seqno','SeqNo','edSeqNo','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,4,'---',0,1),(100,3,'hs_hr_emp_passport.ep_seqno','SeqNo','epSeqNo','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,5,'---',0,1),(101,3,'hs_hr_emp_basicsalary.id','salaryId','salaryId','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,7,'---',0,1),(102,3,'subordinate.emp_number','subordinateId','subordinateId','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,8,'---',0,1),(103,3,'supervisor.emp_number','supervisorId','supervisorId','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,9,'---',0,1),(104,3,'hs_hr_emp_work_experience.eexp_seqno','workExpSeqNo','workExpSeqNo','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,10,'---',0,1),(105,3,'ohrm_emp_education.education_id','empEduCode','empEduCode','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,11,'---',0,1),(106,3,'hs_hr_emp_skill.skill_id','empSkillCode','empSkillCode','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,12,'---',0,1),(107,3,'hs_hr_emp_language.lang_id','empLangCode','empLangCode','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,13,'---',0,1),(108,3,'hs_hr_emp_language.fluency','empLangType','empLangType','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,13,'---',0,1),(109,3,'ohrm_emp_license.license_id','empLicenseCode','empLicenseCode','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,14,'---',0,1),(110,3,'hs_hr_emp_member_detail.membship_code','membershipCode','membershipCode','false',NULL,NULL,'label','<xml><getter>ecMobile</getter></xml>','100','0',NULL,1,15,'---',0,1),(112,3,'ROUND(DATEDIFF(hs_hr_emp_work_experience.eexp_to_date, hs_hr_emp_work_experience.eexp_from_date)/365,1)','Duration','expDuration','false',NULL,NULL,'label','<xml><getter>expDuration</getter></xml>','100','0',NULL,1,10,'---',0,0),(113,3,'ohrm_emp_termination.termination_date','Termination Date','terminationDate','false',NULL,NULL,'labelDate','<xml><getter>terminationDate</getter></xml>','100','0',NULL,1,6,'---',0,0),(114,3,'ohrm_emp_termination_reason.name','Termination Reason','terminationReason','false',NULL,NULL,'label','<xml><getter>terminationReason</getter></xml>','100','0',NULL,1,6,'---',0,0),(115,3,'ohrm_emp_education.institute','Institute','getInstitute','false',NULL,NULL,'label','<xml><getter>getInstitute</getter></xml>','80','0',NULL,1,11,'---',0,0),(116,3,'ohrm_emp_education.major','Major/Specialization','getMajor','false',NULL,NULL,'label','<xml><getter>getMajor</getter></xml>','80','0',NULL,1,11,'---',0,0),(117,3,'ohrm_emp_education.start_date','Start Date','getStartDate','false',NULL,NULL,'labelDate','<xml><getter>getStartDate</getter></xml>','80','0',NULL,1,11,'---',0,0),(118,3,'ohrm_emp_education.end_date','End Date','getEndDate','false',NULL,NULL,'labelDate','<xml><getter>getEndDate</getter></xml>','80','0',NULL,1,11,'---',0,0),(119,3,'ohrm_emp_license.license_no','License Number','getLicenseNo','false',NULL,NULL,'label','<xml><getter>getLicenseNo</getter></xml>','200','0',NULL,1,14,'---',0,0),(120,3,'ohrm_emp_termination.note','Termination Note','getNote','false',NULL,NULL,'label','<xml><getter>getNote</getter></xml>','100','0',NULL,1,6,'---',0,0);
/*!40000 ALTER TABLE `ohrm_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_display_field_group`
--

DROP TABLE IF EXISTS `ohrm_display_field_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_display_field_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_list` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `report_group_id` (`report_group_id`),
  CONSTRAINT `ohrm_display_field_group_ibfk_1` FOREIGN KEY (`report_group_id`) REFERENCES `ohrm_report_group` (`report_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_display_field_group`
--

LOCK TABLES `ohrm_display_field_group` WRITE;
/*!40000 ALTER TABLE `ohrm_display_field_group` DISABLE KEYS */;
INSERT INTO `ohrm_display_field_group` VALUES (1,3,'Personal',0),(2,3,'Contact Details',0),(3,3,'Emergency Contacts',1),(4,3,'Dependents',1),(5,3,'Immigration',1),(6,3,'Job',0),(7,3,'Salary',1),(8,3,'Subordinates',1),(9,3,'Supervisors',1),(10,3,'Work Experience',1),(11,3,'Education',1),(12,3,'Skills',1),(13,3,'Languages',1),(14,3,'License',1),(15,3,'Memberships',1),(16,3,'Custom Fields',0);
/*!40000 ALTER TABLE `ohrm_display_field_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_education`
--

DROP TABLE IF EXISTS `ohrm_education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_education`
--

LOCK TABLES `ohrm_education` WRITE;
/*!40000 ALTER TABLE `ohrm_education` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_education` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email`
--

DROP TABLE IF EXISTS `ohrm_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `ohrm_email_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email`
--

LOCK TABLES `ohrm_email` WRITE;
/*!40000 ALTER TABLE `ohrm_email` DISABLE KEYS */;
INSERT INTO `ohrm_email` VALUES (1,'leave.apply'),(3,'leave.approve'),(2,'leave.assign'),(4,'leave.cancel'),(6,'leave.change'),(5,'leave.reject');
/*!40000 ALTER TABLE `ohrm_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email_configuration`
--

DROP TABLE IF EXISTS `ohrm_email_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email_configuration` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mail_type` varchar(50) DEFAULT NULL,
  `sent_as` varchar(250) NOT NULL,
  `sendmail_path` varchar(250) DEFAULT NULL,
  `smtp_host` varchar(250) DEFAULT NULL,
  `smtp_port` int(10) DEFAULT NULL,
  `smtp_username` varchar(250) DEFAULT NULL,
  `smtp_password` varchar(250) DEFAULT NULL,
  `smtp_auth_type` varchar(50) DEFAULT NULL,
  `smtp_security_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email_configuration`
--

LOCK TABLES `ohrm_email_configuration` WRITE;
/*!40000 ALTER TABLE `ohrm_email_configuration` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_email_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email_notification`
--

DROP TABLE IF EXISTS `ohrm_email_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email_notification` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_enable` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email_notification`
--

LOCK TABLES `ohrm_email_notification` WRITE;
/*!40000 ALTER TABLE `ohrm_email_notification` DISABLE KEYS */;
INSERT INTO `ohrm_email_notification` VALUES (1,'Leave Applications',0),(2,'Leave Assignments',0),(3,'Leave Approvals',0),(4,'Leave Cancellations',0),(5,'Leave Rejections',0);
/*!40000 ALTER TABLE `ohrm_email_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email_processor`
--

DROP TABLE IF EXISTS `ohrm_email_processor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email_processor` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `email_id` int(6) NOT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `ohrm_email_processor_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `ohrm_email` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email_processor`
--

LOCK TABLES `ohrm_email_processor` WRITE;
/*!40000 ALTER TABLE `ohrm_email_processor` DISABLE KEYS */;
INSERT INTO `ohrm_email_processor` VALUES (1,1,'LeaveEmailProcessor'),(2,2,'LeaveEmailProcessor'),(3,3,'LeaveEmailProcessor'),(4,4,'LeaveEmailProcessor'),(5,5,'LeaveEmailProcessor'),(6,6,'LeaveChangeMailProcessor');
/*!40000 ALTER TABLE `ohrm_email_processor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email_subscriber`
--

DROP TABLE IF EXISTS `ohrm_email_subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email_subscriber` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `notification_id` int(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_id` (`notification_id`),
  CONSTRAINT `ohrm_email_subscriber_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `ohrm_email_notification` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email_subscriber`
--

LOCK TABLES `ohrm_email_subscriber` WRITE;
/*!40000 ALTER TABLE `ohrm_email_subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_email_subscriber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_email_template`
--

DROP TABLE IF EXISTS `ohrm_email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_email_template` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `email_id` int(6) NOT NULL,
  `locale` varchar(20) DEFAULT NULL,
  `performer_role` varchar(50) DEFAULT NULL,
  `recipient_role` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `ohrm_email_template_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `ohrm_email` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_email_template`
--

LOCK TABLES `ohrm_email_template` WRITE;
/*!40000 ALTER TABLE `ohrm_email_template` DISABLE KEYS */;
INSERT INTO `ohrm_email_template` VALUES (1,1,'en_US',NULL,'supervisor','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationBody.txt'),(2,1,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubscriberBody.txt'),(3,3,'en_US',NULL,'ess','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalBody.txt'),(4,3,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubscriberBody.txt'),(5,2,'en_US',NULL,'ess','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentBody.txt'),(6,2,'en_US',NULL,'supervisor','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubjectForSupervisors.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentBodyForSupervisors.txt'),(7,2,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubscriberBody.txt'),(8,4,'en_US','ess','supervisor','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationBody.txt'),(9,4,'en_US','ess','subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubscriberBody.txt'),(10,4,'en_US',NULL,'ess','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationBody.txt'),(11,4,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubscriberBody.txt'),(12,5,'en_US',NULL,'ess','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionBody.txt'),(13,5,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubscriberBody.txt'),(14,6,'en_US',NULL,'ess','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeBody.txt'),(15,6,'en_US',NULL,'subscriber','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubscriberSubject.txt','orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubscriberBody.txt');
/*!40000 ALTER TABLE `ohrm_email_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_emp_education`
--

DROP TABLE IF EXISTS `ohrm_emp_education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_emp_education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_number` int(11) NOT NULL,
  `education_id` int(11) NOT NULL,
  `institute` varchar(100) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `year` decimal(4,0) DEFAULT NULL,
  `score` varchar(25) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_number` (`emp_number`),
  KEY `education_id` (`education_id`),
  CONSTRAINT `ohrm_emp_education_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_emp_education_ibfk_2` FOREIGN KEY (`education_id`) REFERENCES `ohrm_education` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_emp_education`
--

LOCK TABLES `ohrm_emp_education` WRITE;
/*!40000 ALTER TABLE `ohrm_emp_education` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_emp_education` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_emp_license`
--

DROP TABLE IF EXISTS `ohrm_emp_license`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_emp_license` (
  `emp_number` int(11) NOT NULL,
  `license_id` int(11) NOT NULL,
  `license_no` varchar(50) DEFAULT NULL,
  `license_issued_date` date DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  PRIMARY KEY (`emp_number`,`license_id`),
  KEY `license_id` (`license_id`),
  CONSTRAINT `ohrm_emp_license_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_emp_license_ibfk_2` FOREIGN KEY (`license_id`) REFERENCES `ohrm_license` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_emp_license`
--

LOCK TABLES `ohrm_emp_license` WRITE;
/*!40000 ALTER TABLE `ohrm_emp_license` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_emp_license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_emp_reporting_method`
--

DROP TABLE IF EXISTS `ohrm_emp_reporting_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_emp_reporting_method` (
  `reporting_method_id` int(7) NOT NULL AUTO_INCREMENT,
  `reporting_method_name` varchar(100) NOT NULL,
  PRIMARY KEY (`reporting_method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_emp_reporting_method`
--

LOCK TABLES `ohrm_emp_reporting_method` WRITE;
/*!40000 ALTER TABLE `ohrm_emp_reporting_method` DISABLE KEYS */;
INSERT INTO `ohrm_emp_reporting_method` VALUES (1,'Direct'),(2,'Indirect');
/*!40000 ALTER TABLE `ohrm_emp_reporting_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_emp_termination`
--

DROP TABLE IF EXISTS `ohrm_emp_termination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_emp_termination` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `emp_number` int(4) DEFAULT NULL,
  `reason_id` int(4) DEFAULT NULL,
  `termination_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reason_id` (`reason_id`),
  KEY `emp_number` (`emp_number`),
  CONSTRAINT `ohrm_emp_termination_ibfk_1` FOREIGN KEY (`reason_id`) REFERENCES `ohrm_emp_termination_reason` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_emp_termination_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_emp_termination`
--

LOCK TABLES `ohrm_emp_termination` WRITE;
/*!40000 ALTER TABLE `ohrm_emp_termination` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_emp_termination` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_emp_termination_reason`
--

DROP TABLE IF EXISTS `ohrm_emp_termination_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_emp_termination_reason` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_emp_termination_reason`
--

LOCK TABLES `ohrm_emp_termination_reason` WRITE;
/*!40000 ALTER TABLE `ohrm_emp_termination_reason` DISABLE KEYS */;
INSERT INTO `ohrm_emp_termination_reason` VALUES (1,'Other'),(2,'Retired'),(3,'Contract Not Renewed'),(4,'Resigned - Company Requested'),(5,'Resigned - Self Proposed'),(6,'Resigned'),(7,'Deceased'),(8,'Physically Disabled/Compensated'),(9,'Laid-off'),(10,'Dismissed');
/*!40000 ALTER TABLE `ohrm_emp_termination_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_employee_event`
--

DROP TABLE IF EXISTS `ohrm_employee_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_employee_event` (
  `event_id` int(7) NOT NULL AUTO_INCREMENT,
  `employee_id` int(7) NOT NULL DEFAULT 0,
  `type` varchar(45) DEFAULT NULL,
  `event` varchar(45) DEFAULT NULL,
  `note` varchar(150) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_employee_event`
--

LOCK TABLES `ohrm_employee_event` WRITE;
/*!40000 ALTER TABLE `ohrm_employee_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_employee_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_employee_work_shift`
--

DROP TABLE IF EXISTS `ohrm_employee_work_shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_employee_work_shift` (
  `work_shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_number` int(11) NOT NULL,
  PRIMARY KEY (`work_shift_id`,`emp_number`),
  KEY `emp_number` (`emp_number`),
  CONSTRAINT `ohrm_employee_work_shift_ibfk_1` FOREIGN KEY (`work_shift_id`) REFERENCES `ohrm_work_shift` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_employee_work_shift_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_employee_work_shift`
--

LOCK TABLES `ohrm_employee_work_shift` WRITE;
/*!40000 ALTER TABLE `ohrm_employee_work_shift` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_employee_work_shift` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_employment_status`
--

DROP TABLE IF EXISTS `ohrm_employment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_employment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_employment_status`
--

LOCK TABLES `ohrm_employment_status` WRITE;
/*!40000 ALTER TABLE `ohrm_employment_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_employment_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_filter_field`
--

DROP TABLE IF EXISTS `ohrm_filter_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_filter_field` (
  `filter_field_id` bigint(20) NOT NULL,
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `where_clause_part` mediumtext NOT NULL,
  `filter_field_widget` varchar(255) DEFAULT NULL,
  `condition_no` int(20) NOT NULL,
  `required` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`filter_field_id`),
  KEY `report_group_id` (`report_group_id`),
  CONSTRAINT `ohrm_filter_field_ibfk_1` FOREIGN KEY (`report_group_id`) REFERENCES `ohrm_report_group` (`report_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_filter_field`
--

LOCK TABLES `ohrm_filter_field` WRITE;
/*!40000 ALTER TABLE `ohrm_filter_field` DISABLE KEYS */;
INSERT INTO `ohrm_filter_field` VALUES (1,1,'project_name','ohrm_project.project_id','ohrmWidgetProjectList',2,'true'),(2,1,'activity_show_deleted','ohrm_project_activity.is_deleted','ohrmWidgetInputCheckbox',2,'false'),(3,1,'project_date_range','date','ohrmWidgetDateRange',1,'false'),(4,1,'employee','hs_hr_employee.emp_number','ohrmReportWidgetEmployeeListAutoFill',2,'true'),(5,1,'activity_name','ohrm_project_activity.activity_id','ohrmWidgetProjectActivityList',2,'true'),(6,1,'project_name','ohrm_project.project_id','ohrmWidgetProjectListWithAllOption',2,'true'),(7,1,'only_include_approved_timesheets','ohrm_timesheet.state','ohrmWidgetApprovedTimesheetInputCheckBox',2,NULL),(8,3,'employee_name','hs_hr_employee.emp_number','ohrmReportWidgetEmployeeListAutoFill',1,NULL),(9,3,'pay_grade','hs_hr_emp_basicsalary.sal_grd_code','ohrmReportWidgetPayGradeDropDown',1,NULL),(10,3,'education','ohrm_emp_education.education_id','ohrmReportWidgetEducationtypeDropDown',1,NULL),(11,3,'employment_status','hs_hr_employee.emp_status','ohrmWidgetEmploymentStatusList',1,NULL),(12,3,'service_period','datediff(current_date(), hs_hr_employee.joined_date)/365','ohrmReportWidgetServicePeriod',1,NULL),(13,3,'joined_date','hs_hr_employee.joined_date','ohrmReportWidgetJoinedDate',1,NULL),(14,3,'job_title','hs_hr_employee.job_title_code','ohrmWidgetJobTitleList',1,NULL),(15,3,'language','hs_hr_emp_language.lang_id','ohrmReportWidgetLanguageDropDown',1,NULL),(16,3,'skill','hs_hr_emp_skill.skill_id','ohrmReportWidgetSkillDropDown',1,NULL),(17,3,'age_group','datediff(current_date(), hs_hr_employee.emp_birthday)/365','ohrmReportWidgetAgeGroup',1,NULL),(18,3,'sub_unit','hs_hr_employee.work_station','ohrmWidgetSubDivisionList',1,NULL),(19,3,'gender','hs_hr_employee.emp_gender','ohrmReportWidgetGenderDropDown',1,NULL),(20,3,'location','ohrm_location.id','ohrmReportWidgetOperationalCountryLocationDropDown',1,NULL),(21,1,'is_deleted','ohrm_project_activity.is_deleted','',2,NULL),(22,3,'include','hs_hr_employee.termination_id','ohrmReportWidgetIncludedEmployeesDropDown',1,'true');
/*!40000 ALTER TABLE `ohrm_filter_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_group_field`
--

DROP TABLE IF EXISTS `ohrm_group_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_group_field` (
  `group_field_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_by_clause` mediumtext NOT NULL,
  `group_field_widget` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_group_field`
--

LOCK TABLES `ohrm_group_field` WRITE;
/*!40000 ALTER TABLE `ohrm_group_field` DISABLE KEYS */;
INSERT INTO `ohrm_group_field` VALUES (1,'activity id','GROUP BY ohrm_project_activity.activity_id',NULL),(2,'employee number','GROUP BY hs_hr_employee.emp_number',NULL);
/*!40000 ALTER TABLE `ohrm_group_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_holiday`
--

DROP TABLE IF EXISTS `ohrm_holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_holiday` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `recurring` tinyint(3) unsigned DEFAULT 0,
  `length` int(10) unsigned DEFAULT NULL,
  `operational_country_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ohrm_holiday_ohrm_operational_country` (`operational_country_id`),
  CONSTRAINT `fk_ohrm_holiday_ohrm_operational_country` FOREIGN KEY (`operational_country_id`) REFERENCES `ohrm_operational_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_holiday`
--

LOCK TABLES `ohrm_holiday` WRITE;
/*!40000 ALTER TABLE `ohrm_holiday` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_home_page`
--

DROP TABLE IF EXISTS `ohrm_home_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_home_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `enable_class` varchar(100) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'lowest priority 0',
  PRIMARY KEY (`id`),
  KEY `user_role_id` (`user_role_id`),
  CONSTRAINT `ohrm_home_page_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `ohrm_user_role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_home_page`
--

LOCK TABLES `ohrm_home_page` WRITE;
/*!40000 ALTER TABLE `ohrm_home_page` DISABLE KEYS */;
INSERT INTO `ohrm_home_page` VALUES (1,1,'dashboard/index',NULL,15),(2,2,'dashboard/index',NULL,5);
/*!40000 ALTER TABLE `ohrm_home_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_candidate`
--

DROP TABLE IF EXISTS `ohrm_job_candidate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_candidate` (
  `id` int(13) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `middle_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `status` int(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `mode_of_application` int(4) NOT NULL,
  `date_of_application` date NOT NULL,
  `cv_file_id` int(13) DEFAULT NULL,
  `cv_text_version` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `added_person` int(13) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `added_person` (`added_person`),
  CONSTRAINT `ohrm_job_candidate_ibfk_1` FOREIGN KEY (`added_person`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_candidate`
--

LOCK TABLES `ohrm_job_candidate` WRITE;
/*!40000 ALTER TABLE `ohrm_job_candidate` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_candidate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_candidate_attachment`
--

DROP TABLE IF EXISTS `ohrm_job_candidate_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_candidate_attachment` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `candidate_id` int(13) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `file_content` mediumblob DEFAULT NULL,
  `attachment_type` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `ohrm_job_candidate_attachment_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `ohrm_job_candidate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_candidate_attachment`
--

LOCK TABLES `ohrm_job_candidate_attachment` WRITE;
/*!40000 ALTER TABLE `ohrm_job_candidate_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_candidate_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_candidate_history`
--

DROP TABLE IF EXISTS `ohrm_job_candidate_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_candidate_history` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `candidate_id` int(13) NOT NULL,
  `vacancy_id` int(13) DEFAULT NULL,
  `candidate_vacancy_name` varchar(255) DEFAULT NULL,
  `interview_id` int(13) DEFAULT NULL,
  `action` int(4) NOT NULL,
  `performed_by` int(13) DEFAULT NULL,
  `performed_date` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `interviewers` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_id` (`candidate_id`),
  KEY `vacancy_id` (`vacancy_id`),
  KEY `interview_id` (`interview_id`),
  KEY `performed_by` (`performed_by`),
  CONSTRAINT `ohrm_job_candidate_history_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `ohrm_job_candidate` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_job_candidate_history_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `ohrm_job_vacancy` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_job_candidate_history_ibfk_3` FOREIGN KEY (`interview_id`) REFERENCES `ohrm_job_interview` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_job_candidate_history_ibfk_4` FOREIGN KEY (`performed_by`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_candidate_history`
--

LOCK TABLES `ohrm_job_candidate_history` WRITE;
/*!40000 ALTER TABLE `ohrm_job_candidate_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_candidate_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_candidate_vacancy`
--

DROP TABLE IF EXISTS `ohrm_job_candidate_vacancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_candidate_vacancy` (
  `id` int(13) DEFAULT NULL,
  `candidate_id` int(13) NOT NULL,
  `vacancy_id` int(13) NOT NULL,
  `status` varchar(100) NOT NULL,
  `applied_date` date NOT NULL,
  PRIMARY KEY (`candidate_id`,`vacancy_id`),
  UNIQUE KEY `id` (`id`),
  KEY `vacancy_id` (`vacancy_id`),
  CONSTRAINT `ohrm_job_candidate_vacancy_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `ohrm_job_candidate` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_job_candidate_vacancy_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `ohrm_job_vacancy` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_candidate_vacancy`
--

LOCK TABLES `ohrm_job_candidate_vacancy` WRITE;
/*!40000 ALTER TABLE `ohrm_job_candidate_vacancy` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_candidate_vacancy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_category`
--

DROP TABLE IF EXISTS `ohrm_job_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_category`
--

LOCK TABLES `ohrm_job_category` WRITE;
/*!40000 ALTER TABLE `ohrm_job_category` DISABLE KEYS */;
INSERT INTO `ohrm_job_category` VALUES (1,'Officials and Managers'),(2,'Professionals'),(3,'Technicians'),(4,'Sales Workers'),(5,'Operatives'),(6,'Office and Clerical Workers'),(7,'Craft Workers'),(8,'Service Workers'),(9,'Laborers and Helpers');
/*!40000 ALTER TABLE `ohrm_job_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_interview`
--

DROP TABLE IF EXISTS `ohrm_job_interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_interview` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `candidate_vacancy_id` int(13) DEFAULT NULL,
  `candidate_id` int(13) DEFAULT NULL,
  `interview_name` varchar(100) NOT NULL,
  `interview_date` date DEFAULT NULL,
  `interview_time` time DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_vacancy_id` (`candidate_vacancy_id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `ohrm_job_interview_ibfk_1` FOREIGN KEY (`candidate_vacancy_id`) REFERENCES `ohrm_job_candidate_vacancy` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_job_interview_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `ohrm_job_candidate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_interview`
--

LOCK TABLES `ohrm_job_interview` WRITE;
/*!40000 ALTER TABLE `ohrm_job_interview` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_interview_attachment`
--

DROP TABLE IF EXISTS `ohrm_job_interview_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_interview_attachment` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `interview_id` int(13) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `file_content` mediumblob DEFAULT NULL,
  `attachment_type` int(4) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_id` (`interview_id`),
  CONSTRAINT `ohrm_job_interview_attachment_ibfk_1` FOREIGN KEY (`interview_id`) REFERENCES `ohrm_job_interview` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_interview_attachment`
--

LOCK TABLES `ohrm_job_interview_attachment` WRITE;
/*!40000 ALTER TABLE `ohrm_job_interview_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_interview_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_interview_interviewer`
--

DROP TABLE IF EXISTS `ohrm_job_interview_interviewer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_interview_interviewer` (
  `interview_id` int(13) NOT NULL,
  `interviewer_id` int(13) NOT NULL,
  PRIMARY KEY (`interview_id`,`interviewer_id`),
  KEY `interviewer_id` (`interviewer_id`),
  CONSTRAINT `ohrm_job_interview_interviewer_ibfk_1` FOREIGN KEY (`interview_id`) REFERENCES `ohrm_job_interview` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_job_interview_interviewer_ibfk_2` FOREIGN KEY (`interviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_interview_interviewer`
--

LOCK TABLES `ohrm_job_interview_interviewer` WRITE;
/*!40000 ALTER TABLE `ohrm_job_interview_interviewer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_interview_interviewer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_specification_attachment`
--

DROP TABLE IF EXISTS `ohrm_job_specification_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_specification_attachment` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `job_title_id` int(13) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `file_content` mediumblob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_title_id` (`job_title_id`),
  CONSTRAINT `ohrm_job_specification_attachment_ibfk_1` FOREIGN KEY (`job_title_id`) REFERENCES `ohrm_job_title` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_specification_attachment`
--

LOCK TABLES `ohrm_job_specification_attachment` WRITE;
/*!40000 ALTER TABLE `ohrm_job_specification_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_specification_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_title`
--

DROP TABLE IF EXISTS `ohrm_job_title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_title` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `job_title` varchar(100) NOT NULL,
  `job_description` varchar(400) DEFAULT NULL,
  `note` varchar(400) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_title`
--

LOCK TABLES `ohrm_job_title` WRITE;
/*!40000 ALTER TABLE `ohrm_job_title` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_title` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_vacancy`
--

DROP TABLE IF EXISTS `ohrm_job_vacancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_vacancy` (
  `id` int(13) NOT NULL,
  `job_title_code` int(4) NOT NULL,
  `hiring_manager_id` int(13) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `no_of_positions` int(13) DEFAULT NULL,
  `status` int(4) NOT NULL,
  `published_in_feed` tinyint(1) NOT NULL DEFAULT 0,
  `defined_time` datetime NOT NULL,
  `updated_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_title_code` (`job_title_code`),
  KEY `hiring_manager_id` (`hiring_manager_id`),
  CONSTRAINT `ohrm_job_vacancy_ibfk_1` FOREIGN KEY (`job_title_code`) REFERENCES `ohrm_job_title` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_job_vacancy_ibfk_2` FOREIGN KEY (`hiring_manager_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_vacancy`
--

LOCK TABLES `ohrm_job_vacancy` WRITE;
/*!40000 ALTER TABLE `ohrm_job_vacancy` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_vacancy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_job_vacancy_attachment`
--

DROP TABLE IF EXISTS `ohrm_job_vacancy_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_job_vacancy_attachment` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `vacancy_id` int(13) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `file_content` mediumblob DEFAULT NULL,
  `attachment_type` int(4) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancy_id` (`vacancy_id`),
  CONSTRAINT `ohrm_job_vacancy_attachment_ibfk_1` FOREIGN KEY (`vacancy_id`) REFERENCES `ohrm_job_vacancy` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_job_vacancy_attachment`
--

LOCK TABLES `ohrm_job_vacancy_attachment` WRITE;
/*!40000 ALTER TABLE `ohrm_job_vacancy_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_job_vacancy_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_kpi`
--

DROP TABLE IF EXISTS `ohrm_kpi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_kpi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_title_code` varchar(10) DEFAULT NULL,
  `kpi_indicators` varchar(255) DEFAULT NULL,
  `min_rating` int(7) DEFAULT 0,
  `max_rating` int(7) DEFAULT 0,
  `default_kpi` smallint(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_kpi`
--

LOCK TABLES `ohrm_kpi` WRITE;
/*!40000 ALTER TABLE `ohrm_kpi` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_kpi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_language`
--

DROP TABLE IF EXISTS `ohrm_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_language`
--

LOCK TABLES `ohrm_language` WRITE;
/*!40000 ALTER TABLE `ohrm_language` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave`
--

DROP TABLE IF EXISTS `ohrm_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `length_hours` decimal(6,2) unsigned DEFAULT NULL,
  `length_days` decimal(6,4) unsigned DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `comments` varchar(256) DEFAULT NULL,
  `leave_request_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `emp_number` int(7) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration_type` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `leave_request_type_emp` (`leave_request_id`,`leave_type_id`,`emp_number`),
  KEY `request_status` (`leave_request_id`,`status`),
  KEY `leave_type_id` (`leave_type_id`),
  CONSTRAINT `ohrm_leave_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `ohrm_leave_request` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave`
--

LOCK TABLES `ohrm_leave` WRITE;
/*!40000 ALTER TABLE `ohrm_leave` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_adjustment`
--

DROP TABLE IF EXISTS `ohrm_leave_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_adjustment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `emp_number` int(7) NOT NULL,
  `no_of_days` decimal(19,15) NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `credited_date` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `adjustment_type` int(10) unsigned NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by_id` int(10) DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `emp_number` (`emp_number`),
  KEY `created_by_id` (`created_by_id`),
  KEY `adjustment_type` (`adjustment_type`),
  CONSTRAINT `ohrm_leave_adjustment_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_adjustment_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_adjustment_ibfk_3` FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_leave_adjustment_ibfk_4` FOREIGN KEY (`adjustment_type`) REFERENCES `ohrm_leave_entitlement_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_adjustment`
--

LOCK TABLES `ohrm_leave_adjustment` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_adjustment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_adjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_comment`
--

DROP TABLE IF EXISTS `ohrm_leave_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by_name` varchar(255) NOT NULL,
  `created_by_id` int(10) DEFAULT NULL,
  `created_by_emp_number` int(7) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_id` (`leave_id`),
  KEY `created_by_id` (`created_by_id`),
  KEY `created_by_emp_number` (`created_by_emp_number`),
  CONSTRAINT `ohrm_leave_comment_ibfk_1` FOREIGN KEY (`leave_id`) REFERENCES `ohrm_leave` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_comment_ibfk_2` FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_leave_comment_ibfk_3` FOREIGN KEY (`created_by_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_comment`
--

LOCK TABLES `ohrm_leave_comment` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_entitlement`
--

DROP TABLE IF EXISTS `ohrm_leave_entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_entitlement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `emp_number` int(7) NOT NULL,
  `no_of_days` decimal(19,15) NOT NULL,
  `days_used` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `leave_type_id` int(10) unsigned NOT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime DEFAULT NULL,
  `credited_date` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `entitlement_type` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by_id` int(10) DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `emp_number` (`emp_number`),
  KEY `entitlement_type` (`entitlement_type`),
  KEY `created_by_id` (`created_by_id`),
  CONSTRAINT `ohrm_leave_entitlement_ibfk_1` FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_entitlement_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_entitlement_ibfk_3` FOREIGN KEY (`entitlement_type`) REFERENCES `ohrm_leave_entitlement_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_entitlement_ibfk_4` FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_entitlement`
--

LOCK TABLES `ohrm_leave_entitlement` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_entitlement` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_entitlement_adjustment`
--

DROP TABLE IF EXISTS `ohrm_leave_entitlement_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_entitlement_adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(10) unsigned NOT NULL,
  `entitlement_id` int(10) unsigned NOT NULL,
  `length_days` decimal(4,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entitlement_id` (`entitlement_id`),
  KEY `adjustment_id` (`adjustment_id`),
  CONSTRAINT `ohrm_leave_entitlement_adjustment_ibfk_1` FOREIGN KEY (`entitlement_id`) REFERENCES `ohrm_leave_entitlement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_entitlement_adjustment_ibfk_2` FOREIGN KEY (`adjustment_id`) REFERENCES `ohrm_leave_adjustment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_entitlement_adjustment`
--

LOCK TABLES `ohrm_leave_entitlement_adjustment` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_entitlement_adjustment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_entitlement_adjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_entitlement_type`
--

DROP TABLE IF EXISTS `ohrm_leave_entitlement_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_entitlement_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_editable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_entitlement_type`
--

LOCK TABLES `ohrm_leave_entitlement_type` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_entitlement_type` DISABLE KEYS */;
INSERT INTO `ohrm_leave_entitlement_type` VALUES (1,'Added',1);
/*!40000 ALTER TABLE `ohrm_leave_entitlement_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_leave_entitlement`
--

DROP TABLE IF EXISTS `ohrm_leave_leave_entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_leave_entitlement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_id` int(11) NOT NULL,
  `entitlement_id` int(10) unsigned NOT NULL,
  `length_days` decimal(6,4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entitlement_id` (`entitlement_id`),
  KEY `leave_id` (`leave_id`),
  CONSTRAINT `ohrm_leave_leave_entitlement_ibfk_1` FOREIGN KEY (`entitlement_id`) REFERENCES `ohrm_leave_entitlement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_leave_entitlement_ibfk_2` FOREIGN KEY (`leave_id`) REFERENCES `ohrm_leave` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_leave_entitlement`
--

LOCK TABLES `ohrm_leave_leave_entitlement` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_leave_entitlement` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_leave_entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_period_history`
--

DROP TABLE IF EXISTS `ohrm_leave_period_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_period_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_period_start_month` int(11) NOT NULL,
  `leave_period_start_day` int(11) NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_period_history`
--

LOCK TABLES `ohrm_leave_period_history` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_period_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_period_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_request`
--

DROP TABLE IF EXISTS `ohrm_leave_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_id` int(10) unsigned NOT NULL,
  `date_applied` date NOT NULL,
  `emp_number` int(7) NOT NULL,
  `comments` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_number` (`emp_number`),
  KEY `leave_type_id` (`leave_type_id`),
  CONSTRAINT `ohrm_leave_request_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_request_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_request`
--

LOCK TABLES `ohrm_leave_request` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_request_comment`
--

DROP TABLE IF EXISTS `ohrm_leave_request_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_request_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_request_id` int(10) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by_name` varchar(255) NOT NULL,
  `created_by_id` int(10) DEFAULT NULL,
  `created_by_emp_number` int(7) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_request_id` (`leave_request_id`),
  KEY `created_by_id` (`created_by_id`),
  KEY `created_by_emp_number` (`created_by_emp_number`),
  CONSTRAINT `ohrm_leave_request_comment_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `ohrm_leave_request` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_leave_request_comment_ibfk_2` FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_leave_request_comment_ibfk_3` FOREIGN KEY (`created_by_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_request_comment`
--

LOCK TABLES `ohrm_leave_request_comment` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_request_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_request_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_status`
--

DROP TABLE IF EXISTS `ohrm_leave_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` smallint(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_status`
--

LOCK TABLES `ohrm_leave_status` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_status` DISABLE KEYS */;
INSERT INTO `ohrm_leave_status` VALUES (1,-1,'REJECTED'),(2,0,'CANCELLED'),(3,1,'PENDING APPROVAL'),(4,2,'SCHEDULED'),(5,3,'TAKEN'),(6,4,'WEEKEND'),(7,5,'HOLIDAY');
/*!40000 ALTER TABLE `ohrm_leave_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_leave_type`
--

DROP TABLE IF EXISTS `ohrm_leave_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_leave_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `exclude_in_reports_if_no_entitlement` tinyint(1) NOT NULL DEFAULT 0,
  `operational_country_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `operational_country_id` (`operational_country_id`),
  CONSTRAINT `ohrm_leave_type_ibfk_1` FOREIGN KEY (`operational_country_id`) REFERENCES `ohrm_operational_country` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_leave_type`
--

LOCK TABLES `ohrm_leave_type` WRITE;
/*!40000 ALTER TABLE `ohrm_leave_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_leave_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_license`
--

DROP TABLE IF EXISTS `ohrm_license`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_license`
--

LOCK TABLES `ohrm_license` WRITE;
/*!40000 ALTER TABLE `ohrm_license` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_license` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_location`
--

DROP TABLE IF EXISTS `ohrm_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(110) NOT NULL,
  `country_code` varchar(3) NOT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip_code` varchar(35) DEFAULT NULL,
  `phone` varchar(35) DEFAULT NULL,
  `fax` varchar(35) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country_code` (`country_code`),
  CONSTRAINT `ohrm_location_ibfk_1` FOREIGN KEY (`country_code`) REFERENCES `hs_hr_country` (`cou_code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_location`
--

LOCK TABLES `ohrm_location` WRITE;
/*!40000 ALTER TABLE `ohrm_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_login`
--

DROP TABLE IF EXISTS `ohrm_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_role_name` text NOT NULL,
  `user_role_predefined` tinyint(1) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_login`
--

LOCK TABLES `ohrm_login` WRITE;
/*!40000 ALTER TABLE `ohrm_login` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_membership`
--

DROP TABLE IF EXISTS `ohrm_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_membership` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_membership`
--

LOCK TABLES `ohrm_membership` WRITE;
/*!40000 ALTER TABLE `ohrm_membership` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_menu_item`
--

DROP TABLE IF EXISTS `ohrm_menu_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_title` varchar(255) NOT NULL,
  `screen_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` tinyint(4) NOT NULL,
  `order_hint` int(11) NOT NULL,
  `url_extras` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `screen_id` (`screen_id`),
  CONSTRAINT `ohrm_menu_item_ibfk_1` FOREIGN KEY (`screen_id`) REFERENCES `ohrm_screen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_menu_item`
--

LOCK TABLES `ohrm_menu_item` WRITE;
/*!40000 ALTER TABLE `ohrm_menu_item` DISABLE KEYS */;
INSERT INTO `ohrm_menu_item` VALUES (1,'Admin',74,NULL,1,100,NULL,1),(2,'User Management',NULL,1,2,100,NULL,1),(3,'Project Info',NULL,52,2,400,NULL,0),(4,'Customers',36,3,3,100,NULL,0),(5,'Projects',37,3,3,200,NULL,0),(6,'Job',NULL,1,2,300,NULL,1),(7,'Job Titles',23,6,3,100,NULL,1),(8,'Pay Grades',24,6,3,200,NULL,1),(9,'Employment Status',25,6,3,300,NULL,1),(10,'Job Categories',26,6,3,400,NULL,1),(11,'Work Shifts',27,6,3,500,NULL,1),(12,'Organization',NULL,1,2,400,NULL,1),(13,'General Information',20,12,3,100,NULL,1),(14,'Locations',21,12,3,200,NULL,1),(15,'Structure',22,12,3,300,NULL,1),(16,'Qualifications',NULL,1,2,500,NULL,1),(17,'Skills',28,16,3,100,NULL,1),(18,'Education',29,16,3,200,NULL,1),(19,'Licenses',30,16,3,300,NULL,1),(20,'Languages',31,16,3,400,NULL,1),(21,'Memberships',32,16,3,500,NULL,1),(22,'Nationalities',33,1,2,700,NULL,1),(23,'Configuration',NULL,1,2,900,NULL,1),(24,'Email Configuration',34,23,3,100,NULL,1),(25,'Email Subscriptions',35,23,3,200,NULL,1),(27,'Localization',38,23,3,300,NULL,1),(28,'Modules',39,23,3,400,NULL,1),(30,'PIM',75,NULL,1,200,NULL,1),(31,'Configuration',NULL,30,2,100,NULL,1),(32,'Optional Fields',40,31,3,100,NULL,1),(33,'Custom Fields',41,31,3,200,NULL,1),(34,'Data Import',42,31,3,300,NULL,1),(35,'Reporting Methods',43,31,3,400,NULL,1),(36,'Termination Reasons',44,31,3,500,NULL,1),(37,'Employee List',5,30,2,200,'/reset/1',1),(38,'Add Employee',4,30,2,300,NULL,1),(39,'Reports',45,30,2,400,'/reportGroup/3/reportType/PIM_DEFINED',1),(40,'My Info',46,NULL,1,700,NULL,1),(41,'Leave',68,NULL,1,300,NULL,1),(42,'Configure',NULL,41,2,500,NULL,0),(43,'Leave Period',47,42,3,100,NULL,0),(44,'Leave Types',7,42,3,200,NULL,0),(45,'Work Week',14,42,3,300,NULL,0),(46,'Holidays',11,42,3,400,NULL,0),(48,'Leave List',16,41,2,600,'/reset/1',0),(49,'Assign Leave',17,41,2,700,NULL,0),(50,'My Leave',48,41,2,200,'/reset/1',0),(51,'Apply',49,41,2,100,NULL,0),(52,'Time',67,NULL,1,400,NULL,1),(53,'Timesheets',NULL,52,2,100,NULL,1),(54,'My Timesheets',51,53,3,100,NULL,0),(55,'Employee Timesheets',52,53,3,200,NULL,0),(56,'Attendance',NULL,52,2,200,NULL,1),(57,'My Records',53,56,3,100,NULL,0),(58,'Punch In/Out',54,56,3,200,NULL,0),(59,'Employee Records',55,56,3,300,NULL,0),(60,'Configuration',56,56,3,400,NULL,0),(61,'Reports',NULL,52,2,300,NULL,1),(62,'Project Reports',57,61,3,100,'?reportId=1',0),(63,'Employee Reports',58,61,3,200,'?reportId=2',0),(64,'Attendance Summary',59,61,3,300,'?reportId=4',0),(65,'Recruitment',76,NULL,1,500,NULL,1),(66,'Candidates',60,65,2,100,NULL,1),(67,'Vacancies',61,65,2,200,NULL,1),(74,'Entitlements',NULL,41,2,300,NULL,0),(75,'Add Entitlements',72,74,3,100,NULL,0),(76,'My Entitlements',70,74,3,300,'/reset/1',0),(77,'Employee Entitlements',69,74,3,200,'/reset/1',0),(78,'Reports',NULL,41,2,400,NULL,0),(79,'Leave Entitlements and Usage Report',78,78,3,100,NULL,0),(80,'My Leave Entitlements and Usage Report',79,78,3,200,NULL,0),(81,'Users',1,2,3,100,NULL,1),(82,'Dashboard',103,NULL,1,800,NULL,1),(83,'Performance',NULL,NULL,1,700,'',1),(84,'Configure',NULL,83,2,100,'',1),(85,'Manage Reviews',NULL,83,2,200,'',1),(86,'KPIs',105,84,3,100,'',1),(87,'Manage Reviews',111,85,3,100,'',1),(88,'My Reviews',106,85,3,200,'',1),(89,'Review List',110,85,3,300,'',1),(90,'Trackers',112,84,3,200,NULL,1),(91,'Employee Trackers',113,83,2,800,NULL,1),(92,'My Trackers',114,83,2,700,NULL,1),(93,'Directory',116,NULL,1,1000,'/reset/1',1),(94,'Social Media Authentication',117,23,3,500,NULL,1),(95,'Register OAuth Client',118,23,3,600,NULL,1);
/*!40000 ALTER TABLE `ohrm_menu_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_module`
--

DROP TABLE IF EXISTS `ohrm_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_module`
--

LOCK TABLES `ohrm_module` WRITE;
/*!40000 ALTER TABLE `ohrm_module` DISABLE KEYS */;
INSERT INTO `ohrm_module` VALUES (1,'core',1),(2,'admin',1),(3,'pim',1),(4,'leave',1),(5,'time',1),(6,'attendance',1),(7,'recruitment',1),(8,'recruitmentApply',1),(9,'communication',1),(10,'dashboard',1),(11,'performance',1),(12,'directory',1);
/*!40000 ALTER TABLE `ohrm_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_module_default_page`
--

DROP TABLE IF EXISTS `ohrm_module_default_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_module_default_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `enable_class` varchar(100) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'lowest priority 0',
  PRIMARY KEY (`id`),
  KEY `user_role_id` (`user_role_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `ohrm_module_default_page_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `ohrm_user_role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_module_default_page_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `ohrm_module` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_module_default_page`
--

LOCK TABLES `ohrm_module_default_page` WRITE;
/*!40000 ALTER TABLE `ohrm_module_default_page` DISABLE KEYS */;
INSERT INTO `ohrm_module_default_page` VALUES (1,2,1,'admin/viewSystemUsers',NULL,20),(2,3,1,'pim/viewEmployeeList',NULL,20),(3,3,3,'pim/viewEmployeeList',NULL,10),(4,3,2,'pim/viewMyDetails',NULL,0),(5,4,1,'leave/viewLeaveList/reset/1',NULL,20),(6,4,3,'leave/viewLeaveList/reset/1',NULL,10),(7,4,2,'leave/viewMyLeaveList',NULL,0),(8,4,1,'leave/defineLeavePeriod','LeavePeriodDefinedHomePageEnabler',100),(9,4,2,'leave/showLeavePeriodNotDefinedWarning','LeavePeriodDefinedHomePageEnabler',90),(10,5,1,'time/viewEmployeeTimesheet',NULL,20),(11,5,2,'time/viewMyTimesheet',NULL,0),(12,5,1,'time/defineTimesheetPeriod','TimesheetPeriodDefinedHomePageEnabler',200),(13,5,2,'time/timesheetPeriodNotDefined','TimesheetPeriodDefinedHomePageEnabler',100),(14,7,1,'recruitment/viewCandidates',NULL,20),(15,7,5,'recruitment/viewCandidates',NULL,10),(16,7,6,'recruitment/viewCandidates',NULL,5);
/*!40000 ALTER TABLE `ohrm_module_default_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_nationality`
--

DROP TABLE IF EXISTS `ohrm_nationality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_nationality` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_nationality`
--

LOCK TABLES `ohrm_nationality` WRITE;
/*!40000 ALTER TABLE `ohrm_nationality` DISABLE KEYS */;
INSERT INTO `ohrm_nationality` VALUES (1,'Afghan'),(2,'Albanian'),(3,'Algerian'),(4,'American'),(5,'Andorran'),(6,'Angolan'),(7,'Antiguans'),(8,'Argentinean'),(9,'Armenian'),(10,'Australian'),(11,'Austrian'),(12,'Azerbaijani'),(13,'Bahamian'),(14,'Bahraini'),(15,'Bangladeshi'),(16,'Barbadian'),(17,'Barbudans'),(18,'Batswana'),(19,'Belarusian'),(20,'Belgian'),(21,'Belizean'),(22,'Beninese'),(23,'Bhutanese'),(24,'Bolivian'),(25,'Bosnian'),(26,'Brazilian'),(27,'British'),(28,'Bruneian'),(29,'Bulgarian'),(30,'Burkinabe'),(31,'Burmese'),(32,'Burundian'),(33,'Cambodian'),(34,'Cameroonian'),(35,'Canadian'),(36,'Cape Verdean'),(37,'Central African'),(38,'Chadian'),(39,'Chilean'),(40,'Chinese'),(41,'Colombian'),(42,'Comoran'),(43,'Congolese'),(44,'Costa Rican'),(45,'Croatian'),(46,'Cuban'),(47,'Cypriot'),(48,'Czech'),(49,'Danish'),(50,'Djibouti'),(51,'Dominican'),(52,'Dutch'),(53,'East Timorese'),(54,'Ecuadorean'),(55,'Egyptian'),(56,'Emirian'),(57,'Equatorial Guinean'),(58,'Eritrean'),(59,'Estonian'),(60,'Ethiopian'),(61,'Fijian'),(62,'Filipino'),(63,'Finnish'),(64,'French'),(65,'Gabonese'),(66,'Gambian'),(67,'Georgian'),(68,'German'),(69,'Ghanaian'),(70,'Greek'),(71,'Grenadian'),(72,'Guatemalan'),(73,'Guinea-Bissauan'),(74,'Guinean'),(75,'Guyanese'),(76,'Haitian'),(77,'Herzegovinian'),(78,'Honduran'),(79,'Hungarian'),(80,'I-Kiribati'),(81,'Icelander'),(82,'Indian'),(83,'Indonesian'),(84,'Iranian'),(85,'Iraqi'),(86,'Irish'),(87,'Israeli'),(88,'Italian'),(89,'Ivorian'),(90,'Jamaican'),(91,'Japanese'),(92,'Jordanian'),(93,'Kazakhstani'),(94,'Kenyan'),(95,'Kittian and Nevisian'),(96,'Kuwaiti'),(97,'Kyrgyz'),(98,'Laotian'),(99,'Latvian'),(100,'Lebanese'),(101,'Liberian'),(102,'Libyan'),(103,'Liechtensteiner'),(104,'Lithuanian'),(105,'Luxembourger'),(106,'Macedonian'),(107,'Malagasy'),(108,'Malawian'),(109,'Malaysian'),(110,'Maldivan'),(111,'Malian'),(112,'Maltese'),(113,'Marshallese'),(114,'Mauritanian'),(115,'Mauritian'),(116,'Mexican'),(117,'Micronesian'),(118,'Moldovan'),(119,'Monacan'),(120,'Mongolian'),(121,'Moroccan'),(122,'Mosotho'),(123,'Motswana'),(124,'Mozambican'),(125,'Namibian'),(126,'Nauruan'),(127,'Nepalese'),(128,'New Zealander'),(129,'Nicaraguan'),(130,'Nigerian'),(131,'Nigerien'),(132,'North Korean'),(133,'Northern Irish'),(134,'Norwegian'),(135,'Omani'),(136,'Pakistani'),(137,'Palauan'),(138,'Panamanian'),(139,'Papua New Guinean'),(140,'Paraguayan'),(141,'Peruvian'),(142,'Polish'),(143,'Portuguese'),(144,'Qatari'),(145,'Romanian'),(146,'Russian'),(147,'Rwandan'),(148,'Saint Lucian'),(149,'Salvadoran'),(150,'Samoan'),(151,'San Marinese'),(152,'Sao Tomean'),(153,'Saudi'),(154,'Scottish'),(155,'Senegalese'),(156,'Serbian'),(157,'Seychellois'),(158,'Sierra Leonean'),(159,'Singaporean'),(160,'Slovakian'),(161,'Slovenian'),(162,'Solomon Islander'),(163,'Somali'),(164,'South African'),(165,'South Korean'),(166,'Spanish'),(167,'Sri Lankan'),(168,'Sudanese'),(169,'Surinamer'),(170,'Swazi'),(171,'Swedish'),(172,'Swiss'),(173,'Syrian'),(174,'Taiwanese'),(175,'Tajik'),(176,'Tanzanian'),(177,'Thai'),(178,'Togolese'),(179,'Tongan'),(180,'Trinidadian or Tobagonian'),(181,'Tunisian'),(182,'Turkish'),(183,'Tuvaluan'),(184,'Ugandan'),(185,'Ukrainian'),(186,'Uruguayan'),(187,'Uzbekistani'),(188,'Venezuelan'),(189,'Vietnamese'),(190,'Welsh'),(191,'Yemenite'),(192,'Zambian'),(193,'Zimbabwean');
/*!40000 ALTER TABLE `ohrm_nationality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_oauth_access_token`
--

DROP TABLE IF EXISTS `ohrm_oauth_access_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_oauth_access_token` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_oauth_access_token`
--

LOCK TABLES `ohrm_oauth_access_token` WRITE;
/*!40000 ALTER TABLE `ohrm_oauth_access_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_oauth_access_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_oauth_authorization_code`
--

DROP TABLE IF EXISTS `ohrm_oauth_authorization_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_oauth_authorization_code` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_oauth_authorization_code`
--

LOCK TABLES `ohrm_oauth_authorization_code` WRITE;
/*!40000 ALTER TABLE `ohrm_oauth_authorization_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_oauth_authorization_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_oauth_client`
--

DROP TABLE IF EXISTS `ohrm_oauth_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_oauth_client` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) NOT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_oauth_client`
--

LOCK TABLES `ohrm_oauth_client` WRITE;
/*!40000 ALTER TABLE `ohrm_oauth_client` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_oauth_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_oauth_refresh_token`
--

DROP TABLE IF EXISTS `ohrm_oauth_refresh_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_oauth_refresh_token` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_oauth_refresh_token`
--

LOCK TABLES `ohrm_oauth_refresh_token` WRITE;
/*!40000 ALTER TABLE `ohrm_oauth_refresh_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_oauth_refresh_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_oauth_user`
--

DROP TABLE IF EXISTS `ohrm_oauth_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_oauth_user` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_oauth_user`
--

LOCK TABLES `ohrm_oauth_user` WRITE;
/*!40000 ALTER TABLE `ohrm_oauth_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_oauth_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_openid_provider`
--

DROP TABLE IF EXISTS `ohrm_openid_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_openid_provider` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(40) DEFAULT NULL,
  `provider_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_openid_provider`
--

LOCK TABLES `ohrm_openid_provider` WRITE;
/*!40000 ALTER TABLE `ohrm_openid_provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_openid_provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_openid_user_identity`
--

DROP TABLE IF EXISTS `ohrm_openid_user_identity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_openid_user_identity` (
  `user_id` int(10) DEFAULT NULL,
  `provider_id` int(10) DEFAULT NULL,
  `user_identity` varchar(255) DEFAULT NULL,
  KEY `ohrm_user_identity_ibfk_1` (`user_id`),
  KEY `ohrm_user_identity_ibfk_2` (`provider_id`),
  CONSTRAINT `ohrm_user_identity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ohrm_user_identity_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `ohrm_openid_provider` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_openid_user_identity`
--

LOCK TABLES `ohrm_openid_user_identity` WRITE;
/*!40000 ALTER TABLE `ohrm_openid_user_identity` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_openid_user_identity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_operational_country`
--

DROP TABLE IF EXISTS `ohrm_operational_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_operational_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ohrm_operational_country_hs_hr_country` (`country_code`),
  CONSTRAINT `fk_ohrm_operational_country_hs_hr_country` FOREIGN KEY (`country_code`) REFERENCES `hs_hr_country` (`cou_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_operational_country`
--

LOCK TABLES `ohrm_operational_country` WRITE;
/*!40000 ALTER TABLE `ohrm_operational_country` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_operational_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_organization_gen_info`
--

DROP TABLE IF EXISTS `ohrm_organization_gen_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_organization_gen_info` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `tax_id` varchar(30) DEFAULT NULL,
  `registration_number` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `zip_code` varchar(30) DEFAULT NULL,
  `street1` varchar(100) DEFAULT NULL,
  `street2` varchar(100) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_organization_gen_info`
--

LOCK TABLES `ohrm_organization_gen_info` WRITE;
/*!40000 ALTER TABLE `ohrm_organization_gen_info` DISABLE KEYS */;
INSERT INTO `ohrm_organization_gen_info` VALUES (1,'OHRM',NULL,NULL,NULL,NULL,NULL,'LK',NULL,NULL,NULL,NULL,NULL,NULL),(2,'OHRM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'OHRM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'OHRM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'OHRM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ohrm_organization_gen_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_pay_grade`
--

DROP TABLE IF EXISTS `ohrm_pay_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_pay_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_pay_grade`
--

LOCK TABLES `ohrm_pay_grade` WRITE;
/*!40000 ALTER TABLE `ohrm_pay_grade` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_pay_grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_pay_grade_currency`
--

DROP TABLE IF EXISTS `ohrm_pay_grade_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_pay_grade_currency` (
  `pay_grade_id` int(11) NOT NULL,
  `currency_id` varchar(6) NOT NULL DEFAULT '',
  `min_salary` double DEFAULT NULL,
  `max_salary` double DEFAULT NULL,
  PRIMARY KEY (`pay_grade_id`,`currency_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `ohrm_pay_grade_currency_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `hs_hr_currency_type` (`currency_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_pay_grade_currency_ibfk_2` FOREIGN KEY (`pay_grade_id`) REFERENCES `ohrm_pay_grade` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_pay_grade_currency`
--

LOCK TABLES `ohrm_pay_grade_currency` WRITE;
/*!40000 ALTER TABLE `ohrm_pay_grade_currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_pay_grade_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_performance_review`
--

DROP TABLE IF EXISTS `ohrm_performance_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_performance_review` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `status_id` int(7) DEFAULT NULL,
  `employee_number` int(7) DEFAULT NULL,
  `work_period_start` date DEFAULT NULL,
  `work_period_end` date DEFAULT NULL,
  `job_title_code` int(7) DEFAULT NULL,
  `department_id` int(7) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `activated_date` datetime DEFAULT NULL,
  `final_comment` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `final_rate` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_number` (`employee_number`),
  CONSTRAINT `ohrm_performance_review_ibfk_1` FOREIGN KEY (`employee_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_performance_review`
--

LOCK TABLES `ohrm_performance_review` WRITE;
/*!40000 ALTER TABLE `ohrm_performance_review` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_performance_review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_performance_track`
--

DROP TABLE IF EXISTS `ohrm_performance_track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_performance_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_number` int(7) NOT NULL,
  `tracker_name` varchar(200) NOT NULL,
  `added_date` timestamp NULL DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ohrm_performance_track_fk1_idx` (`emp_number`),
  KEY `ohrm_performance_track_fk2_idx` (`added_by`),
  CONSTRAINT `ohrm_performance_track_fk1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ohrm_performance_track_fk2` FOREIGN KEY (`added_by`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_performance_track`
--

LOCK TABLES `ohrm_performance_track` WRITE;
/*!40000 ALTER TABLE `ohrm_performance_track` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_performance_track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_performance_tracker_log`
--

DROP TABLE IF EXISTS `ohrm_performance_tracker_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_performance_tracker_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_track_id` int(11) DEFAULT NULL,
  `log` varchar(150) DEFAULT NULL,
  `comment` varchar(3000) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `added_date` timestamp NULL DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL,
  `reviewer_id` int(7) DEFAULT NULL,
  `achievement` varchar(45) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ohrm_performance_tracker_log_fk1_idx` (`performance_track_id`),
  KEY `ohrm_performance_tracker_log_fk2_idx` (`reviewer_id`),
  KEY `fk_ohrm_performance_tracker_log_1` (`user_id`),
  CONSTRAINT `fk_ohrm_performance_tracker_log_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ohrm_performance_tracker_log_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ohrm_performance_tracker_log_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_performance_tracker_log`
--

LOCK TABLES `ohrm_performance_tracker_log` WRITE;
/*!40000 ALTER TABLE `ohrm_performance_tracker_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_performance_tracker_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_performance_tracker_reviewer`
--

DROP TABLE IF EXISTS `ohrm_performance_tracker_reviewer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_performance_tracker_reviewer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_track_id` int(11) NOT NULL,
  `reviewer_id` int(7) NOT NULL,
  `added_date` timestamp NULL DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ohrm_performance_tracker_reviewer_fk1_idx` (`performance_track_id`),
  KEY `ohrm_performance_tracker_reviewer_fk2_idx` (`reviewer_id`),
  CONSTRAINT `ohrm_performance_tracker_reviewer_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ohrm_performance_tracker_reviewer_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_performance_tracker_reviewer`
--

LOCK TABLES `ohrm_performance_tracker_reviewer` WRITE;
/*!40000 ALTER TABLE `ohrm_performance_tracker_reviewer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_performance_tracker_reviewer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_plugin`
--

DROP TABLE IF EXISTS `ohrm_plugin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `version` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_plugin`
--

LOCK TABLES `ohrm_plugin` WRITE;
/*!40000 ALTER TABLE `ohrm_plugin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_plugin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_project`
--

DROP TABLE IF EXISTS `ohrm_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`project_id`,`customer_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_project`
--

LOCK TABLES `ohrm_project` WRITE;
/*!40000 ALTER TABLE `ohrm_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_project_activity`
--

DROP TABLE IF EXISTS `ohrm_project_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_project_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(110) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`activity_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `ohrm_project_activity_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `ohrm_project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_project_activity`
--

LOCK TABLES `ohrm_project_activity` WRITE;
/*!40000 ALTER TABLE `ohrm_project_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_project_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_project_admin`
--

DROP TABLE IF EXISTS `ohrm_project_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_project_admin` (
  `project_id` int(11) NOT NULL,
  `emp_number` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`emp_number`),
  KEY `emp_number` (`emp_number`),
  CONSTRAINT `ohrm_project_admin_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `ohrm_project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_project_admin_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_project_admin`
--

LOCK TABLES `ohrm_project_admin` WRITE;
/*!40000 ALTER TABLE `ohrm_project_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_project_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_report`
--

DROP TABLE IF EXISTS `ohrm_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_report` (
  `report_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `report_group_id` bigint(20) NOT NULL,
  `use_filter_field` tinyint(1) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `report_group_id` (`report_group_id`),
  CONSTRAINT `ohrm_report_ibfk_1` FOREIGN KEY (`report_group_id`) REFERENCES `ohrm_report_group` (`report_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_report`
--

LOCK TABLES `ohrm_report` WRITE;
/*!40000 ALTER TABLE `ohrm_report` DISABLE KEYS */;
INSERT INTO `ohrm_report` VALUES (1,'Project Report',1,1,NULL),(2,'Employee Report',1,1,NULL),(3,'Project Activity Details',1,1,NULL),(4,'Attendance Total Summary Report',2,0,NULL),(5,'PIM Sample Report',3,1,'PIM_DEFINED');
/*!40000 ALTER TABLE `ohrm_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_report_group`
--

DROP TABLE IF EXISTS `ohrm_report_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_report_group` (
  `report_group_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `core_sql` mediumtext NOT NULL,
  PRIMARY KEY (`report_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_report_group`
--

LOCK TABLES `ohrm_report_group` WRITE;
/*!40000 ALTER TABLE `ohrm_report_group` DISABLE KEYS */;
INSERT INTO `ohrm_report_group` VALUES (1,'timesheet','SELECT selectCondition FROM ohrm_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE whereCondition1) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = ohrm_project_activity.activity_id) LEFT JOIN ohrm_project ON (ohrm_project.project_id = ohrm_project_activity.project_id) LEFT JOIN hs_hr_employee ON (hs_hr_employee.emp_number = ohrm_timesheet_item.employee_id) LEFT JOIN ohrm_timesheet ON (ohrm_timesheet.timesheet_id = ohrm_timesheet_item.timesheet_id) LEFT JOIN ohrm_customer ON (ohrm_customer.customer_id = ohrm_project.customer_id) WHERE whereCondition2 groupByClause ORDER BY ohrm_customer.name, ohrm_project.name, ohrm_project_activity.name, hs_hr_employee.emp_lastname, hs_hr_employee.emp_firstname'),(2,'attendance','SELECT selectCondition FROM hs_hr_employee LEFT JOIN (SELECT * FROM ohrm_attendance_record WHERE ( ( ohrm_attendance_record.punch_in_user_time BETWEEN \"#@fromDate@,@1970-01-01@#\" AND #@\"toDate\"@,@CURDATE()@# ) AND ( ohrm_attendance_record.punch_out_user_time BETWEEN \"#@fromDate@,@1970-01-01@#\" AND #@\"toDate\"@,@CURDATE()@# ) ) ) AS ohrm_attendance_record ON (hs_hr_employee.emp_number = ohrm_attendance_record.employee_id) WHERE hs_hr_employee.emp_number = #@employeeId@,@hs_hr_employee.emp_number AND (hs_hr_employee.termination_id is null) @# AND (hs_hr_employee.job_title_code = #@\"jobTitle\")@,@hs_hr_employee.job_title_code OR hs_hr_employee.job_title_code is null)@# AND (hs_hr_employee.work_station IN (#@subUnit)@,@SELECT id FROM ohrm_subunit) OR hs_hr_employee.work_station is null@#) AND (hs_hr_employee.emp_status = #@\"employeeStatus\")@,@hs_hr_employee.emp_status OR hs_hr_employee.emp_status is null)@# groupByClause ORDER BY hs_hr_employee.emp_lastname, hs_hr_employee.emp_firstname'),(3,'pim','SELECT selectCondition FROM hs_hr_employee \n                    LEFT JOIN hs_hr_emp_emergency_contacts ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_emergency_contacts.emp_number) \n                    LEFT JOIN ohrm_subunit ON \n                        (hs_hr_employee.work_station = ohrm_subunit.id) \n                    LEFT JOIN ohrm_employment_status ON \n                        (hs_hr_employee.emp_status = ohrm_employment_status.id) \n                    LEFT JOIN ohrm_job_title ON\n                        (hs_hr_employee.job_title_code = ohrm_job_title.id)\n                    LEFT JOIN ohrm_job_category ON \n                        (hs_hr_employee.eeo_cat_code = ohrm_job_category.id) \n                    LEFT JOIN ohrm_nationality ON\n                        (hs_hr_employee.nation_code = ohrm_nationality.id)\n                    LEFT JOIN hs_hr_emp_dependents ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_dependents.emp_number)\n                    LEFT JOIN hs_hr_emp_locations AS emp_location ON\n                        (hs_hr_employee.emp_number = emp_location.emp_number)\n                    LEFT JOIN ohrm_location ON\n                        (emp_location.location_id = ohrm_location.id)\n                    LEFT JOIN hs_hr_emp_contract_extend ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_contract_extend.emp_number) \n                    LEFT JOIN hs_hr_emp_basicsalary ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) \n                    LEFT JOIN ohrm_pay_grade ON \n                        (hs_hr_emp_basicsalary.sal_grd_code = ohrm_pay_grade.id) \n                    LEFT JOIN hs_hr_currency_type ON \n                        (hs_hr_emp_basicsalary.currency_id = hs_hr_currency_type.currency_id) \n                    LEFT JOIN hs_hr_payperiod ON \n                        (hs_hr_emp_basicsalary.payperiod_code = hs_hr_payperiod.payperiod_code) \n                    LEFT JOIN hs_hr_emp_passport ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_passport.emp_number) \n                    LEFT JOIN hs_hr_emp_reportto AS subordinate_list ON \n                        (hs_hr_employee.emp_number = subordinate_list.erep_sup_emp_number) \n                    LEFT JOIN hs_hr_employee AS subordinate ON\n                        (subordinate.emp_number = subordinate_list.erep_sub_emp_number)\n                    LEFT JOIN ohrm_emp_reporting_method AS subordinate_reporting_method ON \n                        (subordinate_list.erep_reporting_mode = subordinate_reporting_method.reporting_method_id) \n                    LEFT JOIN hs_hr_emp_work_experience ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_work_experience.emp_number) \n                    LEFT JOIN ohrm_emp_education ON \n                        (hs_hr_employee.emp_number = ohrm_emp_education.emp_number) \n                    LEFT JOIN ohrm_education ON \n                        (ohrm_emp_education.education_id = ohrm_education.id) \n                    LEFT JOIN hs_hr_emp_skill ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) \n                    LEFT JOIN ohrm_skill ON \n                        (hs_hr_emp_skill.skill_id = ohrm_skill.id) \n                    LEFT JOIN hs_hr_emp_language ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) \n                    LEFT JOIN ohrm_language ON \n                        (hs_hr_emp_language.lang_id = ohrm_language.id) \n                    LEFT JOIN ohrm_emp_license ON \n                        (hs_hr_employee.emp_number = ohrm_emp_license.emp_number) \n                    LEFT JOIN ohrm_license ON \n                        (ohrm_emp_license.license_id = ohrm_license.id) \n                    LEFT JOIN hs_hr_emp_member_detail ON \n                        (hs_hr_employee.emp_number = hs_hr_emp_member_detail.emp_number) \n                    LEFT JOIN ohrm_membership ON\n                        (hs_hr_emp_member_detail.membship_code = ohrm_membership.id)\n                    LEFT JOIN hs_hr_country ON \n                        (hs_hr_employee.coun_code = hs_hr_country.cou_code) \n                    LEFT JOIN hs_hr_emp_directdebit ON \n                        (hs_hr_emp_basicsalary.id = hs_hr_emp_directdebit.salary_id) \n                    LEFT JOIN hs_hr_emp_reportto AS supervisor_list ON \n                        (hs_hr_employee.emp_number = supervisor_list.erep_sub_emp_number) \n                    LEFT JOIN hs_hr_employee AS supervisor ON\n                        (supervisor.emp_number = supervisor_list.erep_sup_emp_number)\n                    LEFT JOIN ohrm_emp_reporting_method AS supervisor_reporting_method ON \n                        (supervisor_list.erep_reporting_mode = supervisor_reporting_method.reporting_method_id) \n                    LEFT JOIN ohrm_emp_termination ON\n                        (hs_hr_employee.termination_id = ohrm_emp_termination.id)\n                    LEFT JOIN ohrm_emp_termination_reason ON\n                        (ohrm_emp_termination.reason_id = ohrm_emp_termination_reason.id)\n                WHERE hs_hr_employee.emp_number in (\n                    SELECT hs_hr_employee.emp_number FROM hs_hr_employee\n                        LEFT JOIN hs_hr_emp_basicsalary ON \n                            (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) \n                        LEFT JOIN ohrm_emp_education ON \n                            (hs_hr_employee.emp_number = ohrm_emp_education.emp_number) \n                        LEFT JOIN hs_hr_emp_skill ON \n                            (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) \n                        LEFT JOIN hs_hr_emp_language ON \n                            (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) \n                    WHERE whereCondition1\n                )\n                GROUP BY \n                     hs_hr_employee.emp_number,\n                     hs_hr_employee.emp_lastname,\n                     hs_hr_employee.emp_firstname,\n                     hs_hr_employee.emp_middle_name,\n                     hs_hr_employee.emp_birthday,\n                     ohrm_nationality.name,\n                     hs_hr_employee.emp_gender,\n                     hs_hr_employee.emp_marital_status,\n                     hs_hr_employee.emp_dri_lice_num,\n                     hs_hr_employee.emp_dri_lice_exp_date,\n                     hs_hr_employee.emp_street1,\n                     hs_hr_employee.emp_street2,\n                     hs_hr_employee.city_code,\n                     hs_hr_employee.provin_code,\n                     hs_hr_employee.emp_zipcode,\n                     hs_hr_country.cou_code,\n                     hs_hr_employee.emp_hm_telephone,\n                     hs_hr_employee.emp_mobile,\n                     hs_hr_employee.emp_work_telephone,\n                     hs_hr_employee.emp_work_email,\n                     hs_hr_employee.emp_oth_email\n\nORDER BY hs_hr_employee.emp_lastname\n');
/*!40000 ALTER TABLE `ohrm_report_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_reviewer`
--

DROP TABLE IF EXISTS `ohrm_reviewer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_reviewer` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `review_id` int(7) DEFAULT NULL,
  `employee_number` int(7) DEFAULT NULL,
  `status` int(7) DEFAULT NULL,
  `reviewer_group_id` int(7) DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  CONSTRAINT `ohrm_reviewer_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_reviewer`
--

LOCK TABLES `ohrm_reviewer` WRITE;
/*!40000 ALTER TABLE `ohrm_reviewer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_reviewer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_reviewer_group`
--

DROP TABLE IF EXISTS `ohrm_reviewer_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_reviewer_group` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `piority` int(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_reviewer_group`
--

LOCK TABLES `ohrm_reviewer_group` WRITE;
/*!40000 ALTER TABLE `ohrm_reviewer_group` DISABLE KEYS */;
INSERT INTO `ohrm_reviewer_group` VALUES (1,'Supervisor',1),(2,'Employee',2);
/*!40000 ALTER TABLE `ohrm_reviewer_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_reviewer_rating`
--

DROP TABLE IF EXISTS `ohrm_reviewer_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_reviewer_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` decimal(18,2) DEFAULT NULL,
  `kpi_id` int(7) DEFAULT NULL,
  `review_id` int(7) DEFAULT NULL,
  `reviewer_id` int(7) NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  KEY `reviewer_id` (`reviewer_id`),
  CONSTRAINT `ohrm_reviewer_rating_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `ohrm_reviewer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_reviewer_rating_ibfk_2` FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_reviewer_rating`
--

LOCK TABLES `ohrm_reviewer_rating` WRITE;
/*!40000 ALTER TABLE `ohrm_reviewer_rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_reviewer_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_role_user_selection_rule`
--

DROP TABLE IF EXISTS `ohrm_role_user_selection_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_role_user_selection_rule` (
  `user_role_id` int(10) NOT NULL,
  `selection_rule_id` int(10) NOT NULL,
  `configurable_params` text DEFAULT NULL,
  PRIMARY KEY (`user_role_id`,`selection_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_role_user_selection_rule`
--

LOCK TABLES `ohrm_role_user_selection_rule` WRITE;
/*!40000 ALTER TABLE `ohrm_role_user_selection_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_role_user_selection_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_screen`
--

DROP TABLE IF EXISTS `ohrm_screen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_screen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `module_id` int(11) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `ohrm_screen_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `ohrm_module` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_screen`
--

LOCK TABLES `ohrm_screen` WRITE;
/*!40000 ALTER TABLE `ohrm_screen` DISABLE KEYS */;
INSERT INTO `ohrm_screen` VALUES (1,'User List',2,'viewSystemUsers'),(2,'Add/Edit System User',2,'saveSystemUser'),(3,'Delete System Users',2,'deleteSystemUsers'),(4,'Add Employee',3,'addEmployee'),(5,'View Employee List',3,'viewEmployeeList'),(6,'Delete Employees',3,'deleteEmployees'),(7,'Leave Type List',4,'leaveTypeList'),(8,'Define Leave Type',4,'defineLeaveType'),(9,'Undelete Leave Type',4,'undeleteLeaveType'),(10,'Delete Leave Type',4,'deleteLeaveType'),(11,'View Holiday List',4,'viewHolidayList'),(12,'Define Holiday',4,'defineHoliday'),(13,'Delete Holiday',4,'deleteHoliday'),(14,'Define WorkWeek',4,'defineWorkWeek'),(16,'Leave List',4,'viewLeaveList'),(17,'Assign Leave',4,'assignLeave'),(18,'View Leave Summary',4,'viewLeaveSummary'),(19,'Save Leave Entitlements',4,'saveLeaveEntitlements'),(20,'General Information',2,'viewOrganizationGeneralInformation'),(21,'Location List',2,'viewLocations'),(22,'View Company Structure',2,'viewCompanyStructure'),(23,'Job Title List',2,'viewJobTitleList'),(24,'Pay Grade List',2,'viewPayGrades'),(25,'Employment Status List',2,'employmentStatus'),(26,'Job Category List',2,'jobCategory'),(27,'Work Shift List',2,'workShift'),(28,'Skill List',2,'viewSkills'),(29,'Education List',2,'viewEducation'),(30,'License List',2,'viewLicenses'),(31,'Language List',2,'viewLanguages'),(32,'Membership List',2,'membership'),(33,'Nationality List',2,'nationality'),(34,'Add/Edit Mail Configuration',2,'listMailConfiguration'),(35,'Notification List',2,'viewEmailNotification'),(36,'Customer List',2,'viewCustomers'),(37,'Project List',2,'viewProjects'),(38,'Localization',2,'localization'),(39,'Module Configuration',2,'viewModules'),(40,'Configure PIM',3,'configurePim'),(41,'Custom Field List',3,'listCustomFields'),(42,'Data Import',2,'pimCsvImport'),(43,'Reporting Method List',3,'viewReportingMethods'),(44,'Termination Reason List',3,'viewTerminationReasons'),(45,'PIM Reports List',1,'viewDefinedPredefinedReports'),(46,'View MyInfo',3,'viewMyDetails'),(47,'Define Leave Period',4,'defineLeavePeriod'),(48,'View My Leave List',4,'viewMyLeaveList'),(49,'Apply Leave',4,'applyLeave'),(50,'Define Timesheet Start Date',5,'defineTimesheetPeriod'),(51,'View My Timesheet',5,'viewMyTimesheet'),(52,'View Employee Timesheet',5,'viewEmployeeTimesheet'),(53,'View My Attendance',6,'viewMyAttendanceRecord'),(54,'Punch In/Out',6,'punchIn'),(55,'View Employee Attendance',6,'viewAttendanceRecord'),(56,'Attendance Configuration',6,'configure'),(57,'View Project Report Criteria',5,'displayProjectReportCriteria'),(58,'View Employee Report Criteria',5,'displayEmployeeReportCriteria'),(59,'View Attendance Report Criteria',5,'displayAttendanceSummaryReportCriteria'),(60,'Candidate List',7,'viewCandidates'),(61,'Vacancy List',7,'viewJobVacancy'),(67,'View Time Module',5,'viewTimeModule'),(68,'View Leave Module',4,'viewLeaveModule'),(69,'Leave Entitlements',4,'viewLeaveEntitlements'),(70,'My Leave Entitlements',4,'viewMyLeaveEntitlements'),(71,'Delete Leave Entitlements',4,'deleteLeaveEntitlements'),(72,'Add Leave Entitlement',4,'addLeaveEntitlement'),(73,'Edit Leave Entitlement',4,'editLeaveEntitlement'),(74,'View Admin Module',2,'viewAdminModule'),(75,'View PIM Module',3,'viewPimModule'),(76,'View Recruitment Module',7,'viewRecruitmentModule'),(78,'Leave Balance Report',4,'viewLeaveBalanceReport'),(79,'My Leave Balance Report',4,'viewMyLeaveBalanceReport'),(80,'Save Job Title',2,'saveJobTitle'),(81,'Delete Job Title',2,'deleteJobTitle'),(82,'Save Pay Grade',2,'payGrade'),(83,'Delete Pay Grade',2,'deletePayGrades'),(84,'Save Pay Grade Currency',2,'savePayGradeCurrency'),(85,'Delete Pay Grade Currency',2,'deletePayGradeCurrency'),(86,'Add Customer',2,'addCustomer'),(87,'Delete Customer',2,'deleteCustomer'),(88,'Save Project',2,'saveProject'),(89,'Delete Project',2,'deleteProject'),(90,'Add Project Adtivity',2,'addProjectActivity'),(91,'Delete Project Adtivity',2,'deleteProjectActivity'),(92,'Define PIM reports',1,'definePredefinedReport'),(93,'Display PIM reports',1,'displayPredefinedReport'),(94,'Add Job Vacancy',7,'addJobVacancy'),(95,'Delete Job Vacancy',7,'deleteJobVacancy'),(96,'Add Candidate',7,'addCandidate'),(97,'Delete Candidate',7,'deleteCandidateVacancies'),(98,'View Leave Request',4,'viewLeaveRequest'),(99,'Change Leave Status',4,'changeLeaveStatus'),(100,'Terminate Employment',3,'terminateEmployement'),(101,'View Attendance Summary Report',5,'displayAttendanceSummaryReport'),(102,'View Project Activity Details Report',5,'displayProjectActivityDetailsReport'),(103,'Dashboard',10,'index'),(104,'Save KPI',11,'saveKpi'),(105,'Saearch KPI',11,'searchKpi'),(106,'My Reviews',11,'myPerformanceReview'),(107,'Add Review',11,'saveReview'),(108,'Review Evaluate',11,'reviewEvaluate'),(109,'Review Evaluate By Admin',11,'reviewEvaluateByAdmin'),(110,'Search Evaluate Performance',11,'searchEvaluatePerformancReview'),(111,'Search Performance Review',11,'searchPerformancReview'),(112,'Manage_Trackers',11,'addPerformanceTracker'),(113,'Employee_Trackers',11,'viewEmployeePerformanceTrackerList'),(114,'My_Trackers',11,'viewMyPerformanceTrackerList'),(115,'Employee_Tracker_Logs',11,'addPerformanceTrackerLog'),(116,'Directory',12,'viewDirectory'),(117,'Manage OpenId',2,'openIdProvider'),(118,'Register OAuth Client',2,'registerOAuthClient');
/*!40000 ALTER TABLE `ohrm_screen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_selected_composite_display_field`
--

DROP TABLE IF EXISTS `ohrm_selected_composite_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_selected_composite_display_field` (
  `id` bigint(20) NOT NULL,
  `composite_display_field_id` bigint(20) NOT NULL,
  `report_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`,`composite_display_field_id`,`report_id`),
  KEY `composite_display_field_id` (`composite_display_field_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `ohrm_selected_composite_display_field_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `ohrm_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_composite_display_field_ibfk_2` FOREIGN KEY (`composite_display_field_id`) REFERENCES `ohrm_composite_display_field` (`composite_display_field_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_selected_composite_display_field`
--

LOCK TABLES `ohrm_selected_composite_display_field` WRITE;
/*!40000 ALTER TABLE `ohrm_selected_composite_display_field` DISABLE KEYS */;
INSERT INTO `ohrm_selected_composite_display_field` VALUES (1,1,3),(2,1,4),(3,2,2);
/*!40000 ALTER TABLE `ohrm_selected_composite_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_selected_display_field`
--

DROP TABLE IF EXISTS `ohrm_selected_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_selected_display_field` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `display_field_id` bigint(20) NOT NULL,
  `report_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`,`display_field_id`,`report_id`),
  KEY `display_field_id` (`display_field_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `ohrm_selected_display_field_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `ohrm_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_display_field_ibfk_2` FOREIGN KEY (`display_field_id`) REFERENCES `ohrm_display_field` (`display_field_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_selected_display_field`
--

LOCK TABLES `ohrm_selected_display_field` WRITE;
/*!40000 ALTER TABLE `ohrm_selected_display_field` DISABLE KEYS */;
INSERT INTO `ohrm_selected_display_field` VALUES (2,2,1),(4,8,2),(5,9,5),(6,10,5),(7,11,5),(8,12,5),(9,13,5),(10,14,5),(11,15,5),(13,17,5),(14,18,5),(15,19,5),(16,20,5),(17,21,5),(18,22,5),(19,23,5),(20,24,5),(21,25,5),(22,26,5),(23,27,5),(24,28,5),(25,29,5),(26,30,5),(27,31,5),(28,32,5),(29,33,5),(31,35,5),(32,36,5),(33,37,5),(34,38,5),(35,39,5),(36,40,5),(37,41,5),(38,42,5),(39,43,5),(40,44,5),(41,45,5),(43,47,5),(44,48,5),(45,49,5),(48,52,5),(49,53,5),(50,54,5),(51,55,5),(53,57,5),(54,58,5),(55,59,5),(56,60,5),(57,61,5),(58,62,5),(59,63,5),(60,64,5),(61,65,5),(62,66,5),(63,67,5),(64,68,5),(65,69,5),(66,70,5),(67,71,5),(68,72,5),(69,73,5),(70,74,5),(71,75,5),(72,76,5),(73,77,5),(74,78,5),(76,80,5),(77,81,5),(78,82,5),(79,83,5),(80,84,5),(81,85,5),(82,86,5),(83,87,5),(84,88,5),(85,89,5),(86,90,5),(87,91,5),(88,92,5),(89,93,5),(90,94,5),(91,95,5),(93,97,5);
/*!40000 ALTER TABLE `ohrm_selected_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_selected_display_field_group`
--

DROP TABLE IF EXISTS `ohrm_selected_display_field_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_selected_display_field_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `display_field_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `display_field_group_id` (`display_field_group_id`),
  CONSTRAINT `ohrm_selected_display_field_group_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `ohrm_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_display_field_group_ibfk_2` FOREIGN KEY (`display_field_group_id`) REFERENCES `ohrm_display_field_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_selected_display_field_group`
--

LOCK TABLES `ohrm_selected_display_field_group` WRITE;
/*!40000 ALTER TABLE `ohrm_selected_display_field_group` DISABLE KEYS */;
INSERT INTO `ohrm_selected_display_field_group` VALUES (1,5,1),(2,5,2),(3,5,3),(4,5,4),(5,5,5),(6,5,6),(7,5,7),(8,5,8),(9,5,9),(10,5,10),(11,5,11),(12,5,12),(13,5,13),(14,5,14),(15,5,15);
/*!40000 ALTER TABLE `ohrm_selected_display_field_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_selected_filter_field`
--

DROP TABLE IF EXISTS `ohrm_selected_filter_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_selected_filter_field` (
  `report_id` bigint(20) NOT NULL,
  `filter_field_id` bigint(20) NOT NULL,
  `filter_field_order` bigint(20) NOT NULL,
  `value1` varchar(255) DEFAULT NULL,
  `value2` varchar(255) DEFAULT NULL,
  `where_condition` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`report_id`,`filter_field_id`),
  KEY `report_id` (`report_id`),
  KEY `filter_field_id` (`filter_field_id`),
  CONSTRAINT `ohrm_selected_filter_field_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `ohrm_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_filter_field_ibfk_2` FOREIGN KEY (`filter_field_id`) REFERENCES `ohrm_filter_field` (`filter_field_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_selected_filter_field`
--

LOCK TABLES `ohrm_selected_filter_field` WRITE;
/*!40000 ALTER TABLE `ohrm_selected_filter_field` DISABLE KEYS */;
INSERT INTO `ohrm_selected_filter_field` VALUES (1,1,1,NULL,NULL,NULL,'Runtime'),(1,3,2,NULL,NULL,NULL,'Runtime'),(1,7,3,NULL,NULL,NULL,'Runtime'),(1,21,4,'0',NULL,'=','Predefined'),(2,3,4,NULL,NULL,NULL,'Runtime'),(2,4,1,NULL,NULL,NULL,'Runtime'),(2,5,3,NULL,NULL,NULL,'Runtime'),(2,6,2,NULL,NULL,NULL,'Runtime'),(2,7,5,NULL,NULL,NULL,'Runtime'),(3,3,2,NULL,NULL,NULL,'Runtime'),(3,5,1,NULL,NULL,NULL,'Runtime'),(3,7,3,NULL,NULL,NULL,'Runtime'),(5,22,1,NULL,NULL,'IS NULL','Predefined');
/*!40000 ALTER TABLE `ohrm_selected_filter_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_selected_group_field`
--

DROP TABLE IF EXISTS `ohrm_selected_group_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_selected_group_field` (
  `group_field_id` bigint(20) NOT NULL,
  `summary_display_field_id` bigint(20) NOT NULL,
  `report_id` bigint(20) NOT NULL,
  PRIMARY KEY (`group_field_id`,`summary_display_field_id`,`report_id`),
  KEY `group_field_id` (`group_field_id`),
  KEY `summary_display_field_id` (`summary_display_field_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `ohrm_selected_group_field_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `ohrm_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_group_field_ibfk_2` FOREIGN KEY (`group_field_id`) REFERENCES `ohrm_group_field` (`group_field_id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_selected_group_field_ibfk_3` FOREIGN KEY (`summary_display_field_id`) REFERENCES `ohrm_summary_display_field` (`summary_display_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_selected_group_field`
--

LOCK TABLES `ohrm_selected_group_field` WRITE;
/*!40000 ALTER TABLE `ohrm_selected_group_field` DISABLE KEYS */;
INSERT INTO `ohrm_selected_group_field` VALUES (1,1,1),(1,1,2),(2,1,3),(2,2,4);
/*!40000 ALTER TABLE `ohrm_selected_group_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_skill`
--

DROP TABLE IF EXISTS `ohrm_skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_skill`
--

LOCK TABLES `ohrm_skill` WRITE;
/*!40000 ALTER TABLE `ohrm_skill` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_subunit`
--

DROP TABLE IF EXISTS `ohrm_subunit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_subunit` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `unit_id` varchar(100) DEFAULT NULL,
  `description` varchar(400) DEFAULT NULL,
  `lft` smallint(6) unsigned DEFAULT NULL,
  `rgt` smallint(6) unsigned DEFAULT NULL,
  `level` smallint(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_subunit`
--

LOCK TABLES `ohrm_subunit` WRITE;
/*!40000 ALTER TABLE `ohrm_subunit` DISABLE KEYS */;
INSERT INTO `ohrm_subunit` VALUES (1,'Organization','','',1,2,0);
/*!40000 ALTER TABLE `ohrm_subunit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_summary_display_field`
--

DROP TABLE IF EXISTS `ohrm_summary_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_summary_display_field` (
  `summary_display_field_id` bigint(20) NOT NULL,
  `function` varchar(1000) NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_alias` varchar(255) DEFAULT NULL,
  `is_sortable` varchar(10) NOT NULL,
  `sort_order` varchar(255) DEFAULT NULL,
  `sort_field` varchar(255) DEFAULT NULL,
  `element_type` varchar(255) NOT NULL,
  `element_property` varchar(1000) NOT NULL,
  `width` varchar(255) NOT NULL,
  `is_exportable` varchar(10) DEFAULT NULL,
  `text_alignment_style` varchar(20) DEFAULT NULL,
  `is_value_list` tinyint(1) NOT NULL DEFAULT 0,
  `display_field_group_id` int(10) unsigned DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`summary_display_field_id`),
  KEY `display_field_group_id` (`display_field_group_id`),
  CONSTRAINT `ohrm_summary_display_field_ibfk_1` FOREIGN KEY (`display_field_group_id`) REFERENCES `ohrm_display_field_group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_summary_display_field`
--

LOCK TABLES `ohrm_summary_display_field` WRITE;
/*!40000 ALTER TABLE `ohrm_summary_display_field` DISABLE KEYS */;
INSERT INTO `ohrm_summary_display_field` VALUES (1,'ROUND(COALESCE(sum(duration)/3600, 0),2)','Time (Hours)','totalduration','false',NULL,NULL,'label','<xml><getter>totalduration</getter></xml>','100','false','right',0,NULL,NULL),(2,'ROUND(COALESCE(sum(TIMESTAMPDIFF(SECOND , ohrm_attendance_record.punch_in_utc_time , ohrm_attendance_record.punch_out_utc_time))/3600, 0),2)','Time (Hours)','totalduration','false',NULL,NULL,'label','<xml><getter>totalduration</getter></xml>','100','false','right',0,NULL,NULL);
/*!40000 ALTER TABLE `ohrm_summary_display_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_timesheet`
--

DROP TABLE IF EXISTS `ohrm_timesheet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_timesheet` (
  `timesheet_id` bigint(20) NOT NULL,
  `state` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  PRIMARY KEY (`timesheet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_timesheet`
--

LOCK TABLES `ohrm_timesheet` WRITE;
/*!40000 ALTER TABLE `ohrm_timesheet` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_timesheet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_timesheet_action_log`
--

DROP TABLE IF EXISTS `ohrm_timesheet_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_timesheet_action_log` (
  `timesheet_action_log_id` bigint(20) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `date_time` date NOT NULL,
  `performed_by` int(20) NOT NULL,
  `timesheet_id` bigint(20) NOT NULL,
  PRIMARY KEY (`timesheet_action_log_id`),
  KEY `timesheet_id` (`timesheet_id`),
  KEY `performed_by` (`performed_by`),
  CONSTRAINT `ohrm_timesheet_action_log_ibfk_1` FOREIGN KEY (`performed_by`) REFERENCES `ohrm_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_timesheet_action_log`
--

LOCK TABLES `ohrm_timesheet_action_log` WRITE;
/*!40000 ALTER TABLE `ohrm_timesheet_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_timesheet_action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_timesheet_item`
--

DROP TABLE IF EXISTS `ohrm_timesheet_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_timesheet_item` (
  `timesheet_item_id` bigint(20) NOT NULL,
  `timesheet_id` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `duration` bigint(20) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `project_id` bigint(20) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  `activity_id` bigint(20) NOT NULL,
  PRIMARY KEY (`timesheet_item_id`),
  KEY `timesheet_id` (`timesheet_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_timesheet_item`
--

LOCK TABLES `ohrm_timesheet_item` WRITE;
/*!40000 ALTER TABLE `ohrm_timesheet_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_timesheet_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_upgrade_history`
--

DROP TABLE IF EXISTS `ohrm_upgrade_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_upgrade_history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `start_version` varchar(30) DEFAULT NULL,
  `end_version` varchar(30) DEFAULT NULL,
  `start_increment` int(11) NOT NULL,
  `end_increment` int(11) NOT NULL,
  `upgraded_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_upgrade_history`
--

LOCK TABLES `ohrm_upgrade_history` WRITE;
/*!40000 ALTER TABLE `ohrm_upgrade_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_upgrade_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_user`
--

DROP TABLE IF EXISTS `ohrm_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_role_id` int(10) NOT NULL,
  `emp_number` int(13) DEFAULT NULL,
  `user_name` varchar(40) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_entered` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_user_id` int(10) DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `user_role_id` (`user_role_id`),
  KEY `emp_number` (`emp_number`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `ohrm_user_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_user_ibfk_2` FOREIGN KEY (`user_role_id`) REFERENCES `ohrm_user_role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_user`
--

LOCK TABLES `ohrm_user` WRITE;
/*!40000 ALTER TABLE `ohrm_user` DISABLE KEYS */;
INSERT INTO `ohrm_user` VALUES (2,1,1,'admin','$2y$12$ZU.gDYwQ1L6F6VSnUSxuZeHXAOTejXFh2Eb.vi8bEdnbyMQcVKTG6',0,1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ohrm_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_user_role`
--

DROP TABLE IF EXISTS `ohrm_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_user_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `is_assignable` tinyint(1) DEFAULT 0,
  `is_predefined` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_user_role`
--

LOCK TABLES `ohrm_user_role` WRITE;
/*!40000 ALTER TABLE `ohrm_user_role` DISABLE KEYS */;
INSERT INTO `ohrm_user_role` VALUES (1,'Admin','Admin',1,1),(2,'ESS','ESS',1,1),(3,'Supervisor','Supervisor',0,1),(4,'ProjectAdmin','ProjectAdmin',0,1),(5,'Interviewer','Interviewer',0,1),(6,'HiringManager','HiringManager',0,1),(7,'Reviewer','Reviewer',0,1);
/*!40000 ALTER TABLE `ohrm_user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_user_role_data_group`
--

DROP TABLE IF EXISTS `ohrm_user_role_data_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_user_role_data_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role_id` int(11) DEFAULT NULL,
  `data_group_id` int(11) DEFAULT NULL,
  `can_read` tinyint(4) DEFAULT NULL,
  `can_create` tinyint(4) DEFAULT NULL,
  `can_update` tinyint(4) DEFAULT NULL,
  `can_delete` tinyint(4) DEFAULT NULL,
  `self` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_role_id` (`user_role_id`),
  KEY `data_group_id` (`data_group_id`),
  CONSTRAINT `ohrm_user_role_data_group_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `ohrm_user_role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_user_role_data_group_ibfk_2` FOREIGN KEY (`data_group_id`) REFERENCES `ohrm_data_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_user_role_data_group`
--

LOCK TABLES `ohrm_user_role_data_group` WRITE;
/*!40000 ALTER TABLE `ohrm_user_role_data_group` DISABLE KEYS */;
INSERT INTO `ohrm_user_role_data_group` VALUES (1,1,1,1,NULL,1,NULL,0),(2,1,2,1,1,1,1,0),(3,1,3,1,NULL,1,NULL,0),(4,1,4,1,NULL,1,NULL,0),(5,1,5,1,1,1,1,0),(6,1,6,1,NULL,1,NULL,0),(7,1,7,1,1,1,1,0),(8,1,8,1,1,1,1,0),(9,1,9,1,NULL,1,NULL,0),(10,1,10,1,1,1,1,0),(11,1,11,1,1,1,1,0),(12,1,12,1,NULL,1,NULL,0),(13,1,13,1,1,1,1,0),(14,1,14,1,1,1,1,0),(15,1,15,1,NULL,1,NULL,0),(16,1,16,1,NULL,1,NULL,0),(17,1,17,1,1,1,1,0),(18,1,18,1,NULL,1,NULL,0),(19,1,19,1,1,1,1,0),(20,1,20,1,1,1,1,0),(21,1,21,1,NULL,1,NULL,0),(22,1,22,1,NULL,1,NULL,0),(23,1,23,1,1,1,1,0),(24,1,24,1,NULL,1,NULL,0),(25,1,25,1,1,1,1,0),(26,1,26,1,1,1,1,0),(27,1,27,1,1,1,1,0),(28,1,28,1,NULL,1,NULL,0),(29,1,29,1,1,1,1,0),(30,1,30,1,1,1,1,0),(31,1,31,1,1,1,1,0),(32,1,32,1,1,1,1,0),(33,1,33,1,1,1,1,0),(34,1,34,1,1,1,1,0),(35,1,35,1,NULL,1,NULL,0),(36,1,36,1,1,1,1,0),(37,1,37,1,1,1,1,0),(38,1,38,1,NULL,1,NULL,0),(39,1,39,1,NULL,1,1,0),(40,1,40,1,1,1,1,0),(41,1,41,1,NULL,NULL,NULL,0),(42,1,40,1,1,1,1,1),(43,2,1,1,NULL,1,NULL,1),(44,2,2,1,1,1,1,1),(45,2,3,1,NULL,1,NULL,1),(46,2,4,1,NULL,1,NULL,1),(47,2,5,1,1,1,1,1),(48,2,6,1,NULL,1,NULL,1),(49,2,7,1,1,1,1,1),(50,2,8,1,1,1,1,1),(51,2,9,1,NULL,1,NULL,1),(52,2,10,1,1,1,1,1),(53,2,11,1,1,1,1,1),(54,2,12,1,NULL,1,NULL,1),(55,2,13,1,1,1,1,1),(56,2,14,1,1,1,1,1),(57,2,15,1,NULL,1,NULL,1),(58,2,16,1,NULL,NULL,NULL,1),(59,2,17,1,0,0,0,1),(60,2,18,1,0,0,0,1),(61,2,19,1,NULL,NULL,NULL,1),(62,2,20,1,0,0,0,1),(63,2,21,1,0,0,0,1),(64,2,22,1,NULL,NULL,NULL,1),(65,2,23,1,0,0,0,1),(66,2,24,1,0,0,0,1),(67,2,25,1,NULL,NULL,NULL,1),(68,2,26,1,NULL,NULL,NULL,1),(69,2,27,1,0,0,0,1),(70,2,28,1,0,0,0,1),(71,2,29,1,1,1,1,1),(72,2,30,1,1,1,1,1),(73,2,31,1,1,1,1,1),(74,2,32,1,1,1,1,1),(75,2,33,1,1,1,1,1),(76,2,34,1,1,1,1,1),(77,2,35,1,NULL,1,NULL,1),(78,2,36,1,1,1,1,1),(79,2,37,1,1,1,1,1),(80,2,38,1,NULL,1,NULL,1),(81,2,39,1,NULL,1,1,1),(82,2,40,1,0,0,0,1),(83,2,41,1,NULL,NULL,NULL,1),(84,3,1,1,NULL,1,NULL,0),(85,3,2,1,1,1,1,0),(86,3,3,1,NULL,1,NULL,0),(87,3,4,1,NULL,1,NULL,0),(88,3,5,1,1,1,1,0),(89,3,6,1,NULL,1,NULL,0),(90,3,7,1,1,1,1,0),(91,3,8,1,1,1,1,0),(92,3,9,1,NULL,1,NULL,0),(93,3,10,1,1,1,1,0),(94,3,11,1,1,1,1,0),(95,3,12,1,NULL,1,NULL,0),(96,3,13,1,1,1,1,0),(97,3,14,1,1,1,1,0),(98,3,15,1,NULL,1,NULL,0),(99,3,16,1,NULL,NULL,NULL,0),(100,3,17,1,0,0,0,0),(101,3,18,1,0,0,0,0),(102,3,19,0,0,0,0,0),(103,3,20,0,0,0,0,0),(104,3,21,0,0,0,0,0),(105,3,22,1,NULL,NULL,NULL,0),(106,3,23,1,0,0,0,0),(107,3,24,1,0,0,0,0),(108,3,25,1,NULL,NULL,NULL,0),(109,3,26,1,NULL,NULL,NULL,0),(110,3,27,1,0,0,0,0),(111,3,28,1,0,0,0,0),(112,3,29,1,1,1,1,0),(113,3,30,1,1,1,1,0),(114,3,31,1,1,1,1,0),(115,3,32,1,1,1,1,0),(116,3,33,1,1,1,1,0),(117,3,34,1,1,1,1,0),(118,3,35,1,NULL,1,NULL,0),(119,3,36,1,1,1,1,0),(120,3,37,1,1,1,1,0),(121,3,38,1,NULL,1,NULL,0),(122,3,39,1,NULL,1,1,0),(123,3,40,1,0,0,0,0),(124,3,41,1,NULL,NULL,NULL,0),(125,3,1,1,NULL,1,NULL,1),(126,3,2,1,1,1,1,1),(127,3,3,1,NULL,1,NULL,1),(128,3,4,1,NULL,1,NULL,1),(129,3,5,1,1,1,1,1),(130,3,6,1,NULL,1,NULL,1),(131,3,7,1,1,1,1,1),(132,3,8,1,1,1,1,1),(133,3,9,1,NULL,1,NULL,1),(134,3,10,1,1,1,1,1),(135,3,11,1,1,1,1,1),(136,3,12,1,NULL,1,NULL,1),(137,3,13,1,1,1,1,1),(138,3,14,1,1,1,1,1),(139,3,15,1,NULL,1,NULL,1),(140,3,16,1,NULL,NULL,NULL,1),(141,3,17,1,0,0,0,1),(142,3,18,1,0,0,0,1),(143,3,19,1,0,0,0,1),(144,3,20,1,0,0,0,1),(145,3,21,1,0,0,0,1),(146,3,22,1,NULL,NULL,NULL,1),(147,3,23,1,0,0,0,1),(148,3,24,1,0,0,0,1),(149,3,25,1,NULL,NULL,NULL,1),(150,3,26,1,NULL,NULL,NULL,1),(151,3,27,1,0,0,0,1),(152,3,28,1,0,0,0,1),(153,3,29,1,1,1,1,1),(154,3,30,1,1,1,1,1),(155,3,31,1,1,1,1,1),(156,3,32,1,1,1,1,1),(157,3,33,1,1,1,1,1),(158,3,34,1,1,1,1,1),(159,3,35,1,NULL,1,NULL,1),(160,3,36,1,1,1,1,1),(161,3,37,1,1,1,1,1),(162,3,38,1,NULL,1,NULL,1),(163,3,39,1,NULL,1,1,1),(164,3,40,1,0,0,0,1),(165,3,41,1,NULL,NULL,NULL,1),(166,1,42,1,1,1,1,0),(167,2,42,0,0,0,0,0),(168,3,42,0,0,0,0,0),(169,1,43,1,1,1,1,0),(170,2,43,0,0,0,0,0),(171,3,43,0,0,0,0,0),(172,1,44,1,1,1,1,0),(173,2,44,0,0,0,0,0),(174,3,44,0,0,0,0,0),(175,1,45,1,1,1,1,0),(176,2,45,0,0,0,0,0),(177,3,45,0,0,0,0,0),(178,4,45,1,0,1,0,0),(179,1,46,1,1,1,1,0),(180,2,46,0,0,0,0,0),(181,3,46,0,0,0,0,0),(182,1,47,1,NULL,1,NULL,0),(183,2,47,0,0,0,0,0),(184,3,47,0,0,0,0,0),(185,1,48,1,0,0,0,0),(186,2,48,0,0,0,0,0),(187,2,48,1,0,0,0,1),(188,3,48,1,0,0,0,0),(189,1,49,1,0,0,0,0),(190,2,49,0,0,0,0,0),(191,3,49,0,0,0,0,0),(192,4,49,1,0,0,0,0),(193,1,50,1,0,0,0,0),(194,2,50,0,0,0,0,0),(195,3,50,1,0,0,0,0),(196,1,51,1,0,0,0,0),(197,2,51,0,0,0,0,0),(198,3,51,1,0,0,0,0),(199,1,52,1,NULL,1,NULL,0),(200,2,52,0,0,0,0,0),(201,3,52,0,0,0,0,0),(202,1,53,1,1,1,1,0),(203,2,53,0,0,0,0,0),(204,3,53,0,0,0,0,0),(205,1,54,1,0,1,0,0),(206,2,54,0,0,0,0,0),(207,3,54,0,0,0,0,0),(208,1,55,1,1,1,1,0),(209,2,55,0,0,0,0,0),(210,3,55,0,0,0,0,0),(211,1,56,1,1,1,1,0),(212,2,56,0,0,0,0,0),(213,3,56,0,0,0,0,0),(214,1,57,1,1,1,1,0),(215,6,57,1,1,1,1,0),(216,5,57,1,0,1,0,0),(217,1,58,1,0,0,0,0),(218,2,58,0,0,0,0,0),(219,2,58,1,0,0,0,1),(220,3,58,1,0,0,0,0),(221,1,59,1,0,0,0,0),(222,2,59,1,0,0,0,1),(223,3,59,1,0,0,0,0),(224,1,60,0,1,0,0,0),(225,2,60,0,1,0,0,1),(226,3,60,0,1,0,0,0);
/*!40000 ALTER TABLE `ohrm_user_role_data_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_user_role_screen`
--

DROP TABLE IF EXISTS `ohrm_user_role_screen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_user_role_screen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role_id` int(11) NOT NULL,
  `screen_id` int(11) NOT NULL,
  `can_read` tinyint(1) NOT NULL DEFAULT 0,
  `can_create` tinyint(1) NOT NULL DEFAULT 0,
  `can_update` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_role_id` (`user_role_id`),
  KEY `screen_id` (`screen_id`),
  CONSTRAINT `ohrm_user_role_screen_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `ohrm_user_role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ohrm_user_role_screen_ibfk_2` FOREIGN KEY (`screen_id`) REFERENCES `ohrm_screen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_user_role_screen`
--

LOCK TABLES `ohrm_user_role_screen` WRITE;
/*!40000 ALTER TABLE `ohrm_user_role_screen` DISABLE KEYS */;
INSERT INTO `ohrm_user_role_screen` VALUES (1,1,1,1,1,1,1),(2,1,2,1,1,1,1),(3,2,2,0,0,0,0),(4,3,2,0,0,0,0),(5,1,3,1,1,1,1),(6,2,3,0,0,0,0),(7,3,3,0,0,0,0),(8,1,4,1,1,1,1),(9,1,5,1,1,1,1),(10,3,5,1,0,0,0),(11,1,6,1,0,0,1),(12,1,7,1,1,1,1),(13,1,8,1,1,1,1),(14,1,9,1,1,1,1),(15,1,10,1,1,1,1),(16,1,11,1,1,1,1),(17,1,12,1,1,1,1),(18,1,13,1,1,1,1),(19,1,14,1,1,1,1),(20,1,16,1,1,1,0),(21,3,16,1,1,1,0),(22,1,17,1,1,1,0),(23,3,17,1,1,1,0),(24,1,18,1,1,1,0),(25,2,18,1,0,0,0),(26,3,18,1,0,0,0),(27,1,19,1,1,1,1),(28,1,20,1,1,1,1),(29,1,21,1,1,1,1),(30,1,22,1,1,1,1),(31,1,23,1,1,1,1),(32,1,24,1,1,1,1),(33,1,25,1,1,1,1),(34,1,26,1,1,1,1),(35,1,27,1,1,1,1),(36,1,28,1,1,1,1),(37,1,29,1,1,1,1),(38,1,30,1,1,1,1),(39,1,31,1,1,1,1),(40,1,32,1,1,1,1),(41,1,33,1,1,1,1),(42,1,34,1,1,1,1),(43,1,35,1,1,1,1),(44,1,36,1,1,1,1),(45,1,37,1,1,1,1),(46,4,37,1,0,0,0),(47,1,38,1,1,1,1),(48,1,39,1,1,1,1),(49,1,40,1,1,1,1),(50,1,41,1,1,1,1),(51,1,42,1,1,1,1),(52,1,43,1,1,1,1),(53,1,44,1,1,1,1),(54,1,45,1,1,1,1),(55,2,46,1,1,1,1),(56,1,47,1,1,1,1),(57,2,48,1,1,1,0),(58,2,49,1,1,1,1),(59,1,50,1,1,1,1),(60,2,51,1,1,1,1),(61,1,52,1,1,1,1),(62,3,52,1,1,1,1),(63,2,53,1,1,0,0),(64,2,54,1,1,1,1),(65,1,55,1,1,0,1),(66,3,55,1,1,0,0),(67,1,56,1,1,1,1),(68,1,57,1,1,1,1),(69,4,57,1,1,1,1),(70,1,58,1,1,1,1),(71,3,58,1,1,1,1),(72,1,59,1,1,1,1),(73,3,59,1,1,1,1),(74,1,60,1,1,1,1),(75,6,60,1,1,1,1),(76,5,60,1,0,1,0),(77,1,61,1,1,1,1),(78,1,67,1,1,1,1),(79,2,67,1,0,1,0),(80,3,67,1,0,1,0),(81,1,68,1,1,1,1),(82,2,68,1,0,1,0),(83,3,68,1,0,1,0),(84,1,69,1,1,1,1),(85,3,69,1,0,0,0),(86,2,70,1,0,0,0),(87,1,71,1,0,0,1),(88,1,72,1,1,1,0),(89,1,73,1,0,1,0),(90,1,74,1,1,1,1),(91,1,75,1,1,1,1),(92,3,75,1,1,1,1),(93,1,76,1,1,1,1),(94,5,76,1,1,1,1),(95,6,76,1,1,1,1),(96,1,78,1,0,0,0),(97,3,78,1,0,0,0),(98,2,79,1,0,0,0),(99,1,80,1,1,1,1),(100,1,81,1,1,1,1),(101,1,82,1,1,1,1),(102,1,83,1,1,1,1),(103,1,84,1,1,1,1),(104,1,85,1,1,1,1),(105,1,86,1,1,1,1),(106,1,87,1,1,1,1),(107,1,88,1,1,1,1),(108,4,88,1,1,1,1),(109,1,89,1,1,1,1),(110,1,90,1,1,1,1),(111,4,90,1,1,1,1),(112,1,91,1,1,1,1),(113,4,91,1,1,1,1),(114,1,92,1,1,1,1),(115,1,93,1,1,1,1),(116,1,94,1,1,1,1),(117,1,95,1,1,1,1),(118,1,96,1,1,1,1),(119,5,96,1,1,1,1),(120,6,96,1,1,1,1),(121,1,97,1,1,1,1),(122,6,97,1,1,1,1),(123,1,98,1,1,1,1),(124,2,98,1,1,1,1),(125,3,98,1,1,1,1),(126,1,99,1,0,1,0),(127,2,99,1,0,1,0),(128,3,99,1,0,1,0),(129,1,100,1,0,0,0),(130,1,101,1,1,1,1),(131,3,101,1,1,1,1),(132,1,102,1,1,1,1),(133,4,102,1,1,1,1),(134,1,103,1,0,0,0),(135,2,103,1,0,0,0),(136,1,104,1,1,1,0),(137,1,105,1,1,1,1),(138,1,107,1,1,1,0),(139,1,109,1,1,1,0),(140,1,111,1,1,1,1),(141,2,110,1,0,1,0),(142,2,108,1,1,1,0),(143,2,106,1,0,1,0),(144,3,109,1,1,1,0),(145,2,109,1,1,1,0),(146,1,112,1,1,1,1),(147,2,112,0,0,0,0),(148,1,113,1,1,1,1),(149,2,113,1,1,1,0),(150,1,114,0,0,0,0),(151,2,114,1,0,1,0),(152,1,115,1,1,1,0),(153,2,115,1,0,0,0),(154,1,116,1,1,1,1),(155,2,116,1,1,1,1),(156,1,117,1,1,1,0),(157,1,118,1,1,1,0);
/*!40000 ALTER TABLE `ohrm_user_role_screen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_user_selection_rule`
--

DROP TABLE IF EXISTS `ohrm_user_selection_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_user_selection_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `implementation_class` varchar(255) NOT NULL,
  `rule_xml_data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_user_selection_rule`
--

LOCK TABLES `ohrm_user_selection_rule` WRITE;
/*!40000 ALTER TABLE `ohrm_user_selection_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_user_selection_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_work_shift`
--

DROP TABLE IF EXISTS `ohrm_work_shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_work_shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `hours_per_day` decimal(4,2) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_work_shift`
--

LOCK TABLES `ohrm_work_shift` WRITE;
/*!40000 ALTER TABLE `ohrm_work_shift` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_work_shift` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_work_week`
--

DROP TABLE IF EXISTS `ohrm_work_week`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_work_week` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operational_country_id` int(10) unsigned DEFAULT NULL,
  `mon` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `tue` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `wed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `thu` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `fri` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sat` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sun` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_ohrm_work_week_ohrm_operational_country` (`operational_country_id`),
  CONSTRAINT `fk_ohrm_work_week_ohrm_operational_country` FOREIGN KEY (`operational_country_id`) REFERENCES `ohrm_operational_country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_work_week`
--

LOCK TABLES `ohrm_work_week` WRITE;
/*!40000 ALTER TABLE `ohrm_work_week` DISABLE KEYS */;
INSERT INTO `ohrm_work_week` VALUES (1,NULL,0,0,0,0,0,8,8);
/*!40000 ALTER TABLE `ohrm_work_week` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_workflow_state_machine`
--

DROP TABLE IF EXISTS `ohrm_workflow_state_machine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_workflow_state_machine` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `workflow` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `resulting_state` varchar(255) NOT NULL,
  `roles_to_notify` text DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'lowest priority 0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_workflow_state_machine`
--

LOCK TABLES `ohrm_workflow_state_machine` WRITE;
/*!40000 ALTER TABLE `ohrm_workflow_state_machine` DISABLE KEYS */;
INSERT INTO `ohrm_workflow_state_machine` VALUES (1,'0','INITIAL','SYSTEM','7','NOT SUBMITTED','',0),(2,'0','SUBMITTED','ADMIN','2','APPROVED','',0),(3,'0','SUBMITTED','ADMIN','3','REJECTED','',0),(4,'0','SUBMITTED','ADMIN','0','SUBMITTED','',0),(5,'0','SUBMITTED','ADMIN','5','SUBMITTED','',0),(6,'0','SUBMITTED','SUPERVISOR','2','APPROVED','',0),(7,'0','SUBMITTED','SUPERVISOR','3','REJECTED','',0),(8,'0','SUBMITTED','SUPERVISOR','5','SUBMITTED','',0),(9,'0','SUBMITTED','SUPERVISOR','0','SUBMITTED','',0),(10,'0','SUBMITTED','ESS USER','0','SUBMITTED','',0),(11,'0','SUBMITTED','ESS USER','5','SUBMITTED','',0),(12,'0','NOT SUBMITTED','ESS USER','1','SUBMITTED','',0),(13,'0','NOT SUBMITTED','ESS USER','5','NOT SUBMITTED','',0),(15,'0','NOT SUBMITTED','ESS USER','0','NOT SUBMITTED','',0),(16,'0','NOT SUBMITTED','SUPERVISOR','0','NOT SUBMITTED','',0),(17,'0','NOT SUBMITTED','SUPERVISOR','5','NOT SUBMITTED','',0),(18,'0','NOT SUBMITTED','SUPERVISOR','1','SUBMITTED','',0),(19,'0','NOT SUBMITTED','ADMIN','0','NOT SUBMITTED','',0),(20,'0','NOT SUBMITTED','ADMIN','5','NOT SUBMITTED','',0),(21,'0','NOT SUBMITTED','ADMIN','1','SUBMITTED','',0),(22,'0','REJECTED','ESS USER','1','SUBMITTED','',0),(23,'0','REJECTED','ESS USER','0','REJECTED','',0),(24,'0','REJECTED','ESS USER','5','REJECTED','',0),(25,'0','REJECTED','SUPERVISOR','1','SUBMITTED','',0),(26,'0','REJECTED','SUPERVISOR','0','REJECTED','',0),(27,'0','REJECTED','SUPERVISOR','5','REJECTED','',0),(28,'0','REJECTED','ADMIN','0','REJECTED','',0),(29,'0','REJECTED','ADMIN','5','SUBMITTED','',0),(30,'0','REJECTED','ADMIN','1','SUBMITTED','',0),(31,'0','APPROVED','ESS USER','0','APPROVED','',0),(32,'0','APPROVED','SUPERVISOR','0','APPROVED','',0),(33,'0','APPROVED','ADMIN','0','APPROVED','',0),(34,'0','APPROVED','ADMIN','4','SUBMITTED','',0),(35,'1','PUNCHED IN','ESS USER','1','PUNCHED OUT','',0),(36,'1','INITIAL','ESS USER','0','PUNCHED IN','',0),(37,'2','INITIAL','ADMIN','1','APPLICATION INITIATED','',0),(38,'2','APPLICATION INITIATED','ADMIN','2','SHORTLISTED','',0),(39,'2','APPLICATION INITIATED','ADMIN','3','REJECTED','',0),(40,'2','SHORTLISTED','ADMIN','4','INTERVIEW SCHEDULED','',0),(41,'2','SHORTLISTED','ADMIN','3','REJECTED','',0),(42,'2','INTERVIEW SCHEDULED','ADMIN','3','REJECTED','',0),(43,'2','INTERVIEW SCHEDULED','ADMIN','5','INTERVIEW PASSED','',0),(44,'2','INTERVIEW SCHEDULED','ADMIN','6','INTERVIEW FAILED','',0),(45,'2','INTERVIEW PASSED','ADMIN','4','INTERVIEW SCHEDULED','',0),(46,'2','INTERVIEW PASSED','ADMIN','7','JOB OFFERED','',0),(47,'2','INTERVIEW PASSED','ADMIN','3','REJECTED','',0),(48,'2','INTERVIEW FAILED','ADMIN','3','REJECTED','',0),(49,'2','JOB OFFERED','ADMIN','8','OFFER DECLINED','',0),(50,'2','JOB OFFERED','ADMIN','3','REJECTED','',0),(51,'2','JOB OFFERED','ADMIN','9','HIRED','',0),(52,'2','OFFER DECLINED','ADMIN','3','REJECTED','',0),(53,'2','INITIAL','HIRING MANAGER','1','APPLICATION INITIATED','',0),(54,'2','APPLICATION INITIATED','HIRING MANAGER','2','SHORTLISTED','',0),(55,'2','APPLICATION INITIATED','HIRING MANAGER','3','REJECTED','',0),(56,'2','SHORTLISTED','HIRING MANAGER','4','INTERVIEW SCHEDULED','',0),(57,'2','SHORTLISTED','HIRING MANAGER','3','REJECTED','',0),(58,'2','INTERVIEW SCHEDULED','HIRING MANAGER','3','REJECTED','',0),(59,'2','INTERVIEW SCHEDULED','HIRING MANAGER','5','INTERVIEW PASSED','',0),(60,'2','INTERVIEW SCHEDULED','HIRING MANAGER','6','INTERVIEW FAILED','',0),(61,'2','INTERVIEW PASSED','HIRING MANAGER','4','INTERVIEW SCHEDULED','',0),(62,'2','INTERVIEW PASSED','HIRING MANAGER','7','JOB OFFERED','',0),(63,'2','INTERVIEW PASSED','HIRING MANAGER','3','REJECTED','',0),(64,'2','INTERVIEW FAILED','HIRING MANAGER','3','REJECTED','',0),(65,'2','JOB OFFERED','HIRING MANAGER','8','OFFER DECLINED','',0),(66,'2','JOB OFFERED','HIRING MANAGER','3','REJECTED','',0),(67,'2','JOB OFFERED','HIRING MANAGER','9','HIRED','',0),(68,'2','OFFER DECLINED','HIRING MANAGER','3','REJECTED','',0),(69,'2','INTERVIEW SCHEDULED','INTERVIEWER','5','INTERVIEW PASSED','',0),(70,'2','INTERVIEW SCHEDULED','INTERVIEWER','6','INTERVIEW FAILED','',0),(71,'1','INITIAL','ADMIN','5','PUNCHED IN','',0),(72,'1','PUNCHED IN','ADMIN','6','PUNCHED OUT','',0),(73,'1','PUNCHED IN','ADMIN','2','PUNCHED IN','',0),(74,'1','PUNCHED IN','ADMIN','7','N/A','',0),(75,'1','PUNCHED OUT','ADMIN','2','PUNCHED OUT','',0),(76,'1','PUNCHED OUT','ADMIN','3','PUNCHED OUT','',0),(77,'1','PUNCHED OUT','ADMIN','7','N/A','',0),(78,'0','INITIAL','ADMIN','7','NOT SUBMITTED','',0),(79,'0','INITIAL','ESS USER','7','NOT SUBMITTED','',0),(80,'0','INITIAL','SUPERVISOR','7','NOT SUBMITTED','',0),(81,'3','NOT_EXIST','ADMIN','1','ACTIVE','',0),(82,'3','ACTIVE','ADMIN','2','NOT_EXIST','',0),(83,'3','ACTIVE','ADMIN','3','TERMINATED','',0),(84,'3','TERMINATED','ADMIN','4','ACTIVE','',0),(85,'3','TERMINATED','ADMIN','5','NOT_EXIST','',0),(86,'4','INITIAL','ESS','APPLY','PENDING APPROVAL','supervisor,subscriber',0),(87,'4','INITIAL','ADMIN','ASSIGN','SCHEDULED','ess,supervisor,subscriber',0),(88,'4','INITIAL','SUPERVISOR','ASSIGN','SCHEDULED','ess,supervisor,subscriber',0),(89,'4','PENDING APPROVAL','ADMIN','APPROVE','SCHEDULED','ess,subscriber',0),(90,'4','PENDING APPROVAL','SUPERVISOR','APPROVE','SCHEDULED','ess,subscriber',0),(91,'4','PENDING APPROVAL','ESS','CANCEL','CANCELLED','supervisor,subscriber',0),(92,'4','PENDING APPROVAL','ADMIN','CANCEL','CANCELLED','ess,subscriber',0),(93,'4','PENDING APPROVAL','SUPERVISOR','CANCEL','CANCELLED','ess,subscriber',0),(94,'4','PENDING APPROVAL','ADMIN','REJECT','REJECTED','ess,subscriber',0),(95,'4','PENDING APPROVAL','SUPERVISOR','REJECT','REJECTED','ess,subscriber',0),(96,'4','SCHEDULED','ESS','CANCEL','CANCELLED','supervisor,subscriber',0),(97,'4','SCHEDULED','ADMIN','CANCEL','CANCELLED','ess,subscriber',0),(98,'4','SCHEDULED','SUPERVISOR','CANCEL','CANCELLED','ess,subscriber',0),(99,'4','TAKEN','ADMIN','CANCEL','CANCELLED','ess,subscriber',0),(100,'4','LEAVE TYPE DELETED PENDING APPROVAL','ESS','CANCEL','CANCELLED','supervisor,subscriber',0),(101,'4','LEAVE TYPE DELETED PENDING APPROVAL','ADMIN','CANCEL','CANCELLED','ess,subscriber',0),(102,'4','LEAVE TYPE DELETED PENDING APPROVAL','SUPERVISOR','CANCEL','CANCELLED','ess,subscriber',0),(103,'4','LEAVE TYPE DELETED SCHEDULED','ESS','CANCEL','CANCELLED','supervisor,subscriber',0),(104,'4','LEAVE TYPE DELETED SCHEDULED','ADMIN','CANCEL','CANCELLED','ess,subscriber',0),(105,'4','LEAVE TYPE DELETED SCHEDULED','SUPERVISOR','CANCEL','CANCELLED','ess,subscriber',0),(106,'4','LEAVE TYPE DELETED TAKEN','ADMIN','CANCEL','CANCELLED','ess,subscriber',0);
/*!40000 ALTER TABLE `ohrm_workflow_state_machine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ohrm_ws_consumer`
--

DROP TABLE IF EXISTS `ohrm_ws_consumer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ohrm_ws_consumer` (
  `app_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_token` varchar(10) NOT NULL,
  `app_name` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ohrm_ws_consumer`
--

LOCK TABLES `ohrm_ws_consumer` WRITE;
/*!40000 ALTER TABLE `ohrm_ws_consumer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ohrm_ws_consumer` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-09  5:59:33
