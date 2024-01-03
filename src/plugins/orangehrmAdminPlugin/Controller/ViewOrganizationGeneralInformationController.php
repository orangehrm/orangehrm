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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class ViewOrganizationGeneralInformationController extends AbstractVueController
{
    use EmployeeServiceTrait;
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $noOfEmployees = $this->getEmployeeService()->getNumberOfEmployees();
        /** @var CountryService $countryService */
        $countryService = $this->getContainer()->get(Services::COUNTRY_SERVICE);
        $countryList = $countryService->getCountryArray();
        $component = new Component('organization-general-information-view');
        $component->addProp(new Prop('number-of-employees', Prop::TYPE_NUMBER, $noOfEmployees));
        $component->addProp(new Prop('country-list', Prop::TYPE_ARRAY, $countryList));
        $this->setComponent($component);
    }
}
