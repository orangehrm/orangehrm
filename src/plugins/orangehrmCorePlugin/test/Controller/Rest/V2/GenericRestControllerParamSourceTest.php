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

namespace OrangeHRM\Tests\Core\Controller\Rest\V2;

use OrangeHRM\Core\Controller\Rest\V2\GenericRestController;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * A query value must not stand in for the route or body value of the same name during validation,
 * because handlers read the route or body value.
 *
 * @group Core
 * @group APIv2
 */
class GenericRestControllerParamSourceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->createKernel();
        ParamSourceTestAPI::$handled = null;
    }

    public function testRouteValueIsValidated(): void
    {
        $this->assertHandled(['empNumber' => 1], $this->send('GET', [], [], ['empNumber' => '1']));
        $this->assertRejected($this->send('GET', [], [], ['empNumber' => '2']), 'empNumber');
    }

    public function testQueryValueCannotReplaceRouteValue(): void
    {
        $this->assertRejected($this->send('GET', ['empNumber' => '1'], [], ['empNumber' => '2']), 'empNumber');
        $this->assertRejected($this->send('PUT', ['empNumber' => '1'], [], ['empNumber' => '2']), 'empNumber');
        $this->assertRejected($this->send('DELETE', ['empNumber' => '1'], [], ['empNumber' => '2']), 'empNumber');
    }

    public function testInaccessibleQueryValueWithAccessibleRouteValueIsRejected(): void
    {
        $this->assertRejected($this->send('GET', ['empNumber' => '2'], [], ['empNumber' => '1']), 'empNumber');
    }

    /**
     * Both values pass the rule on their own, so the 422 comes from the conflict, not from the rule.
     */
    public function testDifferingValuesAreRejectedEvenWhenBothAreAccessible(): void
    {
        $this->assertHandled(['empNumber' => 3], $this->send('GET', [], [], ['empNumber' => '3']));
        $this->assertRejected($this->send('GET', ['empNumber' => '1'], [], ['empNumber' => '3']), 'empNumber');
        $this->assertRejected($this->send('POST', ['empNumber' => '1'], ['empNumber' => 3]), 'empNumber');
    }

    public function testQueryArrayCannotReplaceRouteValue(): void
    {
        $this->assertRejected($this->send('GET', ['empNumber' => ['1']], [], ['empNumber' => '1']), 'empNumber');
    }

    public function testSameQueryAndRouteValueIsAccepted(): void
    {
        $this->assertHandled(['empNumber' => 1], $this->send('GET', ['empNumber' => '1'], [], ['empNumber' => '1']));
    }

    public function testQueryValueCannotReplaceBodyValue(): void
    {
        $this->assertRejected($this->send('POST', ['empNumber' => '1'], ['empNumber' => 2]), 'empNumber');
        $this->assertRejected($this->send('POST', ['empNumber' => '1'], ['empNumber' => '2']), 'empNumber');
    }

    public function testSameQueryAndBodyValueIsAccepted(): void
    {
        $this->assertHandled(
            ['empNumber' => 1, 'routeEmpNumber' => null],
            $this->send('POST', ['empNumber' => '1'], ['empNumber' => 1])
        );
    }

    public function testBodyValueIsValidated(): void
    {
        $this->assertHandled(
            ['empNumber' => 1, 'routeEmpNumber' => null],
            $this->send('POST', [], ['empNumber' => 1])
        );
        $this->assertRejected($this->send('POST', [], ['empNumber' => 2]), 'empNumber');
    }

    public function testQueryOnlyValueIsValidated(): void
    {
        $this->assertHandled(['empNumber' => 1], $this->send('GET', ['empNumber' => '1']));
        $this->assertRejected($this->send('GET', ['empNumber' => '2']), 'empNumber');
    }

    /**
     * Route and body may carry different values (e.g. POST /pim/employees/{empNumber}/subordinates with the
     * subordinate in the body); the route value is validated as before.
     */
    public function testRouteAndBodyWithDifferentValuesStillDispatch(): void
    {
        $this->assertHandled(
            ['empNumber' => 2, 'routeEmpNumber' => 1],
            $this->send('POST', [], ['empNumber' => 2], ['empNumber' => '1'])
        );
        $this->assertRejected($this->send('POST', [], ['empNumber' => 1], ['empNumber' => '2']), 'empNumber');
    }

    /**
     * @param string $method
     * @param array $query
     * @param array $body
     * @param array $attributes route attributes; `_api` is added, and `_key` when the route names an employee
     * @return array [status code, decoded content]
     */
    private function send(string $method, array $query, array $body = [], array $attributes = []): array
    {
        ParamSourceTestAPI::$handled = null;
        $httpRequest = $this->getHttpRequest(
            $query,
            $body,
            array_merge(
                $attributes,
                ['_api' => ParamSourceTestAPI::class],
                isset($attributes['empNumber']) ? ['_key' => 'empNumber'] : []
            )
        );
        $httpRequest->setMethod($method);
        $response = (new GenericRestController())->handle($httpRequest);
        return [$response->getStatusCode(), json_decode($response->getContent(), true)];
    }

    private function assertHandled(array $expected, array $response): void
    {
        [$status, $content] = $response;
        $this->assertEquals(200, $status, json_encode($content));
        $this->assertSame($expected, $content['data']);
        $this->assertSame($expected, ParamSourceTestAPI::$handled);
    }

    private function assertRejected(array $response, string $key): void
    {
        [$status, $content] = $response;
        $this->assertEquals(422, $status, json_encode($content));
        $this->assertSame([$key], $content['error']['data']['invalidParamKeys']);
        $this->assertNull(ParamSourceTestAPI::$handled, 'the handler must not run');
    }
}
