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

use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class EmployeePersonalDetailController extends BaseViewEmployeeController
{
    use ConfigServiceTrait;

    /**
     * @var NationalityService|null
     */
    protected ?NationalityService $nationalityService = null;

    /**
     * @return NationalityService
     */
    protected function getNationalityService(): NationalityService
    {
        if (!$this->nationalityService instanceof NationalityService) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $empNumber = $request->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-personal-details');
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));

            $showDeprecatedFields = $this->getConfigService()->showPimDeprecatedFields();
            $showSsn = $this->getConfigService()->showPimSSN();
            $showSin = $this->getConfigService()->showPimSIN();
            $component->addProp(new Prop('show-deprecated-fields', Prop::TYPE_BOOLEAN, $showDeprecatedFields));
            $component->addProp(new Prop('show-ssn-field', Prop::TYPE_BOOLEAN, $showSsn));
            $component->addProp(new Prop('show-sin-field', Prop::TYPE_BOOLEAN, $showSin));

            $nationalities = $this->getNationalityService()->getNationalityArray();
            $component->addProp(new Prop('nationalities', Prop::TYPE_ARRAY, $nationalities));
            $this->setComponent($component);

            $this->setPermissionsForEmployee(
                [
                    'personal_information',
                    'personal_attachment',
                    'personal_custom_fields',
                    'personal_sensitive_information'
                ],
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
        return ['personal_information'];
    }
}
