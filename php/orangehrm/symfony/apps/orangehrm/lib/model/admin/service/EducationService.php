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
class EducationService extends BaseService {
	
  /**
     * Get Education List
     * @return unknown_type
     */
    public function getEducationList( $orderField='eduCode',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Education edu')
			    ->orderBy($orderField.' '.$orderBy);
			
			$educationList = $q->execute();
			   
			return  $educationList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
 /**
     * Save Education
     * @param Education $education
     * @return unknown_type
     */
    public function saveEducation(Education $education)
    {
    	try
        {
        	if( $education->getEduCode() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($education);
				$education->setEduCode( $idGenService->getNextID() );
        	}
        	$education->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Education
     * @param $saleryGradeList
     * @return unknown_type
     */
    public function deleteEducation( $educationList )
    {
   	 	try
        {
	    	if(is_array($educationList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Education')
					    ->whereIn('eduCode', $educationList );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read Education
     * @param $customFieldList
     * @return void
     */
    public function readEducation( $id )
    {
   	 	try
        {
	    	$education = Doctrine::getTable('Education')->find($id);
	    	return $education;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
    /**
     * Search Education
     * @param $saleryGradeList
     * @return unknown_type
     */
  	public function searchEducation( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Education') 
				    			->where("$searchMode = ?",$searchValue);
				    
			$educationList = $q->execute();
			
			return $educationList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	 /**
     * Get Employee stat
     * @return unknown_type
     */
    public function getLicensesList( $orderField='licenses_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Licenses')
			    ->orderBy($orderField.' '.$orderBy);
			
			$educationList = $q->execute();
			   
			return  $educationList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
 /**
     * Save Employee Status
     * @param SalaryGrade $salaryGrade
     * @return unknown_type
     */
    public function saveLicenses(Licenses $licenses)
    {
    	try
        {
        	if( $licenses->getLicensesCode() == '' )
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($licenses);
				$licenses->setLicensesCode( $idGenService->getNextID() );
        	}
        	$licenses->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Employee status
     * @param $saleryGradeList
     * @return unknown_type
     */
    public function deleteLicenses( $licensesList )
    {
   	 	try
        {
	    	if(is_array($licensesList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Licenses')
					    ->whereIn('licenses_code', $licensesList );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search Employee stat
     * @param $saleryGradeList
     * @return unknown_type
     */
  	public function searchLicenses( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Licenses') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$licensesList = $q->execute();
			
			return $licensesList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Read Licenses
     * @param $customFieldList
     * @return void
     */
    public function readLicenses( $id )
    {
   	 	try
        {
	    	$licenses = Doctrine::getTable('Licenses')->find($id);
	    	return $licenses;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
}