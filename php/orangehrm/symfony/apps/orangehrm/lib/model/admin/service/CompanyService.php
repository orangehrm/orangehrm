<?php
/**
 * CompanyService
 *
 * @author Sujith T
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
   * Get Supervisor Employee List
   * This function should be available in EmployeeService, not here
   * Due to backward compatibility temporarily its made available. referencing places need to be pointed to employee service
   * and this should be removed
   */
   public function getSupervisorEmployeeList($supervisorId)
   {
      try {
         $employeeService = new EmployeeService();
         return $employeeService->getSupervisorEmployeeList($supervisorId);
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

   public function getCompanyStructureWithTitle($title) {
       try {
          return $this->companyDao->getCompanyStructureWithTitle($title);
       } catch(Exception $e) {
          throw new AdminServiceException($e->getMessage());
       }
   }

   /**There is no need of having seperate functions to insert/update can perform with the same function
    * Save CompanyStructure Object
    * @param CompanyStructure companyStructure
    */
   public function saveCompanyStructure(CompanyStructure $companyStructure) {
      try {
         return $this->companyDao->saveCompanyStructure($companyStructure);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Returns CompanyStructure Object by Id
    * @param int $id
    * @returns CompanyStructure
    * @throws AdminServiceException
    */
   public function readCompanyStructure($id) {
      try {
         return $this->companyDao->readCompanyStructure($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyStructure List by Hierarchy Order
    * @param int $parent_id
    * @returns Collection
    * @throws AdminServiceException
    */
   function getCompanyStructureList($parentId = null) {
      try{
         return $this->companyDao->getCompanyStructureList($parentId);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete CompanyStructure Object
    * @param int $id
    * @throws AdminServiceException
    */
   public function deleteCompanyStructure($id) {
      try {
         return $this->companyDao->deleteCompanyStructure($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Build Recusive Array CompanyStructure with the Hierarchy
    * The first level of nodes will be listed with its Ids
    * All child level nodes will have keys delimited with | character
    */
   public function getCompanyStructureHierarchy()
   {

      $recusiveList = array();
      $list = $this->getCompanyStructureList(1);
      $parentLookup  = array();
      $grouped       = array();
      $maxDepth      = 0;

      foreach($list as $k => $node) {
         $recusiveList[$node->getId()]['x'] = $node;
      }

      $list = $this->getCompanyStructureList();
      $lastKey = null;

      foreach($list as $k => $node) {
         if(isset($recusiveList[$node->getParnt()])) {
            $rows = $recusiveList[$node->getParnt()];
            foreach($rows as $k => $v) {
               $lastKey = $k;
            }
            if($lastKey != "") {
               $lastKey .= "|" . $node->getId();
            } else {
               $lastKey = $node->getId();
            }
            $recusiveList[$node->getParnt()][$lastKey] = $node;
            $grouped[$node->getParnt()] = $lastKey;
            if($maxDepth < count(explode("|", $lastKey))) {
               $maxDepth = count(explode("|", $lastKey));
            }
         } else {
            $parentLookup[$node->getParnt()] = $node;
            $maxDepth = 1;
         }
      }

      //records not sorted by its parent
      foreach($parentLookup as $parentId => $node) {
         foreach($grouped as $k => $v) {
            $parents = explode("|", $v);
            if($parents[count($parents) - 1] == $parentId) {
               $v .= "|" . $node->getId();
               $recusiveList[$k][$v] = $node;
               $grouped[$k] = $v;
               if($maxDepth < count(explode("|", $v))) {
                  $maxDepth = count(explode("|", $v));
               }
            }
         }
      }
      $recusiveList['maxDepth'] = $maxDepth;
      return $recusiveList;
   }

   public function getSubDivisionList() {

      try {
         return $this->companyDao->getSubDivisionList();
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }

   }

}
