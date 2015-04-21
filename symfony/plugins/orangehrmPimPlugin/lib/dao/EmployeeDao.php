<?php

/*
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
     * Mapping of search field names to database fields
     * @var array
     */
    protected static $searchMapping = array(
            'id' => 'e.employee_id',
            'employee_name' => 'concat_ws(\' \', e.emp_firstname,e.emp_middle_name,e.emp_lastname)',
            'middleName' => 'e.emp_middle_name',
            'lastName' => 'e.emp_lastName',
            'job_title' => 'j.job_title',
            'employee_status' => 'es.estat_name',
            'sub_unit' => 'cs.name',
            'supervisor_name' => 'concat_ws(\' \', se.emp_firstname,se.emp_middle_name,se.emp_lastname)',
            'supervisorId' => 's.emp_firstname',
            'termination' => 'e.termination_id',
            'location' => 'l.location_id',
            'employee_id_list' => 'e.emp_number',
    );

    /**
     * Mapping of sort field names to database fields
     * @var array
     */
    protected static $sortMapping = array(
            'employeeId' => 'e.employee_id',
            'firstName' => 'e.emp_firstname',
            'middleName' => 'e.emp_middle_name',
            'firstMiddleName' => array('e.emp_firstname','e.emp_middle_name'),
            'lastName' => 'e.emp_lastName',
            'fullName' => array('e.emp_firstname', 'e.emp_middle_name', 'e.emp_lastName'),
            'jobTitle' => 'j.job_title',
            'empLocation' => 'loc.name',
            'employeeStatus' => 'es.name',
            'subDivision' => 'cs.name',
            'supervisor' => array('s.emp_firstname', 's.emp_lastname')
    );
    
    /**
     * Save Employee
     * @param Employee $employee
     * @returns boolean
     * @throws DaoException
     */
    public function saveEmployee(Employee $employee) {
        try {
            if ($employee->getEmpNumber() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($employee);
                $employee->setEmpNumber($idGenService->getNextID());
            }
            
            $employee->save();
            
            return $employee;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve Picture
     * @param int $empNumber
     * @returns Collection
     * @throws DaoException
     */
    public function getEmployeePicture($empNumber) {
        try {
            return Doctrine :: getTable('EmpPicture')->find($empNumber);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $empNumber Employee Number
     * @return array EmpEmergencyContact objects as array
     */
    public function getEmployeeEmergencyContacts($empNumber) {

        try {
            $q = Doctrine_Query:: create()->from('EmpEmergencyContact ec')
                            ->where('ec.emp_number = ?', $empNumber)
                            ->orderBy('ec.name ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Emergency contacts
     * @param int $empNumber
     * @param array $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeEmergencyContacts($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmpEmergencyContact')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('seqno', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd        
        
    }

    /**
     * Delete Immigration
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeImmigrationRecords($empNumber, $entriesToDelete = null) {

        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeImmigrationRecord')
                                         ->where('empNumber = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('recordId', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * save immigration
     * @param EmployeeImmigrationRecord $employeeImmigrationRecord
     * @return EmployeeImmigrationRecord
     */
    public function saveEmployeeImmigrationRecord(EmployeeImmigrationRecord $employeeImmigrationRecord) {

        try {

            $recordId = 1;

            if (trim($employeeImmigrationRecord->getRecordId()) == "") {
                
                $q = Doctrine_Query::create()
                                ->select('MAX(p.recordId)')
                                ->from('EmployeeImmigrationRecord p')
                                ->where('p.empNumber = ?', $employeeImmigrationRecord->getEmpNumber());
                $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
                $recordId = $result[0]['MAX'] + 1;
                
                $employeeImmigrationRecord->setRecordId($recordId);
                
            }

            $employeeImmigrationRecord->save();
            
            return $employeeImmigrationRecord;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Get EmployeeImmigrationRecord
     * @param int $empNumber
     * @param int $recordId
     * @returns Collection/EmployeeImmigrationRecord
     * @throws DaoException
     */
    public function getEmployeeImmigrationRecords($empNumber, $recordId = null) {
        
        try {
            
            $q = Doctrine_Query::create()
                            ->from('EmployeeImmigrationRecord p')
                            ->where('p.empNumber = ?', $empNumber)
                            ->orderBy('p.type, p.number');

            if (!is_null($recordId)) {
                $q->andwhere('p.recordId = ?', $recordId);
                return $q->fetchOne();
            }
            
            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Get WorkExperience
     * @param int $empNumber
     * @param int $sequenceNo
     * @returns Collection/WorkExperience
     * @throws DaoException
     */
    public function getEmployeeWorkExperienceRecords($empNumber, $recordId = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmpWorkExperience w')
                            ->where('w.emp_number = ?', $empNumber);

            if (!is_null($recordId)) {
                $q->andwhere('w.seqno = ?', $recordId);
                return $q->fetchOne();
            }

            $q->orderBy('w.employer ASC, w.jobtitle ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * save WorkExperience
     * @param EmpWorkExperience $empWorkExp
     * @return EmpWorkExperience
     */
    public function saveEmployeeWorkExperience(EmpWorkExperience $empWorkExp) {
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
            
            return $empWorkExp;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete WorkExperiences
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeWorkExperienceRecords($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmpWorkExperience ec')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('seqno', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd     
        
    }

    /**
     * Get Education
     * @param int $empNumber
     * @param int $eduCode
     * @returns Collection/Education
     * @throws DaoException
     */
    public function getEducation($id) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeEducation w')
                            ->where('w.id = ?', $id);

            return $q->fetchOne();
                
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    public function getEmployeeEducations($empNumber, $educationId=null) {

        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeEducation ee')
                            ->leftJoin('ee.Education as edu')
                            ->where('ee.emp_number = ?', $empNumber);
            
            if (!empty($educationId)) {
                $q->addWhere('ee.education_id = ?', $educationId);
            }
            $q->orderBy('edu.name ASC');
            return $q->execute();
                
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }   

    /**
     * save Education
     * @param EmpEducation $empEdu
     * @returns EmployeeEducation
     */
    public function saveEmployeeEducation(EmployeeEducation $empEdu) {
        
        try {
            
            $empEdu->save();
            
            return $empEdu;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete Educations
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeEducationRecords($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeEducation')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('id', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd       
        
    }

    /**
     * Get Language
     * @param int $empNumber
     * @param int $langCode
     * @returns Collection/Language
     * @throws DaoException
     */
    public function getEmployeeLanguages($empNumber, $langCode = null, $langType = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeLanguage el')
                            ->leftJoin('el.Language l')
                            ->where('el.emp_number = ?', $empNumber);

            if (!is_null($langCode)) {
                $q->andwhere('el.lang_id = ?', $langCode);
            }

            if (!is_null($langType)) {
                $q->andwhere('el.fluency = ?', $langType);
            }

            if (!is_null($langCode) && !is_null($langType)) {
                return $q->fetchOne();
            } else {
                $q->orderBy('l.name ASC');
                return $q->execute();
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * save Language
     * @param EmpLanguage $empLang
     * @returns EmployeeLanguage
     */
    public function saveEmployeeLanguage(EmployeeLanguage $empLang) {
        
        try {
            
            $empLang->save();
            
            return $empLang;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete Languages
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @return integer
     * @throws DaoException
     */
    public function deleteEmployeeLanguages($empNumber, $entriesToDelete = null) {

        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeLanguage');
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                
                foreach ($entriesToDelete as $lang) {
                    foreach ($lang as $langId => $fluency) {
                        $q->orWhere('(lang_id = ? and fluency = ?)', array($langId, $fluency));
                    }
                }                
                
            }
            
            $q->andWhere('emp_number = ?', $empNumber);
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd      
        
    }

    /**
     * Get Skill
     * @param int $empNumber
     * @param int $SkillCode
     * @returns Collection/Skill
     * @throws DaoException
     */
    public function getEmployeeSkills($empNumber, $skillCode = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeSkill es')
                            ->leftJoin('es.Skill s')
                            ->where('es.emp_number = ?', $empNumber);

            if (!is_null($skillCode)) {
                $q->andwhere('es.skill_id = ?', $skillCode);
                return $q->fetchOne();
            }
            $q->orderBy('s.name ASC');

            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * save Skill
     * @param EmployeeSkill $empSkill
     * @returns EmployeeSkill
     */
    public function saveEmployeeSkill(EmployeeSkill $empSkill) {
        
        try {
            
            $empSkill->save();
            
            return $empSkill;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete Skills
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeSkills($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeSkill')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('skill_id', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd    
        
    }

    /**
     * Get License
     * @param int $empNumber
     * @param int $LicenseCode
     * @returns Collection/License
     * @throws DaoException
     */
    public function getEmployeeLicences($empNumber, $licenseCode = null) {
        try {
            $q = Doctrine_Query::create()
                            ->from('EmployeeLicense el')
                            ->leftJoin('el.License l')
                            ->where('el.emp_number = ?', $empNumber);

            if (!is_null($licenseCode)) {
                $q->andwhere('el.license_id = ?', $licenseCode);
                return $q->fetchOne();
            }
            $q->orderBy('l.name ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * save License
     * @param EmployeeLicense $empLicense
     * @returns EmployeeLicense
     */
    public function saveEmployeeLicense(EmployeeLicense $empLicense) {
        
        try {
            
            $empLicense->save();
            
            return $empLicense;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete Licenses
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeLicenses($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeLicense')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('license_id', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd    
        
    }

    /**
     * Get attachment
     * @param type $empNumber - employee number
     * @param type $screen - screen attached to
     */
    public function getEmployeeAttachments($empNumber, $screen) {
        try {
            $q = Doctrine_Query:: create()
                            ->from('EmployeeAttachment a')
                            ->where('a.emp_number = ?', $empNumber)
                            ->andWhere('a.screen = ?', $screen)
                            ->orderBy('a.filename ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve Attachment
     * @param int $empNumber
     * @returns Collection
     * @throws DaoException
     */
    public function getEmployeeAttachment($empNumber, $attachId) {
        try {
            $result = Doctrine :: getTable('EmployeeAttachment')->find(array(
                'emp_number' => $empNumber,
                'attach_id' => $attachId
            ));
            
            if (!$result) {
                return null;
            }
            
            return $result;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Attachments
     * @param int $empNumber
     * @param array $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeAttachments($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeAttachment')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('attach_id', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd      
        
    }

    /**
     * Get dependents for given employee
     * @param int $empNumber Employee Number
     * @return array Dependents as array
     */
    public function getEmployeeDependents($empNumber) {
        try {
            $q = Doctrine_Query:: create()->from('EmpDependent ed')
                            ->where('ed.emp_number = ?', $empNumber)
                            ->orderBy('ed.name ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Dependents
     * @param int $empNumber
     * @param array() $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeDependents($empNumber, $entriesToDelete = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmpDependent d')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {                
                $q->whereIn('seqno', $entriesToDelete);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd        
        
    }

    /**
     * Delete Photo
     * @param int $empNumber
     * @return integer
     * @throws DaoException
     */
    public function deleteEmployeePicture($empNumber) {
        
        try {
            
            $q = Doctrine_Query :: create()->delete('EmpPicture p')
                            ->where('emp_number = ?', $empNumber);
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Save EmployeePicture
     * @param EmpPicture $empPicture
     * @return EmpPicture
     * @throws DaoException
     */
    public function saveEmployeePicture(EmpPicture $empPicture) {
        
        try {
            
            $empPicture->save();
            
            return $empPicture;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns Employee List
     * @returns Collection
     * @throws DaoException
     */
    public function getEmployeeList($orderField = 'lastName', $orderBy = 'ASC', $includeTerminatedEmployees = false) {
        try {
            $q = Doctrine_Query :: create()->from('Employee');
            $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
            $q->orderBy($orderField . ' ' . $orderBy);

            if (!$includeTerminatedEmployees) {
                $q->andwhere("termination_id IS NULL");
            }

            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get Employee id list
     * 
     * @version 2.7.1
     * @param Boolean $excludeTerminatedEmployees 
     * @returns Array EmployeeId List
     * @throws DaoException
     */
    public function getEmployeeIdList($excludeTerminatedEmployees = false) {
        
        try {
                $q = Doctrine_Query :: create()
                            ->select('e.empNumber')
                            ->from('Employee e');
                
                if ($excludeTerminatedEmployees) {
                    $q->andwhere("e.termination_id IS NULL");
                }
                $employeeIds = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

                // If only one result, Doctrine_Core::HYDRATE_SINGLE_SCALAR gives a single string.
                if (is_string($employeeIds)) {
                    $employeeIds = array($employeeIds);
                }
                return $employeeIds;
        
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get List of Employee Properties
     * 
     * @version 2.7.1
     * @param Boolean $excludeTerminatedEmployees
     * @returns Array List of Employee Properties 
     * @throws DaoException
     */
    public function getEmployeePropertyList($properties, $orderField, $orderBy, $excludeTerminatedEmployees = false) {

        try {
                $q = Doctrine_Query :: create();
                foreach ($properties as $property) {
                    $q->addSelect($property);
                }
                $q->from('Employee e');
                
                if ($excludeTerminatedEmployees) {
                    $q->andwhere("e.termination_id IS NULL");
                }
                
                if ($orderField && $orderBy) {
                    $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
                    $q->orderBy($orderField . ' ' . $orderBy);
                }

                $employeeProperties = $q->fetchArray();

                return $employeeProperties;
                
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }
    

    /**
     * Returns list of supervisors (employees having at least one subordinate)
     *
     * @returns Collection
     * @throws DaoException
     */
    public function getSupervisorList($includeTerminated = false, $orderField = 'lastName', $orderBy = 'ASC') {
        
        try {
            
            if (!property_exists('Employee', $orderField)) {
                $orderField = 'lastName';
            }
            
            $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
            
            $q = Doctrine_Query :: create()
                            ->from('Employee e')
                            ->innerJoin('e.subordinates s')
                            ->orderBy("e.$orderField $orderBy");
            
            if (!$includeTerminated) {
                $q->where('e.termination_id IS NULL');
            }

            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns Employee Count
     * @returns int
     * @throws DaoException
     */
    public function getEmployeeCount($includeTerminated = false) {
        
        try {
            $q = Doctrine_Query :: create()->from('Employee');

            if (!$includeTerminated) {
                $q->where("termination_id IS NULL");
            }
            
            return $q->count();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    public function getSubordinateIdList() {
        try {
            $idList = array();
            $q = Doctrine_Query :: create()->select("rt.subordinateId")
                            ->from('ReportTo rt');
            $reportToList = $q->execute();
            foreach ($reportToList as $reportTo) {
                array_push($idList, $reportTo->getSubordinateId());
            }
            return $idList;
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get Subordinate Id List by supervisor id
     * 
     * @param int $supervisorId
     * @param boolean $includeChain
     * @param Array $supervisorIdStack
     * @return Array of SubordinateId List
     * @throws DaoException
     */
    public function getSubordinateIdListBySupervisorId($supervisorId, $includeChain = false, $supervisorIdStack = array (), $maxDepth = NULL, $depth = 1) {
        
        try {
            $employeeIdList = array();
            $q = "SELECT h.erep_sub_emp_number
            	FROM hs_hr_emp_reportto h 
            		WHERE (h.erep_sup_emp_number = ?)";
            
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute(array($supervisorId));
            $subordinates =  $query->fetchAll();
            
            foreach ($subordinates as $subordinate) {
                array_push($employeeIdList, $subordinate['erep_sub_emp_number']);
                
                if ($includeChain || (!is_null($maxDepth) && ($depth < $maxDepth))) {
                    if (!in_array($subordinate['erep_sub_emp_number'], $supervisorIdStack)) {
                        $supervisorIdStack[] = $subordinate['erep_sub_emp_number'];
                        $subordinateIdList = $this->getSubordinateIdListBySupervisorId($subordinate['erep_sub_emp_number'], $includeChain, $supervisorIdStack, $maxDepth, $depth + 1);
                        if (count($subordinateIdList) > 0) {
                            foreach ($subordinateIdList as $id) {
                                array_push($employeeIdList, $id);
                            }
                        }
                    }
                }
            }
            return $employeeIdList;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get Supervisor Id List by subordinate id
     * 
     * @param int $subordinateId
     * @param boolean $includeChain
     * @param Array $supervisorIdStack
     * @return Array of Supervisor Id List
     * @throws DaoException
     */
    public function getSupervisorIdListBySubordinateId($subordinateId, $includeChain = false, $supervisorIdStack = array()) {
        
        try {
            $employeeIdList = array();
            $q = "SELECT h.erep_sup_emp_number
            	FROM hs_hr_emp_reportto h
            		WHERE (h.erep_sub_emp_number = ?)";
            
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute(array($subordinateId));
            $supervisors =  $query->fetchAll();
            
            foreach ($supervisors as $supervisor) {
                array_push($employeeIdList, $supervisor['erep_sup_emp_number']);
                if ($includeChain) {
                    if (!in_array($supervisor['erep_sup_emp_number'], $supervisorIdStack)) {
                        $supervisorIdStack[] = $subordinate['erep_sub_emp_number'];
                        $supervisorIdList = $this->getSupervisorIdListBySubordinateId($supervisor['erep_sup_emp_number'], $includeChain, $supervisorIdStack);
                        if (count($supervisorIdList) > 0) {
                            foreach ($supervisorIdList as $id) {
                                array_push($employeeIdList, $id);
                            }
                        }
                    }
                }
            }
            return $employeeIdList;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Get List of Subordinate Properties by supervisor id
     * 
     * @param int $supervisorId
     * @param Array $properties
     * @param boolean $includeChain
     * @param String $orderField
     * @param String $orderBy
     * @param Boolean $excludeTerminatedEmployees
     * @param Array $supervisorIdStack 
     * @returns Array List of Subordinate Properties
     * @throws DaoException
     * 
     * @todo Use a parameter object
     */
    public function getSubordinatePropertyListBySupervisorId($supervisorId, $properties, $includeChain = false, $orderField = NULL, 
            $orderBy = NULL, $includeTerminated = false, $supervisorIdStack = array(), $maxDepth = NULL, $depth = 1) {

        try {

            $employeePropertyList = array();
            
            $q = Doctrine_Query::create()
                            ->select("rt2.subordinateId AS isSupervisor, supervisorId, e.empNumber as empNumber");
            foreach ($properties as $property) {
                $q->addSelect('e.'.$property);
            }
                            
            $q->from('ReportTo rt')
              ->leftJoin('rt.subordinate e')
              ->leftJoin('e.ReportTo rt2 ON rt.erep_sub_emp_number = rt2.erep_sup_emp_number')
              ->where("rt.supervisorId = ?", $supervisorId);
            
            if ($includeTerminated == false) {
                $q->addWhere("e.termination_id IS NULL");
            }
            
            if($orderField && $orderBy) {
                $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
                $q->orderBy('e.'.$orderField . ' ' . $orderBy);
            }
              
            $subordinates =  $q->fetchArray();
            foreach ($subordinates as $subordinate) {
                $employeePropertyList[$subordinate['subordinateId']] = $subordinate['subordinate'];
                
                if ($subordinate['isSupervisor'] && ($includeChain || (!is_null($maxDepth) && ($depth < $maxDepth))) ) {
                    if (!in_array($subordinate['subordinateId'], $supervisorIdStack)) {
                        $supervisorIdStack[] = $subordinate['subordinateId'];
                        $subordinatePropertyList = $this->getSubordinatePropertyListBySupervisorId($subordinate['subordinateId'], $properties, 
                                $includeChain, $orderField, $orderBy, $includeTerminated, $supervisorIdStack, $maxDepth, $depth - 1);
                        if (count($subordinatePropertyList) > 0) {
                            foreach ($subordinatePropertyList as $key => $value) {
                                $employeePropertyList[$key] = $value;
                            }
                        }
                    }
                }
            }
            return $employeePropertyList;
        
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
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
                    $employeeWorkShift = $this->getEmployeeWorkShift($employee->getEmpNumber());
                    if ($employeeWorkShift != null) {
                        $workShiftLength = $employeeWorkShift->getWorkShift()->getHoursPerDay();
                    } else
                        $workShiftLength = WorkShift :: DEFAULT_WORK_SHIFT_LENGTH;
                }
                $terminationId = $employee->getTerminationId();
                if (empty($terminationId)) {
                    $name = $employee->getFirstName() . " " . $employee->getLastName();
                    $jsonString[] = array('name' => $name, 'id' => $employee->getEmpNumber());
                }
            }

            $jsonStr = json_encode($jsonString);
            return $jsonStr;
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return List of Subordinates for given Supervisor
     * 
     * @version 2.7.1
     * @param int $supervisorId Supervisor Id
     * @param boolean $includeTerminated Terminated status
     * @param boolean $includeChain
     * @param Array $supervisorIdStack
     * @return Doctrine_Collection of Subordinates
     * @throws DaoException
     */
    public function getSubordinateList($supervisorId, $includeTerminated = false, $includeChain = false, $supervisorIdStack = array()) {
        try {
            $employeeList = array();

            $query = Doctrine_Query::create()
                    ->from('ReportTo rt')
                    ->leftJoin('rt.subordinate emp')
                    ->where('rt.erep_sup_emp_number = ?', $supervisorId);

            if ($includeTerminated == false) {
                $query->addWhere('emp.termination_id IS NULL');
            }

            $subordinates = $query->execute();


            foreach ($subordinates as $subordinate) {
                $employeeList[] = $subordinate->getSubordinate();

                if ($includeChain) {
                    if (!in_array($subordinate->getSubordinateId(), $supervisorIdStack)) {
                        $supervisorIdStack[] = $subordinate->getSubordinateId();
                        $subordinateList = $this->getSubordinateList($subordinate->getSubordinateId(), $includeChain, $supervisorIdStack);
                        if (count($subordinateList) > 0) {
                            foreach ($subordinateList as $sub) {
                                $employeeList[] = $sub;
                            }
                        }
                    }
                }
            }

            return $employeeList;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Employee
     * 
     * This method prevents deleting all employees in case $empNumbers is not provided.
     * 
     * @param array $empNumbers
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployees($empNumbers) {
        
        try {
            
            if (!is_array($empNumbers) || empty($empNumbers)) {
                throw new DaoException('Invalid parameter: $empNumbers should be an array and should not be empty');
            }

            $q = Doctrine_Query::create()
                            ->delete('Employee')
                            ->whereIn('empNumber', $empNumbers);

            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Checks if the given employee id is in use.
     * @throws PIMServiceException
     * @param  $employeeId
     * @return ?#M#P#CEmployeeService.employeeDao.isEmployeeIdInUse
     */
    public function isExistingEmployeeId($employeeId) {
        try {
            $count = Doctrine_Query::create()
                            ->from('Employee')
                            ->where('employeeId = ?', $employeeId)
                            ->count();

            return ($count > 0) ? true : false;
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve Workshift for a given employee number
     * @param int $empNumber
     * @returns EmployeeWorkShift
     * @throws DaoException
     */
    public function getEmployeeWorkShift($empNumber) {
        try {
            $q = Doctrine_Query::create()->from('EmployeeWorkShift ews')
                            ->where('ews.emp_number =?', $empNumber);

            return $q->fetchOne();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Save Employee Tax Exemptios
     * @param EmpUsTaxExemption $empUsTaxExemption
     * @returns EmpUsTaxExemption
     * @throws DaoException
     */
    public function saveEmployeeTaxExemptions(EmpUsTaxExemption $empUsTaxExemption) {
        
        try {
            
            $empUsTaxExemption->save();
            
            return $empUsTaxExemption;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Get membership details for given employee
     * @param int $empNumber 
     * @param int $membershipId
     * @return array EmployeeMembership 
     */
    public function getEmployeeMemberships($empNumber, $membershipId = null) {

        try {
            
            $q = Doctrine_Query::create()->from('EmployeeMembership em')
                                        ->leftJoin('em.Membership m')
                                        ->where('em.empNumber = ?', $empNumber);
            
            if (!empty($membershipId)) {
                $q->andWhere("em.membershipId = ?", $membershipId);
            }
            
            $q->orderBy('m.name ASC');

            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Membership Detail
     * @param $empNumber 
     * @param $membershipIds
     * @return integer
     */
    public function deleteEmployeeMemberships($empNumber, $membershipIds = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeMembership')
                                         ->where('empNumber = ?', $empNumber);
            
            if (is_array($membershipIds) && count($membershipIds) > 0) {                
                $q->whereIn('membershipId', $membershipIds);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd    
        
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
                            ->leftJoin('c.PayGradeCurrency s')
                            ->where('s.pay_grade_id = ?', $salaryGrade)
                            ->andWhere('c.currency_id NOT IN (SELECT e.currency_id FROM EmployeeSalary e WHERE e.emp_number = ? AND e.sal_grd_code = ?)'
                                    , array($empNumber, $salaryGrade));

            return $q->execute(array(), $hydrateMode);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Save EmployeeSalary
     * @param EmployeeSalary $empBasicsalary
     * @returns EmployeeSalary
     * @throws DaoException
     */
    public function saveEmployeeSalary(EmployeeSalary $empBasicsalary) {
        
        try {
            
            $empBasicsalary->save();
            
            return $empBasicsalary;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    /**
     * Delete Salary
     * @param int $empNumber
     * @param array $salaryIds
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeSalaryComponents($empNumber, $salaryIds = null) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('EmployeeSalary')
                                         ->where('emp_number = ?', $empNumber);
            
            if (is_array($salaryIds) && count($salaryIds) > 0) {                
                $q->whereIn('id', $salaryIds);                
            }
            
            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd     
        
    }
    
    /**
     * Get Salary Record(s) for given employee
     * 
     * @version 2.6.11
     * @param int $empNumber Employee number
     * @param int $empSalaryId Employee Basic Salary ID
     * 
     * @return Collection/EmbBasicsalary  If $empSalaryId is given returns matching 
     * EmbBasicsalary or false if not found. If $empSalaryId is not given, returns 
     * EmbBasicsalary collection. (Empty collection if no records available)
     * @throws DaoException
     * 
     * @todo Exceptions should preserve previous exception
     */
    public function getEmployeeSalaries($empNumber, $empSalaryId = null) {
        try {
            $q = Doctrine_Query::create()
                ->from('EmployeeSalary s')
                ->leftJoin('s.currencyType c')
                ->where('s.emp_number = ?', $empNumber);

            if (!is_null($empSalaryId)) {
                $q->andwhere('s.id = ?', $empSalaryId);
                return $q->fetchOne();
            }

            $q->orderBy('s.salary_component ASC');
            return $q->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }     

    /**
     * get supervisor list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getImmediateSupervisors($empNumber) {

        try {
            $q = Doctrine_Query :: create()
                            ->select('rt.*, s.firstName, s.lastName, s.middleName, rm.*')
                            ->from('ReportTo rt')
                            ->leftJoin('rt.supervisor as s')
                            ->leftJoin('rt.ReportingMethod as rm')
                            ->where('rt.erep_sub_emp_number =?', $empNumber)
                            ->orderBy('s.lastName ASC, s.firstName ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * get subordinate list
     * @param $empNumber
     * @return Doctine collection ReportTo
     */
    public function getSubordinateListForEmployee($empNumber) {

        try {
            $q = Doctrine_Query :: create()->from('ReportTo rt')
                            ->select('rt.*, s.empNumber, s.firstName, s.lastName, s.middleName, rm.*')
                            ->leftJoin('rt.subordinate as s')
                            ->leftJoin('rt.ReportingMethod as rm')                    
                            ->where('rt.erep_sup_emp_number =?', $empNumber)
                            ->orderBy('s.lastName ASC, s.firstName ASC');
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get report to details object
     * @param int $supNumber $subNumber $reportingMethod
     * @return ReportTo object
     */
    public function getReportToObject($supNumber, $subNumber) {

        try {
            $q = Doctrine_Query::create()->from('ReportTo rt')
                            ->where('rt.erep_sup_emp_number =?', $supNumber)
                            ->andWhere('rt.erep_sub_emp_number =?', $subNumber);

            return $q->fetchOne();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete reportTo object
     * @param int $supNumber $subNumber $reportingMethod
     * @return boolean
     */
    public function deleteReportToObject($supNumber, $subNumber, $reportingMethod) {

        try {
            $q = Doctrine_Query::create()
                            ->delete()
                            ->from('ReportTo rt')
                            ->where('rt.erep_sup_emp_number =?', $supNumber)
                            ->andWhere('rt.erep_sub_emp_number =?', $subNumber)
                            ->andWhere('rt.erep_reporting_mode =?', $reportingMethod);

            $executed = $q->execute();

            if ($executed > 0) {
                return true;
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if user with given userId is a admin
     * @param string $userId
     * @return bool - True if given user is a admin, false if not
     */
    public function isAdmin($userId) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('SystemUser')
                            ->where('id = ?', $userId)
                            ->andWhere('deleted = ?', SystemUser::UNDELETED)
                            ->andWhere('status = ?', SystemUser::ENABLED)
                            ->andWhere('user_role_id = ?', SystemUser::ADMIN_USER_ROLE_ID);

            $result = $q->fetchOne();
            
            if ($result instanceof SystemUser) {
                return true;
            }
            
            return false;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getEmailList() {
        try {
            $q = Doctrine_Query :: create()
                            ->select('e.emp_work_email, e.emp_oth_email')
                            ->from('Employee e');

            return $q->fetchArray();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        
    }

    public function terminateEmployment(EmployeeTerminationRecord $employeeTerminationRecord) {

        try {
            
            $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
            $connection->beginTransaction();            
            
            /* Saving EmployeeTerminationRecord */
            $employeeTerminationRecord->save();
            
            /* Updating employee record */
            $q = Doctrine_Query :: create()->update('Employee')
                            ->set('termination_id', '?', $employeeTerminationRecord->getId())
                            ->where('empNumber = ?', $employeeTerminationRecord->getEmpNumber());
            
            $q->execute();
            
            $connection->commit();
            
            return $employeeTerminationRecord;
            
        // @codeCoverageIgnoreStart    
        } catch (Exception $e) {
            
            $connection->rollback();
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
            
        }
        // @codeCoverageIgnoreEnd
    }

    public function activateTerminatedEmployment($empNumber) {

        try {
            $q = Doctrine_Query :: create()->update('Employee')
                            ->set('termination_id', 'NULL')
                            ->where('empNumber = ?', $empNumber);
            return $q->execute();
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getEmployeeTerminationRecord($terminatedId) {

        try {
            return Doctrine::getTable('EmployeeTerminationRecord')->find($terminatedId);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
     /**
     * Retrieve assigned Currency List
     * @param String $salaryGrade
     * @param boolean $asArray
     * @returns Collection
     * @throws DaoException
     */
    public function getAssignedCurrencyList($salaryGrade, $asArray = false) {
        try {
            $hydrateMode = ($asArray) ? Doctrine :: HYDRATE_ARRAY : Doctrine :: HYDRATE_RECORD;
            $q = Doctrine_Query :: create()->select('c.currency_id, c.currency_name')
                            ->from('CurrencyType c')
                            ->leftJoin('c.PayGradeCurrency s')
                            ->where('s.pay_grade_id = ?', $salaryGrade)
                            ->orderBy('c.currency_name ASC');

            return $q->execute(array(), $hydrateMode);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getEmployeeByEmployeeId($employeeId) {

        try {

            $q = Doctrine_Query::create()
                               ->from('Employee')
                               ->where('employeeId = ?', trim($employeeId));

            $result = $q->fetchOne();
            
            if (!$result) {
                return null;
            }

            return $result;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }
    
    /**
     * Get Employees under the given subunits
     * @param string/array $subUnits Sub Unit IDs
     * @param type $includeTerminatedEmployees if true, includes terminated employees
     * @return Array of Employees
     */
    public function getEmployeesBySubUnit($subUnits, $includeTerminatedEmployees = false) {
        try {

            $q = Doctrine_Query::create()
                               ->from('Employee');
            if (is_array($subUnits)) {
                $q->whereIn('work_station', $subUnits);
            } else {
                $q->where('work_station = ?', $subUnits);
            }
            
            if (!$includeTerminatedEmployees) {                
                $q->andwhere("termination_id IS NULL");
            }

            return $q->execute();

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
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
                'GROUP_CONCAT(s.emp_firstname, \'## \', s.emp_middle_name, \'## \', s.emp_lastname) AS supervisors,'.
                'GROUP_CONCAT(DISTINCT loc.id, \'##\',loc.name) AS locationIds';
              

        $query = 'FROM hs_hr_employee e ' .
                '  LEFT JOIN ohrm_subunit cs ON cs.id = e.work_station ' .
                '  LEFT JOIN ohrm_job_title j on j.id = e.job_title_code ' .
                '  LEFT JOIN ohrm_employment_status es on e.emp_status = es.id ' .
                '  LEFT JOIN hs_hr_emp_reportto rt on e.emp_number = rt.erep_sub_emp_number ' .
                '  LEFT JOIN hs_hr_employee s on s.emp_number = rt.erep_sup_emp_number '.
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
                    } else if ($searchField == 'supervisorId') {
                        
                        $subordinates = $this->_getSubordinateIds($searchBy);
                        if (count($subordinates) > 0) {
                            $conditions[] = ' e.emp_number IN (' . implode(',', $subordinates) . ') ';
                        } else {                        
                            $conditions[] = ' s.emp_number = ? ';
                            $bindParams[] = $searchBy;
                        }
                    } else if ($searchField == 'employee_id_list') {
                        $conditions[] = ' e.emp_number IN (' . implode(',', $searchBy) . ') ';
                    } else if ($searchField == 'supervisor_name') {
                       // $conditions[] = $field . ' LIKE ? ';
                        $conditions[] =  ' e.emp_number IN ((SELECT srt.erep_sub_emp_number  FROM hs_hr_emp_reportto  srt LEFT JOIN hs_hr_employee se on ( srt.erep_sup_emp_number = se.emp_number )
                        					WHERE '. $field.' LIKE ? ))';
                        // Replace multiple spaces in string with wildcards
                        $value = preg_replace('!\s+!', '%', $searchBy);
                        $bindParams[] = '%' . $value . '%';
                       
                       // $conditions[] = " e.emp_number IN (SELECT erep_sup_emp_number FROM hs_hr_emp_reportto where erep_sub_emp_number = e.emp_number))";
                    } else if ($searchField == 'employee_name') {
                        $conditions[] = $field . ' LIKE ? ';
                        // Replace multiple spaces in string with wildcards
                        $value = preg_replace('!\s+!', '%', $searchBy);
                        $bindParams[] = '%' . $value . '%';
                    } elseif( $searchField == 'location' ) {
                        if (!empty($filters['location']) && $filters['location'] != '-1') {
                            $locations = explode(',', $filters['location']);
                            $bindParams = array_merge($bindParams, $locations);
                            $conditions[] = ' l.location_id IN (' . implode(',', array_fill(0, count($locations), '?')) . ') ';
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

        /* Sort subordinates direct first, then indirect, then by supervisor name */
        $order['rt.erep_reporting_mode'] = 'asc';

        if ($sortField != 'supervisor') {
            $order['s.emp_firstname'] = 'asc';
            $order['s.emp_lastname'] = 'asc';
        }
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

        $conn = Doctrine_Manager::connection();
        $statement = $conn->prepare($completeQuery);
        $result = $statement->execute($bindParams);
       
        if ($returnType == EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT) {
            $employees = new Doctrine_Collection(Doctrine::getTable('Employee'));

            if ($result) {
                while ($row = $statement->fetch() ) {
                    $employee = new Employee();

                    $employee->setEmpNumber($row['empNumber']);
                    $employee->setEmployeeId($row['employeeId']);
                    $employee->setFirstName($row['firstName']);
                    $employee->setMiddleName($row['middleName']);
                    $employee->setLastName($row['lastName']);
                    $employee->setTerminationId($row['terminationId']);

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
     * Get list of subordinate employee Ids as an array on integers
     * 
     * @return type Comma separated list or false if no subordinates
     */
    private function _getSubordinateIds($supervisorId) {

        $subordinatesList = $this->getSubordinateList($supervisorId, true);

        $ids = array();
        
        foreach ($subordinatesList as $employee) {        
            $ids[] = intval($employee->getEmpNumber());
        }        
        
        return $ids;
    }

}
