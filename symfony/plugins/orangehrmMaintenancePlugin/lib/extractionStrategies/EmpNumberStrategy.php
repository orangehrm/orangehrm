<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 7/9/18
 * Time: 2:20 PM
 */
class EmpNumberStrategy {

    public function extractData($empNumber,$table){
        return $this->getAllEmployeeRecordsService()->getExtractDao()->extractDataFromEmployeeNum($empNumber,$table);
    }

    public function getAllEmployeeRecordsService()
    {
        if (!isset($this->getAllEmployeeRecordsService)) {
            $this->getAllEmployeeRecordsService = new GetAllEmployeeRecordsService();
        }
        return $this->getAllEmployeeRecordsService;
    }
}