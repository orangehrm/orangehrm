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

use Doctrine\ORM\Query\Expr;
use OrangeHRM\Admin\Dto\I18NGroupSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Entity\I18NTranslation;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;

class LocalizationDao extends BaseDao
{
    use LocalizationServiceTrait;

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

    /**
     * @param I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
     * @return array e.g. [0 => ['id' => 1, 'source' => 'About', 'note' => null, 'target' => '关于']]
     */
    public function getNormalizedTranslations(
        I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
    ): array {
        $q = $this->getTranslationsQueryBuilderWrapper($i18NTargetLangStringSearchFilterParams)->getQueryBuilder();
        $q->select(
            'langString.id AS langStringId',
            'langString.value AS source',
            'langString.note AS note',
            'translation.value AS target',
        );
        return array_values($q->getQuery()->execute());
    }

    /**
     * @param I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
     * @return array
     */
    public function getNormalizedTranslationsForExport(
        I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
    ): array {
        $q = $this->getTranslationsQueryBuilderWrapper($i18NTargetLangStringSearchFilterParams)->getQueryBuilder();
        $q->select(
            'langString.id AS langStringId',
            'langString.unitId AS unitId',
            'langString.value AS source',
            'langString.note AS note',
            'langString.version AS version',
            'translation.value AS target',
        );
        return $q->getQuery()->execute();
    }

    /**
     * @param I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getTranslationsQueryBuilderWrapper(
        I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(I18NLangString::class, 'langString');
        $q->leftJoin(
            'langString.translations',
            'translation',
            Expr\Join::WITH,
            'IDENTITY(translation.language) = :langId'
        );
        $q->setParameter('langId', $i18NTargetLangStringSearchFilterParams->getLanguageId());

        if (!is_null($i18NTargetLangStringSearchFilterParams->getOnlyTranslated())) {
            if ($i18NTargetLangStringSearchFilterParams->getOnlyTranslated() === true) {
                $q->andWhere($q->expr()->isNotNull('translation.value'));
            } elseif ($i18NTargetLangStringSearchFilterParams->getOnlyTranslated() === false) {
                $q->andWhere($q->expr()->isNull('translation.value'));
            }
        }

        if (!empty($i18NTargetLangStringSearchFilterParams->getGroupId())) {
            $q->andWhere('langString.group = :groupId')
                ->setParameter('groupId', $i18NTargetLangStringSearchFilterParams->getGroupId());
        }

        if (!empty($i18NTargetLangStringSearchFilterParams->getSourceText())) {
            $q->andWhere($q->expr()->like('langString.value', ':sourceText'))
                ->setParameter('sourceText', '%' . $i18NTargetLangStringSearchFilterParams->getSourceText() . '%');
        }

        if (!empty($i18NTargetLangStringSearchFilterParams->getTranslatedText())) {
            $q->andWhere($q->expr()->like('translation.value', ':translatedText'))
                ->setParameter(
                    'translatedText',
                    '%' . $i18NTargetLangStringSearchFilterParams->getTranslatedText() . '%'
                );
        }

        $this->setSortingAndPaginationParams($q, $i18NTargetLangStringSearchFilterParams);

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
     * @return int
     */
    public function getTranslationsCount(
        I18NTranslationSearchFilterParams $i18NTargetLangStringSearchFilterParams
    ): int {
        $q = $this->getTranslationsQueryBuilderWrapper($i18NTargetLangStringSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($q)->count();
    }

    /**
     * @param I18NTranslation[] $i18NTranslations
     */
    public function saveAndUpdateTranslatedLangString(array $i18NTranslations): void
    {
        $q = $this->createQueryBuilder(I18NTranslation::class, 'translation');

        /** @var I18NTranslation $i18NTranslation */
        foreach (array_values($i18NTranslations) as $i => $i18NTranslation) {
            $languageIdParamKey = 'languageId_' . $i;
            $langStringIdParamKey = 'langStringId_' . $i;

            $languageId = $i18NTranslation->getLanguage()->getId();
            $langStringId = $i18NTranslation->getLangString()->getId();

            $q->orWhere(
                $q->expr()->andX(
                    $q->expr()->eq('translation.langString', ':' . $langStringIdParamKey),
                    $q->expr()->eq('translation.language', ':' . $languageIdParamKey),
                )
            );
            $q->setParameter($languageIdParamKey, $languageId);
            $q->setParameter($langStringIdParamKey, $langStringId);
        }

        /** @var array<string, I18NTranslation> $updatableTranslationValues */
        $updatableTranslationValues = [];
        foreach ($q->getQuery()->execute() as $updatableTranslationValue) {
            $itemKey = $this->getLocalizationService()->generateLangStringLanguageKey(
                $updatableTranslationValue->getLanguage()->getId(),
                $updatableTranslationValue->getLangString()->getId(),
            );
            $updatableTranslationValues[$itemKey] = $updatableTranslationValue;
        }

        foreach ($i18NTranslations as $key => $i18NTranslation) {
            if (isset($updatableTranslationValues[$key])) {
                $updatableTranslationValues[$key]->setValue($i18NTranslation->getValue());
                $updatableTranslationValues[$key]->setCustomized($i18NTranslation->isCustomized());
                $updatableTranslationValues[$key]->setModifiedAt($i18NTranslation->getModifiedAt());

                //update
                $this->getEntityManager()->persist($updatableTranslationValues[$key]);
                continue;
            }
            //create
            $this->getEntityManager()->persist($i18NTranslation);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param I18NGroupSearchFilterParams $i18NGroupSearchFilterParams
     * @return array e.g. [0 => ['id' => 1, 'name' => 'admin', 'title' => 'Admin']]
     */
    public function searchGroups(I18NGroupSearchFilterParams $i18NGroupSearchFilterParams): array
    {
        return $this->getI18NGroupPaginator($i18NGroupSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param I18NGroupSearchFilterParams $i18NGroupSearchFilterParams
     * @return Paginator
     */
    private function getI18NGroupPaginator(I18NGroupSearchFilterParams $i18NGroupSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(I18NGroup::class, 'g');
        $this->setSortingAndPaginationParams($q, $i18NGroupSearchFilterParams);

        return $this->getPaginator($q);
    }

    /**
     * @param I18NGroupSearchFilterParams $i18NGroupSearchFilterParams
     * @return int
     */
    public function getI18NGroupCount(I18NGroupSearchFilterParams $i18NGroupSearchFilterParams): int
    {
        return $this->getI18NGroupPaginator($i18NGroupSearchFilterParams)->count();
    }
}
