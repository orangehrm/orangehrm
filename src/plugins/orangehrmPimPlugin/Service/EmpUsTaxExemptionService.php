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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Pim\Dao\EmpUsTaxExemptionDao;

class EmpUsTaxExemptionService
{
    /**
     * @var EmpUsTaxExemptionDao|null
     */
    private ?EmpUsTaxExemptionDao $empUsTaxExemptionDao = null;

    /**
     * @return EmpUsTaxExemptionDao|null
     */
    public function getEmpUsTaxExemptionDao(): EmpUsTaxExemptionDao
    {
        if (!($this->empUsTaxExemptionDao instanceof EmpUsTaxExemptionDao)) {
            $this->empUsTaxExemptionDao = new EmpUsTaxExemptionDao();
        }
        return $this->empUsTaxExemptionDao;
    }
}
