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

namespace OrangeHRM\Tools\Migrations\V5;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Yaml\Yaml;

class LangStringHelper
{
    use EntityManagerHelperTrait;

    /**
     * @param int $groupId
     * @return LangString[]
     */
    public function getLangStringArray(string $groupName): array
    {
        $groupId = $this->getGroupId($groupName);
        $langArray = [];
        $filepath = 'installer/upgrader/Migrations/V5/' . $groupName . 'LangString.yaml';
        $yml = Yaml::parseFile($filepath);
        $langStrings = array_shift($yml);
        foreach ($langStrings as $langString) {
            $langArray[] = new LangString($langString['unitId'], $groupId, $langString['value'], null, $langString['note']??null);
        }
        return $langArray;
    }

    /**
     * @param string $moduleName
     * @return int
     * @throws Exception
     */
    public function getGroupId(string $moduleName): int
    {
        $q = $this->createQueryBuilder();
        $q->select('module.id')->from('ohrm_i18n_group', 'module')->where('module.name = :group')->setParameter('group', $moduleName);
        return $q->executeQuery()->fetchOne();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @param int $groupId
     * @return void
     * @throws Exception
     */
    public function deleteNonCustomLangStrings(int $groupId): void
    {
        $groupStrings = $this->getLangStringRecords($groupId);
        $q = $this->createQueryBuilder();
        $q->delete('ohrm_i18n_translate')->where('ohrm_i18n_translate.customized != 1')->andWhere($q->expr()->in('ohrm_i18n_translate.lang_string_id', ':groupIds'))->setParameter('groupIds', $groupStrings, Connection::PARAM_INT_ARRAY)->executeQuery();
        $q2 = $this->createQueryBuilder();
        $deleteStrings = $this->getNonCustomLangStringIds($groupId);
        $q2->delete('ohrm_i18n_lang_string')->andWhere($q2->expr()->in('ohrm_i18n_lang_string.id', ':deleteIds'))->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)->executeQuery();
    }

    private function getLangStringRecords(int $groupId)
    {
        $q = $this->createQueryBuilder();
        $q->select('langString.id')->from('ohrm_i18n_lang_string', 'langString')->where('langString.group_id = :module')->setParameter('module', $groupId);
        $results = $q->executeQuery()->fetchAllAssociative();
        return array_column($results, 'id');
    }

    /**
     * @param int $groupId
     * @return array
     * @throws Exception
     */
    private function getNonCustomLangStringIds(int $groupId): array
    {
        $q = $this->createQueryBuilder();
        $q->select('translate.lang_string_id')->from('ohrm_i18n_lang_string', 'langString')->leftJoin('langString', 'ohrm_i18n_translate', 'translate', 'langString.id = translate.lang_string_id')->where('langString.group_id = :module')->andWhere('translate.customized = 1')->setParameter('module', $groupId);
        $results = $q->executeQuery()->fetchAllAssociative();
        $customStrings = array_column($results, 'lang_string_id');
        if ($customStrings == null) {
            return $this->getLangStringRecords($groupId);
        }
        $q2 = $this->createQueryBuilder();
        $q2->select('langString.id')->from('ohrm_i18n_lang_string', 'langString')->andWhere($q2->expr()->notIn('langString.id', ':customStrings'))->andWhere('langString.group_id = :module')->setParameter('customStrings', $customStrings, Connection::PARAM_INT_ARRAY)->setParameter('module', $groupId);
        $results2 = $q2->executeQuery()->fetchAllAssociative();
        return array_column($results2, 'id');
    }

    /**
     * @param array $langStringArray
     * @param int $groupId
     * @return void
     * @throws Exception
     */
    public function versionMigrateLangStrings(array $langStringArray, int $groupId)
    {
        foreach ($langStringArray as $langString) {
            $result = $this->getLangStringRecord($langString->getValue(), $groupId);
            if ($result == null) {
                $this->saveLangString($langString);
            } else {
                $this->updateLangStrings($langString->getUnitId(), array_column($result, 'id'));
            }
        }
    }

    /**
     * @param string $langStringValue
     * @param int $groupId
     * @return array
     * @throws Exception
     */

    public function getLangStringRecord(string $langStringValue, int $groupId): array
    {
        $q = $this->createQueryBuilder();
        $q->select('langString.id')->from('ohrm_i18n_lang_string', 'langString')->where('langString.value = :source')->andWhere('langString.group_id = :module')->setParameter('source', $langStringValue)->setParameter('module', $groupId);
        return $q->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param LangString $LangString
     * @return void
     * @throws Exception
     */
    private function saveLangString(LangString $LangString): void
    {
        $q = $this->createQueryBuilder();
        $q->insert('ohrm_i18n_lang_string')->values(['value' => ':string', 'group_id' => ':module', 'unit_id' => ':unitId', 'version' => ':version', 'note' => ':note',])->setParameter('string', $LangString->getValue())->setParameter('module', $LangString->getGroupId())->setParameter('unitId', $LangString->getUnitId())->setParameter('version', $LangString->getVersion())->setParameter('note', $LangString->getNote())->executeQuery();
    }

    /**
     * @param string $unitId
     * @param array $id
     * @return void
     * @throws Exception
     */
    private function updateLangStrings(string $unitId, array $id): void
    {
        $q = $this->createQueryBuilder();
        $q->update('ohrm_i18n_lang_string')->set('ohrm_i18n_lang_string.unit_id', ':key')->where('ohrm_i18n_lang_string.id = :id')->setParameter('key', $unitId)->setParameter('id', $id, Connection::PARAM_INT_ARRAY)->executeQuery();
    }
}
