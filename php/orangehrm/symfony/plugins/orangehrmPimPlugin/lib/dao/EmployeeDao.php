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
class EmployeeDao extends BaseDao{

   /**
    * Save Employee
    * @param Employee $employee
    * @returns boolean
    * @throws DaoException
    */
   public function addEmployee(Employee $employee) {
      try {
         if($employee->getEmpNumber() == '') {
            $idGenService = new IDGeneratorService();
            $idGenService->setEntity($employee);
            $employee->setEmpNumber($idGenService->getNextID());
         }
         $employee->save();
         return true ;
      } catch(Exception $e) {
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
    * Retrieve Past Job Titles for a given Emp Number
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getPastJobTitles($empNumber) {
      try {
         $q = Doctrine_Query :: create()
           ->from('EmpJobtitleHistory h')
           ->where('h.emp_number = ?', $empNumber)
           ->andWhere('h.end_date IS NOT NULL')
           ->orderBy('h.start_date');
         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Past Sub Divisions by empNumber
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getPastSubdivisions($empNumber) {
      try {
         $q = Doctrine_Query :: create()
              ->from('EmpSubdivisionHistory h')
              ->where('h.emp_number = ?', $empNumber)
              ->andWhere('h.end_date IS NOT NULL')
              ->orderBy('h.start_date');
         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Past Locations by empNumber
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getPastLocations($empNumber) {
      try {
         $q = Doctrine_Query :: create()
            ->from('EmpLocationHistory h')
            ->where('h.emp_number = ?', $empNumber)
            ->andWhere('h.end_date IS NOT NULL')
            ->orderBy('h.start_date');
         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
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
         $q = Doctrine_Query :: create()->select('a.emp_number, a.attach_id, a.size, a.description, a.filename, a.file_type')
            ->from('EmpAttachment a')
            ->where('a.emp_number = ?', $empNumber);
         return $q->execute();
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
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
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
         return Doctrine :: getTable('EmpAttachment')->find(array(
            'emp_number' => $empNumber,
            'attach_id' => $attachId
         ));
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
                                        ->set('smoker', '?', !empty($employee->smoker)?$employee->smoker:0)
                                        ->set('emp_gender', '?', $employee->emp_gender)
                                        ->set('militaryService', '?', $employee->militaryService);

         if (empty ($employee->emp_dri_lice_exp_date)) {
            $q->set('emp_dri_lice_exp_date', 'NULL');
         } else {
            $q->set('emp_dri_lice_exp_date', '?', $employee->emp_dri_lice_exp_date);
         }

         if (empty ($employee->nation_code)) {
            $q->set('nation_code', 'NULL');
         } else {
            $q->set('nation_code', '?', $employee->nation_code);
         }

         if (empty ($employee->ethnic_race_code)) {
            $q->set('ethnic_race_code', 'NULL');
         } else {
            $q->set('ethnic_race_code', '?', $employee->ethnic_race_code);
         }

         if (!$isESS) {
         $q->set('employeeId', '?', $employee->employeeId)->set('ssn', '?', $employee->ssn)->set('sin', '?', $employee->sin)->set('licenseNo', '?', $employee->licenseNo);

            if (empty ($employee->emp_birthday)) {
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

         if (!empty ($employee->job_title_code)) {
             $q->set('job_title_code', '?', $employee->job_title_code);
         }

         if (!empty ($employee->emp_status)) {
             $q->set('emp_status', '?', $employee->emp_status);
         }

         if (!empty ($employee->eeo_cat_code)) {
             $q->set('eeo_cat_code', '?', $employee->eeo_cat_code);
         }

         if (!empty ($employee->work_station)) {
             $q->set('work_station', '?', $employee->work_station);
         }

         if (!empty ($employee->joined_date)) {
             $q->set('joined_date', '?', $employee->joined_date);
         }

         /*if (!empty ($employee->terminated_date)) {
             $q->set('terminated_date', '?', $employee->terminated_date);
         }*/

         if (!empty ($employee->termination_reason)) {
             $q->set('termination_reason', '?', $employee->termination_reason);
         }

         $q->where('empNumber = ?', $employee->empNumber);
         $result = $q->execute();

         // if a job title is defined, update job title history
         if (!empty ($employee->job_title_code)) {

            // find if current history item is the same job
            $q = Doctrine_Query :: create($conn)->select('h.*')->from('EmpJobtitleHistory h')->where('h.emp_number = ?', $employee->empNumber)->andWhere('h.end_date IS NULL')->andWhere('h.code = ?', $employee->job_title_code);
            $result = $q->execute();

            // if not same job title, update history
            if ($result->count() == 0) {
              // find job title name
              $q = Doctrine_Query :: create($conn)->select('j.jobtit_name')->from('JobTitle j')->where('j.id = ?', $employee->job_title_code);
              $result = $q->execute();

              if ($result->count() != 1) {
                  throw new DaoException('jobtitle ' . $employee->job_title_code . ' not found');
              }

              $jobTitleName = $result[0]->name;

              // update end_date for current item
              $q = Doctrine_Query :: create($conn)->update('EmpJobtitleHistory h')->set('h.end_date', 'NOW()')->where('h.emp_number = ?', $employee->empNumber)->andWhere('h.end_date IS NULL');
              $results = $q->execute();

              // add new history item
              $history = new EmpJobtitleHistory();
              $history->emp_number = $employee->empNumber;
              $history->code = $employee->job_title_code;
              $history->name = $jobTitleName;
              $history->start_date = new Doctrine_Expression('NOW()');
              $history->save();
            }
         }

         // update employee subdivision history
         if (!empty ($employee->work_station)) {

            // find if current history item is the location
            $q = Doctrine_Query :: create($conn)->select('h.*')->from('EmpSubdivisionHistory h')->where('h.emp_number = ?', $employee->empNumber)->andWhere('h.end_date IS NULL')->andWhere('h.code = ?', $employee->work_station);
            $result = $q->execute();

            // if not same sub division, update history
            if ($result->count() == 0) {
               // find location name
               $q = Doctrine_Query :: create($conn)->select('c.title')->from('CompanyStructure c')->where('c.id = ?', $employee->work_station);
               $result = $q->execute();

               if ($result->count() != 1) {
                  throw new DaoException('company structure position ' . $employee->work_station . ' not found');
               }

               $title = $result[0]->title;

               // update end_date for current item
               $q = Doctrine_Query :: create($conn)->update('EmpSubdivisionHistory h')->set('h.end_date', 'NOW()')->where('h.emp_number = ?', $employee->empNumber)->andWhere('h.end_date IS NULL');
               $results = $q->execute();

               // add new history item
               $history = new EmpSubdivisionHistory();
               $history->emp_number = $employee->empNumber;
               $history->code = $employee->work_station;
               $history->name = $title;
               $history->start_date = new Doctrine_Expression('NOW()');
               $history->save();
            }
         }
         $conn->commit();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Available Location
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getAvailableLocations($empNumber) {
      try {
         $q = Doctrine_Query :: create()->select('l.loc_code, l.loc_name')
           ->from('Location l')
           ->leftJoin('l.EmpLocation e WITH e.emp_number = ' . $empNumber)
           ->where('e.emp_number IS NULL');

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Available Location
    * @param int $empNumber
    * @param String $locationCode
    * @returns Collection
    * @throws DaoException
    */
   public function assignLocation($empNumber, $locationCode) {
      $conn = Doctrine_Manager::connection();
      $conn->beginTransaction();

      try {
         // select location to get location name
         $location = Doctrine :: getTable('Location')->find($locationCode);

         if ($location) {
            // save emp - location (if not exists)
            $empLoc = Doctrine :: getTable('EmpLocation')->find(array(
              'emp_number' => $empNumber,
              'loc_code' => $locationCode
            ));
            if (!$empLoc) {
               $empLocation = new EmpLocation();
               $empLocation->empNumber = $empNumber;
               $empLocation->loc_code = $locationCode;
               $empLocation->save();
            }

            // Search for non-finished history items with same location
            $q = Doctrine_Query :: create($conn)->select('h.*')->from('EmpLocationHistory h')->where('h.emp_number = ?', $empNumber)->andWhere('h.code = ?', $locationCode)->andWhere('h.end_date IS NULL');
            $results = $q->execute();

            // add new history item with null end_date
            if (count($results) == 0) {
               $history = new EmpLocationHistory();
               $history->emp_number = $empNumber;
               $history->code = $locationCode;
               $history->name = $location->loc_name;
               $history->start_date = new Doctrine_Expression('NOW()');
               $history->save();
            } else {
               // this shouldn't be reached, but update start_date anyway
               foreach ($results as $result) {
                  $result->start_date = new Doctrine_Expression('NOW()');
                  $result->save();
               }
            }
         }
         $conn->commit();
         return true;
      } catch (Exception $e) {
         $conn->rollBack();
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Location
    * @param int $empNumber
    * @param String $locationCode
    * @returns boolean
    * @throws DaoException
    */
   public function removeLocation($empNumber, $locationCode) {
      $conn = Doctrine_Manager :: connection();
      $conn->beginTransaction();

      try {
         $q = Doctrine_Query :: create($conn)
            ->delete('EmpLocation el')
            ->where('empNumber = ?', $empNumber)
            ->andWhere('el.loc_code = ?', $locationCode);
         
         sfContext :: getInstance()->getLogger()->info('SQL = ' . $q->getSqlQuery());
         $result = $q->execute();
         sfContext :: getInstance()->getLogger()->info('result = ' . $result);

         // if deleted, delete history item as well.
         if ($result > 0) {
            // Search for non-finished history items with same location
            $q = Doctrine_Query :: create($conn)
               ->update('EmpLocationHistory h')
               ->set('h.end_date', 'NOW()')
               ->where('h.emp_number = ?', $empNumber)
               ->andWhere('h.code = ?', $locationCode)
               ->andWhere('h.end_date IS NULL');

            sfContext :: getInstance()->getLogger()->info('SQL = ' . $q->getSqlQuery());
            $result = $q->execute();
         }
         $conn->commit();
         return true;
      } catch (Exception $e) {
         $conn->rollBack();
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete JobTitleHistory
    * @param int $empNumber
    * @param String $jobTitlesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteJobTitleHistory($empNumber, $jobTitlesToDelete) {
      try {
         // Delete only complete history items (UI displays only complete items)
         $q = Doctrine_Query :: create()
            ->delete('EmpJobtitleHistory h')
            ->whereIn('id', $jobTitlesToDelete)
            ->andwhere('emp_number = ?', $empNumber)
            ->andWhere('h.end_date IS NOT NULL');

         $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete SubDivisionHistory
    * @param int $empNumber
    * @param String $subDivisionsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSubDivisionHistory($empNumber, $subDivisionsToDelete) {
      try {
         // Delete only complete history items (UI displays only complete items)
         $q = Doctrine_Query :: create()
            ->delete('EmpSubdivisionHistory h')
            ->whereIn('id', $subDivisionsToDelete)
            ->andwhere('emp_number = ?', $empNumber)
            ->andWhere('h.end_date IS NOT NULL');
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }


   /**
    * Delete LocationHistory
    * @param int $empNumber
    * @param String $locationsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteLocationHistory($empNumber, $locationsToDelete) {
      try {
         // Delete only complete history items (UI displays only complete items)
         $q = Doctrine_Query :: create()->delete('EmpLocationHistory h')
                 ->whereIn('id', $locationsToDelete)
                 ->andwhere('emp_number = ?', $empNumber)
                 ->andWhere('h.end_date IS NOT NULL');
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Update JobHistory
    * @param int $empNumber
    * @param String $params
    * @returns boolean
    * @throws DaoException
    */
   public function updateJobHistory($empNumber, $params) {
      $historyItems = array ();
      $conn = Doctrine_Manager :: connection();
      $conn->beginTransaction();

      try {
         // Get job title history
         if (isset ($params['jobTitleHisId'])) {
            $jobTitleIds = $params['jobTitleHisId'];
            $jobTitleCodes = $params['jobTitleHisCode'];
            $jobTitleFromDates = $params['jobTitleHisFromDate'];
            $jobTitleToDates = $params['jobTitleHisToDate'];

            for ($i = 0; $i < count($jobTitleIds); $i++) {
              $id = $jobTitleIds[$i];
              $code = $jobTitleCodes[$i];
              $startDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($jobTitleFromDates[$i]);
              $endDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($jobTitleToDates[$i]);
              $q = Doctrine_Query :: create($conn)->update('EmpJobtitleHistory h')->set('h.end_date', '?', $endDate)->set('h.start_date', '?', $startDate)->where('h.id = ?', $id)->andWhere('h.code = ?', $code)->andWhere('h.emp_number = ?', $empNumber);
              $result = $q->execute();
            }
         }

         // Get sub division history
         if (isset ($params['subDivHisId'])) {
            $subDivIds = $params['subDivHisId'];
            $subDivCodes = $params['subDivHisCode'];
            $subDivFromDates = $params['subDivHisFromDate'];
            $subDivToDates = $params['subDivHisToDate'];

            for ($i = 0; $i < count($subDivIds); $i++) {
               $id = $subDivIds[$i];
               $code = $subDivCodes[$i];
               $startDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($subDivFromDates[$i]);
               $endDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($subDivToDates[$i]);
               $q = Doctrine_Query :: create($conn)->update('EmpSubdivisionHistory h')->set('h.end_date', '?', $endDate)->set('h.start_date', '?', $startDate)->where('h.id = ?', $id)->andWhere('h.code = ?', $code)->andWhere('h.emp_number = ?', $empNumber);
               $result = $q->execute();
            }
         }

         // Get location history
         if (isset ($params['locHisId'])) {
            $locIds = $params['locHisId'];
            $locCodes = $params['locHisCode'];
            $locFromDates = $params['locHisFromDate'];
            $locToDates = $params['locHisToDate'];

            for ($i = 0; $i < count($locIds); $i++) {
              $id = $locIds[$i];
              $startDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($locFromDates[$i]);
              $endDate = LocaleUtil :: getInstance()->convertToStandardDateFormat($locToDates[$i]);
              $code = $locCodes[$i];

              $q = Doctrine_Query :: create($conn)->update('EmpLocationHistory h')->set('h.end_date', '?', $endDate)->set('h.start_date', '?', $startDate)->where('h.id = ?', $id)->andWhere('h.code = ?', $code)->andWhere('h.emp_number = ?', $empNumber);
              $result = $q->execute();
            }
         }
         $conn->commit();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Contracts
    * @param int $empNumber
    * @param array() $contractsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteContracts($empNumber, $contractsToDelete) {
      try {
         //Delete contracts
         $q = Doctrine_Query::create()->delete('EmpContract c')
                 ->whereIn('contract_id', $contractsToDelete)
                 ->andwhere('emp_number = ?', $empNumber);
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmpContract
    * @param EmpContract $empContract
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmpContract(EmpContract $empContract) {
      try {
         if($empContract->getContractId() == "") {
            $q = Doctrine_Query::create()
               ->select('MAX(c.contract_id)')
               ->from('EmpContract c')
               ->where('c.emp_number = ?', $empContract->getEmpNumber());
            $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
            $id = is_null($result[0]['MAX']) ? 1 : $result[0]['MAX'] + 1;
            $empContract->setContractId($id);
         }
         $empContract->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Licenses
    * @param int $empNumber
    * @param array() $licensesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteLicenses($empNumber, $licensesToDelete) {
      try {
         $q = Doctrine_Query :: create()
            ->delete('EmployeeLicense l')
            ->whereIn('code', $licensesToDelete)
            ->andwhere('empNumber = ?', $empNumber);
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeLicense
    * @param EmployeeLicense $employeeLicense
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeLicense(EmployeeLicense $employeeLicense) {
      try {
         $employeeLicense->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeSkill
    * @param EmployeeSkill $empSkills
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeSkill(EmployeeSkill $empSkills) {
      try {
         $empSkills->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Skills
    * @param int $empNumber
    * @param array() $skillsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSkills($empNumber, $skillsToDelete) {
      try {
         $q = Doctrine_Query :: create()->delete('EmployeeSkill s')
                 ->whereIn('code', $skillsToDelete)
                 ->andwhere('emp_number = ?', $empNumber);
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeEducation
    * @param EmployeeEducation $empEdu
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeEducation(EmployeeEducation $empEdu) {
      try {
         $empEdu->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Education
    * @param int $empNumber
    * @param array() $educationsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteEducation($empNumber, $educationsToDelete) {
      try {
         $q = Doctrine_Query :: create()->delete('EmployeeEducation s')
                 ->whereIn('code', $educationsToDelete)
                 ->andwhere('emp_number = ?', $empNumber);
         $result = $q->execute();
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeLanguage
    * @param EmployeeLanguage $empLang
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeLanguage(EmployeeLanguage $empLang) {
      try {
         $empLang->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Languages
    * @param int $empNumber
    * @param array() $languagesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteLanguages($empNumber, $languagesToDelete) {
      try {
         // Skip if no languages because running the following query
         // with no languages will delete all this employee's assigned
         // languages
         if (count($languagesToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('EmployeeLanguage s');
            foreach ($languagesToDelete as $lang) {
              $q->orWhere('code = ? AND lang_type = ?', array_values($lang));
            }
            $q->andWhere('emp_number = ?', $empNumber);
            $result = $q->execute();
         }
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeMemberDetail
    * @param EmployeeMemberDetail $empMemberDetail
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeMemberDetail(EmployeeMemberDetail $empMemberDetail) {
      try {
         $empMemberDetail->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Memberships
    * @param int $empNumber
    * @param array() $membershipsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteMemberships($empNumber, $membershipsToDelete) {
      try {
         // Skip if no memberships because running the following query
         // with no memberships will delete all this employee's assigned
         // memberships
         if (count($membershipsToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('EmployeeMemberDetail s');
            foreach ($membershipsToDelete as $lang) {
               $q->orWhere('membship_code = ? AND membtype_code = ?', array_values($lang));
            }
            $q->andWhere('emp_number = ?', $empNumber);
            $result = $q->execute();
         }
         return true;
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
         // salarys
         if (count($salaryToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('EmpBasicsalary s');
            foreach ($salaryToDelete as $sal) {
               $q->orWhere('sal_grd_code = ? AND currency_id = ?', array_values($sal));
            }
            $q->andWhere('emp_number = ?', $empNumber);
            $result = $q->execute();
         }
         return true;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve All Licenses
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getAvailableLicenses($empNumber) {
      try {
         $q = Doctrine_Query :: create()->select('l.*')
           ->from('Licenses l')
           ->leftJoin('l.EmployeeLicense el WITH el.empNumber = ' . $empNumber)
           ->where('el.empNumber IS NULL');

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve All Memberships
    * @param int $empNumber
    * @param String $membershipType
    * @returns Collection
    * @throws DaoException
    */
   public function getAvailableMemberships($empNumber, $membershipType) {
      try {
         $q = Doctrine_Query :: create()->select('m.membship_code, m.membship_name')
           ->from('Membership m')
           ->leftJoin('m.EmployeeMemberDetail em WITH em.emp_number = ' . $empNumber)
           ->where('m.membtype_code = ?', $membershipType)
           ->andWhere('em.emp_number IS NULL');

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve All Education List
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getAvailableEducationList($empNumber) {
      try {
         $q = Doctrine_Query :: create()->select('e.*')
           ->from('Education e')
           ->leftJoin('e.EmployeeEducation ee WITH ee.emp_number = ' . $empNumber)
           ->where('ee.emp_number IS NULL');

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve All Skills for an employee number
    * @param int $empNumber
    * @returns Collection
    * @throws DaoException
    */
   public function getAvailableSkills($empNumber) {
      try {
         $q = Doctrine_Query :: create()->select('s.skill_code, s.skill_name')
           ->from('Skill s')
           ->leftJoin('s.EmployeeSkill es WITH es.emp_number = ' . $empNumber)
           ->where('es.emp_number IS NULL');

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
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
         if(is_array($emergencyContactsToDelete)) {
            
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
    * Delete Direct Debit
    * @param int $empNumber
    * @param array() $directDebitToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteDirectDebit($empNumber, $directDebitToDelete) {
      try {
         if(is_array($directDebitToDelete)) {
            // Delete direct debit
            $q = Doctrine_Query :: create()->delete('EmpDirectdebit ec')
              ->whereIn('seqno', $directDebitToDelete)
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
    * Delete WorkExperiences
    * @param int $empNumber
    * @param array() $workExperienceToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteWorkExperiences($empNumber, $workExperienceToDelete) {
      try {
         if(is_array($workExperienceToDelete)) {
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
    * Delete Immigration
    * @param int $empNumber
    * @param array() $entriesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteImmigration($empNumber, $entriesToDelete) {
      try {
         if(is_array($entriesToDelete)) {
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
    * Delete Dependents
    * @param int $empNumber
    * @param array() $entriesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteDependents($empNumber, $entriesToDelete) {
      try {
         if(is_array($entriesToDelete)) {
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
         if(is_array($entriesToDelete)) {
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
    * Delete Supervisors
    * @param int $empNumber
    * @param array() $supervisorsToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSupervisors($empNumber, $supervisorsToDelete) {
      try {
         if (count($supervisorsToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('ReportTo r');
            foreach ($supervisorsToDelete as $sup) {
               $q->orWhere('supervisorId = ? AND reportingMode = ?', array_values($sup));
            }
            $q->andWhere('subordinateId = ?', $empNumber);
            $result = $q->execute();
            return true;
         }
         return false;
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Subordinates
    * @param int $empNumber
    * @param array() $subordinatesToDelete
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSubordinates($empNumber, $subordinatesToDelete) {
      try {
         // Skip if no subordinates because running the following query
         // with no subordinates will delete all this employee's assigned
         // subordinate/subordinates
         if (count($subordinatesToDelete) > 0) {
            $q = Doctrine_Query :: create()->delete('ReportTo r');
            foreach ($subordinatesToDelete as $sub) {
               $q->orWhere('subordinateId = ? AND reportingMode = ?', array_values($sub));
            }
            $q->andWhere('supervisorId = ?', $empNumber);
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
            $q = Doctrine_Query :: create()->delete('EmpAttachment a')
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
      } catch(Exception $e) {
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
         $jsonString = array ();
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
   public function getSupervisorEmployeeChain($supervisorId){
      try {
         $employeeList	=	array();

         $q = Doctrine_Query::create()
            ->select("rt.supervisorId,emp.*")
            ->from('ReportTo rt')
            ->leftJoin('rt.subordinate emp')
            ->where("rt.supervisorId=$supervisorId");

         $reportToList = $q->execute();
         foreach( $reportToList as $reportTo) {
            array_push($employeeList,$reportTo->getSubordinate());
            $list	=	$this->getSupervisorEmployeeChain($reportTo->getSubordinateId());
            if(count($list)>0)
               foreach($list as $employee )
                  array_push($employeeList,$employee);
         }
         return  $employeeList;
      } catch(Exception $e) {
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
         if(is_array($empList) && count($empList) > 0) {
            $q = Doctrine_Query::create()
               ->delete('Employee')
               ->whereIn('empNumber', $empList);
            
            return $q->execute();
         }
         return 0;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Direct Debit entries for given employee
    * @param  $empNumber Employee Number
    * @return Array of direct debit entries
    */
   public function getEmployeeDirectDebit($empNumber) {
      try {
            $q = Doctrine_Query::create()
               ->from('EmpDirectdebit')
               ->where('emp_number = ?', $empNumber);
            return $q->execute();

      } catch(Exception $e) {
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

      } catch(Exception $e) {
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

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
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
           $directDebit->save();
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
          $tax->save();
       } catch (Exception $e) {
           throw new PIMServiceException($e->getMessage());
       }

   }
}