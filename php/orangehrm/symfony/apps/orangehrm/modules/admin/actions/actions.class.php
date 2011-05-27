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
 * Actions class for Admin Module
 */
class adminActions extends sfActions {
	
	
	
	/**
     * Index action. Displays employee list
     * 
     * @param sfWebRequest $request
     */
    public function executeCompanygeninfo(sfWebRequest $request){
		
    	
    	$countryService 	=	new CountryService();
		$adminService		=	new CompanyService();
		
    	if ($request->isMethod('post')) {
    		$company	=	new Company();
    		$company->setComCode($request->getParameter('txtCode'));
    		$company->setComapanyName($request->getParameter('txtCompanyName'));
    		$company->setFax($request->getParameter('txtFax'));
    		$company->setNaics($request->getParameter('txtNAICS'));
    		$company->setPhone($request->getParameter('txtPhone'));
    		$company->setTaxId($request->getParameter('txtTaxID'));
    		$company->setCountry($request->getParameter('cmbCountry'));
    		$company->setStreet1($request->getParameter('txtStreet1'));
    		$company->setStreet2($request->getParameter('txtStreet2'));
    		$company->setCity($request->getParameter('txtCity'));
    		$company->setState($request->getParameter('txtState'));
    		$company->setZipCode($request->getParameter('txtZIP'));
    		$company->setComments($request->getParameter('txtComments'));
    		
    		
    		
    		$adminService->saveCompany($company);
    	}
    	
    	
		$this->countryList  =	$countryService->getCountryList();
		$this->provinceList	=	$countryService->getProvinceList();
		$this->company		=	$adminService->getCompany();
		
    }
    
