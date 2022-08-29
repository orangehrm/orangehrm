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

namespace OrangeHRM\Dashboard\Dao;

use OrangeHRM\Entity\Subunit;
use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Dto\Traits\SubunitIdChainTrait;

class ChartDao extends BaseDao
{
    use SubunitIdChainTrait;

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
     * @param EmployeeSearchFilterParams $employeeSearchFilterParams
     * @return array
     */
    public function getEmployeeDistributionBySubunit(EmployeeSearchFilterParams $employeeSearchFilterParams): array
    {
        $q = $this->createQueryBuilder(Subunit::class, 'subunit');
        $q->andWhere('subunit.level = :level');
        $q->setParameter('level', 1);

        $subunits = $q->getQuery()->execute();

        $employeeCount = [];
        foreach ($subunits as $subunit){
            $result = [];
            $count = 0;

            $subunitChains = $this->getCompanyStructureService()->getSubunitChainById($subunit->getId());
            $result['subunit'] = $subunit->getName();

            foreach ($subunitChains as $subunitChain){
                $employeeSearchFilterParams->setSubunitId(4);
                $count = $count + $this->getEmployeeCount($employeeSearchFilterParams);
            }
            $result['count'] = $count;
            $employeeCount[] = $result;
        }
        return $employeeCount;
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchFilterParams
     *
     * @return int
     */
    public function getEmployeeCount(EmployeeSearchFilterParams $employeeSearchFilterParams): int
    {
        $qb = $this->getEmployeeDistributionQueryBuilderWrapper(
            $employeeSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchFilterParams
     * @return QueryBuilderWrapper
     */
    public function getEmployeeDistributionQueryBuilderWrapper(EmployeeSearchFilterParams $employeeSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.subDivision', 'subunit');

        if (!is_null($employeeSearchFilterParams->getSubunitId())) {
            $q->andWhere($q->expr()->in('subunit.id', ':subunitId'))
                ->setParameter('subunitId', $employeeSearchFilterParams->getSubunitIdChain());
        }
        return $this->getQueryBuilderWrapper($q);
    }
}
