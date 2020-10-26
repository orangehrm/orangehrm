<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Class SchemaIncrementTask77
 */
class SchemaIncrementTask77 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql[] = "CREATE TABLE `ohrm_i18n_group` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255),
  `title` VARCHAR(255) DEFAULT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

        $sql[] = "CREATE TABLE `ohrm_i18n_language` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  `code` VARCHAR(100) NOT NULL UNIQUE,
  `enabled` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `added` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` DATETIME DEFAULT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

        $sql[] = "CREATE TABLE `ohrm_i18n_lang_string` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `unit_id` INT NOT NULL,
  `source_id` INT,
  `group_id` INT DEFAULT NULL,
  `value` TEXT COLLATE utf8mb4_bin NOT NULL,
  `note` TEXT,
  `version` VARCHAR(20) DEFAULT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

        $sql[] = "CREATE TABLE `ohrm_i18n_translate` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `lang_string_id` INT NOT NULL,
  `language_id` INT NOT NULL,
  `value` TEXT,
  `translated` TINYINT UNSIGNED DEFAULT 1,
  `customized` TINYINT UNSIGNED DEFAULT 0,
  `modified_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

        $sql[] = "CREATE TABLE `ohrm_i18n_source` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `source` MEDIUMTEXT NOT NULL,
  `modified_at` DATETIME
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;";

        $sql[] = "ALTER TABLE `ohrm_i18n_lang_string`
    ADD CONSTRAINT `groupId` FOREIGN KEY (`group_id`)
        REFERENCES `ohrm_i18n_group` (`id`) ON DELETE SET NULL;";

        $sql[] = "ALTER TABLE `ohrm_i18n_translate`
    ADD CONSTRAINT `languageId` FOREIGN KEY (`language_id`)
        REFERENCES `ohrm_i18n_language` (`id`);";

        $sql[] = "ALTER TABLE `ohrm_i18n_translate`
    ADD CONSTRAINT `langStringId` FOREIGN KEY (`lang_string_id`)
        REFERENCES `ohrm_i18n_lang_string` (`id`) ON DELETE CASCADE;";

        $sql[] = "ALTER TABLE `ohrm_i18n_lang_string`
    ADD CONSTRAINT `sourceId` FOREIGN KEY (`source_id`)
        REFERENCES `ohrm_i18n_source` (`id`) ON DELETE CASCADE;";

        $sql[] = "ALTER TABLE `ohrm_i18n_translate`
    ADD CONSTRAINT `translateUniqueId` UNIQUE (`lang_string_id`, `language_id`);";

        $sql[] = "SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin');";

        $sql[] = "SET @admin_module_id = (SELECT `id` FROM `ohrm_module` WHERE `name`='admin');";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
('Language Packages', @admin_module_id, 'languagePackage'),
('Language Customization', @admin_module_id, 'languageCustomization'),
('Save Language Customization', @admin_module_id, 'saveLanguageCustomization'),
('Export Language Package', @admin_module_id, 'exportLanguagePackage');";

        $sql[] = "SET @language_packages_screen_id=(SELECT `id` FROM `ohrm_screen` WHERE `name`='Language Packages');";

        $sql[] = "SET @language_customization_screen_id=(SELECT `id` FROM `ohrm_screen` WHERE `name`='Language Customization');";

        $sql[] = "SET @save_language_customization_screen_id=(SELECT `id` FROM `ohrm_screen` WHERE `name`='Save Language Customization');";

        $sql[] = "SET @export_language_package_screen_id=(SELECT `id` FROM `ohrm_screen` WHERE `name`='Export Language Package');";

        $sql[] = "SET @admin_menu_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Admin' AND `level` = 1);";

        $sql[] = "SET @configuration_menu_id := (SELECT id FROM ohrm_menu_item where menu_title = 'Configuration' AND `parent_id` = @admin_menu_id);";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Language Packages', @language_packages_screen_id, @configuration_menu_id, 3, 350, '', 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
(@admin_role_id, @language_packages_screen_id, 1, 1, 1, 0),
(@admin_role_id, @language_customization_screen_id, 1, 1, 1, 0),
(@admin_role_id, @save_language_customization_screen_id, 1, 1, 1, 0),
(@admin_role_id, @export_language_package_screen_id, 1, 1, 1, 0);";

        $sql[] = "INSERT INTO `ohrm_i18n_language` (`name`, `code`, `added`) VALUES
('Chinese (Simplified, China) - 中文（简体，中国）', 'zh_Hans_CN', 1),
('Chinese (Traditional, Taiwan) - 中文（繁體，台灣）', 'zh_Hant_TW', 1),
('Dutch - Nederlands', 'nl', 1),
('English (United States)', 'en_US', 1),
('French - Français', 'fr', 1),
('German - Deutsch', 'de', 1),
('Spanish - Español', 'es', 1),
('Spanish (Costa Rica) - Español (Costa Rica)', 'es_CR', 1);";

        $sql[] = "INSERT INTO `ohrm_i18n_language` (`name`, `code`, `enabled`) VALUES
('test - TEST', 'zz_ZZ', 0);";

        $sql[] = "INSERT INTO `ohrm_i18n_language` (`name`, `code`) VALUES
