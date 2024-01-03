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

namespace OrangeHRM\CorporateDirectory\Controller;

use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Admin\Service\JobTitleService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class CorporateDirectoryModuleController extends AbstractVueController
{
    protected ?JobTitleService $jobTitleService = null;
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
     * @return LocationService
     */
    protected function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('corporate-directory-employee-list');

        $jobTitles = $this->getJobTitleService()->getJobTitleArray();
        $component->addProp(new Prop('job-titles', Prop::TYPE_ARRAY, $jobTitles));

        $locations = $this->getLocationService()->getLocationsArray();
        $component->addProp(new Prop('locations', Prop::TYPE_ARRAY, $locations));

        $this->setComponent($component);
    }
}
