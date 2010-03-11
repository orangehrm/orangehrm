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
class PerformanceReviewTable extends Doctrine_Table {
	/**
     * Mapping of search field names to database fields
     * @var array
     */
    protected static $searchMapping = array(
            'ReviewPeriodFrom' => 'e.emp_firstname',
            'ReviewPeriodTo' => 'e.emp_middle_name',
            'JobTitle' => 'j.jobtit_name',
            'SubUnit' => 'cs.title',
            'Employee' => 'e.emp_firstname',
            'Reviewer' => 's.emp_firstname',
        );

    /**
     * Mapping of sort field names to database fields
     * @var array
     */
    protected static $sortMapping = array(
            'firstName' => 'e.emp_firstname',
            'middleName' => 'e.emp_middle_name',
            'lastName' => 'e.emp_lastName',
            'fullName' => array('e.emp_firstname', 'e.emp_middle_name', 'e.emp_lastName'),
            'jobTitle' => 'j.jobtit_name',
            'subDivision' => 'cs.title',
            'supervisor' => array('s.emp_firstname', 's.emp_lastname'),
        );


        /*
         * SELECT jobtit_name
FROM hs_hr_job_title AS b LEFT JOIN hs_hr_employee AS ab ON b.jobtit_code=ab.job_title_code
LEFT JOIN hs_hr_performance_review AS a ON ab.emp_number=a.employee_id
ORDER BY jobtit_name;

SELECT jobtit_name, CONCAT_WS(' ', emp_firstname, emp_middle_name, emp_lastname) AS empname
FROM hs_hr_job_title AS b LEFT JOIN hs_hr_employee AS ab ON b.jobtit_code=ab.job_title_code
LEFT JOIN hs_hr_performance_review AS a ON ab.emp_number=a.employee_id
ORDER BY jobtit_name;
         */
        
    /**
     * Get employee list after sorting and filtering using given parameters.
     *
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return array
     */
	public function getEmployeePerformanceReviewList($sortField = 'empNumber', $sortOrder = 'asc', array $filters = null) {

	    $query = $this->getEmployeePerformanceReviewListQuery($sortField, $sortOrder, $filters);
		return $query->execute();
	}
    /**
     * Get Doctrine Query which can be used fetch employee list with the given
     * sorting and filtering options
     *
     * @param array $sortField
     * @param $sortOrder
     * @param $filters
     * @return array
     */
	public function getEmployeePerformanceReviewListQuery($sortField = null, $sortOrder = null, array $filters = null) {
		print_r($filters);
	    $searchByStatus = false;

	    /*
	     * Using RawSQL since it is difficult to use DQL to get an efficient query for the
	     * employee list search
	     */
	    
		$query = new Doctrine_RawSql();
		$query->select('{e.emp_number}, {e.employee_id}, {e.emp_firstname}, {e.emp_lastname}, ' .
		               '{e.emp_middle_name}, {cs.title}, {j.jobtit_name}, {es.estat_name}, ' .
		               '{s.emp_firstname}, {s.emp_lastname}, rt.erep_reporting_mode')
		      ->from("hs_hr_employee e LEFT JOIN hs_hr_compstructtree cs on cs.id = e.work_station " .
		             "  LEFT JOIN hs_hr_job_title j on j.jobtit_code = e.job_title_code " .
		             "  LEFT JOIN hs_hr_empstat es on e.emp_status = es.estat_code " .
		      		 "  LEFT JOIN hs_hr_emp_reportto rt on e.emp_number = rt.erep_sub_emp_number " .
		             "  LEFT JOIN hs_hr_employee s on s.emp_number = rt.erep_sup_emp_number " );
		$query->addComponent('e', 'Employee e');
		$query->addComponent('cs', 'e.subDivision cs');
		$query->addComponent('j', 'e.jobTitle j');
		$query->addComponent('es', 'e.employeeStatus es');
		$query->addComponent('s', 'e.supervisors s');

		/* search filters */
        if (!empty($filters)) {

            $filterCount = 0;

            foreach ($filters as $searchField => $searchBy ){
                if (!empty($searchField) && !empty($searchBy) && array_key_exists($searchField, self::$searchMapping) ) {
                    $field = self::$searchMapping[$searchField];
                    $value = '%' . $searchBy . '%';

                    if ($searchField == 'subDivision'){

                        /*
                         * Not efficient if searching substations by more than one value, but
                         * we only have the facility to search by one value in the UI.
                         */
                        $query->andwhere('e.work_station IN (SELECT n.id FROM hs_hr_compstructtree n ' .
                                         'INNER JOIN hs_hr_compstructtree p WHERE n.lft >= p.lft ' .
                                         'AND n.rgt <= p.rgt AND p.title LIKE ? )', $value);
                    } else if ($searchField == 'supervisorId') {
                        $query->andwhere('s.emp_number = ?', $searchBy);
                    } else {
                        if ($filterCount == 0) {
                            $query->where($field . ' LIKE ?', $value);
                        } else {
                            $query->andwhere($field . ' LIKE ?', $value);
                        }
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
            $query->andwhere('( e.emp_status != ? OR e.emp_status IS NULL )', 'EST000');
        }

        /* sorting */
        if( !empty($sortField) && !empty($sortOrder) ){
            if( array_key_exists($sortField, self::$sortMapping) ) {
                $field = self::$sortMapping[$sortField];
                if (is_array($field)) {
                    foreach ($field as $key=>$name) {
                        $query->addOrderBy($name . ' ' . $sortOrder);
                    }
                } else {
                    $query->orderBy($field . ' ' . $sortOrder);
                }
            }
        }

        /* Default sort by emp_number, makes resulting order predictable, useful for testing */
        $query->addOrderBy('e.emp_number', 'asc');

        /* Sort subordinates direct first, then indirect, then by supervisor name */
        $query->addOrderBy('rt.erep_reporting_mode', 'asc');

        if ($sortField != 'supervisor'){
            $query->addOrderBy('s.emp_firstname', 'asc');
            $query->addOrderBy('s.emp_lastname', 'asc');
        }

		return $query;
	}
	
	public function getEmployeeSuggestionList ($searchBy) {
		$value = '%' . $searchBy . '%';
		$q = new Doctrine_RawSql();
		$q->select('{e.*}')
		->from('hs_hr_employee e')
		->where('e.emp_firstname' . ' LIKE ?', $value)
		//->where('e.emp_firstname = ' . $jobTitleId)
		->addComponent('e', 'Employee');
		$employees = $q->execute();
		return $employees;
	}
	
	public function getReviewerSuggestionList ($searchBy) {
		$value = '%' . $searchBy . '%';
		$q = new Doctrine_RawSql();
		
		$q->select('{e.*}')
		->from('hs_hr_emp_reportto jk')
		->leftjoin('hs_hr_employee e ON e.emp_number = jk.erep_sup_emp_number')
		->andwhere('e.emp_firstname' . ' LIKE ?', $value)
		->addComponent('e', 'Employee');
		$query = $q->execute();
		return $query;
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
}