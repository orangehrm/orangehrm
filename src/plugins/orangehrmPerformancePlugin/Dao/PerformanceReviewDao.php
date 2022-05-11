<?php

namespace OrangeHRM\Performance\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
    public function getPerformanceReviewList(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams)
    {
        try {
            $query = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getPerformanceReviewCount(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): int
    {
        try {
            $query = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
            return $this->getPaginator($query)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return \OrangeHRM\ORM\QueryBuilderWrapper
     */
    private function getPerformanceReviewQueryBuilderWrapper(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams)
    {
        $q = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $q->leftJoin('performanceReview.employee ', 'employee');
        $this->setSortingAndPaginationParams($q, $performanceReviewSearchFilterParams);
        $q->andWhere('employee.empNumber = :employeeNumber')
            ->setParameter('employeeNumber', $performanceReviewSearchFilterParams->getEmpNumber());
        return $this->getQueryBuilderWrapper($q);
    }
}
