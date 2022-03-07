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

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;

/**
 * Class FormatWithPayGradeId
 */
class FormatWithPayPeriodId implements ValueFormatter
{
    use EntityManagerHelperTrait;
    public const NO_PAY_GRADE = 'No Pay Grade Details';

    /**
     * @param $entityValue
     * @return string
     */
    public function getFormattedValue($entityValue): string
    {
        if ($entityValue->getName()) {
            return $entityValue->getName();
        } else {
            return self::NO_PAY_GRADE;
        }
    }
}
