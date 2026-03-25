<?php

namespace OrangeHRM\Time\Service;

use OrangeHRM\Time\Dao\ClientEmployeeDao;

class ClientEmployeeService
{
    private ?ClientEmployeeDao $dao = null;

    public function getClientEmployeeDao(): ClientEmployeeDao
    {
        if (!$this->dao) {
            $this->dao = new ClientEmployeeDao();
        }

        return $this->dao;
    }

    public function getEmployeesByCustomer(int $customerId, ?string $search)
    {
        return $this->getClientEmployeeDao()
            ->getEmployeesByCustomer($customerId, $search);
    }
}