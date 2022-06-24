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

use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use Doctrine\ORM\Query\Expr;

class LocalizationDao extends BaseDao
{
    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
     * @return array
     */
    public function searchLanguages(I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams): array
    {
        return $this->getI18NLanguagePaginator($i18NLanguageSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
     * @return Paginator
     */
    private function getI18NLanguagePaginator(
        I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(I18NLanguage::class, 'l');
        $this->setSortingAndPaginationParams($q, $i18NLanguageSearchFilterParams);

        if ($i18NLanguageSearchFilterParams->getEnabledOnly()) {
            $q->andWhere('l.enabled = :enabled');
            $q->setParameter('enabled', true);
        }
        if (!is_null($i18NLanguageSearchFilterParams->getAddedOnly())) {
            $q->andWhere('l.added = :added');
            $q->setParameter('added', $i18NLanguageSearchFilterParams->getAddedOnly());
        }
        return $this->getPaginator($q);
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
     * @return int
     */
    public function getLanguagesCount(I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams): int
    {
        return $this->getI18NLanguagePaginator($i18NLanguageSearchFilterParams)->count();
    }

    /**
     * @param int $languageId
     * @return I18NLanguage|null
     */
    public function getLanguageById(int $languageId): ?I18NLanguage
    {
        return $this->getRepository(I18NLanguage::class)->find($languageId);
    }

    /**
     * @param I18NLanguage $i18NLanguage
     * @return I18NLanguage
     */
    public function saveI18NLanguage(I18NLanguage $i18NLanguage): I18NLanguage
    {
        $this->persist($i18NLanguage);
        return $i18NLanguage;
    }

    public function getAllSourceText()
    {
        $q = $this->createQueryBuilderWrapper()->getQueryBuilder();

        $q->leftJoin('langString.group', 'module');
        $q->select(
            'langString.unitId',
            'langString.value AS source',
            'translation.value AS target',
            'module.name AS groupName',
        );

        return $q->getQuery()->getArrayResult();

    }

    public function createQueryBuilderWrapper(): QueryBuilderWrapper
    {
//        dump('here');
        $q = $this->createQueryBuilder(I18NLangString::class, 'langString');
        $q->leftJoin(
            'langString.translations',
            'translation',
            Expr\Join::WITH,
            'IDENTITY(translation.language) = :langId'
        );

        $q->setParameter('langId', 1);
//        dump($q);
        return $this->getQueryBuilderWrapper($q);
    }
}
