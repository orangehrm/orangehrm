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

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiHelpAPITest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Request
     */
    private $request = null;

    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $this->request = new Request($sfRequest);
    }

    public function testGetHelpConfigurationWithCategoryAndQuery()
    {
        $articleResult = array(
            array(
                "name"=> "Admin User Guide",
                "url"=> "https://opensourcehelp.orangehrm.com/hc/en-us/categories/360002945799-Admin-User-Guide"
            ),
            array(
                "name"=> "Employee User Guide",
                "url"=> "https://opensourcehelp.orangehrm.com/hc/en-us/categories/360002926580-Employee-User-Guide"
            )
        );
        $helpService = $this->getMockBuilder('HelpService')
            ->setMethods(['getDefaultRedirectUrl','getCategoriesFromSearchQuery','getRedirectUrlList'])
            ->getMock();
        $helpService->expects($this->once())
            ->method('getDefaultRedirectUrl')
            ->will($this->returnValue('https://opensourcehelp.orangehrm.com/hc/en-us'));
        $helpService->expects($this->once())
            ->method('getCategoriesFromSearchQuery')
            ->with('Admin')
            ->will($this->returnValue($articleResult));
        $params = ['query' => "Admin",'mode'=>'category'];
        $helpAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Help\HelpConfigurationAPI')
            ->setMethods(['getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $helpAPI->setHelpService($helpService);
        $helpAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $actual=$helpAPI->getHelpConfiguration();
        $expected= new Response(
            array(
                'defaultRedirectUrl'=>'https://opensourcehelp.orangehrm.com/hc/en-us',
                'redirectUrls'=> $articleResult
                ));
        $this->assertEquals($expected,$actual);
    }

    public function testGetHelpConfigurationWithArticlesInCategoryWithQuery()
    {
        $articleResult = array(
            array(
                "name"=> "How to Approve Leave by Admin or Supervisor",
                "url"=> "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018659479-How-to-Approve-Leave-by-Admin-or-Supervisor"            ),
            array(
                "name"=> "How to Create Dynamic Reports",
                "url"=> "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018591300-How-to-Create-Dynamic-Reports"            )
        );
        $helpService = $this->getMockBuilder('HelpService')
            ->setMethods(['getDefaultRedirectUrl','getCategoriesFromSearchQuery','getRedirectUrlList'])
            ->getMock();
        $helpService->expects($this->once())
            ->method('getDefaultRedirectUrl')
            ->will($this->returnValue('https://opensourcehelp.orangehrm.com/hc/en-us'));
        $helpService->expects($this->once())
            ->method('getRedirectUrlList')
            ->with('Admin')
            ->will($this->returnValue($articleResult));
        $params = ['query' => "Admin",'categories'=>array('360002945799')];
        $helpAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\Help\HelpConfigurationAPI')
            ->setMethods(['getParameters'])
            ->setConstructorArgs([$this->request])
            ->getMock();
        $helpAPI->setHelpService($helpService);
        $helpAPI->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue($params));
        $actual=$helpAPI->getHelpConfiguration();
        $expected= new Response(
            array(
                'defaultRedirectUrl'=>'https://opensourcehelp.orangehrm.com/hc/en-us',
                'redirectUrls'=> $articleResult
            ));
        $this->assertEquals($expected,$actual);
    }
}
