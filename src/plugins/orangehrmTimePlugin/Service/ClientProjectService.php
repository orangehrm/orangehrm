<?php

namespace OrangeHRM\Time\Service;

use OrangeHRM\Time\Dao\ClientProjectDao;

class ClientProjectService
{
    private ?ClientProjectDao $dao = null;

    public function getClientProjectDao(): ClientProjectDao
    {
        if (!$this->dao) {
            $this->dao = new ClientProjectDao();
        }

        return $this->dao;
    }

    public function getProjectsByCustomer(int $customerId, ?string $search)
    {
        return $this->getClientProjectDao()
            ->getProjectsByCustomer($customerId, $search);
    }
}