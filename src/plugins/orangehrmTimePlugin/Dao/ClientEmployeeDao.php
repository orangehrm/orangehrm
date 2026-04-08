<?php

namespace OrangeHRM\Time\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;

class ClientEmployeeDao extends BaseDao
{
    public function getEmployeesByCustomer(int $customerId, ?string $search)
    {
        $qb = $this->createQueryBuilder(Employee::class, 'e')
                    ->leftJoin('e.subDivision', 's')
                    ->andWhere('s.name = (
                        SELECT c.name FROM OrangeHRM\Entity\Customer c WHERE c.id = :customerId
                    )')
                    ->setParameter('customerId', $customerId);

        if (!empty($search)) {
            $qb->andWhere(
                '(e.firstName LIKE :search 
                OR e.lastName LIKE :search 
                OR e.employeeId LIKE :search)'
            )->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getArrayResult();
    }
}