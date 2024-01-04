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
use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Installer\Util\V1\Dto\LangString;

class LangStringHelper
{
    private Connection $connection;
    private ?LanguageHelper $languageHelper = null;

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
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * @return LanguageHelper
     */
    public function getLangHelper(): LanguageHelper
    {
        if (!$this->languageHelper instanceof LanguageHelper) {
            $this->languageHelper = new LanguageHelper($this->getConnection());
        }
        return $this->languageHelper;
    }

    /**
     * @param string $moduleName
     * @return int
     */
    public function getGroupId(string $moduleName): int
    {
        $q = $this->createQueryBuilder();
        $q->select('module.id')
            ->from('ohrm_i18n_group', 'module')
            ->where('module.name = :group')
            ->setParameter('group', $moduleName);
        return $q->executeQuery()->fetchOne();
    }

    /**
     * @param string $groupName
     * @return void
     */
    public function deleteNonCustomizedLangStrings(string $groupName): void
    {
        $groupId = $this->getLangHelper()->getGroupIdByName($groupName);
        $langStringIds = $this->getLangStringIdsForGroup($groupId);
        $qb = $this->createQueryBuilder()
            ->delete('ohrm_i18n_translate')
            ->where('ohrm_i18n_translate.customized != 1');
        $qb->andWhere($qb->expr()->in('ohrm_i18n_translate.lang_string_id', ':langStringIds'))
            ->setParameter('langStringIds', $langStringIds, Connection::PARAM_INT_ARRAY)
            ->executeQuery();

        $deleteStrings = $this->getNonCustomizedLangStringIds($groupId);
        $qb = $this->createQueryBuilder()
            ->delete('ohrm_i18n_lang_string');
        $qb->andWhere($qb->expr()->in('ohrm_i18n_lang_string.id', ':deleteIds'))
            ->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }

    /**
     * @param int $groupId
     * @return array
     */
    private function getLangStringIdsForGroup(int $groupId): array
    {
        $q = $this->createQueryBuilder();
        $q->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->where('langString.group_id = :module')
            ->setParameter('module', $groupId);
        $results = $q->executeQuery()->fetchAllAssociative();
        return array_column($results, 'id');
    }

    /**
     * @param int $groupId
     * @return array
     */
    private function getNonCustomizedLangStringIds(int $groupId): array
    {
        $q = $this->createQueryBuilder()
            ->select('translate.lang_string_id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->leftJoin('langString', 'ohrm_i18n_translate', 'translate', 'langString.id = translate.lang_string_id')
            ->where('langString.group_id = :module')
            ->andWhere('translate.customized = 1')
            ->setParameter('module', $groupId);
        $results = $q->executeQuery()->fetchAllAssociative();
        $customStrings = array_column($results, 'lang_string_id');
        if ($customStrings == null) {
            return $this->getLangStringIdsForGroup($groupId);
        }

        $qb = $this->createQueryBuilder()
            ->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString');
        $qb->andWhere($qb->expr()->notIn('langString.id', ':customStrings'))
            ->andWhere('langString.group_id = :module')
            ->setParameter('customStrings', $customStrings, Connection::PARAM_INT_ARRAY)
            ->setParameter('module', $groupId);
        $results = $qb->executeQuery()->fetchAllAssociative();
        return array_column($results, 'id');
    }

    /**
     * @param string $directoryPath
     * @param string $groupName
     */
    public function insertOrUpdateLangStrings(string $directoryPath, string $groupName)
    {
        $langStringArray = $this->getLangHelper()->readLangStrings(
            realpath($directoryPath . "/lang-string/$groupName.yaml"),
            $groupName
        );
        foreach ($langStringArray as $langString) {
            $langStringId = $this->getLangStringIdByValueAndGroup($langString->getValue());
            if (is_null($langStringId)) {
                $this->saveLangString($langString);
            } else {
                $this->updateLangString($langStringId, $langString);
            }
        }
    }

    /**
     * @param string $langStringValue
     * @param int|null $groupId
     * @return int|null
     */
    public function getLangStringIdByValueAndGroup(string $langStringValue, ?int $groupId = null): ?int
    {
        $q = $this->createQueryBuilder()
            ->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->where('langString.value = :source')
            ->setParameter('source', $langStringValue);
        if (!is_null($groupId)) {
            $q->andWhere('langString.group_id = :module')
                ->setParameter('module', $groupId);
        }

        if (false != $result = $q->executeQuery()->fetchOne()) {
            return $result;
        }
        return null;
    }

    /**
     * @param string $langStringUnitId
     * @param int $groupId
     * @return int|null
     */
    public function getLangStringIdByUnitIdAndGroup(string $langStringUnitId, int $groupId): ?int
    {
        $q = $this->createQueryBuilder()
            ->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->where('langString.unit_id = :unitId')
            ->andWhere('langString.group_id = :group')
            ->setParameter('unitId', $langStringUnitId)
            ->setParameter('group', $groupId);
        if (false != $result = $q->executeQuery()->fetchOne()) {
            return $result;
        }
        return null;
    }

    /**
     * @param LangString $langString
     */
    private function saveLangString(LangString $langString): void
    {
        $this->createQueryBuilder()
            ->insert('ohrm_i18n_lang_string')
            ->values([
                'value' => ':string',
                'group_id' => ':module',
                'unit_id' => ':unitId',
                'version' => ':version',
                'note' => ':note'
            ])
            ->setParameter('string', $langString->getValue())
            ->setParameter('module', $langString->getGroupId())
            ->setParameter('unitId', $langString->getUnitId())
            ->setParameter('version', $langString->getVersion())
            ->setParameter('note', $langString->getNote())
            ->executeQuery();
    }

    /**
     * @param int $langStringId
     * @param LangString $langString
     */
    private function updateLangString(int $langStringId, LangString $langString): void
    {
        $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string')
            ->set('ohrm_i18n_lang_string.unit_id', ':key')
            ->set('ohrm_i18n_lang_string.group_id', ':groupId')
            ->where('ohrm_i18n_lang_string.id = :id')
            ->setParameter('key', $langString->getUnitId())
            ->setParameter('groupId', $langString->getGroupId())
            ->setParameter('id', $langStringId)
            ->executeQuery();
    }
}
