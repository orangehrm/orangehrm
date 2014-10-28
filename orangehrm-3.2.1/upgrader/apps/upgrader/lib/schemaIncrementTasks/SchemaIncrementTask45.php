<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask45 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 45;
        parent::execute();
        
        for($i = 0; $i <= 3; $i++) {
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
    
        $sql[0] = "INSERT INTO `hs_hr_config` (`key`, `value`) VALUES 
                    ('admin.localization.default_language', 'en'),
                    ('admin.localization.use_browser_language', 'No'),
                    ('admin.localization.default_date_format', 'Y-m-d');";
        
        $row[0] = 'SELECT selectCondition FROM hs_hr_employee 
                    LEFT JOIN hs_hr_emp_emergency_contacts ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_emergency_contacts.emp_number) 
                    LEFT JOIN hs_hr_compstructtree ON 
                        (hs_hr_employee.work_station = hs_hr_compstructtree.id) 
                    LEFT JOIN hs_hr_empstat ON 
                        (hs_hr_employee.emp_status = hs_hr_empstat.estat_code) 
                    LEFT JOIN hs_hr_job_title ON 
                        (hs_hr_employee.job_title_code = hs_hr_job_title.jobtit_code) 
                    LEFT JOIN hs_hr_eec ON 
                        (hs_hr_employee.eeo_cat_code = hs_hr_eec.eec_code) 
                    LEFT JOIN hs_hr_nationality ON 
                        (hs_hr_employee.nation_code = hs_hr_nationality.nat_code) 
                    LEFT JOIN hs_hr_ethnic_race ON 
                        (hs_hr_employee.ethnic_race_code = hs_hr_ethnic_race.ethnic_race_code) 
                    LEFT JOIN hs_hr_emp_dependents ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_dependents.emp_number)
                    LEFT JOIN hs_hr_emp_locations AS emp_location ON
                        (hs_hr_employee.emp_number = emp_location.emp_number)
                    LEFT JOIN hs_hr_location ON
                        (emp_location.loc_code = hs_hr_location.loc_code)
                    LEFT JOIN hs_hr_job_spec ON 
                        (hs_hr_job_title.jobspec_id = hs_hr_job_spec.jobspec_id) 
                    LEFT JOIN hs_hr_emp_contract_extend ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_contract_extend.emp_number) 
                    LEFT JOIN hs_hr_emp_basicsalary ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) 
                    LEFT JOIN hs_pr_salary_grade ON 
                        (hs_hr_emp_basicsalary.sal_grd_code = hs_pr_salary_grade.sal_grd_code) 
                    LEFT JOIN hs_hr_currency_type ON 
                        (hs_hr_emp_basicsalary.currency_id = hs_hr_currency_type.currency_id) 
                    LEFT JOIN hs_hr_payperiod ON 
                        (hs_hr_emp_basicsalary.payperiod_code = hs_hr_payperiod.payperiod_code) 
                    LEFT JOIN hs_hr_emp_passport ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_passport.emp_number) 
                    LEFT JOIN hs_hr_emp_reportto AS subordinate_list ON 
                        (hs_hr_employee.emp_number = subordinate_list.erep_sup_emp_number) 
                    LEFT JOIN hs_hr_employee AS subordinate ON
                        (subordinate.emp_number = subordinate_list.erep_sub_emp_number)
                    LEFT JOIN ohrm_emp_reporting_method AS subordinate_reporting_method ON 
                        (subordinate_list.erep_reporting_mode = subordinate_reporting_method.reporting_method_id) 
                    LEFT JOIN hs_hr_emp_work_experience ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_work_experience.emp_number) 
                    LEFT JOIN hs_hr_emp_education ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_education.emp_number) 
                    LEFT JOIN hs_hr_education ON 
                        (hs_hr_emp_education.edu_code = hs_hr_education.edu_code) 
                    LEFT JOIN hs_hr_emp_skill ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) 
                    LEFT JOIN hs_hr_skill ON 
                        (hs_hr_emp_skill.skill_code = hs_hr_skill.skill_code) 
                    LEFT JOIN hs_hr_emp_language ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) 
                    LEFT JOIN hs_hr_language ON 
                        (hs_hr_emp_language.lang_code = hs_hr_language.lang_code) 
                    LEFT JOIN hs_hr_emp_licenses ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_licenses.emp_number) 
                    LEFT JOIN hs_hr_licenses ON 
                        (hs_hr_emp_licenses.licenses_code = hs_hr_licenses.licenses_code) 
                    LEFT JOIN hs_hr_emp_member_detail ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_member_detail.emp_number) 
                    LEFT JOIN hs_hr_membership ON 
                        (hs_hr_emp_member_detail.membship_code = hs_hr_membership.membship_code) 
                    LEFT JOIN hs_hr_membership_type ON 
                        (hs_hr_emp_member_detail.membtype_code = hs_hr_membership_type.membtype_code) 
                    LEFT JOIN hs_hr_country ON 
                        (hs_hr_employee.coun_code = hs_hr_country.cou_code) 
                    LEFT JOIN hs_hr_emp_directdebit ON 
                        (hs_hr_emp_basicsalary.id = hs_hr_emp_directdebit.salary_id) 
                    LEFT JOIN hs_hr_emp_reportto AS supervisor_list ON 
                        (hs_hr_employee.emp_number = supervisor_list.erep_sub_emp_number) 
                    LEFT JOIN hs_hr_employee AS supervisor ON
                        (supervisor.emp_number = supervisor_list.erep_sup_emp_number)
                    LEFT JOIN ohrm_emp_reporting_method AS supervisor_reporting_method ON 
                        (supervisor_list.erep_reporting_mode = supervisor_reporting_method.reporting_method_id) 
                WHERE hs_hr_employee.emp_number in (
                    SELECT hs_hr_employee.emp_number FROM hs_hr_employee
                        LEFT JOIN hs_hr_emp_basicsalary ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) 
                        LEFT JOIN hs_hr_emp_education ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_education.emp_number) 
                        LEFT JOIN hs_hr_emp_skill ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) 
                        LEFT JOIN hs_hr_emp_language ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) 
                    WHERE whereCondition1
                )
                GROUP BY 
                     hs_hr_employee.emp_number,
                     hs_hr_employee.emp_lastname,
                     hs_hr_employee.emp_firstname,
                     hs_hr_employee.emp_middle_name,
                     hs_hr_employee.emp_birthday,
                     hs_hr_nationality.nat_name,
                     hs_hr_employee.emp_gender,
                     hs_hr_ethnic_race.ethnic_race_desc,
                     hs_hr_employee.emp_marital_status,
                     hs_hr_employee.emp_dri_lice_num,
                     hs_hr_employee.emp_dri_lice_exp_date,
                     hs_hr_employee.emp_street1,
                     hs_hr_employee.emp_street2,
                     hs_hr_employee.city_code,
                     hs_hr_employee.provin_code,
                     hs_hr_employee.emp_zipcode,
                     hs_hr_country.cou_code,
                     hs_hr_employee.emp_hm_telephone,
                     hs_hr_employee.emp_mobile,
                     hs_hr_employee.emp_work_telephone,
                     hs_hr_employee.emp_work_email,
                     hs_hr_employee.emp_oth_email';
        
        $sql[1] = "UPDATE ohrm_report_group SET core_sql = CASE report_group_id
                        WHEN '3' THEN '$row[0]'
                        END
                        WHERE report_group_id in(3)";
        
        $row[1] = 'datediff(current_date(), hs_hr_employee.joined_date)/365';
        $row[2] = 'hs_hr_employee.job_title_code';
        $row[3] = 'datediff(current_date(), hs_hr_employee.emp_birthday)/365';
        $row[4] = 'hs_hr_employee.work_station';
        $row[5] = 'emp_location.loc_code';
        
        $sql[2] = "UPDATE ohrm_filter_field SET where_clause_part = CASE filter_field_id
                        WHEN '12' THEN '$row[1]'
                        WHEN '14' THEN '$row[2]'
                        WHEN '17' THEN '$row[3]'
                        WHEN '18' THEN '$row[4]'
                        WHEN '20' THEN '$row[5]'
                        END
                        WHERE filter_field_id in(12,14,17,18,20)";
        
        $sql[3] = "UPDATE ohrm_filter_field 
                        SET where_clause_part = 'hs_hr_employee.emp_status', filter_field_widget = 'ohrmWidgetEmploymentStatusList' WHERE filter_field_id = 11";
        
        $this->sql = $sql;
    }
    
    public function getNotes() {
        
        return $notes;
    }
    
}