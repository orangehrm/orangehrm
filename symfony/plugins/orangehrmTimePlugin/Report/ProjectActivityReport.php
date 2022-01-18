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

namespace OrangeHRM\Time\Report;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Report\Filter\Filter;
use OrangeHRM\Core\Report\Filter\FilterDefinition;
use OrangeHRM\Core\Report\Header\Column;
use OrangeHRM\Core\Report\Header\Header;
use OrangeHRM\Core\Report\Header\HeaderDefinition;
use OrangeHRM\Core\Report\ReportData;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class ProjectActivityReport extends ProjectReport implements EndpointAwareReport
{
    use UserRoleManagerTrait;
    use TextHelperTrait;

    public const PARAMETER_EMPLOYEE_NAME = 'employeeName';

    /**
     * @inheritDoc
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams
    {
        // TODO: Implement prepareFilterParams() method.
    }

    /**
     * @inheritDoc
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection
    {
        // TODO: Implement getValidationRule() method.
    }

    /**
     * @inheritDoc
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void
    {
        if (!$this->getUserRoleManagerHelper()
            ->getEntityIndependentDataGroupPermissions('time_project_reports')->canRead()) {
            throw new ForbiddenException();
        }
    }

    /**
     * @return Header
     */
    public function getHeaderDefinition(): HeaderDefinition
    {
        return new Header(
            [
                (new Column(self::PARAMETER_EMPLOYEE_NAME))->setName('Employee Name')
                    ->setSize(ProjectReport::DEFAULT_COLUMN_SIZE),
                (new Column(ProjectReport::PARAMETER_TIME))->setName('Time (Hours)')
                    ->setCellProperties(['class' => ['col-alt' => true]])
                    ->setSize(ProjectReport::DEFAULT_COLUMN_SIZE),
            ]
        );
    }

    /**
     * @return Filter
     */
    public function getFilterDefinition(): FilterDefinition
    {
        return new Filter();
    }

    /**
     * @inheritDoc
     */
    public function getData(FilterParams $filterParams): ReportData
    {
        // TODO: Implement getData() method.
    }
}
