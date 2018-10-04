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
 * Class FormatWithAchievement
 */
class FormatWithAchievement implements ValueFormatter
{
    const ACHIEVEMENT_POSITIVE = 1;
    const ACHIEVEMENT_POSITIVE_DISPLAY_STRING = 'Positive';

    const ACHIEVEMENT_NEGATTIVE = 2;
    const ACHIEVEMENT_NEGATTIVE_DISPLAY_STRING = 'Negative';

    /**
     * @param $entityValue
     * @return mixed|string
     */
    public function getFormattedValue($entityValue)
    {
        switch ($entityValue) {
            case self::ACHIEVEMENT_POSITIVE:
                return self::ACHIEVEMENT_POSITIVE_DISPLAY_STRING;
            case self::ACHIEVEMENT_NEGATTIVE:
                return self::ACHIEVEMENT_NEGATTIVE_DISPLAY_STRING;
        }
    }
}
