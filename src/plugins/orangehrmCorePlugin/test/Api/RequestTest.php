<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
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

    public function testGetAllParametersPrecedence(): void
    {
        $httpRequest = new HttpRequest(
            ['all' => 'query', 'queryAndBody' => 'query'],
            ['all' => 'body', 'queryAndBody' => 'body', 'routeAndBody' => 'body'],
            ['all' => 'route', 'routeAndBody' => 'route']
        );
        $request = new Request($httpRequest);
        $this->assertEquals(
            ['all' => 'route', 'queryAndBody' => 'body', 'routeAndBody' => 'route'],
            $request->getAllParameters()
        );
    }

    /**
     * @dataProvider conflictingQueryKeysDataProvider
     */
    public function testGetConflictingQueryKeys(array $query, array $body, array $attributes, array $expected): void
    {
        $request = new Request(new HttpRequest($query, $body, $attributes));
        $this->assertSame($expected, $request->getConflictingQueryKeys());
    }

    public function conflictingQueryKeysDataProvider(): array
    {
        return [
            'no parameters' => [[], [], [], []],
            'query only' => [['empNumber' => '2', 'limit' => '50'], [], [], []],
            'route only' => [[], [], ['empNumber' => '2'], []],
            'body only' => [[], ['empNumber' => 2], [], []],
            'query differs from route' => [['empNumber' => '1'], [], ['empNumber' => '2'], ['empNumber']],
            'query equals route' => [['empNumber' => '2'], [], ['empNumber' => '2'], []],
            'query differs from body' => [['empNumber' => '1'], ['empNumber' => 2], [], ['empNumber']],
            'query equals integer body' => [['empNumber' => '2'], ['empNumber' => 2], [], []],
            'query equals string body' => [['empNumber' => '2'], ['empNumber' => '2'], [], []],
            'leading zero is a different value' => [['empNumber' => '02'], [], ['empNumber' => '2'], ['empNumber']],
            'padded value is a different value' => [['empNumber' => '2 '], [], ['empNumber' => '2'], ['empNumber']],
            'null body' => [['empNumber' => '2'], ['empNumber' => null], [], ['empNumber']],
            'boolean body' => [['active' => '1'], ['active' => true], [], ['active']],
            'float body' => [['empNumber' => '2'], ['empNumber' => 2.0], [], ['empNumber']],
            'array body' => [['empNumber' => '2'], ['empNumber' => [2]], [], ['empNumber']],
            'query array against route' => [['empNumber' => ['2']], [], ['empNumber' => '2'], ['empNumber']],
            'query array against body array' => [['ids' => ['1', '2']], ['ids' => [1, 2]], [], ['ids']],
            'route default differs' => [['id' => '5'], [], ['id' => 0], ['id']],
            'route default equals' => [['id' => '0'], [], ['id' => 0], []],
            'route and body differ without query' => [[], ['empNumber' => 3], ['empNumber' => '2'], []],
            'several keys' => [
                ['empNumber' => '1', 'reviewId' => '3', 'limit' => '5'],
                ['reviewId' => 4],
                ['empNumber' => '2'],
                ['empNumber', 'reviewId'],
            ],
            'underscore attribute' => [['_api' => 'X'], [], ['_api' => 'Y'], ['_api']],
        ];
    }
}
