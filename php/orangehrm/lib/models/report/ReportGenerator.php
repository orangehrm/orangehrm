<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.

 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTabILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/models/report/JoinTable.php';
require_once ROOT_PATH . '/lib/models/report/ReportField.php';
require_once ROOT_PATH . '/lib/models/report/ReportModuleObject.php';

class ReportGenerator {
	private static $dbCon;

    private $dataValues = array (
        'reportId' => null,
        'reportName' => null,
        'employeeIdLength' => null, // TODO: Deprecate

    );

    private $criteriaFieldMap = array (
        'EMPNO' => 'emp_number',
        'AGE' => 'emp_birthday',
        'PAYGRD' => 'sal_grd_code',
        'QUL' => array (
            'table' => 'hs_hr_emp_education',
            'field' => 'edu_code',
            'pk' => 'emp_number',
            'fk' => 'emp_number'
        ),
        'EMPSTATUS' => 'emp_status',
        'SERPIR' => 'joined_date',
        'JOIDAT' => 'joined_date',
        'JOBTITLE' => 'job_title_code',
        'LANGUAGE' => array (
            'table' => 'hs_hr_emp_language',
            'field' => 'lang_code',
            'pk' => 'emp_number',
            'fk' => 'emp_number'
        ),
        'SKILL' => array (
            'table' => 'hs_hr_emp_skill',
            'field' => 'skill_code',
            'pk' => 'emp_number',
            'fk' => 'emp_number'
        ),

    );

    private $criteriaFieldTableMap = array(
    	'EMPNO' => 'hs_hr_employee',
        'AGE' => 'hs_hr_employee',
        'PAYGRD' => 'hs_hr_employee',
        'QUL' => 'hs_hr_emp_education',
        'EMPSTATUS' => 'hs_hr_employee',
        'SERPIR' => 'hs_hr_employee',
        'JOIDAT' => 'hs_hr_employee',
        'JOBTITLE' => 'hs_hr_employee',
        'LANGUAGE' => null,
        'SKILL' => null,
    );

    private $selectionFieldMap = array ();

    /**
     * @deprecated 2.5-beta.14 - Aug 14, 2009
     * @todo Remove when old public properties are replaced with current properties
     */
    private $publicPropertyMapping = array (
        'repID' => 'reportId',
        'repName' => 'reportName',
    );

    private $preffixedFields = array('EMPNO');
    private $listFields = array('REPORTTO', 'QUL', 'YEAROFPASSING', 'SKILLS', 'CONTRACT', 'WORKEXPERIENCE', 'REPORTINGMETHOD', 'LANGUAGES');

    private $criteria;
    private $field;
    private $headName;

