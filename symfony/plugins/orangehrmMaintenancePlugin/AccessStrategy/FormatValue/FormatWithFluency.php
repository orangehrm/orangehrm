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
 * Class FormatGender
 */
class FormatWithFluency implements ValueFormatter
{
    public const FLUENCY_WRITING = 1;
    public const FLUENCY_WRITING_DISPLAY_STRING = 'Writing';

    public const FLUENCY_SPEAKING = 2;
    public const FLUENCY_SPEAKING_DISPLAY_STRING = 'Speaking';

    public const FLUENCY_READING_DISPLAY_STRING = 'Reading';

    /**
     * @param $entityValue
     * @return string
     */
    public function getFormattedValue($entityValue): string
    {
        switch ($entityValue) {
            case self::FLUENCY_WRITING:
                return self::FLUENCY_WRITING_DISPLAY_STRING;
            case self::FLUENCY_SPEAKING:
                return self::FLUENCY_SPEAKING_DISPLAY_STRING;
            default:
                return self::FLUENCY_READING_DISPLAY_STRING;
        }
    }
}
