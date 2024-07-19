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

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Pim\Dto\ReportingMethodSearchFilterParams;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;

class EmployeeReportToController extends BaseViewEmployeeController
{
    use I18NHelperTrait;

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
        $empNumber = $request->attributes->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-report-to');
            $reportingMethodParamHolder = new ReportingMethodSearchFilterParams();
            $reportingMethods = $this->getReportingMethodConfigurationService()->getReportingMethodArray(
                $reportingMethodParamHolder
            );
            $reportingMethods = array_map(function ($reportingMethod) {
                return [
                    'id' => $reportingMethod['id'],
                    'label' => $this->getI18NHelper()->transBySource($reportingMethod['label'])
                ];
            }, $reportingMethods);
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
