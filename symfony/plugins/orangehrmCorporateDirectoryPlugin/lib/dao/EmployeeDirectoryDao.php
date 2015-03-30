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

class EmployeeDirectoryDao extends BaseDao{
    
    /**
     * Mapping of search field names to database fields
     * @var array
     */
    protected static $searchMapping = array(
            'id' => 'e.employee_id',
            'employee_name' => 'concat_ws(\' \', e.emp_firstname,e.emp_middle_name,e.emp_lastname)',
            'middleName' => 'e.emp_middle_name',
            'lastName' => 'e.emp_lastName',
            'job_title' => 'j.job_title',
        'emp_work_telephone' => 'e.emp_work_telephone', 
        'emp_work_email' => 'e.emp_work_email',
            'employee_status' => 'es.estat_name',
            'sub_unit' => 'cs.name',
            'termination' => 'e.termination_id',
            'location' => 'l.location_id',
            'employee_id_list' => 'e.emp_number',
    );

    /**
     * Mapping of sort field names to database fields
     * @var array
     */
    protected static $sortMapping = array(       
            'firstName' => 'e.emp_firstname',
            'middleName' => 'e.emp_middle_name',
            'firstMiddleName' => array('e.emp_firstname','e.emp_middle_name'),
            'lastName' => 'e.emp_lastName',
            'fullName' => array('e.emp_firstname', 'e.emp_middle_name', 'e.emp_lastName'),
            'jobTitle' => 'j.job_title',
            'empLocation' => 'loc.name',
            'employeeStatus' => 'es.name',
            'subDivision' => 'cs.name',
    );
    
    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return array
     */
    public function getSearchEmployeeCount(array $filters = null) {

        $select = '';
        $query = '';
        $bindParams = array();
        $orderBy = '';

        $this->_getEmployeeListQuery($select, $query, $bindParams, $orderBy, null, null, $filters);

        $countQuery = 'SELECT COUNT(*) FROM (' . $select . ' ' . $query . ' ) AS countqry';

        if (sfConfig::get('sf_logging_enabled')) {
            $msg = 'COUNT: ' . $countQuery;
            if (count($bindParams) > 0 ) {
                $msg .=  ' (' . implode(',', $bindParams) . ')';
            }
            sfContext::getInstance()->getLogger()->info($msg);
        }

        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare($countQuery);
        $result = $statement->execute($bindParams);
        $count = 0;
        if ($result) {
            if ($statement->rowCount() > 0) {
                $count = $statement->fetchColumn();
            }
        }

        return $count;
    }
    
