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

use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Admin\Service\EmploymentStatusService;
use OrangeHRM\Admin\Service\JobCategoryService;
use OrangeHRM\Admin\Service\JobTitleService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeJobController extends BaseViewEmployeeController
{
    use ConfigServiceTrait;
    use EmployeeServiceTrait;

    protected ?JobTitleService $jobTitleService = null;
    protected ?JobCategoryService $jobCategoryService = null;
    protected ?CompanyStructureService $companyStructureService = null;
    protected ?EmploymentStatusService $employmentStatusService = null;
    protected ?LocationService $locationService = null;

    /**
     * @return JobTitleService
     */
    protected function getJobTitleService(): JobTitleService
    {
        if (!$this->jobTitleService instanceof JobTitleService) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }

    /**
     * @return JobCategoryService
     */
    protected function getJobCategoryService(): JobCategoryService
    {
        if (!$this->jobCategoryService instanceof JobCategoryService) {
            $this->jobCategoryService = new JobCategoryService();
        }
        return $this->jobCategoryService;
    }

    /**
     * @return CompanyStructureService
     */
    protected function getCompanyStructureService(): CompanyStructureService
    {
        if (!$this->companyStructureService instanceof CompanyStructureService) {
            $this->companyStructureService = new CompanyStructureService();
        }
        return $this->companyStructureService;
    }

    /**
     * @return EmploymentStatusService
     */
    protected function getEmploymentStatusService(): EmploymentStatusService
    {
        if (!$this->employmentStatusService instanceof EmploymentStatusService) {
            $this->employmentStatusService = new EmploymentStatusService();
        }
        return $this->employmentStatusService;
    }

    /**
     * @return LocationService
     */
    protected function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    public function preRender(Request $request): void
    {
        $empNumber = $request->attributes->get('empNumber');
        if ($empNumber) {
            $component = new Component('employee-job');
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $empNumber));

            $jobTitles = $this->getJobTitleService()->getJobTitleArrayForEmployee($empNumber);
            $component->addProp(new Prop('job-titles', Prop::TYPE_ARRAY, $jobTitles));

            $jobCategories = $this->getJobCategoryService()->getJobCategoryArray();
            $component->addProp(new Prop('job-categories', Prop::TYPE_ARRAY, $jobCategories));

            $employmentStatuses = $this->getEmploymentStatusService()->getEmploymentStatusArray();
            $component->addProp(new Prop('employment-statuses', Prop::TYPE_ARRAY, $employmentStatuses));

            $subunits = $this->getCompanyStructureService()->getSubunitArray();
            $component->addProp(new Prop('subunits', Prop::TYPE_ARRAY, $subunits));

            $locations = $this->getLocationService()->getAccessibleLocationsArray($empNumber);
            $component->addProp(new Prop('locations', Prop::TYPE_ARRAY, $locations));

            $terminationReasons = $this->getEmployeeService()
                ->getEmployeeTerminationService()
                ->getTerminationReasonsArray();
            $component->addProp(new Prop('termination-reasons', Prop::TYPE_ARRAY, $terminationReasons));

            $this->setComponent($component);

            $this->setPermissionsForEmployee(['job_details', 'job_attachment', 'job_custom_fields'], $empNumber);
        } else {
            $this->handleBadRequest();
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDataGroupsForCapabilityCheck(): array
    {
        return ['job_details'];
    }
}
