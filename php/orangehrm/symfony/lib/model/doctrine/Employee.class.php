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
 * Employee class
 */
class Employee extends BaseEmployee {
	
	/**
	 * Returns the full name of employee, (first middle last)
	 * 
	 * @return String Full Name 
	 */
	public function getFullName() {
		
	    $fullName = trim($this->firstName) . " " . trim($this->middleName);
	    $fullName = trim( trim($fullName) . " " . trim($this->lastName) ); 
		
		return $fullName;
	}

	/**
	 * Returns the first and last names of employee
	 *
	 * @return String
	 */
	public function getFirstAndLastNames() {

	    $fullName = trim($this->firstName) . " " . trim($this->lastName);

		return $fullName;
	}
	
	/**
	 * Gets the names of all the supervisors of this employee as a comma separated string
	 * Only the first and last name are used.
	 * 
	 * @return String String containing comma separated list of supervisor names. 
	 *                Empty string if employee has no supervisors
	 */
	public function getSupervisorNames() {
	    $supervisorNames = array();
	    
	    foreach ($this->supervisors as $supervisor ){
	        $supervisorNames[] = trim($supervisor->firstName . ' ' . $supervisor->lastName); 
	    }
	    
	    return implode(',', $supervisorNames);
	}

    /**
     * Returns emergency contact with given sequence no, or null if not found.
     *
     * @param int $seqNo Sequence no
     *
     * @return EmergencyContact Emergency contact with given sequence no.
     */
    public function getEmergencyContact($seqNo) {

        $emergencyContact = null;

        foreach ($this->emergencyContacts as $contact) {
            if ($contact->seqno == $seqNo) {
                $emergencyContact = $contact;
                break;
            }
        }

        return ($emergencyContact);
    }

    /**
     * Returns dependent with given sequence no, or null if not found.
     *
     * @param int $seqNo Sequence no
     *
     * @return EmpDependent Dependent with given sequence no.
     */
    public function getDependent($seqNo) {

        $dependent = null;

        foreach ($this->dependents as $dep) {
            if ($dep->seqno == $seqNo) {
                $dependent = $dep;
                break;
            }
        }

        return ($dependent);
    }

    /**
     * Returns child with given sequence no, or null if not found.
     *
     * @param int $seqNo Sequence no
     *
     * @return EmpChild Child with given sequence no.
     */
    public function getChild($seqNo) {

        $child = null;

        foreach ($this->children as $currentChild) {
            if ($currentChild->seqno == $seqNo) {
                $child = $currentChild;
                break;
            }
        }

        return ($child);
    }

    /**
     * Returns immigration document with given sequence no, or null if not found.
     *
     * @param int $seqNo Sequence no
     *
     * @return EmpPassport Immigration document with given sequence no.
     */
    public function getImmigrationDocument($seqNo) {

        $immigrationDocument = null;

        foreach ($this->immigrationDocuments as $doc) {
            if ($doc->seqno == $seqNo) {
                $immigrationDocument = $doc;
                break;
            }
        }

        return ($immigrationDocument);
    }

    /**
     * Returns Direct debit details with given sequence no, or null if not found.
     *
     * @param int $seqNo Sequence no
     *
     * @return EmpDirectdebit Direct debit details with given sequence no.
     */
    public function getDirectDebit($seqNo) {

        $directDebit = null;

        foreach ($this->directDebits as $dd) {
            if ($dd->seqno == $seqNo) {
                $directDebit = $dd;
                break;
            }
        }

        return ($directDebit);
    }

    /**
     * Returns education details with given code, or null if not found.
     *
     * @param int $eduCode Education code
     *
     * @return EmpEducation Education details with given code.
     */
    public function getEducation($eduCode) {

        $education = null;

        foreach ($this->education as $edu) {
            if ($edu->code == $eduCode) {
                $education = $edu;
                break;
            }
        }

        return ($education);
    }

    /**
     * Returns education details with given code, or null if not found.
     *
     * @param int $eduCode Education code
     *
     * @return EmpEducation Education details with given code.
     */
    public function getSkill($skillCode) {

        $skill = null;

        foreach ($this->skills as $sk) {
            if ($sk->code == $skillCode) {
                $skill = $sk;
                break;
            }
        }

        return ($skill);
    }

