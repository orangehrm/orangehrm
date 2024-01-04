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

namespace OrangeHRM\Core\Report\Api;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Report;

interface EndpointAwareReport extends Report
{
    /**
     * Prepare specific FilterParams object from request params
     *
     * @param EndpointProxy $endpoint
     * @return FilterParams
     */
    public function prepareFilterParams(EndpointProxy $endpoint): FilterParams;

    /**
     * Get param validation rule collection to validate filter parameters
     *
     * @param EndpointProxy $endpoint
     * @return ParamRuleCollection
     */
    public function getValidationRule(EndpointProxy $endpoint): ParamRuleCollection;

    /**
     * Evaluate report permission and throw forbidden exception if not accessible
     *
     * @param EndpointProxy $endpoint
     * @return void
     * @throws ForbiddenException
     */
    public function checkReportAccessibility(EndpointProxy $endpoint): void;
}
