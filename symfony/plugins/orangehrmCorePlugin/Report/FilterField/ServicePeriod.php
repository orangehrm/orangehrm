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

use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\ORM\QueryBuilderWrapper;

class ServicePeriod extends FilterField
{
    use DateTimeHelperTrait;

    public const DAYS_PER_YEAR = 365;

    /**
     * @inheritDoc
     */
    public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        $expr = null;
        if ($this->getOperator() === Operator::LESS_THAN && !is_null($this->getX())) {
            $expr = $qb->expr()->lt('DATE_DIFF(:ServicePeriod_now, employee.joinedDate)', ':ServicePeriod_lt');
            $qb->setParameter('ServicePeriod_lt', $this->getX() * self::DAYS_PER_YEAR);
        } elseif ($this->getOperator() === Operator::GREATER_THAN && !is_null($this->getX())) {
            $expr = $qb->expr()->gt('DATE_DIFF(:ServicePeriod_now, employee.joinedDate)', ':ServicePeriod_gt');
            $qb->setParameter('ServicePeriod_gt', $this->getX() * self::DAYS_PER_YEAR);
        } elseif ($this->getOperator() === Operator::BETWEEN && !is_null($this->getX()) && !is_null($this->getY())) {
            $expr = $qb->expr()->between(
                'DATE_DIFF(:ServicePeriod_now, employee.joinedDate)',
                ':ServicePeriod_x',
                ':ServicePeriod_y'
            );
            $qb->setParameter('ServicePeriod_x', $this->getX() * self::DAYS_PER_YEAR)
                ->setParameter('ServicePeriod_y', $this->getY() * self::DAYS_PER_YEAR);
        }
        if (!is_null($expr)) {
            $qb->andWhere($expr)
                ->setParameter('ServicePeriod_now', $this->getDateTimeHelper()->getNow());
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
