<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask46 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 46;
        parent::execute();
        
        $result[] = $this->updateSalaryCurrencyDetail();
        
        $result[] = $this->updateOhrmDisplayField();
        
        for($i = 0; $i <= 2; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
    
    public function getUserInputWidgets() {
        
    }
    
    public function setUserInputs() {
        
    }
    
    public function loadSql() {
        
        $sql[0] = "UPDATE hs_hr_emp_basicsalary 
                        SET currency_id = 'ZAR' WHERE currency_id = 'SAR'";
        
        $sql[1] = "UPDATE hs_hr_emp_member_detail 
                        SET ememb_subs_currency = 'ZAR' WHERE ememb_subs_currency = 'SAR'";
        
        $sql[2] = "UPDATE hs_hr_currency_type 
                        SET code = '173', currency_name = 'Saudi Arabia Riyal' WHERE currency_id = 'SAR'";
        
        $this->sql = $sql;
    }
    
    private function updateSalaryCurrencyDetail() {
        $salaryCurrancyDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_currency_detail");
        $success = true;
        if($salaryCurrancyDetails) {
            while($row = $this->upgradeUtility->fetchArray($salaryCurrancyDetails))
            {
                $salGrdCode = $row['sal_grd_code'];
                $currencyId = $row['currency_id'];
                if ($currencyId == 'SAR') {
                    $duplicateSalaryCurrencyDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_currency_detail WHERE currency_id = 'ZAR' AND sal_grd_code = '$salGrdCode'");
                    if ($this->upgradeUtility->fetchArray($duplicateSalaryCurrencyDetails)) {
                        $sql = "DELETE FROM hs_pr_salary_currency_detail 
                            WHERE currency_id = 'SAR' AND sal_grd_code = '$salGrdCode'";
                        
                        $result = $this->upgradeUtility->executeSql($sql);
                        if(!$result) {
                            $success = false;
                        }
                    } else {
                        $sql = "UPDATE hs_pr_salary_currency_detail 
                            SET currency_id = 'ZAR' WHERE currency_id = 'SAR' AND sal_grd_code = '$salGrdCode'";
                        
                        $result = $this->upgradeUtility->executeSql($sql);
                        if(!$result) {
                            $success = false;
                        }
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateOhrmDisplayField() {
        $customFields = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_custom_fields");
        $success = true;
        if($customFields){
            while($row = $this->upgradeUtility->fetchArray($customFields))
            {
                $customFieldNo = $row['field_num'];
                $name = "hs_hr_employee.custom" . $customFieldNo;
                $existingCustomFields = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_display_field WHERE name = '$name'");
                if (!($this->upgradeUtility->getRowCount($existingCustomFields) > 0)) {
                    $reportGroupId = "3";
                    $label = $row['name'];
                    $fieldAlias = "customField" . $customFieldNo;
                    $isSortable = "false";
                    $sortOrder = 'NULL';
                    $sortField = 'NULL';
                    $elementType = "label";
                    $elementProperty = "<xml><getter>customField" . $customFieldNo . "</getter></xml>";
                    $width = "200";
                    $isExportable = "0";
                    $textAlignmentStyle = 'NULL';
                    $isValueList = "0";
                    $displayFieldGroupId = "16";
                    $defaultValue = "---";
                    $isEncrypted = '0';
                    
                    $valueString = "'".$reportGroupId."', '". $name."', '". $label."', '". $fieldAlias."', '". $isSortable."', ". $sortOrder.", ". $sortField.", '". $elementType."', '". $elementProperty."', '". $width."', '". $isExportable."', ". $textAlignmentStyle.", '". $isValueList."', '". $displayFieldGroupId."', '". $defaultValue."', '". $isEncrypted."'";
                    $sql = "INSERT INTO ohrm_display_field 
                                    (report_group_id, name, label, field_alias, is_sortable, sort_order, sort_field, element_type, element_property, width, is_exportable, text_alignment_style, is_value_list, display_field_group_id, default_value, is_encrypted) 
                                    VALUES($valueString); ";
                    
                    $success = $this->upgradeUtility->executeSql($sql);
                }
            }
        }
        return $success;
    }
    
    public function getNotes() {
        
        return $notes;
    }  
    
}