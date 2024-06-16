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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;
use OrangeHRM\Entity\PayPeriod;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use Doctrine\ORM\Query\Expr;

class PayGradeDao extends BaseDao
{
    /**
     * @param int $payGradeId
     * @return PayGrade|null
     */
    public function getPayGradeById(int $payGradeId): ?PayGrade
    {
        return $this->getRepository(PayGrade::class)->find($payGradeId);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingPayGradeIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(PayGrade::class, 'payGrade');

        $qb->select('payGrade.id')
            ->andWhere($qb->expr()->in('payGrade.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return PayGrade[]
     */
    public function getPayGradeList(PayGradeSearchFilterParams $payGradeSearchFilterParams): array
    {
        return $this->getPayGradesPaginator($payGradeSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param PayGradeSearchFilterParams $payGradeSearchFilterParams
     * @return Paginator
     */
    private function getPayGradesPaginator(PayGradeSearchFilterParams $payGradeSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(PayGrade::class, 'pg');
        $this->setSortingAndPaginationParams($q, $payGradeSearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param int $payGradeId
     * @return PayGradeCurrency[]
     */
    public function getCurrencyListByPayGradeId(int $payGradeId): array
    {
        $q = $this->createQueryBuilder(PayGradeCurrency::class, 'pgc');
        $q->leftJoin('pgc.currencyType', 'ct');
        $q->andWhere('pgc.payGrade = :payGradeId')
            ->setParameter('payGradeId', $payGradeId);
        $q->addOrderBy('ct.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return Paginator
     */
    public function getPayGradeCurrencyPaginator(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(PayGradeCurrency::class, 'pgc');
        $q->leftJoin('pgc.currencyType', 'ct');
        if (!empty($payGradeCurrencySearchFilterParams->getPayGradeId())) {
            $q->andWhere('pgc.payGrade = :payGradeId')
                ->setParameter('payGradeId', $payGradeCurrencySearchFilterParams->getPayGradeId());
        }
        $this->setSortingAndPaginationParams($q, $payGradeCurrencySearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return PayGradeCurrency[]
     */
    public function getPayGradeCurrencyList(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams)
    {
        return $this->getPayGradeCurrencyPaginator($payGradeCurrencySearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return int
     */
    public function getPayGradeCurrencyListCount(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): int
    {
        return $this->getPayGradeCurrencyPaginator($payGradeCurrencySearchFilterParams)->count();
    }

    /**
     * @param string $currencyId
     * @param int $payGradeId
     * @return PayGradeCurrency|null
     */
    public function getCurrencyByCurrencyIdAndPayGradeId(string $currencyId, int $payGradeId): ?PayGradeCurrency
    {
        $q = $this->createQueryBuilder(PayGradeCurrency::class, 'pgc');
        $q->andWhere('pgc.payGrade = :payGradeId')
            ->setParameter('payGradeId', $payGradeId);
        $q->andWhere('pgc.currencyType = :currencyTypeId')
            ->setParameter('currencyTypeId', $currencyId);

        return $this->fetchOne($q);
    }

    /**
     * @return PayPeriod[]
     */
    public function getPayPeriods(): array
    {
        $q = $this->createQueryBuilder(PayPeriod::class, 'pp');
        $q->addOrderBy('pp.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }

    /**
     * @return CurrencyType[]
     */
    public function getCurrencies(): array
    {
        $q = $this->createQueryBuilder(CurrencyType::class, 'ct');
        $q->addOrderBy('ct.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }

    /**
     * @param string $id
     * @return CurrencyType|null
     */
    public function getCurrencyById(string $id): ?CurrencyType
    {
        return $this->getRepository(CurrencyType::class)->find($id);
    }

    /**
     * @param int[] $ids
     * @param int $payGradeId
     * @return int[]
     */
    public function getExistingCurrencyIdsForPayGradeId(array $ids, int $payGradeId): array
    {
        $qb = $this->createQueryBuilder(PayGradeCurrency::class, 'payGradeCurrency');

        $qb->select('payGradeCurrency.currencyId')
            ->andWhere($qb->expr()->in('payGradeCurrency.currencyId', ':ids'))
            ->andWhere($qb->expr()->eq('payGradeCurrency.payGradeId', ':payGradeId'))
            ->setParameter('ids', $ids)
            ->setParameter('payGradeId', $payGradeId);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param PayGradeSearchFilterParams $payGradeSearchFilterParams
     * @return int
     */
    public function getPayGradesCount(PayGradeSearchFilterParams $payGradeSearchFilterParams): int
    {
        return $this->getPayGradesPaginator($payGradeSearchFilterParams)->count();
    }

    /**
     * @param PayGrade $payGrade
     * @return PayGrade
     */
    public function savePayGrade(PayGrade $payGrade): PayGrade
    {
        $this->persist($payGrade);
        return $payGrade;
    }

    /**
     * @param PayGradeCurrency $payGradeCurrency
     * @return PayGradeCurrency
     */
    public function savePayGradeCurrency(PayGradeCurrency $payGradeCurrency): PayGradeCurrency
    {
        $this->persist($payGradeCurrency);
        return $payGradeCurrency;
    }

    /**
     * @param array $tobeDeletedIds
     * @return int
     */
    public function deletePayGrades(array $tobeDeletedIds): int
    {
        $q = $this->createQueryBuilder(PayGrade::class, 'pg');
        $q->delete()
            ->where($q->expr()->in('pg.id', ':ids'))
            ->setParameter('ids', $tobeDeletedIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $payGradeId
     * @param array $toBeDeletedIds
     * @return int
     */
    public function deletePayGradeCurrency(int $payGradeId, array $toBeDeletedIds): int
    {
        $q = $this->createQueryBuilder(PayGradeCurrency::class, 'pgc');
        $q->delete()
            ->where($q->expr()->in('pgc.currencyType', ':currencyIds'))
            ->andWhere('pgc.payGrade = :payGradeId')
            ->setParameters([
                'currencyIds' => $toBeDeletedIds,
                'payGradeId' => $payGradeId,
            ]);
        return $q->getQuery()->execute();
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return CurrencyType[]
     */
    public function getAllowedPayCurrencies(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): array
    {
        $paginator = $this->getAllowedPayCurrenciesPaginator($payGradeCurrencySearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return int
     */
    public function getAllowedPayCurrenciesCount(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): int
    {
        $paginator = $this->getAllowedPayCurrenciesPaginator($payGradeCurrencySearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return Paginator
     */
    public function getAllowedPayCurrenciesPaginator(
        PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(CurrencyType::class, 'ct');
        $q->leftJoin('ct.payGradeCurrencies', 'pgc', Expr\Join::WITH, 'pgc.payGradeId = :payGradeId');

        $q->andWhere($q->expr()->isNull('pgc.payGradeId'));
        $q->setParameter('payGradeId', $payGradeCurrencySearchFilterParams->getPayGradeId());
        $this->setSortingAndPaginationParams($q, $payGradeCurrencySearchFilterParams);
        return $this->getPaginator($q);
    }
}
