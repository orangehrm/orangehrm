<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 29/9/18
 * Time: 2:04 PM
 */
class BasicAccessStrategy extends AccessStrategy
{


    public function access($employeeNumber)
    {
        $entitiyAccessData  = array();
        $matchByValues = $this->getMatchByValues($employeeNumber);
        $accessEntities = $this->getEntityRecords($matchByValues, $this->getEntityClassName());
        foreach ($accessEntities as $accessEntity) {
            $data =  $this->addRecordsToArray($accessEntity);
            array_push($entitiyAccessData, $data);
        }
        var_dump($data);
    }

    public function addRecordsToArray($accessEntity)
    {

        $parameters = $this->getParameters();
        $data = array();
        foreach ($parameters as $field) {
            $columnName = $field['field'];
            if ($accessEntity->$columnName) {
                $value = $accessEntity->$columnName;
                if ($field['class']){
                    $value = $this->getColumnValue($field['class'],$value);
                }
                $data[$columnName] =$value;
            }
        }
        return $data;
    }
}
