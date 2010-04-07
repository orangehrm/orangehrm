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
class JobService extends BaseService {
	
	/**
	 * Save Job Category
	 * @return unknown_type
	 */
	public function saveJobCategory( JobCategory $jobCategory)
	{
		try
        {
	    	if( $jobCategory->getEecCode() == '')
	    	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($jobCategory);
				$jobCategory->setEecCode( $idGenService->getNextID());
	    	}
        	$jobCategory->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
	}
	
	/**
	 * List Job category
	 * @return unknown_type
	 */
	public function getJobCategoryList( $orderField='eec_code',$orderBy='ASC' )
	{
		try
        {
	    	$q = Doctrine_Query::create()
			    ->from('JobCategory')
			    ->orderBy($orderField.' '.$orderBy);
			
			$jobCategoryList = $q->execute();
			   
			return  $jobCategoryList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
	}
	
	/**
	 * Delete Job category
	 * @return unknown_type
	 */
	public function deleteJobCategory( $jobCategoryList)
	{
		try
        {
	    	if(is_array($jobCategoryList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('JobCategory')
					    ->whereIn('eec_code', $jobCategoryList );
					
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
	}
	
  /**
     * 
     * @param $searchParam
     * @return unknown_type
     */
    public function searchJobCategory( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q = Doctrine_Query::create( )
				    ->from('JobCategory') 
				   ->where("$searchMode = ?",$searchValue);
				    
			$jobCategoryList = $q->execute();
			
			return $jobCategoryList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
     /**
     * Read Job Category
     * @param $customFieldList
     * @return void
     */
    public function readJobCategory( $id )
    {
   	 	try
        {
	    	$jobCategory = Doctrine::getTable('JobCategory')->find($id);
	    	return $jobCategory;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
    /**
     * Save Salery grade
     * @param SalaryGrade $salaryGrade
     * @return unknown_type
     */
    public function saveSaleryGrade(SalaryGrade $salaryGrade)
    {
    	try
        {
	    	if( $salaryGrade->getSalGrdCode() == '')
	    	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($salaryGrade);
				$salaryGrade->setSalGrdCode( $idGenService->getNextID() );
	    	}
        	$salaryGrade->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Get Salery Grade
     * @return unknown_type
     */
    public function getSaleryGradeList( $orderField='sal_grd_code',$orderBy='ASC')
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('SalaryGrade')
			    ->orderBy($orderField.' '.$orderBy);
			
			$saleryGradeList = $q->execute();
			   
			return  $saleryGradeList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Delete Salery grade
     * @param $saleryGradeList
     * @return unknown_type
     */
    public function deleteSaleryGrade( $saleryGradeList)
    {
   	 	try
        {
	    	if(is_array($saleryGradeList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('SalaryGrade')
					    ->whereIn('sal_grd_code', $saleryGradeList );
					
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
 	/**
     * Search the salery grade 
     * @param $searchParam
     * @return unknown_type
     */
    public function searchSaleryGrade( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q = Doctrine_Query::create( )
				    ->from('SalaryGrade') 
				   ->where("$searchMode = ?",$searchValue);
				    
			$saleryGradeList = $q->execute();
			
			return $saleryGradeList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read SaleryGrade
     * @param $customFieldList
     * @return void
     */
    public function readSaleryGrade( $id )
    {
   	 	try
        {
	    	$saleryGrade = Doctrine::getTable('SalaryGrade')->find($id);
	    	return $saleryGrade;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
     /**
     * Read SaleryGrade
     * @param $customFieldList
     * @return void
     */
    public function saveSalleryGradeCurrency( SalaryCurrencyDetail $salaryCurrencyDetail )
    {
    	try
        {
        	if(!$this->isExistingSalleryGradeCurrency($salaryCurrencyDetail))
        	{
        		$salaryCurrencyDetail->save();
        	}else
        		return false;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
  /**
     * Read SaleryGrade
     * @param $customFieldList
     * @return void
     */
    public function isExistingSalleryGradeCurrency( SalaryCurrencyDetail $salaryCurrencyDetail )
    {
    	try
        {
        	$q = Doctrine_Query::create()
			    ->from('SalaryCurrencyDetail')
			    ->where("sal_grd_code='".$salaryCurrencyDetail->getSalGrdCode()."' AND currency_id='".$salaryCurrencyDetail->getCurrencyId()."'");
			
			 if($q->count()>0)
			 	return true ;
			 else
			 	return false ;
			 	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
 	/**
     * Get Salery Grade
     * @return unknown_type
     */
    public function getSalleryGradeCurrency( $saleryGradeCode)
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('SalaryCurrencyDetail')
			    ->where("sal_grd_code='$saleryGradeCode'");
			
			
			$saleryGradeCurrencyList = $q->execute();
			   
			return  $saleryGradeCurrencyList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Delete Salery grade
     * @param $saleryGradeList
     * @return unknown_type
     */
    public function deleteSalleryGradeCurrency( $saleryGradeId,$saleryGradeCurrencyList)
    {
   	 	try
        {
	    	if(is_array($saleryGradeCurrencyList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('SalaryCurrencyDetail')
					    ->where("sal_grd_code ='$saleryGradeId'")
					    ->whereIn('currency_id', $saleryGradeCurrencyList  );
					
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Get Employee stat
     * @return unknown_type
     */
    public function getEmployeeStatusList( $orderField='id',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('EmployeeStatus')
			    ->orderBy($orderField.' '.$orderBy);
			
			$employeeStatusList = $q->execute();
			   
			return  $employeeStatusList ;
			
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
    public function saveEmployeeStatus(EmployeeStatus $employeeStatus)
    {
    	try
        {
        	if( $employeeStatus->getId()=='')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($employeeStatus);
				$employeeStatus->setId( $idGenService->getNextID() );
        	}
        	$employeeStatus->save();
			
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
    public function deleteEmployeeStatus( $employeeStatusList)
    {
   	 	try
        {
	    	if(is_array($employeeStatusList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('EmployeeStatus')
					    ->whereIn('id', $employeeStatusList );
					
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
  	public function searchEmployeeStatus( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('EmployeeStatus') 
				    			->where("$searchMode = ?",$searchValue);
				    
			$employeeStatList = $q->execute();
			
			return $employeeStatList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read EmployeeStatus
     * @return void
     */
    public function readEmployeeStatus( $id )
    {
   	 	try
        {
	    	$employeeStatus = Doctrine::getTable('EmployeeStatus')->find($id);
	    	return $employeeStatus;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    }
    
   /**
     * Get Employee stat
     * @return unknown_type
     */
    public function getJobSpecificationsList( $orderField='jobspec_id',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('JobSpecifications')
			    ->orderBy($orderField.' '.$orderBy);
			
			$jobSpecificationsList = $q->execute();
			   
			return  $jobSpecificationsList ;
			
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
    public function saveJobSpecifications(JobSpecifications $jobSpecifications)
    {
    	try
        {
        	if( $jobSpecifications->getJobspecId() == '') 
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($jobSpecifications);
				$jobSpecifications->setJobspecId( $idGenService->getNextID() );
        	}
        	
        	$jobSpecifications->save();
			
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
    public function deleteJobSpecifications( $jobSpecificationsList)
    {
   	 	try
        {
	    	if(is_array($jobSpecificationsList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('JobSpecifications')
					    ->whereIn('jobspec_id', $jobSpecificationsList );
					
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
  	public function searchJobSpecifications( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('JobSpecifications') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$jobSpecificationsList = $q->execute();
			
			return $jobSpecificationsList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
     /**
     * Read JobSpecifications
     * @return void
     */
    public function readJobSpecifications( $id )
    {
   	 	try
        {
	    	$jobSpecifications = Doctrine::getTable('JobSpecifications')->find($id);
	    	return $jobSpecifications;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
      /**
     * Get Employee stat
     * @return unknown_type
     */
    public function getJobTitleList( $orderField='job.id',$orderBy='ASC', $activeStatus=array(1))
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('JobTitle job')
			    ->whereIn('isActive', $activeStatus)
			    ->orderBy($orderField.' '.$orderBy);
			
			$jobTitleList = $q->execute();
			   
			return  $jobTitleList ;
			
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
    public function saveJobTitle(JobTitle $jobTitle,$emplymentStatus = array())
    {
    	try
        {
        	if( $jobTitle->getId() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($jobTitle);
				$jobTitle->setId( $idGenService->getNextID() );
				
				
        	}
        	
        	if( $jobTitle->getSalaryGradeId() == '-1')
        		$jobTitle->setSalaryGradeId(new SalaryGrade());
        	
        		if( $jobTitle->getJobspecId() == '-1')
        		$jobTitle->setJobspecId(new JobSpecifications());	
        		
			$jobTitle->save();
        	
			foreach( $emplymentStatus as $empStatus)
			{
				$jobEmpStatus	=	new JobTitleEmployeeStatus();
    			$jobEmpStatus->setJobtitCode($jobTitle->getId());
    			$jobEmpStatus->setEstatCode($empStatus->getId());
    			$jobEmpStatus->save();
			}
        	
			
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
    public function deleteJobTitle( $jobTitleList)
    {
   	 	try
        {
	    	if(is_array($jobTitleList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('JobTitle')
					    ->whereIn('id', $jobTitleList );
	
					   
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
  	public function searchJobTitle( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('JobTitle') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$jobTitleList = $q->execute();
			
			return $jobTitleList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read JobTitle
     * @return void
     */
    public function readJobTitle( $id )
    {
   	 	try
        {
	    	$jobTitle = Doctrine::getTable('JobTitle')->find($id);
	    	return $jobTitle;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
   
}