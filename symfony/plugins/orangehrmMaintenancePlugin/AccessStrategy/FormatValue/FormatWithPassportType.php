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
 * Class FormatWithPassportType
 */
class FormatWithPassportType implements ValueFormatter
{
    public const PASSPORT = 1;
    public const PASSPORT_DISPLAY_STRING = 'Passport';

    public const VISA_DISPLAY_STRING = 'Visa';

    /**
     * @param $entityValue
     * @return string
     */
    public function getFormattedValue($entityValue): string
    {
        switch ($entityValue) {
            case self::PASSPORT:
                return self::PASSPORT_DISPLAY_STRING;
            default:
                return self::VISA_DISPLAY_STRING;
        }
    }
}
