<?php
/**
 * CompanyService
 *
 */
class CompanyService extends BaseService {

   private $companyDao;

   /**
    * Constructor
    */
   function __construct() {
      $this->companyDao = new CompanyDao();
   }
   
   /**
    * Sets CompanyDao
    * @param CompanyDao $companyDao
    */
   public function setCompanyDao(CompanyDao $companyDao) {
      $this->companyDao = $companyDao;
   }

   /**
    * Returns CompanyDao
    * @returns CompanyDao
    */
   public function getCompanyDao() {
      return $this->companyDao;
   }

   /**
    * Retrieve Company Info
    * @returns Company
    * @throws AdminServiceException
    */
   public function getCompany() {
      try{
         return $this->companyDao->getCompany();
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

  /**
   * Get Employee List
   * This function should be available in EmployeeService, not here
   * Due to backward compatibility temporarily its made available. referencing places need to be pointed to employee service
   * and this should be removed
   */
   public function getEmployeeList() {
      try {
         $employeeService = new EmployeeService();
         return $employeeService->getEmployeeList();
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

  /**
   * Get Employee List As Json
   * This function should be available in EmployeeService, not here
   * Due to backward compatibility temporarily its made available. referencing places need to be pointed to employee service
   * and this should be removed
   */
   public function getEmployeeListAsJson()
   {
      try{
         $employeeService = new EmployeeService();
         return $employeeService->getEmployeeListAsJson();
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save Company
    * @param Company $company
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveCompany(Company $company)
   {
      try {
         return $this->companyDao->saveCompany($company);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Return CompanyLocations
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getCompanyLocation($orderField = "loc_code", $orderBy = "ASC") {
      try {
         return $this->companyDao->getCompanyLocation($orderField, $orderBy);
      } catch(Exception $e){
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save CompanyLocation
    * @param Location location
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveCompanyLocation(Location $location) {
      try {
         return $this->companyDao->saveCompanyLocation($location);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete CompanyLocation
    * @param array locationCodes
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteCompanyLocation($locationCodes = array()) {
      try {
         return $this->companyDao->deleteCompanyLocation($locationCodes);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * search CompanyLocation
    * @param String $param
    * @param String $value
    * @returns Collection
    * @throws AdminServiceException
    */
   public function searchCompanyLocation($searchMode, $searchValue) {
      try {
         return $this->companyDao->searchCompanyLocation($searchMode, $searchValue);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Reads Location by Id(need to modify the DAO)
    * @param Location location
    * @returns array()
    * @throws AdminServiceException
    */
   public function readLocation($id) {
      try {
         return $this->companyDao->readLocation($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyProperty
    * @param String $orderField
    * @param String $orderBy
    * @returns CompanyProperty
    * @throws AdminServiceException
    */
   public function getCompanyProperty($orderField = "prop_id", $orderBy = "ASC") {
      try {
         return $this->companyDao->getCompanyProperty($orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyProperty for Supervisors
    * @param Collection $subordinates
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getCompanyPropertyForSupervisor($subordinates, $orderField = "prop_id", $orderBy = "ASC") {
      try {
         return $this->companyDao->getCompanyPropertyForSupervisor($subordinates, $orderField, $orderBy);
      } catch( Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Save CompanyProperty
    * @param CompanyProperty $companyProperty
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveCompanyProperty(CompanyProperty $companyProperty) {
      try {
         return $this->companyDao->saveCompanyProperty($companyProperty);
      } catch( Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete CompanyProperty
    * @param array() $propertyList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteCompanyProperty($propertyList = array()) {
      try {
         return $this->companyDao->deleteCompanyProperty($propertyList);
      } catch( Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Reads CompanyProperty - Currently this function returns array and action class relying on the function,
    * later we need to convert this array into object and all references need to be modified
    * @param int $id
    * @returns array()
    * @throws AdminServiceException
    */
   public function readCompanyProperty($id) {
      try {
         return $this->companyDao->readCompanyProperty($id);
      } catch( Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

}
