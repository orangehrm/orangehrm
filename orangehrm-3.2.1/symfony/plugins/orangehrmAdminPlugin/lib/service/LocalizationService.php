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
class LocalizationService {

    public function convertPHPFormatDateToISOFormatDate($inputPHPFormat, $date) {
        $dateFormat = new sfDateFormat();
        try {
            $symfonyPattern = $this->__getSymfonyDateFormatPattern($inputPHPFormat);
            $dateParts = $dateFormat->getDate($date, $symfonyPattern);

            if (is_array($dateParts) && isset($dateParts['year']) && isset($dateParts['mon']) && isset($dateParts['mday'])) {

                $day = $dateParts['mday'];
                $month = $dateParts['mon'];
                $year = $dateParts['year'];

                // Additional check done for 3 digit years, or more than 4 digit years
                if (checkdate($month, $day, $year) && ($year >= 1000) && ($year <= 9999)) {
                    $dateTime = new DateTime();
                    $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
                    $dateTime->setDate($year, $month, $day);

                    $date = $dateTime->format('Y-m-d');
                    return $date;
                }
                else
                    return "Invalid date";
            }
        } catch (Exception $e) {
            return "Invalid date";
        }

        return null;
    }

    private function __getSymfonyDateFormatPattern($pattern) {
        $symfonyDateFormat = "";

        $len = strlen($pattern);

        for ($i = 0; $i < $len; $i++) {
            $char = $pattern{$i};
            switch ($char) {
                case 'j':
                    $symfonyDateFormat .= 'd';
                    break;
                case 'd':
                    $symfonyDateFormat .= 'dd';
                    break;
                case 'D':
                    $symfonyDateFormat .= 'EE';
                    break;
                case 'l':
                    $symfonyDateFormat .= 'EEEE';
                    break;
                case 'n':
                    $symfonyDateFormat .= 'M';
                    break;
                case 'm':
                    $symfonyDateFormat .= 'MM';
                    break;
                case 'M':
                    $symfonyDateFormat .= 'MMM';
                    break;
                case 'F':
                    $symfonyDateFormat .= 'MMMM';
                    break;
                case 'y':
                    $symfonyDateFormat .= 'yy';
                    break;
                case 'Y':
                    $symfonyDateFormat .= 'y';
                    break;
                default:
                    $symfonyDateFormat .= $char;
                    break;
            }
        }
        return $symfonyDateFormat;
    }

    public function getSupportedLanguageListFromYML() {
        $languageList = array();
        $languages = sfYaml::load(sfConfig::get("sf_plugins_dir") . '/orangehrmAdminPlugin/config/supported_languages.yml');
        foreach ($languages['languages'] as $lang) {
            $languageList[$lang['key']] = $lang['value'];
        }
        return $languageList;
    }

}
