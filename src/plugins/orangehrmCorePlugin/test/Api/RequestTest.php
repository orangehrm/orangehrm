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

namespace OrangeHRM\Tests\Core\Api;

use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Framework\Http\Request as HttpRequest;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group APIv2
 */
class RequestTest extends TestCase
{
    public function testRequest()
    {
        $httpRequest = new HttpRequest();
        $request = new Request($httpRequest);
        $this->assertEmpty($request->getAllParameters());

        $httpRequest = new HttpRequest(['query' => 'param']);
        $request = new Request($httpRequest);
        $this->assertTrue($request->getQuery() instanceof ParameterBag);
        $this->assertEquals('param', $request->getQuery()->get('query'));
        $this->assertEquals(['query' => 'param'], $request->getAllParameters());

        $httpRequest = new HttpRequest([], ['body' => 'param']);
        $request = new Request($httpRequest);
        $this->assertTrue($request->getBody() instanceof ParameterBag);
        $this->assertEquals('param', $request->getBody()->get('body'));
        $this->assertEquals(['body' => 'param'], $request->getAllParameters());

        $httpRequest = new HttpRequest([], [], ['attribute' => 'param']);
        $request = new Request($httpRequest);
        $this->assertTrue($request->getAttributes() instanceof ParameterBag);
        $this->assertEquals('param', $request->getAttributes()->get('attribute'));
        $this->assertEquals(['attribute' => 'param'], $request->getAllParameters());
    }
}
