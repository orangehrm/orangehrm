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

/**
 * EmployeeTable class
 *
 */
class EmployeeTable extends Doctrine_Table {

    /**
     * Mapping of search field names to database fields
     * @var array
     */
    protected static $searchMapping = array(
            'employeeId' => 'e.employee_id',
            'firstName' => 'e.emp_firstname',
            'middleName' => 'e.emp_middle_name',
            'lastName' => 'e.emp_lastName',
            'jobTitle' => 'j.jobtit_name',
            'employeeStatus' => 'es.estat_name',
            'subDivision' => 'cs.title',
            'supervisor' => 's.emp_firstname',
 			'supervisorId' => 's.emp_firstname',
        );

    /**
     * Mapping of sort field names to database fields
     * @var array
     */
    protected static $sortMapping = array(
            'employeeId' => 'e.employee_id',
            'firstName' => 'e.emp_firstname',
            'middleName' => 'e.emp_middle_name',
            'lastName' => 'e.emp_lastName',
            'fullName' => array('e.emp_firstname', 'e.emp_middle_name', 'e.emp_lastName'),
            'jobTitle' => 'j.jobtit_name',
            'employeeStatus' => 'es.estat_name',
            'subDivision' => 'cs.title',
            'supervisor' => array('s.emp_firstname', 's.emp_lastname'),
        );


    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return array
     */
	public function getEmployeeList($sortField = 'empNumber', $sortOrder = 'asc', array $filters = null, $offset = null, $limit = null) {

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
    	    if (count($bindParams) > 0 ){
    	        $msg .=  ' (' . implode(',', $bindParams) . ')';
    	    }
            sfContext::getInstance()->getLogger()->info($msg);
        }

        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare($completeQuery);
        $result = $statement->execute($bindParams);
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $employees = new Doctrine_Collection(Doctrine::getTable('Employee'));

        if ($result) {
            while ($row = $statement->fetch() ) {
                $employee = new Employee();

                $employee->setEmpNumber($row['empNumber']);
                $employee->setEmployeeId($row['employeeId']);
                $employee->setFirstName($row['firstName']);
                $employee->setMiddleName($row['middleName']);
                $employee->setLastName($row['lastName']);

                $jobTitle = new JobTitle();
                $jobTitle->setId($row['jobTitleId']);
                $jobTitle->setName($row['jobTitle']);
                $employee->setJobTitle($jobTitle);

                $employeeStatus = new EmployeeStatus();
                $employeeStatus->setId($row['employeeStatusId']);
                $employeeStatus->setName($row['employeeStatus']);
                $employee->setEmployeeStatus($employeeStatus);

                $workStation = new CompanyStructure();
                $workStation->setTitle($row['subDivision']);
                $workStation->setId($row['subDivisionId']);
                $employee->setSubDivision($workStation);

                $supervisorList = $row['supervisors'];

                if (!empty($supervisorList)){

                    $supervisors = new Doctrine_Collection(Doctrine::getTable('Employee'));

                    $supervisorArray = explode(',', $supervisorList);
                    foreach ($supervisorArray as $supervisor){
                        list($first, $last) = explode(' ', $supervisor);
                        $supervisor = new Employee();
                        $supervisor->setFirstName($first);
                        $supervisor->setLastName($last);
                        $employee->supervisors[] = $supervisor;
                    }
                }

                $employees[] = $employee;
            }
        }

