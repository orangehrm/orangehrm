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

namespace OrangeHRM\Core\Report\FilterField;

use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\ORM\QueryBuilderWrapper;

class JoinedDate extends FilterField
{
    use DateTimeHelperTrait;

    /**
     * @inheritDoc
     */
    public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        $expr = null;
        if ($this->getOperator() === Operator::LESS_THAN && !is_null($this->getX())) {
            $expr = $qb->expr()->lt('employee.joinedDate', ':JoinedDate_lt');
            $qb->setParameter('JoinedDate_lt', $this->getX());
        } elseif ($this->getOperator() === Operator::GREATER_THAN && !is_null($this->getX())) {
            $expr = $qb->expr()->gt('employee.joinedDate', ':JoinedDate_gt');
            $qb->setParameter('JoinedDate_gt', $this->getX());
        } elseif ($this->getOperator() === Operator::BETWEEN && !is_null($this->getX()) && !is_null($this->getY())) {
            $expr = $qb->expr()->between('employee.joinedDate', ':JoinedDate_x', ':JoinedDate_y');
            $qb->setParameter('JoinedDate_x', $this->getX())
                ->setParameter('JoinedDate_y', $this->getY());
        }
        if (!is_null($expr)) {
            $qb->andWhere($expr);
        }
    }

    /**
     * @inheritDoc
     */
    public function getEntityAliases(): array
    {
        return ['employee'];
    }
}
