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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Language;
use OrangeHRM\ORM\Paginator;

class LanguageDao extends BaseDao
{
    /**
     * @param Language $language
     * @return Language
     * @throws DaoException
     */
    public function saveLanguage(Language $language): Language
    {
        try {
            $this->persist($language);
            return $language;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return Language|null
     * @throws DaoException
     */
    public function getLanguageById(int $id): ?Language
    {
        try {
            $language = $this->getRepository(Language::class)->find($id);
            if ($language instanceof Language) {
                return $language;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return Language|null
     * @throws DaoException
     */
    public function getLanguageByName(string $name): ?Language
    {
        try {
            $query = $this->createQueryBuilder(Language::class, 'l');
            $trimmed = trim($name, ' ');
            $query->andWhere('l.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LanguageSearchFilterParams $languageSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getLanguageList(LanguageSearchFilterParams $languageSearchFilterParams): array
    {
        try {
            $paginator = $this->getLanguageListPaginator($languageSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
     * @throws DaoException
     */
    public function getLanguageCount(LanguageSearchFilterParams $languageSearchParamHolder): int
    {
        try {
            $paginator = $this->getLanguageListPaginator($languageSearchParamHolder);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteLanguages(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(Language::class, 'l');
            $q->delete()
                ->where($q->expr()->in('l.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $languageName
     * @return bool
     * @throws DaoException
     */
    public function isExistingLanguageName(string $languageName): bool
    {
        try {
            $q = $this->createQueryBuilder(Language::class, 'l');
            $trimmed = trim($languageName, ' ');
            $q->Where('l.name = :name');
            $q->setParameter('name', $trimmed);
            $count = $this->count($q);
            if ($count > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
