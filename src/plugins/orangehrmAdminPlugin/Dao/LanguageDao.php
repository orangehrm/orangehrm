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

use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Language;
use OrangeHRM\ORM\Paginator;

class LanguageDao extends BaseDao
{
    /**
     * @param Language $language
     * @return Language
     */
    public function saveLanguage(Language $language): Language
    {
        $this->persist($language);
        return $language;
    }

    /**
     * @param int $id
     * @return Language|null
     */
    public function getLanguageById(int $id): ?Language
    {
        $language = $this->getRepository(Language::class)->find($id);
        if ($language instanceof Language) {
            return $language;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingLanguageIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Language::class, 'language');
        $qb->select('language.id')
            ->andWhere($qb->expr()->in('language.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $name
     * @return Language|null
     */
    public function getLanguageByName(string $name): ?Language
    {
        $query = $this->createQueryBuilder(Language::class, 'l');
        $trimmed = trim($name, ' ');
        $query->andWhere('l.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param LanguageSearchFilterParams $languageSearchFilterParams
     * @return array
     */
    public function getLanguageList(LanguageSearchFilterParams $languageSearchFilterParams): array
    {
        $paginator = $this->getLanguageListPaginator($languageSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param LanguageSearchFilterParams $languageSearchFilterParams
     * @return Paginator
     */
    public function getLanguageListPaginator(
        LanguageSearchFilterParams $languageSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Language::class, 'l');
        $this->setSortingAndPaginationParams($q, $languageSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param LanguageSearchFilterParams $languageSearchParamHolder
     * @return int
     */
    public function getLanguageCount(LanguageSearchFilterParams $languageSearchParamHolder): int
    {
        $paginator = $this->getLanguageListPaginator($languageSearchParamHolder);
        return $paginator->count();
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteLanguages(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Language::class, 'l');
        $q->delete()
            ->where($q->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param string $languageName
     * @return bool
     */
    public function isExistingLanguageName(string $languageName): bool
    {
        $q = $this->createQueryBuilder(Language::class, 'l');
        $trimmed = trim($languageName, ' ');
        $q->where('l.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
