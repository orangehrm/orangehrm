<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be use
 */

namespace OrangeHRM\Maintenance\AccessStrategy\FormatValue;

use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;

/**
 * Class FormatWithSmoker
 */
class FormatWithSmoker implements ValueFormatter
{
    public const SMOKER_ID = 1;
    public const SMOKER_ID_DISPLAY_STRING = 'Smoker';

    /**
     * @param $entityValue
     * @return null|string
     */
    public function getFormattedValue($entityValue): ?string
    {
        switch ($entityValue) {
            case self::SMOKER_ID:
                return self::SMOKER_ID_DISPLAY_STRING;
            default:
                return null;
        }
    }
}
