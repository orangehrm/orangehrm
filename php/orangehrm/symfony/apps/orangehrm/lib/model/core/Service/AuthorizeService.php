<?php
/* 
 * 
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
 * 
 */

/**
 * Description of CompanyService
 *
 * @author orange
 */
class AuthorizeService extends BaseService {
	
	const AUTHORIZE_ROLE_ADMIN 			= 'Admin';
	const AUTHORIZE_ROLE_SUPERVISOR 	= 'Supervisor';
	const AUTHORIZE_ROLE_ESS 			= 'ESS';
	const AUTHORIZE_ROLE_PROJECT_ADMIN 	= "ProjectAdmin";
    const AUTHORIZE_ROLE_MANAGER 		= 'Manager';
    const AUTHORIZE_ROLE_DIRECTOR 		= 'Director';

    const YES = 'Yes';
    const NO = 'No';
	
    
	public $roleAdmin 			= "Admin";
	public $roleSupervisor 		= "Supervisor";
	public $roleESS 			= "ESS";
	public $roleProjectAdmin 	= "ProjectAdmin";
    public $roleManager 		= "Manager";
    public $roleDirector 		= "Director";
    public $roleAcceptor 		= "Acceptor";
    public $roleOfferer 		= "Offerer";

        private $employeeService;

	private $employeeID;
	private $isAdmin;
	private $roles;

        /**
         * Get EmployeeService
         * @returns EmployeeService
         */
        public function getEmployeeService() {
            if (is_null($this->employeeService)) {
                $this->employeeService = new EmployeeService();
                $this->employeeService->setEmployeeDao(new EmployeeDao());
            }
            return $this->employeeService;
        }

        /**
         * Set EmployeeService
         * @param EmployeeService $employeeService
         */
        public function setEmployeeService(EmployeeService $employeeService) {
            $this->employeeService = $employeeService;
        }

	public function setEmployeeId($employeeId) {
		$this->employeeID = $employeeId;
	}

	public function getEmployeeId() {
		return $this->employeeID;
	}

	public function setIsAdmin($isAdmin) {
		$this->isAdmin = $isAdmin;
	}

	public  function getIsAdmin() {
		return $this->isAdmin;
	}

	public function setRoles($roles) {
		$this->roles = $roles;
	}

	public  function getRoles() {
		return $this->roles;
	}

	/**
	 * Class contructor
	 *
	 * @param String $employeeId
	 * @param String $isAdmin
	 */
	public function __construct($employeeId, $isAdmin) {
		$this->setEmployeeId($employeeId);
		$this->setIsAdmin($isAdmin);

		$this->setRoles($this->_roles());
	}

	/**
	 * Constructs roles
	 *
	 * @return boolean[]
	 */
	private function _roles() {
		$roles 		= null;
		$isAdmin 	= $this->getIsAdmin();
		$empId 		= $this->getEmployeeId();

		if ($isAdmin === self::YES) {
			$roles[$this->roleAdmin] = true;
		} else {
			$roles[$this->roleAdmin] = false;
		}

		$roles[$this->roleSupervisor] 	= $this->_checkIsSupervisor();
		$roles[$this->roleProjectAdmin] = $this->_checkIsProjectAdmin();
        $roles[$this->roleManager] 		= $this->_checkIsManager();
        $roles[$this->roleDirector] 	= $this->_checkIsDirector();
        $roles[$this->roleAcceptor] 	= $this->_checkIsAcceptor();
        $roles[$this->roleOfferer] 		= $this->_checkIsOfferer();

		if (!empty($empId)) {
			$roles[$this->roleESS] = true;
		} else {
			$roles[$this->roleESS] = false;
		}

		return $roles;
	}

	/**
	 * Check whether there are any subordinates
	 *
	 * @return boolean
	 */
	private function _checkIsSupervisor() {
                $isSupervisor = false;

                if (!empty($this->employeeID)) {
                    $isSupervisor = $this->getEmployeeService()->isSupervisor($this->employeeID);
                }

                return $isSupervisor;

	}

	/**
	 * Check whether the user is a project admin
	 *
	 * @param int $projectId Project for which to check. If not given, all projects are checked.
	 * @return boolean
	 */
	private function _checkIsProjectAdmin($projectId = null) {

		return false;
	}

    /**
     * Check whether the user is a Manager
     *
     * @return boolean
     */
    private function _checkIsManager() {
    	 
        return false;
    }