    /**
     * Returns language details with given code, or null if not found.
     *
     * @param int $langCode Language code
     * @param int $langFluency Language fluency code
     *
     * @return EmpLanguage Language details with given code.
     */
    public function getLanguage($langCode, $langFluency) {
        $language = null;

        foreach ($this->languages as $lang) {
            if (($lang->code == $langCode) && ($lang->lang_type == $langFluency)) {
                $language = $lang;
                break;
            }
        }

        return ($language);

    }


    /**
     * Returns license details with given code, or null if not found.
     *
     * @param int $licenseCode Language code
     *
     * @return Employeelicenses license details with given code.
     */
    public function getLicense($licenseCode) {
        $license = null;

        foreach ($this->languages as $lic) {
            if ($lic->code == $licenseCode) {
                $license = $lic;
                break;
            }
        }

        return ($license);
    }

    /**
     * Returns membership details with given code, or null if not found.
     *
     * @param String $membershipType Membership type code
     * @param String $membershipCode Membership code
     *
     * @return EmployeeMembership membership details with given code.
     */
    public function getMembership($membershipType, $membershipCode) {
        $membership = null;

        foreach ($this->memberships as $mem) {
            if (($mem->membship_code == $membershipCode) && ($mem->membtype_code == $membershipType)) {
                $membership = $mem;
                break;
            }
        }

        return ($membership);
    }

    /**
     * Get this employee's salary grade, or null if not available.
     *
     * @return String Salary Grade
     */
    public function getSalaryGrade() {

        $salaryGrade = null;

        if (count($this->salaryDetails) > 0) {
            $basicSalary = $this->salaryDetails[0];
            $salaryGrade = $basicSalary->salaryGrade;
        }

        return($salaryGrade);
    }


    /**
     * Get this employee's salary in the given currency
     *
     * @param String $currencyCode Currency code
     *
     * @return EmpBasicSalary
     */
    public function getSalary($currencyCode) {

        $empSalary = null;

        foreach ($this->salaryDetails as $sal) {

            if ($sal->currency_id == $currencyCode) {
                $empSalary = $sal;
                break;
            }
        }

        return($empSalary);
    }

    /**
     * Get the ReportTo object for the supervisor with given emp number
     *
     * @param int $supervisorEmpNumber Supervisors employee number
     *
     * @return ReportTo object
     */
    public function getSupervisorRepTo($supervisorEmpNumber)
    {
        $supRepTo = null;

        foreach ($this->ReportToSup as $repTo) {
            if ($supervisorEmpNumber == $repTo->supervisorId) {
                $supRepTo = $repTo;
                break;
            }
        }

        return ($supRepTo);
    }

    /**
     * Get the ReportTo object for the subordinate with given emp number
     *
     * @param int $subordinateEmpNumber Subordinate's employee number
     *
     * @return ReportTo object
     */
    public function getSubordinateRepTo($subordinateEmpNumber)
    {
        $subRepTo = null;

        foreach ($this->ReportToSub as $repTo) {
            if ($subordinateEmpNumber == $repTo->subordinateId) {
                $subRepTo = $repTo;
                break;
            }
        }

        return ($subRepTo);
    }

    /**
     * Get this employee's contract with given id
     *
     * @param $contractId contract ID
     *
     * @return EmpContract
     */
    public function getContract($contractId) {

        $empContract = null;

        foreach ($this->contracts as $contract) {

            if ($contract->currency_id == $contractId) {
                $empContract = $contract;
                break;
            }
        }

        return($empContract);
    }

    /**
     * Saving/Updating Employee Picture
     * @param EmpPicture $empPicture
     */
    public function setEmployeePicture(EmpPicture $empPicture)
    {
      if($this->getEmpNumber() != "") {
         $service             = new EmployeeService();
         $currentEmpPicture   = $service->readEmployeePicture($this->getEmpNumber());

         if($currentEmpPicture instanceof EmpPicture) {
            $currentEmpPicture->setPicture($empPicture->getPicture());
            $currentEmpPicture->setFilename($empPicture->getFilename());
            $currentEmpPicture->setFileType($empPicture->getFileType());
            $currentEmpPicture->setSize($empPicture->getSize());
         } else {
            $currentEmpPicture = $empPicture;
         }
         $service->saveEmployeePicture($currentEmpPicture);
      }
    }

    /**
     * Get year of service
     * @return unknown_type
     */
    public function getYearOfService() {
        $joinedDate = $this->getJoinedDate();

        list($Y, $m, $d) = explode("-", $joinedDate);
        $years = date("Y") - $Y;

        if (date("md") < $m . $d) {
            $years--;
        }
        return $years;
    }
}
?>