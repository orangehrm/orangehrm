<?php
/**
 * JobService Class
 * @author Sujith T
 *
 */
class JobService extends BaseService {
   private $jobDao;

   /**
    * Constructor
    */
   public function __construct() {
      $this->jobDao = new JobDao();
   }

   /**
    * Set JobDao
    * @param JobDao $jobDao
    */
   public function setJobDao(JobDao $jobDao) {
      $this->jobDao = $jobDao;
   }

   /**
    * Return JobDao
    * @returns JobDao
    */
   public function getJobDao() {
      return $this->jobDao;
   }

   /**
    * Saving Job Category
    * @param JobCategory $jobCategory
    * @returns boolean
    * @throws AdminServiceException, DataDuplicationException
    */
   public function saveJobCategory(JobCategory $jobCategory) {
      try {
         return $this->jobDao->saveJobCategory($jobCategory);
      } catch(DataDuplicationException $e) {
         throw new DuplicateNameException($e->getMessage());
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve Job Category List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getJobCategoryList($orderField = 'eec_code', $orderBy = 'ASC') {
      try {
         return $this->jobDao->getJobCategoryList($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete Job Category
    * @param array() $jobCategoryList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteJobCategory($jobCategoryList = array()) {
      try {
         return $this->jobDao->deleteJobCategory($jobCategoryList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Searching JobCategory
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws AdminServiceException
    */
   public function searchJobCategory($searchMode, $searchValue) {
      try {
         return $this->jobDao->searchJobCategory($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Returns JobCategory by Id. This need to be refactored to retrieve JC object, need to change all references
    * @param String $id
    * @returns JobCategory
    * @throws AdminServiceException
    */
   public function readJobCategory($id) {
      try {
         return $this->jobDao->readJobCategory($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Saves SalaryGrade
    * @param String $id
    * @returns SalaryGrade
    * @throws AdminServiceException
    */
   public function saveSalaryGrade(SalaryGrade $salaryGrade) {
      try {
         return $this->jobDao->saveSalaryGrade($salaryGrade);
      } catch(DataDuplicationException $e) {
         throw $e;
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve SalaryGrade List. need to make the correction in the function name
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getSaleryGradeList($orderField = 'sal_grd_code', $orderBy = 'ASC') {
      try {
         return $this->jobDao->getSalaryGradeList($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete SalaryGrade
    * @param array() $saleryGradeList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteSalaryGrade($saleryGradeList = array()){
      try {
         return $this->jobDao->deleteSalaryGrade($saleryGradeList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Searching SalaryGrade
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws AdminServiceException
    */
   public function searchSalaryGrade($searchMode, $searchValue) {
      try {
         return $this->jobDao->searchSalaryGrade($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Return SalaryGrade from Id
    * @param String $id
    * @returns SaleryGrade.
    * @throws AdminServiceException
    */
   public function readSalaryGrade($id) {
      try {
         return $this->jobDao->readSalaryGrade($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Saving SalaryCurrencyDetail
    * @param SalaryCurrencyDetail $salaryCurrencyDetail
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveSalleryGradeCurrency(SalaryCurrencyDetail $salaryCurrencyDetail) {
      try {
         return $this->jobDao->saveSalleryGradeCurrency($salaryCurrencyDetail);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Checks for existing SalaryCurrencyDetail for a given currency
    * @param SalaryCurrencyDetail $salaryCurrencyDetail
    * @returns boolean
    * @throws AdminServiceException
    */
   public function isExistingSalleryGradeCurrency(SalaryCurrencyDetail $salaryCurrencyDetail) {
      try {
         return $this->jobDao->isExistingSalleryGradeCurrency($salaryCurrencyDetail);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve SalleryGradeCurrency by code, correction to the function name need to be done
    * @param String $saleryGradeCode
    * @returns SalleryGradeCurrency/Collection
    * @throws AdminServiceException
    */
   public function getSalleryGradeCurrency($saleryGradeCode) {
      try {
         return $this->jobDao->getSalaryGradeCurrency($saleryGradeCode);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete SalleryGradeCurrency, name correction need to be done
    * @param String $saleryGradeId
    * @param array() $saleryGradeCurrencyList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteSalleryGradeCurrency($saleryGradeId, $saleryGradeCurrencyList) {
      try {
         return $this->jobDao->deleteSalaryGradeCurrency($saleryGradeId, $saleryGradeCurrencyList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve SalaryCurrencyDetail
    * @param String $saleryGradeId
    * @param String $currency
    * @returns SalaryCurrencyDetail
    * @throws PIMServiceException
    */
   public function getSalaryCurrencyDetail($salaryGrade, $currency) {
      try {
         return $this->jobDao->getSalaryCurrencyDetail($salaryGrade, $currency);
      } catch(Exception $e) {
         throw new PIMServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve EmployeeStatus List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getEmployeeStatusList($orderField = 'id', $orderBy = 'ASC') {
      try {
         return $this->jobDao->getEmployeeStatusList($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve EmployeeStatus for a Job Title
    * @param String $jobTitleCode
    * @param array() $asArray
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getEmployeeStatusForJob($jobTitleCode, $asArray = false) {
      try {
         return $this->jobDao->getEmployeeStatusForJob($jobTitleCode, $asArray);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save EmployeeStatus
    * @param EmployeeStatus $employeeStatus
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveEmployeeStatus(EmployeeStatus $employeeStatus) {
      try {
         return $this->jobDao->saveEmployeeStatus($employeeStatus);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete EmployeeStatus
    * @param array() $employeeStatusList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteEmployeeStatus($employeeStatusList = array()) {
      try {
         return $this->jobDao->deleteEmployeeStatus($employeeStatusList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Search EmployeeStatus
    * @param String $searchMode
    * @param String $searchValue
    * @returns boolean
    * @throws AdminServiceException
    */
  	public function searchEmployeeStatus($searchMode, $searchValue) {
      try {
         return $this->jobDao->searchEmployeeStatus($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Search EmployeeStatus by Id
    * @param String $id
    * @returns EmployeeStatus
    * @throws AdminServiceException
    */
   public function readEmployeeStatus($id) {
      try {
         return $this->jobDao->readEmployeeStatus($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve JobSpecifications List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getJobSpecificationsList($orderField = 'jobspec_id', $orderBy = 'ASC') {
      try {
         return $this->jobDao->getJobSpecificationsList($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save JobSpecifications
    * @param JobSpecifications $jobSpecifications
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveJobSpecifications(JobSpecifications $jobSpecifications) {
      try {
         return $this->jobDao->saveJobSpecifications($jobSpecifications);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete JobSpecifications
    * @param array() $jobSpecificationsList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteJobSpecifications($jobSpecificationsList = array()) {
      try {
         return $this->jobDao->deleteJobSpecifications($jobSpecificationsList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Search JobSpecifications by fields
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws AdminServiceException
    */
   public function searchJobSpecifications($searchMode, $searchValue) {
      try {
         return $this->jobDao->searchJobSpecifications($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Return JobSpecifications by Id
    * @param id $id
    * @returns JobSpecifications. need to refactor later
    * @throws AdminServiceException
    */
   public function readJobSpecifications($id) {
      try {
         return $this->jobDao->readJobSpecifications($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Get job specification for given job
    *
    * @param id int JobSpec ID
    * @param asArray bool If true, returns job spec as an array, if false, returns job spec as an object
    * @return $jobSpecifications object (or null if job has no job specification)
    * @throws AdminServiceException
    */
   public function getJobSpecForJob($jobId, $asArray = false) {
      try {
         return $this->jobDao->getJobSpecForJob($jobId, $asArray);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve JobTitle List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getJobTitleList($orderField = 'job.id', $orderBy = 'ASC'){
      try {
         return $this->jobDao->getJobTitleList($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save JobTitle
    * @param JobTitle $jobTitle
    * @param array() $emplymentStatus
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveJobTitle(JobTitle $jobTitle, $emplymentStatus = array()) {
      try {
         return $this->jobDao->saveJobTitle($jobTitle, $emplymentStatus);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete JobTitleEmpStstus. Need to change the name later - mispelt
    * @param JobTitle $jobTitle
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteJobTitleEmpStstus($jobTitle) {
      try {
         return $this->jobDao->deleteJobTitleEmpStstus($jobTitle);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete JobTitle
    * @param array() $jobTitleList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteJobTitle($jobTitleList = array()) {
      try {
         return $this->jobDao->deleteJobTitle($jobTitleList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Search JobTitle
    * @param String $searchMode
    * @param String $searchValue
    * @returns JobTitle/Collection
    * @throws AdminServiceException
    */
   public function searchJobTitle($searchMode, $searchValue) {
      try {
         return $this->jobDao->searchJobTitle($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve JobTitle by Id
    * @param int $id
    * @returns JobTitle
    * @throws AdminServiceException
    */
   public function readJobTitle($id) {
      try {
         return $this->jobDao->readJobTitle($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }
}
?>