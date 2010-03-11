<?php
require_once 'PHPUnit/Framework.php';



class CompanyServiceTest extends PHPUnit_Framework_TestCase
{
	
	
	/**
	 * Set up method
	 * @return unknown_type
	 */
	protected function setUp()
    {
       
    }
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function testGetEmployeeCount()
	{
		 $companyService	=	new CompanyService();
		 $empList			=	$companyService->getEmployeeList();
		 $this->assertEquals(0, count($empList));
	}
	
	/**
	 * Save Company
	 * @return unknown_type
	 */
    public function testSaveCompany()
    {
       $company	=	new Company();
       $company->setComapanyName('OrangeHrm inc');
       
       $companyService	=	new CompanyService();
       $companyService->saveCompany($company);
    }    
}