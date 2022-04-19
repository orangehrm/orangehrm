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

namespace OrangeHRM\Installer\Util;

use DateTimeZone;

class InstanceCreationHelper
{
    public const COUNTRIES = [
        ['id' => 'AD', 'label' => 'Andorra'],
        ['id' => 'AE', 'label' => 'United Arab Emirates'],
        ['id' => 'AF', 'label' => 'Afghanistan'],
        ['id' => 'AG', 'label' => 'Antigua and Barbuda'],
        ['id' => 'AI', 'label' => 'Anguilla'],
        ['id' => 'AL', 'label' => 'Albania'],
        ['id' => 'AM', 'label' => 'Armenia'],
        ['id' => 'AN', 'label' => 'Netherlands Antilles'],
        ['id' => 'AO', 'label' => 'Angola'],
        ['id' => 'AQ', 'label' => 'Antarctica'],
        ['id' => 'AR', 'label' => 'Argentina'],
        ['id' => 'AS', 'label' => 'American Samoa'],
        ['id' => 'AT', 'label' => 'Austria'],
        ['id' => 'AU', 'label' => 'Australia'],
        ['id' => 'AW', 'label' => 'Aruba'],
        ['id' => 'AZ', 'label' => 'Azerbaijan'],
        ['id' => 'BA', 'label' => 'Bosnia and Herzegovina'],
        ['id' => 'BB', 'label' => 'Barbados'],
        ['id' => 'BD', 'label' => 'Bangladesh'],
        ['id' => 'BE', 'label' => 'Belgium'],
        ['id' => 'BF', 'label' => 'Burkina Faso'],
        ['id' => 'BG', 'label' => 'Bulgaria'],
        ['id' => 'BH', 'label' => 'Bahrain'],
        ['id' => 'BI', 'label' => 'Burundi'],
        ['id' => 'BJ', 'label' => 'Benin'],
        ['id' => 'BM', 'label' => 'Bermuda'],
        ['id' => 'BN', 'label' => 'Brunei Darussalam'],
        ['id' => 'BO', 'label' => 'Bolivia'],
        ['id' => 'BR', 'label' => 'Brazil'],
        ['id' => 'BS', 'label' => 'Bahamas'],
        ['id' => 'BT', 'label' => 'Bhutan'],
        ['id' => 'BV', 'label' => 'Bouvet Island'],
        ['id' => 'BW', 'label' => 'Botswana'],
        ['id' => 'BY', 'label' => 'Belarus'],
        ['id' => 'BZ', 'label' => 'Belize'],
        ['id' => 'CA', 'label' => 'Canada'],
        ['id' => 'CC', 'label' => 'Cocos (Keeling) Islands'],
        ['id' => 'CD', 'label' => 'Congo, the Democratic Republic of the'],
        ['id' => 'CF', 'label' => 'Central African Republic'],
        ['id' => 'CG', 'label' => 'Congo'],
        ['id' => 'CH', 'label' => 'Switzerland'],
        ['id' => 'CI', 'label' => 'Cote D\'Ivoire'],
        ['id' => 'CK', 'label' => 'Cook Islands'],
        ['id' => 'CL', 'label' => 'Chile'],
        ['id' => 'CM', 'label' => 'Cameroon'],
        ['id' => 'CN', 'label' => 'China'],
        ['id' => 'CO', 'label' => 'Colombia'],
        ['id' => 'CR', 'label' => 'Costa Rica'],
        ['id' => 'CS', 'label' => 'Serbia and Montenegro'],
        ['id' => 'CU', 'label' => 'Cuba'],
        ['id' => 'CV', 'label' => 'Cape Verde'],
        ['id' => 'CX', 'label' => 'Christmas Island'],
        ['id' => 'CY', 'label' => 'Cyprus'],
        ['id' => 'CZ', 'label' => 'Czech Republic'],
        ['id' => 'DE', 'label' => 'Germany'],
        ['id' => 'DJ', 'label' => 'Djibouti'],
        ['id' => 'DK', 'label' => 'Denmark'],
        ['id' => 'DM', 'label' => 'Dominica'],
        ['id' => 'DO', 'label' => 'Dominican Republic'],
        ['id' => 'DZ', 'label' => 'Algeria'],
        ['id' => 'EC', 'label' => 'Ecuador'],
        ['id' => 'EE', 'label' => 'Estonia'],
        ['id' => 'EG', 'label' => 'Egypt'],
        ['id' => 'EH', 'label' => 'Western Sahara'],
        ['id' => 'ER', 'label' => 'Eritrea'],
        ['id' => 'ES', 'label' => 'Spain'],
        ['id' => 'ET', 'label' => 'Ethiopia'],
        ['id' => 'FI', 'label' => 'Finland'],
        ['id' => 'FJ', 'label' => 'Fiji'],
        ['id' => 'FK', 'label' => 'Falkland Islands (Malvinas)'],
        ['id' => 'FM', 'label' => 'Micronesia, Federated States of'],
        ['id' => 'FO', 'label' => 'Faroe Islands'],
        ['id' => 'FR', 'label' => 'France'],
        ['id' => 'GA', 'label' => 'Gabon'],
        ['id' => 'GB', 'label' => 'United Kingdom'],
        ['id' => 'GD', 'label' => 'Grenada'],
        ['id' => 'GE', 'label' => 'Georgia'],
        ['id' => 'GF', 'label' => 'French Guiana'],
        ['id' => 'GH', 'label' => 'Ghana'],
        ['id' => 'GI', 'label' => 'Gibraltar'],
        ['id' => 'GL', 'label' => 'Greenland'],
        ['id' => 'GM', 'label' => 'Gambia'],
        ['id' => 'GN', 'label' => 'Guinea'],
        ['id' => 'GP', 'label' => 'Guadeloupe'],
        ['id' => 'GQ', 'label' => 'Equatorial Guinea'],
        ['id' => 'GR', 'label' => 'Greece'],
        ['id' => 'GS', 'label' => 'South Georgia and the South Sandwich Islands'],
        ['id' => 'GT', 'label' => 'Guatemala'],
        ['id' => 'GU', 'label' => 'Guam'],
        ['id' => 'GW', 'label' => 'Guinea-Bissau'],
        ['id' => 'GY', 'label' => 'Guyana'],
        ['id' => 'HK', 'label' => 'Hong Kong'],
        ['id' => 'HM', 'label' => 'Heard Island and Mcdonald Islands'],
        ['id' => 'HN', 'label' => 'Honduras'],
        ['id' => 'HR', 'label' => 'Croatia'],
        ['id' => 'HT', 'label' => 'Haiti'],
        ['id' => 'HU', 'label' => 'Hungary'],
        ['id' => 'ID', 'label' => 'Indonesia'],
        ['id' => 'IE', 'label' => 'Ireland'],
        ['id' => 'IL', 'label' => 'Israel'],
        ['id' => 'IN', 'label' => 'India'],
        ['id' => 'IO', 'label' => 'British Indian Ocean Territory'],
        ['id' => 'IQ', 'label' => 'Iraq'],
        ['id' => 'IR', 'label' => 'Iran, Islamic Republic of'],
        ['id' => 'IS', 'label' => 'Iceland'],
        ['id' => 'IT', 'label' => 'Italy'],
        ['id' => 'JM', 'label' => 'Jamaica'],
        ['id' => 'JO', 'label' => 'Jordan'],
        ['id' => 'JP', 'label' => 'Japan'],
        ['id' => 'KE', 'label' => 'Kenya'],
        ['id' => 'KG', 'label' => 'Kyrgyzstan'],
        ['id' => 'KH', 'label' => 'Cambodia'],
        ['id' => 'KI', 'label' => 'Kiribati'],
        ['id' => 'KM', 'label' => 'Comoros'],
        ['id' => 'KN', 'label' => 'Saint Kitts and Nevis'],
        ['id' => 'KP', 'label' => 'Korea, Democratic People\'s Republic of'],
        ['id' => 'KR', 'label' => 'Korea, Republic of'],
        ['id' => 'KW', 'label' => 'Kuwait'],
        ['id' => 'KY', 'label' => 'Cayman Islands'],
        ['id' => 'KZ', 'label' => 'Kazakhstan'],
        ['id' => 'LA', 'label' => 'Lao People\'s Democratic Republic'],
        ['id' => 'LB', 'label' => 'Lebanon'],
        ['id' => 'LC', 'label' => 'Saint Lucia'],
        ['id' => 'LI', 'label' => 'Liechtenstein'],
        ['id' => 'LK', 'label' => 'Sri Lanka'],
        ['id' => 'LR', 'label' => 'Liberia'],
        ['id' => 'LS', 'label' => 'Lesotho'],
        ['id' => 'LT', 'label' => 'Lithuania'],
        ['id' => 'LU', 'label' => 'Luxembourg'],
        ['id' => 'LV', 'label' => 'Latvia'],
        ['id' => 'LY', 'label' => 'Libyan Arab Jamahiriya'],
        ['id' => 'MA', 'label' => 'Morocco'],
        ['id' => 'MC', 'label' => 'Monaco'],
        ['id' => 'MD', 'label' => 'Moldova, Republic of'],
        ['id' => 'MG', 'label' => 'Madagascar'],
        ['id' => 'MH', 'label' => 'Marshall Islands'],
        ['id' => 'MK', 'label' => 'Macedonia, the Former Yugoslav Republic of'],
        ['id' => 'ML', 'label' => 'Mali'],
        ['id' => 'MM', 'label' => 'Myanmar'],
        ['id' => 'MN', 'label' => 'Mongolia'],
        ['id' => 'MO', 'label' => 'Macao'],
        ['id' => 'MP', 'label' => 'Northern Mariana Islands'],
        ['id' => 'MQ', 'label' => 'Martinique'],
        ['id' => 'MR', 'label' => 'Mauritania'],
        ['id' => 'MS', 'label' => 'Montserrat'],
        ['id' => 'MT', 'label' => 'Malta'],
        ['id' => 'MU', 'label' => 'Mauritius'],
        ['id' => 'MV', 'label' => 'Maldives'],
        ['id' => 'MW', 'label' => 'Malawi'],
        ['id' => 'MX', 'label' => 'Mexico'],
        ['id' => 'MY', 'label' => 'Malaysia'],
        ['id' => 'MZ', 'label' => 'Mozambique'],
        ['id' => 'NA', 'label' => 'Namibia'],
        ['id' => 'NC', 'label' => 'New Caledonia'],
        ['id' => 'NE', 'label' => 'Niger'],
        ['id' => 'NF', 'label' => 'Norfolk Island'],
        ['id' => 'NG', 'label' => 'Nigeria'],
        ['id' => 'NI', 'label' => 'Nicaragua'],
        ['id' => 'NL', 'label' => 'Netherlands'],
        ['id' => 'NO', 'label' => 'Norway'],
        ['id' => 'NP', 'label' => 'Nepal'],
        ['id' => 'NR', 'label' => 'Nauru'],
        ['id' => 'NU', 'label' => 'Niue'],
        ['id' => 'NZ', 'label' => 'New Zealand'],
        ['id' => 'OM', 'label' => 'Oman'],
        ['id' => 'PA', 'label' => 'Panama'],
        ['id' => 'PE', 'label' => 'Peru'],
        ['id' => 'PF', 'label' => 'French Polynesia'],
        ['id' => 'PG', 'label' => 'Papua New Guinea'],
        ['id' => 'PH', 'label' => 'Philippines'],
        ['id' => 'PK', 'label' => 'Pakistan'],
        ['id' => 'PL', 'label' => 'Poland'],
        ['id' => 'PM', 'label' => 'Saint Pierre and Miquelon'],
        ['id' => 'PN', 'label' => 'Pitcairn'],
        ['id' => 'PR', 'label' => 'Puerto Rico'],
        ['id' => 'PS', 'label' => 'Palestinian Territory, Occupied'],
        ['id' => 'PT', 'label' => 'Portugal'],
        ['id' => 'PW', 'label' => 'Palau'],
        ['id' => 'PY', 'label' => 'Paraguay'],
        ['id' => 'QA', 'label' => 'Qatar'],
        ['id' => 'RE', 'label' => 'Reunion'],
        ['id' => 'RO', 'label' => 'Romania'],
        ['id' => 'RU', 'label' => 'Russian Federation'],
        ['id' => 'RW', 'label' => 'Rwanda'],
        ['id' => 'SA', 'label' => 'Saudi Arabia'],
        ['id' => 'SB', 'label' => 'Solomon Islands'],
        ['id' => 'SC', 'label' => 'Seychelles'],
        ['id' => 'SD', 'label' => 'Sudan'],
        ['id' => 'SE', 'label' => 'Sweden'],
        ['id' => 'SG', 'label' => 'Singapore'],
        ['id' => 'SH', 'label' => 'Saint Helena'],
        ['id' => 'SI', 'label' => 'Slovenia'],
        ['id' => 'SJ', 'label' => 'Svalbard and Jan Mayen'],
        ['id' => 'SK', 'label' => 'Slovakia'],
        ['id' => 'SL', 'label' => 'Sierra Leone'],
        ['id' => 'SM', 'label' => 'San Marino'],
        ['id' => 'SN', 'label' => 'Senegal'],
        ['id' => 'SO', 'label' => 'Somalia'],
        ['id' => 'SR', 'label' => 'Suriname'],
        ['id' => 'ST', 'label' => 'Sao Tome and Principe'],
        ['id' => 'SV', 'label' => 'El Salvador'],
        ['id' => 'SY', 'label' => 'Syrian Arab Republic'],
        ['id' => 'SZ', 'label' => 'Swaziland'],
        ['id' => 'TC', 'label' => 'Turks and Caicos Islands'],
        ['id' => 'TD', 'label' => 'Chad'],
        ['id' => 'TF', 'label' => 'French Southern Territories'],
        ['id' => 'TG', 'label' => 'Togo'],
        ['id' => 'TH', 'label' => 'Thailand'],
        ['id' => 'TJ', 'label' => 'Tajikistan'],
        ['id' => 'TK', 'label' => 'Tokelau'],
        ['id' => 'TL', 'label' => 'Timor-Leste'],
        ['id' => 'TM', 'label' => 'Turkmenistan'],
        ['id' => 'TN', 'label' => 'Tunisia'],
        ['id' => 'TO', 'label' => 'Tonga'],
        ['id' => 'TR', 'label' => 'Turkey'],
        ['id' => 'TT', 'label' => 'Trinidad and Tobago'],
        ['id' => 'TV', 'label' => 'Tuvalu'],
        ['id' => 'TW', 'label' => 'Taiwan'],
        ['id' => 'TZ', 'label' => 'Tanzania, United Republic of'],
        ['id' => 'UA', 'label' => 'Ukraine'],
        ['id' => 'UG', 'label' => 'Uganda'],
        ['id' => 'UM', 'label' => 'United States Minor Outlying Islands'],
        ['id' => 'US', 'label' => 'United States'],
        ['id' => 'UY', 'label' => 'Uruguay'],
        ['id' => 'UZ', 'label' => 'Uzbekistan'],
        ['id' => 'VA', 'label' => 'Holy See (Vatican City State)'],
        ['id' => 'VC', 'label' => 'Saint Vincent and the Grenadines'],
        ['id' => 'VE', 'label' => 'Venezuela'],
        ['id' => 'VG', 'label' => 'Virgin Islands, British'],
        ['id' => 'VI', 'label' => 'Virgin Islands, U.s.'],
        ['id' => 'VN', 'label' => 'Viet Nam'],
        ['id' => 'VU', 'label' => 'Vanuatu'],
        ['id' => 'WF', 'label' => 'Wallis and Futuna'],
        ['id' => 'WS', 'label' => 'Samoa'],
        ['id' => 'YE', 'label' => 'Yemen'],
        ['id' => 'YT', 'label' => 'Mayotte'],
        ['id' => 'ZA', 'label' => 'South Africa'],
        ['id' => 'ZM', 'label' => 'Zambia'],
        ['id' => 'ZW', 'label' => 'Zimbabwe'],
    ];

