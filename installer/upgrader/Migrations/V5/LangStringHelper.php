<?php

namespace OrangeHRM\Tools\Migrations\V5;

use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Tools\Migrations\V5\LangString;

class LangStringHelper
{
    use EntityManagerHelperTrait;

    private function getModuleId(string $moduleName): int
    {

        $query = $this->createQueryBuilder();
        $query->select('module.id')->from('ohrm_i18n_group', 'module')->where('module.name = :group')->setParameter('group', $moduleName);
        return $query->executeQuery()->fetchOne();
    }

    private function getLangStringArray($module): array
    {
        $langStringobj = new LangString('shift_name', $module, 'Shift Name',null,'name of shift label');
        $langArray[] = $langStringobj;
        $langStringobj1 = new LangString('edit_job_title', $module, 'Edit Job Title',null,null);
        $langArray[] = $langStringobj1;
        $langStringobj2 = new LangString('duration_per_day', $module, 'Duration Per Day',null,null);
        $langArray[] = $langStringobj2;
        $langStringobj3 = new LangString('job_titles', $module, 'Job Titles',null,null);
        $langArray[] = $langStringobj3;
        $langStringobj4 = new LangString('add_job_titles', $module, 'Add Job Titles',null,null);
        $langArray[] = $langStringobj4;
        return $langArray;
    }

    private function getNonCustomLangStringIds($module)
    {
        $customStrings = [];
        $query = $this->createQueryBuilder();
        $query->select('translate.lang_string_id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->leftJoin('langString', 'ohrm_i18n_translate', 'translate', 'langString.id = translate.lang_string_id')
            ->where('langString.group_id = :module')
            ->andWhere('translate.customized = 1')
            ->setParameter('module', $module);
        $results = $query->executeQuery()->fetchAllAssociative();

        $customStrings = array_column($results, 'lang_string_id');

        $deleteStrings = [];
        $query2 = $this->createQueryBuilder();
        $query2->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->andWhere($query2->expr()->notIn('langString.id', ':customStrings'))
            ->andWhere('langString.group_id = :module')
            ->setParameter('customStrings', $customStrings, Connection::PARAM_INT_ARRAY)
            ->setParameter('module', $module);
        $results2 = $query2->executeQuery()->fetchAllAssociative();

        return array_column($results2, 'id');
    }

    private function deleteNonCustomLangStrings($module)
    {
        $deleteStrings = $this->getNonCustomLangStringIds($module);
        var_dump('todelete', $deleteStrings);

        $query = $this->createQueryBuilder();
        $query->delete('ohrm_i18n_translate')->andWhere($query->expr()->in('ohrm_i18n_translate.lang_string_id', ':deleteIds'))->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)->executeQuery();

        $query2 = $this->createQueryBuilder();
        $query2->delete('ohrm_i18n_lang_string')->andWhere($query2->expr()->in('ohrm_i18n_lang_string.id', ':deleteIds'))->setParameter('deleteIds', $deleteStrings, Connection::PARAM_INT_ARRAY)->executeQuery();
    }
    private function getLangStringRecord($langString , $module): array
    {
        $q = $this->createQueryBuilder();
        $q->select('langString.id')
            ->from('ohrm_i18n_lang_string', 'langString')
            ->where('langString.value = :source')
            ->andWhere('langString.group_id = :module')
            ->setParameter('source', $langString)
            ->setParameter('module', $module);
        return $q->executeQuery()->fetchAllAssociative();
    }

    private function versionMigrateLangStrings($langStringArray , $module)
    {
        foreach ($langStringArray as $langString){
            // var_dump($langString->getValue());
            $result = $this->getLangStringRecord($langString->getValue(), $module);
            if($result == null){
                $langStringObj = new LangString($langString->getUnitId(),$langString->getGroupId(),$langString->getValue(),$langString->getVersion(),$langString->getNote());
                $this->saveLangString($langStringObj);
            }else{
                $this->updateLangStrings($langString->getUnitId(),array_column($result, 'id') );
            }
        }
    }

    private function updateLangStrings($unitId , $result)
    {
        $updateQuery = $this->createQueryBuilder();
        $updateQuery->update('ohrm_i18n_lang_string')
            ->set('ohrm_i18n_lang_string.unit_id', ':key')
            ->where('ohrm_i18n_lang_string.id = :id')
            ->setParameter('key', $unitId)
            ->setParameter('id', $result , Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }

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
            ->setParameter('version',$LangStringObj->getVersion())
            ->setParameter('note',$LangStringObj->getNote())
            ->executeQuery();
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }
}