('Afrikaans (Namibia) - Afrikaans (Namibië)', 'af_NA'),
('Afrikaans (South Africa) - Afrikaans (Suid-Afrika)', 'af_ZA'),
('Aghem (Cameroon)', 'agq_CM'),
('Akan (Ghana)', 'ak_GH'),
('Albanian (Albania) - Albanian (Albania)', 'sq_AL'),
('Albanian (Macedonia) - Albanian (Macedonia)', 'sq_MK'),
('Amharic (Ethiopia) - Amharic (Ethiopia)', 'am_ET'),
('Arabic (Algeria) - العربية (الجزائر)', 'ar_DZ'),
('Arabic (Bahrain) - العربية (البحرين)', 'ar_BH'),
('Arabic (Chad) - العربية (تشاد)', 'ar_TD'),
('Arabic (Comoros) - العربية (جزر القمر)', 'ar_KM'),
('Arabic (Djibouti) - العربية (جيبوتي)', 'ar_DJ'),
('Arabic (Egypt) - العربية (مصر)', 'ar_EG'),
('Arabic (Eritrea) - العربية (إريتريا)', 'ar_ER'),
('Arabic (Iraq) - العربية (العراق)', 'ar_IQ'),
('Arabic (Israel) - العربية (إسرائيل)', 'ar_IL'),
('Arabic (Jordan) - العربية (الأردن)', 'ar_JO'),
('Arabic (Kuwait) - العربية (الكويت)', 'ar_KW'),
('Arabic (Lebanon) - العربية (لبنان)', 'ar_LB'),
('Arabic (Libya) - العربية (ليبيا)', 'ar_LY'),
('Arabic (Mauritania) - العربية (موريتانيا)', 'ar_MR'),
('Arabic (Morocco) - العربية (المغرب)', 'ar_MA'),
('Arabic (Oman) - العربية (عمان)', 'ar_OM'),
('Arabic (Palestinian Territories) - العربية (الأراضي الفلسطينية)', 'ar_PS'),
('Arabic (Qatar) - العربية (قطر)', 'ar_QA'),
('Arabic (Saudi Arabia) - العربية (المملكة العربية السعودية)', 'ar_SA'),
('Arabic (Somalia) - العربية (الصومال)', 'ar_SO'),
('Arabic (South Sudan) - العربية (جنوب السودان)', 'ar_SS'),
('Arabic (Sudan) - العربية (السودان)', 'ar_SD'),
('Arabic (Syria) - العربية (سوريا)', 'ar_SY'),
('Arabic (Tunisia) - العربية (تونس)', 'ar_TN'),
('Arabic (United Arab Emirates) - العربية (الإمارات العربية المتحدة)', 'ar_AE'),
('Arabic (Western Sahara) - العربية (الصحراء الغربية)', 'ar_EH'),
('Arabic (Yemen) - العربية (اليمن)', 'ar_YE'),
('Armenian (Armenia) - Հայերեն (Հայաստան)', 'hy_AM'),
('Assamese (India)', 'as_IN'),
('Asturian (Spain)', 'ast_ES'),
('Asu (Tanzania)', 'asa_TZ'),
('Azerbaijani (Cyrillic, Azerbaijan) - Azərbaycan (kiril, Azərbaycan)', 'az_Cyrl_AZ'),
('Azerbaijani (Latin, Azerbaijan) - Azərbaycan (Latın, Azərbaycan)', 'az_Latn_AZ'),
('Bafia (Cameroon)', 'ksf_CM'),
('Bambara (Mali)', 'bm_ML'),
('Bangla (Bangladesh) - বাংলা (বাংলাদেশ)', 'bn_BD'),
('Bangla (India) - বাংলা (ভারত)', 'bn_IN'),
('Basaa (Cameroon)', 'bas_CM'),
('Basque (Spain) - Basque (Espainia)', 'eu_ES'),
('Belarusian (Belarus) - Беларуская (Беларусь)', 'be_BY'),
('Bemba (Zambia)', 'bem_ZM'),
('Bena (Tanzania)', 'bez_TZ'),
('Bodo (India)', 'brx_IN'),
('Bosnian (Cyrillic, Bosnia & Herzegovina) - Bosanski (ćirilica, Bosna i Hercegovina)', 'bs_Cyrl_BA'),
('Bosnian (Latin, Bosnia & Herzegovina) - Bosanski (latinica, Bosna i Hercegovina)', 'bs_Latn_BA'),
('Breton (France)', 'br_FR'),
('Bulgarian (Bulgaria) - Български (България)', 'bg_BG'),
('Burmese (Myanmar [Burma]) - မြန်မာ (မြန်မာ [မြန်မာနိုင်ငံ])', 'my_MM'),
('Cantonese (Simplified, China) - 广东话（简体中文）', 'yue_Hans_CN'),
('Cantonese (Traditional, Hong Kong SAR China) - 粵語（繁體中文，中國香港特別行政區）', 'yue_Hant_HK'),
('Catalan (Andorra) - Català (Andorra)', 'ca_AD'),
('Catalan (France) - Català (França)', 'ca_FR'),
('Catalan (Italy) - Català (Itàlia)', 'ca_IT'),
('Catalan (Spain) - Català (Espanya)', 'ca_ES'),
('Central Atlas Tamazight (Morocco)', 'tzm_MA'),
('Central Kurdish (Iran)', 'ckb_IR'),
('Central Kurdish (Iraq)', 'ckb_IQ'),
('Chakma (Bangladesh)', 'ccp_BD'),
('Chakma (India)', 'ccp_IN'),
('Chechen (Russia)', 'ce_RU'),
('Cherokee (United States)', 'chr_US'),
('Chiga (Uganda)', 'cgg_UG'),
('Chinese (Simplified, Hong Kong SAR China) - 中文（简体，中国香港特别行政区）', 'zh_Hans_HK'),
('Chinese (Simplified, Macau SAR China) - 中文（简体，中国澳门特别行政区）', 'zh_Hans_MO'),
('Chinese (Simplified, Singapore) - 中文（简体，新加坡）', 'zh_Hans_SG'),
('Chinese (Traditional, Hong Kong SAR China) - 中文（繁體字，中國香港特別行政區）', 'zh_Hant_HK'),
('Chinese (Traditional, Macau SAR China) - 中文（繁體字，中國澳門特別行政區）', 'zh_Hant_MO'),
('Colognian (Germany)', 'ksh_DE'),
('Cornish (United Kingdom)', 'kw_GB'),
('Croatian (Bosnia & Herzegovina) - Hrvatska (Bosna i Hercegovina)', 'hr_BA'),
('Croatian (Croatia) - Hrvatski (Hrvatska)', 'hr_HR'),
('Czech (Czechia) - Český (Česko)', 'cs_CZ'),
('Danish (Denmark) - Dansk (Danmark)', 'da_DK'),
('Danish (Greenland) - Dansk (Grønland)', 'da_GL'),
('Duala (Cameroon)', 'dua_CM'),
('Dutch (Aruba) - Nederlands (Aruba)', 'nl_AW'),
('Dutch (Belgium) - Nederlands (België)', 'nl_BE'),
('Dutch (Caribbean Netherlands) - Nederlands (Caribisch Nederland)', 'nl_BQ'),
('Dutch (Curaçao) - Nederlands (Curaçao)', 'nl_CW'),
('Dutch (Netherlands) - Nederlands (Nederland)', 'nl_NL'),
('Dutch (Sint Maarten) - Nederlands (Sint Maarten)', 'nl_SX'),
('Dutch (Suriname) - Nederlands (Suriname)', 'nl_SR'),
('Dzongkha (Bhutan)', 'dz_BT'),
('Embu (Kenya)', 'ebu_KE'),
('English (American Samoa)', 'en_AS'),
('English (Anguilla)', 'en_AI'),
('English (Antigua & Barbuda)', 'en_AG'),
('English (Australia)', 'en_AU'),
('English (Austria)', 'en_AT'),
('English (Bahamas)', 'en_BS'),
('English (Barbados)', 'en_BB'),
('English (Belgium)', 'en_BE'),
('English (Belize)', 'en_BZ'),
('English (Bermuda)', 'en_BM'),
('English (Botswana)', 'en_BW'),
('English (British Indian Ocean Territory)', 'en_IO'),
('English (British Virgin Islands)', 'en_VG'),
('English (Burundi)', 'en_BI'),
('English (Cameroon)', 'en_CM'),
('English (Canada)', 'en_CA'),
('English (Cayman Islands)', 'en_KY'),
('English (Christmas Island)', 'en_CX'),
('English (Cocos [Keeling] Islands)', 'en_CC'),
('English (Cook Islands)', 'en_CK'),
('English (Cyprus)', 'en_CY'),
('English (Denmark)', 'en_DK'),
('English (Dominica)', 'en_DM'),
('English (Eritrea)', 'en_ER'),
('English (Falkland Islands)', 'en_FK'),
('English (Fiji)', 'en_FJ'),
('English (Finland)', 'en_FI'),
('English (Gambia)', 'en_GM'),
('English (Germany)', 'en_DE'),
('English (Ghana)', 'en_GH'),
('English (Gibraltar)', 'en_GI'),
('English (Grenada)', 'en_GD'),
('English (Guam)', 'en_GU'),
('English (Guernsey)', 'en_GG'),
('English (Guyana)', 'en_GY'),
('English (Hong Kong SAR China)', 'en_HK'),
('English (India)', 'en_IN'),
('English (Ireland)', 'en_IE'),
('English (Isle of Man)', 'en_IM'),
('English (Israel)', 'en_IL'),
('English (Jamaica)', 'en_JM'),
('English (Jersey)', 'en_JE'),
('English (Kenya)', 'en_KE'),
('English (Kiribati)', 'en_KI'),
('English (Lesotho)', 'en_LS'),
('English (Liberia)', 'en_LR'),
('English (Macau SAR China)', 'en_MO'),
('English (Madagascar)', 'en_MG'),
('English (Malawi)', 'en_MW'),
('English (Malaysia)', 'en_MY'),
('English (Malta)', 'en_MT'),
('English (Marshall Islands)', 'en_MH'),
('English (Mauritius)', 'en_MU'),
('English (Micronesia)', 'en_FM'),
('English (Montserrat)', 'en_MS'),
('English (Namibia)', 'en_NA'),
('English (Nauru)', 'en_NR'),
('English (Netherlands)', 'en_NL'),
('English (New Zealand)', 'en_NZ'),
('English (Nigeria)', 'en_NG'),
('English (Niue)', 'en_NU'),
('English (Norfolk Island)', 'en_NF'),
('English (Northern Mariana Islands)', 'en_MP'),
('English (Pakistan)', 'en_PK'),
('English (Palau)', 'en_PW'),
('English (Papua New Guinea)', 'en_PG'),
('English (Philippines)', 'en_PH'),
('English (Pitcairn Islands)', 'en_PN'),
('English (Puerto Rico)', 'en_PR'),
('English (Rwanda)', 'en_RW'),
('English (Samoa)', 'en_WS'),
('English (Seychelles)', 'en_SC'),
('English (Sierra Leone)', 'en_SL'),
('English (Singapore)', 'en_SG'),
('English (Sint Maarten)', 'en_SX'),
('English (Slovenia)', 'en_SI'),
('English (Solomon Islands)', 'en_SB'),
('English (South Africa)', 'en_ZA'),
('English (South Sudan)', 'en_SS'),
('English (St. Helena)', 'en_SH'),
('English (St. Kitts & Nevis)', 'en_KN'),
('English (St. Lucia)', 'en_LC'),
('English (St. Vincent & Grenadines)', 'en_VC'),
('English (Sudan)', 'en_SD'),
('English (Swaziland)', 'en_SZ'),
('English (Sweden)', 'en_SE'),
('English (Switzerland)', 'en_CH'),
('English (Tanzania)', 'en_TZ'),
('English (Tokelau)', 'en_TK'),
('English (Tonga)', 'en_TO'),
('English (Trinidad & Tobago)', 'en_TT'),
('English (Turks & Caicos Islands)', 'en_TC'),
('English (Tuvalu)', 'en_TV'),
('English (U.S. Outlying Islands)', 'en_UM'),
('English (U.S. Virgin Islands)', 'en_VI'),
('English (Uganda)', 'en_UG'),
('English (United Kingdom)', 'en_GB'),
('English (United States, Computer)', 'en_US_POSIX'),
('English (Vanuatu)', 'en_VU'),
('English (Zambia)', 'en_ZM'),
('English (Zimbabwe)', 'en_ZW'),
('Estonian (Estonia) - Eesti (Eesti)', 'et_EE'),
('Ewe (Ghana)', 'ee_GH'),
('Ewe (Togo)', 'ee_TG'),
('Ewondo (Cameroon)', 'ewo_CM'),
('Faroese (Denmark)', 'fo_DK'),
('Faroese (Faroe Islands)', 'fo_FO'),
('Filipino (Philippines) - Filipino (Pilipinas)', 'fil_PH'),
('Finnish (Finland) - Suomi (Suomi)', 'fi_FI'),
('French (Algeria) - Français (Algérie)', 'fr_DZ'),
('French (Belgium) - Français (Belgique)', 'fr_BE'),
('French (Benin) - Français (Bénin)', 'fr_BJ'),
('French (Burkina Faso) - Français (Burkina Faso)', 'fr_BF'),
('French (Burundi) - Français (Burundi)', 'fr_BI'),
('French (Cameroon) - Français (Cameroun)', 'fr_CM'),
('French (Canada) - Français (Canada)', 'fr_CA'),
('French (Central African Republic) - Français (République centrafricaine)', 'fr_CF'),
('French (Chad) - Français (Tchad)', 'fr_TD'),
('French (Comoros) - Français (Comores)', 'fr_KM'),
('French (Congo - Brazzaville) (fr-CG) - Français (Congo-Brazzaville) (fr-CG)', 'fr_CG'),
('French (Congo - Kinshasa) - Français (Congo-Kinshasa)', 'fr_CD'),
(\"French (Côte d’Ivoire) - Français (Côte d'Ivoire)\", 'fr_CI'),
('French (Djibouti) - Français (Djibouti)', 'fr_DJ'),
('French (Equatorial Guinea) - Français (Guinée équatoriale)', 'fr_GQ'),
('French (France) - France francaise)', 'fr_FR'),
('French (French Guiana) - Français (Guyane française)', 'fr_GF'),
('French (French Polynesia) - Français (Polynésie française)', 'fr_PF'),
('French (Gabon) - Français (Gabon)', 'fr_GA'),
('French (Guadeloupe) - Français (Guadeloupe)', 'fr_GP'),
('French (Guinea) - Français (Guinée)', 'fr_GN'),
('French (Haiti) - Français (Haïti)', 'fr_HT'),
('French (Luxembourg) - Français (Luxembourg)', 'fr_LU'),
('French (Madagascar) - Français (Madagascar)', 'fr_MG'),
('French (Mali) - Français (Mali)', 'fr_ML'),
('French (Martinique) - Français (Martinique)', 'fr_MQ'),
('French (Mauritania) - Français (Mauritanie)', 'fr_MR'),
('French (Mauritius) - Français (Maurice)', 'fr_MU'),
('French (Mayotte) - Français (Mayotte)', 'fr_YT'),
('French (Monaco) - Français (Monaco)', 'fr_MC'),
('French (Morocco) - Français (Maroc)', 'fr_MA'),
('French (New Caledonia) - Français (Nouvelle-Calédonie)', 'fr_NC'),
('French (Niger) - Français (Niger)', 'fr_NE'),
('French (Réunion) - Français (Réunion)', 'fr_RE'),
('French (Rwanda) - Français (Rwanda)', 'fr_RW'),
('French (Senegal) - Français (Sénégal)', 'fr_SN'),
('French (Seychelles) - Français (Seychelles)', 'fr_SC'),
('French (St. Barthélemy) - Français (Saint-Barthélemy)', 'fr_BL'),
('French (St. Martin) - Français (Saint-Martin)', 'fr_MF'),
('French (St. Pierre & Miquelon) - Français (Saint-Pierre-et-Miquelon)', 'fr_PM'),
('French (Switzerland) - Français (Suisse)', 'fr_CH'),
('French (Syria) - Français (Syrie)', 'fr_SY'),
('French (Togo) - Français (Togo)', 'fr_TG'),
('French (Tunisia) - Français (Tunisie)', 'fr_TN'),
('French (Vanuatu) - Français (Vanuatu)', 'fr_VU'),
('French (Wallis & Futuna) - Français (Wallis et Futuna)', 'fr_WF'),
('Friulian (Italy)', 'fur_IT'),
('Fulah (Cameroon)', 'ff_CM'),
('Fulah (Guinea)', 'ff_GN'),
('Fulah (Mauritania)', 'ff_MR'),
('Fulah (Senegal)', 'ff_SN'),
('Galician (Spain) - Galicia (España)', 'gl_ES'),
('Ganda (Uganda)', 'lg_UG'),
('Georgian (Georgia) - Georgian (Georgia)', 'ka_GE'),
('German (Austria) - Deutsch (Österreich)', 'de_AT'),
('German (Belgium) - Deutsch (Belgien)', 'de_BE'),
('German (Germany) - Deutsches Deutschland)', 'de_DE'),
('German (Italy) - Deutsch (Italien)', 'de_IT'),
('German (Liechtenstein) - Deutsch (Liechtenstein)', 'de_LI'),
('German (Luxembourg) - Deutsch (Luxemburg)', 'de_LU'),
('German (Switzerland) - Deutsch (Schweiz)', 'de_CH'),
('Greek (Cyprus) - Ελληνικά (Κύπρος)', 'el_CY'),
('Greek (Greece) - Ελληνικά (Ελλάδα)', 'el_GR'),
('Gujarati (India) - ગુજરાતી (ભારત)', 'gu_IN'),
('Gusii (Kenya)', 'guz_KE'),
('Hausa (Ghana) - Hausa (Ghana)', 'ha_GH'),
('Hausa (Niger) - Hausa (Nijar)', 'ha_NE'),
('Hausa (Nigeria) - Hausa (Nigeria)', 'ha_NG'),
('Hawaiian (United States) - Hawaiian (United States)', 'haw_US'),
('Hebrew (Israel) - עברית (ישראל)', 'he_IL'),
('Hindi (India) - हिंदी भारत)', 'hi_IN'),
('Hungarian (Hungary) - Magyar (Magyarország)', 'hu_HU'),
('Icelandic (Iceland) - Icelandic (Iceland)', 'is_IS'),
('Igbo (Nigeria) - Igbo (Nigeria)', 'ig_NG'),
('Inari Sami (Finland)', 'smn_FI'),
('Indonesian (Indonesia) - Indonesia (Indonesia)', 'id_ID'),
('Irish (Ireland) - Gaeilge (Éire)', 'ga_IE'),
('Italian (Italy) - Italiano (Italia)', 'it_IT'),
('Italian (San Marino) - Italiano (San Marino)', 'it_SM'),
('Italian (Switzerland) - Italiano (Svizzera)', 'it_CH'),
('Italian (Vatican City) - Italiano (Città del Vaticano)', 'it_VA'),
('Japanese (Japan) - 日本語（日本）', 'ja_JP'),
('Jola-Fonyi (Senegal)', 'dyo_SN'),
('Kabuverdianu (Cape Verde)', 'kea_CV'),
('Kabyle (Algeria)', 'kab_DZ'),
('Kako (Cameroon)', 'kkj_CM'),
('Kalaallisut (Greenland)', 'kl_GL'),
('Kalenjin (Kenya)', 'kln_KE'),
('Kamba (Kenya)', 'kam_KE'),
('Kannada (India) - ಕನ್ನಡ (ಭಾರತ)', 'kn_IN'),
('Kashmiri (India)', 'ks_IN'),
('Kazakh (Kazakhstan) - Қазақ (Қазақстан)', 'kk_KZ'),
('Khmer (Cambodia) - ខ្មែរ (កម្ពុជា)', 'km_KH'),
('Kikuyu (Kenya)', 'ki_KE'),
('Kinyarwanda (Rwanda) - Kinyarwanda (Rwanda)', 'rw_RW'),
('Konkani (India)', 'kok_IN'),
('Korean (North Korea) - 한국어 (북한)', 'ko_KP'),
('Korean (South Korea) - 한국어 (한국)', 'ko_KR'),
('Koyra Chiini (Mali)', 'khq_ML'),
('Koyraboro Senni (Mali)', 'ses_ML'),
('Kwasio (Cameroon)', 'nmg_CM'),
('Kyrgyz (Kyrgyzstan) - Kyrgyz (Kyrgyzstan)', 'ky_KG'),
('Lakota (United States)', 'lkt_US'),
('Langi (Tanzania)', 'lag_TZ'),
('Lao (Laos) - ລາວ (ລາວ)', 'lo_LA'),
('Latvian (Latvia) - Latviešu (Latvija)', 'lv_LV'),
('Lingala (Angola)', 'ln_AO'),
('Lingala (Central African Republic)', 'ln_CF'),
('Lingala (Congo - Brazzaville)', 'ln_CG'),
('Lingala (Congo - Kinshasa)', 'ln_CD'),
('Lithuanian (Lithuania) - Lietuvių (Lietuva)', 'lt_LT'),
('Low German (Germany)', 'nds_DE'),
('Low German (Netherlands)', 'nds_NL'),
('Lower Sorbian (Germany)', 'dsb_DE'),
('Luba-Katanga (Congo - Kinshasa)', 'lu_CD'),
('Luo (Kenya)', 'luo_KE'),
('Luxembourgish (Luxembourg) - Lëtzebuergesch (Lëtzebuerg)', 'lb_LU'),
('Luyia (Kenya)', 'luy_KE'),
('Macedonian (Macedonia) - Македонски (Macedonia)', 'mk_MK'),
('Machame (Tanzania)', 'jmc_TZ'),
('Makhuwa-Meetto (Mozambique)', 'mgh_MZ'),
('Makonde (Tanzania)', 'kde_TZ'),
('Malagasy (Madagascar) - Malagasy (Madagascar)', 'mg_MG'),
('Malay (Brunei) - Melayu (Brunei)', 'ms_BN'),
('Malay (Malaysia) - Melayu (Malaysia)', 'ms_MY'),
('Malay (Singapore) - Melayu (Singapura)', 'ms_SG'),
('Malayalam (India) - മലയാളം (ഇന്ത്യ)', 'ml_IN'),
('Maltese (Malta) - Malti (Malta)', 'mt_MT'),
('Manx (Isle of Man)', 'gv_IM'),
('Marathi (India) - मराठी (भारत)', 'mr_IN'),
('Masai (Kenya)', 'mas_KE'),
('Masai (Tanzania)', 'mas_TZ'),
('Mazanderani (Iran)', 'mzn_IR'),
('Meru (Kenya)', 'mer_KE'),
('Metaʼ (Cameroon)', 'mgo_CM'),
('Mongolian (Mongolia) - Монгол улсын (Монгол)', 'mn_MN'),
('Morisyen (Mauritius)', 'mfe_MU'),
('Mundang (Cameroon)', 'mua_CM'),
('Nama (Namibia)', 'naq_NA'),
('Nepali (India) - नेपाली (भारत)', 'ne_IN'),
('Nepali (Nepal) - नेपाली (नेपाल)', 'ne_NP'),
('Ngiemboon (Cameroon)', 'nnh_CM'),
('Ngomba (Cameroon)', 'jgo_CM'),
('North Ndebele (Zimbabwe)', 'nd_ZW'),
('Northern Luri (Iran)', 'lrc_IR'),
('Northern Luri (Iraq)', 'lrc_IQ'),
('Northern Sami (Finland)', 'se_FI'),
('Northern Sami (Norway)', 'se_NO'),
('Northern Sami (Sweden)', 'se_SE'),
('Norwegian Bokmål (Norway) - Norsk bokmål (Norge)', 'nb_NO'),
('Norwegian Bokmål (Svalbard & Jan Mayen) - Norsk bokmål (Svalbard og Jan Mayen)', 'nb_SJ'),
('Norwegian Nynorsk (Norway)', 'nn_NO'),
('Nuer (South Sudan)', 'nus_SS'),
('Nyankole (Uganda)', 'nyn_UG'),
('Odia (India) - ଓଡ଼ିଆ (ଭାରତ)', 'or_IN'),
('Oromo (Ethiopia)', 'om_ET'),
('Oromo (Kenya)', 'om_KE'),
('Ossetic (Georgia)', 'os_GE'),
('Ossetic (Russia)', 'os_RU'),
('Pashto (Afghanistan) - پښتو (افغانستان)', 'ps_AF'),
('Persian (Afghanistan) - فارسی (افغانستان)', 'fa_AF'),
('Persian (Iran) - فارسی (ایران)', 'fa_IR'),
('Polish (Poland) - Polski (Polska)', 'pl_PL'),
('Portuguese (Angola) - Português (Angola)', 'pt_AO'),
('Portuguese (Brazil) - Português (Brasil)', 'pt_BR'),
('Portuguese (Cape Verde) - Português (Cabo Verde)', 'pt_CV'),
('Portuguese (Equatorial Guinea) - Português (Guiné Equatorial)', 'pt_GQ'),
('Portuguese (Guinea-Bissau) - Português (Guiné-Bissau)', 'pt_GW'),
('Portuguese (Luxembourg) - Português (Luxemburgo)', 'pt_LU'),
('Portuguese (Macau SAR China) - Português (Macau SAR China)', 'pt_MO'),
('Portuguese (Mozambique) - Português (Moçambique)', 'pt_MZ'),
('Portuguese (Portugal) - Português (Portugal)', 'pt_PT'),
('Portuguese (São Tomé & Príncipe) - Português (São Tomé e Príncipe)', 'pt_ST'),
('Portuguese (Switzerland) - Português (Suíça)', 'pt_CH'),
('Portuguese (Timor-Leste) - Português (Timor-Leste)', 'pt_TL'),
('Punjabi (Arabic, Pakistan) - ਪੰਜਾਬੀ (ਅਰਬੀ, ਪਾਕਿਸਤਾਨ)', 'pa_Arab_PK'),
('Punjabi (Gurmukhi, India) - ਪੰਜਾਬੀ (ਗੁਰਮੁਖੀ, ਭਾਰਤ)', 'pa_Guru_IN'),
('Quechua (Bolivia)', 'qu_BO'),
('Quechua (Ecuador)', 'qu_EC'),
('Quechua (Peru)', 'qu_PE'),
('Romanian (Moldova) - Română (Moldova)', 'ro_MD'),
('Romanian (Romania) - Română (România)', 'ro_RO'),
('Romansh (Switzerland)', 'rm_CH'),
('Rombo (Tanzania)', 'rof_TZ'),
('Rundi (Burundi)', 'rn_BI'),
('Russian (Belarus) - России (Беларусь)', 'ru_BY'),
('Russian (Kazakhstan) - России (Казахстан)', 'ru_KZ'),
('Russian (Kyrgyzstan) - России (Кыргызстан)', 'ru_KG'),
('Russian (Moldova) - России (Молдова)', 'ru_MD'),
('Russian (Russia) - России (Россия)', 'ru_RU'),
('Russian (Ukraine) - России (Украина)', 'ru_UA'),
('Rwa (Tanzania)', 'rwk_TZ'),
('Sakha (Russia)', 'sah_RU'),
('Samburu (Kenya)', 'saq_KE'),
('Sango (Central African Republic)', 'sg_CF'),
('Sangu (Tanzania)', 'sbp_TZ'),
('Scottish Gaelic (United Kingdom) - Gàidhlig na h-Alba (An Rìoghachd Aonaichte)', 'gd_GB'),
('Sena (Mozambique)', 'seh_MZ'),
('Serbian (Cyrillic, Bosnia & Herzegovina) - Српски језик (Ћирилица, Босна и Херцеговина)', 'sr_Cyrl_BA'),
('Serbian (Cyrillic, Montenegro) - Српски језик (Ћирилица, Црна Гора)', 'sr_Cyrl_ME'),
('Serbian (Cyrillic, Serbia) - Српски језик (Ћирилица, Србија)', 'sr_Cyrl_RS'),
('Serbian (Latin, Bosnia & Herzegovina) - Српски (Ћирилица, Босна и Херцеговина)', 'sr_Latn_BA'),
('Serbian (Latin, Montenegro) - Српски (латински, Црна Гора)', 'sr_Latn_ME'),
('Serbian (Latin, Serbia) - Српски (латински, Србија)', 'sr_Latn_RS'),
('Shambala (Tanzania)', 'ksb_TZ'),
('Shona (Zimbabwe) - Shona (Zimbabwe)', 'sn_ZW'),
('Sichuan Yi (China)', 'ii_CN'),
('Sinhala (Sri Lanka) - සිංහල (ශ්‍රී ලංකාව)', 'si_LK'),
('Slovak (Slovakia) - Slovenskú (Slovensko)', 'sk_SK'),
('Slovenian (Slovenia) - Slovensko (Slovenija)', 'sl_SI'),
('Soga (Uganda)', 'xog_UG'),
('Somali (Djibouti) - Soomaali (Jabuuti)', 'so_DJ'),
('Somali (Ethiopia) - Soomaali (Itoobiya)', 'so_ET'),
('Somali (Kenya) - Soomaali (Kenya)', 'so_KE'),
('Somali (Somalia) - Soomaali (Soomaaliya)', 'so_SO'),
('Spanish (Argentina) - Español (Argentina)', 'es_AR'),
('Spanish (Belize) - Español (Belice)', 'es_BZ'),
('Spanish (Bolivia) - Español (Bolivia)', 'es_BO'),
('Spanish (Brazil) - Español (Brasil)', 'es_BR'),
('Spanish (Chile) - Español (Chile)', 'es_CL'),
('Spanish (Colombia) - Español (Colombia)', 'es_CO'),
('Spanish (Cuba) - Español (Cuba)', 'es_CU'),
('Spanish (Dominican Republic) - Española (República Dominicana)', 'es_DO'),
('Spanish (Ecuador) - Español (Ecuador)', 'es_EC'),
('Spanish (El Salvador) - Español (El Salvador)', 'es_SV'),
('Spanish (Equatorial Guinea) - Español (Guinea Ecuatorial)', 'es_GQ'),
('Spanish (Guatemala) - Español (Guatemala)', 'es_GT'),
('Spanish (Honduras) - Español (Honduras)', 'es_HN'),
('Spanish (Mexico) - Español (México)', 'es_MX'),
('Spanish (Nicaragua) - Español (Nicaragua)', 'es_NI'),
('Spanish (Panama) - Español (Panamá)', 'es_PA'),
('Spanish (Paraguay) - Español (Paraguay)', 'es_PY'),
('Spanish (Peru) - Español (Perú)', 'es_PE'),
('Spanish (Philippines) - Español (Filipinas)', 'es_PH'),
('Spanish (Puerto Rico) - Español (Puerto Rico)', 'es_PR'),
('Spanish (Spain) - Español (España)', 'es_ES'),
('Spanish (United States) - Español (Estados Unidos)', 'es_US'),
('Spanish (Uruguay) - Español (Uruguay)', 'es_UY'),
('Spanish (Venezuela) - Español (Venezuela)', 'es_VE'),
('Standard Moroccan Tamazight (Morocco)', 'zgh_MA'),
('Swahili (Congo - Kinshasa) - Kiswahili (Congo - Kinshasa)', 'sw_CD'),
('Swahili (Kenya) - Kiswahili (Kenya)', 'sw_KE'),
('Swahili (Tanzania) - Kiswahili (Tanzania)', 'sw_TZ'),
('Swahili (Uganda) - Kiswahili (Uganda)', 'sw_UG'),
('Swedish (Åland Islands) - Svenska (Åland)', 'sv_AX'),
('Swedish (Finland) - Svenska (Finland)', 'sv_FI'),
('Swedish (Sweden) - Svenska (Sverige)', 'sv_SE'),
('Swiss German (France)', 'gsw_FR'),
('Swiss German (Liechtenstein)', 'gsw_LI'),
('Swiss German (Switzerland)', 'gsw_CH'),
('Tachelhit (Latin, Morocco)', 'shi_Latn_MA'),
('Tachelhit (Tifinagh, Morocco)', 'shi_Tfng_MA'),
('Taita (Kenya)', 'dav_KE'),
('Tajik (Tajikistan) - Тоҷикистон (Тоҷикистон)', 'tg_TJ'),
('Tamil (India) - தமிழ் (இந்தியா)', 'ta_IN'),
('Tamil (Malaysia) - தமிழ் (மலேஷியா)', 'ta_MY'),
('Tamil (Singapore) - தமிழ் (சிங்கப்பூர்)', 'ta_SG'),
('Tamil (Sri Lanka) - தமிழ் (இலங்கை)', 'ta_LK'),
('Tasawaq (Niger)', 'twq_NE'),
('Tatar (Russia) - Татар (Россия)', 'tt_RU'),
('Telugu (India) - తెలుగు (భారతదేశం)', 'te_IN'),
('Teso (Kenya)', 'teo_KE'),
('Teso (Uganda)', 'teo_UG'),
('Thai (Thailand) - ไทย (ไทยแลนด์)', 'th_TH'),
('Tibetan (China)', 'bo_CN'),
('Tibetan (India)', 'bo_IN'),
('Tigrinya (Eritrea)', 'ti_ER'),
('Tigrinya (Ethiopia)', 'ti_ET'),
('Tongan (Tonga)', 'to_TO'),
('Turkish (Cyprus) - Türk (Kıbrıs)', 'tr_CY'),
('Turkish (Turkey) - Türk (Türkiye)', 'tr_TR'),
('Ukrainian (Ukraine) - Український (Україна)', 'uk_UA'),
('Upper Sorbian (Germany)', 'hsb_DE'),
('Urdu (India) - اردو (بھارت)', 'ur_IN'),
('Urdu (Pakistan) - اردو (پاکستان)', 'ur_PK'),
('Uyghur (China) - ئۇيغۇر (جۇڭگو)', 'ug_CN'),
(\"Uzbek (Arabic, Afghanistan) - O'zbekiston (arab, Afg'oniston)\", 'uz_Arab_AF'),
(\"Uzbek (Cyrillic, Uzbekistan) - O'zbek (kirill, O'zbekiston)\", 'uz_Cyrl_UZ'),
(\"Uzbek (Latin, Uzbekistan) - O'zbek (Lotin, O'zbekiston)\", 'uz_Latn_UZ'),
('Vai (Latin, Liberia)', 'vai_Latn_LR'),
('Vai (Vai, Liberia)', 'vai_Vaii_LR'),
('Vietnamese (Vietnam) - Việt (Việt Nam)', 'vi_VN'),
('Vunjo (Tanzania)', 'vun_TZ'),
('Walser (Switzerland)', 'wae_CH'),
('Welsh (United Kingdom) - Cymraeg (Welsh)', 'cy_GB'),
('Western Frisian (Netherlands) - Western Frysk (Nederland)', 'fy_NL'),
('Wolof (Senegal)', 'wo_SN'),
('Yangben (Cameroon)', 'yav_CM'),
('Yoruba (Benin) - Yorùbá (Benin)', 'yo_BJ'),
('Yoruba (Nigeria) - Yorùbá (Nigeria)', 'yo_NG'),
('Zarma (Niger)', 'dje_NE'),
('Zulu (South Africa) - Zulu (South Africa)', 'zu_ZA');";

        $sql[] = "INSERT INTO `ohrm_i18n_group` (`name`, `title`) VALUES
('general', 'General'),
('admin', 'Admin'),
('pim', 'PIM'),
('leave', 'Leave'),
('time', 'Time'),
('recruitment', 'Recruitment'),
('performance', 'Performance'),
('dashboard', 'Dashboard'),
('directory', 'Directory'),
('maintenance', 'Maintenance'),
('buzz', 'Buzz'),
('marketplace', 'Marketplace'),
('mobile', 'Mobile');";

        $sql[] = "
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('instance.version','4.6');";

        $sql[] = "
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('instance.increment_number','" . $this->incrementNumber . "');";

        $this->sql = $sql;
    }

    public function getUserInputWidgets()
    {
    }

    public function setUserInputs()
    {
    }

    public function getNotes()
    {
    }

    public function execute()
    {
        $this->incrementNumber = 77;
        parent::execute();
        $result = [];
        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
}
