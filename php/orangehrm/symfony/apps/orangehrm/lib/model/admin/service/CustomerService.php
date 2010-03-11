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
class CustomerService extends BaseService {
	
   /**
     * Get NalityList List
     * @return NalityList 
     */
    public function getCustomerList( $orderField='customer_id',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Customer')
			    ->orderBy($orderField.' '.$orderBy);
			
			$customerList = $q->execute();
			   
			return  $customerList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save Nationality
     * @param Nationality $nationality 
     * @return void
     */
    public function saveCustomer(Customer $customer)
    {
    	try
        {
			if( $customer->getCustomerId() == '')
			{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($customer);
				$customer->setCustomerId( $idGenService->getNextID() );
			}
        	$customer->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Nationality
     * @param $nationalityList
     * @return unknown_type
     */
    public function deleteCustomer( $customerList )
    {
   	 	try
        {
        	if( is_array($customerList))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Customer')
					    ->whereIn('customer_id', $customerList );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search Nationality
     * @return unknown_type
     */
  	public function searchCustomer( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Customer') 
				    			->where("$searchMode = ?",$searchValue);
				    
			$customerList = $q->execute();
			
			return $customerList;
			   
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
    public function readCustomer( $id )
    {
   	 	try
        {
	    	$customer = Doctrine::getTable('Customer')->find($id);
	    	return $customer;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
}