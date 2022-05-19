<?php

namespace OrangeHRM\Performance\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use Doctrine\ORM\Query\Expr;
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
        $q->leftJoin('performanceReview.jobTitle', 'jobTitle');
        $q->leftJoin('performanceReview.department', 'subUnit');
        $q->leftJoin(Reviewer::class, 'reviewer', Expr\Join::WITH, 'performanceReview.id = reviewer.review');
        $this->setSortingAndPaginationParams($q, $performanceReviewSearchFilterParams);
        $q->andWhere('reviewer.employee = performanceReview.employee')
        ->andWhere('employee.empNumber = :employeeNumber')
            ->setParameter('employeeNumber', $performanceReviewSearchFilterParams->getEmpNumber());
        return $this->getQueryBuilderWrapper($q);
    }

    public function getPerformanceSelfReviewStatus(PerformanceReview $performanceReview): string
    {
        $selfReviewer = $this->getPerformanceSelfReviewer($performanceReview);
        switch ($selfReviewer->getStatus()) {
            case 1:
                return 'Activated';
            case 2:
                return 'In progress';
            case 4:
            case 3:
                return 'Completed';
            default:
                return '';
        }
    }

    private function getPerformanceSelfReviewer(PerformanceReview $performanceReview): Reviewer
    {
        $reviewer = $this->getRepository(Reviewer::class)->findOneBy(['review'=>$performanceReview->getId(),'employee'=>$performanceReview->getEmployee()]);
        $q = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $q->andWhere('reviewer.review = :reviewId')
            ->setParameter('reviewId', $performanceReview->getId())
            ->andWhere('reviewer.employee =:employeeId')
            ->setParameter('employeeId', $performanceReview->getEmployee()->getEmployeeId());
        return $reviewer;
    }
}
