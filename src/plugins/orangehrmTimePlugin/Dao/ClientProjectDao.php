<?php

namespace OrangeHRM\Time\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Project;

class ClientProjectDao extends BaseDao
{
    public function getProjectsByCustomer(int $customerId, ?string $search)
    {
        $qb = $this->createQueryBuilder(Project::class, 'p');

        // Filter by customer
        $qb->andWhere('IDENTITY(p.customer) = :customerId')
           ->setParameter('customerId', $customerId);

        // Ignore deleted projects (if mapped in entity)
        $qb->andWhere('p.deleted = :isDeleted')
            ->setParameter('isDeleted', false);

        // Search by project name
        if (!empty($search)) {
            $qb->andWhere('p.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Order by name
        $qb->orderBy('p.name', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}