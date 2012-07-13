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
 * CustomFields Service
 * @package pim
 * @todo Rename to CustomFieldsConfigurationService [DONE]
 * @todo Remove exceptions that only wraps DAO exceptions [DONE]
 */
class CustomFieldConfigurationService extends BaseService {
	//not sure of the business purpose of the constants, need to check their references
   const FIELD_TYPE_STRING			=	0 ;
   const FIELD_TYPE_DROP_DOWN		=	1 ;
   const NUMBER_OF_FIELDS			=	10 ;

   /**
    * @ignore
    * @var CustomFieldConfigurationDao 
    */
   private $customFieldsDao;
   
   /**
    * Constructor
    */
   public function __construct() {
      $this->customFieldsDao = new CustomFieldConfigurationDao();
   }

   /**
    * @ignore
    * 
    * Sets CustomFieldsDao
    * @param CustomFieldConfigurationDao $customFieldsDao
    */
   public function setCustomFieldsDao(CustomFieldConfigurationDao $customFieldsDao) {
      $this->customFieldsDao = $customFieldsDao;
   }

   /**
    * @ignore
    * 
    * Returns CustomFieldsDao
    * @return CustomFieldConfigurationDao
    */
   public function getCustomFieldsDao() {
      return $this->customFieldsDao;
   }

   /**
    * Retrieve Custom Fields
    * @param String $screen
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws AdminServiceException
    * 
    * @todo rename method as searchCustomFieldList( $sortField , $sortOrder, $filters ) [DONE: Won't change. Can be implemented on request]
    */
   public function getCustomFieldList($screen = null, $orderField = "field_num", $orderBy = "ASC") {
       return $this->customFieldsDao->getCustomFieldList($screen, $orderField, $orderBy);
   } 
    
   /**
    * Save CustomFields
    * @param CustomFields $customFields
    * @returns boolean
    * @throws AdminServiceException, DuplicateNameException
    * 
    * @todo return saved entity
    * @todo rename entity as CustomField
    */
   public function saveCustomField(CustomFields $customFields) {

      $reportGeneratorService = new ReportGeneratorService();
      $customFields = $this->customFieldsDao->saveCustomField($customFields);
      $reportGeneratorService->saveCustomDisplayField($customFields, "3");
      
      return $customFields;

   }
    
   /**
    * Delete CustomField
    * @param array $customFieldList
    * @returns integer Number of records deleted
    * @throws DaoException
    * 
    * @todo rename method as deleteCustomFields
    * @todo return number of items deleted [DONE]
    */
   public function deleteCustomFields($customFieldList) {

      $reportGeneratorService = new ReportGeneratorService();
      $reportGeneratorService->deleteCustomDisplayFieldList($customFieldList);
      
      return $this->customFieldsDao->deleteCustomFields($customFieldList);

   }
    
   /**
    * Returns CustomField by Id. This need to be update to retrieve entity object
    * @param int $id
    * @returns CustomFields
    * @throws AdminServiceException
    * 
    * @todo rename method as getCustomeField
    */
   public function readCustomField($id) {
       return $this->customFieldsDao->readCustomField($id);
   }
    
   /**
    * @ignore
    * 
    * Retrievs available field numbers
    * @returns array()
    * @throws AdminServiceException
    * 
    * @todo remove method since it not used any where 
    */
    public function getAvailableFieldNumbers() {

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

    }
   
   
   
}
