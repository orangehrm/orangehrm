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

namespace OrangeHRM\Tests\OAuth\Service;

use Nyholm\Psr7\Factory\Psr17Factory;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Http\BinaryFileResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\OAuth\Service\PsrHttpFactoryHelper;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group OAuth
 * @group Service
 */
class PsrHttpFactoryHelperTest extends TestCase
{
    public function testCreatePsr7Request(): void
    {
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            ['nameOrId' => 'Abbey'],
            [],
            [],
        );
        $psr7Request = $psrHttpFactory->createPsr7Request($request);
        $this->assertEquals('/api/v2/pim/employees', $psr7Request->getUri()->getPath());
        $this->assertEquals('GET', $psr7Request->getMethod());
        $this->assertEquals(['nameOrId' => 'Abbey'], $psr7Request->getQueryParams());

        // POST request with headers
        $request = Request::create(
            '/api/v2/pim/employees',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
                'HTTP_AUTHORIZATION' => 'Bearer qwertyui'
            ],
            'name=Abbey&ID=0001'
        );

        $psr7Request = $psrHttpFactory->createPsr7Request($request);
        $this->assertEquals('/api/v2/pim/employees', $psr7Request->getUri()->getPath());
        $this->assertEquals('POST', $psr7Request->getMethod());
        $this->assertEquals('Bearer qwertyui', $psr7Request->getHeader('authorization')[0]);
        $this->assertEquals('name=Abbey&ID=0001', $psr7Request->getBody()->getContents());
    }

    public function testCreatePsr7Response(): void
    {
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $response = new Response();
        $response->setStatusCode(201);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent('{"name": "Abbey"}');

        $psr7Response = $psrHttpFactory->createPsr7Response($response);
        $this->assertEquals(201, $psr7Response->getStatusCode());
        $this->assertEquals('', $psr7Response->getBody()->getContents()); // content not moved to PSR7 response
        $this->assertEquals('application/json', $psr7Response->getHeader('content-type')[0]);

        // Test with Binary response
        $file = Config::get(Config::PLUGINS_DIR) . '/orangehrmCoreOAuthPlugin/test/fixtures/dummy.txt';
        $response = new BinaryFileResponse($file);
        $response->setAutoEtag();

        $psr7Response = $psrHttpFactory->createPsr7Response($response);
        $this->assertEquals(200, $psr7Response->getStatusCode());
        $this->assertEquals(
            "PSR7 Factory Testing\n",
            $psr7Response->getBody()->getContents()
        ); // content moved from symfony response to PSR7 response
        $this->assertEquals('"wv9f/4/CFoXJL92J1+8eHGycB3sYTPklQ3PXjxJf5Ss="', $psr7Response->getHeader('etag')[0]);
    }

    public function testCreateRequestFromPsr7Request(): void
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $psr7Request = $psr17Factory->createServerRequest('GET', 'http://example.org:4044/api/v2/pim/employees');
        $psr7Request = $psr7Request->withAttribute('empNumber', 5);
        $psr7Request = $psr7Request->withQueryParams(['limit' => 10]);

        $request = $psrHttpFactory->createRequestFromPsr7Request($psr7Request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/api/v2/pim/employees', $request->getRequestUri());
        $this->assertEquals(10, $request->query->get('limit'));
        $this->assertEquals(5, $request->attributes->get('empNumber'));
    }

    public function testCreateResponseFromPsr7Response(): void
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $psr7Response = $psr17Factory->createResponse(400);
        $psr7Response = $psr7Response->withHeader('content-type', 'application/json');

        $response = $psrHttpFactory->createResponseFromPsr7Response($psr7Response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
