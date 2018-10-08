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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class FormatWithCompetency
 */
class FormatWithCompetency implements ValueFormatter
{
    const COMPETENCY_POOR = 1;
    const COMPETENCY_POOR_DISPLAY_STRING = 'Poor';

    const COMPETENCY_BASIC = 2;
    const COMPETENCY_BASIC_DISPLAY_STRING = 'Basic';

    const COMPETENCY_GOOD = 3;
    const COMPETENCY_GOOD_DISPLAY_STRING = 'Good';

    const COMPETENCY_MOTHER_TONGUE_DISPLAY_STRING = 'Mother Tongue';

    /**
     * @param $entityValue
     * @return mixed|string
     */
    public function getFormattedValue($entityValue)
    {
        switch ($entityValue) {
            case self::COMPETENCY_POOR:
                return self::COMPETENCY_POOR_DISPLAY_STRING;
            case self::COMPETENCY_BASIC:
                return self::COMPETENCY_BASIC_DISPLAY_STRING;
            case self::COMPETENCY_GOOD:
                return self::COMPETENCY_GOOD_DISPLAY_STRING;
            default:
                return self::COMPETENCY_MOTHER_TONGUE_DISPLAY_STRING;
        }
    }
}
