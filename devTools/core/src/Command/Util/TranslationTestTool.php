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

namespace OrangeHRM\DevTools\Command\Util;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Installer\Migration\V5_0_0\LangStringHelper;
use Symfony\Component\Yaml\Yaml;

class TranslationTestTool
{
    use EntityManagerHelperTrait;

    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @param string $groupName
     * @return void
     * @throws Exception
     */
    public function execute(string $groupName, string $version)
    {
        $langCode = 'zz_ZZ';   //the test language will be added as zz_ZZ
        $this->addTranslations($langCode, $groupName, $version);
        //setting the default language to tr
        $q = $this->createQueryBuilder();
        $q->update('hs_hr_config')
            ->set('value', ':code')
            ->andWhere('name = :name')
            ->setParameter('code', $langCode)
            ->setParameter('name', 'admin.localization.default_language')
            ->executeQuery();
    }

    /**
     * @param string $language
     * @param string $groupName
     * @return void
     * @throws Exception
     */
    private function addTranslations(string $language, string $groupName, string $version): void
    {
        $filepath2 = 'installer/Migration/'.$version.'/lang-string/' . $groupName . '.yaml';
        $yml2 = Yaml::parseFile($filepath2);
        $langStrings = array_shift($yml2);
        foreach ($langStrings as $langString) {
            if (! ($langString['value'] == 'Allows Phone Numbers Only')) {
                $sourceObj = new TranslationUnit('tr_' . $langString['value'], null, $langString['value'], );
                $this->saveTranslationRecord($groupName, $sourceObj, $language);
            }
        }
    }

    /**
     * @param string $groupName
     * @param TranslationUnit $source
     * @param string $language
     * @return void
     * @throws Exception
     */
    private function saveTranslationRecord(string $groupName, TranslationUnit $source, string $language): void
    {
        $groupId = $this->getLangStringHelper()->getGroupId($groupName);
        $langStringId = $this->getLangStringHelper()->getLangStringIdByValueAndGroup($source->getSource(), $groupId);
        if ($langStringId == null) {
            throw new Exception(
                'Cannot add a translation to a non existent lang string: ' . $source->getSource()
            );
        }
        $langId = $this->getLanguageId($language);
        $existTranslation = $this->getTranslationRecord($langStringId, $langId);
        if ($existTranslation != null) {
            // TODO hanldle customized translations
        } else {
            $insetQuery = $this->createQueryBuilder();
            $insetQuery->insert('ohrm_i18n_translate')
                ->values(
                    ['lang_string_id' => ':langStringId',
                        'language_id' => ':langId',
                        'value' => ':target',
                    ]
                )
                ->setParameter('langStringId', $langStringId)
                ->setParameter('langId', $langId)
                ->setParameter('target', $source->getTarget())->executeQuery();
        }
    }

    /**
     * @return LangStringHelper|null
     */
    public function getLangStringHelper(): ?LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper($this->getEntityManager()->getConnection());
        }
        return $this->langStringHelper;
    }

    /**
     * @param string $langCode
     * @return int
     * @throws Exception
     */
    private function getLanguageId(string $langCode): int
    {
        $searchQuery = $this->createQueryBuilder();
        $searchQuery->select('language.id')
            ->from('ohrm_i18n_language', 'language')
            ->where('language.code = :langCode')
            ->setParameter('langCode', $langCode);
        return $searchQuery->executeQuery()->fetchOne();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @param array $langStringId
     * @param int $langId
     * @return string
     * @throws Exception
     */
    private function getTranslationRecord(int $langStringId, int $langId): string
    {
        $searchQuery = $this->createQueryBuilder();
        $searchQuery->select('translate.id')
            ->from('ohrm_i18n_translate', 'translate')
            ->where('translate.language_id = :langCode')
            ->andWhere('translate.lang_string_id = :langStringId')
            ->setParameter('langCode', $langId)
            ->setParameter('langStringId', $langStringId);
        return $searchQuery->executeQuery()->fetchOne();
    }

    /**\
     * @param string $langCode
     * @return void
     */
    public function setTestLanguage(string $langCode): void
    {
        $q = $this->createQueryBuilder();
        $q->select('language.code')
            ->from('ohrm_i18n_language', 'language')
            ->where('language.code = :code')
            ->setParameter('code', $langCode);
        $result = $q->executeQuery()->fetchOne();

        if (is_null($result)) {
            $insetQuery = $this->createQueryBuilder();
            $insetQuery->insert('ohrm_i18n_language')
                ->values(
                    [
                        'name' => ':name',
                        'code' => ':code',
                        'enabled ' => ':enabled',
                        'added' => ':added'
                    ]
                )
                ->setParameter('name', 'Test Language (TR)')
                ->setParameter('code', $langCode)
                ->setParameter('enabled', 1)
                ->setParameter('added', 0)
                ->executeQuery();
        }
    }
}
