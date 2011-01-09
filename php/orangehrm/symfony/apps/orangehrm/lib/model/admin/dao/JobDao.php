<?php
/**
 * Description of JobDao
 *
 * @author Sujith T
 */
class JobDao extends BaseDao {

   /**
    * Saving Job Category
    * @param JobCategory $jobCategory
    * @returns boolean
    * @throws DaoException, DataDuplicationException
    */
   public function saveJobCategory(JobCategory $jobCategory) {
      try {
         $q = Doctrine_Query::create()
             ->from('JobCategory j')
             ->where('j.eec_desc = ?', $jobCategory->getEecDesc());

         if (!empty($jobCategory->eec_code)) {
            $q->andWhere('j.eec_code <> ?', $jobCategory->eec_code) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException();
         }

         if ($jobCategory->getEecCode() == '') {
            $idGenService = new IDGeneratorService();
            $idGenService->setEntity($jobCategory);
            $jobCategory->setEecCode( $idGenService->getNextID());
         }
         
         $jobCategory->save();
         return true;
      } catch (Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Job Category List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getJobCategoryList($orderField = 'eec_code', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('JobCategory')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Job Category
    * @param array() $jobCategoryList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteJobCategory($jobCategoryList = array()) {
      try {
         if(is_array($jobCategoryList)) {
            $q = Doctrine_Query::create()
               ->delete('JobCategory')
               ->whereIn('eec_code', $jobCategoryList );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Searching JobCategory
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchJobCategory($searchMode, $searchValue) {
      try {
         $q = Doctrine_Query::create( )
            ->from('JobCategory')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Returns JobCategory by Id. This need to be refactored to retrieve JC object, need to change all references
    * @param String $id
    * @returns JobCategory
    * @throws DaoException
    */
   public function readJobCategory($id) {
      try {
         return Doctrine::getTable('JobCategory')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves SalaryGrade
    * @param String $id
    * @returns SalaryGrade. this is preserved as in the original but better to return boolean
    * @throws DaoException
    */
   public function saveSalaryGrade(SalaryGrade $salaryGrade) {
      try {
         if($salaryGrade->getSalGrdCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($salaryGrade);
            $salaryGrade->setSalGrdCode( $idGenService->getNextID() );
         }

         $salaryGrade->save();
         return $salaryGrade;
      } catch(Doctrine_Connection_Mysql_Exception $e) {
         throw new DataDuplicationException($e->getMessage());
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve SalaryGrade List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getSalaryGradeList($orderField = 'sal_grd_code', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('SalaryGrade')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete SalaryGrade
    * @param array() $saleryGradeList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSalaryGrade($saleryGradeList = array()) {
      try {
         if(is_array($saleryGradeList)) {
            $q = Doctrine_Query::create()
               ->delete('SalaryGrade')
               ->whereIn('sal_grd_code', $saleryGradeList );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Searching SalaryGrade
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchSalaryGrade($searchMode, $searchValue) {
      try {
         $q = Doctrine_Query::create( )
            ->from('SalaryGrade');

         if($searchMode == "sal_grd_name") {
            $q->where("$searchMode LIKE ?", trim($searchValue) . "%");
         } else {
            $q->where("$searchMode = ?", trim($searchValue));
         }
         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Return SalaryGrade from Id
    * @param String $id
    * @returns SaleryGrade. Need to update the references to use model object, can do it later
    * @throws DaoException
    */
   public function readSalaryGrade($id) {
      try {
         return Doctrine::getTable('SalaryGrade')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saving SalaryCurrencyDetail
    * @param SalaryCurrencyDetail $salaryCurrencyDetail
    * @returns boolean
    * @throws DaoException
    */
   public function saveSalleryGradeCurrency(SalaryCurrencyDetail $salaryCurrencyDetail) {
      try {
         if(!$this->isExistingSalleryGradeCurrency($salaryCurrencyDetail)) {
            $salaryCurrencyDetail->save();
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Checks for existing SalaryCurrencyDetail for a given currency
    * @param SalaryCurrencyDetail $salaryCurrencyDetail
    * @returns boolean
    * @throws DaoException
    */
   public function isExistingSalleryGradeCurrency(SalaryCurrencyDetail $salaryCurrencyDetail) {
      try {
         $q = Doctrine_Query::create()
             ->from('SalaryCurrencyDetail')
             ->where("sal_grd_code='". $salaryCurrencyDetail->getSalGrdCode(). "' AND currency_id='". $salaryCurrencyDetail->getCurrencyId() ."'");

         if($q->count() > 0) {
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve SalaryGradeCurrency by code
    * @param String $salaryGradeCode
    * @returns SalleryGradeCurrency/Collection
    * @throws DaoException
    */
   public function getSalaryGradeCurrency($salaryGradeCode) {
      try {
         $q = Doctrine_Query::create()
             ->from('SalaryCurrencyDetail')
             ->where("sal_grd_code='$salaryGradeCode'");
         
         return $q->execute();

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete SalaryGradeCurrency
    * @param String $salaryGradeId
    * @param array() $salaryGradeCurrencyList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSalaryGradeCurrency($salaryGradeId, $salaryGradeCurrencyList = array()) {
      try {
	    	if(is_array($salaryGradeCurrencyList )) {
	        	$q = Doctrine_Query::create()
					    ->delete('SalaryCurrencyDetail')
					    ->where("sal_grd_code ='$salaryGradeId'")
					    ->whereIn('currency_id', $salaryGradeCurrencyList  );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
	    	}
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve SalaryCurrencyDetail
    * @param String $saleryGradeId
    * @param String $currency
    * @returns SalaryCurrencyDetail
    * @throws DaoException / catch and throw PIM exception for backward compatibility
    */
   public function getSalaryCurrencyDetail($salaryGrade, $currency) {
      try {
         return Doctrine::getTable('SalaryCurrencyDetail')->find(
                       array('sal_grd_code' => $salaryGrade,
                       'currency_id' => $currency));
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve EmployeeStatus List
    * @param String $orderField
    * @param String $orderBy
    * @param bool $includeTerminated - Include the Terminated state
    * @returns Collection
    * @throws DaoException
    */
   public function getEmployeeStatusList($orderField = 'id', $orderBy = 'ASC', $includeTerminated = true) {
      try {
         $q = Doctrine_Query::create()
            ->from('EmployeeStatus e');

         if ( !$includeTerminated ) {
             $q->where('e.id != ?', TERMINATED_STATUS);
         }

         $q->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve EmployeeStatus for a Job Title
    * @param String $jobTitleCode
    * @param array() $asArray
    * @returns Collection
    * @throws DaoException
    */
   public function getEmployeeStatusForJob($jobTitleCode, $asArray = false) {
      try {
            $hydrateMode = ($asArray) ? Doctrine::HYDRATE_ARRAY : Doctrine::HYDRATE_RECORD;

            $q = Doctrine_Query::create()
                ->select('s.id, s.name')
                ->from('EmployeeStatus s')
                ->leftJoin('s.JobTitleEmployeeStatus j ON s.id = j.estat_code')
                ->where('j.jobtit_code = ?', $jobTitleCode)
                ->orderBy('s.name');

            return $q->execute(array(), $hydrateMode);

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EmployeeStatus
    * @param EmployeeStatus $employeeStatus
    * @returns boolean
    * @throws DaoException
    */
   public function saveEmployeeStatus(EmployeeStatus $employeeStatus) {
      try {
         if( $employeeStatus->getId()=='') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($employeeStatus);
            $employeeStatus->setId( $idGenService->getNextID() );
         }
         $employeeStatus->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete EmployeeStatus
    * @param array() $employeeStatusList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteEmployeeStatus($employeeStatusList = array()) {
      try {
         if(is_array($employeeStatusList )) {
            $q = Doctrine_Query::create()
                   ->delete('EmployeeStatus')
                   ->whereIn('id', $employeeStatusList );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search EmployeeStatus
    * @param String $searchMode
    * @param String $searchValue
    * @returns boolean
    * @throws DaoException
    */
  	public function searchEmployeeStatus($searchMode, $searchValue) {
      try {
         $q	= 	Doctrine_Query::create( )
            ->from('EmployeeStatus')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search EmployeeStatus by Id
    * @param String $id
    * @returns EmployeeStatus. need to update to return the model, check the references before updating
    * @throws DaoException
    */
   public function readEmployeeStatus($id) {
      try {
         return Doctrine::getTable('EmployeeStatus')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve JobSpecifications List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getJobSpecificationsList($orderField = 'jobspec_id', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('JobSpecifications')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save JobSpecifications
    * @param JobSpecifications $jobSpecifications
    * @returns boolean
    * @throws DaoException
    */
   public function saveJobSpecifications(JobSpecifications $jobSpecifications) {
      try {
         if($jobSpecifications->getJobspecId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($jobSpecifications);
            $jobSpecifications->setJobspecId($idGenService->getNextID());
         }
         $jobSpecifications->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete JobSpecifications
    * @param array() $jobSpecificationsList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteJobSpecifications($jobSpecificationsList = array()) {
      try {
         if(is_array($jobSpecificationsList)) {
            $q = Doctrine_Query::create()
                   ->delete('JobSpecifications')
                   ->whereIn('jobspec_id', $jobSpecificationsList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search JobSpecifications by fields
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchJobSpecifications($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('JobSpecifications')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch( Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Return JobSpecifications by Id
    * @param id $id
    * @returns JobSpecifications. need to refactor later
    * @throws DaoException
    */
   public function readJobSpecifications($id) {
      try {
         return Doctrine::getTable('JobSpecifications')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get job specification for given job
    *
    * @param id int JobSpec ID
    * @param asArray bool If true, returns job spec as an array, if false, returns job spec as an object
    * @return $jobSpecifications object (or null if job has no job specification)
    * @throws DaoException
    */
   public function getJobSpecForJob($jobId, $asArray = false) {
      try {
         $hydrateMode = ($asArray) ? Doctrine::HYDRATE_ARRAY : Doctrine::HYDRATE_RECORD;

         $q = Doctrine_Query::create()
               ->select('js.*')
               ->from('JobSpecifications js')
               ->leftJoin('js.JobTitle j')
               ->where('j.id = ?', $jobId);

         $jobSpecList = $q->execute(array(), $hydrateMode);
         $jobSpec = null;
         if (count($jobSpecList) == 1) {
            $jobSpec = $jobSpecList[0];
         }
         return $jobSpec;
      } catch( Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve JobTitle List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getJobTitleList($orderField = 'job.id', $orderBy = 'ASC'){
      try {
         $q = Doctrine_Query::create()
             ->select('job.*')
             ->from('JobTitle job')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save JobTitle
    * @param JobTitle $jobTitle
    * @param array() $emplymentStatus
    * @returns boolean
    * @throws DaoException
    */
   public function saveJobTitle(JobTitle $jobTitle, $emplymentStatus = array()) {
      try {
         if( $jobTitle->getId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($jobTitle);
            $jobTitle->setId( $idGenService->getNextID());
         }

         if( $jobTitle->getSalaryGradeId() == '-1')
            $jobTitle->setSalaryGradeId(new SalaryGrade());

         if( $jobTitle->getJobspecId() == '-1')
            $jobTitle->setJobspecId(new JobSpecifications());

         $jobTitle->save();

         $this->deleteJobTitleEmpStstus($jobTitle);
         foreach( $emplymentStatus as $empStatus) {
            $jobEmpStatus	=	new JobTitleEmployeeStatus();
            $jobEmpStatus->setJobtitCode($jobTitle->getId());
            $jobEmpStatus->setEstatCode($empStatus->getId());
            $jobEmpStatus->save();
         }
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete JobTitleEmpStstus. Need to change the name later - mispelt
    * @param JobTitle $jobTitle
    * @returns boolean
    * @throws DaoException
    */
   public function deleteJobTitleEmpStstus($jobTitle) {
      try {
         $q = Doctrine_Query::create()
            ->delete('JobTitleEmployeeStatus')
            ->where("jobtit_code='".$jobTitle->getId()."'");

         $numDeleted = $q->execute();
         if($numDeleted > 0) {
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete JobTitle
    * @param array() $jobTitleList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteJobTitle($jobTitleList = array()) {
      try {
         if(is_array($jobTitleList)) {
            $q = Doctrine_Query::create()
               ->delete('JobTitle')
               ->whereIn('id', $jobTitleList );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search JobTitle
    * @param String $searchMode
    * @param String $searchValue
    * @returns JobTitle/Collection
    * @throws DaoException
    */
   public function searchJobTitle($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('JobTitle')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve JobTitle by Id
    * @param int $id
    * @returns JobTitle . need to refactor to return JobTitle domain object
    * @throws DaoException
    */
   public function readJobTitle($id) {
      try {
         return Doctrine::getTable('JobTitle')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>
