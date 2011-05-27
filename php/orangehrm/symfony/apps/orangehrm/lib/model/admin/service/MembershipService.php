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
class MembershipService extends BaseService {
	
   /**
     * Get MembershipType List
     * @return MembershipType 
     */
    public function getMembershipTypeList( $orderField='membtype_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('MembershipType')
			    ->orderBy($orderField.' '.$orderBy);
			
			$membershipTypeList = $q->execute();

			return  $membershipTypeList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save MembershipType
     * @param MembershipType 
     * @return void
     */
    public function saveMembershipType(MembershipType $membershipType)
    {
    	try
        {
        	if( $membershipType->getMembtypeCode() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($membershipType);
				$membershipType->setMembtypeCode( $idGenService->getNextID() );
        	}
        	
        	$membershipType->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Membership
     * @param $skillList
     * @return unknown_type
     */
    public function deleteMembershipType( $membershipType )
    {
   	 	try
        {
	    	if(is_array($membershipType ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('MembershipType')
					    ->whereIn('membtype_code', $membershipType  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search MembershipList
     * @return unknown_type
     */
  	public function searchMembershipType( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('MembershipType') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$membershipTypeList = $q->execute();
			
			return $membershipTypeList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read EthnicRace
     * @param $customFieldList
     * @return void
     */
    public function readMembershipType( $id )
    {
   	 	try
        {
	    	$membershipType = Doctrine::getTable('MembershipType')->find($id);
	    	return $membershipType;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
    /**
     * Get MembershipType List
     * @return MembershipType 
     */
    public function getMembershipList( $orderField='membship_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Membership')
			    ->orderBy($orderField.' '.$orderBy);
			
			$membershipList = $q->execute();
			   
			return  $membershipList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save MembershipType
     * @param MembershipType 
     * @return void
     */
    public function saveMembership(Membership $membership)
    {
    	try
        {
        	if( $membership->getMembshipCode() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($membership);
				$membership->setMembshipCode( $idGenService->getNextID() );
        	}
        	
        	$membership->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Membership
     * @param $skillList
     * @return unknown_type
     */
    public function deleteMembership( $membershipList )
    {
   	 	try
        {
	    	if(is_array($membershipList))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Membership')
					    ->whereIn('membship_code',  $membershipList  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search MembershipList
     * @return unknown_type
     */
  	public function searchMembership( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Membership') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$membershipList = $q->execute();
			
			return $membershipList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	  /**
     * Read EthnicRace
     * @param $customFieldList
     * @return void
     */
    public function readMembership( $id )
    {
   	 	try
        {
	    	$membership = Doctrine::getTable('Membership')->find($id);
	    	return $membership;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
}