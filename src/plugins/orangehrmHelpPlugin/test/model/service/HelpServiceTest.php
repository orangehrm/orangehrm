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
 * Test class of Api/EmployeeService
 *
 * @group
 */


use Orangehrm\Rest\Api\Pim\Entity\Employee;

//use Orangehrm\Help\

class HelpServiceTest extends PHPUnit_Framework_TestCase
{
    private $helpService;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->helpService = new HelpService();
    }

    public function testGetHelpProcessorClass()
    {
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getHelpProcessorClass'])
            ->getMock();
        $helpConfigService
            ->method('getHelpProcessorClass')
            ->will($this->returnValue('ZendeskHelpProcessor'));
        $this->helpService->setHelpConfigService($helpConfigService);
        $this->assertTrue($this->helpService->getHelpProcessorClass() instanceof ZendeskHelpProcessor);
    }

    public function testSetHelpProcessorClass()
    {
        $helpService = new HelpService();
        $helpService->setHelpProcessorClass(new ZendeskHelpProcessor());
        $this->assertTrue($helpService->getHelpProcessorClass() instanceof HelpProcessor);
    }

    public function testGetHelpConfigService()
    {
        $this->assertTrue($this->helpService->getHelpConfigService() instanceof HelpConfigService);
    }

    public function testSetHelpConfigService()
    {
        $helpService = new HelpService();
        $helpService->setHelpConfigService(new HelpConfigService());
        $this->assertTrue($helpService->getHelpConfigService() instanceof HelpConfigService);
    }

    public function testGetDefaultRedirectUrl()
    {
        $articleResult = array(
            array(
                "name" => "Admin User Guide",
                "url" => "https://opensourcehelp.orangehrm.com/hc/en-us/categories/360002945799-Admin-User-Guide"
            ),
            array(
                "name" => "Employee User Guide",
                "url" => "https://opensourcehelp.orangehrm.com/hc/en-us/categories/360002926580-Employee-User-Guide"
            )
        );
        $helpProcessor = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(['getCategoriesFromSearchQuery'])
            ->getMock();
        $helpProcessor->expects($this->once())
            ->method('getCategoriesFromSearchQuery')
            ->will($this->returnValue($articleResult));
        $this->helpService->setHelpProcessorClass($helpProcessor);
        $expected = $articleResult;
        $actual = $this->helpService->getCategoriesFromSearchQuery();
        $this->assertEquals($expected, $actual);
    }

    public function testGetRedirectUrl()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpProcessor = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(['getRedirectUrl'])
            ->getMock();
        $helpProcessor->expects($this->once())
            ->method('getRedirectUrl')
            ->will($this->returnValue($baseUrl . '/api/v2/help_center/articles/employees'));
        $this->helpService->setHelpProcessorClass($helpProcessor);
        $expected = $baseUrl . '/api/v2/help_center/articles/employees';
        $actual = $this->helpService->getRedirectUrl('employees');
        $this->assertEquals($expected, $actual);
    }
}
