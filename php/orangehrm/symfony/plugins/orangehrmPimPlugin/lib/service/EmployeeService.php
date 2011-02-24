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
 * Employee Service
 *
 */
class EmployeeService extends BaseService {

    private $employeeDao;

    /**
     * Get Employee Dao
     * @return EmployeeDao
     */
    public function getEmployeeDao() {
        return $this->employeeDao;
    }

    /**
     * Set Employee Dao
     * @param EmployeeDao $employeeDao
     * @return void
     */
    public function setEmployeeDao(EmployeeDao $employeeDao) {
        $this->employeeDao = $employeeDao;
    }

    /**
     * Construct
     */
    public function __construct() {
        $this->employeeDao = new EmployeeDao();
    }

    /**
     * Add a new employee
     *
     * @param Employee $employee
     * @return boolean
     * @throws PIMServiceException
     */
    public function addEmployee(Employee $employee) {
        try {
            return $this->employeeDao->addEmployee($employee);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get employee with given empNumber
     * @param int $empNumber Employee number
     * @return Employee
     */
    public function getEmployee($empNumber) {
        try {
            return $this->employeeDao->getEmployee($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get the default employee id to be used for next employee being
     * added to the system.
     *
     * @return employee id based on empNumber
     */
    public function getDefaultEmployeeId() {
        $idGenService = new IDGeneratorService();
        $idGenService->setEntity(new Employee());
        return $idGenService->getNextID(false);
    }

    /**
     * Retrieve Past Job Titles for a given Emp Number
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getPastJobTitles($empNumber) {
        try {
            return $this->employeeDao->getPastJobTitles($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Past Sub Divisions by empNumber
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getPastSubdivisions($empNumber) {
        try {
            return $this->employeeDao->getPastSubdivisions($empNumber);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Past Locations by empNumber
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getPastLocations($empNumber) {
        try {
            return $this->employeeDao->getPastLocations($empNumber);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve AttachmentList by empNumber
     * @param int $empNumber
     * @returns Collection
     * @throws DaoException
     */
    public function getAttachmentList($empNumber) {
        try {
            return $this->employeeDao->getAttachmentList($empNumber);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Picture
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getPicture($empNumber) {
        try {
            return $this->employeeDao->getPicture($empNumber);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Attachment
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAttachment($empNumber, $attachId) {
        try {
            return $this->employeeDao->getAttachment($empNumber, $attachId);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save Personal Details
     * @param Employee $employee
     * @param boolean $isESS
     * @returns boolean
     * @throws PIMServiceException
     */
    public function savePersonalDetails(Employee $employee, $isESS = false) {
        try {
            return $this->employeeDao->savePersonalDetails($employee, $isESS);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save Contact Details
     * @param Employee $employee
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveContactDetails(Employee $employee) {
        try {
            return $this->employeeDao->saveContactDetails($employee);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save Contact Details
     * @param Employee $employee
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveJobDetails(Employee $employee) {
        try {
            return $this->employeeDao->saveJobDetails($employee);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Available Location
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAvailableLocations($empNumber) {
        try {
            return $this->employeeDao->getAvailableLocations($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Available Location
     * @param int $empNumber
     * @param String $locationCode
     * @returns Collection
     * @throws PIMServiceException
     */
    public function assignLocation($empNumber, $locationCode) {
        try {
            return $this->employeeDao->assignLocation($empNumber, $locationCode);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Location
     * @param int $empNumber
     * @param String $locationCode
     * @returns boolean
     * @throws PIMServiceException
     */
    public function removeLocation($empNumber, $locationCode) {
        try {
            return $this->employeeDao->removeLocation($empNumber, $locationCode);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete JobTitleHistory
     * @param int $empNumber
     * @param String $jobTitlesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteJobTitleHistory($empNumber, $jobTitlesToDelete) {
        try {
            return $this->employeeDao->deleteJobTitleHistory($empNumber, $jobTitlesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete SubDivisionHistory
     * @param int $empNumber
     * @param String $subDivisionsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteSubDivisionHistory($empNumber, $subDivisionsToDelete) {
        try {
            return $this->employeeDao->deleteSubDivisionHistory($empNumber, $subDivisionsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete LocationHistory
     * @param int $empNumber
     * @param String $locationsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteLocationHistory($empNumber, $locationsToDelete) {
        try {
            return $this->employeeDao->deleteLocationHistory($empNumber, $locationsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Update JobHistory
     * @param int $empNumber
     * @param String $params
     * @returns boolean
     * @throws PIMServiceException
     */
    public function updateJobHistory($empNumber, $params) {
        try {
            return $this->employeeDao->updateJobHistory($empNumber, $params);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Update JobHistory
     * @param int $empNumber
     * @param array() $contractsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteContracts($empNumber, $contractsToDelete) {
        try {
            return $this->employeeDao->deleteContracts($empNumber, $contractsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmpContract
     * @param EmpContract $empContract
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmpContract(EmpContract $empContract) {
        try {
            return $this->employeeDao->saveEmpContract($empContract);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Licenses
     * @param int $empNumber
     * @param array() $licensesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteLicenses($empNumber, $licensesToDelete) {
        try {
            return $this->employeeDao->deleteLicenses($empNumber, $licensesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeeLicense
     * @param EmployeeLicense $employeeLicense
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeLicense(EmployeeLicense $employeeLicense) {
        try {
            return $this->employeeDao->saveEmployeeLicense($employeeLicense);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeeSkill
     * @param EmployeeSkill $empSkills
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeSkill(EmployeeSkill $empSkills) {
        try {
            return $this->employeeDao->saveEmployeeSkill($empSkills);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Skills
     * @param int $empNumber
     * @param array() $skillsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteSkills($empNumber, $skillsToDelete) {
        try {
            return $this->employeeDao->deleteSkills($empNumber, $skillsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeeEducation
     * @param EmployeeEducation $empEdu
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeEducation(EmployeeEducation $empEdu) {
        try {
            return $this->employeeDao->saveEmployeeEducation($empEdu);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Education
     * @param int $empNumber
     * @param array() $educationsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteEducation($empNumber, $educationsToDelete) {
        try {
            return $this->employeeDao->deleteEducation($empNumber, $educationsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeeLanguage
     * @param EmployeeLanguage $empLang
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeLanguage(EmployeeLanguage $empLang) {
        try {
            return $this->employeeDao->saveEmployeeLanguage($empLang);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Languages
     * @param int $empNumber
     * @param array() $languagesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteLanguages($empNumber, $languagesToDelete) {
        try {
            return $this->employeeDao->deleteLanguages($empNumber, $languagesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeeMemberDetail
     * @param EmployeeMemberDetail $empMemberDetail
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeMemberDetail(EmployeeMemberDetail $empMemberDetail) {
        try {
            return $this->employeeDao->saveEmployeeMemberDetail($empMemberDetail);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Memberships
     * @param int $empNumber
     * @param array() $membershipsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteMemberships($empNumber, $membershipsToDelete) {
        try {
            return $this->employeeDao->deleteMemberships($empNumber, $membershipsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmpBasicsalary
     * @param EmpBasicsalary $empBasicsalary
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmpBasicsalary(EmpBasicsalary $empBasicsalary) {
        try {
            return $this->employeeDao->saveEmpBasicsalary($empBasicsalary);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Salary
     * @param int $empNumber
     * @param array() $salaryToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteSalary($empNumber, $salaryToDelete) {
        try {
            return $this->employeeDao->deleteSalary($empNumber, $salaryToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve All Licenses
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAvailableLicenses($empNumber) {
        try {
            return $this->employeeDao->getAvailableLicenses($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve All Memberships
     * @param int $empNumber
     * @param String $membershipType
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAvailableMemberships($empNumber, $membershipType) {
        try {
            return $this->employeeDao->getAvailableMemberships($empNumber, $membershipType);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve All Education List
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAvailableEducationList($empNumber) {
        try {
            return $this->employeeDao->getAvailableEducationList($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve All Skills for an employee number
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getAvailableSkills($empNumber) {
        try {
            return $this->employeeDao->getAvailableSkills($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Unassigned Currency List
     * @param int $empNumber
     * @param String $salaryGrade
     * @param boolean $asArray
     * @returns Collection
     * @throws DaoException
     */
    public function getUnAssignedCurrencyList($empNumber, $salaryGrade, $asArray = false) {
        try {
            return $this->employeeDao->getUnAssignedCurrencyList($empNumber, $salaryGrade, $asArray);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Workshift for a given employee number
     * @param int $empNumber
     * @returns EmployeeWorkShift
     * @throws PIMServiceException
     */
    public function getWorkShift($empNumber) {
        try {
            return $this->employeeDao->getWorkShift($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $empNumber Employee Number
     * @return array Emergency Contacts as array
     */
    public function getEmergencyContacts($empNumber) {
        try {
            return $this->employeeDao->getEmergencyContacts($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Emergency contacts
     * @param int $empNumber
     * @param array() $emergencyContactsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteEmergencyContacts($empNumber, $emergencyContactsToDelete) {
        try {
            return $this->employeeDao->deleteEmergencyContacts($empNumber, $emergencyContactsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Direct Debit
     * @param int $empNumber
     * @param array() $directDebitToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteDirectDebit($empNumber, $directDebitToDelete) {
        try {
            return $this->employeeDao->deleteDirectDebit($empNumber, $directDebitToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete WorkExperiences
     * @param int $empNumber
     * @param array() $workExperienceToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteWorkExperiences($empNumber, $workExperienceToDelete) {
        try {
            return $this->employeeDao->deleteWorkExperiences($empNumber, $workExperienceToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Immigration
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteImmigration($empNumber, $entriesToDelete) {

        try {
            return $this->employeeDao->deleteImmigration($empNumber, $entriesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
        
    }

    /**
     * Delete Dependents
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteDependents($empNumber, $entriesToDelete) {
        try {
            return $this->employeeDao->deleteDependents($empNumber, $entriesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Children
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteChildren($empNumber, $entriesToDelete) {
        try {
            return $this->employeeDao->deleteChildren($empNumber, $entriesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Supervisors
     * @param int $empNumber
     * @param array() $supervisorsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteSupervisors($empNumber, $supervisorsToDelete) {
        try {
            return $this->employeeDao->deleteSupervisors($empNumber, $supervisorsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Subordinates
     * @param int $empNumber
     * @param array() $subordinatesToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteSubordinates($empNumber, $subordinatesToDelete) {
        try {
            return $this->employeeDao->deleteSubordinates($empNumber, $subordinatesToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Photo
     * @param int $empNumber
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deletePhoto($empNumber) {
        try {
            return $this->employeeDao->deletePhoto($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Attachments
     * @param int $empNumber
     * @param array $attachmentsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteAttachments($empNumber, $attachmentsToDelete) {
        try {
            return $this->employeeDao->deleteAttachments($empNumber, $attachmentsToDelete);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save EmployeePicture
     * @param EmpPicture $empPicture
     * @returns boolean
     * @throws PIMServiceException
     */
    function saveEmployeePicture(EmpPicture $empPicture) {
        try {
            return $this->employeeDao->saveEmployeePicture($empPicture);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * save immigration
     * @param EmpPassport $empPassport
     * @returns boolean
     */
    public function saveEmployeePassport(EmpPassport $empPassport) {
        return $this->employeeDao->saveEmployeePassport($empPassport);
    }

    /**
     * Get EmpPassport
     * @param int $empNumber
     * @param int $sequenceNo
     * @returns Collection/EmpPassport
     */
    public function getEmployeePassport($empNumber, $sequenceNo = null) {
        return $this->employeeDao->getEmployeePassport($empNumber, $sequenceNo);
    }
    
    /**
     * Returns EmployeePicture by Emp Number
     * @param int $empNumber
     * @returns EmpPicture
     * @throws PIMServiceException
     */
    function readEmployeePicture($empNumber) {
        try {
            return $this->employeeDao->readEmployeePicture($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Returns Employee List
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getEmployeeList($orderField = 'empNumber', $orderBy = 'ASC') {
        try {
            return $this->employeeDao->getEmployeeList($orderField, $orderBy);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Search Employee
     * @param String $field
     * @param String $value
     * @return Collection
     * @throws PIMServiceException
     */
    public function searchEmployee($field, $value) {
        try {
            return $this->employeeDao->searchEmployee($field, $value);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Returns Employee Count
     * @returns int
     * @throws PIMServiceException
     */
    public function getEmployeeCount() {
        try {
            return $this->employeeDao->getEmployeeCount();
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Returns Supervisor Employee List
     * @param int $supervisorId
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getSupervisorEmployeeList($supervisorId) {
        try {
            return $this->employeeDao->getSupervisorEmployeeList($supervisorId);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Returns Employee List as Json
     * @param boolean $workShift
     * @returns String
     * @throws PIMServiceException
     */
    public function getEmployeeListAsJson($workShift = false) {
        try {
            return $this->employeeDao->getEmployeeListAsJson($workShift);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Supervisor Employee Chain
     * @param int $supervisorId
     * @throws PIMServiceException
     */
    public function getSupervisorEmployeeChain($supervisorId) {
        try {
            return $this->getEmployeeDao()->getSupervisorEmployeeChain($supervisorId);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Filtering Employee by Sub units
     * @param Collection $employeeList
     * @param String $subUnitId
     * @returns array()
     * @throws PIMServiceException
     */
    public function filterEmployeeListBySubUnit($employeeList, $subUnitId) {
        try {
            if (empty($subUnitId) || $subUnitId == CompanyStructure::ROOT_ID) {
                return $employeeList;
            }

            if (empty($employeeList)) {
                $employeeList = $this->getEmployeeList();
            }

            $filteredList = array();
            foreach ($employeeList as $employee) {
                if ($employee->getWorkStation() == $subUnitId) {
                    $filteredList[] = $employee;
                }
            }
            return $filteredList;
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Employee
     * @param array $empList
     * @returns int
     * @throws PIMServiceException
     */
    public function deleteEmployee($empList = array()) {
        try {
            return $this->employeeDao->deleteEmployee($empList);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Direct Debit entries for given employee
     * @param  $empNumber Employee Number
     * @return Array of direct debit entries
     */
    public function getEmployeeDirectDebit($empNumber) {
        try {
            return $this->employeeDao->getEmployeeDirectDebit($empNumber);
        } catch(Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Checks if the given employee id is in use.
     * @throws PIMServiceException
     * @param  $employeeId
     * @return ?#M#P#CEmployeeService.employeeDao.isEmployeeIdInUse
     */
    public function isEmployeeIdInUse($employeeId) {
        try {
            return $this->employeeDao->isEmployeeIdInUse($employeeId);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Checks if employee with same name already exists
     *
     * @throws PIMServiceException
     * @param  $first
     * @param  $middle
     * @param  $last
     * @return ?#M#P#CEmployeeService.employeeDao.checkForEmployeeWithSameName
     */

    public function checkForEmployeeWithSameName($first, $middle, $last) {
        try {
            return $this->employeeDao->checkForEmployeeWithSameName($first, $middle, $last);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }

    }

    /**
     * Save direct debit
     *
     * @throws PIMServiceException
     * @param  $directDebit
     * @return ?#M#P#CEmployeeService.employeeDao.saveDirectDebit
     */
    public function saveDirectDebit($directDebit) {
        try {
            return $this->employeeDao->saveDirectDebit($directDebit);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }

    }

    /**
     * Save tax information
     *
     * @throws PIMServiceException
     * @param  $directDebit
     * @return ?#M#P#CEmployeeService.employeeDao.saveDirectDebit
     */
    public function saveEmpTaxInfo($tax) {
        try {
            return $this->employeeDao->saveEmpTaxInfo($tax);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }

    }

    public function getEmployeeYearsOfService($employeeId, $currentDate) {
        $employee = $this->getEmployee($employeeId);
        if (! ($employee instanceof Employee) ) {
            throw new PIMServiceException("Employee with employeeId " . $employeeId . " not found!");
        }
        return $this->getDurationInYears($employee->getJoinedDate(), $currentDate);
    }

    public function getDurationInYears($fromDate, $toDate) {
        $years = 0;
        $secondsOfYear = 60 * 60 * 24 * 365;
        $secondsOfMonth = 60 * 60 * 24 * 30;

        if($fromDate != "" && $toDate != "") {
            $fromDateTimeStamp = strtotime($fromDate);
            $toDateTimeStamp = strtotime($toDate);

            $timeStampDiff = 0;
            if($toDateTimeStamp > $fromDateTimeStamp) {
                $timeStampDiff = $toDateTimeStamp - $fromDateTimeStamp;

                $years = floor($timeStampDiff/$secondsOfYear);

                //adjusting the months
                $remainingMonthsTimeStamp = ($timeStampDiff - ($years * $secondsOfYear));
                $months = round($remainingMonthsTimeStamp/$secondsOfMonth);
                $yearByMonth = ($months > 0)? $months/12:0;
                
                if(floor($years + $yearByMonth) == ($years + $yearByMonth)) {
                    $years = $this->getBorderPeriodMonths($fromDate, $toDate);
                } else {
                    $years = $years + $yearByMonth;
                }
                
            }

        }
        return $years;
    }

    private function getBorderPeriodMonths($fromDate, $toDate) {
        $years = 0;
        $secondsOfDay = 60 * 60 * 24;
        $numberOfDaysInYear = 365;
        $secondsOfYear = $secondsOfDay * $numberOfDaysInYear;
        $numberOfMonths = 12;

        $timeStampDiff = strtotime($toDate) - strtotime($fromDate);
        $noOfDays = floor($timeStampDiff/$secondsOfDay);
        $fromYear = date("Y", strtotime($fromDate));
        $toYear = date("Y", strtotime($toDate));
        $ctr = $fromYear;
        $daysCount = 0;

        list($fY, $fM, $fD) = explode("-", $fromDate);
        list($tY, $tM, $tD) = explode("-", $toDate);
        $years = $tY - $fY;

        $temp = date("Y"). "-". $fM. "-". $fD ;
        $newFromMonthDay = date("m-d", strtotime("-1 day", strtotime($temp)));
        $toMonthDay = $tM . "-"  . $tD;

        if($newFromMonthDay != $toMonthDay) {
            if (($tM - $fM) < 0) {
              $years--;
            } elseif (($tM - $fM) == 0 && ($tD - $fD) < -1) {
                $years--;
            }
        }
        //this sections commented off if there is a need to extend it further
        /*while($ctr < $toYear) {
            $daysCount = $daysCount + $numberOfDaysInYear;
            //this is for leap year
            if($ctr % 4 == 0) {
                $daysCount = $daysCount + 1;
            }
            if($noOfDays < $daysCount) {
                $daysCount = $daysCount - $numberOfDaysInYear;
                if($ctr % 4 == 0) {
                    $daysCount = $daysCount - 1;
                }
                break;
            }

            $years++;
            $ctr++;
        }*/

        /*$years = floor($timeStampDiff/$secondsOfYear);

        $remainingMonthsTimeStamp = ($timeStampDiff - ($years * $secondsOfYear));
        $remainingDays = $remainingMonthsTimeStamp/$secondsOfDay;*/
        /*$remainingDays = $noOfDays - $daysCount;

        $months = floor(($remainingDays/$numberOfDaysInYear) * $numberOfMonths);
        $yearByMonth = ($months > 0)? ($months/12):0;
        $years = $years + $yearByMonth;*/
        return $years;
    }
}