<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Maintenance\AccessStrategy\FormatValue;

use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;

/**
 * Class FormatWithAchievement
 */
class FormatWithAchievement implements ValueFormatter
{
    public const ACHIEVEMENT_POSITIVE = 1;
    public const ACHIEVEMENT_POSITIVE_DISPLAY_STRING = 'Positive';

    public const ACHIEVEMENT_NEGATTIVE = 2;
    public const ACHIEVEMENT_NEGATTIVE_DISPLAY_STRING = 'Negative';

    /**
     * @param $entityValue
     * @return string|null
     */
    public function getFormattedValue($entityValue): ?string
    {
        switch ($entityValue) {
            case self::ACHIEVEMENT_POSITIVE:
                return self::ACHIEVEMENT_POSITIVE_DISPLAY_STRING;
            case self::ACHIEVEMENT_NEGATTIVE:
                return self::ACHIEVEMENT_NEGATTIVE_DISPLAY_STRING;
            default:
                return null;

        }
    }
}
