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

namespace OrangeHRM\Time\Service;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Time\Dao\CustomerDao;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Core\Exception\ServiceException;

class CustomerService
{

    /**
     * @var CustomerDao|null
     */
    private ?CustomerDao $customerDao = null;

    /**
     * @return CustomerDao
     */
    public function getCustomerDao(): CustomerDao
    {
        if (!$this->customerDao instanceof CustomerDao) {
            $this->customerDao = new CustomerDao();
        }
        return $this->customerDao;
    }

    /**
     * Get Customer List
     * Get Customer List with pagination.
     * @param false $count
     * @param int|null $offset
     * @param int|null $limit
     * @param string $sortField
     * @param string $sortOrder
     * @return int|Customer[]
     */
    public function getCustomerList(
        string $sortField = 'cus.name',
        string $sortOrder = 'ASC',
        ?int $limit = null,
        ?int $offset = null,
        bool $count = false)
    {
        //TODO
        return $this->customerDao->getCustomerList($sortField, $sortOrder, $limit, $offset, $count);
    }

    /**
     * @param CustomerDao $customerDao
     */
    public function setCustomerDao(CustomerDao $customerDao): void
    {
        //TODO
        $this->customerDao = $customerDao;
    }


    /**
     * Get Active customer cout.
     * Get the total number of active customers for list component.
     * @param type $activeOnly
     * @return type
     */
    public function getCustomerCount($activeOnly = true)
    {
        //TODO
        return $this->customerDao->getCustomerCount($activeOnly);
    }

    /**
     * Get customer by id
     * @param type $customerId
     * @return type
     */
    public function getCustomerById($customerId)
    {
        //TODO
        return $this->customerDao->getCustomerById($customerId);
    }

    /**
     * Get customer by Name
     * @param type $customerId
     * @return type
     */
    public function getActiveCustomerById($customerId)
    {
        //TODO
        return $this->customerDao->getActiveCustomerById($customerId);
    }

    /**
     * Get customer by id
     * @param type $customerName
     * @return type
     */
    public function getCustomerByName($customerName)
    {
        //TODO
        return $this->customerDao->getCustomerByName($customerName);
    }

    /**
     * Delete customer
     * Set customer 'is_deleted' parameter to 1.
     * @param type $customerId
     * @return type
     */
    public function deleteCustomer($customerId)
    {
        //TODO
        return $this->customerDao->deleteCustomer($customerId);
    }

    /**
     * Undelete customer
     * Set customer 'is_deleted' parameter to 0.
     * @param type $customerId
     * @return type
     */
    public function undeleteCustomer($customerId)
    {
        //TODO
        return $this->customerDao->undeleteCustomer($customerId);
    }

    /**
     * Get all customer list
     * Get all active customers
     * @param type $activeOnly
     * @return type
     */
    public function getAllCustomers($activeOnly = true)
    {
        //TODO
        return $this->customerDao->getAllCustomers($activeOnly);
    }

    /**
     * Return an array of Customer Names
     *
     * <pre>
     * Ex: $customerIdList = array(1, 2);
     *
     * For above $customerIdList parameter there will be an array like below as the response.
     *
     * array(
     *          0 => array('customerId' => 1, 'name' => 'Xavier'),
     *          1 => array('customerId' => 2, 'name' => 'ACME')
     * )
     * </pre>
     *
     * @param Array $customerIdList List of Customer Ids
     * @param Boolean $excludeDeletedCustomers Exclude deleted Customers or not
     * @return Array of Customer Names
     * @version 2.7.1
     */
    public function getCustomerNameList($customerIdList, $excludeDeletedCustomers = true)
    {
        //TODO
        return $this->customerDao->getCustomerNameList($customerIdList, $excludeDeletedCustomers);
    }

    /**
     * Check wheather the customer has any timesheet records
     * @param type $customerId
     * @return type
     */
    public function hasCustomerGotTimesheetItems($customerId)
    {
        //TODO
        return $this->customerDao->hasCustomerGotTimesheetItems($customerId);
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return array
     * @throws ServiceException
     */
    public function searchCustomers(CustomerSearchFilterParams $customerSearchFilterParams): array
    {
        try {
            return $this->getCustomerDao()->searchCustomers($customerSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return int
     * @throws ServiceException
     */
    public function getCustomersCount(CustomerSearchFilterParams $customerSearchFilterParams): int
    {
        try {
            return $this->getCustomerDao()->getSearchCustomersCount($customerSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get customer by id
     * @param int $customerId
     * @return Customer|null
     * @throws ServiceException
     */
    public function getCustomer(int $customerId): ?Customer
    {
        try {
            return $this->getCustomerDao()->getCustomerById($customerId);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete Customers
     * @param array $deletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteCustomers(array $deletedIds): int
    {
        return $this->getCustomerDao()->deleteCustomer($deletedIds);
    }
}
?>
