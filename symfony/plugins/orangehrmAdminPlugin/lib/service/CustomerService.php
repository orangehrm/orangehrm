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
class CustomerService extends BaseService {

    private $customerDao;

    /**
     * Construct
     */
    public function __construct() {
        $this->customerDao = new CustomerDao();
    }

    /**
     *
     * @return <type>
     */
    public function getCustomerDao() {
        return $this->customerDao;
    }

    /**
     *
     * @param CustomerDao $customerDao 
     */
    public function setCustomerDao(CustomerDao $customerDao) {
        $this->customerDao = $customerDao;
    }

    /**
     * Get Customer List
     * 
     * Get Customer List with pagination.
     * 
     * @param type $noOfRecords
     * @param type $offset
     * @param type $sortField
     * @param type $sortOrder
     * @param type $activeOnly
     * @return type 
     */
    public function getCustomerList($limit = 50, $offset = 0, $sortField = 'name', $sortOrder = 'ASC', $activeOnly = true) {
        return $this->customerDao->getCustomerList($limit, $offset, $sortField, $sortOrder, $activeOnly);
    }

    /**
     * Get Active customer cout.
     *
     * Get the total number of active customers for list component.
     * 
     * @param type $activeOnly
     * @return type 
     */
    public function getCustomerCount($activeOnly = true) {
        return $this->customerDao->getCustomerCount($activeOnly);
    }

    /**
     * Get customer by id
     * 
     * @param type $customerId
     * @return type 
     */
    public function getCustomerById($customerId) {
        return $this->customerDao->getCustomerById($customerId);
    }

    /**
     * Delete customer
     * 
     * Set customer 'is_deleted' parameter to 1.
     * 
     * @param type $customerId
     * @return type 
     */
    public function deleteCustomer($customerId) {
        return $this->customerDao->deleteCustomer($customerId);
    }

    /**
     * Undelete customer
     * 
     * Set customer 'is_deleted' parameter to 0.
     * 
     * @param type $customerId
     * @return type 
     */
    public function undeleteCustomer($customerId) {
        return $this->customerDao->undeleteCustomer($customerId);
    }

    /**
     * 
     * Get all customer list
     * 
     * Get all active customers
     * 
     * @param type $activeOnly
     * @return type 
     */
    public function getAllCustomers($activeOnly = true) {
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
     * @version 2.7.1
     * @param Array $customerIdList List of Customer Ids
     * @param Boolean $excludeDeletedCustomers Exclude deleted Customers or not
     * @return Array of Customer Names
     */
    public function getCustomerNameList($customerIdList, $excludeDeletedCustomers = true) {
        return $this->customerDao->getCustomerNameList($customerIdList, $excludeDeletedCustomers);
    }

    /**
     * Check wheather the customer has any timesheet records
     * 
     * @param type $customerId
     * @return type 
     */
    public function hasCustomerGotTimesheetItems($customerId) {
        return $this->customerDao->hasCustomerGotTimesheetItems($customerId);
    }

}

?>
