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

namespace OrangeHRM\Time\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Customer;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;

class CustomerDao extends BaseDao
{
    /**
     * Get Customer for given customer Id (Active only)
     * @param int $customerId
     * @return Customer|null
     */
    public function getCustomerById(int $customerId): ?Customer
    {
        $customer = $this->getRepository(Customer::class)->findOneBy(['id' => $customerId, 'deleted' => false]);
        if ($customer instanceof Customer) {
            return $customer;
        }
        return null;
    }

    /**
     * @param int[] $deletedIds
     * @return int
     */
    public function deleteCustomer(array $deletedIds): int
    {
        $q = $this->createQueryBuilder(Customer::class, 'cus');
        $q->update()
            ->set('cus.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('cus.id', ':ids'))
            ->setParameter('ids', $deletedIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param Customer $customer
     * @return Customer
     */
    public function saveCustomer(Customer $customer): Customer
    {
        $this->persist($customer);
        return $customer;
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return Customer[]
     */
    public function searchCustomers(CustomerSearchFilterParams $customerSearchFilterParams): array
    {
        $paginator = $this->getSearchCustomerPaginator($customerSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return Paginator
     */
    private function getSearchCustomerPaginator(CustomerSearchFilterParams $customerSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(Customer::class, 'customer');
        $this->setSortingAndPaginationParams($q, $customerSearchFilterParams);

        if (!empty($customerSearchFilterParams->getName())) {
            $q->andWhere($q->expr()->like('customer.name', ':customerName'))
                ->setParameter('customerName', '%' . $customerSearchFilterParams->getName() . '%');
        }
        if (!is_null($customerSearchFilterParams->getCustomerIds())) {
            $q->andWhere($q->expr()->in('customer.id', ':customerIds'))
                ->setParameter('customerIds', $customerSearchFilterParams->getCustomerIds());
        }

        $q->andWhere('customer.deleted = :deleted');
        $q->setParameter('deleted', false);

        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return int
     */
    public function getSearchCustomersCount(CustomerSearchFilterParams $customerSearchFilterParams): int
    {
        $paginator = $this->getSearchCustomerPaginator($customerSearchFilterParams);
        return $paginator->count();
    }

    /**
     **this function for validating the customer name availability. ( true -> customer name already exist, false - customer name is not exist )
     * @param string $customerName
     * @param int|null $customerId
     * @return bool
     */
    public function isCustomerNameTaken(string $customerName, ?int $customerId = null): bool
    {
        $q = $this->createQueryBuilder(Customer::class, 'customer');
        $q->andWhere('customer.name = :customerName');
        $q->setParameter('customerName', $customerName);
        //if the customer is deleted, the name is not counted as a duplicated name
        $q->andWhere('customer.deleted = :deleted');
        $q->setParameter('deleted', false);
        if (!is_null($customerId)) {
            $q->andWhere(
                'customer.id != :customerId'
            ); // we need to skip the current customer on update, otherwise count always return 1
            $q->setParameter('customerId', $customerId);
        }
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * Get Customer for given customer Id (Active/Deleted)
     * @param int $customerId
     * @return Customer|null
     */
    public function getCustomer(int $customerId): ?Customer
    {
        $customer = $this->getRepository(Customer::class)->find($customerId);
        if ($customer instanceof Customer) {
            return $customer;
        }
        return null;
    }

    /**
     * @param bool $includeDeleted
     * @return int[]
     */
    public function getCustomerIdList(bool $includeDeleted = false): array
    {
        $q = $this->createQueryBuilder(Customer::class, 'customer')
            ->select('customer.id');
        if (!$includeDeleted) {
            $q->andWhere('customer.deleted = :deleted')
                ->setParameter('deleted', false);
        }
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $projectAdminEmpNumber
     * @param bool $includeDeleted
     * @return int[]
     */
    public function getCustomerIdListForProjectAdmin(int $projectAdminEmpNumber, bool $includeDeleted = false): array
    {
        $q = $this->createQueryBuilder(Customer::class, 'customer')
            ->select('customer.id')
            ->distinct()
            ->leftJoin('customer.projects', 'project')
            ->innerJoin('project.projectAdmins', 'projectAdmin')
            ->andWhere('projectAdmin.empNumber = :projectAdminEmpNumber')
            ->setParameter('projectAdminEmpNumber', $projectAdminEmpNumber);
        if (!$includeDeleted) {
            $q->andWhere('customer.deleted = :deleted')
                ->setParameter('deleted', false);
        }
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }
}
