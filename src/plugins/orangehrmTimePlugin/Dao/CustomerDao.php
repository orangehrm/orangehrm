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

namespace OrangeHRM\Time\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Time\Exception\CustomerServiceException;

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
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingCustomerIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Customer::class, 'customer');

        $qb->select('customer.id')
            ->andWhere($qb->expr()->in('customer.id', ':ids'))
            ->andWhere($qb->expr()->eq('customer.deleted', ':deleted'))
            ->setParameter('ids', $ids)
            ->setParameter('deleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int[] $deletedIds
     * @return int
     * @throws TransactionException|CustomerServiceException
     */
    public function deleteCustomer(array $deletedIds): int
    {
        foreach ($deletedIds as $toBeDeletedCustomerId) {
            if ($this->hasCustomerGotTimesheetItems($toBeDeletedCustomerId)) {
                throw CustomerServiceException::CustomerExist();
            }
        }

        $this->beginTransaction();
        try {
            $q = $this->createQueryBuilder(Customer::class, 'cus');
            $q->update()
                ->set('cus.deleted', ':deleted')
                ->setParameter('deleted', true)
                ->where($q->expr()->in('cus.id', ':ids'))
                ->setParameter('ids', $deletedIds);
            $status =  $q->getQuery()->execute();
            $this->deleteRelativeProjectsForCustomer($deletedIds);
            $this->commitTransaction();
            return $status;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
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

    /**
     * @param int $customerId
     * @return bool
     */
    public function hasCustomerGotTimesheetItems(int $customerId): bool
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $q->leftJoin('timesheetItem.project', 'project');
        $q->leftJoin('project.customer', 'customer');
        $q->andWhere('customer.id = :customerId');
        $q->setParameter('customerId', $customerId);
        $count = $this->getPaginator($q)->count();
        return ($count > 0);
    }

    /**
     * @return int[]
     */
    public function getUnselectableCustomerIds(): array
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $q->leftJoin('timesheetItem.project', 'project');
        $q->leftJoin('project.customer', 'customer');
        $q->select('customer.id');
        $q->groupBy('customer.id');
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int[] $customerIds
     * @return void
     */
    public function deleteRelativeProjectsForCustomer(array $customerIds): void
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->andWhere($q->expr()->in('project.customer', ':customerIds'));
        $q->setParameter('customerIds', $customerIds);
        $q->andWhere('project.deleted = :deleted');
        $q->setParameter('deleted', false);

        /* @var Project[] $projects */
        $projects = $q->getQuery()->execute();
        $projectIds = [];
        foreach ($projects as $project) {
            $projectIds[] = $project->getId();
        }
        // Delete the records that belong to the customer
        $this->deleteProjects($projectIds);
        $this->deleteRelativeProjectActivitiesForProject($projectIds);
        $this->deleteRelativeProjectAdminsForProject($projectIds);
    }

    /**
     * @param int[] $toBeDeletedProjectIds
     * @return void
     */
    private function deleteProjects(array $toBeDeletedProjectIds): void
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->update()
            ->set('project.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('project.id', ':ids'))
            ->setParameter('ids', $toBeDeletedProjectIds);
        $q->getQuery()->execute();
    }

    /**
     * @param int[] $projectIds
     * @return void
     */
    private function deleteRelativeProjectActivitiesForProject(array $projectIds): void
    {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->update()
            ->set('projectActivity.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('projectActivity.project', ':projectIds'))
            ->setParameter('projectIds', $projectIds);
        $q->getQuery()->execute();
    }

    /**
     * @param array $projectIds
     * @return void
     */
    private function deleteRelativeProjectAdminsForProject(array $projectIds)
    {
        $q = $this->createQueryBuilder(ProjectAdmin::class, 'projectAdmin');
        $q->delete()
            ->where($q->expr()->in('projectAdmin.project', ':projectIds'))
            ->setParameter('projectIds', $projectIds)
            ->getQuery()
            ->execute();
    }
}
