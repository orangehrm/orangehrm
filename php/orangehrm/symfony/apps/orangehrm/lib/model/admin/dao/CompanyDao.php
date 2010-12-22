<?php
/**
 * Company DAO class to make CRUD operations
 * @author Sujith T
 *
 */
class CompanyDao extends BaseDao {

   /**
    * Retrieve Company General Information
    * @throws DaoException
    */
   public function getCompany() {
      try{
         $q = Doctrine_Query::create()
			    ->from('CompanyGeninfo')
			    ->where("code = ?", "001");

         $companyGeninfo = $q->fetchOne();
         $info    = explode("|", $companyGeninfo->getGeninfoValues());
         $company = new Company();
         $company->setComCode($companyGeninfo->getCode());
         $company->comapanyName  =   $info[0];
         $company->country       =   $info[1];
         $company->street1       =   $info[2];
         $company->street2       =   $info[3];
         $company->state         =   $info[4];
         $company->city          =   $info[5];
         $company->zipCode       =   $info[6];
         $company->phone         =   $info[7];
         $company->fax           =   $info[8];
         $company->taxId         =   $info[9];
         $company->naics         =   $info[10];
         $company->comments      =   $info[11];
         
         return $company;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Company
    * @param Company $company
    * @returns boolean
    * @throws DaoException
    */
   public function saveCompany(Company $company) {
      try{
         $infoStr = $company->comapanyName.'|'.$company->country.'|'.$company->street1.'|'.$company->street2.'|'
                 .$company->state.'|'. $company->city.'|'.$company->zipCode.'|'.$company->phone.'|'
                 .$company->fax.'|'.$company->taxId.'|'.$company->naics.'|'.$company->comments;

         $q = Doctrine_Query::create()
			    ->from('CompanyGeninfo')
			    ->where("code = ?", "001");

         $companyGeninfo = $q->fetchOne();
         $companyGeninfo->setGeninfoValues($infoStr);
         $companyGeninfo->save();

         $rootCompanyStructure	=	$this->readCompanyStructure(1);
         $rootCompanyStructure->setTitle( $company->getCompanyName());
         $rootCompanyStructure->save();
         
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Returns CompanyStructure Object by Id
    * @param int $id
    * @returns CompanyStructure
    * @throws DaoException
    */
   public function readCompanyStructure($id)
   {
      try {
         $q = Doctrine_Query::create()
            ->from('CompanyStructure cs')
            ->where("id = ?", $id);
         if($q->count() == 0) {
            return false;
         }
         return $q->fetchOne();
      } catch(Exception $e) {
          throw new DaoException($e->getMessage());
      }
   }

   /**
    * Return CompanyLocations
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getCompanyLocation($orderField = "loc_code", $orderBy = "ASC") {
      try {
         $q = Doctrine_Query::create()
             ->from("Location")
             ->orderBy($orderField . " " . $orderBy);
 
         return $q->execute();

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save CompanyLocation
    * @param Location location
    * @returns boolean
    * @throws DaoException
    */
   public function saveCompanyLocation(Location $location) {
      try {
         if($location->getLocCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($location);
            $location->setLocCode($idGenService->getNextID());
         }

         $location->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete CompanyLocation
    * @param array locationCodes
    * @returns boolean
    * @throws DaoException
    */
   public function deleteCompanyLocation($locationCodes = array()) {
      try {
         if(is_array($locationCodes)) {
            $q = Doctrine_Query::create()
                   ->delete('Location')
                   ->whereIn('loc_code', $locationCodes);

            $q->execute();
            return true ;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * search CompanyLocation
    * @param String $param
    * @param String $value
    * @returns Collection
    * @throws DaoException
    */
   public function searchCompanyLocation($param, $value) {
      try {
         $q = Doctrine_Query::create()
                ->from('Location')
                ->where("$param = ?",trim($value));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Read by Id and returns Location / Need to be as commented after correcting the references
    * @param String $locCode
    * @returns Location
    * @throws DaoException
    */
   public function readLocation($locCode) {
      try {
         /*$q = Doctrine_Query::create()
                ->from('Location')
                ->where("loc_code = ?",trim($locCode));

         return $q->fetchOne();*/
         return Doctrine::getTable('Location')->find($locCode);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyProperty
    * @param String $orderField
    * @param String $orderBy
    * @returns CompanyProperty
    * @throws DaoException
    */
   public function getCompanyProperty($orderField = "prop_id", $orderBy = "ASC") {
      try {
         $q = Doctrine_Query::create()
               ->from('CompanyProperty')
               ->orderBy($orderField . ' ' . $orderBy);

         return $q->execute();
      } catch (Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save CompanyProperty
    * @param CompanyProperty $companyProperty
    * @returns boolean
    * @throws DaoException
    */
   public function saveCompanyProperty(CompanyProperty $companyProperty) {
      try {
         $companyProperty->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyProperty for Supervisors
    * @param Collection $subordinates
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getCompanyPropertyForSupervisor($subordinates, $orderField = "prop_id", $orderBy = "ASC") {
      try {
         $q = Doctrine_Query::create()
               ->from('CompanyProperty p')
                   ->where('(p.emp_id IS NULL) OR (p.emp_id = 0)');

         if (!empty($subordinates)) {
            $employeeIds = array();
            foreach($subordinates as $employee) {
               $employeeIds[] = $employee->empNumber;
            }

            $q->orWhereIn('emp_id', $employeeIds);
         }

         $q->orderBy($orderField.' '.$orderBy);
         return $q->execute();
      } catch( Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete CompanyProperty
    * @param array() $propertyList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteCompanyProperty($propertyList = array()) {
      try {
         if(is_array($propertyList)) {
            $q = 	Doctrine_Query::create()
               ->delete('CompanyProperty')
               ->whereIn('prop_id', $propertyList);

            $q->execute();
            return true;
         }
      } catch( Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Reads CompanyProperty - Currently this function returns array and action class relying on the function,
    * later we need to convert this array into object and all references need to be modified
    * @param int $id
    * @returns array()
    * @throws DaoException
    */
   public function readCompanyProperty($id) {
      try{
         $companyProperty = Doctrine::getTable('CompanyProperty')->find($id);
         return $companyProperty;
      } catch( Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   public function getCompanyStructureWithTitle($title) {
      try {
         $like = $title . '%';

         $q = Doctrine_Query::create()
            ->from('CompanyStructure cs')
            ->where("title LIKE ?", $like);
         if($q->count() == 0) {
            return false;
         }
         return $q->fetchOne();
      } catch(Exception $e) {
          throw new DaoException($e->getMessage());
      }
   }

   /**There is no need of having seperate functions to insert/update can perform with the same function
    * Save CompanyStructure Object
    * @param CompanyStructure companyStructure
    * @return boolean
    * @throws DaoException
    */
   public function saveCompanyStructure(CompanyStructure $companyStructure) {
      try {
         //this part determines the insert if not update will be performed
         if($companyStructure->getId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($companyStructure);
            $companyStructure->setId($idGenService->getNextID());
         }
         $companyStructure->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve CompanyStructure List by Hierarchy Order
    * @param int $parentId
    * @returns Collection
    * @throws DaoException
    */
   public function getCompanyStructureList($parentId = null) {
      try{
         $q = Doctrine_Query::create()
			    ->from('CompanyStructure cs');

         if(!is_null($parentId)) {
            $q->where("parnt = ?", $parentId);
         }
         return $q->execute();

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete CompanyStructure Object
    * @param int $id
    * @return boolean
    * @throws DaoException
    */
   public function deleteCompanyStructure($id) {
      try {
         $q = Doctrine_Query::create()
            ->delete('CompanyStructure cs')
            ->where("id = ?", $id);
         $q->execute();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /** I am not sure whether we need this function, instead we can use getCompanyStructureList and omit the first record
    * please follow the conventions, comment blocks, direct simplying methods
    * Retrieve SubDivisionList.
    * @return Collection
    * @throws DaoException
    */
   public function getSubDivisionList() {
      try {
         $q = Doctrine_Query::create()
             ->from('CompanyStructure')
             ->where('id > 1');

         return $q->execute();
      } catch( Exception $e)         {
         throw new DaoException($e->getMessage());
      }
   }
}
?>