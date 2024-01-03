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

use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Pim\Dao\ReportingMethodConfigurationDao;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Pim\Service\Model\ReportingMethodModel;

class ReportingMethodConfigurationService
{
    use NormalizerServiceTrait;

    /**
     * @var ReportingMethodConfigurationDao|null
     */
    private ?ReportingMethodConfigurationDao $reportingMethodDao = null;

    /**
     * @return ReportingMethodConfigurationDao
     */
    public function getReportingMethodDao(): ReportingMethodConfigurationDao
    {
        if (!($this->reportingMethodDao instanceof ReportingMethodConfigurationDao)) {
            $this->reportingMethodDao = new ReportingMethodConfigurationDao();
        }

        return $this->reportingMethodDao;
    }

    /**
     * @param ReportingMethodConfigurationDao $reportingMethodDao
     */
    public function setReportingMethodDao(ReportingMethodConfigurationDao $reportingMethodDao): void
    {
        $this->reportingMethodDao = $reportingMethodDao;
    }

    /**
     * @param ReportingMethod $reportingMethod
     * @return ReportingMethod
     */
    public function saveReportingMethod(ReportingMethod $reportingMethod): ReportingMethod
    {
        return $this->getReportingMethodDao()->saveReportingMethod($reportingMethod);
    }

    /**
     * @param int $id
     * @return ReportingMethod|null
     */
    public function getReportingMethodById(int $id): ?ReportingMethod
    {
        return $this->getReportingMethodDao()->getReportingMethodById($id);
    }

    /**
     * @param string $name
     * @return ReportingMethod|null
     */
    public function getReportingMethodByName(string $name): ?ReportingMethod
    {
        return $this->getReportingMethodDao()->getReportingMethodByName($name);
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return array
     */
    public function getReportingMethodList(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams): array
    {
        return $this->getReportingMethodDao()->getReportingMethodList($reportingMethodSearchFilterParams);
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return int
     */
    public function getReportingMethodCount(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams): int
    {
        return $this->getReportingMethodDao()->getReportingMethodCount($reportingMethodSearchFilterParams);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteReportingMethods(array $toDeleteIds): int
    {
        return $this->getReportingMethodDao()->deleteReportingMethods($toDeleteIds);
    }

    /**
     * @param string $reportingMethodName
     * @return bool
     */
    public function isExistingReportingMethodName(string $reportingMethodName): bool
    {
        return $this->getReportingMethodDao()->isExistingReportingMethodName($reportingMethodName);
    }

    /**
     * @return int[]
     */
    public function getReportingMethodIdsInUse(): array
    {
        return $this->getReportingMethodDao()->getReportingMethodIdsInUse();
    }

    /**
     * @param ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams
     * @return array
     */
    public function getReportingMethodArray(ReportingMethodSearchFilterParams $reportingMethodSearchFilterParams): array
    {
        $reportingMethods = $this->getReportingMethodDao()->getReportingMethodList($reportingMethodSearchFilterParams);
        return $this->getNormalizerService()->normalizeArray(ReportingMethodModel::class, $reportingMethods);
    }
}
