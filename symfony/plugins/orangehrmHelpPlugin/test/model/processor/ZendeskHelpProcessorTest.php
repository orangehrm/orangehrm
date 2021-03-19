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
class ZendeskHelpProcessorTest extends PHPUnit_Framework_TestCase
{
    private $helpProcessor;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->helpProcessor = new ZendeskHelpProcessor();
    }

    public function testGetSearchUrlFromQueryOnlyWithSearchQuery()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?query=Admin';
        $actual = $this->helpProcessor->getSearchUrlFromQuery('Admin');
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryOnlyWithLabels()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?label_names=add_employee,apply_leave';
        $actual = $this->helpProcessor->getSearchUrlFromQuery(null, ['add_employee', 'apply_leave']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryOnlyWithOneLabel()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?label_names=add_employee';
        $actual = $this->helpProcessor->getSearchUrlFromQuery(null, ['add_employee']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryOnlyWithCategories()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?category=0123456,987654321';
        $actual = $this->helpProcessor->getSearchUrlFromQuery(null, [], ['0123456', '987654321']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryOnlyWithOneCategory()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?category=0123456';
        $actual = $this->helpProcessor->getSearchUrlFromQuery(null, [], ['0123456']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryWithQueryOnlyWithOneCategory()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?query=Admin&category=0123456';
        $actual = $this->helpProcessor->getSearchUrlFromQuery('Admin', [], ['0123456']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetSearchUrlFromQueryWithQueryOnlyWithLabels()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $expected = $baseUrl . '/api/v2/help_center/articles/search.json?query=Admin&label_names=add_employee,apply_leave';
        $actual = $this->helpProcessor->getSearchUrlFromQuery('Admin', ['add_employee', 'apply_leave']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetRedirectUrlOneResult()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $results = array();
        $results['count'] = 1;
        $results['results'] = array(
            array(
                'html_url' => $baseUrl . '/api/v2/help_center/articles/employees'
            )
        );
        $response = array(
            'response' => json_encode($results),
            'responseCode' => 200
        );
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $this->helpProcessor->setHelpConfigService($helpConfigService);

        $mockHelpProcessorClass = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(array('sendQuery'))
            ->getMock();
        $mockHelpProcessorClass->expects($this->once())
            ->method('sendQuery')
            ->with($baseUrl . '/api/v2/help_center/articles/search.json?label_names=employees')
            ->will($this->returnValue($response));
        $mockHelpProcessorClass->setHelpConfigService($helpConfigService);
        $this->assertEquals(
            $baseUrl . '/api/v2/help_center/articles/employees',
            $mockHelpProcessorClass->getRedirectUrl('employees')
        );
    }

    public function testGetRedirectUrlNoResult()
    {
        $results = array();
        $results['count'] = 0;
        $results['results'] = array();
        $response = array(
            'response' => json_encode($results),
            'responseCode' => 200
        );
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $mockHelpProcessorClass = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(array('sendQuery'))
            ->getMock();
        $mockHelpProcessorClass->expects($this->once())
            ->method('sendQuery')
            ->with($baseUrl . '/api/v2/help_center/articles/search.json?label_names=employees')
            ->will($this->returnValue($response));
        $mockHelpProcessorClass->setHelpConfigService($helpConfigService);
        $this->assertEquals(
            $baseUrl . '/hc/en-us',
            $mockHelpProcessorClass->getRedirectUrl('employees')
        );
    }

    public function testGetRedirectUrlManyResults()
    {
        $results = array();
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));

        $results['count'] = 3;
        $results['results'] = array(
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/admins'
            ),
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/supervisors'
            ),
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/employees'
            )
        );
        $response = array(
            'response' => json_encode($results),
            'responseCode' => 200
        );
        $mockHelpProcessorClass = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(array('sendQuery'))
            ->getMock();
        $mockHelpProcessorClass->setHelpConfigService($helpConfigService);
        $mockHelpProcessorClass->expects($this->once())
            ->method('sendQuery')
            ->with($baseUrl . '/api/v2/help_center/articles/search.json?label_names=employees')
            ->will($this->returnValue($response));

        $this->assertEquals(
            'https://orangehrm.zendesk.com/api/v2/help_center/articles/admins',
            $mockHelpProcessorClass->getRedirectUrl('employees')
        );
    }

    public function testGetHelpConfigService()
    {
        $this->assertTrue($this->helpProcessor->getHelpConfigService() instanceof HelpConfigService);
    }

    public function testSetHelpConfigService()
    {
        $processor = new ZendeskHelpProcessor();
        $processor->setHelpConfigService(new HelpConfigService());
        $this->assertTrue($processor->getHelpConfigService() instanceof HelpConfigService);
    }

    public function testGetBaseUrl()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $this->assertEquals($baseUrl, $this->helpProcessor->getBaseUrl());
    }

    public function testGetSearchUrl()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $this->assertEquals(
            $baseUrl . '/api/v2/help_center/articles/search.json?label_names=employees',
            $this->helpProcessor->getSearchUrl('employees')
        );
    }

    public function testGetDefaultRedirectUrl()
    {
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $this->helpProcessor->setHelpConfigService($helpConfigService);
        $this->assertEquals(
            $baseUrl . '/hc/en-us',
            $this->helpProcessor->getDefaultRedirectUrl()
        );
    }

    public function testGetRedirectUrlList()
    {
        $results = array();
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));;
        $results['count'] = 3;
        $results['results'] = array(
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/admins',
                'name' => 'Admin'
            ),
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/supervisors',
                'name' => 'Supervisors'
            ),
            array(
                'html_url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/employees',
                'name' => 'Employees'
            )
        );
        $response = array(
            'response' => json_encode($results),
            'responseCode' => 200
        );
        $mockHelpProcessorClass = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(array('sendQuery'))
            ->getMock();
        $mockHelpProcessorClass->setHelpConfigService($helpConfigService);
        $mockHelpProcessorClass->expects($this->once())
            ->method('sendQuery')
            ->with($baseUrl . '/api/v2/help_center/articles/search.json?query=employees')
            ->will($this->returnValue($response));
        $expected = array(
            array(
                'url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/admins',
                'name' => 'Admin'
            ),
            array(
                'url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/supervisors',
                'name' => 'Supervisors'
            ),
            array(
                'url' => 'https://orangehrm.zendesk.com/api/v2/help_center/articles/employees',
                'name' => 'Employees'
            )
        );
        $this->assertEquals(
            $expected,
            $mockHelpProcessorClass->getRedirectUrlList('employees')
        );
    }

    public function testGetCategoryRedirectUrl()
    {
        $results = array();
        $baseUrl = 'https://opensourcehelp.orangehrm.com';
        $results['category'] = array(
            'html_url' => $baseUrl . '/api/v2/help_center/categories/123456'
        );

        $response = array(
            'response' => json_encode($results),
            'responseCode' => 200
        );
        $helpConfigService = $this->getMockBuilder('HelpConfigService')
            ->setMethods(['getBaseHelpUrl'])
            ->getMock();
        $helpConfigService
            ->method('getBaseHelpUrl')
            ->will($this->returnValue($baseUrl));
        $mockHelpProcessorClass = $this->getMockBuilder('ZendeskHelpProcessor')
            ->setMethods(array('sendQuery'))
            ->getMock();
        $mockHelpProcessorClass->setHelpConfigService($helpConfigService);
        $mockHelpProcessorClass->expects($this->once())
            ->method('sendQuery')
            ->with($baseUrl . '/api/v2/help_center/categories/123456')
            ->will($this->returnValue($response));

        $this->assertEquals(
            $baseUrl . '/api/v2/help_center/categories/123456',
            $mockHelpProcessorClass->getCategoryRedirectUrl('123456')
        );
    }

}
