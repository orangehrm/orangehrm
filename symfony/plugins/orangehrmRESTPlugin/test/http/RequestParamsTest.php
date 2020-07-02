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
use Orangehrm\Rest\Http\RequestParams;

/**
 * @group API
 */
class RequestParamsTest extends PHPUnit\Framework\TestCase
{

    public function testGetQueryParam()
    {
        $_GET['id'] = 1;
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $this->assertEquals(1, $requestParam->getQueryParam('id'));
    }

    public function testSetParam()
    {
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $this->assertTrue(is_null($requestParam->getQueryParam('test')));
        $requestParam->setParam('test', 'TestValue');
        $this->assertEquals('TestValue', $requestParam->getQueryParam('test'));
    }

    public function testGetPostParams()
    {
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $this->assertTrue(empty($requestParam->getPostParams()));

        $_POST['id'] = 1;
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $this->assertEquals(['id' => 1], $requestParam->getPostParams());
    }

    public function testSetPostParams()
    {
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $this->assertTrue(empty($requestParam->getPostParams()));

        $requestParam->setPostParam('test', 'TestValue');
        $this->assertEquals(['test' => 'TestValue'], $requestParam->getPostParams());

        $_POST['id'] = 1;
        $request = new Request($this->getSfRequest());
        $requestParam = new RequestParams($request);
        $requestParam->setPostParam('id', 2);
        $this->assertEquals(['id' => 2], $requestParam->getPostParams());
    }

    private function getSfRequest()
    {
        $sfEvent = new sfEventDispatcher();
        return new sfWebRequest($sfEvent);
    }
}
