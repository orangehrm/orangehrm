-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Generation Time: Sep 21, 2006 at 10:12 AM
-- Server version: 5.0.22
-- PHP Version: 5.1.6
-- 
-- Database: `hr_mysqltest`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_compstructtree`
-- 

DROP TABLE IF EXISTS `hs_hr_compstructtree`;
CREATE TABLE `hs_hr_compstructtree` (
  `title` tinytext NOT NULL,
  `description` text NOT NULL,
  `loc_code` varchar(6) default '',
  `lft` tinyint(4) NOT NULL default '0',
  `rgt` tinyint(4) NOT NULL default '0',
  `id` int(6) NOT NULL auto_increment,
  `parnt` int(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `loc_code` (`loc_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `hs_hr_compstructtree`
-- 

INSERT INTO `hs_hr_compstructtree` VALUES ('hSenid', 'Parent Company', NULL, 1, 2, 1, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_country`
-- 

DROP TABLE IF EXISTS `hs_hr_country`;
CREATE TABLE `hs_hr_country` (
  `cou_code` char(2) NOT NULL default '',
  `name` varchar(80) NOT NULL default '',
  `cou_name` varchar(80) NOT NULL default '',
  `iso3` char(3) default NULL,
  `numcode` smallint(6) default NULL,
  PRIMARY KEY  (`cou_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_country`
-- 

INSERT INTO `hs_hr_country` VALUES ('AD', 'ANDORRA', 'Andorra', 'AND', 20);
INSERT INTO `hs_hr_country` VALUES ('AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784);
INSERT INTO `hs_hr_country` VALUES ('AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4);
INSERT INTO `hs_hr_country` VALUES ('AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28);
INSERT INTO `hs_hr_country` VALUES ('AI', 'ANGUILLA', 'Anguilla', 'AIA', 660);
INSERT INTO `hs_hr_country` VALUES ('AL', 'ALBANIA', 'Albania', 'ALB', 8);
INSERT INTO `hs_hr_country` VALUES ('AM', 'ARMENIA', 'Armenia', 'ARM', 51);
INSERT INTO `hs_hr_country` VALUES ('AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530);
INSERT INTO `hs_hr_country` VALUES ('AO', 'ANGOLA', 'Angola', 'AGO', 24);
INSERT INTO `hs_hr_country` VALUES ('AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('AR', 'ARGENTINA', 'Argentina', 'ARG', 32);
INSERT INTO `hs_hr_country` VALUES ('AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16);
INSERT INTO `hs_hr_country` VALUES ('AT', 'AUSTRIA', 'Austria', 'AUT', 40);
INSERT INTO `hs_hr_country` VALUES ('AU', 'AUSTRALIA', 'Australia', 'AUS', 36);
INSERT INTO `hs_hr_country` VALUES ('AW', 'ARUBA', 'Aruba', 'ABW', 533);
INSERT INTO `hs_hr_country` VALUES ('AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31);
INSERT INTO `hs_hr_country` VALUES ('BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70);
INSERT INTO `hs_hr_country` VALUES ('BB', 'BARBADOS', 'Barbados', 'BRB', 52);
INSERT INTO `hs_hr_country` VALUES ('BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50);
INSERT INTO `hs_hr_country` VALUES ('BE', 'BELGIUM', 'Belgium', 'BEL', 56);
INSERT INTO `hs_hr_country` VALUES ('BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854);
INSERT INTO `hs_hr_country` VALUES ('BG', 'BULGARIA', 'Bulgaria', 'BGR', 100);
INSERT INTO `hs_hr_country` VALUES ('BH', 'BAHRAIN', 'Bahrain', 'BHR', 48);
INSERT INTO `hs_hr_country` VALUES ('BI', 'BURUNDI', 'Burundi', 'BDI', 108);
INSERT INTO `hs_hr_country` VALUES ('BJ', 'BENIN', 'Benin', 'BEN', 204);
INSERT INTO `hs_hr_country` VALUES ('BM', 'BERMUDA', 'Bermuda', 'BMU', 60);
INSERT INTO `hs_hr_country` VALUES ('BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96);
INSERT INTO `hs_hr_country` VALUES ('BO', 'BOLIVIA', 'Bolivia', 'BOL', 68);
INSERT INTO `hs_hr_country` VALUES ('BR', 'BRAZIL', 'Brazil', 'BRA', 76);
INSERT INTO `hs_hr_country` VALUES ('BS', 'BAHAMAS', 'Bahamas', 'BHS', 44);
INSERT INTO `hs_hr_country` VALUES ('BT', 'BHUTAN', 'Bhutan', 'BTN', 64);
INSERT INTO `hs_hr_country` VALUES ('BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('BW', 'BOTSWANA', 'Botswana', 'BWA', 72);
INSERT INTO `hs_hr_country` VALUES ('BY', 'BELARUS', 'Belarus', 'BLR', 112);
INSERT INTO `hs_hr_country` VALUES ('BZ', 'BELIZE', 'Belize', 'BLZ', 84);
INSERT INTO `hs_hr_country` VALUES ('CA', 'CANADA', 'Canada', 'CAN', 124);
INSERT INTO `hs_hr_country` VALUES ('CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180);
INSERT INTO `hs_hr_country` VALUES ('CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140);
INSERT INTO `hs_hr_country` VALUES ('CG', 'CONGO', 'Congo', 'COG', 178);
INSERT INTO `hs_hr_country` VALUES ('CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756);
INSERT INTO `hs_hr_country` VALUES ('CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384);
INSERT INTO `hs_hr_country` VALUES ('CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184);
INSERT INTO `hs_hr_country` VALUES ('CL', 'CHILE', 'Chile', 'CHL', 152);
INSERT INTO `hs_hr_country` VALUES ('CM', 'CAMEROON', 'Cameroon', 'CMR', 120);
INSERT INTO `hs_hr_country` VALUES ('CN', 'CHINA', 'China', 'CHN', 156);
INSERT INTO `hs_hr_country` VALUES ('CO', 'COLOMBIA', 'Colombia', 'COL', 170);
INSERT INTO `hs_hr_country` VALUES ('CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188);
INSERT INTO `hs_hr_country` VALUES ('CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('CU', 'CUBA', 'Cuba', 'CUB', 192);
INSERT INTO `hs_hr_country` VALUES ('CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132);
INSERT INTO `hs_hr_country` VALUES ('CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('CY', 'CYPRUS', 'Cyprus', 'CYP', 196);
INSERT INTO `hs_hr_country` VALUES ('CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203);
INSERT INTO `hs_hr_country` VALUES ('DE', 'GERMANY', 'Germany', 'DEU', 276);
INSERT INTO `hs_hr_country` VALUES ('DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262);
INSERT INTO `hs_hr_country` VALUES ('DK', 'DENMARK', 'Denmark', 'DNK', 208);
INSERT INTO `hs_hr_country` VALUES ('DM', 'DOMINICA', 'Dominica', 'DMA', 212);
INSERT INTO `hs_hr_country` VALUES ('DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214);
INSERT INTO `hs_hr_country` VALUES ('DZ', 'ALGERIA', 'Algeria', 'DZA', 12);
INSERT INTO `hs_hr_country` VALUES ('EC', 'ECUADOR', 'Ecuador', 'ECU', 218);
INSERT INTO `hs_hr_country` VALUES ('EE', 'ESTONIA', 'Estonia', 'EST', 233);
INSERT INTO `hs_hr_country` VALUES ('EG', 'EGYPT', 'Egypt', 'EGY', 818);
INSERT INTO `hs_hr_country` VALUES ('EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732);
INSERT INTO `hs_hr_country` VALUES ('ER', 'ERITREA', 'Eritrea', 'ERI', 232);
INSERT INTO `hs_hr_country` VALUES ('ES', 'SPAIN', 'Spain', 'ESP', 724);
INSERT INTO `hs_hr_country` VALUES ('ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231);
INSERT INTO `hs_hr_country` VALUES ('FI', 'FINLAND', 'Finland', 'FIN', 246);
INSERT INTO `hs_hr_country` VALUES ('FJ', 'FIJI', 'Fiji', 'FJI', 242);
INSERT INTO `hs_hr_country` VALUES ('FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238);
INSERT INTO `hs_hr_country` VALUES ('FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583);
INSERT INTO `hs_hr_country` VALUES ('FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234);
INSERT INTO `hs_hr_country` VALUES ('FR', 'FRANCE', 'France', 'FRA', 250);
INSERT INTO `hs_hr_country` VALUES ('GA', 'GABON', 'Gabon', 'GAB', 266);
INSERT INTO `hs_hr_country` VALUES ('GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826);
INSERT INTO `hs_hr_country` VALUES ('GD', 'GRENADA', 'Grenada', 'GRD', 308);
INSERT INTO `hs_hr_country` VALUES ('GE', 'GEORGIA', 'Georgia', 'GEO', 268);
INSERT INTO `hs_hr_country` VALUES ('GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254);
INSERT INTO `hs_hr_country` VALUES ('GH', 'GHANA', 'Ghana', 'GHA', 288);
INSERT INTO `hs_hr_country` VALUES ('GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292);
INSERT INTO `hs_hr_country` VALUES ('GL', 'GREENLAND', 'Greenland', 'GRL', 304);
INSERT INTO `hs_hr_country` VALUES ('GM', 'GAMBIA', 'Gambia', 'GMB', 270);
INSERT INTO `hs_hr_country` VALUES ('GN', 'GUINEA', 'Guinea', 'GIN', 324);
INSERT INTO `hs_hr_country` VALUES ('GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312);
INSERT INTO `hs_hr_country` VALUES ('GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226);
INSERT INTO `hs_hr_country` VALUES ('GR', 'GREECE', 'Greece', 'GRC', 300);
INSERT INTO `hs_hr_country` VALUES ('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('GT', 'GUATEMALA', 'Guatemala', 'GTM', 320);
INSERT INTO `hs_hr_country` VALUES ('GU', 'GUAM', 'Guam', 'GUM', 316);
INSERT INTO `hs_hr_country` VALUES ('GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624);
INSERT INTO `hs_hr_country` VALUES ('GY', 'GUYANA', 'Guyana', 'GUY', 328);
INSERT INTO `hs_hr_country` VALUES ('HK', 'HONG KONG', 'Hong Kong', 'HKG', 344);
INSERT INTO `hs_hr_country` VALUES ('HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('HN', 'HONDURAS', 'Honduras', 'HND', 340);
INSERT INTO `hs_hr_country` VALUES ('HR', 'CROATIA', 'Croatia', 'HRV', 191);
INSERT INTO `hs_hr_country` VALUES ('HT', 'HAITI', 'Haiti', 'HTI', 332);
INSERT INTO `hs_hr_country` VALUES ('HU', 'HUNGARY', 'Hungary', 'HUN', 348);
INSERT INTO `hs_hr_country` VALUES ('ID', 'INDONESIA', 'Indonesia', 'IDN', 360);
INSERT INTO `hs_hr_country` VALUES ('IE', 'IRELAND', 'Ireland', 'IRL', 372);
INSERT INTO `hs_hr_country` VALUES ('IL', 'ISRAEL', 'Israel', 'ISR', 376);
INSERT INTO `hs_hr_country` VALUES ('IN', 'INDIA', 'India', 'IND', 356);
INSERT INTO `hs_hr_country` VALUES ('IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('IQ', 'IRAQ', 'Iraq', 'IRQ', 368);
INSERT INTO `hs_hr_country` VALUES ('IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364);
INSERT INTO `hs_hr_country` VALUES ('IS', 'ICELAND', 'Iceland', 'ISL', 352);
INSERT INTO `hs_hr_country` VALUES ('IT', 'ITALY', 'Italy', 'ITA', 380);
INSERT INTO `hs_hr_country` VALUES ('JM', 'JAMAICA', 'Jamaica', 'JAM', 388);
INSERT INTO `hs_hr_country` VALUES ('JO', 'JORDAN', 'Jordan', 'JOR', 400);
INSERT INTO `hs_hr_country` VALUES ('JP', 'JAPAN', 'Japan', 'JPN', 392);
INSERT INTO `hs_hr_country` VALUES ('KE', 'KENYA', 'Kenya', 'KEN', 404);
INSERT INTO `hs_hr_country` VALUES ('KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417);
INSERT INTO `hs_hr_country` VALUES ('KH', 'CAMBODIA', 'Cambodia', 'KHM', 116);
INSERT INTO `hs_hr_country` VALUES ('KI', 'KIRIBATI', 'Kiribati', 'KIR', 296);
INSERT INTO `hs_hr_country` VALUES ('KM', 'COMOROS', 'Comoros', 'COM', 174);
INSERT INTO `hs_hr_country` VALUES ('KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659);
INSERT INTO `hs_hr_country` VALUES ('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408);
INSERT INTO `hs_hr_country` VALUES ('KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410);
INSERT INTO `hs_hr_country` VALUES ('KW', 'KUWAIT', 'Kuwait', 'KWT', 414);
INSERT INTO `hs_hr_country` VALUES ('KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136);
INSERT INTO `hs_hr_country` VALUES ('KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398);
INSERT INTO `hs_hr_country` VALUES ('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418);
INSERT INTO `hs_hr_country` VALUES ('LB', 'LEBANON', 'Lebanon', 'LBN', 422);
INSERT INTO `hs_hr_country` VALUES ('LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662);
INSERT INTO `hs_hr_country` VALUES ('LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438);
INSERT INTO `hs_hr_country` VALUES ('LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144);
INSERT INTO `hs_hr_country` VALUES ('LR', 'LIBERIA', 'Liberia', 'LBR', 430);
INSERT INTO `hs_hr_country` VALUES ('LS', 'LESOTHO', 'Lesotho', 'LSO', 426);
INSERT INTO `hs_hr_country` VALUES ('LT', 'LITHUANIA', 'Lithuania', 'LTU', 440);
INSERT INTO `hs_hr_country` VALUES ('LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442);
INSERT INTO `hs_hr_country` VALUES ('LV', 'LATVIA', 'Latvia', 'LVA', 428);
INSERT INTO `hs_hr_country` VALUES ('LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434);
INSERT INTO `hs_hr_country` VALUES ('MA', 'MOROCCO', 'Morocco', 'MAR', 504);
INSERT INTO `hs_hr_country` VALUES ('MC', 'MONACO', 'Monaco', 'MCO', 492);
INSERT INTO `hs_hr_country` VALUES ('MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498);
INSERT INTO `hs_hr_country` VALUES ('MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450);
INSERT INTO `hs_hr_country` VALUES ('MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584);
INSERT INTO `hs_hr_country` VALUES ('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807);
INSERT INTO `hs_hr_country` VALUES ('ML', 'MALI', 'Mali', 'MLI', 466);
INSERT INTO `hs_hr_country` VALUES ('MM', 'MYANMAR', 'Myanmar', 'MMR', 104);
INSERT INTO `hs_hr_country` VALUES ('MN', 'MONGOLIA', 'Mongolia', 'MNG', 496);
INSERT INTO `hs_hr_country` VALUES ('MO', 'MACAO', 'Macao', 'MAC', 446);
INSERT INTO `hs_hr_country` VALUES ('MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580);
INSERT INTO `hs_hr_country` VALUES ('MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474);
INSERT INTO `hs_hr_country` VALUES ('MR', 'MAURITANIA', 'Mauritania', 'MRT', 478);
INSERT INTO `hs_hr_country` VALUES ('MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500);
INSERT INTO `hs_hr_country` VALUES ('MT', 'MALTA', 'Malta', 'MLT', 470);
INSERT INTO `hs_hr_country` VALUES ('MU', 'MAURITIUS', 'Mauritius', 'MUS', 480);
INSERT INTO `hs_hr_country` VALUES ('MV', 'MALDIVES', 'Maldives', 'MDV', 462);
INSERT INTO `hs_hr_country` VALUES ('MW', 'MALAWI', 'Malawi', 'MWI', 454);
INSERT INTO `hs_hr_country` VALUES ('MX', 'MEXICO', 'Mexico', 'MEX', 484);
INSERT INTO `hs_hr_country` VALUES ('MY', 'MALAYSIA', 'Malaysia', 'MYS', 458);
INSERT INTO `hs_hr_country` VALUES ('MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508);
INSERT INTO `hs_hr_country` VALUES ('NA', 'NAMIBIA', 'Namibia', 'NAM', 516);
INSERT INTO `hs_hr_country` VALUES ('NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540);
INSERT INTO `hs_hr_country` VALUES ('NE', 'NIGER', 'Niger', 'NER', 562);
INSERT INTO `hs_hr_country` VALUES ('NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574);
INSERT INTO `hs_hr_country` VALUES ('NG', 'NIGERIA', 'Nigeria', 'NGA', 566);
INSERT INTO `hs_hr_country` VALUES ('NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558);
INSERT INTO `hs_hr_country` VALUES ('NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528);
INSERT INTO `hs_hr_country` VALUES ('NO', 'NORWAY', 'Norway', 'NOR', 578);
INSERT INTO `hs_hr_country` VALUES ('NP', 'NEPAL', 'Nepal', 'NPL', 524);
INSERT INTO `hs_hr_country` VALUES ('NR', 'NAURU', 'Nauru', 'NRU', 520);
INSERT INTO `hs_hr_country` VALUES ('NU', 'NIUE', 'Niue', 'NIU', 570);
INSERT INTO `hs_hr_country` VALUES ('NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554);
INSERT INTO `hs_hr_country` VALUES ('OM', 'OMAN', 'Oman', 'OMN', 512);
INSERT INTO `hs_hr_country` VALUES ('PA', 'PANAMA', 'Panama', 'PAN', 591);
INSERT INTO `hs_hr_country` VALUES ('PE', 'PERU', 'Peru', 'PER', 604);
INSERT INTO `hs_hr_country` VALUES ('PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258);
INSERT INTO `hs_hr_country` VALUES ('PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598);
INSERT INTO `hs_hr_country` VALUES ('PH', 'PHILIPPINES', 'Philippines', 'PHL', 608);
INSERT INTO `hs_hr_country` VALUES ('PK', 'PAKISTAN', 'Pakistan', 'PAK', 586);
INSERT INTO `hs_hr_country` VALUES ('PL', 'POLAND', 'Poland', 'POL', 616);
INSERT INTO `hs_hr_country` VALUES ('PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666);
INSERT INTO `hs_hr_country` VALUES ('PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612);
INSERT INTO `hs_hr_country` VALUES ('PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630);
INSERT INTO `hs_hr_country` VALUES ('PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('PT', 'PORTUGAL', 'Portugal', 'PRT', 620);
INSERT INTO `hs_hr_country` VALUES ('PW', 'PALAU', 'Palau', 'PLW', 585);
INSERT INTO `hs_hr_country` VALUES ('PY', 'PARAGUAY', 'Paraguay', 'PRY', 600);
INSERT INTO `hs_hr_country` VALUES ('QA', 'QATAR', 'Qatar', 'QAT', 634);
INSERT INTO `hs_hr_country` VALUES ('RE', 'REUNION', 'Reunion', 'REU', 638);
INSERT INTO `hs_hr_country` VALUES ('RO', 'ROMANIA', 'Romania', 'ROM', 642);
INSERT INTO `hs_hr_country` VALUES ('RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643);
INSERT INTO `hs_hr_country` VALUES ('RW', 'RWANDA', 'Rwanda', 'RWA', 646);
INSERT INTO `hs_hr_country` VALUES ('SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682);
INSERT INTO `hs_hr_country` VALUES ('SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90);
INSERT INTO `hs_hr_country` VALUES ('SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690);
INSERT INTO `hs_hr_country` VALUES ('SD', 'SUDAN', 'Sudan', 'SDN', 736);
INSERT INTO `hs_hr_country` VALUES ('SE', 'SWEDEN', 'Sweden', 'SWE', 752);
INSERT INTO `hs_hr_country` VALUES ('SG', 'SINGAPORE', 'Singapore', 'SGP', 702);
INSERT INTO `hs_hr_country` VALUES ('SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654);
INSERT INTO `hs_hr_country` VALUES ('SI', 'SLOVENIA', 'Slovenia', 'SVN', 705);
INSERT INTO `hs_hr_country` VALUES ('SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744);
INSERT INTO `hs_hr_country` VALUES ('SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703);
INSERT INTO `hs_hr_country` VALUES ('SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694);
INSERT INTO `hs_hr_country` VALUES ('SM', 'SAN MARINO', 'San Marino', 'SMR', 674);
INSERT INTO `hs_hr_country` VALUES ('SN', 'SENEGAL', 'Senegal', 'SEN', 686);
INSERT INTO `hs_hr_country` VALUES ('SO', 'SOMALIA', 'Somalia', 'SOM', 706);
INSERT INTO `hs_hr_country` VALUES ('SR', 'SURINAME', 'Suriname', 'SUR', 740);
INSERT INTO `hs_hr_country` VALUES ('ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678);
INSERT INTO `hs_hr_country` VALUES ('SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222);
INSERT INTO `hs_hr_country` VALUES ('SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760);
INSERT INTO `hs_hr_country` VALUES ('SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748);
INSERT INTO `hs_hr_country` VALUES ('TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796);
INSERT INTO `hs_hr_country` VALUES ('TD', 'CHAD', 'Chad', 'TCD', 148);
INSERT INTO `hs_hr_country` VALUES ('TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('TG', 'TOGO', 'Togo', 'TGO', 768);
INSERT INTO `hs_hr_country` VALUES ('TH', 'THAILAND', 'Thailand', 'THA', 764);
INSERT INTO `hs_hr_country` VALUES ('TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762);
INSERT INTO `hs_hr_country` VALUES ('TK', 'TOKELAU', 'Tokelau', 'TKL', 772);
INSERT INTO `hs_hr_country` VALUES ('TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795);
INSERT INTO `hs_hr_country` VALUES ('TN', 'TUNISIA', 'Tunisia', 'TUN', 788);
INSERT INTO `hs_hr_country` VALUES ('TO', 'TONGA', 'Tonga', 'TON', 776);
INSERT INTO `hs_hr_country` VALUES ('TR', 'TURKEY', 'Turkey', 'TUR', 792);
INSERT INTO `hs_hr_country` VALUES ('TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780);
INSERT INTO `hs_hr_country` VALUES ('TV', 'TUVALU', 'Tuvalu', 'TUV', 798);
INSERT INTO `hs_hr_country` VALUES ('TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158);
INSERT INTO `hs_hr_country` VALUES ('TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834);
INSERT INTO `hs_hr_country` VALUES ('UA', 'UKRAINE', 'Ukraine', 'UKR', 804);
INSERT INTO `hs_hr_country` VALUES ('UG', 'UGANDA', 'Uganda', 'UGA', 800);
INSERT INTO `hs_hr_country` VALUES ('UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('US', 'UNITED STATES', 'United States', 'USA', 840);
INSERT INTO `hs_hr_country` VALUES ('UY', 'URUGUAY', 'Uruguay', 'URY', 858);
INSERT INTO `hs_hr_country` VALUES ('UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860);
INSERT INTO `hs_hr_country` VALUES ('VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336);
INSERT INTO `hs_hr_country` VALUES ('VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670);
INSERT INTO `hs_hr_country` VALUES ('VE', 'VENEZUELA', 'Venezuela', 'VEN', 862);
INSERT INTO `hs_hr_country` VALUES ('VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92);
INSERT INTO `hs_hr_country` VALUES ('VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850);
INSERT INTO `hs_hr_country` VALUES ('VN', 'VIET NAM', 'Viet Nam', 'VNM', 704);
INSERT INTO `hs_hr_country` VALUES ('VU', 'VANUATU', 'Vanuatu', 'VUT', 548);
INSERT INTO `hs_hr_country` VALUES ('WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876);
INSERT INTO `hs_hr_country` VALUES ('WS', 'SAMOA', 'Samoa', 'WSM', 882);
INSERT INTO `hs_hr_country` VALUES ('YE', 'YEMEN', 'Yemen', 'YEM', 887);
INSERT INTO `hs_hr_country` VALUES ('YT', 'MAYOTTE', 'Mayotte', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710);
INSERT INTO `hs_hr_country` VALUES ('ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894);
INSERT INTO `hs_hr_country` VALUES ('ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_currency_type`
-- 

DROP TABLE IF EXISTS `hs_hr_currency_type`;
CREATE TABLE `hs_hr_currency_type` (
  `code` int(11) NOT NULL default '0',
  `currency_id` char(3) NOT NULL default '',
  `currency_name` varchar(70) NOT NULL default '',
  PRIMARY KEY  (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_currency_type`
-- 

INSERT INTO `hs_hr_currency_type` VALUES (1, 'ADF', 'Andorran Franc');
INSERT INTO `hs_hr_currency_type` VALUES (2, 'ADP', 'Andorran Peseta');
INSERT INTO `hs_hr_currency_type` VALUES (3, 'AED', 'Utd. Arab Emir. Dirham');
INSERT INTO `hs_hr_currency_type` VALUES (4, 'AFA', 'Afghanistan Afghani');
INSERT INTO `hs_hr_currency_type` VALUES (5, 'ALL', 'Albanian Lek');
INSERT INTO `hs_hr_currency_type` VALUES (6, 'ANG', 'NL Antillian Guilder');
INSERT INTO `hs_hr_currency_type` VALUES (7, 'AON', 'Angolan New Kwanza');
INSERT INTO `hs_hr_currency_type` VALUES (177, 'ARP', 'Argentina Pesos');
INSERT INTO `hs_hr_currency_type` VALUES (8, 'ARS', 'Argentine Peso');
INSERT INTO `hs_hr_currency_type` VALUES (9, 'ATS', 'Austrian Schilling');
INSERT INTO `hs_hr_currency_type` VALUES (10, 'AUD', 'Australian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (11, 'AWG', 'Aruban Florin');
INSERT INTO `hs_hr_currency_type` VALUES (12, 'BBD', 'Barbados Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (13, 'BDT', 'Bangladeshi Taka');
INSERT INTO `hs_hr_currency_type` VALUES (14, 'BEF', 'Belgium Franc');
INSERT INTO `hs_hr_currency_type` VALUES (15, 'BGL', 'Bulgarian Lev');
INSERT INTO `hs_hr_currency_type` VALUES (16, 'BHD', 'Bahraini Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (17, 'BIF', 'Burundi Franc');
INSERT INTO `hs_hr_currency_type` VALUES (18, 'BMD', 'Bermudian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (19, 'BND', 'Brunei Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (20, 'BOB', 'Bolivian Boliviano');
INSERT INTO `hs_hr_currency_type` VALUES (21, 'BRL', 'Brazilian Real');
INSERT INTO `hs_hr_currency_type` VALUES (22, 'BSD', 'Bahamian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (23, 'BTN', 'Bhutan Ngultrum');
INSERT INTO `hs_hr_currency_type` VALUES (24, 'BWP', 'Botswana Pula');
INSERT INTO `hs_hr_currency_type` VALUES (25, 'BZD', 'Belize Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (26, 'CAD', 'Canadian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (27, 'CHF', 'Swiss Franc');
INSERT INTO `hs_hr_currency_type` VALUES (28, 'CLP', 'Chilean Peso');
INSERT INTO `hs_hr_currency_type` VALUES (29, 'CNY', 'Chinese Yuan Renminbi');
INSERT INTO `hs_hr_currency_type` VALUES (30, 'COP', 'Colombian Peso');
INSERT INTO `hs_hr_currency_type` VALUES (31, 'CRC', 'Costa Rican Colon');
INSERT INTO `hs_hr_currency_type` VALUES (171, 'CSK', 'Czech Koruna');
INSERT INTO `hs_hr_currency_type` VALUES (32, 'CUP', 'Cuban Peso');
INSERT INTO `hs_hr_currency_type` VALUES (33, 'CVE', 'Cape Verde Escudo');
INSERT INTO `hs_hr_currency_type` VALUES (34, 'CYP', 'Cyprus Pound');
INSERT INTO `hs_hr_currency_type` VALUES (35, 'CZK', 'Czech Krona');
INSERT INTO `hs_hr_currency_type` VALUES (36, 'DEM', 'German Mark');
INSERT INTO `hs_hr_currency_type` VALUES (37, 'DJF', 'Djibouti Franc');
INSERT INTO `hs_hr_currency_type` VALUES (38, 'DKK', 'Danish Krona');
INSERT INTO `hs_hr_currency_type` VALUES (39, 'DOP', 'Dominican Peso');
INSERT INTO `hs_hr_currency_type` VALUES (40, 'DZD', 'Algerian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (41, 'ECS', 'Ecuador Sucre');
INSERT INTO `hs_hr_currency_type` VALUES (43, 'EEK', 'Estonian Krona');
INSERT INTO `hs_hr_currency_type` VALUES (44, 'EGP', 'Egyptian Pound');
INSERT INTO `hs_hr_currency_type` VALUES (45, 'ESP', 'Spanish Peseta');
INSERT INTO `hs_hr_currency_type` VALUES (46, 'ETB', 'Ethiopian Birr');
INSERT INTO `hs_hr_currency_type` VALUES (42, 'EUR', 'Euro');
INSERT INTO `hs_hr_currency_type` VALUES (47, 'FIM', 'Finnish Makka');
INSERT INTO `hs_hr_currency_type` VALUES (48, 'FJD', 'Fiji Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (49, 'FKP', 'Falkland Islands Pound');
INSERT INTO `hs_hr_currency_type` VALUES (50, 'FRF', 'French Franc');
INSERT INTO `hs_hr_currency_type` VALUES (51, 'GBP', 'Pound Sterling');
INSERT INTO `hs_hr_currency_type` VALUES (52, 'GHC', 'Ghanaian Cedi');
INSERT INTO `hs_hr_currency_type` VALUES (53, 'GIP', 'Gibraltar Pound');
INSERT INTO `hs_hr_currency_type` VALUES (54, 'GMD', 'Gambian Dalasi');
INSERT INTO `hs_hr_currency_type` VALUES (55, 'GNF', 'Guinea Franc');
INSERT INTO `hs_hr_currency_type` VALUES (56, 'GRD', 'Greek Drachma');
INSERT INTO `hs_hr_currency_type` VALUES (57, 'GTQ', 'Guatemalan Quetzal');
INSERT INTO `hs_hr_currency_type` VALUES (58, 'GYD', 'Guyanan Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (59, 'HKD', 'Hong Kong Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (60, 'HNL', 'Honduran Lempira');
INSERT INTO `hs_hr_currency_type` VALUES (61, 'HRK', 'Croatian Kuna');
INSERT INTO `hs_hr_currency_type` VALUES (62, 'HTG', 'Haitian Gourde');
INSERT INTO `hs_hr_currency_type` VALUES (63, 'HUF', 'Hungarian Forint');
INSERT INTO `hs_hr_currency_type` VALUES (64, 'IDR', 'Indonesian Rupiah');
INSERT INTO `hs_hr_currency_type` VALUES (65, 'IEP', 'Irish Punt');
INSERT INTO `hs_hr_currency_type` VALUES (66, 'ILS', 'Israeli New Shekel');
INSERT INTO `hs_hr_currency_type` VALUES (67, 'INR', 'Indian Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (68, 'IQD', 'Iraqi Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (69, 'IRR', 'Iranian Rial');
INSERT INTO `hs_hr_currency_type` VALUES (70, 'ISK', 'Iceland Krona');
INSERT INTO `hs_hr_currency_type` VALUES (71, 'ITL', 'Italian Lira');
INSERT INTO `hs_hr_currency_type` VALUES (72, 'JMD', 'Jamaican Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (73, 'JOD', 'Jordanian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (74, 'JPY', 'Japanese Yen');
INSERT INTO `hs_hr_currency_type` VALUES (75, 'KES', 'Kenyan Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (76, 'KHR', 'Kampuchean Riel');
INSERT INTO `hs_hr_currency_type` VALUES (77, 'KMF', 'Comoros Franc');
INSERT INTO `hs_hr_currency_type` VALUES (78, 'KPW', 'North Korean Won');
INSERT INTO `hs_hr_currency_type` VALUES (79, 'KRW', 'Korean Won');
INSERT INTO `hs_hr_currency_type` VALUES (80, 'KWD', 'Kuwaiti Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (81, 'KYD', 'Cayman Islands Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (82, 'KZT', 'Kazakhstan Tenge');
INSERT INTO `hs_hr_currency_type` VALUES (83, 'LAK', 'Lao Kip');
INSERT INTO `hs_hr_currency_type` VALUES (84, 'LBP', 'Lebanese Pound');
INSERT INTO `hs_hr_currency_type` VALUES (85, 'LKR', 'Sri Lanka Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (86, 'LRD', 'Liberian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (87, 'LSL', 'Lesotho Loti');
INSERT INTO `hs_hr_currency_type` VALUES (88, 'LTL', 'Lithuanian Litas');
INSERT INTO `hs_hr_currency_type` VALUES (89, 'LUF', 'Luxembourg Franc');
INSERT INTO `hs_hr_currency_type` VALUES (90, 'LVL', 'Latvian Lats');
INSERT INTO `hs_hr_currency_type` VALUES (91, 'LYD', 'Libyan Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (92, 'MAD', 'Moroccan Dirham');
INSERT INTO `hs_hr_currency_type` VALUES (93, 'MGF', 'Malagasy Franc');
INSERT INTO `hs_hr_currency_type` VALUES (94, 'MMK', 'Myanmar Kyat');
INSERT INTO `hs_hr_currency_type` VALUES (95, 'MNT', 'Mongolian Tugrik');
INSERT INTO `hs_hr_currency_type` VALUES (96, 'MOP', 'Macau Pataca');
INSERT INTO `hs_hr_currency_type` VALUES (97, 'MRO', 'Mauritanian Ouguiya');
INSERT INTO `hs_hr_currency_type` VALUES (98, 'MTL', 'Maltese Lira');
INSERT INTO `hs_hr_currency_type` VALUES (99, 'MUR', 'Mauritius Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (100, 'MVR', 'Maldive Rufiyaa');
INSERT INTO `hs_hr_currency_type` VALUES (101, 'MWK', 'Malawi Kwacha');
INSERT INTO `hs_hr_currency_type` VALUES (102, 'MXN', 'Mexican New Peso');
INSERT INTO `hs_hr_currency_type` VALUES (172, 'MXP', 'Mexican Peso');
INSERT INTO `hs_hr_currency_type` VALUES (103, 'MYR', 'Malaysian Ringgit');
INSERT INTO `hs_hr_currency_type` VALUES (104, 'MZM', 'Mozambique Metical');
INSERT INTO `hs_hr_currency_type` VALUES (105, 'NAD', 'Namibia Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (106, 'NGN', 'Nigerian Naira');
INSERT INTO `hs_hr_currency_type` VALUES (107, 'NIO', 'Nicaraguan Cordoba Oro');
INSERT INTO `hs_hr_currency_type` VALUES (108, 'NLG', 'Dutch Guilder');
INSERT INTO `hs_hr_currency_type` VALUES (109, 'NOK', 'Norwegian Krona');
INSERT INTO `hs_hr_currency_type` VALUES (110, 'NPR', 'Nepalese Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (111, 'NZD', 'New Zealand Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (112, 'OMR', 'Omani Rial');
INSERT INTO `hs_hr_currency_type` VALUES (113, 'PAB', 'Panamanian Balboa');
INSERT INTO `hs_hr_currency_type` VALUES (114, 'PEN', 'Peruvian Nuevo Sol');
INSERT INTO `hs_hr_currency_type` VALUES (115, 'PGK', 'Papua New Guinea Kina');
INSERT INTO `hs_hr_currency_type` VALUES (116, 'PHP', 'Philippine Peso');
INSERT INTO `hs_hr_currency_type` VALUES (117, 'PKR', 'Pakistan Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (118, 'PLN', 'Polish Zloty');
INSERT INTO `hs_hr_currency_type` VALUES (173, 'PLZ', 'Polish Zloty');
INSERT INTO `hs_hr_currency_type` VALUES (119, 'PTE', 'Portuguese Escudo');
INSERT INTO `hs_hr_currency_type` VALUES (120, 'PYG', 'Paraguay Guarani');
INSERT INTO `hs_hr_currency_type` VALUES (121, 'QAR', 'Qatari Rial');
INSERT INTO `hs_hr_currency_type` VALUES (122, 'ROL', 'Romanian Leu');
INSERT INTO `hs_hr_currency_type` VALUES (123, 'RUB', 'Russian Rouble');
INSERT INTO `hs_hr_currency_type` VALUES (180, 'RUR', 'Russia Rubles');
INSERT INTO `hs_hr_currency_type` VALUES (124, 'SAR', 'South African Rand');
INSERT INTO `hs_hr_currency_type` VALUES (125, 'SBD', 'Solomon Islands Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (126, 'SCR', 'Seychelles Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (127, 'SDD', 'Sudanese Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (128, 'SDP', 'Sudanese Pound');
INSERT INTO `hs_hr_currency_type` VALUES (129, 'SEK', 'Swedish Krona');
INSERT INTO `hs_hr_currency_type` VALUES (131, 'SGD', 'Singapore Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (132, 'SHP', 'St. Helena Pound');
INSERT INTO `hs_hr_currency_type` VALUES (133, 'SIT', 'Slovenian Tolar');
INSERT INTO `hs_hr_currency_type` VALUES (130, 'SKK', 'Slovak Koruna');
INSERT INTO `hs_hr_currency_type` VALUES (135, 'SLL', 'Sierra Leone Leone');
INSERT INTO `hs_hr_currency_type` VALUES (136, 'SOS', 'Somali Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (137, 'SRG', 'Suriname Guilder');
INSERT INTO `hs_hr_currency_type` VALUES (138, 'STD', 'Sao Tome/Principe Dobra');
INSERT INTO `hs_hr_currency_type` VALUES (139, 'SVC', 'El Salvador Colon');
INSERT INTO `hs_hr_currency_type` VALUES (140, 'SYP', 'Syrian Pound');
INSERT INTO `hs_hr_currency_type` VALUES (141, 'SZL', 'Swaziland Lilangeni');
INSERT INTO `hs_hr_currency_type` VALUES (142, 'THB', 'Thai Baht');
INSERT INTO `hs_hr_currency_type` VALUES (143, 'TND', 'Tunisian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (144, 'TOP', 'Tongan Pa''anga');
INSERT INTO `hs_hr_currency_type` VALUES (145, 'TRL', 'Turkish Lira');
INSERT INTO `hs_hr_currency_type` VALUES (146, 'TTD', 'Trinidad/Tobago Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (147, 'TWD', 'Taiwan Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (148, 'TZS', 'Tanzanian Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (149, 'UAH', 'Ukraine Hryvnia');
INSERT INTO `hs_hr_currency_type` VALUES (150, 'UGX', 'Uganda Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (151, 'USD', 'United States Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (152, 'UYP', 'Uruguayan Peso');
INSERT INTO `hs_hr_currency_type` VALUES (153, 'VEB', 'Venezuelan Bolivar');
INSERT INTO `hs_hr_currency_type` VALUES (154, 'VND', 'Vietnamese Dong');
INSERT INTO `hs_hr_currency_type` VALUES (155, 'VUV', 'Vanuatu Vatu');
INSERT INTO `hs_hr_currency_type` VALUES (156, 'WST', 'Samoan Tala');
INSERT INTO `hs_hr_currency_type` VALUES (158, 'XAF', 'CFA Franc BEAC');
INSERT INTO `hs_hr_currency_type` VALUES (159, 'XAG', 'Silver (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (160, 'XAU', 'Gold (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (161, 'XCD', 'Eastern Caribbean Dollars');
INSERT INTO `hs_hr_currency_type` VALUES (179, 'XDR', 'IMF Special Drawing Right');
INSERT INTO `hs_hr_currency_type` VALUES (157, 'XEU', 'ECU');
INSERT INTO `hs_hr_currency_type` VALUES (162, 'XOF', 'CFA Franc BCEAO');
INSERT INTO `hs_hr_currency_type` VALUES (163, 'XPD', 'Palladium (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (164, 'XPF', 'Franc des Comptoirs français du Pacifique');
INSERT INTO `hs_hr_currency_type` VALUES (165, 'XPT', 'Platinum (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (166, 'YER', 'Yemeni Riyal');
INSERT INTO `hs_hr_currency_type` VALUES (167, 'YUM', 'Yugoslavian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (175, 'YUN', 'Yugoslav Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (168, 'ZAR', 'South African Rand');
INSERT INTO `hs_hr_currency_type` VALUES (176, 'ZMK', 'Zambian Kwacha');
INSERT INTO `hs_hr_currency_type` VALUES (169, 'ZRN', 'New Zaire');
INSERT INTO `hs_hr_currency_type` VALUES (170, 'ZWD', 'Zimbabwe Dollar');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_db_version`
-- 

DROP TABLE IF EXISTS `hs_hr_db_version`;
CREATE TABLE `hs_hr_db_version` (
  `id` varchar(36) NOT NULL default '',
  `name` varchar(45) default NULL,
  `description` varchar(100) default NULL,
  `entered_date` datetime default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `entered_by` varchar(36) default NULL,
  `modified_by` varchar(36) default NULL,
  PRIMARY KEY  (`id`),
  KEY `entered_by` (`entered_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_db_version`
-- 

INSERT INTO `hs_hr_db_version` VALUES ('DVR001', 'mysql4.1', 'initial DB', '2005-10-10 00:00:00', '2005-12-20 00:00:00', NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_developer`
-- 

DROP TABLE IF EXISTS `hs_hr_developer`;
CREATE TABLE `hs_hr_developer` (
  `id` varchar(36) NOT NULL default '',
  `first_name` varchar(45) default NULL,
  `last_name` varchar(45) default NULL,
  `reports_to_id` varchar(45) default NULL,
  `description` varchar(200) default NULL,
  `department` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_developer`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_district`
-- 

DROP TABLE IF EXISTS `hs_hr_district`;
CREATE TABLE `hs_hr_district` (
  `district_code` varchar(6) NOT NULL default '',
  `district_name` varchar(50) default NULL,
  `province_code` varchar(6) default NULL,
  PRIMARY KEY  (`district_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_district`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_education`
-- 

DROP TABLE IF EXISTS `hs_hr_education`;
CREATE TABLE `hs_hr_education` (
  `edu_code` varchar(6) NOT NULL default '',
  `edu_uni` varchar(100) default NULL,
  `edu_deg` varchar(100) default NULL,
  PRIMARY KEY  (`edu_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_education`
-- 

INSERT INTO `hs_hr_education` VALUES ('EDU001', 'University of Moratuwa', 'Bachelor of Science in Engineering');
INSERT INTO `hs_hr_education` VALUES ('EDU002', 'University of Colombo', 'Bachelor of Science in Computer Science');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_eec`
-- 

DROP TABLE IF EXISTS `hs_hr_eec`;
CREATE TABLE `hs_hr_eec` (
  `eec_code` varchar(6) NOT NULL default '',
  `eec_desc` varchar(50) default NULL,
  PRIMARY KEY  (`eec_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_eec`
-- 

INSERT INTO `hs_hr_eec` VALUES ('EEC001', 'OFFICIALS AND ADMINISTRATORS');
INSERT INTO `hs_hr_eec` VALUES ('EEC002', 'PROFESSIONALS');
INSERT INTO `hs_hr_eec` VALUES ('EEC003', 'TECHNICIANS');
INSERT INTO `hs_hr_eec` VALUES ('EEC004', 'PROTECTIVE SERVICE WORKERS');
INSERT INTO `hs_hr_eec` VALUES ('EEC005', 'PARAPROFESSIONALS');
INSERT INTO `hs_hr_eec` VALUES ('EEC006', 'ADMINISTRATIVE SUPPORT');
INSERT INTO `hs_hr_eec` VALUES ('EEC007', 'SKILLED CRAFT WORKERS');
INSERT INTO `hs_hr_eec` VALUES ('EEC008', 'SERVICE-MAINTENANCE');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_attachment`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_attachment`;
CREATE TABLE `hs_hr_emp_attachment` (
  `emp_number` varchar(6) NOT NULL default '',
  `eattach_id` decimal(10,0) NOT NULL default '0',
  `eattach_desc` varchar(200) default NULL,
  `eattach_filename` varchar(100) default NULL,
  `eattach_size` int(11) default '0',
  `eattach_attachment` mediumblob,
  `eattach_type` varchar(50) default NULL,
  PRIMARY KEY  (`emp_number`,`eattach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_attachment`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_basicsalary`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_basicsalary`;
CREATE TABLE `hs_hr_emp_basicsalary` (
  `emp_number` varchar(6) NOT NULL default '',
  `sal_grd_code` varchar(6) NOT NULL default '',
  `currency_id` varchar(6) NOT NULL default '',
  `ebsal_basic_salary` float default NULL,
  PRIMARY KEY  (`emp_number`,`sal_grd_code`,`currency_id`),
  KEY `sal_grd_code` (`sal_grd_code`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_basicsalary`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_children`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_children`;
CREATE TABLE `hs_hr_emp_children` (
  `emp_number` varchar(6) NOT NULL default '',
  `ec_seqno` decimal(2,0) NOT NULL default '0',
  `ec_name` varchar(100) default '',
  `ec_date_of_birth` date default '0000-00-00',
  PRIMARY KEY  (`emp_number`,`ec_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_children`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_contract_extend`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_contract_extend`;
CREATE TABLE `hs_hr_emp_contract_extend` (
  `emp_number` varchar(6) NOT NULL default '',
  `econ_extend_id` decimal(10,0) NOT NULL default '0',
  `econ_extend_start_date` datetime default NULL,
  `econ_extend_end_date` datetime default NULL,
  PRIMARY KEY  (`emp_number`,`econ_extend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_contract_extend`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_dependents`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_dependents`;
CREATE TABLE `hs_hr_emp_dependents` (
  `emp_number` varchar(6) NOT NULL default '',
  `ed_seqno` decimal(2,0) NOT NULL default '0',
  `ed_name` varchar(100) default '',
  `ed_relationship` varchar(100) default '',
  PRIMARY KEY  (`emp_number`,`ed_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_dependents`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_education`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_education`;
CREATE TABLE `hs_hr_emp_education` (
  `emp_number` varchar(6) NOT NULL default '',
  `edu_code` varchar(6) NOT NULL default '',
  `edu_major` varchar(100) default NULL,
  `edu_year` decimal(4,0) default NULL,
  `edu_gpa` varchar(25) default NULL,
  `edu_start_date` datetime default NULL,
  `edu_end_date` datetime default NULL,
  PRIMARY KEY  (`edu_code`,`emp_number`),
  KEY `emp_number` (`emp_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_education`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_emergency_contacts`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_emergency_contacts`;
CREATE TABLE `hs_hr_emp_emergency_contacts` (
  `emp_number` varchar(6) NOT NULL default '',
  `eec_seqno` decimal(2,0) NOT NULL default '0',
  `eec_name` varchar(100) default '',
  `eec_relationship` varchar(100) default '',
  `eec_home_no` varchar(100) default '',
  `eec_mobile_no` varchar(100) default '',
  `eec_office_no` varchar(100) default '',
  PRIMARY KEY  (`emp_number`,`eec_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_emergency_contacts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_history_of_ealier_pos`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_history_of_ealier_pos`;
CREATE TABLE `hs_hr_emp_history_of_ealier_pos` (
  `emp_number` varchar(6) NOT NULL default '',
  `emp_seqno` decimal(2,0) NOT NULL default '0',
  `ehoep_job_title` varchar(100) default '',
  `ehoep_years` varchar(100) default '',
  PRIMARY KEY  (`emp_number`,`emp_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_history_of_ealier_pos`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_language`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_language`;
CREATE TABLE `hs_hr_emp_language` (
  `emp_number` varchar(6) NOT NULL default '',
  `lang_code` varchar(6) NOT NULL default '',
  `elang_type` smallint(6) NOT NULL default '0',
  `competency` smallint(6) default '0',
  PRIMARY KEY  (`emp_number`,`lang_code`,`elang_type`),
  KEY `lang_code` (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_language`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_licenses`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_licenses`;
CREATE TABLE `hs_hr_emp_licenses` (
  `emp_number` varchar(6) NOT NULL default '',
  `licenses_code` varchar(100) NOT NULL default '',
  `licenses_date` date NOT NULL default '0000-00-00',
  `licenses_renewal_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`emp_number`,`licenses_code`),
  KEY `licenses_code` (`licenses_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_licenses`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_member_detail`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_member_detail`;
CREATE TABLE `hs_hr_emp_member_detail` (
  `emp_number` varchar(6) NOT NULL default '',
  `membship_code` varchar(6) NOT NULL default '',
  `membtype_code` varchar(6) NOT NULL default '',
  `ememb_subscript_ownership` varchar(20) default NULL,
  `ememb_subscript_amount` decimal(15,2) default NULL,
  `ememb_commence_date` datetime default NULL,
  `ememb_renewal_date` datetime default NULL,
  PRIMARY KEY  (`emp_number`,`membship_code`,`membtype_code`),
  KEY `membtype_code` (`membtype_code`),
  KEY `membship_code` (`membship_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_member_detail`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_passport`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_passport`;
CREATE TABLE `hs_hr_emp_passport` (
  `emp_number` varchar(6) NOT NULL default '',
  `ep_seqno` decimal(2,0) NOT NULL default '0',
  `ep_passport_num` varchar(100) NOT NULL default '',
  `ep_passportissueddate` datetime default NULL,
  `ep_passportexpiredate` datetime default NULL,
  `ep_comments` varchar(255) default NULL,
  `ep_passport_type_flg` smallint(6) default NULL,
  `ep_i9_status` varchar(100) default '',
  `ep_i9_review_date` date default '0000-00-00',
  `cou_code` varchar(6) default NULL,
  PRIMARY KEY  (`emp_number`,`ep_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_passport`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_picture`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_picture`;
CREATE TABLE `hs_hr_emp_picture` (
  `emp_number` varchar(6) NOT NULL default '',
  `epic_picture` blob,
  `epic_filename` varchar(100) default NULL,
  `epic_type` varchar(50) default NULL,
  `epic_file_size` varchar(20) default NULL,
  PRIMARY KEY  (`emp_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_picture`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_reportto`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_reportto`;
CREATE TABLE `hs_hr_emp_reportto` (
  `erep_sup_emp_number` varchar(6) NOT NULL default '',
  `erep_sub_emp_number` varchar(6) NOT NULL default '',
  `erep_reporting_mode` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`erep_sup_emp_number`,`erep_sub_emp_number`,`erep_reporting_mode`),
  KEY `erep_sub_emp_number` (`erep_sub_emp_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_reportto`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_skill`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_skill`;
CREATE TABLE `hs_hr_emp_skill` (
  `emp_number` varchar(6) NOT NULL default '',
  `skill_code` varchar(6) NOT NULL default '',
  `years_of_exp` decimal(2,0) NOT NULL default '0',
  `comments` varchar(100) NOT NULL default '',
  KEY `emp_number` (`emp_number`),
  KEY `skill_code` (`skill_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_skill`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emp_work_experience`
-- 

DROP TABLE IF EXISTS `hs_hr_emp_work_experience`;
CREATE TABLE `hs_hr_emp_work_experience` (
  `emp_number` varchar(6) NOT NULL default '',
  `eexp_seqno` decimal(10,0) NOT NULL default '0',
  `eexp_employer` varchar(100) default NULL,
  `eexp_jobtit` varchar(120) default NULL,
  `eexp_from_date` datetime default NULL,
  `eexp_to_date` datetime default NULL,
  `eexp_comments` varchar(200) default NULL,
  `eexp_internal` int(1) default NULL,
  PRIMARY KEY  (`emp_number`,`eexp_seqno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emp_work_experience`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_employee`
-- 

DROP TABLE IF EXISTS `hs_hr_employee`;
CREATE TABLE `hs_hr_employee` (
  `emp_number` varchar(6) NOT NULL default '',
  `emp_lastname` varchar(100) default '',
  `emp_firstname` varchar(100) default '',
  `emp_middle_name` varchar(100) default '',
  `emp_nick_name` varchar(100) default '',
  `emp_smoker` smallint(6) default '0',
  `ethnic_race_code` varchar(100) default NULL,
  `emp_birthday` datetime default '0000-00-00 00:00:00',
  `nation_code` varchar(100) default NULL,
  `emp_gender` smallint(6) default NULL,
  `emp_marital_status` varchar(20) default NULL,
  `emp_ssn_num` varchar(100) default '',
  `emp_sin_num` varchar(100) default '',
  `emp_other_id` varchar(100) default '',
  `emp_dri_lice_num` varchar(100) default '',
  `emp_dri_lice_exp_date` date default '0000-00-00',
  `emp_military_service` varchar(100) default '',
  `emp_status` varchar(100) default NULL,
  `job_title_code` varchar(100) default NULL,
  `eeo_cat_code` varchar(100) default NULL,
  `work_station` int(6) default NULL,
  `emp_street1` varchar(100) default '',
  `emp_street2` varchar(100) default '',
  `city_code` varchar(100) default '',
  `coun_code` varchar(100) default '',
  `provin_code` varchar(100) default '',
  `emp_zipcode` varchar(20) default NULL,
  `emp_hm_telephone` varchar(50) default NULL,
  `emp_mobile` varchar(6) default NULL,
  `emp_work_telephone` varchar(6) default NULL,
  `emp_work_email` varchar(50) default NULL,
  `sal_grd_code` varchar(100) default NULL,
  `joined_date` date default '0000-00-00',
  `emp_oth_email` varchar(50) default NULL,
  PRIMARY KEY  (`emp_number`),
  KEY `work_station` (`work_station`),
  KEY `ethnic_race_code` (`ethnic_race_code`),
  KEY `nation_code` (`nation_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_employee`
-- 

INSERT INTO `hs_hr_employee` VALUES ('EMP001', 'Prasad', 'Frey', '', 'Franc', 0, NULL, '0000-00-00 00:00:00', NULL, NULL, NULL, '', '', '', '', '0000-00-00', '', NULL, NULL, NULL, NULL, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', NULL);
INSERT INTO `hs_hr_employee` VALUES ('EMP002', 'Arnold', 'Subasinghe', '', 'Arnold', 0, NULL, '0000-00-00 00:00:00', NULL, NULL, NULL, '', '', '', '', '0000-00-00', '', NULL, NULL, NULL, NULL, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_emprep_usergroup`
-- 

DROP TABLE IF EXISTS `hs_hr_emprep_usergroup`;
CREATE TABLE `hs_hr_emprep_usergroup` (
  `userg_id` varchar(6) NOT NULL default '',
  `rep_code` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`userg_id`,`rep_code`),
  KEY `rep_code` (`rep_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_emprep_usergroup`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_empreport`
-- 

DROP TABLE IF EXISTS `hs_hr_empreport`;
CREATE TABLE `hs_hr_empreport` (
  `rep_code` varchar(6) NOT NULL default '',
  `rep_name` varchar(60) default NULL,
  `rep_cridef_str` varchar(100) default NULL,
  `rep_flddef_str` varchar(100) default NULL,
  PRIMARY KEY  (`rep_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_empreport`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_empstat`
-- 

DROP TABLE IF EXISTS `hs_hr_empstat`;
CREATE TABLE `hs_hr_empstat` (
  `estat_code` varchar(6) NOT NULL default '',
  `estat_name` varchar(50) default NULL,
  PRIMARY KEY  (`estat_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_empstat`
-- 

INSERT INTO `hs_hr_empstat` VALUES ('EST001', 'Permanent');
INSERT INTO `hs_hr_empstat` VALUES ('EST002', 'Part Time');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_ethnic_race`
-- 

DROP TABLE IF EXISTS `hs_hr_ethnic_race`;
CREATE TABLE `hs_hr_ethnic_race` (
  `ethnic_race_code` varchar(6) NOT NULL default '',
  `ethnic_race_desc` varchar(50) default NULL,
  PRIMARY KEY  (`ethnic_race_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_ethnic_race`
-- 

INSERT INTO `hs_hr_ethnic_race` VALUES ('ETH001', 'Sinhala');
INSERT INTO `hs_hr_ethnic_race` VALUES ('ETH002', 'Islam');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_file_version`
-- 

DROP TABLE IF EXISTS `hs_hr_file_version`;
CREATE TABLE `hs_hr_file_version` (
  `id` varchar(36) NOT NULL default '',
  `altered_module` varchar(36) default NULL,
  `description` varchar(200) default NULL,
  `entered_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `entered_by` varchar(36) default NULL,
  `modified_by` varchar(36) default NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `altered_module` (`altered_module`),
  KEY `entered_by` (`entered_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_file_version`
-- 

INSERT INTO `hs_hr_file_version` VALUES ('FVR001', NULL, 'Release 1', '2006-03-15 00:00:00', '2006-03-15 00:00:00', NULL, NULL, 'file_ver_01');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_geninfo`
-- 

DROP TABLE IF EXISTS `hs_hr_geninfo`;
CREATE TABLE `hs_hr_geninfo` (
  `code` varchar(8) NOT NULL default '',
  `geninfo_keys` varchar(200) default NULL,
  `geninfo_values` varchar(200) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_geninfo`
-- 

INSERT INTO `hs_hr_geninfo` VALUES ('001', 'COMPANY|COUNTRY|STREET1|STREET2|STATE|CITY|ZIP|PHONE|FAX|TAX|NAICS|COMMENTS', 'hSenid|0||||||||||');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_job_title`
-- 

DROP TABLE IF EXISTS `hs_hr_job_title`;
CREATE TABLE `hs_hr_job_title` (
  `jobtit_code` varchar(6) NOT NULL default '',
  `jobtit_name` varchar(50) default NULL,
  `jobtit_desc` varchar(200) default NULL,
  `jobtit_comm` varchar(400) default NULL,
  `sal_grd_code` varchar(6) default NULL,
  PRIMARY KEY  (`jobtit_code`),
  KEY `sal_grd_code` (`sal_grd_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_job_title`
-- 

INSERT INTO `hs_hr_job_title` VALUES ('JOB001', 'Web Developer', 'RTT', 'YYX', 'SAL001');
INSERT INTO `hs_hr_job_title` VALUES ('JOB002', 'Technical Writer', 'Technical Writer', 'RTX', 'SAL001');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_jobtit_empstat`
-- 

DROP TABLE IF EXISTS `hs_hr_jobtit_empstat`;
CREATE TABLE `hs_hr_jobtit_empstat` (
  `jobtit_code` varchar(6) NOT NULL default '',
  `estat_code` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`jobtit_code`,`estat_code`),
  KEY `estat_code` (`estat_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_jobtit_empstat`
-- 

INSERT INTO `hs_hr_jobtit_empstat` VALUES ('JOB001', 'EST001');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_language`
-- 

DROP TABLE IF EXISTS `hs_hr_language`;
CREATE TABLE `hs_hr_language` (
  `lang_code` varchar(6) NOT NULL default '',
  `lang_name` varchar(120) default NULL,
  PRIMARY KEY  (`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_language`
-- 

INSERT INTO `hs_hr_language` VALUES ('LAN001', 'Tamil');
INSERT INTO `hs_hr_language` VALUES ('LAN002', 'French');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_licenses`
-- 

DROP TABLE IF EXISTS `hs_hr_licenses`;
CREATE TABLE `hs_hr_licenses` (
  `licenses_code` varchar(6) NOT NULL default '',
  `licenses_desc` varchar(50) default NULL,
  PRIMARY KEY  (`licenses_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_licenses`
-- 

INSERT INTO `hs_hr_licenses` VALUES ('LIC001', 'Driving License');
INSERT INTO `hs_hr_licenses` VALUES ('LIC002', 'Surveyor');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_location`
-- 

DROP TABLE IF EXISTS `hs_hr_location`;
CREATE TABLE `hs_hr_location` (
  `loc_code` varchar(6) NOT NULL default '',
  `loc_name` varchar(100) default NULL,
  `loc_country` varchar(3) default NULL,
  `loc_state` varchar(50) default NULL,
  `loc_city` varchar(50) default NULL,
  `loc_add` varchar(100) default NULL,
  `loc_zip` varchar(10) default NULL,
  `loc_phone` varchar(30) default NULL,
  `loc_fax` varchar(30) default NULL,
  `loc_comments` varchar(100) default NULL,
  PRIMARY KEY  (`loc_code`),
  KEY `loc_country` (`loc_country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_location`
-- 

INSERT INTO `hs_hr_location` VALUES ('LOC001', 'Nawam Mawatha', 'LK', 'Western', 'Colombo', 'Sayuru Sevana', '00200', '2446623', '2307579', 'RTZ');
INSERT INTO `hs_hr_location` VALUES ('LOC002', 'NJ', 'US', 'NJ', 'Secaucus', 'hSenid Software USA Inc.\r\n538, Teal Plaza', '07094', '2013622585', '1917592944', 'RTX');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_membership`
-- 

DROP TABLE IF EXISTS `hs_hr_membership`;
CREATE TABLE `hs_hr_membership` (
  `membship_code` varchar(6) NOT NULL default '',
  `membtype_code` varchar(6) default NULL,
  `membship_name` varchar(120) default NULL,
  PRIMARY KEY  (`membship_code`),
  KEY `membtype_code` (`membtype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_membership`
-- 

INSERT INTO `hs_hr_membership` VALUES ('MME001', 'MEM001', 'BCS');
INSERT INTO `hs_hr_membership` VALUES ('MME002', 'MEM001', 'CIMA');
INSERT INTO `hs_hr_membership` VALUES ('MME003', 'MEM002', 'SIAM');
INSERT INTO `hs_hr_membership` VALUES ('MME004', 'MEM002', 'FIG');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_membership_type`
-- 

DROP TABLE IF EXISTS `hs_hr_membership_type`;
CREATE TABLE `hs_hr_membership_type` (
  `membtype_code` varchar(6) NOT NULL default '',
  `membtype_name` varchar(120) default NULL,
  PRIMARY KEY  (`membtype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_membership_type`
-- 

INSERT INTO `hs_hr_membership_type` VALUES ('MEM001', 'Professional');
INSERT INTO `hs_hr_membership_type` VALUES ('MEM002', 'Academic');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_module`
-- 

DROP TABLE IF EXISTS `hs_hr_module`;
CREATE TABLE `hs_hr_module` (
  `mod_id` varchar(36) NOT NULL default '',
  `name` varchar(45) default NULL,
  `owner` varchar(45) default NULL,
  `owner_email` varchar(100) default NULL,
  `version` varchar(36) default NULL,
  `description` text,
  PRIMARY KEY  (`mod_id`),
  KEY `version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_module`
-- 

INSERT INTO `hs_hr_module` VALUES ('MOD001', 'Admin', 'Koshika', 'koshika@beyondm.net', 'VER001', 'HR Admin');
INSERT INTO `hs_hr_module` VALUES ('MOD002', 'PIM', 'Koshika', 'koshika@beyondm.net', 'VER001', 'HR Functions');
INSERT INTO `hs_hr_module` VALUES ('MOD003', 'Maintenance', 'Koshika', 'koshika@beyondm.net', 'VER001', 'Application Maintenance');
INSERT INTO `hs_hr_module` VALUES ('MOD004', 'Report', 'Koshika', 'koshika@beyondm.net', 'VER001', 'Reporting');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_nationality`
-- 

DROP TABLE IF EXISTS `hs_hr_nationality`;
CREATE TABLE `hs_hr_nationality` (
  `nat_code` varchar(6) NOT NULL default '',
  `nat_name` varchar(120) default NULL,
  PRIMARY KEY  (`nat_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_nationality`
-- 

INSERT INTO `hs_hr_nationality` VALUES ('NAT001', 'Sri Lankan');
INSERT INTO `hs_hr_nationality` VALUES ('NAT002', 'Indian');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_province`
-- 

DROP TABLE IF EXISTS `hs_hr_province`;
CREATE TABLE `hs_hr_province` (
  `id` int(11) NOT NULL auto_increment,
  `province_name` varchar(40) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `cou_code` char(2) NOT NULL default 'US',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

-- 
-- Dumping data for table `hs_hr_province`
-- 

INSERT INTO `hs_hr_province` VALUES (1, 'Alaska', 'AK', 'US');
INSERT INTO `hs_hr_province` VALUES (2, 'Alabama', 'AL', 'US');
INSERT INTO `hs_hr_province` VALUES (3, 'American Samoa', 'AS', 'US');
INSERT INTO `hs_hr_province` VALUES (4, 'Arizona', 'AZ', 'US');
INSERT INTO `hs_hr_province` VALUES (5, 'Arkansas', 'AR', 'US');
INSERT INTO `hs_hr_province` VALUES (6, 'California', 'CA', 'US');
INSERT INTO `hs_hr_province` VALUES (7, 'Colorado', 'CO', 'US');
INSERT INTO `hs_hr_province` VALUES (8, 'Connecticut', 'CT', 'US');
INSERT INTO `hs_hr_province` VALUES (9, 'Delaware', 'DE', 'US');
INSERT INTO `hs_hr_province` VALUES (10, 'District of Columbia', 'DC', 'US');
INSERT INTO `hs_hr_province` VALUES (11, 'Federated States of Micronesia', 'FM', 'US');
INSERT INTO `hs_hr_province` VALUES (12, 'Florida', 'FL', 'US');
INSERT INTO `hs_hr_province` VALUES (13, 'Georgia', 'GA', 'US');
INSERT INTO `hs_hr_province` VALUES (14, 'Guam', 'GU', 'US');
INSERT INTO `hs_hr_province` VALUES (15, 'Hawaii', 'HI', 'US');
INSERT INTO `hs_hr_province` VALUES (16, 'Idaho', 'ID', 'US');
INSERT INTO `hs_hr_province` VALUES (17, 'Illinois', 'IL', 'US');
INSERT INTO `hs_hr_province` VALUES (18, 'Indiana', 'IN', 'US');
INSERT INTO `hs_hr_province` VALUES (19, 'Iowa', 'IA', 'US');
INSERT INTO `hs_hr_province` VALUES (20, 'Kansas', 'KS', 'US');
INSERT INTO `hs_hr_province` VALUES (21, 'Kentucky', 'KY', 'US');
INSERT INTO `hs_hr_province` VALUES (22, 'Louisiana', 'LA', 'US');
INSERT INTO `hs_hr_province` VALUES (23, 'Maine', 'ME', 'US');
INSERT INTO `hs_hr_province` VALUES (24, 'Marshall Islands', 'MH', 'US');
INSERT INTO `hs_hr_province` VALUES (25, 'Maryland', 'MD', 'US');
INSERT INTO `hs_hr_province` VALUES (26, 'Massachusetts', 'MA', 'US');
INSERT INTO `hs_hr_province` VALUES (27, 'Michigan', 'MI', 'US');
INSERT INTO `hs_hr_province` VALUES (28, 'Minnesota', 'MN', 'US');
INSERT INTO `hs_hr_province` VALUES (29, 'Mississippi', 'MS', 'US');
INSERT INTO `hs_hr_province` VALUES (30, 'Missouri', 'MO', 'US');
INSERT INTO `hs_hr_province` VALUES (31, 'Montana', 'MT', 'US');
INSERT INTO `hs_hr_province` VALUES (32, 'Nebraska', 'NE', 'US');
INSERT INTO `hs_hr_province` VALUES (33, 'Nevada', 'NV', 'US');
INSERT INTO `hs_hr_province` VALUES (34, 'New Hampshire', 'NH', 'US');
INSERT INTO `hs_hr_province` VALUES (35, 'New Jersey', 'NJ', 'US');
INSERT INTO `hs_hr_province` VALUES (36, 'New Mexico', 'NM', 'US');
INSERT INTO `hs_hr_province` VALUES (37, 'New York', 'NY', 'US');
INSERT INTO `hs_hr_province` VALUES (38, 'North Carolina', 'NC', 'US');
INSERT INTO `hs_hr_province` VALUES (39, 'North Dakota', 'ND', 'US');
INSERT INTO `hs_hr_province` VALUES (40, 'Northern Mariana Islands', 'MP', 'US');
INSERT INTO `hs_hr_province` VALUES (41, 'Ohio', 'OH', 'US');
INSERT INTO `hs_hr_province` VALUES (42, 'Oklahoma', 'OK', 'US');
INSERT INTO `hs_hr_province` VALUES (43, 'Oregon', 'OR', 'US');
INSERT INTO `hs_hr_province` VALUES (44, 'Palau', 'PW', 'US');
INSERT INTO `hs_hr_province` VALUES (45, 'Pennsylvania', 'PA', 'US');
INSERT INTO `hs_hr_province` VALUES (46, 'Puerto Rico', 'PR', 'US');
INSERT INTO `hs_hr_province` VALUES (47, 'Rhode Island', 'RI', 'US');
INSERT INTO `hs_hr_province` VALUES (48, 'South Carolina', 'SC', 'US');
INSERT INTO `hs_hr_province` VALUES (49, 'South Dakota', 'SD', 'US');
INSERT INTO `hs_hr_province` VALUES (50, 'Tennessee', 'TN', 'US');
INSERT INTO `hs_hr_province` VALUES (51, 'Texas', 'TX', 'US');
INSERT INTO `hs_hr_province` VALUES (52, 'Utah', 'UT', 'US');
INSERT INTO `hs_hr_province` VALUES (53, 'Vermont', 'VT', 'US');
INSERT INTO `hs_hr_province` VALUES (54, 'Virgin Islands', 'VI', 'US');
INSERT INTO `hs_hr_province` VALUES (55, 'Virginia', 'VA', 'US');
INSERT INTO `hs_hr_province` VALUES (56, 'Washington', 'WA', 'US');
INSERT INTO `hs_hr_province` VALUES (57, 'West Virginia', 'WV', 'US');
INSERT INTO `hs_hr_province` VALUES (58, 'Wisconsin', 'WI', 'US');
INSERT INTO `hs_hr_province` VALUES (59, 'Wyoming', 'WY', 'US');
INSERT INTO `hs_hr_province` VALUES (60, 'Armed Forces Africa', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (61, 'Armed Forces Americas (except Canada)', 'AA', 'US');
INSERT INTO `hs_hr_province` VALUES (62, 'Armed Forces Canada', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (63, 'Armed Forces Europe', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (64, 'Armed Forces Middle East', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (65, 'Armed Forces Pacific', 'AP', 'US');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_rights`
-- 

DROP TABLE IF EXISTS `hs_hr_rights`;
CREATE TABLE `hs_hr_rights` (
  `userg_id` varchar(36) NOT NULL default '',
  `mod_id` varchar(36) NOT NULL default '',
  `addition` smallint(5) unsigned default '0',
  `editing` smallint(5) unsigned default '0',
  `deletion` smallint(5) unsigned default '0',
  `viewing` smallint(5) unsigned default '0',
  PRIMARY KEY  (`mod_id`,`userg_id`),
  KEY `userg_id` (`userg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_rights`
-- 

INSERT INTO `hs_hr_rights` VALUES ('USG001', 'MOD001', 1, 1, 1, 1);
INSERT INTO `hs_hr_rights` VALUES ('USG001', 'MOD002', 1, 1, 1, 1);
INSERT INTO `hs_hr_rights` VALUES ('USG001', 'MOD003', 1, 1, 1, 1);
INSERT INTO `hs_hr_rights` VALUES ('USG001', 'MOD004', 1, 1, 1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_skill`
-- 

DROP TABLE IF EXISTS `hs_hr_skill`;
CREATE TABLE `hs_hr_skill` (
  `skill_code` varchar(6) NOT NULL default '',
  `skill_name` varchar(120) default NULL,
  PRIMARY KEY  (`skill_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_skill`
-- 

INSERT INTO `hs_hr_skill` VALUES ('SKI001', 'Driving');
INSERT INTO `hs_hr_skill` VALUES ('SKI002', 'Rowing');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_user_group`
-- 

DROP TABLE IF EXISTS `hs_hr_user_group`;
CREATE TABLE `hs_hr_user_group` (
  `userg_id` varchar(36) NOT NULL default '',
  `userg_name` varchar(45) default NULL,
  `userg_repdef` smallint(5) unsigned default '0',
  PRIMARY KEY  (`userg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_user_group`
-- 

INSERT INTO `hs_hr_user_group` VALUES ('USG001', 'Admin', 1);
INSERT INTO `hs_hr_user_group` VALUES ('USG002', 'HR Excecutive', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_users`
-- 

DROP TABLE IF EXISTS `hs_hr_users`;
CREATE TABLE `hs_hr_users` (
  `id` varchar(36) NOT NULL default '',
  `user_name` varchar(20) default '',
  `user_password` varchar(32) default NULL,
  `first_name` varchar(45) default NULL,
  `last_name` varchar(45) default NULL,
  `emp_number` varchar(36) default NULL,
  `user_hash` varchar(32) default NULL,
  `is_admin` char(3) default NULL,
  `receive_notification` char(1) default NULL,
  `description` text,
  `date_entered` datetime default '0000-00-00 00:00:00',
  `date_modified` datetime default '0000-00-00 00:00:00',
  `modified_user_id` varchar(36) default NULL,
  `created_by` varchar(36) default NULL,
  `title` varchar(50) default NULL,
  `department` varchar(50) default NULL,
  `phone_home` varchar(45) default NULL,
  `phone_mobile` varchar(45) default NULL,
  `phone_work` varchar(45) default NULL,
  `phone_other` varchar(45) default NULL,
  `phone_fax` varchar(45) default NULL,
  `email1` varchar(100) default NULL,
  `email2` varchar(100) default NULL,
  `status` varchar(25) default NULL,
  `address_street` varchar(150) default NULL,
  `address_city` varchar(150) default NULL,
  `address_state` varchar(100) default NULL,
  `address_country` varchar(25) default NULL,
  `address_postalcode` varchar(10) default NULL,
  `user_preferences` text,
  `deleted` tinyint(1) NOT NULL default '0',
  `employee_status` varchar(25) default NULL,
  `userg_id` varchar(36) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_name` USING BTREE (`user_name`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `created_by` (`created_by`),
  KEY `userg_id` (`userg_id`),
  KEY `emp_number` (`emp_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_users`
-- 

INSERT INTO `hs_hr_users` VALUES ('USR001', 'Admin', 'f2c84b9f7a7bff99f21cdbae50238873', 'Admin', '', NULL, '', 'Yes', '1', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL, '', '', '', '', '', '', '', '', '', 'Enabled', '', '', '', '', '', '', 0, '', 'USG001');
INSERT INTO `hs_hr_users` VALUES ('USR002', 'Samie', 'ff7539f768d2569642f23bf3035d32c6', NULL, NULL, NULL, NULL, 'Yes', NULL, NULL, '2006-09-21 00:00:00', '0000-00-00 00:00:00', NULL, 'USR001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Enabled', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'USG001');
INSERT INTO `hs_hr_users` VALUES ('USR003', 'Prasad', '59402cf1a28054b8df0d6cdd62dec86c', NULL, NULL, 'EMP001', NULL, 'No', NULL, NULL, '2006-09-21 00:00:00', '0000-00-00 00:00:00', NULL, 'USR001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Enabled', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL);
INSERT INTO `hs_hr_users` VALUES ('USR004', 'Arnold', '0952dfc8d6a1ba5cdde6b842203a0cb7', NULL, NULL, 'EMP002', NULL, 'No', NULL, NULL, '2006-09-21 00:00:00', '0000-00-00 00:00:00', NULL, 'USR001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Enabled', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_hr_versions`
-- 

DROP TABLE IF EXISTS `hs_hr_versions`;
CREATE TABLE `hs_hr_versions` (
  `id` varchar(36) NOT NULL default '',
  `name` varchar(45) default NULL,
  `entered_date` datetime default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `modified_by` varchar(36) default NULL,
  `created_by` varchar(36) default NULL,
  `deleted` tinyint(4) NOT NULL default '0',
  `db_version` varchar(36) default NULL,
  `file_version` varchar(36) default NULL,
  `description` text,
  PRIMARY KEY  (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `db_version` (`db_version`),
  KEY `file_version` (`file_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_hr_versions`
-- 

INSERT INTO `hs_hr_versions` VALUES ('VER001', 'Release 1', '2006-03-15 00:00:00', '2006-03-15 00:00:00', NULL, NULL, 0, 'DVR001', 'FVR001', 'version 1.0');

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_pr_salary_currency_detail`
-- 

DROP TABLE IF EXISTS `hs_pr_salary_currency_detail`;
CREATE TABLE `hs_pr_salary_currency_detail` (
  `sal_grd_code` varchar(6) NOT NULL default '',
  `currency_id` varchar(6) NOT NULL default '',
  `salcurr_dtl_minsalary` float default NULL,
  `salcurr_dtl_stepsalary` float default NULL,
  `salcurr_dtl_maxsalary` float default NULL,
  PRIMARY KEY  (`sal_grd_code`,`currency_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_pr_salary_currency_detail`
-- 

INSERT INTO `hs_pr_salary_currency_detail` VALUES ('SAL001', 'BTN', 123, 235, 456);
INSERT INTO `hs_pr_salary_currency_detail` VALUES ('SAL002', 'USD', 589, 15, 1588);

-- --------------------------------------------------------

-- 
-- Table structure for table `hs_pr_salary_grade`
-- 

DROP TABLE IF EXISTS `hs_pr_salary_grade`;
CREATE TABLE `hs_pr_salary_grade` (
  `sal_grd_code` varchar(6) NOT NULL default '',
  `sal_grd_name` varchar(60) default NULL,
  PRIMARY KEY  (`sal_grd_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `hs_pr_salary_grade`
-- 

INSERT INTO `hs_pr_salary_grade` VALUES ('SAL001', 'Rupee');
INSERT INTO `hs_pr_salary_grade` VALUES ('SAL002', 'Dollars');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `hs_hr_compstructtree`
-- 
ALTER TABLE `hs_hr_compstructtree`
  ADD CONSTRAINT `hs_hr_compstructtree_ibfk_1` FOREIGN KEY (`loc_code`) REFERENCES `hs_hr_location` (`loc_code`);

-- 
-- Constraints for table `hs_hr_db_version`
-- 
ALTER TABLE `hs_hr_db_version`
  ADD CONSTRAINT `hs_hr_db_version_ibfk_1` FOREIGN KEY (`entered_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_db_version_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_attachment`
-- 
ALTER TABLE `hs_hr_emp_attachment`
  ADD CONSTRAINT `hs_hr_emp_attachment_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_attachment_ibfk_2` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_basicsalary`
-- 
ALTER TABLE `hs_hr_emp_basicsalary`
  ADD CONSTRAINT `hs_hr_emp_basicsalary_ibfk_1` FOREIGN KEY (`sal_grd_code`) REFERENCES `hs_pr_salary_grade` (`sal_grd_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_basicsalary_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `hs_hr_currency_type` (`currency_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_basicsalary_ibfk_3` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_children`
-- 
ALTER TABLE `hs_hr_emp_children`
  ADD CONSTRAINT `hs_hr_emp_children_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_contract_extend`
-- 
ALTER TABLE `hs_hr_emp_contract_extend`
  ADD CONSTRAINT `hs_hr_emp_contract_extend_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_dependents`
-- 
ALTER TABLE `hs_hr_emp_dependents`
  ADD CONSTRAINT `hs_hr_emp_dependents_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_education`
-- 
ALTER TABLE `hs_hr_emp_education`
  ADD CONSTRAINT `hs_hr_emp_education_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_education_ibfk_2` FOREIGN KEY (`edu_code`) REFERENCES `hs_hr_education` (`edu_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_emergency_contacts`
-- 
ALTER TABLE `hs_hr_emp_emergency_contacts`
  ADD CONSTRAINT `hs_hr_emp_emergency_contacts_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_history_of_ealier_pos`
-- 
ALTER TABLE `hs_hr_emp_history_of_ealier_pos`
  ADD CONSTRAINT `hs_hr_emp_history_of_ealier_pos_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_language`
-- 
ALTER TABLE `hs_hr_emp_language`
  ADD CONSTRAINT `hs_hr_emp_language_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_language_ibfk_2` FOREIGN KEY (`lang_code`) REFERENCES `hs_hr_language` (`lang_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_licenses`
-- 
ALTER TABLE `hs_hr_emp_licenses`
  ADD CONSTRAINT `hs_hr_emp_licenses_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_licenses_ibfk_2` FOREIGN KEY (`licenses_code`) REFERENCES `hs_hr_licenses` (`licenses_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_member_detail`
-- 
ALTER TABLE `hs_hr_emp_member_detail`
  ADD CONSTRAINT `hs_hr_emp_member_detail_ibfk_1` FOREIGN KEY (`membtype_code`) REFERENCES `hs_hr_membership_type` (`membtype_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_member_detail_ibfk_2` FOREIGN KEY (`membship_code`) REFERENCES `hs_hr_membership` (`membship_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_member_detail_ibfk_3` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_passport`
-- 
ALTER TABLE `hs_hr_emp_passport`
  ADD CONSTRAINT `hs_hr_emp_passport_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_picture`
-- 
ALTER TABLE `hs_hr_emp_picture`
  ADD CONSTRAINT `hs_hr_emp_picture_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_reportto`
-- 
ALTER TABLE `hs_hr_emp_reportto`
  ADD CONSTRAINT `hs_hr_emp_reportto_ibfk_1` FOREIGN KEY (`erep_sup_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_reportto_ibfk_2` FOREIGN KEY (`erep_sub_emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_skill`
-- 
ALTER TABLE `hs_hr_emp_skill`
  ADD CONSTRAINT `hs_hr_emp_skill_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emp_skill_ibfk_2` FOREIGN KEY (`skill_code`) REFERENCES `hs_hr_skill` (`skill_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emp_work_experience`
-- 
ALTER TABLE `hs_hr_emp_work_experience`
  ADD CONSTRAINT `hs_hr_emp_work_experience_ibfk_1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_employee`
-- 
ALTER TABLE `hs_hr_employee`
  ADD CONSTRAINT `hs_hr_employee_ibfk_1` FOREIGN KEY (`work_station`) REFERENCES `hs_hr_compstructtree` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_employee_ibfk_2` FOREIGN KEY (`ethnic_race_code`) REFERENCES `hs_hr_ethnic_race` (`ethnic_race_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_employee_ibfk_3` FOREIGN KEY (`nation_code`) REFERENCES `hs_hr_nationality` (`nat_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_emprep_usergroup`
-- 
ALTER TABLE `hs_hr_emprep_usergroup`
  ADD CONSTRAINT `hs_hr_emprep_usergroup_ibfk_1` FOREIGN KEY (`userg_id`) REFERENCES `hs_hr_user_group` (`userg_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_emprep_usergroup_ibfk_2` FOREIGN KEY (`rep_code`) REFERENCES `hs_hr_empreport` (`rep_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_file_version`
-- 
ALTER TABLE `hs_hr_file_version`
  ADD CONSTRAINT `hs_hr_file_version_ibfk_1` FOREIGN KEY (`altered_module`) REFERENCES `hs_hr_module` (`mod_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_file_version_ibfk_2` FOREIGN KEY (`entered_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_file_version_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_job_title`
-- 
ALTER TABLE `hs_hr_job_title`
  ADD CONSTRAINT `hs_hr_job_title_ibfk_1` FOREIGN KEY (`sal_grd_code`) REFERENCES `hs_pr_salary_grade` (`sal_grd_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_jobtit_empstat`
-- 
ALTER TABLE `hs_hr_jobtit_empstat`
  ADD CONSTRAINT `hs_hr_jobtit_empstat_ibfk_1` FOREIGN KEY (`jobtit_code`) REFERENCES `hs_hr_job_title` (`jobtit_code`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_jobtit_empstat_ibfk_2` FOREIGN KEY (`estat_code`) REFERENCES `hs_hr_empstat` (`estat_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_location`
-- 
ALTER TABLE `hs_hr_location`
  ADD CONSTRAINT `hs_hr_location_ibfk_1` FOREIGN KEY (`loc_country`) REFERENCES `hs_hr_country` (`cou_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_membership`
-- 
ALTER TABLE `hs_hr_membership`
  ADD CONSTRAINT `hs_hr_membership_ibfk_1` FOREIGN KEY (`membtype_code`) REFERENCES `hs_hr_membership_type` (`membtype_code`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_module`
-- 
ALTER TABLE `hs_hr_module`
  ADD CONSTRAINT `hs_hr_module_ibfk_1` FOREIGN KEY (`version`) REFERENCES `hs_hr_versions` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_rights`
-- 
ALTER TABLE `hs_hr_rights`
  ADD CONSTRAINT `hs_hr_rights_ibfk_1` FOREIGN KEY (`mod_id`) REFERENCES `hs_hr_module` (`mod_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_rights_ibfk_2` FOREIGN KEY (`userg_id`) REFERENCES `hs_hr_user_group` (`userg_id`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_hr_users`
-- 
ALTER TABLE `hs_hr_users`
  ADD CONSTRAINT `hs_hr_users_ibfk_1` FOREIGN KEY (`modified_user_id`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_users_ibfk_3` FOREIGN KEY (`userg_id`) REFERENCES `hs_hr_user_group` (`userg_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_users_ibfk_4` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`);

-- 
-- Constraints for table `hs_hr_versions`
-- 
ALTER TABLE `hs_hr_versions`
  ADD CONSTRAINT `hs_hr_versions_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_versions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `hs_hr_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_versions_ibfk_3` FOREIGN KEY (`db_version`) REFERENCES `hs_hr_db_version` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_hr_versions_ibfk_4` FOREIGN KEY (`file_version`) REFERENCES `hs_hr_file_version` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `hs_pr_salary_currency_detail`
-- 
ALTER TABLE `hs_pr_salary_currency_detail`
  ADD CONSTRAINT `hs_pr_salary_currency_detail_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `hs_hr_currency_type` (`currency_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hs_pr_salary_currency_detail_ibfk_2` FOREIGN KEY (`sal_grd_code`) REFERENCES `hs_pr_salary_grade` (`sal_grd_code`) ON DELETE CASCADE;
