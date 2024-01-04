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

namespace OrangeHRM\Core\Report;

use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Report\Filter\FilterDefinition;
use OrangeHRM\Core\Report\Header\HeaderDefinition;

interface Report
{
    /**
     * Get normalizable table header definition
     *
     * @return HeaderDefinition
     */
    public function getHeaderDefinition(): HeaderDefinition;

    /**
     * Get normalizable table filter definition
     *
     * @return FilterDefinition
     */
    public function getFilterDefinition(): FilterDefinition;

    /**
     * Get report resulting date for given filter params
     *
     * @param FilterParams $filterParams
     * @return ReportData
     */
    public function getData(FilterParams $filterParams): ReportData;
}
