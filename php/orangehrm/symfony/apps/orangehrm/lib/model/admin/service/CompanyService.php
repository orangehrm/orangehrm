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
class CompanyService extends BaseService {
    

    /**
     *
     * @param CompanyGeninfo $CompanyGeninfo
     * @return Company
     */
    public function getCompany()
    {
        try
        {
        	$companyGeninfo		=	$this->getCompanyGenInfo();
        	
        
        	
            $dataList   =   explode('|', $companyGeninfo->getGeninfoValues());
        		
            $company    =   new Company();
            $company->setComCode($companyGeninfo->code);
            $company->comapanyName  =   $dataList[0];
            $company->country       =   $dataList[1];
            $company->street1       =   $dataList[2];
            $company->street2       =   $dataList[3];
            $company->state         =   $dataList[4];
            $company->city          =   $dataList[5];
            $company->zipCode       =   $dataList[6];
            $company->phone         =   $dataList[7];
            $company->fax           =   $dataList[8];
            $company->taxId         =   $dataList[9];
            $company->naics         =   $dataList[10];
            $company->comments      =   $dataList[11];
            $company->setEmpCount($this->getEmployeeCount());

          
            return $company;

        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param Company $company
     * @return String
     */
    private function getCompanyValues( Company $company){

        try
        {
            $string =  $company->comapanyName.'|'.$company->country.'|'.$company->street1.'|'.$company->street2.'|'.$company->state.'|'. $company->city.'|'.$company->zipCode.'|'.$company->phone.'|'.$company->fax.'|'.$company->taxId.'|'.$company->naics.'|'.$company->comments;
            return $string;

        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
        
    }
    
    /**
     *
     * @param Company $company
     * @return <type>
     */
    private function getCompanyGenInfo( )
    {
      try
        {
        	
        	$companyInfo	=	Doctrine::getTable('CompanyGeninfo')->find('001');
        	
        	/*
        	$companyInfo	=	new CompanyGeninfo();
           	$q = Doctrine_Query::create()
			    ->from('CompanyGeninfo')
			    ->limit(1);

		
			$companyInfo	=	$q->execute();
			*/
			return $companyInfo;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Get Employee counts
     * @return unknown_type
     */
    private function getEmployeeCount()
    {
    	try
        {
        	$empCount	=	0;
           	$q = Doctrine_Query::create()
			    ->from('Employee');
			    
			$empCount	=	$q->execute();
			return count($empCount);
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * Get Employee counts
     * @return unknown_type
     */
   
    public function getEmployeeList()
    {
    try
        {
        	$empCount	=	0;
           	$q = Doctrine_Query::create()
			    ->from('Employee');
			
			$employeeList = $q->execute();
			   
			return  $employeeList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    public function getEmployeeListAsJson()
    {
     	try
        {
           	$jsonString	=	array();
        	$q = Doctrine_Query::create()
			    ->from('Employee');
			
			$employeeList = $q->execute();
			   
			foreach( $employeeList as $employee)
			{
				array_push($jsonString,"{name:'".$employee->getFirstName().' '.$employee->getLastName()."',id:'".$employee->getEmpNumber()."'}");
			}
			
			$jsonStr	=	" [".implode(",",$jsonString)."]";
			return $jsonStr;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Save Company detail
     * @return unknown_type
     */
    public function saveCompany( Company $company)
    {
    	try
        {
        	$companyInfo		=	$this->getCompanyGenInfo();
        	$companyInfo->setGeninfoValues( $this->getCompanyValues($company));
        	$companyInfo->save();
        			
			$rootCompanyStructure	=	$this->readCompanyStructure(1);
			$rootCompanyStructure->setTitle( $company->getComapanyName());
			$rootCompanyStructure->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Get Company location
     * @return Location
     */
    public function getCompanyLocation($orderField='loc_code',$orderBy='ASC')
    {
    	try
        {
        		$q = Doctrine_Query::create()
				    ->from('Location') 
				    ->orderBy($orderField.' '.$orderBy);
				    
			   $location = $q->execute();
			   
			   return  $location ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * 
     * Save company Location
     */
    public function saveCompanyLocation(Location $location )
    {
    	try
        {
        	if( $location->getLocCode()== '')
        	{
				$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($location);
				$location->setLocCode($idGenService->getNextID());
        	}
        	$location->save();
        	
    	}catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Delete Company location
     * @param $locationCodeList
     * @return unknown_type
     */
    public function deleteCompanyLocation($locationCodeList )
    {
    	try
        {
	    	if( is_array($locationCodeList))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Location')
					    ->whereIn('loc_code', $locationCodeList );
					
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
    public function searchCompanyLocation( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q = Doctrine_Query::create( )
				    ->from('Location') 
				    ->where("$searchMode = ?",$searchValue);
				    
			$location = $q->execute();
			
			
			
			return $location;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
     /**
     * Read Company location
     * @param $customFieldList
     * @return void
     */
    public function readLocation( $id )
    {
   	 	try
        {
	    	$location = Doctrine::getTable('Location')->find($id);
	    	return $location;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
    /**
     *  Get Company Location
     * @return unknown_type
     */
    public function getCompanyProperty( $orderField='prop_id',$orderBy='ASC' )
    {
    	try
        {
	    	$q = 	Doctrine_Query::create()
				    ->from('CompanyProperty')
				    ->orderBy($orderField.' '.$orderBy);
				   
		    $companyProperty = $q->execute();
		   
		    return  $companyProperty ;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Save company property
     * @param $companyProperty
     * @return unknown_type
     */
    public function saveCompanyProporty( CompanyProperty $companyProperty)
    {
    	try
        {
	    	$companyProperty->save();
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Delete Company property
     * @return unknown_type
     */
    public function deleteCompanyProperty( $companyPropertList )
    {
    	try
        {
        	if( is_array($companyPropertList))
	    	{
	        	$q 	= 	Doctrine_Query::create()
					    ->delete('CompanyProperty')
					    ->whereIn('prop_id', $companyPropertList );
					
				$numDeleted = $q->execute();
	    	}
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
     /**
     * Read CompanyProperty
     * @return void
     */
    public function readCompanyProperty( $id )
    {
   	 	try
        {
	    	$companyProperty = Doctrine::getTable('CompanyProperty')->find($id);
	    	return $companyProperty;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
    /**
     * Save company structure
     * @return void
     */
    public function saveCompanyStructure( CompanyStructure $companyStructure)
    {
    	try
        {
	    	if( $companyStructure->getId()== '')
        	{
				$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($companyStructure);
				$companyStructure->setId($idGenService->getNextID());
        	}
        	$companyStructure->save();
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    }
    
    /**
     * Update Company structure
     * @return unknown_type
     */
    public function updateCompanyStructure( CompanyStructure $companyStructure )
    {
    	try
        {
        	$companyStructure->save();
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    }
    
   /**
     * Read CompanyProperty
     * @return void
     */
    public function readCompanyStructure( $id )
    {
   	 	try
        {
	    	$companyStructure = Doctrine::getTable('CompanyStructure')->find($id);
	    	return $companyStructure;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    }
    
    public function getSubDivisionList() {
        
   	 	try {
	    	
	    	$q = Doctrine_query::create()
	    		 ->from('CompanyStructure')
	    		 ->where('id > 1');
	    		 
	    	$subDivisionList = $q->execute();
	    	
	    	return $subDivisionList;
	    	
        }catch( Exception $e)         {
            throw new AdminServiceException($e->getMessage());
     
        }
        
    }

}

