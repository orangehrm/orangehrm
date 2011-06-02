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
 * EmployeeDao for CRUD operation
 *
 */
class EmployeeDao extends BaseDao {

    /**
     * Save Employee
     * @param Employee $employee
     * @returns boolean
     * @throws DaoException
     */
    public function addEmployee(Employee $employee) {
        try {
            if ($employee->getEmpNumber() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($employee);
                $employee->setEmpNumber($idGenService->getNextID());
            }
            $employee->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve employee by empNumber, must make this retrieve domain object
     * @param int $empNumber
     * @returns boolean
     * @throws DaoException
     */
    public function getEmployee($empNumber) {
        try {
            return Doctrine :: getTable('Employee')->find($empNumber);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve Picture
     * @param int $empNumber
     * @returns Collection
     * @throws DaoException
     */
    public function getPicture($empNumber) {
        try {
            return Doctrine :: getTable('EmpPicture')->find($empNumber);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save Personal Details
     * @param Employee $employee
     * @param boolean $isESS
     * @returns boolean
     * @throws DaoException
     */
    public function savePersonalDetails(Employee $employee, $isESS = false) {
        try {
            $q = Doctrine_Query :: create()->update('Employee')
                            ->set('firstName', '?', $employee->firstName)
                            ->set('middleName', '?', $employee->middleName)
                            ->set('lastName', '?', $employee->lastName)
                            ->set('nickName', '?', $employee->nickName)
                            ->set('otherId', '?', $employee->otherId)
                            ->set('emp_marital_status', '?', $employee->emp_marital_status)
                            ->set('smoker', '?', !empty($employee->smoker) ? $employee->smoker : 0)
                            ->set('emp_gender', '?', $employee->emp_gender)
                            ->set('militaryService', '?', $employee->militaryService);

            if (empty($employee->emp_dri_lice_exp_date)) {
                $q->set('emp_dri_lice_exp_date', 'NULL');
            } else {
                $q->set('emp_dri_lice_exp_date', '?', $employee->emp_dri_lice_exp_date);
            }

            if (empty($employee->nation_code)) {
                $q->set('nation_code', 'NULL');
            } else {
                $q->set('nation_code', '?', $employee->nation_code);
            }

            if (empty($employee->ethnic_race_code)) {
                $q->set('ethnic_race_code', 'NULL');
            } else {
                $q->set('ethnic_race_code', '?', $employee->ethnic_race_code);
            }

            if (!$isESS) {
                $q->set('employeeId', '?', $employee->employeeId)->set('ssn', '?', $employee->ssn)->set('sin', '?', $employee->sin)->set('licenseNo', '?', $employee->licenseNo);

                if (empty($employee->emp_birthday)) {
                    $q->set('emp_birthday', 'NULL');
                } else {
                    $q->set('emp_birthday', '?', $employee->emp_birthday);
                }
            }

            $q->where('empNumber = ?', $employee->empNumber);
            $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save Contact Details
     * @param Employee $employee
     * @returns boolean
     * @throws DaoException
     */
    public function saveContactDetails(Employee $employee) {
        try {
            $countryCode = $employee->country;
            $q = Doctrine_Query::create()->update('Employee')
                            ->set('street1', '?', $employee->getStreet1())
                            ->set('street2', '?', $employee->getStreet2())
                            ->set('city', '?', $employee->getCity())
                            ->set('province', '?', $employee->getProvince())
                            ->set('emp_zipcode', '?', $employee->getEmpZipcode())
                            ->set('emp_hm_telephone', '?', $employee->getEmpHmTelephone())
                            ->set('emp_mobile', '?', $employee->getEmpMobile())
                            ->set('emp_work_telephone', '?', $employee->getEmpWorkTelephone())
                            ->set('emp_work_email', '?', $employee->getEmpWorkEmail())
                            ->set('emp_oth_email', '?', $employee->getEmpOthEmail());

            if (trim($employee->country) == "") {
                $q->set('country', '?', 'NULL');
            } else {
                $q->set('country', '?', $employee->country);
            }
            $q->where('empNumber = ?', $employee->getEmpNumber());
            $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $empNumber Employee Number
     * @return array Emergency Contacts as array
     */
    public function getEmergencyContacts($empNumber) {

        try {
            $q = Doctrine_Query:: create()->from('EmpEmergencyContact ec')
                            ->where('ec.emp_number = ?', $empNumber);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Emergency contacts
     * @param int $empNumber
     * @param array() $emergencyContactsToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteEmergencyContacts($empNumber, $emergencyContactsToDelete = array()) {
        try {
            if (is_array($emergencyContactsToDelete)) {

                $q = Doctrine_Query :: create()->delete('EmpEmergencyContact ec')
                                ->whereIn('seqno', $emergencyContactsToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Immigration
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteImmigration($empNumber, $entriesToDelete) {

        try {

            if (is_array($entriesToDelete)) {
                // Delete immigration
                $q = Doctrine_Query :: create()->delete('EmpPassport ec')
                                ->whereIn('seqno', $entriesToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();

                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save immigration
     * @param EmpPassport $empPassport
     * @returns boolean
     */
    public function saveEmployeePassport(EmpPassport $empPassport) {

        try {

            $sequenceNo = 1;

            if (trim($empPassport->getSeqno()) == "") {
                $q = Doctrine_Query::create()
                                ->select('MAX(p.seqno)')
                                ->from('EmpPassport p')
                                ->where('p.emp_number = ?', $empPassport->getEmpNumber());
                $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
                $sequenceNo = $result[0]['MAX'] + 1;
                $empPassport->setSeqno($sequenceNo);
            }

            $empPassport->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get EmpPassport
     * @param int $empNumber
     * @param int $sequenceNo
     * @returns Collection/EmpPassport
     * @throws DaoException
     */
    public function getEmployeePassport($empNumber, $sequenceNo = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmpPassport p')
                            ->where('p.emp_number = ?', $empNumber);

            if (!is_null($sequenceNo)) {
                $q->andwhere('p.seqno = ?', $sequenceNo);
                return $q->fetchOne();
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get WorkExperience
     * @param int $empNumber
     * @param int $sequenceNo
     * @returns Collection/WorkExperience
     * @throws DaoException
     */
    public function getWorkExperience($empNumber, $sequenceNo = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmpWorkExperience w')
                            ->where('w.emp_number = ?', $empNumber);

            if (!is_null($sequenceNo)) {
                $q->andwhere('w.seqno = ?', $sequenceNo);
                return $q->fetchOne();
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save WorkExperience
     * @param EmpWorkExperience $empWorkExp
     * @returns boolean
     */
    public function saveWorkExperience(EmpWorkExperience $empWorkExp) {
        try {

            $sequenceNo = 1;

            if (trim($empWorkExp->getSeqno()) == "") {
                $q = Doctrine_Query::create()
                                ->select('MAX(w.seqno)')
                                ->from('EmpWorkExperience w')
                                ->where('w.emp_number = ?', $empWorkExp->getEmpNumber());
                $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
                $sequenceNo = $result[0]['MAX'] + 1;
                $empWorkExp->setSeqno($sequenceNo);
            }

            $empWorkExp->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete WorkExperiences
     * @param int $empNumber
     * @param array() $workExperienceToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteWorkExperience($empNumber, $workExperienceToDelete) {
        try {
            if (is_array($workExperienceToDelete)) {
                // Delete work experience
                $q = Doctrine_Query :: create()->delete('EmpWorkExperience ec')
                                ->whereIn('seqno', $workExperienceToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Education
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/Education
     * @throws DaoException
     */
    public function getEducation($empNumber, $eduCode = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeEducation w')
                            ->where('w.emp_number = ?', $empNumber);

            if (!is_null($eduCode)) {
                $q->andwhere('w.edu_code = ?', $eduCode);
                return $q->fetchOne();
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save Education
     * @param EmpEducation $empEdu
     * @returns boolean
     */
    public function saveEducation(EmployeeEducation $empEdu) {
        try {
            $empEdu->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Educations
     * @param int $empNumber
     * @param array() $educationToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteEducation($empNumber, $educationToDelete) {
        try {
            if (is_array($educationToDelete)) {
                // Delete work experience
                $q = Doctrine_Query :: create()->delete('EmployeeEducation ec')
                                ->whereIn('edu_code', $educationToDelete)
                                ->andwhere('emp_number = ?', $empNumber);

                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Language
     * @param int $empNumber
     * @param int $langCode
     * @returns Collection/Language
     * @throws DaoException
     */
    public function getLanguage($empNumber, $langCode = null, $langType = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeLanguage l')
                            ->where('l.emp_number = ?', $empNumber);
            $fetchOne = false;

            if (!is_null($langCode)) {
                $q->andwhere('l.lang_code = ?', $langCode);
            }

            if (!is_null($langType)) {
                $q->andwhere('l.lang_type = ?', $langType);
            }

            if (!is_null($langCode) && !is_null($langType)) {
                return $q->fetchOne();
            } else {
                return $q->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save Language
     * @param EmpLanguage $empLang
     * @returns boolean
     */
    public function saveLanguage(EmployeeLanguage $empLang) {
        try {
            $empLang->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Languages
     * @param int $empNumber
     * @param array() $languageToDelete
     * @return int - number of records deleted. False if did not delete anything.
     * @throws DaoException
     */
    public function deleteLanguage($empNumber, $languagesToDelete) {
        try {
            if (is_array($languagesToDelete)) {
                // Delete work experience
                $q = Doctrine_Query::create();
                $q->delete('EmployeeLanguage el');

                foreach ($languagesToDelete as $code => $type) {
                    $q->orWhere('(lang_code = ? and lang_type = ?)', array($code, $type));
                }
                //var_dump($q->getSqlQuery());die;

                $result = $q->execute();
                return $result;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Skill
     * @param int $empNumber
     * @param int $SkillCode
     * @returns Collection/Skill
     * @throws DaoException
     */
    public function getSkill($empNumber, $skillCode = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeSkill w')
                            ->where('w.emp_number = ?', $empNumber);

            if (!is_null($skillCode)) {
                $q->andwhere('w.skill_code = ?', $skillCode);
                return $q->fetchOne();
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save Skill
     * @param EmployeeSkill $empSkill
     * @returns boolean
     */
    public function saveSkill(EmployeeSkill $empSkill) {
        try {
            $empSkill->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Skills
     * @param int $empNumber
     * @param array() $skillToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteSkill($empNumber, $skillToDelete) {
        try {
            if (is_array($skillToDelete)) {
                // Delete work experience
                $q = Doctrine_Query :: create()->delete('EmployeeSkill ec')
                                ->whereIn('skill_code', $skillToDelete)
                                ->andwhere('emp_number = ?', $empNumber);

                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get License
     * @param int $empNumber
     * @param int $LicenseCode
     * @returns Collection/License
     * @throws DaoException
     */
    public function getLicense($empNumber, $licenseCode = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeLicense l')
                            ->where('l.emp_number = ?', $empNumber);

            if (!is_null($licenseCode)) {
                $q->andwhere('l.code = ?', $licenseCode);
                return $q->fetchOne();
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * save License
     * @param EmployeeLicense $empLicense
     * @returns boolean
     */
    public function saveLicense(EmployeeLicense $empLicense) {
        try {
            $empLicense->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get attachment
     * @param type $empNumber - employee number
     * @param type $screen - screen attached to
     */
    public function getAttachments($empNumber, $screen) {
        try {
            $q = Doctrine_Query:: create()
                            ->from('EmployeeAttachment a')
                            ->where('a.emp_number = ?', $empNumber)
                            ->andWhere('a.screen = ?', $screen);
            return $q->execute();
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Retrieve Attachment
     * @param int $empNumber
     * @returns Collection
     * @throws DaoException
     */
    public function getAttachment($empNumber, $attachId) {
        try {
            return Doctrine :: getTable('EmployeeAttachment')->find(array(
                'emp_number' => $empNumber,
                'attach_id' => $attachId
            ));
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Attachments
     * @param int $empNumber
     * @param array $attachmentsToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteAttachments($empNumber, $attachmentsToDelete = array()) {
        try {
            if (count($attachmentsToDelete) > 0) {
                // Delete attachments
                $q = Doctrine_Query :: create()->delete('EmployeeAttachment a')
                                ->whereIn('attach_id', $attachmentsToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Licenses
     * @param int $empNumber
     * @param array() $licenseToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteLicense($empNumber, $licenseToDelete) {
        try {
            if (is_array($licenseToDelete)) {
                // Delete work experience
                $q = Doctrine_Query :: create()->delete('EmployeeLicense l')
                                ->whereIn('l.code', $licenseToDelete)
                                ->andwhere('l.emp_number = ?', $empNumber);

                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get dependents for given employee
     * @param int $empNumber Employee Number
     * @return array Dependents as array
     */
    public function getDependents($empNumber) {
        try {
            $q = Doctrine_Query:: create()->from('EmpDependent ed')
                            ->where('ed.emp_number = ?', $empNumber);
            return $q->execute();
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Delete Dependents
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteDependents($empNumber, $entriesToDelete) {
        try {
            if (is_array($entriesToDelete)) {
                // Delete dependents
                $q = Doctrine_Query :: create()->delete('EmpDependent d')
                                ->whereIn('seqno', $entriesToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Children
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns boolean
     * @throws DaoException
     */
    public function deleteChildren($empNumber, $entriesToDelete) {
        try {
            if (is_array($entriesToDelete)) {
                // Delete children
                $q = Doctrine_Query :: create()->delete('EmpChild c')
                                ->whereIn('seqno', $entriesToDelete)
                                ->andwhere('emp_number = ?', $empNumber);
                $result = $q->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Photo
     * @param int $empNumber
     * @returns boolean
     * @throws DaoException
     */
    public function deletePhoto($empNumber) {
        try {
            $q = Doctrine_Query :: create()->delete('EmpPicture p')
                            ->where('emp_number = ?', $empNumber);
            $result = $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save EmployeePicture
     * @param EmpPicture $empPicture
     * @returns boolean
     * @throws DaoException
     */
    function saveEmployeePicture(EmpPicture $empPicture) {
        try {
            $empPicture->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns EmployeePicture by Emp Number
     * @param int $empNumber
     * @returns EmpPicture
     * @throws DaoException
     */
    function readEmployeePicture($empNumber) {
        try {
            $q = Doctrine_Query :: create()->from('EmpPicture ep')
                            ->where('emp_number = ?', $empNumber);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns Employee List
     * @returns Collection
     * @throws DaoException
     */
    public function getEmployeeList($orderField = 'empNumber', $orderBy = 'ASC') {
        try {
            $q = Doctrine_Query :: create()->from('Employee');
            $q->orderBy($orderField . ' ' . $orderBy);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
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
            $q = Doctrine_Query :: create()
                            ->select('e.firstName, e.lastName, e.empNumber, s.empNumber')
                            ->from('Employee e')
                            ->innerJoin('e.subordinates s')
                            ->orderBy('e.lastName DESC');

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Check if employee with given empNumber is a supervisor
     * @param int $empNumber
     * @return bool - True if given employee is a supervisor, false if not
     */
    public function isSupervisor($empNumber) {
        try {
            $q = Doctrine_Query :: create()
                            ->select('COUNT(*)')
                            ->from('ReportTo r')
                            ->where('r.supervisorId = ?', $empNumber);

            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

            return ($count > 0);
        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }

    /**
     * Search Employee
     * @param String $field
     * @param String $value
     * @return Collection
     * @throws DaoException
     */
    public function searchEmployee($field, $value) {
        try {
            $q = Doctrine_Query::create()
                            ->from('Employee')
                            ->where($field . " = ?", $value);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns Employee Count
     * @returns int
     * @throws DaoException
     */
    public function getEmployeeCount() {
        try {
            $q = Doctrine_Query :: create()->from('Employee');
            return $q->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns Supervisor Employee List
     * @param int $supervisorId
     * @returns Collection
     * @throws DaoException
     */
    public function getSupervisorEmployeeList($supervisorId) {
        try {
            $employeeList = array();
            $q = Doctrine_Query :: create()->select("rt.supervisorId,emp.*")
                            ->from('ReportTo rt')
                            ->leftJoin('rt.subordinate emp')
                            ->where("rt.supervisorId = ?", $supervisorId);

            $reportToList = $q->execute();
            foreach ($reportToList as $reportTo) {
                array_push($employeeList, $reportTo->getSubordinate());
            }

            return $employeeList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Returns Employee List as Json
     * @param boolean $workShift
     * @returns String
     * @throws DaoException
     */
    public function getEmployeeListAsJson($workShift = false) {
        try {
            $jsonString = array();
            $q = Doctrine_Query :: create()->from('Employee');
            $employeeList = $q->execute();

            foreach ($employeeList as $employee) {
                $workShiftLength = 0;
                if ($workShift) {
                    $employeeWorkShift = $this->getWorkShift($employee->getEmpNumber());
                    if ($employeeWorkShift != null) {
                        $workShiftLength = $employeeWorkShift->getWorkShift()->getHoursPerDay();
                    } else
                        $workShiftLength = WorkShift :: DEFAULT_WORK_SHIFT_LENGTH;
                }
                array_push($jsonString, "{name:'" . $employee->getFirstName() . ' ' . $employee->getLastName() . "',id:'" . $employee->getEmpNumber() . "',workShift:'" . $workShiftLength . "'}");
            }

            $jsonStr = " [" . implode(",", $jsonString) . "]";
            return $jsonStr;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve SupervisorEmployeeChain
     * @param int $supervisorId
     * @returns array
     * @throws DaoException
     */
    public function getSupervisorEmployeeChain($supervisorId) {
        try {
            $employeeList = array();

            $q = Doctrine_Query::create()
                            ->select("rt.supervisorId,emp.*")
                            ->from('ReportTo rt')
                            ->leftJoin('rt.subordinate emp')
                            ->where("rt.supervisorId=$supervisorId");

            $reportToList = $q->execute();
            foreach ($reportToList as $reportTo) {
                array_push($employeeList, $reportTo->getSubordinate());
                $list = $this->getSupervisorEmployeeChain($reportTo->getSubordinateId());
                if (count($list) > 0)
                    foreach ($list as $employee)
                        array_push($employeeList, $employee);
            }
            return $employeeList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Employee
     * @param array $empList
     * @returns int
     * @throws DaoException
     */
    public function deleteEmployee($empList = array()) {
        try {
            if (is_array($empList) && count($empList) > 0) {
                $q = Doctrine_Query::create()
                                ->delete('Employee')
                                ->whereIn('empNumber', $empList);

                return $q->execute();
            }
            return 0;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
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
            $count = Doctrine_Query::create()
                            ->from('Employee')
                            ->where('employeeId = ?', $employeeId)
                            ->count();

            return ($count > 0) ? true : false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
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
            $count = Doctrine_Query::create()
                            ->from('Employee')
                            ->where('firstName = ?', $first)
                            ->andWhere('middleName = ?', $middle)
                            ->andWhere('lastName = ?', $last)
                            ->count();

            return ($count > 0) ? true : false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve Workshift for a given employee number
     * @param int $empNumber
     * @returns EmployeeWorkShift
     * @throws DaoException
     */
    public function getWorkShift($empNumber) {
        try {
            $q = Doctrine_Query::create()->from('EmployeeWorkShift ews')
                            ->where('ews.emp_number =?', $empNumber);

            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve Tax Exemptions for a given employee number
     * @param int $empNumber
     * @returns EmpTaxExemption
     * @throws DaoException
     */
    public function getEmployeeTaxExemptions($empNumber) {
        try {
            $q = Doctrine_Query::create()->from('EmpUsTaxExemption eute')
                            ->where('eute.emp_number =?', $empNumber);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save Employee Tax Exemptios
     * @param EmpUsTaxExemption $empUsTaxExemption
     * @returns boolean
     * @throws DaoException
     */
    public function saveEmployeeTaxExemptions(EmpUsTaxExemption $empUsTaxExemption) {
        try {
            $empUsTaxExemption->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Save Contact Details
     * @param Employee $employee
     * @returns boolean
     * @throws DaoException
     */
    public function saveJobDetails(Employee $employee) {

        $conn = Doctrine_Manager :: connection();
        $conn->beginTransaction();
        try {
            $q = Doctrine_Query :: create($conn)->update('Employee');

            if (!empty($employee->job_title_code)) {
                $q->set('job_title_code', '?', $employee->job_title_code);
            }

            if (!empty($employee->emp_status)) {
                $q->set('emp_status', '?', $employee->emp_status);
            }

            if (!empty($employee->eeo_cat_code)) {
                $q->set('eeo_cat_code', '?', $employee->eeo_cat_code);
            }

            if (!empty($employee->work_station)) {
                $q->set('work_station', '?', $employee->work_station);
            }

            if (!empty($employee->joined_date)) {
                $q->set('joined_date', '?', $employee->joined_date);
            }

            $q->where('empNumber = ?', $employee->empNumber);
            $result = $q->execute();

            // Employee locations
            $q = Doctrine_Query :: create()->delete('EmpLocations el')
                            ->where('emp_number = ?', $employee->empNumber);
            $result = $q->execute();

            if (count($employee->locations) > 0) {
                $empLocation = $employee->locations[0];
                $empLocation->save();
            }

            // Employee contracts
            $q = Doctrine_Query :: create()->delete('EmpContract ec')
                            ->where('emp_number = ?', $employee->empNumber);
            $result = $q->execute();

            if (count($employee->contracts) > 0) {
                $empContract = $employee->contracts[0];

                // TODO: Check why emp_number is lost
                $empContract->emp_number = $employee->empNumber;
                $empContract->save();
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get membership details for given employee
     * @param int $empNumber Employee Number
     * @return array Membership details as array
     */
    public function getMembershipDetails($empNumber) {

        try {
            $q = Doctrine_Query::create()->from('EmployeeMemberDetail emd')
                            ->where('emd.emp_number =?', $empNumber);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get membership details for given employee
     * @param int $empNumber $membershipType $membership
     * @return array Membership details as array
     */
    public function getMembershipDetail($empNumber, $membershipType, $membership) {

        try {
            $q = Doctrine_Query::create()->from('EmployeeMemberDetail emd')
                            ->where('emd.emp_number =?', $empNumber)
                            ->andWhere("emd.membershipTypeCode = ?", $membershipType)
                            ->andWhere("emd.membershipCode = ?", $membership);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Membership Detail
     * @param $empNumber $membershipType $membership
     * @return boolean
     */
    public function deleteMembershipDetails($empNumber, $membershipType, $membership) {
        try {
            $query = Doctrine_Query::create()
                            ->delete()
                            ->from("EmployeeMemberDetail")
                            ->where("empNumber = ?", $empNumber)
                            ->andWhere("membershipTypeCode = ?", $membershipType)
                            ->andWhere("membershipCode = ?", $membership);

            $membershipDetailDeleted = $query->execute();

            if ($membershipDetailDeleted > 0) {
                return true;
            }
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
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
         $hydrateMode = ($asArray) ? Doctrine :: HYDRATE_ARRAY : Doctrine :: HYDRATE_RECORD;
         $q = Doctrine_Query :: create()->select('c.currency_id, c.currency_name')
           ->from('CurrencyType c')
           ->leftJoin('c.SalaryCurrencyDetail s')
           ->where('s.sal_grd_code = ?', $salaryGrade)
           ->andWhere('c.currency_id NOT IN (SELECT e.currency_id FROM EmpBasicsalary e WHERE e.emp_number = ? AND e.sal_grd_code = ?)'
                   , array ($empNumber, $salaryGrade));

         return $q->execute(array (), $hydrateMode);
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmpBasicsalary
    * @param EmpBasicsalary $empBasicsalary
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmpBasicsalary(EmpBasicsalary $empBasicsalary) {
      try {
         $empBasicsalary->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
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
         // Skip if no salarys because running the following query
         // with no salarys will delete all this employee's assigned
         // salary
        
         if (count($salaryToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('EmpBasicsalary s')
                ->whereIn('id', array_values($salaryToDelete))
                ->andWhere('emp_number = ?', $empNumber);

            $result = $q->execute();
         }
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
   
    /**
     * Add or Save Report Mode
     * @param ReportMode $reportMode
     * @return Doctine object ReportMode
     */
    public function saveReportMode(ReportMode $reportMode) {

        try {

            if ($reportMode->getReportModeId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($reportMode);
                $reportMode->setReportModeId($idGenService->getNextID());
            }
            $reportMode->save();

            return $reportMode;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Report Mode
     * @param $reportModeId
     * @return Doctine object ReportMode
     */
    public function getReportMode($reportModeId) {

        try {
            $q = Doctrine_Query::create()->from('ReportMode rm')
                            ->where('rm.rep_mode_id =?', $reportModeId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * get Report Mode List
     * @return Doctine collection ReportModeList
     */
    public function getReportModeList() {

        try {
            $q = Doctrine_Query :: create()->from('ReportMode');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * get supervisor list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getSupervisorListForEmployee($empNumber) {

        try {
            $q = Doctrine_Query :: create()->from('ReportTo rt')
                            ->where('rt.erep_sub_emp_number =?', $empNumber);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * get subordinate list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getSubordinateListForEmployee($empNumber) {

        try {
            $q = Doctrine_Query :: create()->from('ReportTo rt')
                            ->where('rt.erep_sup_emp_number =?', $empNumber);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get report to details object
     * @param int $supNumber $subNumber $reportMode
     * @return ReportTo object
     */
    public function getReportToObject($supNumber, $subNumber) {

        try {
            $q = Doctrine_Query::create()->from('ReportTo rt')
                            ->where('rt.erep_sup_emp_number =?', $supNumber)
                            ->andWhere('rt.erep_sub_emp_number =?', $subNumber);

            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete reportTo object
     * @param int $supNumber $subNumber $reportMode
     * @return boolean
     */
    public function deleteReportToObject($supNumber, $subNumber) {

        try {
            $q = Doctrine_Query::create()
                            ->delete()
                            ->from('ReportTo rt')
                            ->where('rt.erep_sup_emp_number =?', $supNumber)
                            ->andWhere('rt.erep_sub_emp_number =?', $subNumber);

            $executed = $q->execute();

            if ($executed > 0) {
                return true;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}
