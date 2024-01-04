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

namespace OrangeHRM\Performance\Service;

use Exception;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Kpi;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Dao\KpiDao;
use OrangeHRM\Performance\Exception\KpiServiceException;

class KpiService
{
    use EntityManagerHelperTrait;

    private ?KpiDao $kpiDao = null;

    /**
     * @return KpiDao
     */
    public function getKpiDao(): KpiDao
    {
        if (!($this->kpiDao instanceof KpiDao)) {
            $this->kpiDao = new KpiDao();
        }
        return $this->kpiDao;
    }

    /**
     * @param Kpi $kpi
     * @return Kpi
     * @throws KpiServiceException|TransactionException
     */
    public function saveKpi(Kpi $kpi): Kpi
    {
        if ($kpi->getMinRating() >= $kpi->getMaxRating()) {
            throw KpiServiceException::minGreaterThanMax();
        }
        $this->beginTransaction();
        try {
            $kpi = $this->getKpiDao()->saveKpi($kpi);
            if ($kpi->isDefaultKpi()) {
                $this->getKpiDao()->unsetDefaultKpi($kpi->getId());
            }
            $this->commitTransaction();
            return $kpi;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }
}