    /**
     * Company info List
     * 
     * @param sfWebRequest $request
     */
    public function executeListCompanylocation( sfWebRequest $request )
    {
    	$adminService		=	new CompanyService();
    	$this->sorter 		= 	new ListSorter('location.sort', 'admin_module', $this->getUser(), array('loc_code', ListSorter::ASCENDING));
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->locationList	=	$adminService->getCompanyLocation($request->getParameter('sort'),$request->getParameter('order'));
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode	=	$request->getParameter('searchMode');
	        		$this->searchValue	=	$request->getParameter('searchValue');
	        		$this->locationList	=	$adminService->searchCompanyLocation($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listCompanylocation');
    		
        		}
        		
        	}else
        		$this->locationList	=	$adminService->getCompanyLocation();
        }
    		
    }

    /**
     * 
     * @param $request
     * @return unknown_type
     */
    public function executeSaveCompanyLocation( sfWebRequest $request )
    {
    	
    	if ($request->isMethod('post')) {
    		
    		$companyLocation	=	new Location();
    		$companyLocation->setLocName($request->getParameter('txtName'));
    		$companyLocation->setLocCountry($request->getParameter('cmbCountry'));
    		$companyLocation->setLocState($request->getParameter('txtState'));
    		$companyLocation->setLocCity($request->getParameter('txtCity'));
    		$companyLocation->setLocAdd($request->getParameter('txtAddress'));
    		$companyLocation->setLocZip($request->getParameter('txtZipCode'));
    		$companyLocation->setLocPhone($request->getParameter('txtPhone'));
    		$companyLocation->setLocFax($request->getParameter('txtFax'));
    		$companyLocation->setLocComments($request->getParameter('txtComments'));
    		
    		$companyService		=	new CompanyService();
    		$companyService->saveCompanyLocation( $companyLocation );
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listCompanylocation');
    		
    	}
    	$countryService 	=	new CountryService();
    	$this->countryList  =	$countryService->getCountryList();
		$this->provinceList	=	$countryService->getProvinceList();
		
    }
    
   /**
     * Update CompanyLocation
     * @return unknown_type
     */
    public function executeUpdateCompanyLocation(sfWebRequest $request)
    {
    	$companyService				=	new CompanyService();
    	$companyLocation			=	$companyService->readLocation($request->getParameter('id'));
    	$this->companyLocation		=	$companyLocation ;
    	if ($request->isMethod('post')) {
    		
    		$companyLocation->setLocName($request->getParameter('txtName'));
    		$companyLocation->setLocCountry($request->getParameter('cmbCountry'));
    		$companyLocation->setLocState($request->getParameter('txtState'));
    		$companyLocation->setLocCity($request->getParameter('txtCity'));
    		$companyLocation->setLocAdd($request->getParameter('txtAddress'));
    		$companyLocation->setLocZip($request->getParameter('txtZipCode'));
    		$companyLocation->setLocPhone($request->getParameter('txtPhone'));
    		$companyLocation->setLocFax($request->getParameter('txtFax'));
    		$companyLocation->setLocComments($request->getParameter('txtComments'));
    		
    		$companyService		=	new CompanyService();
    		$companyService->saveCompanyLocation( $companyLocation );
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listCompanylocation');
    	}
    	
    	$countryService 	=	new CountryService();
    	$this->countryList  =	$countryService->getCountryList();
		$this->provinceList	=	$countryService->getProvinceList();
  }
  
    /**
     * 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeDeleteCompanyLocation( sfWebRequest $request )
    {
    	if ($request->isMethod('post')) {
    		
    		if( count($request->getParameter('chkLocID')) > 0)
    		{
	    		$companyService		=	new CompanyService();
	    		$companyService->deleteCompanyLocation($request->getParameter('chkLocID'));
				$this->setMessage('SUCCESS',array('Successfully Deleted'));
    		}else
    			$this->setMessage('NOTICE',array('Select at least one record to delete'));
			
			$this->redirect('admin/listCompanylocation');
    	}
    	
    }
    
    /**
     * 
     * @param $request
     * @return unknown_type
     */
    public function executeSaveCompanyProporty( sfWebRequest $request )
    {
   		if ($request->isMethod('post')) {
    		
    		$companyProperty	=	new CompanyProperty();
    		$companyProperty->setPropName( $request->getParameter('txtName'));
    		
    		$companyService		=	new CompanyService();
    		$companyService->saveCompanyProporty($companyProperty);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listCompanyProporty');
    	}
    }
    
 	/**
     * Update Education
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateCompanyProporty(sfWebRequest $request)
    {
    	$companyService		=	new CompanyService();
    	$companyProperty		=	$companyService->readCompanyProperty($request->getParameter('id'));
    	$this->companyProperty	=	$companyProperty ;
    	if ($request->isMethod('post')) {
    		
    		
    		$companyProperty->setPropName( $request->getParameter('txtName'));
    		$companyService->saveCompanyProporty($companyProperty);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listCompanyProporty');
    	}
  }
  
    /**
     * 
     * @param $request
     * @return unknown_type
     */
    public function executeListCompanyProporty( sfWebRequest $request)
    {
   	 	$companyService		=	new CompanyService();
    	
    	$this->sorter 		= 	new ListSorter('location.sort', 'admin_module', $this->getUser(), array('prop_id', ListSorter::ASCENDING));
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->proportyList	=	$companyService	->getCompanyProperty($request->getParameter('sort'),$request->getParameter('order'));
        }else
        {
        		$this->proportyList	=	$companyService	->getCompanyProperty();
        }

    	/*$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('prop_id', ListSorter::ASCENDING));
    	
    	$companyService		=	new CompanyService();
    	$this->proportyList	=	$companyService	->getCompanyProperty();
    	$employeeList		=	$companyService->getEmployeeList();
    	$this->employeeList	=	$employeeList ;
    	$employeeListString	=	'';
    	$empArray			=	array();
    	foreach( $employeeList as $employee)
    	{
    		$employeeListString .= $employee->getEmpFirstname().',';
    		array_push($empArray,array($employee->getEmpNumber(),$employee->getEmpFirstname()));
    	}
    	$this->employeeListString	=	$employeeListString ;
    	$this->employeeListString1	=	json_encode($empArray);
    	*/
    }
    
    /**
     * Delete Company property 
     * 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeProcessCompnayProperty( sfWebRequest $request )
    {
    	if ($request->isMethod('post')) {
    		
    		$mode				=	$request->getParameter('mode');
    		
    		switch( $mode )
    		{
    			case 'delete':
    				if( count($request->getParameter('chkLocID')) > 0)
    				{
	    				$companyService		=	new CompanyService();
	    				$companyService->deleteCompanyProperty($request->getParameter('chkLocID'));	
	    				$this->setMessage('SUCCESS',array('Successfully Deleted'));
    				}else
    					$this->setMessage('NOTICE',array('Select at least one record to delete'));
    					
    			break;
    			
    			case 'save':
    				$companyService		=	new CompanyService();
    				foreach( $request->getParameter('txtProperty') as $id=>$value)
    				{
    					$comProperty	=	$companyService->readCompanyProperty($id);
    					$comProperty->setEmpId( $value );
    					$companyService->saveCompanyProporty( $comProperty );
    					
    				}
    				$this->setMessage('SUCCESS',array('Successfully Updated'));
    				
    			break;
    		}
    	}
    	$this->redirect('admin/listCompanyProporty');
    }
    
   /**
     * View job category list 
     * 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListJobCategory( sfWebRequest $request)
    {
    	$jobService	=	new JobService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('eec_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->jobCategoryList	=	$jobService->getJobCategoryList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->jobCategoryList	=	$jobService->searchJobCategory($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->jobCategoryList	=	$jobService->getJobCategoryList();
        		}
        		
        		
        	}else
        		$this->jobCategoryList	=	$jobService->getJobCategoryList();
        }
    	
    }
    
    /**
     *  Delete Job Category
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeDeleteJobCategory( sfWebRequest $request)
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$jobService		=	new JobService();
	    	$jobService->deleteJobCategory($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	
    	$this->redirect('admin/listJobCategory');
    }
    
    /**
     * Save Job category
     * @param $request
     * @return unknown_type
     */
    public function executeSaveJobCategory( sfWebRequest $request)
    {
    
    	if ($request->isMethod('post')) {
    		$jobService		=	new JobService();
    		
    		$jobCategory	=	new JobCategory();
    		$jobCategory->setEecDesc( $request->getParameter('txtName') );
    		$jobService->saveJobCategory( $jobCategory );
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listJobCategory');
    	}
    }
   
 	/**
     * Update JobCategory
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateJobCategory(sfWebRequest $request)
    {
    	$jobService		=	new JobService();
    	$jobCategory	=	$jobService->readJobCategory($request->getParameter('id'));
    	$this->jobCategory		=	$jobCategory ;
    	if ($request->isMethod('post')) {
    		
    		$jobCategory->setEecDesc( $request->getParameter('txtName') );
    		$jobService->saveJobCategory( $jobCategory );
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listJobCategory');
    	}
  }
  
    /**
     * List Salery grade
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListSaleryGrade(sfWebRequest $request)
    {
   	 	$jobService	=	new JobService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('sal_grd_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->saleryGradeList	=	$jobService->getSaleryGradeList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->saleryGradeList	=	$jobService->searchSaleryGrade($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listSaleryGrade');
        		}
        		
        		
        	}else
        		$this->saleryGradeList	=	$jobService->getSaleryGradeList();
        }
    }
    
    /**
     * Save Salery Grade 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveSaleryGrade(sfWebRequest $request)
    {
    	if ($request->isMethod('post')) {
    		$jobService		=	new JobService();
    		
    		$saleryGrade	=	new SalaryGrade();
    		$saleryGrade->setSalGrdName( $request->getParameter('txtName') );
    		$jobService->saveSaleryGrade($saleryGrade);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		
    		$this->redirect('admin/listSaleryGrade');
    	}
    }
    
 	/**
     * Update SaleryGrade
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateSaleryGrade(sfWebRequest $request)
    {
    	$currencyService		=	new CurrencyService();
    	$jobService				=	new JobService();
    	$saleryGrade			=	$jobService->readSaleryGrade($request->getParameter('id'));
    	$this->saleryGrade		=	$saleryGrade ;
    	$this->currencyList		=	$currencyService->getCurrencyList();
    	$this->sallerGradeCurrencyList	=	$jobService->getSalleryGradeCurrency($request->getParameter('id'));
    	
    	if ($request->isMethod('post')) {
    		
    		
    		$saleryGrade->setSalGrdName( $request->getParameter('txtName') );
    		$jobService->saveSaleryGrade($saleryGrade);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listSaleryGrade');
    	}
    	
  }
  
  	/**
     * Save Currency
     * @param sfWebRequest $request
     * @return unknown_type
     */
  public function executeSaveSaleryGradeCurrency( sfWebRequest $request)
  {
  	if ($request->isMethod('post')) {
  		$jobService				=	new JobService();
  		
  		$saleryGradeId	=	$request->getParameter('id');
  		$salaryCurrencyDetail	=	new SalaryCurrencyDetail();
  		$salaryCurrencyDetail->setSalGrdCode( $saleryGradeId );
  		$salaryCurrencyDetail->setCurrencyId( $request->getParameter('cmbUnAssCurrency') );
  		$salaryCurrencyDetail->setMinSalary( $request->getParameter('txtMinSal') );
  		$salaryCurrencyDetail->setMaxSalary( $request->getParameter('txtMaxSal') );
  		$salaryCurrencyDetail->setSalaryStep( $request->getParameter('txtStepSal') );
  		
  		$jobService->saveSalleryGradeCurrency( $salaryCurrencyDetail );
  		$this->redirect('admin/updateSaleryGrade?id='.$saleryGradeId);
  	}
  	
  }
  
 /**
     * delete salery grade 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeDeleteSaleryGradeCurrency( sfWebRequest $request )
    {
    	$saleryGradeId	=	$request->getParameter('id');
    	
    	if( count($request->getParameter('chkdel')) > 0)
    	{
	    	$jobService		=	new JobService();
	    	$jobService->deleteSalleryGradeCurrency($saleryGradeId,$request->getParameter('chkdel'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	
    	$this->redirect('admin/updateSaleryGrade?id='.$saleryGradeId);
    }
  
    /**
     * delete salery grade 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeDeleteSaleryGrade( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$jobService		=	new JobService();
	    	$jobService->deleteSaleryGrade($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	
    	$this->redirect('admin/listSaleryGrade');
    }
    

    
 	/**
     * List Salery grade
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListEmployeeStatus(sfWebRequest $request)
    {
    	$jobService	=	new JobService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('estat_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listEmpStatus	=	$jobService->getEmployeeStatusList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listEmpStatus	=	$jobService->searchEmployeeStatus($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listEmployeeStatus');
        		}
        		
        		
        	}else
        		$this->listEmpStatus = $jobService->getEmployeeStatusList();
        }
    	
   
    }
    
 	/**
     * Save Salery Grade 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveEmployeeStatus(sfWebRequest $request)
    {
    	if ($request->isMethod('post')) {
    		$jobService		=	new JobService();
    		
    		$employeeStatus	=	new EmployeeStatus();
    		$employeeStatus->setEstatName( $request->getParameter('txtName') );
    		$jobService->saveEmployeeStatus( $employeeStatus );
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listEmployeeStatus');
    	}
    }
    
 	/**
     * Update Education
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateEmployeeStatus(sfWebRequest $request)
    {
    	$jobService					=	new JobService();
    	$employeeStatus				=	$jobService->readEmployeeStatus($request->getParameter('id'));
    	$this->employeeStatus		=	$employeeStatus ;
    	if ($request->isMethod('post')) {
    		
    		
    		$employeeStatus->setEstatName( $request->getParameter('txtName') );
    		$jobService->saveEmployeeStatus( $employeeStatus );
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listEmployeeStatus');
    	}
  }
  
	/**
     * Delete Employee status
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteEmployeeStatus( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$jobService		=	new JobService();
	    	$jobService->deleteEmployeeStatus($request->getParameter('chkLocID'));	
	    	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	
    	$this->redirect('admin/listEmployeeStatus');
    }
    
 	/**
     * List Job Specifications 
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListJobSpecifications(sfWebRequest $request)
    {
    	$jobService	=	new JobService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('jobspec_id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
           $this->listJobSpecifications	=	$jobService->getJobSpecificationsList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listJobSpecifications	=	$jobService->searchJobSpecifications($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listJobSpecifications');
        		}
        		
        	}else
        		$this->listJobSpecifications = $jobService->getJobSpecificationsList();
        }
    	
    }
    
   /**
     * Save JobSpecifications
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveJobSpecifications(sfWebRequest $request)
    {
    	if ($request->isMethod('post')) {
    		$jobService		=	new JobService();
    		
    		$jobSpecification	=	new JobSpecifications();
    		$jobSpecification->setJobspecName( $request->getParameter('txtName') );
    		$jobSpecification->setJobspecDesc( $request->getParameter('txtDesc') );
    		$jobSpecification->setJobspecDuties( $request->getParameter('txtDuties') );
    		
    		$jobService->saveJobSpecifications($jobSpecification);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listJobSpecifications');
    	}
    }
    
   /**
     * Update Education
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateJobSpecifications(sfWebRequest $request)
    {
    	$jobService		=	new JobService();
    	$jobSpecification			=	$jobService->readJobSpecifications($request->getParameter('id'));
    	$this->jobSpecification		=	$jobSpecification ;
    	if ($request->isMethod('post')) {
    		
    		$jobSpecification->setJobspecName( $request->getParameter('txtName') );
    		$jobSpecification->setJobspecDesc( $request->getParameter('txtDesc') );
    		$jobSpecification->setJobspecDuties( $request->getParameter('txtDuties') );
    		
    		$jobService->saveJobSpecifications($jobSpecification);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listJobSpecifications');
    	}
  }
  
	/**
     * Delete Employee status
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteJobSpecifications( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$jobService		=	new JobService();
	    	$jobService->deleteJobSpecifications($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listJobSpecifications');
    }
    
	/**
     * List Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListJobTitle(sfWebRequest $request)
    {
    	$jobService	=	new JobService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('JobTitle.id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
           $this->listJobTitle	=	$jobService->getJobTitleList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listJobTitle	=	$jobService->searchJobTitle($this->searchMode,$this->searchValue);
        		}else
        		{
        			
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listJobTitle');
        		}
        		
        		
        	}else
        		$this->listJobTitle = $jobService->getJobTitleList();
        }
        
    	
    }
    
   /**
     * Save Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveJobTitle(sfWebRequest $request)
    {
    	$jobService		=	new JobService();
    	if ($request->isMethod('post')) {
    		$arrEmployeeStatus	=	array();
    		
    		$jobTitle			=	 new JobTitle();
    		$jobTitle->setName( $request->getParameter('txtName') );
    		$jobTitle->setDescription( $request->getParameter('txtJobTitleDesc') );
    		$jobTitle->setComments( $request->getParameter('txtJobTitleComments') );
    		$jobTitle->setJobspecId( $request->getParameter('txtSpec') );
    		$jobTitle->setSalaryGradeId( $request->getParameter('txtPayGrade') );
    		
    		//$employeeStatus	=	$jobService->readEmployeeStatus($empStatusId);
	    	//array_push($arrEmployeeStatus,$employeeStatus);	
    		$jobService->saveJobTitle($jobTitle,$employeeStatus);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listJobTitle');
    	}
    	$this->listJobSpecifications 	= 	$jobService->getJobSpecificationsList();
    	$this->saleryGradeList			=	$jobService->getSaleryGradeList();
    }
    
 	 /**
     * Update Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateJobTitle(sfWebRequest $request)
    {
    	$jobService			=	new JobService();
    	$jobTitle			=	$jobService->readJobTitle($request->getParameter('id'));
    	$this->jobTitle		=	$jobTitle ;
    	if ($request->isMethod('post')) {
    		
    		$arrEmployeeStatus	=	array();
    		
    		$jobTitle->setName( $request->getParameter('txtName') );
    		$jobTitle->setDescription( $request->getParameter('txtJobTitleDesc') );
    		$jobTitle->setComments( $request->getParameter('txtJobTitleComments') );
    		$jobTitle->setJobspecId( $request->getParameter('txtSpec') );
    		$jobTitle->setSalaryGradeId( $request->getParameter('txtPayGrade') );
    		
    		
    		
    		foreach( explode(',',$request->getParameter('selEmpStatus')) as $empStatusId)
    		{
    			if( $empStatusId != '' )
    			{
	    			$employeeStatus	=	$jobService->readEmployeeStatus($empStatusId);
	    			array_push($arrEmployeeStatus,$employeeStatus);
    			}
    		}
    		
    		$jobService->saveJobTitle($jobTitle,$arrEmployeeStatus);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listJobTitle');
    	}
    	$this->listJobSpecifications 	= 	$jobService->getJobSpecificationsList();
    	$this->saleryGradeList			=	$jobService->getSaleryGradeList();
    	$this->listEmploymentStatus		=	$jobService->getEmployeeStatusList();
  }
    
	/**
     * Delete Employee status
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteJobTitle( sfWebRequest $request )
    {
    	
		if( count($request->getParameter('chkLocID')) > 0)
		{
	    	$jobService		=	new JobService();
	    	$jobService->deleteJobTitle($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
		}else
		{
			$this->setMessage('NOTICE',array('Select at least one record to delete'));
		}
		$this->redirect('admin/listJobTitle');
    }
    
	/**
     * List Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListEducation(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('eduCode', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listEducation	=	$educationService->getEducationList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listEducation	=	$educationService->searchEducation($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listEducation');
        		}
        		
        		
        	}else
        		$this->listEducation = $educationService->getEducationList();
        }
        
    	
    }
    
   /**
     * Save Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveEducation(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	if ($request->isMethod('post')) {
    		
    		
    		$education			=	 new Education();
    		$education->setEduUni( $request->getParameter('txtName') );
    		$education->setEduDeg( $request->getParameter('txtDeg') );

    		$educationService->saveEducation($education);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listEducation');
    	}
    	
    }
    
   /**
     * Update Education
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateEducation(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	$education				=	$educationService->readEducation($request->getParameter('id'));
    	$this->education		=	$education ;
    	if ($request->isMethod('post')) {
    		
    		
    		$education->setEduUni( $request->getParameter('txtName') );
    		$education->setEduDeg( $request->getParameter('txtDeg') );

    		$educationService->saveEducation($education);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listEducation');
    	}
    }
    
    
	/**
     * Delete Employee status
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteEducation( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$educationService	=	new EducationService();
	    	$educationService->deleteEducation($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listEducation');
    }

/**
     * List Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListLicenses(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('eduCode', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listLicenses	=	$educationService->getLicensesList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listLicenses		=	$educationService->searchLicenses($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listLicenses');
        		}
        		
        		
        	}else
        		$this->listLicenses = $educationService->getLicensesList();
        }
        
    	
    }
    
   /**
     * Save Job Title
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveLicenses(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	if ($request->isMethod('post')) {
    		
    		
    		$licenses			=	 new Licenses();
    		$licenses->setLicensesDesc( $request->getParameter('txtLicensesDesc') );
    		

    		$educationService->saveLicenses($licenses);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listLicenses');
    	}
    	
    }
    
	/**
     * Update Licenses
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateLicenses(sfWebRequest $request)
    {
    	$educationService	=	new EducationService();
    	$licenses			=	$educationService->readLicenses($request->getParameter('id'));
    	$this->licenses		=	$licenses ;
    	if ($request->isMethod('post')) {
    		
    		
    		$licenses->setLicensesDesc( $request->getParameter('txtLicensesDesc') );
    		$educationService->saveLicenses($licenses);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listLicenses');
    	}
    }
    
	/**
     * Delete Employee status
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteLicenses( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$educationService	=	new EducationService();
	    	$educationService->deleteLicenses($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listLicenses');
    }
    
   /**
     * List Skill
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListSkill(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('skill_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listSkill	=	$skillService->getSkillList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listSkill		=	$skillService->searchSkill($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listSkill');
        		}
        		
        	}else
        		$this->listSkill = $skillService->getSkillList();
        }
        
    	
    }
    
   /**
     * Save Skill
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveSkill(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	if ($request->isMethod('post')) {
    		
    		
    		$skill	=	new Skill();
    		$skill->setSkillName( $request->getParameter('txtSkillName') );
    		$skill->setSkillDescription( $request->getParameter('txtSkillDesc') );
    		

    		$skillService->saveSkill($skill);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listSkill');
    	}
    	
    }
    
	/**
     * Update Skill
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateSkill(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	$skill			=	$skillService->readSkill($request->getParameter('id'));
    	$this->skill	=	$skill ;
    	
    	if ($request->isMethod('post')) {
    		
    		
    		$skill->setSkillName( $request->getParameter('txtSkillName') );
    		$skill->setSkillDescription( $request->getParameter('txtSkillDesc') );
    		$skillService->saveSkill($skill);
			$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listSkill');
     	}
    }
    
	/**
     * Delete Skill
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteSkill( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$skillService	=	new SkillService();
	    	$skillService->deleteSkill($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	
    	$this->redirect('admin/listSkill');
    }
    
   /**
     * List Language
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListLanguage(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('lang_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listLanguage	=	$skillService->getLanguageList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listLanguage		=	$skillService->searchLanguage($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listLanguage');
        		}
        		
        		
        	}else
        		$this->listLanguage = $skillService->getLanguageList();
        }
        
    	
    }
    
   /**
     * Save Language
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveLanguage(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	if ($request->isMethod('post')) {
    		
    		
    		$language	=	new Language();
    		$language->setLangName( $request->getParameter('txtLanguageInfoDesc') );
    		
    		$skillService->saveLanguage($language);
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listLanguage');
    	}
    	
    }
    
	/**
     * Update Language
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateLanguage(sfWebRequest $request)
    {
    	$skillService	=	new SkillService();
    	$language		=	$skillService->readLanguagee($request->getParameter('id'));
    	$this->language	=	$language ;
    	if ($request->isMethod('post')) 
    	{
    		
    		$language->setLangName( $request->getParameter('txtLanguageInfoDesc') );
    		$skillService->saveLanguage($language);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listLanguage');
     	}
    }
    
	/**
     * Delete Language
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteLanguage( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$skillService	=	new SkillService();
	    	$skillService->deleteLanguage($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listLanguage');
    }
    
  /**
     * List Membership type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListMembershipType(sfWebRequest $request)
    {
    	$membershipService	=	new MembershipService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('membtype_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listMembershipType	=	$membershipService->getMembershipTypeList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listMembershipType		=	$membershipService->searchMembershipType($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listMembershipType');
        		}
        		
        		
        	}else
        		$this->listMembershipType = $membershipService->getMembershipTypeList();
        }
        
    	
    }
    
   /**
     * Save Membership type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveMembershipType(sfWebRequest $request)
    {
    	$membershipService	=	new MembershipService();
    	if ($request->isMethod('post')) {
    		
    		
    		$membershipType	=	new MembershipType();
    		$membershipType->setMembtypeName( $request->getParameter('txtMemTypeDescription') );
    		$membershipService->saveMembershipType($membershipType);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listMembershipType');
    	}
    	
    }
    
	/**
     * update MembershipType
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateMembershipType(sfWebRequest $request)
    {
   	 	$membershipService		=	new MembershipService();
    	$membershipType			=	$membershipService->readMembershipType($request->getParameter('id'));
    	$this->membershipType	=	$membershipType ;
    	if ($request->isMethod('post')) {
    		
    		
    		$membershipType->setMembtypeName( $request->getParameter('txtMemTypeDescription') );
    		$membershipService->saveMembershipType($membershipType);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listMembershipType');
    	}
    }
    
	/**
     * Delete Membership Type
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteMembershipType( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$membershipService	=	new MembershipService();
	    	$membershipService->deleteMembershipType($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listMembershipType');
    }
    
  /**
     * List Membership type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListMembership(sfWebRequest $request)
    {
    	$membershipService	=	new MembershipService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('membship_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listMembership	=	$membershipService->getMembershipList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listMembership		=	$membershipService->searchMembership($this->searchMode,$this->searchValue);
        		}else
        		{
        			
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listMembership');
        		}
        		
        		
        	}else
        		$this->listMembership = $membershipService->getMembershipList();
        }
        
    	
    }
    
   /**
     * Save Membership type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveMembership(sfWebRequest $request)
    {
    	$membershipService	=	new MembershipService();
    	if ($request->isMethod('post')) {
    		
    		
    		$membership	=	new Membership();
    		$membership->setMembtypeCode( $request->getParameter('selMembershipType')  );
    		$membership->setMembshipName( $request->getParameter('txtMembershipInfoDesc'));
    		$membershipService->saveMembership($membership);
    		
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listMembership');
    	}
    	$this->listMembershipType = $membershipService->getMembershipTypeList();
    }
    
	/**
     * update Membership
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateMembership(sfWebRequest $request)
    {
   	 	$membershipService		=	new MembershipService();
    	$membership				=	$membershipService->readMembership($request->getParameter('id'));
    	$this->membership		=	$membership ;
    	if ($request->isMethod('post')) {
    		
    		
    		$membership->setMembtypeCode( $request->getParameter('selMembershipType')  );
    		$membership->setMembshipName( $request->getParameter('txtMembershipInfoDesc'));
    		$membershipService->saveMembership($membership);
    		
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listMembership');
    	}
    	$this->listMembershipType = $membershipService->getMembershipTypeList();
    }
    
	/**
     * Delete Membership Type
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteMembership( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$membershipService	=	new MembershipService();
	    	$membershipService->deleteMembership($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listMembership');
    }
    
   /**
     * List Nationality type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListNationality(sfWebRequest $request)
    {
    	$nationalityService	=	new NationalityService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('nat_code', ListSorter::ASCENDING));
    	
    		if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listNationality	=	$nationalityService->searchNationality($this->searchMode,$this->searchValue);
        		}else
        		{
        			
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listNationality');
        		}
        	}else
        	{
		    	if ($request->getParameter('sort')) {
		            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
		            $this->listNationality	=	$nationalityService->getNationalityList($request->getParameter('sort'),$request->getParameter('order'));
		           
		        }else
		        	$this->listNationality = $nationalityService->getNationalityList();
        	}
    	
    }
    
   /**
     * Save Membership type
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveNationality(sfWebRequest $request)
    {
    	$nationalityService	=	new NationalityService();
    	if ($request->isMethod('post')) {
    		
    		
    		$nationality	=	new Nationality();
    		$nationality->setNatName( $request->getParameter('txtNationalityInfoDesc')  );
    		$nationalityService->saveNationality($nationality);
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listNationality');
    	}
    	
    }
    
	/**
     * Update Nationality
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateNationality(sfWebRequest $request)
    {
    	$nationalityService		=	new NationalityService();
    	$nationality			=	$nationalityService->readNationality($request->getParameter('id'));
    	$this->nationality		=	$nationality ;
    	
    	if ($request->isMethod('post')) {
    		
    		$nationality->setNatName( $request->getParameter('txtNationalityInfoDesc')  );
    		$nationalityService->saveNationality($nationality);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listNationality');
    	}
    }
    
	/**
     * Delete Membership Type
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteNationality( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$nationalityService	=	new NationalityService();
	    	$nationalityService->deleteNationality($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listNationality');
    }
    
    
   /**
     * List Ethnic Race
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListEthnicRace(sfWebRequest $request)
    {
    	$nationalityService	=	new NationalityService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('ethnic_race_code', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listEthnicRace	=	$nationalityService->getEthnicRaceList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listEthnicRace	=	$nationalityService->searchEthnicRace($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listEthnicRace');
        		}
        		
        		
        	}else
        		$this->listEthnicRace = $nationalityService->getEthnicRaceList();
        }
        
    	
    }
    
   /**
     * Save EthnicRace
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveEthnicRace(sfWebRequest $request)
    {
    	$nationalityService	=	new NationalityService();
    	if ($request->isMethod('post')) {
    		
    		
    		$ethnicRace	=	new EthnicRace();
    		$ethnicRace->setEthnicRaceDesc( $request->getParameter('txtEthnicRaceDesc')  );
    		$nationalityService->saveEthnicRace($ethnicRace);
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listEthnicRace');
    	}
    	
    }
    
	/**
     * Update EthnicRace fields
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateEthnicRace(sfWebRequest $request)
    {
    	$nationalityService		=	new NationalityService();
    	$ethnicRace				=	$nationalityService->readEthnicRace($request->getParameter('id'));
    	$this->ethnicRace		=	$ethnicRace ;
    	
    	if ($request->isMethod('post')) {
			
    		$ethnicRace->setEthnicRaceDesc( $request->getParameter('txtEthnicRaceDesc')  );
    		$nationalityService->saveEthnicRace($ethnicRace);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listEthnicRace');
    	}
    }
    
	/**
     * Delete Delete EthnicRace
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteEthnicRace( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$nationalityService	=	new NationalityService();
	    	$nationalityService->deleteEthnicRace($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listEthnicRace');
    }
    
 /**
     * List Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListCustomer(sfWebRequest $request)
    {
    	$customerService	=	new CustomerService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('customer_id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listCustomer	=	$customerService->getCustomerList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listCustomer		=	$customerService->searchCustomer($this->searchMode,$this->searchValue);
        		}else
        		{
        			
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listCustomer');
        		}
        		
        		
        	}else
        		$this->listCustomer 	= $customerService->getCustomerList();
        }
        
    	
    }
    
   /**
     * Save Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveCustomer(sfWebRequest $request)
    {
    	$customerService	=	new CustomerService();
    	if ($request->isMethod('post')) {
    		
    		
    		$customer	=	new Customer();
    		$customer->setName( $request->getParameter('txtName'));
    		$customer->setDescription( $request->getParameter('txtDescription'));
    		$customerService->saveCustomer($customer);
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listCustomer');
    	}
    	
    }
    
	/**
     * Save Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateCustomer(sfWebRequest $request)
    {
    	$customerService	=	new CustomerService();
    	$customer			=	$customerService->readCustomer($request->getParameter('id'));
    	$this->customer		=	$customer ;
    	if ($request->isMethod('post')) {
    		
    		$customer->setName( $request->getParameter('txtName'));
    		$customer->setDescription( $request->getParameter('txtDescription'));
    		$customerService->saveCustomer($customer);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listCustomer');
    	}
    }
    
	/**
     * Delete Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteCustomer( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$customerService	=	new CustomerService();
	    	$customerService->deleteCustomer($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    	{
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    	}
    	$this->redirect('admin/listCustomer');
    }
    
 /**
     * List Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListProject(sfWebRequest $request)
    {
    	$projectService	=	new ProjectService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('customer_id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listProject	=	$projectService->getProjectList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listProject		=	$projectService->searchProject($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listProject');
        		}
        		
        		
        	}else
        		$this->listProject 	= $projectService->getProjectList();
        }
        
    	
    }
    
   /**
     * Save Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveProject(sfWebRequest $request)
    {
    	$projectService	=	new ProjectService();
    	if ($request->isMethod('post')) {
    		
    		
    		$project	=	new Project();
    		$project->setCustomerId( $request->getParameter('cmbCustomerId') ); 
    		$project->setName( $request->getParameter('txtName') );
    		$project->setDescription( $request->getParameter('txtDescription') );
    		
    		$projectService->saveProject($project);
    		$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listProject');
    	}
    	$customerService	=	new CustomerService();
    	$this->listCustomer 	= $customerService->getCustomerList();
    }
    
	/**
     * Update Project
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateProject(sfWebRequest $request)
    {
    	$projectService	=	new ProjectService();
    	
   	 	$project			=	$projectService->readProject($request->getParameter('id'));
    	$this->project		=	$project ;
    	
    	if ($request->isMethod('post')) {
    		
    		
    		$project->setCustomerId( $request->getParameter('cmbCustomerId') ); 
    		$project->setName( $request->getParameter('txtName') );
    		$project->setDescription( $request->getParameter('txtDescription') );
    		
    		$projectService->saveProject($project);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listProject');
    	}
    	$companyService		=	new CompanyService();
    	$customerService	=	new CustomerService();
    	$this->listCustomer = $customerService->getCustomerList();
    	$this->projectAdmins	=	$projectService->getProjectAdmin( $project );
    	
    	$this->empJson			=	$companyService->getEmployeeListAsJson();
    
    	
    }
    
	/**
     * Delete Customer
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteProject( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$projectService	=	new ProjectService();
	    	$projectService->deleteProject($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    		$this->redirect('admin/listProject');
    }
    
    /**
     * Save Project Admin
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeSaveProjectAdmin( sfWebRequest $request )
    {
    	$projectId	=	$request->getParameter('projectId');
    	$empId		=	$request->getParameter('txtEmpId');
    	$projectService	=	new ProjectService();
    	$projectService->saveProjectAdmin($projectId,$empId);
    	$this->setMessage('SUCCESS',array('Successfully Added'));
    	$this->redirect('admin/updateProject?id='.$projectId);
    	
    }
    
	/**
     * Delete Project Admin
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteProjectAdmin( sfWebRequest $request )
    {
    	$projectId	=	$request->getParameter('projectId');
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	
    		$projectService	=	new ProjectService();
	    	$projectService->deleteProjectAdmin($projectId , $request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		$this->redirect('admin/updateProject?id='.$projectId);
    	
    }
    
    /**
     * List Project Activities
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListProjectActivity( sfWebRequest $request )
    {
    	$projectService			=	new ProjectService();
    	$this->listProject 		= 	$projectService->getProjectList();
    	$currentProjectId		=	isset( $_POST['id'])?$_POST['id']:$request->getParameter('id');
    	$this->currentProject	=	$currentProjectId;
    	
    	$projectActivityList		=	$projectService->getProjectActivity($currentProjectId);
    	if(count($projectActivityList)>0)
    	{
    		$this->projectActivityList	=	$projectActivityList ;
    		$this->hasProjectActivity	=	true;
    	}else
    		$this->hasProjectActivity	=	false;
    		
    }
    
    /**
     * Save Project Activity
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeSaveProjectActivity( sfWebRequest $request )
    {
    	$projectId	=	$request->getParameter('id');
    	
    	if( $projectId != '')
    	{
	    	$activity	=	$request->getParameter('activityName');
	    	$projectService	=	new ProjectService();
	    	$projectService->saveProjectActivity($projectId,$activity);
	    	$this->setMessage('SUCCESS',array('Successfully Added'));
    	}else
    		$this->setMessage('NOTICE',array('Select Project'));
    		
    	$this->redirect('admin/listProjectActivity?id='.$projectId);
    	
    }
    
	/**
     * Delete Project Admin
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteProjectActivity( sfWebRequest $request )
    {
    	$projectId	=	$request->getParameter('id');
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	
    		$projectService	=	new ProjectService();
	    	$projectService->deleteProjectActivity( $request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    		$this->redirect('admin/listProjectActivity?id='.$projectId);
    	
    }
    
 	/**
     * List Custom fields
     * @param sfWebRequest $request
     * @return void
     */
    public function executeListCustomFields(sfWebRequest $request)
    {
    	$customFieldsService	=	new CustomFieldsService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('field_num', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listCustomField 	=	$customFieldsService->getCustomFieldList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	
        		$this->listCustomField 	= $customFieldsService->getCustomFieldList();
        }
        
    	
    }
    
   /**
     * Save Custom fields
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveCustomFields(sfWebRequest $request)
    {
    	$customFieldsService	=	new CustomFieldsService();
    	if ($request->isMethod('post')) {
    		
    		
    		$customFields	=	new CustomFields();
    		$customFields->setFieldNum( $request->getParameter('txtId') ); 
    		$customFields->setName( $request->getParameter('txtName') ); 
    		$customFields->setType( $request->getParameter('cmbType') ); 
    		$customFields->setExtraData( $request->getParameter('txtExtra') ); 
    		
    		$customFieldsService->saveCustomField($customFields);
    		
			$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listCustomFields');
    	}
    	
    	$this->avaliableIds 	= $customFieldsService->getAvaliableFieldNumbers();
    }
    
	/**
     * Update Customer fields
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateCustomFields(sfWebRequest $request)
    {
    	$customFieldsService	=	new CustomFieldsService();
    	$customFields			=	$customFieldsService->readCustomField($request->getParameter('id'));
    	$this->customFields		=	$customFields ;
    	if ($request->isMethod('post')) {
    		
    		
    		$customFields->setName( $request->getParameter('txtName') ); 
    		$customFields->setType( $request->getParameter('cmbType') ); 
    		$customFields->setExtraData( $request->getParameter('txtExtra') ); 
			$customFieldsService->saveCustomField($customFields);
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listCustomFields');
    	}
    }
    
	/**
     * Delete Custom fields
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteCustomFields( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$customFieldsService	=	new CustomFieldsService();
	    	$customFieldsService->deleteCustomField($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listCustomFields');
    }
    
    
 	/**
     * List UserGroup
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListUserGroup(sfWebRequest $request)
    {
    	$userService	=	new UserService();
    	
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('userg_id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listUserGroup 	= $userService->getUserGroupList($request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listUserGroup	=	$userService->searchUserGroup($this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listUserGroup');
        		}
        		
        		
        	}else
        		$this->listUserGroup 	= $userService->getUserGroupList();
        }
        
    	
    }
    
   /**
     * Save User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveUserGroup(sfWebRequest $request)
    {
    	$userService	=	new UserService();
    	if ($request->isMethod('post')) {
    		
    		$userGroup		=	new UserGroup();
    		$userGroup->setUsergName(  $request->getParameter('txtUserGroupName') );
    		$userService->saveUserGroup( $userGroup );
    		
			$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listUserGroup');
    	}
    	
    	
    }
    
	/**
     * Update User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateUserGroup(sfWebRequest $request)
    {
    	$userService		=	new UserService();
    	$userGroup			=	$userService->readUserGroup($request->getParameter('id'));
    	$this->userGroup		=	$userGroup ;
    	if ($request->isMethod('post')) {
    		
    		$userGroup->setUsergName(  $request->getParameter('txtUserGroupName') );
    		$userService->saveUserGroup( $userGroup );
    		$this->setMessage('SUCCESS',array('Successfully Updated'));
    		$this->redirect('admin/listUserGroup');
    	}
    }
    
	/**
     * Delete User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteUserGroup( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$userService		=	new UserService();
	    	$userService->deleteUserGroup($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listUserGroup');
    }
    
    /**
     * List User group rights
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListUserGroupRight( sfWebRequest $request )
    {
    	$userService		=	new UserService();
    	$userGroup			=	$userService->readUserGroup($request->getParameter('id'));
    	$this->userGroup	=	$userGroup ;
    	$this->moduleList	=	$userService->getModuleList($userGroup);
    	$this->moduleRights	=	$userService->getUserGroupModelRights($userGroup);
    }
    
	/**
     * Save User group rights
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveUserGroupRight( sfWebRequest $request )
    {
    	$userService		=	new UserService();
    	$userGroup			=	$userService->readUserGroup($request->getParameter('id'));
    	
    	$moduleRights		=	 new ModuleRights();
    	$moduleRights->setUsergId($request->getParameter('id'));
    	$moduleRights->setModId($request->getParameter('cmbModuleID'));
    	$moduleRights->setAddition($request->getParameter('chkAdd'));
    	$moduleRights->setEditing($request->getParameter('chkEdit'));
    	$moduleRights->setDeletion($request->getParameter('chkDelete'));
    	$moduleRights->setViewing($request->getParameter('chkView'));
    	$userService->saveUserGroupModelRights($moduleRights);
    	
    	$this->redirect('admin/listUserGroupRight?id='.$userGroup->getUsergId());	
    }
    
   /**
     * Delete User group rights
     * @param sfWebRequest $request
     * @return unknown_type
     */
    
    public function executeDeleteUserGroupRight( sfWebRequest $request )
    {
    	$userService		=	new UserService();
    	$userGroup			=	$userService->readUserGroup($request->getParameter('id'));
    	$userService->deleteUserGroupModelRights( $userGroup );
    	
    	$this->redirect('admin/listUserGroupRight?id='.$userGroup->getUsergId());	
    }
    
 	/**
     * List UserGroup
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListUser(sfWebRequest $request)
    {
    	
    	$userService	=	new UserService();
    	$this->sorter 		= 	new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('id', ListSorter::ASCENDING));
    	
    	if ($request->getParameter('sort')) {
            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
            $this->listUser 	= $userService->getUsersList($request->getParameter('isAdmin'),$request->getParameter('sort'),$request->getParameter('order'));
           
        }else
        {
        	if($request->getParameter('mode')=='search')
        	{
        		if( $request->getParameter('searchMode') !='all' && $request->getParameter('searchValue') !='')
        		{
	        		$this->searchMode		=	$request->getParameter('searchMode');
	        		$this->searchValue		=	$request->getParameter('searchValue');
	        		$this->listUser			=	$userService->searchUsers($request->getParameter('isAdmin'),$this->searchMode,$this->searchValue);
        		}else
        		{
        			$this->setMessage('NOTICE',array('Select the field to search'));
        			$this->redirect('admin/listUser');
        		}
        		
        		
        	}else
        		$this->listUser 	= $userService->getUsersList($request->getParameter('isAdmin'));
        }
        
    	$this->userType	=	$request->getParameter('isAdmin') ;
    }
    
   /**
     * Save User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeSaveUser(sfWebRequest $request)
    {
    	$userService	=	new UserService();
    	$companyService	=	new CompanyService();
    	if ($request->isMethod('post')) {
    		
    		
    		$user	=	new Users();
    		$user->setIsAdmin( $request->getParameter('isAdmin') );
    		$user->setUserName( $request->getParameter('txtUserName') );
    		$user->setUserPassword( $request->getParameter('txtUserPassword') );
    		$user->setUsergId( $request->getParameter('cmbUserGroupID') );
    		$user->setStatus( $request->getParameter('cmbUserStatus') );
    		$user->setEmpNumber( $request->getParameter('txtEmpId') );
    		$userService->saveUser( $user );
    		
			$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listUser?isAdmin='.$request->getParameter('isAdmin'));
    	}
    	
    	$this->userType			=	$request->getParameter('isAdmin') ;
    	$this->listUserGroup 	= 	$userService->getUserGroupList();
    	$this->empJson			=	$companyService->getEmployeeListAsJson();
    }
    
	/**
     * Update User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateUser(sfWebRequest $request)
    {
    	$companyService	=	new CompanyService();
    	$userService	=	new UserService();
    	$user			=	$userService->readUser($request->getParameter('id'));
    	$this->user		=	$user ;
    	if ($request->isMethod('post')) 
    	{
    		
    		$user->setIsAdmin( $request->getParameter('isAdmin') );
    		$user->setUserName( $request->getParameter('txtUserName') );
    		$user->setUserPassword( $request->getParameter('txtUserPassword') );
    		$user->setUsergId( $request->getParameter('cmbUserGroupID') );
    		$user->setStatus( $request->getParameter('cmbUserStatus') );
    		$user->setEmpNumber( $request->getParameter('txtEmpId') );
    		$userService->saveUser( $user );
    		
			$this->setMessage('SUCCESS',array('Successfully Added'));
    		$this->redirect('admin/listUser?isAdmin='.$request->getParameter('isAdmin'));
    	}
    	
    	$this->userType			=	$request->getParameter('isAdmin') ;
    	$this->listUserGroup 	= 	$userService->getUserGroupList();
    	$this->empJson			=	$companyService->getEmployeeListAsJson();
    	
    }
    
	/**
     * Delete User Group
     * @param sfWebRequest $request
     * @return unknown_type
     */
	public function executeDeleteUser( sfWebRequest $request )
    {
    	if( count($request->getParameter('chkLocID')) > 0)
    	{
	    	$userService		=	new UserService();
	    	$userService->deleteUser($request->getParameter('chkLocID'));	
	    	$this->setMessage('SUCCESS',array('Successfully Deleted'));
	    	
    	}else
    		$this->setMessage('NOTICE',array('Select at least one record to delete'));
    		
    	$this->redirect('admin/listUser?isAdmin='.$request->getParameter('isAdmin'));
    }
    
    
	/**
	 * List Mail Configuration
	 * @param sfWebRequest $request
	 * @return unknown_type
	 */
	public function executeListMailConfiguration(sfWebRequest $request) {

        $emailConfiguration = new EmailConfiguration();
		 
		$this->mailAddress = $emailConfiguration->getSentAs();
		$this->sendMailPath = $emailConfiguration->getSendMailPath();
		$this->smtpAuth = $emailConfiguration->getSmtpAuthType();
		$this->smtpSecurity = $emailConfiguration->getSmtpSecurityType();
		$this->smtpHost = $emailConfiguration->getSmtpHost();
		$this->smtpPort = $emailConfiguration->getSmtpPort();
		$this->smtpUser = $emailConfiguration->getSmtpUsername();
		$this->smtpPass = $emailConfiguration->getSmtpPassword();
		$this->emailType = $emailConfiguration->getMailType();

		if ($this->getUser()->hasFlash('templateMessage')) {
			$this->templateMessage = $this->getUser()->getFlash('templateMessage');
		}
                
	}

	/**
	 * List Mail Subscriptions
	 * @param sfWebRequest $request
	 * @return unknown_type
	 */
	public function executeListMailSubscriptions(sfWebRequest $request) {
		$this->form = new EmailSubscriptionsForm(array(), array(), true);

		$mailService 			= 	new MailService();
		$user					=	$_SESSION['user'] ;
        
        $this->mailnot  = array();
        
        for($i=-1;$i<9;$i++) {
            $this->mailnot[$i] = '';
        }
        
        if ($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()){

				$mailService->removeMailNotification($user);
				foreach( $request->getParameter('notificationMessageStatus') as $notificationTypeId) {
					$mailNotification	=	new MailNotification();
                    
                    $notficationEmail       =   trim($request->getParameter('txtMailAddress_'.$notificationTypeId));
                    
					$mailNotification->setUserId( $user );
					$mailNotification->setNotificationTypeId( $notificationTypeId );
					$mailNotification->setStatus( 1 );
                    $mailNotification->setEmail($notficationEmail);
					$mailService->saveMailNotification( $mailNotification );
				}

			}
		}

		$this->notficationList	=	$mailService->getMailNotificationList($user);

        //$notficationFullList = $mailService->getMailNotificationFullList();

        $AllMailNotifications = $mailService->getAllMailNotifications();
        
        foreach($AllMailNotifications as $mailNotification) {
            $this->mailnot[$mailNotification->notification_type_id] = $mailNotification->email;
        }
        