       /**
     * Get SQL Query which can be used fetch employee list with the given
     * sorting and filtering options
     *
     * @param &$select select part of query
     * @param &$query  query
     * @param &$bindParams bind params for query
     * @param &$orderBy order by part of query
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return none
     */
    private function _getEmployeeListQuery(&$select, &$query, array &$bindParams, &$orderBy,
            $sortField = null, $sortOrder = null, array $filters = null) {

        $searchByTerminated = EmployeeSearchForm::WITHOUT_TERMINATED;

        /*
	     * Using direct SQL since it is difficult to use Doctrine DQL or RawSQL to get an efficient
	     * query taht searches the company structure tree and supervisors.
        */
        
        
        
        $select = 'SELECT e.emp_number AS empNumber, e.employee_id AS employeeId, ' .
                'e.emp_firstname AS firstName, e.emp_lastname AS lastName, ' .
                'e.emp_middle_name AS middleName, e.termination_id AS terminationId, ' .
                'cs.name AS subDivision, cs.id AS subDivisionId,' .
                'j.job_title AS jobTitle, j.id AS jobTitleId, j.is_deleted AS isDeleted, ' .
                'es.name AS employeeStatus, es.id AS employeeStatusId, '.
                'e.emp_hm_telephone,  e.emp_mobile, e.emp_work_telephone, e.emp_work_email, e.emp_oth_email, '.

                'GROUP_CONCAT(DISTINCT loc.id, \'##\',loc.name) AS locationIds';
              

        $query = 'FROM hs_hr_employee e ' .
                '  LEFT JOIN ohrm_subunit cs ON cs.id = e.work_station ' .
                '  LEFT JOIN ohrm_job_title j on j.id = e.job_title_code ' .
                '  LEFT JOIN ohrm_employment_status es on e.emp_status = es.id ' .
                '  LEFT JOIN hs_hr_emp_locations l ON l.emp_number = e.emp_number ' .
                '  LEFT JOIN ohrm_location loc ON l.location_id = loc.id';

        /* search filters */
        $conditions = array();

        if (!empty($filters)) {

            $filterCount = 0;

            foreach ($filters as $searchField=>$searchBy ) {
                if (!empty($searchField) && !empty($searchBy)
                        && array_key_exists($searchField, self::$searchMapping) ) {
                    $field = self::$searchMapping[$searchField];

                    if ($searchField == 'sub_unit') {

                        /*
                         * Not efficient if searching substations by more than one value, but
                         * we only have the facility to search by one value in the UI.
                        */
                        $conditions[] =  'e.work_station IN (SELECT n.id FROM ohrm_subunit n ' .
                                'INNER JOIN ohrm_subunit p WHERE n.lft >= p.lft ' .
                                'AND n.rgt <= p.rgt AND p.id = ? )';
                        $bindParams[] = $searchBy;
                    } else if ($searchField == 'id') {
                        $conditions[] = ' e.employee_id LIKE ? ';
                        $bindParams[] = $searchBy;
                    } else if ($searchField == 'job_title') {
                        $conditions[] = ' j.id = ? ';
                        $bindParams[] = $searchBy;
                    } else if ($searchField == 'employee_status') {
                        $conditions[] = ' es.id = ? ';
                        $bindParams[] = $searchBy;
                    } else if ($searchField == 'employee_id_list') {
                        $conditions[] = ' e.emp_number IN (' . implode(',', $searchBy) . ') ';
                    } else if ($searchField == 'employee_name') {
                        $conditions[] = $field . ' LIKE ? ';
                        // Replace multiple spaces in string with wildcards
                        $value = preg_replace('!\s+!', '%', $searchBy);
                        $bindParams[] = '%' . $value . '%';
                    }elseif( $searchField == 'location' ){
                        //print_r($filters['location']);
                        
                        $locIds = $filters['location'];
                        $idArray = explode(',', $locIds);   
                       
                        if($idArray[0] > 0){  
                             $conditions[] = ' l.location_id IN (' . $searchBy . ') ';
                        
                        }
                    }
                    
                    $filterCount++;

                    if ($searchField == 'termination') {
                        $searchByTerminated = $searchBy;
                    }
                }
            }
        }

        /* If not searching by employee status, hide terminated employees */
        if ($searchByTerminated == EmployeeSearchForm::WITHOUT_TERMINATED) {
            $conditions[] = "( e.termination_id IS NULL )";
        }

        if ($searchByTerminated == EmployeeSearchForm::ONLY_TERMINATED) {
            $conditions[] = "( e.termination_id IS NOT NULL )";
        }

        /* Build the query */
        $numConditions = 0;
        foreach ($conditions as $condition) {
            $numConditions++;

            if ($numConditions == 1) {
                $query .= ' WHERE ' . $condition;
            } else {
                $query .= ' AND ' . $condition;
            }
        }

        /* Group by */
        $query .= ' GROUP BY e.emp_number ';

        /* sorting */
        $order = array();

        if( !empty($sortField) && !empty($sortOrder) ) {
            if( array_key_exists($sortField, self::$sortMapping) ) {
                $field = self::$sortMapping[$sortField];
                if (is_array($field)) {
                    foreach ($field as $name) {
                        $order[$name] = $sortOrder;
                    }
                } else {
                    $order[$field] = $sortOrder;
                }
            }
        }

        /* Default sort by emp_number, makes resulting order predictable, useful for testing */
        $order['e.emp_lastname'] = 'asc';

        $order['e.emp_number'] = 'asc';

        /* Build the order by part */
        $numOrderBy = 0;
        foreach ($order as $field=>$dir) {
            $numOrderBy++;
            if ($numOrderBy == 1) {
                $orderBy = ' ORDER BY ' . $field . ' ' . $dir;
            } else {
                $orderBy .= ', ' . $field . ' ' . $dir;
            }
        }
        
    }
    
    
    
    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param EmployeeSearchParameterHolder $parameterHolder
     */
    public function searchEmployees(EmployeeSearchParameterHolder $parameterHolder) {
        
        $sortField  = $parameterHolder->getOrderField();
        $sortOrder  = $parameterHolder->getOrderBy();
        $offset     = $parameterHolder->getOffset();
        $limit      = $parameterHolder->getLimit();
        $filters    = $parameterHolder->getFilters();
        $returnType = $parameterHolder->getReturnType();

        $select = '';
        $query = '';
        $bindParams = array();
        $orderBy = '';

        $this->_getEmployeeListQuery($select, $query, $bindParams, $orderBy,
                $sortField, $sortOrder, $filters);

        $completeQuery = $select . ' ' . $query . ' ' . $orderBy;

        if (!is_null($offset) && !is_null($limit)) {
            $completeQuery .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        if (sfConfig::get('sf_logging_enabled')) {
            $msg = $completeQuery;
            if (count($bindParams) > 0 ) {
                $msg .=  ' (' . implode(',', $bindParams) . ')';
            }
            sfContext::getInstance()->getLogger()->info($msg);
        }
//print_r($completeQuery);
//print_r($bindParams);
        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare($completeQuery);
        $result = $statement->execute($bindParams);
       
        if ($returnType == EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT) {
            $employees = new Doctrine_Collection(Doctrine::getTable('Employee'));

            if ($result) {
                while ($row = $statement->fetch() ) {
                    //print_r();
                    $employee = new Employee();

                    $employee->setEmpNumber($row['empNumber']);
                    $employee->setEmployeeId($row['employeeId']);
                    $employee->setFirstName($row['firstName']);
                    $employee->setMiddleName($row['middleName']);
                    $employee->setLastName($row['lastName']);
                    $employee->setTerminationId($row['terminationId']);
                    $employee->setEmpHmTelephone($row['emp_hm_telephone']);
                    $employee->setEmpMobile($row['emp_mobile']);
                    $employee->setEmpWorkTelephone($row['emp_work_telephone']);
                    $employee->setEmpWorkEmail($row['emp_work_email']);
                    $employee->setEmpOthEmail($row['emp_oth_email']);
 
                    $jobTitle = new JobTitle();
                    $jobTitle->setId($row['jobTitleId']);
                    $jobTitle->setJobTitleName($row['jobTitle']);
                    $jobTitle->setIsDeleted($row['isDeleted']);
                    $employee->setJobTitle($jobTitle);

                    $employeeStatus = new EmploymentStatus();
                    $employeeStatus->setId($row['employeeStatusId']);
                    $employeeStatus->setName($row['employeeStatus']);
                    $employee->setEmployeeStatus($employeeStatus);

                    $workStation = new SubUnit();
                    $workStation->setName($row['subDivision']);
                    $workStation->setId($row['subDivisionId']);
                    $employee->setSubDivision($workStation);

                    $supervisorList = isset($row['supervisors'])?$row['supervisors']:'';

                    if (!empty($supervisorList)) {

                        $supervisors = new Doctrine_Collection(Doctrine::getTable('Employee'));

                        $supervisorArray = explode(',', $supervisorList);
                        foreach ($supervisorArray as $supervisor) {
                            list($first, $middle, $last) = explode('##', $supervisor);
                            $supervisor = new Employee();
                            $supervisor->setFirstName($first);
                            $supervisor->setMiddleName($middle);
                            $supervisor->setLastName($last);
                            $employee->supervisors[] = $supervisor;
                        }
                    }

                    $locationList = $row['locationIds'];

                    if (!empty($locationList)) {

    //                    $locations = new Doctrine_Collection(Doctrine::getTable('EmpLocations'));

                        $locationArray = explode(',', $locationList);
                        foreach ($locationArray as $location) {
                            list($id, $name) = explode('##', $location);
                            $empLocation = new Location();
                            $empLocation->setId($id);
                            $empLocation->setName($name);
                            $employee->locations[] = $empLocation;
                        }
                    }

                    $employees[] = $employee;
                }
            }
        }
        else {
            return $statement->fetchAll();
        }
        return $employees;

    }

}