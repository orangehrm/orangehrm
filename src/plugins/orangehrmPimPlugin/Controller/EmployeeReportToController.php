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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;

class EmployeeReportToController extends BaseViewEmployeeController
{
    /**
     * @var ReportingMethodConfigurationService|null
     */
    protected ?ReportingMethodConfigurationService $reportingMethodService = null;

    /**
     * @return ReportingMethodConfigurationService
     */
    public function getReportingMethodConfigurationService(): ReportingMethodConfigurationService
    {
        if (!$this->reportingMethodService instanceof ReportingMethodConfigurationService) {
            $this->reportingMethodService = new ReportingMethodConfigurationService();
        }
        return $this->reportingMethodService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $empNumber = $request->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-report-to');
            $reportingMethodParamHolder = new ReportingMethodSearchFilterParams();
            $reportingMethods = $this->getReportingMethodConfigurationService()->getReportingMethodArray(
                $reportingMethodParamHolder
            );
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));
            $component->addProp(new Prop('reporting-methods', Prop::TYPE_ARRAY, $reportingMethods));
            $this->setComponent($component);
            $this->setPermissionsForEmployee(
                ['supervisor', 'subordinates', 'report-to_attachment', 'report-to_custom_fields'],
                $empNumber
            );
        } else {
            $this->handleBadRequest();
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDataGroupsForCapabilityCheck(): array
    {
        return ['supervisor', 'subordinates'];
    }
}
