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
class NationalityService extends BaseService {
	
   /**
     * Get NalityList List
     * @return NalityList 
     */
    public function getNationalityList( $orderField='nat_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Nationality')
			    ->orderBy($orderField.' '.$orderBy);
			
			$nationalityList = $q->execute();
			   
			return  $nationalityList ;
			
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
    public function saveNationality(Nationality $nationality)
    {
    	try
        {
        	if( $nationality->getNatCode() == '' )
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($nationality);
				$nationality->setNatCode( $idGenService->getNextID() );
        	}
        	$nationality->save();
			
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
    public function deleteNationality( $nationalityList )
    {
   	 	try
        {
	    	if(is_array($nationalityList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Nationality')
					    ->whereIn('nat_code', $nationalityList  );
	
					   
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
  	public function searchNationality( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Nationality') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$nationalityList = $q->execute();
			
			return $nationalityList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Read Nationality
     * @param $customFieldList
     * @return void
     */
    public function readNationality( $id )
    {
   	 	try
        {
	    	$nationality = Doctrine::getTable('Nationality')->find($id);
	    	return $nationality;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
   /**
     * Get EthnicRace List
     * @return EthnicRace 
     */
    public function getEthnicRaceList( $orderField='ethnic_race_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('EthnicRace')
			    ->orderBy($orderField.' '.$orderBy);
			
			$ethnicRaceList = $q->execute();
			   
			return  $ethnicRaceList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save EthnicRace
     * @param EthnicRace $ethnicRace 
     * @return void
     */
    public function saveEthnicRace(EthnicRace $ethnicRace)
    {
    	try
        {
        	if( $ethnicRace->getEthnicRaceCode()== '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($ethnicRace);
				$ethnicRace->setEthnicRaceCode( $idGenService->getNextID() );
        	}
        	$ethnicRace->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete EthnicRace
     * @param $ethnicRaceList
     * @return unknown_type
     */
    public function deleteEthnicRace( $ethnicRaceList )
    {
   	 	try
        {
	    	if(is_array($ethnicRaceList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('EthnicRace')
					    ->whereIn('ethnic_race_code', $ethnicRaceList );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search EthnicRace
     * @return unknown_type
     */
  	public function searchEthnicRace( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('EthnicRace') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$ethnicRaceList = $q->execute();
			
			return $ethnicRaceList;
			   
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
    public function readEthnicRace( $id )
    {
   	 	try
        {
	    	$ethnicRace = Doctrine::getTable('EthnicRace')->find($id);
	    	return $ethnicRace;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
}