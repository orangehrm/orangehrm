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

namespace OrangeHRM\Attendance\Controller;

use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Core\Controller\AbstractVueController;

class AttendanceSummaryReportController extends AbstractVueController
{
    protected ?CompanyStructureService $companyStructureService = null;

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
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('attendance-summary-report');

        $subunits = $this->getCompanyStructureService()->getSubunitArray();
        $component->addProp(new Prop('subunits', Prop::TYPE_ARRAY, $subunits));

        $this->setComponent($component);
    }
}