    public const LANGUAGES = [
        ['id' => 'zh_Hans_CN', 'label' => 'Chinese (Simplified, China) - 中文（简体，中国）'],
        ['id' => 'zh_Hant_TW', 'label' => 'Chinese (Traditional, Taiwan) - 中文（繁體，台灣）'],
        ['id' => 'nl', 'label' => 'Dutch - Nederlands'],
        ['id' => 'en_US', 'label' => 'English (United States)'],
        ['id' => 'fr', 'label' => 'French - Français'],
        ['id' => 'de', 'label' => 'German - Deutsch'],
        ['id' => 'es', 'label' => 'Spanish - Español'],
        ['id' => 'es_CR', 'label' => 'Spanish (Costa Rica) - Español (Costa Rica)'],
    ];

    /**
     * @return array
     */
    public static function getTimezones(): array
    {
        $identifiers = DateTimeZone::listIdentifiers();
        $timezones = [];
        foreach ($identifiers as $timezoneIdentifier) {
            $timezones[] = [
                'id' => $timezoneIdentifier,
                'label' => $timezoneIdentifier,
            ];
        }
        return $timezones;
    }

    /**
     * @param string $countryCode
     * @return string[] e.g. ['id' => 'AD', 'label' => 'Andorra']
     */
    public static function getCountryByCode(string $countryCode): array
    {
        $index = array_search($countryCode, array_column(self::COUNTRIES, 'id'));
        return self::COUNTRIES[$index];
    }

    /**
     * @param string $langCode
     * @return string[] e.g. ['id' => 'en_US', 'label' => 'English (United States)']
     */
    public static function getLanguageByCode(string $langCode): array
    {
        $index = array_search($langCode, array_column(self::LANGUAGES, 'id'));
        return self::LANGUAGES[$index];
    }
}
