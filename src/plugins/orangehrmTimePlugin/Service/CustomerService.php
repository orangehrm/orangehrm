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

namespace OrangeHRM\Time\Service;

use OrangeHRM\Entity\Customer;
use OrangeHRM\Time\Dao\CustomerDao;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;

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
     * @param CustomerDao $customerDao
     */
    public function setCustomerDao(CustomerDao $customerDao): void
    {
        $this->customerDao = $customerDao;
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return Customer[]
     */
    public function searchCustomers(CustomerSearchFilterParams $customerSearchFilterParams): array
    {
        return $this->getCustomerDao()->searchCustomers($customerSearchFilterParams);
    }

    /**
     * @param CustomerSearchFilterParams $customerSearchFilterParams
     * @return int
     */
    public function getCustomersCount(CustomerSearchFilterParams $customerSearchFilterParams): int
    {
        return $this->getCustomerDao()->getSearchCustomersCount($customerSearchFilterParams);
    }

    /**
     * @param int $customerId
     * @return Customer|null
     */
    public function getCustomer(int $customerId): ?Customer
    {
        return $this->getCustomerDao()->getCustomerById($customerId);
    }
}
