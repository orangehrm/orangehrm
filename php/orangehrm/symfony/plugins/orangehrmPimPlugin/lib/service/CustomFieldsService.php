<?php
/**
 * CustomFieldsService class
 *
 * @author Sujith T
 */
class CustomFieldsService extends BaseService {
	//not sure of the business purpose of the constants, need to check their references
   const FIELD_TYPE_STRING			=	0 ;
   const FIELD_TYPE_DROP_DOWN		=	1 ;
   const NUMBER_OF_FIELDS			=	10 ;

   private $customFieldsDao;
   
   /**
    * Constructor
    */
   public function __construct() {
      $this->customFieldsDao = new CustomFieldsDao();
   }

   /**
    * Sets CustomFieldsDao
    * @param CustomFieldsDao $customFieldsDao
    */
   public function setCustomFieldsDao(CustomFieldsDao $customFieldsDao) {
      $this->customFieldsDao = $customFieldsDao;
   }

   /**
    * Returns CustomFieldsDao
    * @return CustomFieldsDao
    */
   public function getCustomFieldsDao() {
      return $this->customFieldsDao;
   }

   /**
    * Retrieve Custom Fields
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    */
   public function getCustomFieldList($screen = null, $orderField = "field_num", $orderBy = "ASC") {
      try {
         return $this->customFieldsDao->getCustomFieldList($screen, $orderField, $orderBy);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
    } 
    
   /**
    * Save CustomFields
    * @param CustomFields $customFields
    * @returns boolean
    * @throws AdminServiceException, DuplicateNameException
    */
   public function saveCustomField(CustomFields $customFields) {
      try {
         return $this->customFieldsDao->saveCustomField($customFields);
      } catch(DataDuplicationException $e) {
         //this is for backward compatibility, need to identify the references and update them
         throw new DuplicateNameException($e->getMessage());
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }
    
   /**
    * Delete CustomField
    * @param array() $customFieldList
    * @returns boolean
    * @throws AdminServiceException
    */
   public function deleteCustomField($customFieldList) {
      try {
         return $this->customFieldsDao->deleteCustomField($customFieldList);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }
    
   /**
    * Returns CustomField by Id. This need to be update to retrieve entity object
    * @param int $id
    * @returns CustomFields
    * @throws AdminServiceException
    */
   public function readCustomField($id) {
      try {
         return $this->customFieldsDao->readCustomField($id);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }
    
   /**
    * Retrievs available field numbers
    * @returns array()
    * @throws AdminServiceException
    */
   public function getAvailableFieldNumbers() {
      try {
        	$availableFields	=	array();

			$customFieldList = $this->getCustomFieldList();
			for( $i=1 ; $i<= self::NUMBER_OF_FIELDS ; $i++) {
				$avaliabe	=	true; 
				foreach( $customFieldList as $customField) {
					if($customField->getFieldNum() == $i ) {
						$avaliabe	=	false; 
					}
				}
				if( $avaliabe )
					array_push($availableFields,$i);			
			}
			
			return $availableFields;
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }
}
?>