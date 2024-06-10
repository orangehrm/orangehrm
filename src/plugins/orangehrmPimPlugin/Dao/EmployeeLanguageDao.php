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

namespace OrangeHRM\Pim\Dao;

use Doctrine\ORM\Query\Expr;
use OrangeHRM\Core\Dao\BaseDao;
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
     */
    public function saveEmployeeLanguage(EmployeeLanguage $employeeLanguage): EmployeeLanguage
    {
        $this->persist($employeeLanguage);
        return $employeeLanguage;
    }

    /**
     * @param int $empNumber
     * @param int $languageId
     * @param int $fluencyId
     * @return EmployeeLanguage|null
     */
    public function getEmployeeLanguage(int $empNumber, int $languageId, int $fluencyId): ?EmployeeLanguage
    {
        return $this->getRepository(EmployeeLanguage::class)->findOneBy(
            [
                'employee' => $empNumber,
                'language' => $languageId,
                'fluency' => $fluencyId,
            ]
        );
    }

    /**
     * @param array $entries
     * @param int $empNumber
     * @return array
     */
    public function getExistingEmployeeLanguageRecordsForEmpNumber(array $entries, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmployeeLanguage::class, 'employeeLanguage');

        $qb->select('IDENTITY(employeeLanguage.language) AS languageId', 'employeeLanguage.fluency as fluencyId');

        foreach ($entries as $index => $entry) {
            if (isset($entry['languageId']) && isset($entry['fluencyId'])) {
                $qb->orWhere(
                    $qb->expr()->andX(
                        $qb->expr()->eq('employeeLanguage.language', ':langId' . $index),
                        $qb->expr()->eq('employeeLanguage.fluency', ':fluencyId' . $index)
                    )
                );
                $qb->setParameter('langId' . $index, $entry['languageId'])
                    ->setParameter('fluencyId' . $index, $entry['fluencyId']);
            }
        }

        $qb->andWhere($qb->expr()->in('employeeLanguage.employee', ':empNumber'))
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     */
    public function deleteEmployeeLanguages(int $empNumber, array $entriesToDelete): int
    {
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
    }

    /**
     * @param EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
     * @return EmployeeLanguage[]
     */
    public function getEmployeeLanguages(
        EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
    ): array {
        $paginator = $this->getEmployeeLanguagesPaginator($employeeLanguagesSearchFilterParams);
        return $paginator->getQuery()->execute();
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
     */
    public function getEmployeeLanguagesCount(
        EmployeeLanguagesSearchFilterParams $employeeLanguagesSearchFilterParams
    ): int {
        $paginator = $this->getEmployeeLanguagesPaginator($employeeLanguagesSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return Language[]
     */
    public function getAllowedEmployeeLanguages(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): array {
        $paginator = $this->getAllowedEmployeeLanguagesPaginator($employeeAllowedLanguageSearchFilterParams);
        return array_column($paginator->getQuery()->execute(), 0);
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return int
     */
    public function getAllowedEmployeeLanguagesCount(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): int {
        $paginator = $this->getAllowedEmployeeLanguagesPaginator($employeeAllowedLanguageSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
     * @return Paginator
     */
    private function getAllowedEmployeeLanguagesPaginator(
        EmployeeAllowedLanguageSearchFilterParams $employeeAllowedLanguageSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Language::class, 'language');
        $q->leftJoin('language.employeeLanguages', 'employeeLanguage', Expr\Join::WITH, 'employeeLanguage.employee = :empNumber')
            ->setParameter('empNumber', $employeeAllowedLanguageSearchFilterParams->getEmpNumber());

        $q->addSelect('language.id');
        $q->addSelect('COUNT(language.name) as languageCount');

        // For backwards compatibility
        if ($employeeAllowedLanguageSearchFilterParams->getSortField() === 'l.name') {
            $employeeAllowedLanguageSearchFilterParams->setSortField('language.name');
        }
        $this->setSortingAndPaginationParams($q, $employeeAllowedLanguageSearchFilterParams);

        $q->addGroupBy('language.id');
        $q->andHaving($q->expr()->lt('languageCount', ':fluencyCount'))
            ->setParameter('fluencyCount', count(EmployeeLanguage::FLUENCIES));

        return $this->getPaginator($q);
    }
}
