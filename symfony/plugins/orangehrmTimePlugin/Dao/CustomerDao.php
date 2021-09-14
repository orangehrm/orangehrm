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

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;

class CustomerDao extends BaseDao
{

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return Customer[]
     */
    public function getCustomerList(CustomerSearchFilterParams $customerSearchFilterParams): array
    {
        return $this->getCustomerListPaginator($customerSearchFilterParams)
            ->getQuery()
            ->execute();
    }

    /**
     * @param type $activeOnly
     * @return type
     */
    public function getCustomerCount($activeOnly = true)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Customer');

            if ($activeOnly == true) {
                $q->addWhere('is_deleted = ?', 0);
            }
            $count = $q->execute()->count();
            $q = Doctrine::getEntityManager()->getRepository(Customer::class)->createQueryBuilder('customer');

            if ($activeOnly == true) {
                $q->andWhere('customer.is_deleted = :isDeleted');
                $q->setParameter('isDeleted', '0');
            }
            return $count;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get System Customer for given User Id
     * @param int $customerId
     * @return Customer|null
     * @throws DaoException
     */
    public function getCustomerById($customerId): ?Customer
    {
        try {
            $customer = $this->getRepository(Customer::class)->find($customerId);
            if ($customer instanceof Customer) {
                return $customer;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Customer by name
     * @param $customerId
     * @return mixed
     * @throws DaoException
     */
    public function getCustomerByName($customerName)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Customer')
                ->where('name = ?', $customerName)
                ->andWhere('is_deleted = ?', 0);
            return $q->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *  Delete Customers
     * @param array $deletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteCustomer($deletedIds): int
    {
        try {
            $q = $this->createQueryBuilder(Customer::class, 'cus');
            $q->update()
                ->set('cus.deleted', ':deleted')
                ->setParameter('deleted', true)
                ->where($q->expr()->in('cus.id', ':ids'))
                ->setParameter('ids', $deletedIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function undeleteCustomer($customerId)
    {
        //TODO
        try {
            $customer = Doctrine:: getTable('Customer')->find($customerId);
            $customer->setIsDeleted(Customer::ACTIVE);
            $customer->save();
            $this->_undeleteRelativeProjectsForCustomer($customerId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _deleteRelativeProjectsForCustomer($customerId)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Project')
                ->where('is_deleted = ?', Project::ACTIVE_PROJECT)
                ->andWhere('customer_id = ?', $customerId);
            $projects = $q->execute();

            foreach ($projects as $project) {
                $project->setIsDeleted(Project::DELETED_PROJECT);
                $project->save();
                $this->_deleteRelativeProjectActivitiesForProject($project->getProjectId());
                $this->_deleteRelativeProjectAdminsForProject($project->getProjectId());
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _undeleteRelativeProjectsForCustomer($customerId)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Project')
                ->where('is_deleted = ?', Project::DELETED_PROJECT)
                ->andWhere('customer_id = ?', $customerId);
            $projects = $q->execute();

            foreach ($projects as $project) {
                $project->setIsDeleted(Project::ACTIVE_PROJECT);
                $project->save();
                $this->_undeleteRelativeProjectActivitiesForProject($project->getProjectId());
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _deleteRelativeProjectActivitiesForProject($projectId)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('ProjectActivity')
                ->where('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY)
                ->andWhere('project_id = ?', $projectId);
            $projectActivities = $q->execute();

            foreach ($projectActivities as $projectActivity) {
                $projectActivity->setIsDeleted(ProjectActivity::DELETED_PROJECT_ACTIVITY);
                $projectActivity->save();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _undeleteRelativeProjectActivitiesForProject($projectId)
    {
            // TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('ProjectActivity')
                ->where('is_deleted = ?', ProjectActivity::DELETED_PROJECT_ACTIVITY)
                ->andWhere('project_id = ?', $projectId);
            $projectActivities = $q->execute();

            foreach ($projectActivities as $projectActivity) {
                $projectActivity->setIsDeleted(ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
                $projectActivity->save();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    private function _deleteRelativeProjectAdminsForProject($projectId)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->delete('ProjectAdmin pa')
                ->where('pa.project_id = ?', $projectId);
            $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param type $activeOnly
     * @return type
     */
    public function getAllCustomers($activeOnly = true)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Customer');
            if ($activeOnly == true) {
                $q->where('is_deleted =?', 0);
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Return an array of Customer Names
     *
     * @param Array $customerIdList List of Customer Ids
     * @param Boolean $excludeDeletedCustomers Exclude deleted Customers or not
     * @return Array of Customer Names
     * @version 2.7.1
     */
    public function getCustomerNameList($customerIdList, $excludeDeletedCustomers = true)
    {
        //TODO
        try {
            if (!empty($customerIdList)) {

                $customerIdString = implode(',', $customerIdList);

                $q = "SELECT c.customer_id AS customerId, c.name
                		FROM ohrm_customer AS c
                		WHERE c.customer_id IN ({$customerIdString})";

                if ($excludeDeletedCustomers) {
                    $q .= ' AND c.is_deleted = 0';
                }

                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }

            return $results;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     *
     * @param type $customerId
     * @return type
     */
    public function hasCustomerGotTimesheetItems($customerId)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->select("COUNT(*)")
                ->from('TimesheetItem ti')
                ->leftJoin('ti.Project p')
                ->leftJoin('p.Customer c')
                ->where('c.customerId = ?', $customerId);
            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return ($count > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Customer by Id ( active )
     * @param $customerId
     * @return mixed
     * @throws DaoException
     */
    public function getActiveCustomerById($id)
    {
        //TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('Customer')
                ->where('customer_id = ?', $id)
                ->andWhere('is_deleted = ?', 0);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param Customer $customer
     * @return Customer
     * @throws DaoException
     */
    public function saveCustomer(Customer $customer): Customer
    {
        try {
            $this->persist($customer);
            return $customer;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Search Customers
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function searchCustomers(CustomerSearchFilterParams $customerSearchFilterParams): array
    {
        try {
            $paginator = $this->getSearchCustomerPaginator($customerSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
            $q->andWhere('customer.name = :customerName');
            $q->setParameter('customerName', $customerSearchFilterParams->getName());
        }
        $q->andWhere('customer.deleted = :deleted');
        $q->setParameter('deleted', false);
        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getSearchCustomersCount(CustomerSearchFilterParams $customerSearchFilterParams): int
    {
        try {
            $paginator = $this->getSearchCustomerPaginator($customerSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Customers
     * @return Customer[]
     * @throws DaoException
     */
    public function getCustomers(): array
    {
        try {
            $query = $this->createQueryBuilder(Customer::class, 'cus');
            $query->andWhere('cus.deleted = :deleted');
            $query->setParameter('deleted', false);
            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
?>
