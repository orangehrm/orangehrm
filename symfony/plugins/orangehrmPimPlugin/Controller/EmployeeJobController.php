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


use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Admin\Service\JobCategoryService;
use OrangeHRM\Admin\Service\JobTitleService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;

class EmployeeJobController extends BaseViewEmployeeController
{
    use ConfigServiceTrait;

    protected ?JobTitleService $jobTitleService = null;
    protected ?JobCategoryService $jobCategoryService = null;
    protected ?CompanyStructureService $companyStructureService = null;
    protected ?EmploymentStatusService $employmentStatusService = null;
    protected ?EmploymentStatusSearchFilterParams $employmentStatusSearchFilterParams = null;

    protected function getJobTitleService()
    {
        if (!$this->jobTitleService instanceof JobTitleService) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }

    protected function getJobCategoryService()
    {
        if (!$this->jobCategoryService instanceof JobCategoryService) {
            $this->jobCategoryService = new JobCategoryService();
        }
        return $this->jobCategoryService;
    }

    protected function getCompanyStructureService()
    {
        if (!$this->companyStructureService instanceof CompanyStructureService) {
            $this->companyStructureService = new CompanyStructureService();
        }
        return $this->companyStructureService;
    }


    public function preRender(Request $request): void
    {
        $empNumber = $request->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-job');
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));

            /** @var JobTitleService $jobTitleService */
            $jobTitles = $this->getJobTitleService()->getJobTitleArray();
            $component->addProp(new Prop('job-titles', Prop::TYPE_ARRAY, $jobTitles));
            $this->setComponent($component);

            /** @var JobCategoryService $jobCategoryService */
            $jobCategories = $this->getJobCategoryService()->getJobCategoryArray();
            $component->addProp(new Prop('job-categories', Prop::TYPE_ARRAY, $jobCategories));
            $this->setComponent($component);

            /** @var CompanyStructureService $companyStructureService */
            $subunits = $this->getCompanyStructureService()->getSubunitArray();
            $component->addProp(new Prop('subunits', Prop::TYPE_ARRAY, $subunits));
            $this->setComponent($component);

            /** @var CountryService $countryService */
            $countryService = $this->getContainer()->get(Services::COUNTRY_SERVICE);
            $component->addProp(new Prop('locations', Prop::TYPE_ARRAY, $countryService->getCountryArray()));
            $this->setComponent($component);
        } else {
            $this->handleBadRequest();
        }
    }
}