//        if (!empty($notficationFullList)) {
//           $this->notficationEmail = $notficationFullList[0]->getEmail();
//        } else {
//            $this->notficationEmail = '';
//        }

	}

    public function executeSaveMailConfiguration(sfWebRequest $request) {

        $this->form = new EmailConfigurationForm(array(), array(), true);
	$this->form->bind($request->getParameter($this->form->getName()));

        $emailConfiguration = $this->form->populateEmailConfiguration($request);
        $emailConfiguration->save();

        if ($request->getParameter('chkSendTestEmail')) {

            $emailService = new EmailService();
            $result = $emailService->sendTestEmail($request->getParameter('txtTestEmail'));
            
            if ($result) {
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', 'Email configuration was saved. Test email was sent.'));
            } else {
                $this->getUser()->setFlash('templateMessage', array('WARNING', "Email configuration was saved. Test email couldn't be sent."));
            }

        }

        $this->redirect('admin/listMailConfiguration');

    }
    
	/**
	 * Get JobSpecification for given jobTitle
	 *
	 * @param sfWebRequest $request
	 * @return JSON formatted JobSpec object
	 */
	public function executeGetJobSpecJson(sfWebRequest $request) {
		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);

		$jobSpec = array();

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type','application/json; charset=utf-8');
		}

		$jobId = $request->getParameter('job');
		 
		if (!empty($jobId)) {
			$jobService = new JobService();
			$jobSpec = $jobService->getJobSpecForJob($jobId, true);
		}

		return $this->renderText(json_encode($jobSpec));
	}

	/**
	 * Get employee statuses for given jobTitle
	 *
	 * @param sfWebRequest $request
	 * @return JSON formatted JobSpec object
	 */
	public function executeGetEmpStatusesJson(sfWebRequest $request) {
		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);

		$empStatuses = array();

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type','application/json; charset=utf-8');
		}

		$jobId = $request->getParameter('job');

		if (!empty($jobId)) {
			$jobService = new JobService();
			$empStatuses = $jobService->getEmployeeStatusForJob($jobId, true);
		}

		return $this->renderText(json_encode($empStatuses));
	}    
    
    
    
    
    

    
    /**
     * Set message 
     */
    public function setMessage( $messageType , $message = array())
    {
    	$this->getUser()->setFlash('messageType', $messageType);
    	$this->getUser()->setFlash('message', $message);
    }
}