    /**
     * Check whether the user is a Director
     *
     * @return boolean True if a director, false otherwise
     */
    private function _checkIsDirector() {

		return false;
    }

    /**
     * Check whether the user is an Acceptor that can approve job offers
     *
     * @return boolean True if an acceptor, false otherwise
     */
    private function _checkIsAcceptor() {

		return false;

    }

    /**
     * Check whether the user is an Offerer that can approve job offers
     *
     * @return boolean True if an offerer, false otherwise
     */
    private function _checkIsOfferer() {

		return false;

    }


	/**
	 * Checks whether an admin
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		return $this->_chkRole($this->roleAdmin);
	}

	/**
	 * Checks whether an supervisor
	 *
	 * @return boolean
	 */
	public function isSupervisor() {
		return $this->_chkRole($this->roleSupervisor);
	}

	/**
	 * Checks whether a project admin
	 *
	 * @return boolean true if a project admin. False otherwise
	 */
	public function isProjectAdmin() {
		return $this->_chkRole($this->roleProjectAdmin);
	}

    /**
     * Checks whether a Manager
     *
     * @return boolean true if a Manager. False otherwise
     */
    public function isManager() {
        return $this->_chkRole($this->roleManager);
    }

    /**
     * Checks whether a Director
     *
     * @return boolean true if a Director. False otherwise
     */
    public function isDirector() {
        return $this->_chkRole($this->roleDirector);
    }

    /**
     * Checks whether an Acceptor
     *
     * @return boolean true if an Acceptor. False otherwise
     */
    public function isAcceptor() {
        return $this->_chkRole($this->roleAcceptor);
    }

    /**
     * Checks whether an Offerer
     *
     * @return boolean true if an Offerer. False otherwise
     */
    public function isOfferer() {
        return $this->_chkRole($this->roleOfferer);
    }

	/**
	 * Checks whether an ESS
	 *
	 * @return boolean
	 */
	public function isESS() {
		return $this->_chkRole($this->roleESS);
	}

	/**
	 * Checks whether the particular employee is
	 * the supervisor of the subordinate concerned
	 *
	 * @param unknown_type $subordinateId
	 * @return boolean
	 */
	public function isTheSupervisor($subordinateId) {

		return false;
	}

	/**
	 * Checks whether employee is a project admin of the
	 * given project.
	 *
	 * @param int $projectId The project id
	 * @return bool true if a project admin, false otherwise
	 */
	public function isProjectAdminOf($projectId) {
		return $this->_checkIsProjectAdmin($projectId);
	}

	/**
	 * Test whether element at pos of the array is equal to match
	 *
	 * @param Array array
	 * @param String match
	 * @param int pos
	 */
	private function searchArray($array, $match, $pos) {
		if ($array[$pos] == $match) {
				return true;
		}
		return false;
	}

	/**
	 * Delegates all checks for all is<Role>
	 * functions
	 *
	 * @param String $role
	 * @return boolean
	 */
	private function _chkRole($role) {
		$roles = $this->getRoles();

		if (isset($roles[$role]) && $roles[$role]) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the first role out of the array of
	 * roles sent
	 *
	 * @param String[] $roleArr
	 * @return String/boolean
	 */
	public function firstRole($roleArr) {
		for ($i=0; $i<count($roleArr); $i++) {
			if ($this->_chkRole($roleArr[$i])){
				return $roleArr[$i];
			}
		}

		return false;
	}
	
	public function isActionPermitted($action) {
	    
	    $permitted = false;
	    
	    switch ($action) {
	    	
	    	case 'TCP' :	if (!$this->isAdmin() && !$this->isSupervisor()) {
	    	    				$permitted = false;
	    					} else {
	    					    $permitted = true;
	    					}
	    					break;
	    					
	    	case 'CST' :	if (!$this->isAdmin() && !$this->isSupervisor()) {
	    	    				$permitted = false;
	    					} else {
	    					    $permitted = true;
	    					}
	    					break;
	    					
	    	case 'PAC' :	if (!$this->isAdmin() && !$this->isProjectAdmin()) {
	    	    				$permitted = false;
	    					} else {
	    					    $permitted = true;
	    					}
	    					break;
	        
	        default : 		if ($this->isAdmin()) {
	            	        	$permitted = true;
	        		  		} else {
	        		      		$permitted = false;
	        		  		}
	        		  		break;
	        
	    }
	    
	    return $permitted;	    
	    
	}
}