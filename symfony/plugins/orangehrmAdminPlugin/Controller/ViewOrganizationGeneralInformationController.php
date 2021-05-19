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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Admin\Service\CountryService;

class ViewOrganizationGeneralInformationController extends AbstractVueController
{
    /**
     * @var EmployeeService|null
     */
    protected ?EmployeeService $employeeService = null;

    /**
     * @var CountryService|null
     */
    protected ?CountryService $countryService = null;

    public function init(): void
    {
        $noOfEmployees = $this->getEmployeeService()->getNumberOfEmployees();
        $countryList = $this->getCountryService()->getCountryCodeAndNameFromList();
        $component = new Component('organization-general-information-view');
        $component->addProp(new Prop('number-of-employees', Prop::TYPE_NUMBER, $noOfEmployees));
        $component->addProp(new Prop('country-list', Prop::TYPE_ARRAY, $countryList));
        $this->setComponent($component);
    }

    /**
     * @return EmployeeService|null
     */
    public function getEmployeeService(): ?EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService|null $employeeService
     */
    public function setEmployeeService(?EmployeeService $employeeService): void
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return CountryService|null
     */
    public function getCountryService(): ?CountryService
    {
        if (is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }
}
