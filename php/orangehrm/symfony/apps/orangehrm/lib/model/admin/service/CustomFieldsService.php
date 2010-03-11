<?php
/* 
 * 
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
 * 
 */

/**
 * Description of CompanyService
 *
 * @author orange
 */
class CustomFieldsService extends BaseService {
	
   const FIELD_TYPE_STRING			=	0 ;
   const FIELD_TYPE_DROP_DOWN		=	1 ;
   const NUMBER_OF_FIELDS			=	10 ;
   
   /**
     * Get CustomField List
     * @return Skill 
     */
    public function getCustomFieldList( $orderField='field_num',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('CustomFields')
			    ->orderBy($orderField.' '.$orderBy);
			
			$customFieldList = $q->execute();
			   
			return  $customFieldList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save CustomField
     * @param CustomFields $customFields
     * @return void
     */
    public function saveCustomField(CustomFields $customFields)
    {
    	try
        {
        	$customFields->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete CustomField
     * @param $customFieldList
     * @return void
     */
    public function deleteCustomField( $customFieldList )
    {
   	 	try
        {
	    	if(is_array($customFieldList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('CustomFields')
					    ->whereIn('field_num', $customFieldList  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	/**
     * Read Customer fields
     * @param $customFieldList
     * @return void
     */
    public function readCustomField( $id )
    {
   	 	try
        {
	    	$customFields = Doctrine::getTable('CustomFields')->find($id);
	    	return $customFields;
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Get avaliable field numbers
     * @return void
     */
    public function getAvaliableFieldNumbers()
    {
    	try
        {
        	$avaliableFields	=	array();
	    	$q = Doctrine_Query::create()
			    ->from('CustomFields');
			   
			
			$customFieldList = $q->execute();

			for( $i=1 ; $i<= self::NUMBER_OF_FIELDS ; $i++)
			{
				$avaliabe	=	true; 
				foreach( $customFieldList as $customField)
				{
					if($customField->getFieldNum() == $i )
					{
						$avaliabe	=	false; 
					}
				}
				if( $avaliabe )
					array_push($avaliableFields,$i);
					
			}
			
			return $avaliableFields ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
  
}