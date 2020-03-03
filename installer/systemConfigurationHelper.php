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
 *
 */

require realpath(__DIR__ . "/../symfony/lib/vendor/autoload.php");

class systemConfigurationHelper
{
    /**
     * @var array|null
     */
    private $languages = null;

    public function __construct()
    {
        $file = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'..'.
                DIRECTORY_SEPARATOR. 'symfony'.DIRECTORY_SEPARATOR.'plugins'.
                DIRECTORY_SEPARATOR.'orangehrmAdminPlugin'.DIRECTORY_SEPARATOR.'config'.
                DIRECTORY_SEPARATOR.'supported_languages.yml';
        $this->languages = sfYaml::load(file_get_contents($file));
    }


    private $hs_hr_country = array(
        array('cou_code' => 'AF','name' => 'AFGHANISTAN','cou_name' => 'Afghanistan','iso3' => 'AFG','numcode' => '4'),
        array('cou_code' => 'AL','name' => 'ALBANIA','cou_name' => 'Albania','iso3' => 'ALB','numcode' => '8'),
        array('cou_code' => 'DZ','name' => 'ALGERIA','cou_name' => 'Algeria','iso3' => 'DZA','numcode' => '12'),
        array('cou_code' => 'AS','name' => 'AMERICAN SAMOA','cou_name' => 'American Samoa','iso3' => 'ASM','numcode' => '16'),
        array('cou_code' => 'AD','name' => 'ANDORRA','cou_name' => 'Andorra','iso3' => 'AND','numcode' => '20'),
        array('cou_code' => 'AO','name' => 'ANGOLA','cou_name' => 'Angola','iso3' => 'AGO','numcode' => '24'),
        array('cou_code' => 'AI','name' => 'ANGUILLA','cou_name' => 'Anguilla','iso3' => 'AIA','numcode' => '660'),
        array('cou_code' => 'AQ','name' => 'ANTARCTICA','cou_name' => 'Antarctica','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'AG','name' => 'ANTIGUA AND BARBUDA','cou_name' => 'Antigua and Barbuda','iso3' => 'ATG','numcode' => '28'),
        array('cou_code' => 'AR','name' => 'ARGENTINA','cou_name' => 'Argentina','iso3' => 'ARG','numcode' => '32'),
        array('cou_code' => 'AM','name' => 'ARMENIA','cou_name' => 'Armenia','iso3' => 'ARM','numcode' => '51'),
        array('cou_code' => 'AW','name' => 'ARUBA','cou_name' => 'Aruba','iso3' => 'ABW','numcode' => '533'),
        array('cou_code' => 'AU','name' => 'AUSTRALIA','cou_name' => 'Australia','iso3' => 'AUS','numcode' => '36'),
        array('cou_code' => 'AT','name' => 'AUSTRIA','cou_name' => 'Austria','iso3' => 'AUT','numcode' => '40'),
        array('cou_code' => 'AZ','name' => 'AZERBAIJAN','cou_name' => 'Azerbaijan','iso3' => 'AZE','numcode' => '31'),
        array('cou_code' => 'BS','name' => 'BAHAMAS','cou_name' => 'Bahamas','iso3' => 'BHS','numcode' => '44'),
        array('cou_code' => 'BH','name' => 'BAHRAIN','cou_name' => 'Bahrain','iso3' => 'BHR','numcode' => '48'),
        array('cou_code' => 'BD','name' => 'BANGLADESH','cou_name' => 'Bangladesh','iso3' => 'BGD','numcode' => '50'),
        array('cou_code' => 'BB','name' => 'BARBADOS','cou_name' => 'Barbados','iso3' => 'BRB','numcode' => '52'),
        array('cou_code' => 'BY','name' => 'BELARUS','cou_name' => 'Belarus','iso3' => 'BLR','numcode' => '112'),
        array('cou_code' => 'BE','name' => 'BELGIUM','cou_name' => 'Belgium','iso3' => 'BEL','numcode' => '56'),
        array('cou_code' => 'BZ','name' => 'BELIZE','cou_name' => 'Belize','iso3' => 'BLZ','numcode' => '84'),
        array('cou_code' => 'BJ','name' => 'BENIN','cou_name' => 'Benin','iso3' => 'BEN','numcode' => '204'),
        array('cou_code' => 'BM','name' => 'BERMUDA','cou_name' => 'Bermuda','iso3' => 'BMU','numcode' => '60'),
        array('cou_code' => 'BT','name' => 'BHUTAN','cou_name' => 'Bhutan','iso3' => 'BTN','numcode' => '64'),
        array('cou_code' => 'BO','name' => 'BOLIVIA','cou_name' => 'Bolivia','iso3' => 'BOL','numcode' => '68'),
        array('cou_code' => 'BA','name' => 'BOSNIA AND HERZEGOVINA','cou_name' => 'Bosnia and Herzegovina','iso3' => 'BIH','numcode' => '70'),
        array('cou_code' => 'BW','name' => 'BOTSWANA','cou_name' => 'Botswana','iso3' => 'BWA','numcode' => '72'),
        array('cou_code' => 'BV','name' => 'BOUVET ISLAND','cou_name' => 'Bouvet Island','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'BR','name' => 'BRAZIL','cou_name' => 'Brazil','iso3' => 'BRA','numcode' => '76'),
        array('cou_code' => 'IO','name' => 'BRITISH INDIAN OCEAN TERRITORY','cou_name' => 'British Indian Ocean Territory','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'BN','name' => 'BRUNEI DARUSSALAM','cou_name' => 'Brunei Darussalam','iso3' => 'BRN','numcode' => '96'),
        array('cou_code' => 'BG','name' => 'BULGARIA','cou_name' => 'Bulgaria','iso3' => 'BGR','numcode' => '100'),
        array('cou_code' => 'BF','name' => 'BURKINA FASO','cou_name' => 'Burkina Faso','iso3' => 'BFA','numcode' => '854'),
        array('cou_code' => 'BI','name' => 'BURUNDI','cou_name' => 'Burundi','iso3' => 'BDI','numcode' => '108'),
        array('cou_code' => 'KH','name' => 'CAMBODIA','cou_name' => 'Cambodia','iso3' => 'KHM','numcode' => '116'),
        array('cou_code' => 'CM','name' => 'CAMEROON','cou_name' => 'Cameroon','iso3' => 'CMR','numcode' => '120'),
        array('cou_code' => 'CA','name' => 'CANADA','cou_name' => 'Canada','iso3' => 'CAN','numcode' => '124'),
        array('cou_code' => 'CV','name' => 'CAPE VERDE','cou_name' => 'Cape Verde','iso3' => 'CPV','numcode' => '132'),
        array('cou_code' => 'KY','name' => 'CAYMAN ISLANDS','cou_name' => 'Cayman Islands','iso3' => 'CYM','numcode' => '136'),
        array('cou_code' => 'CF','name' => 'CENTRAL AFRICAN REPUBLIC','cou_name' => 'Central African Republic','iso3' => 'CAF','numcode' => '140'),
        array('cou_code' => 'TD','name' => 'CHAD','cou_name' => 'Chad','iso3' => 'TCD','numcode' => '148'),
        array('cou_code' => 'CL','name' => 'CHILE','cou_name' => 'Chile','iso3' => 'CHL','numcode' => '152'),
        array('cou_code' => 'CN','name' => 'CHINA','cou_name' => 'China','iso3' => 'CHN','numcode' => '156'),
        array('cou_code' => 'CX','name' => 'CHRISTMAS ISLAND','cou_name' => 'Christmas Island','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'CC','name' => 'COCOS (KEELING) ISLANDS','cou_name' => 'Cocos (Keeling) Islands','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'CO','name' => 'COLOMBIA','cou_name' => 'Colombia','iso3' => 'COL','numcode' => '170'),
        array('cou_code' => 'KM','name' => 'COMOROS','cou_name' => 'Comoros','iso3' => 'COM','numcode' => '174'),
        array('cou_code' => 'CG','name' => 'CONGO','cou_name' => 'Congo','iso3' => 'COG','numcode' => '178'),
        array('cou_code' => 'CD','name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE','cou_name' => 'Congo, the Democratic Republic of the','iso3' => 'COD','numcode' => '180'),
        array('cou_code' => 'CK','name' => 'COOK ISLANDS','cou_name' => 'Cook Islands','iso3' => 'COK','numcode' => '184'),
        array('cou_code' => 'CR','name' => 'COSTA RICA','cou_name' => 'Costa Rica','iso3' => 'CRI','numcode' => '188'),
        array('cou_code' => 'CI','name' => 'COTE D\'IVOIRE','cou_name' => 'Cote D\'Ivoire','iso3' => 'CIV','numcode' => '384'),
        array('cou_code' => 'HR','name' => 'CROATIA','cou_name' => 'Croatia','iso3' => 'HRV','numcode' => '191'),
        array('cou_code' => 'CU','name' => 'CUBA','cou_name' => 'Cuba','iso3' => 'CUB','numcode' => '192'),
        array('cou_code' => 'CY','name' => 'CYPRUS','cou_name' => 'Cyprus','iso3' => 'CYP','numcode' => '196'),
        array('cou_code' => 'CZ','name' => 'CZECH REPUBLIC','cou_name' => 'Czech Republic','iso3' => 'CZE','numcode' => '203'),
        array('cou_code' => 'DK','name' => 'DENMARK','cou_name' => 'Denmark','iso3' => 'DNK','numcode' => '208'),
        array('cou_code' => 'DJ','name' => 'DJIBOUTI','cou_name' => 'Djibouti','iso3' => 'DJI','numcode' => '262'),
        array('cou_code' => 'DM','name' => 'DOMINICA','cou_name' => 'Dominica','iso3' => 'DMA','numcode' => '212'),
        array('cou_code' => 'DO','name' => 'DOMINICAN REPUBLIC','cou_name' => 'Dominican Republic','iso3' => 'DOM','numcode' => '214'),
        array('cou_code' => 'EC','name' => 'ECUADOR','cou_name' => 'Ecuador','iso3' => 'ECU','numcode' => '218'),
        array('cou_code' => 'EG','name' => 'EGYPT','cou_name' => 'Egypt','iso3' => 'EGY','numcode' => '818'),
        array('cou_code' => 'SV','name' => 'EL SALVADOR','cou_name' => 'El Salvador','iso3' => 'SLV','numcode' => '222'),
        array('cou_code' => 'GQ','name' => 'EQUATORIAL GUINEA','cou_name' => 'Equatorial Guinea','iso3' => 'GNQ','numcode' => '226'),
        array('cou_code' => 'ER','name' => 'ERITREA','cou_name' => 'Eritrea','iso3' => 'ERI','numcode' => '232'),
        array('cou_code' => 'EE','name' => 'ESTONIA','cou_name' => 'Estonia','iso3' => 'EST','numcode' => '233'),
        array('cou_code' => 'ET','name' => 'ETHIOPIA','cou_name' => 'Ethiopia','iso3' => 'ETH','numcode' => '231'),
        array('cou_code' => 'FK','name' => 'FALKLAND ISLANDS (MALVINAS)','cou_name' => 'Falkland Islands (Malvinas)','iso3' => 'FLK','numcode' => '238'),
        array('cou_code' => 'FO','name' => 'FAROE ISLANDS','cou_name' => 'Faroe Islands','iso3' => 'FRO','numcode' => '234'),
        array('cou_code' => 'FJ','name' => 'FIJI','cou_name' => 'Fiji','iso3' => 'FJI','numcode' => '242'),
        array('cou_code' => 'FI','name' => 'FINLAND','cou_name' => 'Finland','iso3' => 'FIN','numcode' => '246'),
        array('cou_code' => 'FR','name' => 'FRANCE','cou_name' => 'France','iso3' => 'FRA','numcode' => '250'),
        array('cou_code' => 'GF','name' => 'FRENCH GUIANA','cou_name' => 'French Guiana','iso3' => 'GUF','numcode' => '254'),
        array('cou_code' => 'PF','name' => 'FRENCH POLYNESIA','cou_name' => 'French Polynesia','iso3' => 'PYF','numcode' => '258'),
        array('cou_code' => 'TF','name' => 'FRENCH SOUTHERN TERRITORIES','cou_name' => 'French Southern Territories','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'GA','name' => 'GABON','cou_name' => 'Gabon','iso3' => 'GAB','numcode' => '266'),
        array('cou_code' => 'GM','name' => 'GAMBIA','cou_name' => 'Gambia','iso3' => 'GMB','numcode' => '270'),
        array('cou_code' => 'GE','name' => 'GEORGIA','cou_name' => 'Georgia','iso3' => 'GEO','numcode' => '268'),
        array('cou_code' => 'DE','name' => 'GERMANY','cou_name' => 'Germany','iso3' => 'DEU','numcode' => '276'),
        array('cou_code' => 'GH','name' => 'GHANA','cou_name' => 'Ghana','iso3' => 'GHA','numcode' => '288'),
        array('cou_code' => 'GI','name' => 'GIBRALTAR','cou_name' => 'Gibraltar','iso3' => 'GIB','numcode' => '292'),
        array('cou_code' => 'GR','name' => 'GREECE','cou_name' => 'Greece','iso3' => 'GRC','numcode' => '300'),
        array('cou_code' => 'GL','name' => 'GREENLAND','cou_name' => 'Greenland','iso3' => 'GRL','numcode' => '304'),
        array('cou_code' => 'GD','name' => 'GRENADA','cou_name' => 'Grenada','iso3' => 'GRD','numcode' => '308'),
        array('cou_code' => 'GP','name' => 'GUADELOUPE','cou_name' => 'Guadeloupe','iso3' => 'GLP','numcode' => '312'),
        array('cou_code' => 'GU','name' => 'GUAM','cou_name' => 'Guam','iso3' => 'GUM','numcode' => '316'),
        array('cou_code' => 'GT','name' => 'GUATEMALA','cou_name' => 'Guatemala','iso3' => 'GTM','numcode' => '320'),
        array('cou_code' => 'GN','name' => 'GUINEA','cou_name' => 'Guinea','iso3' => 'GIN','numcode' => '324'),
        array('cou_code' => 'GW','name' => 'GUINEA-BISSAU','cou_name' => 'Guinea-Bissau','iso3' => 'GNB','numcode' => '624'),
        array('cou_code' => 'GY','name' => 'GUYANA','cou_name' => 'Guyana','iso3' => 'GUY','numcode' => '328'),
        array('cou_code' => 'HT','name' => 'HAITI','cou_name' => 'Haiti','iso3' => 'HTI','numcode' => '332'),
        array('cou_code' => 'HM','name' => 'HEARD ISLAND AND MCDONALD ISLANDS','cou_name' => 'Heard Island and Mcdonald Islands','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'VA','name' => 'HOLY SEE (VATICAN CITY STATE)','cou_name' => 'Holy See (Vatican City State)','iso3' => 'VAT','numcode' => '336'),
        array('cou_code' => 'HN','name' => 'HONDURAS','cou_name' => 'Honduras','iso3' => 'HND','numcode' => '340'),
        array('cou_code' => 'HK','name' => 'HONG KONG','cou_name' => 'Hong Kong','iso3' => 'HKG','numcode' => '344'),
        array('cou_code' => 'HU','name' => 'HUNGARY','cou_name' => 'Hungary','iso3' => 'HUN','numcode' => '348'),
        array('cou_code' => 'IS','name' => 'ICELAND','cou_name' => 'Iceland','iso3' => 'ISL','numcode' => '352'),
        array('cou_code' => 'IN','name' => 'INDIA','cou_name' => 'India','iso3' => 'IND','numcode' => '356'),
        array('cou_code' => 'ID','name' => 'INDONESIA','cou_name' => 'Indonesia','iso3' => 'IDN','numcode' => '360'),
        array('cou_code' => 'IR','name' => 'IRAN, ISLAMIC REPUBLIC OF','cou_name' => 'Iran, Islamic Republic of','iso3' => 'IRN','numcode' => '364'),
        array('cou_code' => 'IQ','name' => 'IRAQ','cou_name' => 'Iraq','iso3' => 'IRQ','numcode' => '368'),
        array('cou_code' => 'IE','name' => 'IRELAND','cou_name' => 'Ireland','iso3' => 'IRL','numcode' => '372'),
        array('cou_code' => 'IL','name' => 'ISRAEL','cou_name' => 'Israel','iso3' => 'ISR','numcode' => '376'),
        array('cou_code' => 'IT','name' => 'ITALY','cou_name' => 'Italy','iso3' => 'ITA','numcode' => '380'),
        array('cou_code' => 'JM','name' => 'JAMAICA','cou_name' => 'Jamaica','iso3' => 'JAM','numcode' => '388'),
        array('cou_code' => 'JP','name' => 'JAPAN','cou_name' => 'Japan','iso3' => 'JPN','numcode' => '392'),
        array('cou_code' => 'JO','name' => 'JORDAN','cou_name' => 'Jordan','iso3' => 'JOR','numcode' => '400'),
        array('cou_code' => 'KZ','name' => 'KAZAKHSTAN','cou_name' => 'Kazakhstan','iso3' => 'KAZ','numcode' => '398'),
        array('cou_code' => 'KE','name' => 'KENYA','cou_name' => 'Kenya','iso3' => 'KEN','numcode' => '404'),
        array('cou_code' => 'KI','name' => 'KIRIBATI','cou_name' => 'Kiribati','iso3' => 'KIR','numcode' => '296'),
        array('cou_code' => 'KP','name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','cou_name' => 'Korea, Democratic People\'s Republic of','iso3' => 'PRK','numcode' => '408'),
        array('cou_code' => 'KR','name' => 'KOREA, REPUBLIC OF','cou_name' => 'Korea, Republic of','iso3' => 'KOR','numcode' => '410'),
        array('cou_code' => 'KW','name' => 'KUWAIT','cou_name' => 'Kuwait','iso3' => 'KWT','numcode' => '414'),
        array('cou_code' => 'KG','name' => 'KYRGYZSTAN','cou_name' => 'Kyrgyzstan','iso3' => 'KGZ','numcode' => '417'),
        array('cou_code' => 'LA','name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC','cou_name' => 'Lao People\'s Democratic Republic','iso3' => 'LAO','numcode' => '418'),
        array('cou_code' => 'LV','name' => 'LATVIA','cou_name' => 'Latvia','iso3' => 'LVA','numcode' => '428'),
        array('cou_code' => 'LB','name' => 'LEBANON','cou_name' => 'Lebanon','iso3' => 'LBN','numcode' => '422'),
        array('cou_code' => 'LS','name' => 'LESOTHO','cou_name' => 'Lesotho','iso3' => 'LSO','numcode' => '426'),
        array('cou_code' => 'LR','name' => 'LIBERIA','cou_name' => 'Liberia','iso3' => 'LBR','numcode' => '430'),
        array('cou_code' => 'LY','name' => 'LIBYAN ARAB JAMAHIRIYA','cou_name' => 'Libyan Arab Jamahiriya','iso3' => 'LBY','numcode' => '434'),
        array('cou_code' => 'LI','name' => 'LIECHTENSTEIN','cou_name' => 'Liechtenstein','iso3' => 'LIE','numcode' => '438'),
        array('cou_code' => 'LT','name' => 'LITHUANIA','cou_name' => 'Lithuania','iso3' => 'LTU','numcode' => '440'),
        array('cou_code' => 'LU','name' => 'LUXEMBOURG','cou_name' => 'Luxembourg','iso3' => 'LUX','numcode' => '442'),
        array('cou_code' => 'MO','name' => 'MACAO','cou_name' => 'Macao','iso3' => 'MAC','numcode' => '446'),
        array('cou_code' => 'MK','name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','cou_name' => 'Macedonia, the Former Yugoslav Republic of','iso3' => 'MKD','numcode' => '807'),
        array('cou_code' => 'MG','name' => 'MADAGASCAR','cou_name' => 'Madagascar','iso3' => 'MDG','numcode' => '450'),
        array('cou_code' => 'MW','name' => 'MALAWI','cou_name' => 'Malawi','iso3' => 'MWI','numcode' => '454'),
        array('cou_code' => 'MY','name' => 'MALAYSIA','cou_name' => 'Malaysia','iso3' => 'MYS','numcode' => '458'),
        array('cou_code' => 'MV','name' => 'MALDIVES','cou_name' => 'Maldives','iso3' => 'MDV','numcode' => '462'),
        array('cou_code' => 'ML','name' => 'MALI','cou_name' => 'Mali','iso3' => 'MLI','numcode' => '466'),
        array('cou_code' => 'MT','name' => 'MALTA','cou_name' => 'Malta','iso3' => 'MLT','numcode' => '470'),
        array('cou_code' => 'MH','name' => 'MARSHALL ISLANDS','cou_name' => 'Marshall Islands','iso3' => 'MHL','numcode' => '584'),
        array('cou_code' => 'MQ','name' => 'MARTINIQUE','cou_name' => 'Martinique','iso3' => 'MTQ','numcode' => '474'),
        array('cou_code' => 'MR','name' => 'MAURITANIA','cou_name' => 'Mauritania','iso3' => 'MRT','numcode' => '478'),
        array('cou_code' => 'MU','name' => 'MAURITIUS','cou_name' => 'Mauritius','iso3' => 'MUS','numcode' => '480'),
        array('cou_code' => 'YT','name' => 'MAYOTTE','cou_name' => 'Mayotte','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'MX','name' => 'MEXICO','cou_name' => 'Mexico','iso3' => 'MEX','numcode' => '484'),
        array('cou_code' => 'FM','name' => 'MICRONESIA, FEDERATED STATES OF','cou_name' => 'Micronesia, Federated States of','iso3' => 'FSM','numcode' => '583'),
        array('cou_code' => 'MD','name' => 'MOLDOVA, REPUBLIC OF','cou_name' => 'Moldova, Republic of','iso3' => 'MDA','numcode' => '498'),
        array('cou_code' => 'MC','name' => 'MONACO','cou_name' => 'Monaco','iso3' => 'MCO','numcode' => '492'),
        array('cou_code' => 'MN','name' => 'MONGOLIA','cou_name' => 'Mongolia','iso3' => 'MNG','numcode' => '496'),
        array('cou_code' => 'MS','name' => 'MONTSERRAT','cou_name' => 'Montserrat','iso3' => 'MSR','numcode' => '500'),
        array('cou_code' => 'MA','name' => 'MOROCCO','cou_name' => 'Morocco','iso3' => 'MAR','numcode' => '504'),
        array('cou_code' => 'MZ','name' => 'MOZAMBIQUE','cou_name' => 'Mozambique','iso3' => 'MOZ','numcode' => '508'),
        array('cou_code' => 'MM','name' => 'MYANMAR','cou_name' => 'Myanmar','iso3' => 'MMR','numcode' => '104'),
        array('cou_code' => 'NA','name' => 'NAMIBIA','cou_name' => 'Namibia','iso3' => 'NAM','numcode' => '516'),
        array('cou_code' => 'NR','name' => 'NAURU','cou_name' => 'Nauru','iso3' => 'NRU','numcode' => '520'),
        array('cou_code' => 'NP','name' => 'NEPAL','cou_name' => 'Nepal','iso3' => 'NPL','numcode' => '524'),
        array('cou_code' => 'NL','name' => 'NETHERLANDS','cou_name' => 'Netherlands','iso3' => 'NLD','numcode' => '528'),
        array('cou_code' => 'AN','name' => 'NETHERLANDS ANTILLES','cou_name' => 'Netherlands Antilles','iso3' => 'ANT','numcode' => '530'),
        array('cou_code' => 'NC','name' => 'NEW CALEDONIA','cou_name' => 'New Caledonia','iso3' => 'NCL','numcode' => '540'),
        array('cou_code' => 'NZ','name' => 'NEW ZEALAND','cou_name' => 'New Zealand','iso3' => 'NZL','numcode' => '554'),
        array('cou_code' => 'NI','name' => 'NICARAGUA','cou_name' => 'Nicaragua','iso3' => 'NIC','numcode' => '558'),
        array('cou_code' => 'NE','name' => 'NIGER','cou_name' => 'Niger','iso3' => 'NER','numcode' => '562'),
        array('cou_code' => 'NG','name' => 'NIGERIA','cou_name' => 'Nigeria','iso3' => 'NGA','numcode' => '566'),
        array('cou_code' => 'NU','name' => 'NIUE','cou_name' => 'Niue','iso3' => 'NIU','numcode' => '570'),
        array('cou_code' => 'NF','name' => 'NORFOLK ISLAND','cou_name' => 'Norfolk Island','iso3' => 'NFK','numcode' => '574'),
        array('cou_code' => 'MP','name' => 'NORTHERN MARIANA ISLANDS','cou_name' => 'Northern Mariana Islands','iso3' => 'MNP','numcode' => '580'),
        array('cou_code' => 'NO','name' => 'NORWAY','cou_name' => 'Norway','iso3' => 'NOR','numcode' => '578'),
        array('cou_code' => 'OM','name' => 'OMAN','cou_name' => 'Oman','iso3' => 'OMN','numcode' => '512'),
        array('cou_code' => 'PK','name' => 'PAKISTAN','cou_name' => 'Pakistan','iso3' => 'PAK','numcode' => '586'),
        array('cou_code' => 'PW','name' => 'PALAU','cou_name' => 'Palau','iso3' => 'PLW','numcode' => '585'),
        array('cou_code' => 'PS','name' => 'PALESTINIAN TERRITORY, OCCUPIED','cou_name' => 'Palestinian Territory, Occupied','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'PA','name' => 'PANAMA','cou_name' => 'Panama','iso3' => 'PAN','numcode' => '591'),
        array('cou_code' => 'PG','name' => 'PAPUA NEW GUINEA','cou_name' => 'Papua New Guinea','iso3' => 'PNG','numcode' => '598'),
        array('cou_code' => 'PY','name' => 'PARAGUAY','cou_name' => 'Paraguay','iso3' => 'PRY','numcode' => '600'),
        array('cou_code' => 'PE','name' => 'PERU','cou_name' => 'Peru','iso3' => 'PER','numcode' => '604'),
        array('cou_code' => 'PH','name' => 'PHILIPPINES','cou_name' => 'Philippines','iso3' => 'PHL','numcode' => '608'),
        array('cou_code' => 'PN','name' => 'PITCAIRN','cou_name' => 'Pitcairn','iso3' => 'PCN','numcode' => '612'),
        array('cou_code' => 'PL','name' => 'POLAND','cou_name' => 'Poland','iso3' => 'POL','numcode' => '616'),
        array('cou_code' => 'PT','name' => 'PORTUGAL','cou_name' => 'Portugal','iso3' => 'PRT','numcode' => '620'),
        array('cou_code' => 'PR','name' => 'PUERTO RICO','cou_name' => 'Puerto Rico','iso3' => 'PRI','numcode' => '630'),
        array('cou_code' => 'QA','name' => 'QATAR','cou_name' => 'Qatar','iso3' => 'QAT','numcode' => '634'),
        array('cou_code' => 'RE','name' => 'REUNION','cou_name' => 'Reunion','iso3' => 'REU','numcode' => '638'),
        array('cou_code' => 'RO','name' => 'ROMANIA','cou_name' => 'Romania','iso3' => 'ROM','numcode' => '642'),
        array('cou_code' => 'RU','name' => 'RUSSIAN FEDERATION','cou_name' => 'Russian Federation','iso3' => 'RUS','numcode' => '643'),
        array('cou_code' => 'RW','name' => 'RWANDA','cou_name' => 'Rwanda','iso3' => 'RWA','numcode' => '646'),
        array('cou_code' => 'SH','name' => 'SAINT HELENA','cou_name' => 'Saint Helena','iso3' => 'SHN','numcode' => '654'),
        array('cou_code' => 'KN','name' => 'SAINT KITTS AND NEVIS','cou_name' => 'Saint Kitts and Nevis','iso3' => 'KNA','numcode' => '659'),
        array('cou_code' => 'LC','name' => 'SAINT LUCIA','cou_name' => 'Saint Lucia','iso3' => 'LCA','numcode' => '662'),
        array('cou_code' => 'PM','name' => 'SAINT PIERRE AND MIQUELON','cou_name' => 'Saint Pierre and Miquelon','iso3' => 'SPM','numcode' => '666'),
        array('cou_code' => 'VC','name' => 'SAINT VINCENT AND THE GRENADINES','cou_name' => 'Saint Vincent and the Grenadines','iso3' => 'VCT','numcode' => '670'),
        array('cou_code' => 'WS','name' => 'SAMOA','cou_name' => 'Samoa','iso3' => 'WSM','numcode' => '882'),
        array('cou_code' => 'SM','name' => 'SAN MARINO','cou_name' => 'San Marino','iso3' => 'SMR','numcode' => '674'),
        array('cou_code' => 'ST','name' => 'SAO TOME AND PRINCIPE','cou_name' => 'Sao Tome and Principe','iso3' => 'STP','numcode' => '678'),
        array('cou_code' => 'SA','name' => 'SAUDI ARABIA','cou_name' => 'Saudi Arabia','iso3' => 'SAU','numcode' => '682'),
        array('cou_code' => 'SN','name' => 'SENEGAL','cou_name' => 'Senegal','iso3' => 'SEN','numcode' => '686'),
        array('cou_code' => 'CS','name' => 'SERBIA AND MONTENEGRO','cou_name' => 'Serbia and Montenegro','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'SC','name' => 'SEYCHELLES','cou_name' => 'Seychelles','iso3' => 'SYC','numcode' => '690'),
        array('cou_code' => 'SL','name' => 'SIERRA LEONE','cou_name' => 'Sierra Leone','iso3' => 'SLE','numcode' => '694'),
        array('cou_code' => 'SG','name' => 'SINGAPORE','cou_name' => 'Singapore','iso3' => 'SGP','numcode' => '702'),
        array('cou_code' => 'SK','name' => 'SLOVAKIA','cou_name' => 'Slovakia','iso3' => 'SVK','numcode' => '703'),
        array('cou_code' => 'SI','name' => 'SLOVENIA','cou_name' => 'Slovenia','iso3' => 'SVN','numcode' => '705'),
        array('cou_code' => 'SB','name' => 'SOLOMON ISLANDS','cou_name' => 'Solomon Islands','iso3' => 'SLB','numcode' => '90'),
        array('cou_code' => 'SO','name' => 'SOMALIA','cou_name' => 'Somalia','iso3' => 'SOM','numcode' => '706'),
        array('cou_code' => 'ZA','name' => 'SOUTH AFRICA','cou_name' => 'South Africa','iso3' => 'ZAF','numcode' => '710'),
        array('cou_code' => 'GS','name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','cou_name' => 'South Georgia and the South Sandwich Islands','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'ES','name' => 'SPAIN','cou_name' => 'Spain','iso3' => 'ESP','numcode' => '724'),
        array('cou_code' => 'LK','name' => 'SRI LANKA','cou_name' => 'Sri Lanka','iso3' => 'LKA','numcode' => '144'),
        array('cou_code' => 'SD','name' => 'SUDAN','cou_name' => 'Sudan','iso3' => 'SDN','numcode' => '736'),
        array('cou_code' => 'SR','name' => 'SURINAME','cou_name' => 'Suriname','iso3' => 'SUR','numcode' => '740'),
        array('cou_code' => 'SJ','name' => 'SVALBARD AND JAN MAYEN','cou_name' => 'Svalbard and Jan Mayen','iso3' => 'SJM','numcode' => '744'),
        array('cou_code' => 'SZ','name' => 'SWAZILAND','cou_name' => 'Swaziland','iso3' => 'SWZ','numcode' => '748'),
        array('cou_code' => 'SE','name' => 'SWEDEN','cou_name' => 'Sweden','iso3' => 'SWE','numcode' => '752'),
        array('cou_code' => 'CH','name' => 'SWITZERLAND','cou_name' => 'Switzerland','iso3' => 'CHE','numcode' => '756'),
        array('cou_code' => 'SY','name' => 'SYRIAN ARAB REPUBLIC','cou_name' => 'Syrian Arab Republic','iso3' => 'SYR','numcode' => '760'),
        array('cou_code' => 'TW','name' => 'TAIWAN, PROVINCE OF CHINA','cou_name' => 'Taiwan','iso3' => 'TWN','numcode' => '158'),
        array('cou_code' => 'TJ','name' => 'TAJIKISTAN','cou_name' => 'Tajikistan','iso3' => 'TJK','numcode' => '762'),
        array('cou_code' => 'TZ','name' => 'TANZANIA, UNITED REPUBLIC OF','cou_name' => 'Tanzania, United Republic of','iso3' => 'TZA','numcode' => '834'),
        array('cou_code' => 'TH','name' => 'THAILAND','cou_name' => 'Thailand','iso3' => 'THA','numcode' => '764'),
        array('cou_code' => 'TL','name' => 'TIMOR-LESTE','cou_name' => 'Timor-Leste','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'TG','name' => 'TOGO','cou_name' => 'Togo','iso3' => 'TGO','numcode' => '768'),
        array('cou_code' => 'TK','name' => 'TOKELAU','cou_name' => 'Tokelau','iso3' => 'TKL','numcode' => '772'),
        array('cou_code' => 'TO','name' => 'TONGA','cou_name' => 'Tonga','iso3' => 'TON','numcode' => '776'),
        array('cou_code' => 'TT','name' => 'TRINIDAD AND TOBAGO','cou_name' => 'Trinidad and Tobago','iso3' => 'TTO','numcode' => '780'),
        array('cou_code' => 'TN','name' => 'TUNISIA','cou_name' => 'Tunisia','iso3' => 'TUN','numcode' => '788'),
        array('cou_code' => 'TR','name' => 'TURKEY','cou_name' => 'Turkey','iso3' => 'TUR','numcode' => '792'),
        array('cou_code' => 'TM','name' => 'TURKMENISTAN','cou_name' => 'Turkmenistan','iso3' => 'TKM','numcode' => '795'),
        array('cou_code' => 'TC','name' => 'TURKS AND CAICOS ISLANDS','cou_name' => 'Turks and Caicos Islands','iso3' => 'TCA','numcode' => '796'),
        array('cou_code' => 'TV','name' => 'TUVALU','cou_name' => 'Tuvalu','iso3' => 'TUV','numcode' => '798'),
        array('cou_code' => 'UG','name' => 'UGANDA','cou_name' => 'Uganda','iso3' => 'UGA','numcode' => '800'),
        array('cou_code' => 'UA','name' => 'UKRAINE','cou_name' => 'Ukraine','iso3' => 'UKR','numcode' => '804'),
        array('cou_code' => 'AE','name' => 'UNITED ARAB EMIRATES','cou_name' => 'United Arab Emirates','iso3' => 'ARE','numcode' => '784'),
        array('cou_code' => 'GB','name' => 'UNITED KINGDOM','cou_name' => 'United Kingdom','iso3' => 'GBR','numcode' => '826'),
        array('cou_code' => 'US','name' => 'UNITED STATES','cou_name' => 'United States','iso3' => 'USA','numcode' => '840'),
        array('cou_code' => 'UM','name' => 'UNITED STATES MINOR OUTLYING ISLANDS','cou_name' => 'United States Minor Outlying Islands','iso3' => NULL,'numcode' => NULL),
        array('cou_code' => 'UY','name' => 'URUGUAY','cou_name' => 'Uruguay','iso3' => 'URY','numcode' => '858'),
        array('cou_code' => 'UZ','name' => 'UZBEKISTAN','cou_name' => 'Uzbekistan','iso3' => 'UZB','numcode' => '860'),
        array('cou_code' => 'VU','name' => 'VANUATU','cou_name' => 'Vanuatu','iso3' => 'VUT','numcode' => '548'),
        array('cou_code' => 'VE','name' => 'VENEZUELA','cou_name' => 'Venezuela','iso3' => 'VEN','numcode' => '862'),
        array('cou_code' => 'VN','name' => 'VIET NAM','cou_name' => 'Viet Nam','iso3' => 'VNM','numcode' => '704'),
        array('cou_code' => 'VG','name' => 'VIRGIN ISLANDS, BRITISH','cou_name' => 'Virgin Islands, British','iso3' => 'VGB','numcode' => '92'),
        array('cou_code' => 'VI','name' => 'VIRGIN ISLANDS, U.S.','cou_name' => 'Virgin Islands, U.s.','iso3' => 'VIR','numcode' => '850'),
        array('cou_code' => 'WF','name' => 'WALLIS AND FUTUNA','cou_name' => 'Wallis and Futuna','iso3' => 'WLF','numcode' => '876'),
        array('cou_code' => 'EH','name' => 'WESTERN SAHARA','cou_name' => 'Western Sahara','iso3' => 'ESH','numcode' => '732'),
        array('cou_code' => 'YE','name' => 'YEMEN','cou_name' => 'Yemen','iso3' => 'YEM','numcode' => '887'),
        array('cou_code' => 'ZM','name' => 'ZAMBIA','cou_name' => 'Zambia','iso3' => 'ZMB','numcode' => '894'),
        array('cou_code' => 'ZW','name' => 'ZIMBABWE','cou_name' => 'Zimbabwe','iso3' => 'ZWE','numcode' => '716')
    );

    /**
     * Returns Country List
     * @return array
     */
    function getCountryList() {
        $list = array();

        $countries = $this->hs_hr_country;
        foreach ($countries as $country) {
            $list[$country['cou_code']] = $country['cou_name'];
        }
        return $list;
    }

    /**
     * Returns Language List
     * @return array
     */
    function getLanguageList() {
        $list = array();

        $languageArray = $this->languages['languages'];
        foreach ($languageArray as $language) {
            $list[$language['key']] = $language['value'];
        }

        return $list;
    }

    /**
     * Returns Time Zone List
     * @return array
     */
    function getTimeZoneList() {
        $list = array();

        $timezoneList = timezone_identifiers_list();

        foreach ($timezoneList as $key => $timezone) {
            $list[$timezone] = $key;
        }

        return $list;
    }


}