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

namespace OrangeHRM\I18N\Dao;

use Doctrine\ORM\Query\Expr;
use InvalidArgumentException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\ORM\QueryBuilderWrapper;

class I18NDao extends BaseDao
{
    /**
     * @param string $langCode
     * @return I18NLanguage|null
     */
    public function getLanguageByLangCode(string $langCode): ?I18NLanguage
    {
        return $this->getRepository(I18NLanguage::class)->findOneBy(['code' => $langCode, 'enabled' => true]);
    }

    public function getAllTranslationMessagesByLangCode(string $langCode)
    {
        $q = $this->createQueryBuilderWrapperForAllTranslationByLangCode($langCode)->getQueryBuilder();
        $q->leftJoin('langString.group', 'module');
        $q->select(
            'langString.unitId',
            'langString.value AS source',
            'translation.value AS target',
            'module.name AS groupName',
        );

        return $q->getQuery()->getArrayResult();
    }

    /**
     * @param string $langCode
     * @return QueryBuilderWrapper
     */
    private function createQueryBuilderWrapperForAllTranslationByLangCode(
        string $langCode
    ): QueryBuilderWrapper {
        $language = $this->getLanguageByLangCode($langCode);
        if (!$language instanceof I18NLanguage) {
            throw new InvalidArgumentException("Invalid locale: $langCode");
        }

        $q = $this->createQueryBuilder(I18NLangString::class, 'langString');
        $q->leftJoin(
            'langString.translations',
            'translation',
            Expr\Join::WITH,
            'IDENTITY(translation.language) = :langId'
        );

        $q->setParameter('langId', $language->getId());
        return $this->getQueryBuilderWrapper($q);
    }
}
