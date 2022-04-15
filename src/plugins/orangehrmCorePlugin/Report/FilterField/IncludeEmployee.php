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

namespace OrangeHRM\Core\Report\FilterField;

use OrangeHRM\ORM\QueryBuilderWrapper;

class IncludeEmployee extends FilterField
{
    /**
     * @inheritDoc
     */
    public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        if ($this->getOperator() === Operator::IS_NULL) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($this->getOperator() === Operator::IS_NOT_NULL) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        } // else: current and past employees
    }

    /**
     * @inheritDoc
     */
    public function getEntityAliases(): array
    {
        return ['employee'];
    }
}
