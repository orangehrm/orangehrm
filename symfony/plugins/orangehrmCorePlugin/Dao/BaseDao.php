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

namespace OrangeHRM\Core\Dao;

use Doctrine\ORM\QueryBuilder;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

abstract class BaseDao
{
    use EntityManagerHelperTrait;

    /**
     * @param QueryBuilder $qb
     * @return int
     */
    protected function count(QueryBuilder $qb): int
    {
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param QueryBuilder $qb
     * @param FilterParams $filterParams
     * @return QueryBuilder
     */
    protected function setSortingParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder
    {
        if (!is_null($filterParams->getSortField())) {
            $qb->addOrderBy(
                $filterParams->getSortField(),
                $filterParams->getSortOrder()
            );
        }
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param FilterParams $filterParams
     * @return QueryBuilder
     */
    protected function setPaginationParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder
    {
        // If limit = 0, will not paginate
        if (!empty($filterParams->getLimit())) {
            $qb->setFirstResult($filterParams->getOffset())
                ->setMaxResults($filterParams->getLimit());
        }
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param FilterParams $filterParams
     * @return QueryBuilder
     */
    protected function setSortingAndPaginationParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder
    {
        $this->setSortingParams($qb, $filterParams);
        $this->setPaginationParams($qb, $filterParams);
        return $qb;
    }
}
