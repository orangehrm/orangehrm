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

class LangStringHelper
{
    use EntityManagerHelperTrait;

    /**
     * @param string $moduleName
     * @return int
     * @throws Exception
     */
    public function getModuleId(string $moduleName): int
    {
        $query = $this->createQueryBuilder();
        $query->select('module.id')
            ->from('ohrm_i18n_group', 'module')
            ->where('module.name = :group')
            ->setParameter('group', $moduleName);
        return $query->executeQuery()->fetchOne();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @param int $moduleId
     * @return LangString[]
     */
    public function getLangStringArray(int $moduleId): array
    {
        // TODO: MOVE TO Seperate file
        $langArray[] = new LangString(
            'shift_name', $moduleId,
            'Shift Name', null,
            'name of shift label'
        );
        $langArray[] = new LangString(
            'edit_job_title', $moduleId,
            'Edit Job Title',
            null,
            null
        );
        $langArray[] = new LangString(
            'duration_per_day', $moduleId,
            'Duration Per Day',
            null,
            null
        );
        $langArray[] = new LangString(
            'job_titles', $moduleId,
            'Job Titles',
            null,
            null
        );
        $langArray[] = new LangString(
            'add_job_titles', $moduleId,
            'Add Job Titles',
            null,
            null
        );
        return $langArray;
    }

    /**
     * @param $module
     * @return void
     * @throws Exception
     */
    public function deleteNonCustomLangStrings($module): void
    {
        $deleteStrings = $this->getNonCustomLangStringIds($module);

        $query = $this->createQueryBuilder();
        $query->delete('ohrm_i18n_translate')
            ->andWhere($query->expr()->in('ohrm_i18n_translate.lang_string_id', ':deleteIds'))
            ->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)
            ->executeQuery();

        $query2 = $this->createQueryBuilder();
        $query2->delete('ohrm_i18n_lang_string')
            ->andWhere($query2->expr()->in('ohrm_i18n_lang_string.id', ':deleteIds'))
            ->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }

    /**
     * @param $groupId
     * @return array
     * @throws Exception
     */
    private function getNonCustomLangStringIds($groupId): array
    {
        $query = $this->createQueryBuilder();
        $query->select('translate.lang_string_id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->leftJoin('langString', 'ohrm_i18n_translate', 'translate', 'langString.id = translate.lang_string_id')
            ->where('langString.group_id = :module')
            ->andWhere('translate.customized = 1')
            ->setParameter('module', $groupId);
        $results = $query->executeQuery()->fetchAllAssociative();

        $customStrings = array_column($results, 'lang_string_id');

        $query2 = $this->createQueryBuilder();
        $query2->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->andWhere($query2->expr()->notIn('langString.id', ':customStrings'))
            ->andWhere('langString.group_id = :module')
            ->setParameter('customStrings', $customStrings, Connection::PARAM_INT_ARRAY)
            ->setParameter('module', $groupId);
        $results2 = $query2->executeQuery()->fetchAllAssociative();

        return array_column($results2, 'id');
    }

    /**
     * @param $langStringArray
     * @param $groupId
     * @return void
     * @throws Exception
     */
    public function versionMigrateLangStrings($langStringArray, $groupId)
    {
        foreach ($langStringArray as $langString) {
            $result = $this->getLangStringRecord($langString->getValue(), $groupId);
            if ($result == null) {
                $langStringObj = new LangString(
                    $langString->getUnitId(),
                    $langString->getGroupId(),
                    $langString->getValue(),
                    $langString->getVersion(),
                    $langString->getNote()
                );
                $this->saveLangString($langStringObj);
            } else {
                $this->updateLangStrings($langString->getUnitId(), array_column($result, 'id'));
            }
        }
    }

    /**
     * @param $langString
     * @param $groupId
     * @return array
     * @throws Exception
     */
    private function getLangStringRecord($langString, $groupId): array
    {
        $q = $this->createQueryBuilder();
        $q->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->where('langString.value = :source')
            ->andWhere('langString.group_id = :module')
            ->setParameter('source', $langString)
            ->setParameter('module', $groupId);
        return $q->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param LangString $LangStringObj
     * @return void
     * @throws Exception
     */
    private function saveLangString(LangString $LangStringObj)
    {
        $insertQuery = $this->createQueryBuilder();
        $insertQuery->insert('ohrm_i18n_lang_string')
            ->values([
                'value' => ':string',
                'group_id' => ':module',
                'unit_id' => ':unitId',
                'version' => ':version',
                'note' => ':note',
            ])
            ->setParameter('string', $LangStringObj->getValue())
            ->setParameter('module', $LangStringObj->getGroupId())
            ->setParameter('unitId', $LangStringObj->getUnitId())
            ->setParameter('version', $LangStringObj->getVersion())
            ->setParameter('note', $LangStringObj->getNote())
            ->executeQuery();
    }

    /**
     * @param $unitId
     * @param $id
     * @return void
     * @throws Exception
     */
    private function updateLangStrings($unitId, $id)
    {
        $updateQuery = $this->createQueryBuilder();
        $updateQuery->update('ohrm_i18n_lang_string')
            ->set('ohrm_i18n_lang_string.unit_id', ':key')
            ->where('ohrm_i18n_lang_string.id = :id')
            ->setParameter('key', $unitId)
            ->setParameter('id', $id, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }
}