    public function __construct() {
    	$emptyMarker = ReportField::EMPTY_MARKER;

        $ageQuery = "IF(STRCMP(DATE_FORMAT(hs_hr_employee.`emp_birthday`, CONCAT(YEAR(hs_hr_employee.`emp_birthday`), '-%m-%d')), '0-00-00'), " .
        "DATE_FORMAT(hs_hr_employee.`emp_birthday`, CONCAT(YEAR(hs_hr_employee.`emp_birthday`), '-%m-%d')), " .
        "'{$emptyMarker}')";

        $sevicePeriodQuery = "IF(STRCMP(DATE_FORMAT(hs_hr_employee.`joined_date`, CONCAT(YEAR(hs_hr_employee.`joined_date`), '-%m-%d')), '0-00-00'), " .
        		"DATE_FORMAT(hs_hr_employee.`joined_date`, CONCAT(YEAR(hs_hr_employee.`joined_date`), '-%m-%d')), " .
        		"'{$emptyMarker}')";

        $this->selectionFieldMap = array (
            'EMPNO' => new ReportField('emp_number'),
            'EMPFIRSTNAME' => new ReportField('emp_firstname'),
            'EMPLASTNAME' => new ReportField('emp_lastname'),
            'ADDRESS1' => new ReportField(array (
                'emp_street1',
                'emp_street2',
                'city_code',
                'provin_code',
                'coun_code',
                'emp_zipcode'
            ), ReportField :: COMPOSITE_VALUE),
            'TELENO' => new ReportField('emp_hm_telephone'),
            'AGE' => new ReportField($ageQuery, ReportField :: DIRECT_QUERY),
            'REPORTTO' => new ReportField("{CONCAT_WS(' ', eee.`emp_firstname`, eee.`emp_lastname`)}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_reportto', 'erep_sub_emp_number', 'emp_number', new JoinTable('hs_hr_employee', 'emp_number', 'erep_sup_emp_number', 'hs_hr_emp_reportto', 'eee')),
            'REPORTINGMETHOD' => new ReportField("{hs_hr_emp_reportto.`erep_reporting_mode`}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_reportto', 'erep_sub_emp_number', 'emp_number'),
            'JOBTITLE' => new ReportField('jobtit_name', ReportField :: SINGLE_REFERENCE, 'hs_hr_job_title', 'jobtit_code', 'job_title_code'),
            'SERPIR' => new ReportField($sevicePeriodQuery, ReportField::DIRECT_QUERY),
            'SUBDIVISION' => new ReportField('title', ReportField :: SINGLE_REFERENCE, 'hs_hr_compstructtree', 'id', 'work_station'),
            'QUL' => new ReportField("{CONCAT_WS(' - ', hs_hr_education.`edu_uni`, hs_hr_education.`edu_deg`)}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_education', 'emp_number', 'emp_number', new JoinTable('hs_hr_education', 'edu_code', 'edu_code', 'hs_hr_emp_education')),
            'YEAROFPASSING' => new ReportField("{IF(STRCMP(YEAR(`edu_end_date`), '0'), YEAR(`edu_end_date`), '$emptyMarker')}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_education', 'emp_number', 'emp_number'),
            'EMPSTATUS' => new ReportField('estat_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_empstat', 'estat_code', 'emp_status'),
            'PAYGRD' => new ReportField('sal_grd_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_basicsalary', 'emp_number', 'emp_number', new JoinTable('hs_pr_salary_grade', 'sal_grd_code', 'sal_grd_code', 'hs_hr_emp_basicsalary')),
            'LANGUAGES' => new ReportField('lang_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_language', 'emp_number', 'emp_number',  new JoinTable('hs_hr_language', 'lang_code', 'lang_code', 'hs_hr_emp_language')),
            'SKILLS' => new ReportField('skill_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_skill', 'emp_number', 'emp_number', new JoinTable('hs_hr_skill', 'skill_code', 'skill_code', 'hs_hr_emp_skill')),
            'CONTRACT' => new ReportField("{CONCAT(DATE(`econ_extend_start_date`), ' - ', DATE(`econ_extend_end_date`))}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_contract_extend', 'emp_number', 'emp_number'),
            'WORKEXPERIENCE' => new ReportField("{CONCAT(`eexp_employer`, ' - ', `eexp_jobtit`, ' - ',(YEAR(`eexp_to_date`)-YEAR(`eexp_from_date`)), ' Years',' - ',(MONTH(`eexp_to_date`)-MONTH(`eexp_from_date`)),' Months')}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_work_experience', 'emp_number', 'emp_number'),

        );

        $this->criteria = array ();
        $this->field = array ();
        $sysConfObj = new sysConf();

        $this->employeeIdLength = $sysConfObj->getEmployeeIdLength();
    }

    public function ageToYear($age) {
        $currYear = strftime('%Y');
        $currMonthDate = strftime('-%m-%d');
        $birthYear = (int) $currYear - $age;
        return $birthYear . $currMonthDate; // TODO: Format using the locale util
    }

    /**
     * @deprecated 2.5-beta.15 - Aug 14, 2009
     */
    public function reportQueryBuilder() {
        return $this->buildReportQuery();
    }

    public function buildReportQuery() {

        $groupBy = null;

        list ($filterJoins, $where) = $this->_buildFilters();
        list ($selectJoins, $select) = $this->_buildSelections();

        $joins = array_merge($filterJoins, $selectJoins);
        array_unique($joins);

        $query = $this->_buildQuery($select, $joins, $where);

        return $query;
    }

    function reportDisplay($repDetails) {
        $employee = array ();

        if (is_array($repDetails)) {
            $employee = current($repDetails);
        }

        $columns = count($employee);
        $rows = count($repDetails);

        require_once (ROOT_PATH . '/templates/report/report.php'); //TODO: Remove and move to the controller
    }

    public function fetchArray($sql) {
        $arrayDispList = NULL;
        if (!is_object(ReportGenerator :: $dbCon) && !ReportGenerator :: $dbCon instanceof DMLFunctions) {
            ReportGenerator :: $dbCon = new DMLFunctions();
        }

        $message2 = ReportGenerator :: $dbCon->executeQuery($sql);
        $i = 0;
        while ($line = ReportGenerator :: $dbCon->dbObject->getArray($message2, MYSQL_NUM)) {
            for ($c = 0; count($this->field) * 2 > $c; $c += 2) {
                $arrayDispList[$line[0]][$c / 2][$line[$c]] = $line[$c +1];
            }
            $i++;
        }
        return $arrayDispList;
    }

    public function populateCriteria($criteriaString) {
        $criteria = explode('|', $criteriaString);
        $cCount = count($criteria);
        for ($c = 0; $cCount > $c; $c++) {
            $crit_value = explode("=", $criteria[$c]);
            $this->criteria[$crit_value[0]] = '';
            $dCount = count($crit_value);
            for ($d = 1; $dCount > $d; $d++)
                if ($d == $dCount -1) {
                    $this->criteria[$crit_value[0]] .= $crit_value[$d];
                } else {
                    $this->criteria[$crit_value[0]] .= $crit_value[$d] . "|";
                }
        }
    }

    public function populateFields($fieldString) {
        $field = explode('|', $fieldString);

        $empNoField = false;
        $fieldCount = count($field);

        for ($c = 0; $fieldCount > $c; $c++) {
            $this->field[$field[$c]] = 1;
            if ($field[$c] == 'EMPNO') {
                $empNoField = true;
            }
        }

        $repgen->field['EMPNO'] = 1;

        return $empNoField;
    }

    public function setCriteria($key, $value, $concatinate = false) {
        if ($concatinate) {
            $this->criteria[$key] .= $value;
        } else {
            $this->criteria[$key] = $value;
        }
    }

    public function setField($key, $value) {
        $this->field[$key] = $value;
    }

    public function getFieldCount() {
        return count($this->field);
    }

    public function appendHeader($value) {
        $this->headName[] = $value;
    }

    public function getHeaders() {
        return $this->headName;
    }

    public function buildDisplayList($query) {
        $dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		$arrayDispList = array();
		while ($row = $dbConnection->dbObject->getArray($result)) {
			$dataRow = array();
			foreach ($row as $column => $value) {
				if (is_numeric($column)) { // TODO: Remove this part when $dbConnection->dbObject->getArray() can fetch an associative array only
				    continue;
				}

				$trimmedValue = trim(str_replace(ReportField::GROUP_SEPARATOR, '', $value));
				$trimmedValue = trim(str_replace(ReportField::COMPOSITE_SEPARATOR, '', $value));

				if (empty($trimmedValue)) {
				    $value = null;
				} elseif (in_array($column, $this->listFields)) {
				    $value = explode(ReportField::GROUP_SEPARATOR, $value);
				}

				$dataRow[$column] = $value;
			}
		    $arrayDispList[] = $dataRow;
		}

		return $arrayDispList;
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->dataValues)) {
            $this->dataValues[$name] = $value;
        }
        elseif (array_key_exists($name, $this->publicPropertyMapping)) {
            $key = $this->publicPropertyMapping[$name];
            $this->dataValues[$key];
        } else {
            throw new Exception('PropertyNotSet');
        }
    }

    public function __get($name) {
        if (array_key_exists($name, $this->dataValues)) {
            return $this->dataValues[$name];
        } else {
            // TODO: Warn
            return null;
        }
    }

    private function _buildFilters() {
        $where = array ();
        $joins = array ();
        foreach ($this->criteria as $field => $value) {

            if (!empty ($value)) {
                $comparator = '=';

                if (is_array($this->criteriaFieldMap[$field])) {
                    $mappedField = "{$this->criteriaFieldMap[$field]['table']}.`{$this->criteriaFieldMap[$field]['field']}`";
                    $criteria = $this->criteriaFieldMap[$field];
                    $joins[$mappedField] =  new JoinTable($criteria['table'], $criteria['pk'], $criteria['fk']) ;
                } else {
                    $mappedField = "{$this->criteriaFieldTableMap[$field]}.`{$this->criteriaFieldMap[$field]}`";
                }

                $multipleMatchFields = explode('|', $value);

                $multipleMatchFieldsCount = count($multipleMatchFields);
                if ($multipleMatchFieldsCount == 2) {
                    list ($comparator, $value) = $multipleMatchFields;
                } elseif ($multipleMatchFieldsCount == 3 && $multipleMatchFields[0] == 'range') {
                    list ($comparator, $lowerLimit, $upperLimit) = $multipleMatchFields;

                    if ($field == 'AGE' || $field == 'SERPIR') {
                        $lowerLimit = $this->ageToYear($lowerLimit);
                        $upperLimit = $this->ageToYear($upperLimit);

                        /* Swapping the values because ageToYear() will return a lower year value
                         * a higher age
                         */
                        $temp = $lowerLimit;
                        $lowerLimit = $upperLimit;
                        $upperLimit = $temp;
                    }

                    $where[] = "({$mappedField} > '{$lowerLimit}') AND ({$mappedField} < '{$upperLimit}')";
                    continue;
                }

                if ($field == 'AGE' || $field == 'SERPIR') {
                    $value = $this->ageToYear($value);
                } elseif ($field == 'PAYGRD') {
                	$where[] = "hs_hr_emp_basicsalary.`sal_grd_code` = '{$value}'";
                	$joins[] = new ReportField('sal_grd_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_basicsalary', 'emp_number', 'emp_number', new JoinTable('hs_pr_salary_grade', 'sal_grd_code', 'sal_grd_code', 'hs_hr_emp_basicsalary'));
                	continue;
                } elseif ($field == 'QUL') {
                	$joins['hs_hr_emp_education.`edu_code`'] = new ReportField("{CONCAT_WS(' - ', hs_hr_education.`edu_uni`, hs_hr_education.`edu_deg`)}", ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_education', 'emp_number', 'emp_number', new JoinTable('hs_hr_education', 'edu_code', 'edu_code', 'hs_hr_emp_education'));
                } elseif ($field == 'LANGUAGE') {
                    $joins['hs_hr_emp_language.`lang_code`'] = new ReportField('lang_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_language', 'emp_number', 'emp_number',  new JoinTable('hs_hr_language', 'lang_code', 'lang_code', 'hs_hr_emp_language'));
                } elseif ($field == 'SKILL') {
                    $joins['hs_hr_emp_skill.`skill_code`'] = new ReportField('skill_name', ReportField :: MULTIPLE_REFERENCE, 'hs_hr_emp_skill', 'emp_number', 'emp_number', new JoinTable('hs_hr_skill', 'skill_code', 'skill_code', 'hs_hr_emp_skill'));
                }

                $where[] = "{$mappedField} {$comparator} '{$value}'";
            }
        }

        return array (
            $joins,
            $where
        );
    }

    private function _buildSelections() {
        $select = array ();
        $joins = array ();
		$groupSeparator = ReportField::GROUP_SEPARATOR;

        foreach ($this->field as $field => $value) {
            if (!empty ($value) && ($value === 1)) {
                $this->appendHeader($field);

                $mappedField = $this->selectionFieldMap[$field];
				$preffix = ReportField::LEFT_TABLE . '.';

				if (!($mappedField instanceof ReportField)) {
				    continue;
				}

				$selectionAdded = false;
				switch ($mappedField->type) {
				    case ReportField::SINGLE_VALUE:
				    	$fieldString = "{$preffix}`{$mappedField->field}`";
				    	break;
				    case ReportField::COMPOSITE_VALUE:
				    	$fieldString = "CONCAT_WS('" . ReportField :: COMPOSITE_SEPARATOR . "', $preffix`" . implode("`, $preffix`", $mappedField->field) . "`)";
				    	break;
				    case ReportField::DIRECT_QUERY:
				    	$select[] = "{$mappedField->field} AS `{$field}`";
				    	$selectionAdded = true;
				    	break;
				    case ReportField::SINGLE_REFERENCE:
				    	if ($this->_isDirectQuery($mappedField->field)) {
				    	    $fieldString = $this->_extractQuery($mappedField->field);
				    	} else {
							$fieldString = "{$mappedField->table}.`{$mappedField->field}`";
				    	}
				    	$joins["{$mappedField}"] = $mappedField;
				    	break;
				    case ReportField::MULTIPLE_REFERENCE:
				    	if ($this->_isDirectQuery($mappedField->field)) {
				    		$multipleFields = explode('|', $this->_extractQuery($mappedField->field));
				    		$fieldString = "GROUP_CONCAT(DISTINCT " . implode(" SEPARATOR '{$groupSeparator}'), GROUP_CONCAT(DISTINCT ", $multipleFields) . " SEPARATOR '{$groupSeparator}')";
				    	} else {
				    		$tableName = isset($mappedField->ternaryTable) ? $mappedField->ternaryTable->table : $mappedField->table;
							$fieldString = "GROUP_CONCAT(DISTINCT {$tableName}.`{$mappedField->field}` SEPARATOR '{$groupSeparator}')";
				    	}

				    	$joins["{$mappedField}"] = $mappedField;
				    	break;
				    default;
				    	break;
				}

				if (!$selectionAdded) {
					$select[] = "{$fieldString} AS `{$field}`";
				}
            }
        }

        return array (
            $joins,
            $select
        );
    }

    private function _buildQuery($select, $joins, $where) {
        $fields = implode(",\n", $select);
        $joined = array ();
        $table = "hs_hr_employee";
        if (!empty ($joins)) {
            $table .= " ";
            foreach ($joins as $join) {
            	$joinTable = isset($join->alias) ? $join->alias : $join->table;
                if (in_array($joinTable, $joined)) {
                    continue;
                }

				$tableName = "{$join->table}";
				if (isset($join->ternaryTable)) {
					$alias = $join->ternaryTable->alias;
                    $tableName = "(`{$tableName}` INNER JOIN {$join->ternaryTable->table} " . ((!empty($alias)) ? "AS `{$alias}` " : '') .
                    		"ON " . ((!empty($alias)) ? $alias : $join->ternaryTable->table) . ".`{$join->ternaryTable->pk}` = {$join->ternaryTable->fkTable}.`{$join->ternaryTable->fk}`)";
                }

                $table .= "LEFT JOIN {$tableName} \n\tON " . ReportField::LEFT_TABLE . ".`{$join->fk}` = {$join->table}.`{$join->pk}`\n";

                $joined[] = $join->table;
            }
        }

        $query = "SELECT {$fields} \nFROM {$table} \n";

        if (!empty ($where)) {
            $where = 'WHERE (' . implode(") AND \n(", $where) . ')';
            $query .= $where;
        }

        $query .= "\nGROUP BY (hs_hr_employee.`emp_number`)";

        return $query;
    }

    private function _isDirectQuery($value) {
        return (substr($value, 0, 1) == '{' && substr($value, strlen($value) - 1, 1) == '}'); // TODO: Replace with a regular expression
    }

    private function _extractQuery($query) {
        return substr($query, 1, strlen($query) - 2);
    }

    public function getReporingMethods() {

        $query = "SELECT * FROM `ohrm_emp_reporting_method`";

        $dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

        $reportingMethods = array();

        while ($row = mysql_fetch_array($result)) {
            $reportingMethods[$row['reporting_method_id']] = $row['reporting_method_name'];
        }

        return $reportingMethods;

    }

}
