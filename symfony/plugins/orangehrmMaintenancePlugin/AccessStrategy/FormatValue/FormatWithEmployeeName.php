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
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

/**
 * Class FormatWithEmployeeName
 */
class FormatWithEmployeeName implements ValueFormatter
{
    use EmployeeServiceTrait;

    /**
     * @param $entityValue
     * @return null|String
     */
    public function getFormattedValue($entityValue): ?string
    {
        return $this->getEmployeeService()->getEmployeeByEmpNumber($entityValue)->getDecorator()->getFullName();
    }
}
