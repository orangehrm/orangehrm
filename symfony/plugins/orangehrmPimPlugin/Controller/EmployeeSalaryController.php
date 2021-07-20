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

use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;

class EmployeeSalaryController extends BaseViewEmployeeController
{
    use ServiceContainerTrait;

    /**
     * @return PayGradeService
     */
    public function getPayGradeService(): PayGradeService
    {
        return $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
    }

    public function preRender(Request $request): void
    {
        $empNumber = $request->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-salary');

            $currencies = $this->getPayGradeService()->getCurrencyArray();
            $paygrades = $this->getPayGradeService()->getPayGradeArray();
            $payFrequencies = $this->getPayGradeService()->getPayPeriodArray();
            $accountTypes = [
                ["id" => 'SAVINGS', "label" => "Savings"],
                ["id" => 'CHECKING', "label" => "Checking"],
                ["id" => 'OTHER', "label" => "Other"]
            ];
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));
            $component->addProp(new Prop('currencies', Prop::TYPE_ARRAY, $currencies));
            $component->addProp(new Prop('paygrades', Prop::TYPE_ARRAY, $paygrades));
            $component->addProp(new Prop('pay-frequencies', Prop::TYPE_ARRAY, $payFrequencies));
            $component->addProp(new Prop('account-types', Prop::TYPE_ARRAY, $accountTypes));

            $this->setComponent($component);

            $this->setPermissionsForEmployee(
                ['salary_details', 'salary_attachment', 'salary_custom_fields'],
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
        return ['salary_details'];
    }
}
