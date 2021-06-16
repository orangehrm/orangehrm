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

namespace OrangeHRM\Pim\Dao;

use Doctrine\ORM\Query\Expr;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\Language;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeAllowedLanguageSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeLanguagesSearchFilterParams;

class EmployeeLanguageDao extends BaseDao
{
    /**
     * @param EmployeeLanguage $employeeLanguage
     * @return EmployeeLanguage
     * @throws DaoException
     */
    public function saveEmployeeLanguage(EmployeeLanguage $employeeLanguage): EmployeeLanguage
    {
        try {
            $this->persist($employeeLanguage);
            return $employeeLanguage;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $languageId
     * @param int $fluencyId
     * @return EmployeeLanguage|null
     * @throws DaoException
     */
    public function getEmployeeLanguage(int $empNumber, int $languageId, int $fluencyId): ?EmployeeLanguage
    {
        try {
            return $this->getRepository(EmployeeLanguage::class)->findOneBy(
                [
                    'employee' => $empNumber,
                    'language' => $languageId,
                    'fluency' => $fluencyId,
                ]
            );
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeLanguages(int $empNumber, array $entriesToDelete): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeLanguage::class, 'el');
            $q->delete();
            foreach ($entriesToDelete as $key => $langFluency) {
                if (isset($langFluency['languageId']) && isset($langFluency['fluencyId'])) {
                    $q->orWhere(
                        $q->expr()->andX(
                            $q->expr()->eq('el.language', ':langId' . $key),
                            $q->expr()->eq('el.fluency', ':fluencyId' . $key)
                        )
                    );
                    $q->setParameter('langId' . $key, $langFluency['languageId'])
                        ->setParameter('fluencyId' . $key, $langFluency['fluencyId']);
                }
            }
            $q->andWhere('el.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            var_dump($e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
     * @return EmployeeLanguage[]
     * @throws DaoException
     */
    public function getEmployeeLanguages(
        EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
    ): array {
        try {
            $paginator = $this->getEmployeeLanguagesPaginator($employeeLanguagesSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
     * @return Paginator
     */
    private function getEmployeeLanguagesPaginator(
        EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeLanguage::class, 'el');
        $q->leftJoin('el.language', 'l');
        $this->setSortingAndPaginationParams($q, $employeeLanguagesSearchFilterParams);

        $q->andWhere('el.employee = :empNumber')
            ->setParameter('empNumber', $employeeLanguagesSearchFilterParams->getEmpNumber());

        if (!is_null($employeeLanguagesSearchFilterParams->getLanguageIds())) {
            $q->andWhere($q->expr()->in('l.id', ':languageIds'))
                ->setParameter('languageIds', $employeeLanguagesSearchFilterParams->getLanguageIds());
        }

        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getEmployeeLanguagesCount(
        EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
    ): int {
        try {
            $paginator = $this->getEmployeeLanguagesPaginator($employeeLanguagesSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return Language[]
     * @throws DaoException
     */
    public function getAllowedEmployeeLanguages(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): array {
        try {
            $paginator = $this->getAllowedEmployeeLanguagesPaginator($employeeAllowedLanguageSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getAllowedEmployeeLanguagesCount(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): int {
        try {
            $paginator = $this->getAllowedEmployeeLanguagesPaginator($employeeAllowedLanguageSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return Paginator
     */
    private function getAllowedEmployeeLanguagesPaginator(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Language::class, 'l');
        $q->leftJoin('l.employeeLanguages', 'el', Expr\Join::WITH, 'el.employee = :empNumber');
        $q->setParameter('empNumber', $employeeAllowedLanguageSearchFilterParams->getEmpNumber());
        $q->addSelect('el');
        $this->setSortingAndPaginationParams($q, $employeeAllowedLanguageSearchFilterParams);

        $q->addGroupBy('l.name');
        $q->andHaving($q->expr()->lt($q->expr()->count('l.name'), ':fluencyCount'))
            ->setParameter('fluencyCount', count(EmployeeLanguage::FLUENCIES));

        return $this->getPaginator($q);
    }
}