		return $employees;

	}

    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return array
     */
	public function getEmployeeCount(array $filters = null) {

	    $select = '';
	    $query = '';
	    $bindParams = array();
	    $orderBy = '';

	    $this->_getEmployeeListQuery($select, $query, $bindParams, $orderBy, null, null, $filters);

        $countQuery = 'SELECT COUNT(*) FROM (' . $select . ' ' . $query . ' ) AS countqry';

    	if (sfConfig::get('sf_logging_enabled')) {
    	    $msg = 'COUNT: ' . $countQuery;
    	    if (count($bindParams) > 0 ){
    	        $msg .=  ' (' . implode(',', $bindParams) . ')';
    	    }
            sfContext::getInstance()->getLogger()->info($msg);
        }

        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare($countQuery);
        $result = $statement->execute($bindParams);
        $count = 0;
        if ($result){
            if ($statement->rowCount() > 0){
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

	    $searchByStatus = false;

	    /*
	     * Using direct SQL since it is difficult to use Doctrine DQL or RawSQL to get an efficient
	     * query taht searches the company structure tree and supervisors.
	     */
		$select = 'SELECT e.emp_number AS empNumber, e.employee_id AS employeeId, ' .
		          'e.emp_firstname AS firstName, e.emp_lastname AS lastName, ' .
		          'e.emp_middle_name AS middleName, ' .
		          'cs.title AS subDivision, cs.id AS subDivisionId,' .
		          'j.jobtit_name AS jobTitle, j.jobtit_code AS jobTitleId, ' .
		          'es.estat_name AS employeeStatus, es.estat_code AS employeeStatusId, ' .
		          'GROUP_CONCAT(s.emp_firstname, \' \', s.emp_lastname ORDER BY erep_reporting_mode ) ' .
		            ' AS supervisors ';

		$query = 'FROM hs_hr_employee e ' .
		         '  LEFT JOIN hs_hr_compstructtree cs ON cs.id = e.work_station ' .
		         '  LEFT JOIN hs_hr_job_title j on j.jobtit_code = e.job_title_code ' .
		         '  LEFT JOIN hs_hr_empstat es on e.emp_status = es.estat_code ' .
		         '  LEFT JOIN hs_hr_emp_reportto rt on e.emp_number = rt.erep_sub_emp_number ' .
		         '  LEFT JOIN hs_hr_employee s on s.emp_number = rt.erep_sup_emp_number ';

		/* search filters */
		$conditions = array();

        if (!empty($filters)) {

            $filterCount = 0;

            foreach ($filters as $searchField=>$searchBy ){
                if (!empty($searchField) && !empty($searchBy)
                        && array_key_exists($searchField, self::$searchMapping) ) {
                    $field = self::$searchMapping[$searchField];
                    $value = '%' . $searchBy . '%';

                    if ($searchField == 'subDivision'){

                        /*
                         * Not efficient if searching substations by more than one value, but
                         * we only have the facility to search by one value in the UI.
                         */
                        $conditions[] =  'e.work_station IN (SELECT n.id FROM hs_hr_compstructtree n ' .
                                         'INNER JOIN hs_hr_compstructtree p WHERE n.lft >= p.lft ' .
                                         'AND n.rgt <= p.rgt AND p.title LIKE ? )';
                        $bindParams[] = $value;
                    } else if ($searchField == 'supervisorId') {
                        $conditions[] = ' s.emp_number = ? ';
                        $bindParams[] = $searchBy;
                    } else {
                        $conditions[] = $field . ' LIKE ? ';
                        $bindParams[] = $value;
                    }
                    $filterCount++;

                    if ($searchField == 'employeeStatus') {
                        $searchByStatus = true;
                    }
                }
            }
        }

        /* If not searching by employee status, hide terminated employees */
        if (!$searchByStatus) {
            $conditions[] = "( e.emp_status != 'EST000' OR e.emp_status IS NULL )";
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

        if( !empty($sortField) && !empty($sortOrder) ){
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
        $order['e.emp_number'] = 'asc';

        /* Sort subordinates direct first, then indirect, then by supervisor name */
        $order['rt.erep_reporting_mode'] = 'asc';

        if ($sortField != 'supervisor'){
            $order['s.emp_firstname'] = 'asc';
            $order['s.emp_lastname'] = 'asc';
        }
        $order['e.emp_number'] = 'asc';

        /* Build the order by part */
        $numOrderBy = 0;
        foreach ($order as $field=>$dir) {
            $numOrderBy++;
            if ($numOrderBy == 1){
                $orderBy = ' ORDER BY ' . $field . ' ' . $dir;
            } else {
                $orderBy .= ', ' . $field . ' ' . $dir;
            }
        }
	}


	/**
	 * Delete Employees with given IDs.
	 *
	 * @param array $ids Array of employee ids to delete
	 * @return int Number of employees deleted.
	 */
	public function delete(array $ids) {
        $count = Doctrine_Query::create()
          ->delete()
          ->from('Employee')
          ->whereIn('empNumber', $ids)
          ->execute();

        return $count;
	}

//	public function getEmployeeListQuery($sortField = null, $sortOrder = null, array $filters = null) {
//
//	    $searchByStatus = false;
//
//	    /*
//	     * Using RawSQL since it is difficult to use DQL to get an efficient query for the
//	     * employee list search
//	     */
//	    Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_PORTABILITY, Doctrine::PORTABILITY_ALL ^ Doctrine::PORTABILITY_EXPR);
//
///*
// $this->createQuery('e')
//-                     ->select('e.*, sd.title, j.name, es.name, s.*, ss.firstName, ss.middleName, ss.lastName')
//-                     ->leftJoin('e.SubDivision sd')
//-                     ->leftjoin('e.JobTitle j')
//-                     ->leftjoin('e.EmployeeStatus es')
//-                     ->leftjoin('e.Supervisors s')
//-                     ->leftjoin('s.Supervisor ss');
//
// */
//
//		$query = new Doctrine_Query();
//		$query->select('e.emp_number, e.employee_id, e.emp_firstname, e.emp_lastname, ' .
//		               'e.emp_middle_name, cs.title, j.jobtit_name, es.estat_name, ' .
//					   's.emp_firstname')
//
//		               //'CONCAT(s.emp_firstname, \' \', s.emp_lastname ORDER BY s.emp_firstname, rt.erep_reporting_mode, s.emp_lastname )')
//		        ->from("Employee e")
//                ->leftJoin('e.subDivision cs')
//                ->leftjoin('e.jobTitle j')
//                ->leftjoin('e.employeeStatus es')
//                ->leftjoin('e.supervisors rt');
//		//$query->addComponent('e', 'Employee e');
//		//$query->addComponent('cs', 'e.subDivision cs');
//		//$query->addComponent('j', 'e.jobTitle j');
//		//$query->addComponent('es', 'e.employeeStatus es');
//        $query->andwhere('e.work_station IN (SELECT n.id FROM CompanyStructure n ' .
//                         'INNER JOIN CompanyStructure p WHERE n.lft >= p.lft ' .
//                         'AND n.rgt <= p.rgt AND p.title LIKE ? )', '%O%');
//
//		echo $query->getSql();die;
//
//		$query = 'SELECT e.emp_number AS empNumber, e.employee_id AS empId, ' .
//		         'e.emp_firstname AS firstName, e.emp_lastname AS lastName, ' .
//		         'e.emp_middle_name AS middleName, cs.title AS subDivision, ' .
//		         'j.jobtit_name AS jobTitle, es.estat_name AS employeeStatus, ' .
//			     's.emp_firstname AS supervisorFirst, s.emp_lastname AS supervisorLast ' .
//		         'FROM hs_hr_employee e ' .
//		         '  LEFT JOIN hs_hr_compstructtree cs ON cs.id = e.work_station ' .
//		         '  LEFT JOIN hs_hr_job_title j on j.jobtit_code = e.job_title_code ' .
//		         '  LEFT JOIN hs_hr_empstat es on e.emp_status = es.estat_code ' .
//		         '  LEFT JOIN hs_hr_emp_reportto rt on e.emp_number = rt.erep_sub_emp_number ' .
//		         '  LEFT JOIN hs_hr_employee s on s.emp_number = rt.erep_sup_emp_number ';
//
///*
//
//		$query = new Doctrine_RawSql();
//		$query->select('{e.emp_number}, {e.employee_id}, {e.emp_firstname}, {e.emp_lastname}, ' .
//		               '{e.emp_middle_name}, {cs.title}, {j.jobtit_name}, {es.estat_name}, ' .
//					   's.emp_firstname')
//
//		               //'CONCAT(s.emp_firstname, \' \', s.emp_lastname ORDER BY s.emp_firstname, rt.erep_reporting_mode, s.emp_lastname )')
//		      ->from("hs_hr_employee e LEFT JOIN hs_hr_compstructtree cs on cs.id = e.work_station " .
//		             "  LEFT JOIN hs_hr_job_title j on j.jobtit_code = e.job_title_code " .
//		             "  LEFT JOIN hs_hr_empstat es on e.emp_status = es.estat_code " .
//		      		 "  LEFT JOIN hs_hr_emp_reportto rt on e.emp_number = rt.erep_sub_emp_number " .
//		             "  LEFT JOIN hs_hr_employee s on s.emp_number = rt.erep_sup_emp_number " );
//		$query->addComponent('e', 'Employee e');
//		$query->addComponent('cs', 'e.subDivision cs');
//		$query->addComponent('j', 'e.jobTitle j');
//		$query->addComponent('es', 'e.employeeStatus es');
//
// */
//		//$query->addComponent('xx', 'xx');
//		//$query->addComponent('s', 'e.supervisors s');
//
//		/* search filters */
//        if (!empty($filters)) {
//
//            $filterCount = 0;
//
//            foreach ($filters as $searchField=>$searchBy ){
//                if (!empty($searchField) && !empty($searchBy)
//                        && array_key_exists($searchField, self::$searchMapping) ) {
//                    $field = self::$searchMapping[$searchField];
//                    $value = '%' . $searchBy . '%';
//
//                    if ($searchField == 'subDivision'){
//
//                        /*
//                         * Not efficient if searching substations by more than one value, but
//                         * we only have the facility to search by one value in the UI.
//                         */
//                        $query->andwhere('e.work_station IN (SELECT n.id FROM hs_hr_compstructtree n ' .
//                                         'INNER JOIN hs_hr_compstructtree p WHERE n.lft >= p.lft ' .
//                                         'AND n.rgt <= p.rgt AND p.title LIKE ? )', $value);
//                    } else if ($searchField == 'supervisorId') {
//                        $query->andwhere('s.emp_number = ?', $searchBy);
//                    } else {
//                        if ($filterCount == 0) {
//                            $query->where($field . ' LIKE ?', $value);
//                        } else {
//                            $query->andwhere($field . ' LIKE ?', $value);
//                        }
//                    }
//                    $filterCount++;
//
//                    if ($searchField == 'employeeStatus') {
//                        $searchByStatus = true;
//                    }
//                }
//            }
//        }
//
//        /* If not searching by employee status, hide terminated employees */
//        if (!$searchByStatus) {
//            $query->andwhere('( e.emp_status != ? OR e.emp_status IS NULL )', 'EST000');
//        }
//
//        /* sorting */
//        if( !empty($sortField) && !empty($sortOrder) ){
//            if( array_key_exists($sortField, self::$sortMapping) ) {
//                $field = self::$sortMapping[$sortField];
//                if (is_array($field)) {
//                    foreach ($field as $key=>$name) {
//                        $query->addOrderBy($name . ' ' . $sortOrder);
//                    }
//                } else {
//                    $query->orderBy($field . ' ' . $sortOrder);
//                }
//            }
//        }
//
//        /* Default sort by emp_number, makes resulting order predictable, useful for testing */
//        $query->addOrderBy('e.emp_number', 'asc');
//
//        /* Sort subordinates direct first, then indirect, then by supervisor name */
//        $query->addOrderBy('rt.erep_reporting_mode', 'asc');
//
//        if ($sortField != 'supervisor'){
//            $query->addOrderBy('s.emp_firstname', 'asc');
//            $query->addOrderBy('s.emp_lastname', 'asc');
//        }
//        $query->addGroupBy('e.emp_number');
//echo $query->getSql();die;
//		return $query;
//	}
}