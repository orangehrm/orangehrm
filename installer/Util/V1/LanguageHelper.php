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

namespace OrangeHRM\Installer\Util\V1;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use OrangeHRM\Installer\Util\V1\Dto\LangString;
use Symfony\Component\Yaml\Yaml;

class LanguageHelper
{
    private Connection $connection;
    private array $groupIds = [];

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $filepath
     * @param string $groupName
     * @return LangString[]
     */
    public function readLangStrings(string $filepath, string $groupName): array
    {
        $langStrings = [];
        $groupId = $this->getGroupIdByName($groupName);
        foreach (Yaml::parseFile($filepath)['langStrings'] as $langString) {
            $langString['groupId'] = $groupId;
            $langStrings[] = LangString::createFromArray($langString);
        }
        return $langStrings;
    }

    /**
     * @param string $groupName
     * @return int
     */
    public function getGroupIdByName(string $groupName): int
    {
        if (!isset($this->groupIds[$groupName])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('i18nGroup.id')
                ->from('ohrm_i18n_group', 'i18nGroup')
                ->where('i18nGroup.name = :groupName')
                ->setParameter('groupName', $groupName)
                ->setMaxResults(1);
            $this->groupIds[$groupName] = $qb->fetchOne();
        }
        return $this->groupIds[$groupName];
    }

    /**
     * @param string $unitId
     * @param int|null $groupId
     */
    public function deleteLangStringByUnitId(string $unitId, ?int $groupId = null)
    {
        $qb = $this->getConnection()->createQueryBuilder()
            ->delete('ohrm_i18n_lang_string')
            ->andWhere('ohrm_i18n_lang_string.unit_id = :unitId')
            ->setParameter('unitId', $unitId);
        if (!is_null($groupId)) {
            $qb->andWhere('ohrm_i18n_lang_string.group_id = :groupId')
                ->setParameter('groupId', $groupId);
        }
        $qb->executeQuery();
    }

    /**
     * @param int $langId
     * @param int $langStringId
     * @param string|null $translation
     */
    public function insertTranslation(int $langId, int $langStringId, ?string $translation): void
    {
        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_i18n_translate')
            ->values([
                'lang_string_id' => ':langStringId',
                'language_id' => ':langId',
                'value' => ':translation',
            ])
            ->setParameter('langStringId', $langStringId)
            ->setParameter('langId', $langId)
            ->setParameter('translation', $translation)
            ->executeQuery();
    }

    /**
     * @param string $langCode
     * @return int|null
     */
    public function getLanguageIdByLangCode(string $langCode): ?int
    {
        return $this->getConnection()->createQueryBuilder()
            ->select('ohrm_i18n_language.id')
            ->from('ohrm_i18n_language')
            ->where('ohrm_i18n_language.code = :langCode')
            ->setParameter('langCode', $langCode)
            ->executeQuery()
            ->fetchOne() ?: null;
    }

    /**
     * @param string $langCode
     * @param bool $enable
     * @param bool $add
     */
    public function updateLanguageStatusByLangCode(string $langCode, bool $enable, bool $add): void
    {
        $this->getConnection()->createQueryBuilder()
            ->update('ohrm_i18n_language')
            ->set('ohrm_i18n_language.enabled', ':enabled')
            ->set('ohrm_i18n_language.added', ':added')
            ->where('ohrm_i18n_language.code = :langCode')
            ->setParameter('enabled', $enable, ParameterType::BOOLEAN)
            ->setParameter('added', $add, ParameterType::BOOLEAN)
            ->setParameter('langCode', $langCode)
            ->executeQuery();
    }

    public function createTestLanguagePack(): void
    {
        $this->updateLanguageStatusByLangCode('zz_ZZ', true, true);
        $testLangId = $this->getLanguageIdByLangCode('zz_ZZ');

        $offset = 0;
        $limit = 50;
        $done = false;

        do {
            $langStrings = $this->getConnection()->createQueryBuilder()
                ->select('ohrm_i18n_lang_string.id', 'ohrm_i18n_lang_string.value')
                ->from('ohrm_i18n_lang_string')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->executeQuery()
                ->fetchAllAssociative();
            foreach ($langStrings as $langString) {
                $translation = 'tr_' . $langString['value'] ?? '';
                $this->insertTranslation($testLangId, $langString['id'], $translation);
            }
            $offset += $limit;

            if (count($langStrings) < $limit) {
                $done = true;
            }
        } while (!$done);
    }

    public function deleteTestLanguagePack(): void
    {
        $this->updateLanguageStatusByLangCode('zz_ZZ', false, false);
        $testLangId = $this->getLanguageIdByLangCode('zz_ZZ');
        $this->getConnection()->createQueryBuilder()
            ->delete('ohrm_i18n_translate')
            ->where('ohrm_i18n_translate.language_id = :languageId')
            ->setParameter('languageId', $testLangId)
            ->executeQuery();
    }
}
