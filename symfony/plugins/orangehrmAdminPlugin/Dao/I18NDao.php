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
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Core\Exception\DaoException;

class I18NDao extends BaseDao
{
    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function searchLanguages(I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams): array
    {
        try {
            return $this->getSearchI18NLanguagePaginator($i18NLanguageSearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams
     * @return Paginator
     */
    private function getSearchI18NLanguagePaginator(I18NLanguageSearchFilterParams $i18NLanguageSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(I18NLanguage::class, 'l');
        $this->setSortingParams($q, $i18NLanguageSearchFilterParams);

        if ($i18NLanguageSearchFilterParams->getEnabledOnly()) {
            $q->andWhere('l.enabled = :enabled');
            $q->setParameter('enabled', I18NLanguage::ENABLED);
        }
        if ($i18NLanguageSearchFilterParams->getAddedOnly()) {
            $q->andWhere('l.added = :added');
            $q->setParameter('added', I18NLanguage::ADDED);
        }
        return $this->getPaginator($q);
    }
}
