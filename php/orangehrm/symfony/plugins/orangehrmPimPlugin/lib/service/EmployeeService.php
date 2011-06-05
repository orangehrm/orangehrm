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
     * Retrieve Picture
     * @param int $empNumber
     * @returns Collection
     * @throws PIMServiceException
     */
    public function getPicture($empNumber) {
        try {
            return $this->employeeDao->getPicture($empNumber);
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
     * Get dependents for given employee
     * @param int $empNumber Employee Number
     * @return array Dependents as array
     */
    public function getDependents($empNumber) {
        try {
            return $this->employeeDao->getDependents($empNumber);
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
     * Check if employee with given empNumber is a supervisor
     * @param int $empNumber
     * @return bool - True if given employee is a supervisor, false if not
     */
    public function isSupervisor($empNumber) {
        try {
            return $this->employeeDao->isSupervisor($empNumber);
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
     * save WorkExperience
     * @param EmpWorkExperience $empWorkExp
     * @returns boolean
     */
    public function saveWorkExperience(EmpWorkExperience $empWorkExp) {
        return $this->employeeDao->saveWorkExperience($empWorkExp);
    }

    /**
     * Get WorkExperience
     * @param int $empNumber
     * @param int $sequenceNo
     * @returns Collection/WorkExperience
     */
    public function getWorkExperience($empNumber, $sequenceNo = null) {
        return $this->employeeDao->getWorkExperience($empNumber, $sequenceNo);
    }

    /**
     * Delete WorkExperiences
     * @param int $empNumber
     * @param array() $workExperienceToDelete
     * @returns boolean
     */
    public function deleteWorkExperience($empNumber, $workExperienceToDelete) {
        return $this->employeeDao->deleteWorkExperience($empNumber, $workExperienceToDelete);
    }

    /**
     * Get Education
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/Education
     */
    public function getEducation($empNumber, $eduCode = null) {
        return $this->employeeDao->getEducation($empNumber, $eduCode);
    }

    /**
     * Delete Education
     * @param int $empNumber
     * @param array() $educationToDelete
     * @returns boolean
     */
    public function deleteEducation($empNumber, $educationToDelete) {
        return $this->employeeDao->deleteEducation($empNumber, $educationToDelete);
    }

    /**
     * save Education
     * @param EmployeeEducation $education
     * @returns boolean
     */
    public function saveEducation(EmployeeEducation $education) {
        return $this->employeeDao->saveEducation($education);
    }

    /**
     * Get Skill
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/Skill
     */
    public function getSkill($empNumber, $skillCode = null) {
        return $this->employeeDao->getSkill($empNumber, $skillCode);
    }

    /**
     * Delete Skill
     * @param int $empNumber
     * @param array() $skillToDelete
     * @returns boolean
     */
    public function deleteSkill($empNumber, $skillToDelete) {
        return $this->employeeDao->deleteSkill($empNumber, $skillToDelete);
    }

    /**
     * save Skill
     * @param EmployeeSkill $skill
     * @returns boolean
     */
    public function saveSkill(EmployeeSkill $skill) {
        return $this->employeeDao->saveSkill($skill);
    }

    /**
     * Get Language
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/Language
     */
    public function getLanguage($empNumber, $languageCode = null, $languageType = null) {
        return $this->employeeDao->getLanguage($empNumber, $languageCode, $languageType);
    }

    /**
     * Delete Language
     * @param int $empNumber
     * @param array() $languageToDelete (array of langCode->LangType)
     * @return int - number of records deleted. False if did not delete anything.
     */
    public function deleteLanguage($empNumber, $languagesToDelete) {
        return $this->employeeDao->deleteLanguage($empNumber, $languagesToDelete);
    }

    /**
     * save Language
     * @param EmployeeLanguage $language
     * @returns boolean
     */
    public function saveLanguage(EmployeeLanguage $language) {
        return $this->employeeDao->saveLanguage($language);
    }

    /**
     * Get License
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/License
     */
    public function getLicense($empNumber, $licenseCode = null) {
        return $this->employeeDao->getLicense($empNumber, $licenseCode);
    }

    /**
     * Delete License
     * @param int $empNumber
     * @param array() $licenseToDelete
     * @returns boolean
     */
    public function deleteLicense($empNumber, $licenseToDelete) {
        return $this->employeeDao->deleteLicense($empNumber, $licenseToDelete);
    }

    /**
     * save License
     * @param EmployeeLicense $license
     * @returns boolean
     */
    public function saveLicense(EmployeeLicense $license) {
        return $this->employeeDao->saveLicense($license);
    }

    /**
     * Get attachment
     * @param type $empNumber - employee number
     * @param type $screen - screen attached to
     */
    public function getAttachments($empNumber, $screen) {
        try {
            return $this->employeeDao->getAttachments($empNumber, $screen);
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
     * Returns list of supervisors (employees having at least one subordinate)
     *
     * @returns Collection
     * @throws DaoException
     */
    public function getSupervisorList() {
        try {
            return $this->employeeDao->getSupervisorList();
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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

    public function getEmployeeYearsOfService($employeeId, $currentDate) {
        $employee = $this->getEmployee($employeeId);
        if (!($employee instanceof Employee)) {
            throw new PIMServiceException("Employee with employeeId " . $employeeId . " not found!");
        }
        return $this->getDurationInYears($employee->getJoinedDate(), $currentDate);
    }

    public function getDurationInYears($fromDate, $toDate) {
        $years = 0;
        $secondsOfYear = 60 * 60 * 24 * 365;
        $secondsOfMonth = 60 * 60 * 24 * 30;

        if ($fromDate != "" && $toDate != "") {
            $fromDateTimeStamp = strtotime($fromDate);
            $toDateTimeStamp = strtotime($toDate);

            $timeStampDiff = 0;
            if ($toDateTimeStamp > $fromDateTimeStamp) {
                $timeStampDiff = $toDateTimeStamp - $fromDateTimeStamp;

                $years = floor($timeStampDiff / $secondsOfYear);

                //adjusting the months
                $remainingMonthsTimeStamp = ($timeStampDiff - ($years * $secondsOfYear));
                $months = round($remainingMonthsTimeStamp / $secondsOfMonth);
                $yearByMonth = ($months > 0) ? $months / 12 : 0;

                if (floor($years + $yearByMonth) == ($years + $yearByMonth)) {
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
        $noOfDays = floor($timeStampDiff / $secondsOfDay);
        $fromYear = date("Y", strtotime($fromDate));
        $toYear = date("Y", strtotime($toDate));
        $ctr = $fromYear;
        $daysCount = 0;

        list($fY, $fM, $fD) = explode("-", $fromDate);
        list($tY, $tM, $tD) = explode("-", $toDate);
        $years = $tY - $fY;

        $temp = date("Y") . "-" . $fM . "-" . $fD;
        $newFromMonthDay = date("m-d", strtotime("-1 day", strtotime($temp)));
        $toMonthDay = $tM . "-" . $tD;

        if ($newFromMonthDay != $toMonthDay) {
            if (($tM - $fM) < 0) {
                $years--;
            } elseif (($tM - $fM) == 0 && ($tD - $fD) < -1) {
                $years--;
            }
        }
        //this sections commented off if there is a need to extend it further
        /* while($ctr < $toYear) {
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
          } */

        /* $years = floor($timeStampDiff/$secondsOfYear);

          $remainingMonthsTimeStamp = ($timeStampDiff - ($years * $secondsOfYear));
          $remainingDays = $remainingMonthsTimeStamp/$secondsOfDay; */
        /* $remainingDays = $noOfDays - $daysCount;

          $months = floor(($remainingDays/$numberOfDaysInYear) * $numberOfMonths);
          $yearByMonth = ($months > 0)? ($months/12):0;
          $years = $years + $yearByMonth; */
        return $years;
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
     * Retrieve Tax Exemption for a given employee number
     * @param int $empNumber
     * @returns EmpUsTaxExemption
     * @throws PIMServiceException
     */
    public function getEmployeeTaxExemptions($empNumber) {
        try {
            return $this->employeeDao->getEmployeeTaxExemptions($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Save Tax Exemptions
     * @param EmpUsTaxExemption $empUsTaxExemption
     * @returns boolean
     * @throws PIMServiceException
     */
    public function saveEmployeeTaxExemptions(EmpUsTaxExemption $empUsTaxExemption) {
        try {
            return $this->employeeDao->saveEmployeeTaxExemptions($empUsTaxExemption);
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
     * Get membership details for given employee
     * @param int $empNumber Employee Number
     * @return array membership details as array
     */
    public function getMembershipDetails($empNumber) {
        try {
            return $this->employeeDao->getMembershipDetails($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get membership details for given employee
     * @param int $empNumber Employee Number
     * @return array membership details as array
     */
    public function getMembershipDetail($empNumber, $membershipType, $membership) {
        try {
            return $this->employeeDao->getMembershipDetail($empNumber, $membershipType, $membership);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Member Details
     * @param array() $membershipsToDelete
     * @returns boolean
     * @throws PIMServiceException
     */
    public function deleteMembershipDetails($membershipsToDelete) {

        try {
            foreach ($membershipsToDelete as $membershipToDelete) {

                $tempArray = explode(" ", $membershipToDelete);

                $empNumber = $tempArray[0];
                $membershipType = $tempArray[1];
                $membership = $tempArray[2];

                $this->employeeDao->deleteMembershipDetails($empNumber, $membershipType, $membership);
            }

            return true;
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
     * Save Reporting Method
     * @param ReportingMethod $reportingMethod
     * @returns ReportingMethod $reportingMethod
     * @throws PIMServiceException
     */
    public function saveReportingMethod(ReportingMethod $reportingMethod) {
        try {
            return $this->employeeDao->saveReportingMethod($reportingMethod);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Reporting Method for a given reporting method id
     * @param int $reportingMethodId
     * @return ReportingMethod doctrine object
     */
    public function getReportingMethod($reportingMethodId) {
        try {
            return $this->employeeDao->getReportingMethod($reportingMethodId);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get Reporting Method List
     * @return ReportingMethod doctrine Collection
     */
    public function getReportingMethodList() {
        try {
            return $this->employeeDao->getReportingMethodList();
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * get supervisor list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getSupervisorListForEmployee($empNumber) {

        try {
            return $this->employeeDao->getSupervisorListForEmployee($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * get subordinate list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getSubordinateListForEmployee($empNumber) {

        try {
            return $this->employeeDao->getSubordinateListForEmployee($empNumber);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Get report to details object
     * @param int $supNumber $subNumber $reportingMethod
     * @return ReportTo object
     */
    public function getReportToObject($supNumber, $subNumber, $reportingMethod) {

        try {
            return $this->employeeDao->getReportToObject($supNumber, $subNumber, $reportingMethod);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete reportTo object
     * @param $supOrSubListToDelete array
     * @return boolean
     */
    public function deleteReportToObject($supOrSubListToDelete) {

        try {
            foreach ($supOrSubListToDelete as $supOrSubToDelete) {

                $tempArray = explode(" ", $supOrSubToDelete);

                $supNumber = $tempArray[0];
                $subNumber = $tempArray[1];
                $reportingMethod = $tempArray[2];

                $state = $this->employeeDao->deleteReportToObject($supNumber, $subNumber, $reportingMethod);
            }
            return $state;
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

}