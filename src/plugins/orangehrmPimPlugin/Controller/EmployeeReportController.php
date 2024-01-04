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

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Service\ReportGeneratorService;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class EmployeeReportController extends AbstractVueController
{
    /**
     * @var ReportGeneratorService|null
     */
    protected ?ReportGeneratorService $reportGeneratorService = null;

    /**
     * @return ReportGeneratorService
     */
    protected function getReportGeneratorService(): ReportGeneratorService
    {
        if (!$this->reportGeneratorService instanceof ReportGeneratorService) {
            $this->reportGeneratorService = new ReportGeneratorService();
        }
        return $this->reportGeneratorService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $id = $request->attributes->getInt('id');
            $reportName = $this->getReportGeneratorService()->getReportGeneratorDao()->getReportById($id)->getName();
            $component = new Component('employee-report-view');
            $component->addProp(new Prop('report-id', Prop::TYPE_NUMBER, $id));
            $component->addProp(new Prop('report-name', Prop::TYPE_STRING, $reportName));
        } else {
            $component = new Component('employee-report-list');
        }
        $this->setComponent($component);
    }
}
