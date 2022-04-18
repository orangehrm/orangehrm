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

use Generator;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\Helpers\ValidationDecorator;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\Framework\Http\Request as HttpRequest;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group APIv2
 */
class AbstractEndpointTest extends TestCase
{
    use ValidatorTrait;

    public function testEndpoint(): void
    {
        $endpoint = $this->getMockEndpoint();
        $endpointRequest = $this->invokeProtectedMethodOnMock(Endpoint::class, $endpoint, 'getRequest');
        $this->assertTrue($endpointRequest instanceof Request);
        $endpointRequestParams = $this->invokeProtectedMethodOnMock(Endpoint::class, $endpoint, 'getRequestParams');
        $this->assertTrue($endpointRequestParams instanceof RequestParams);
        $endpointValidationDecorator = $this->invokeProtectedMethodOnMock(
            Endpoint::class,
            $endpoint,
            'getValidationDecorator'
        );
        $this->assertTrue($endpointValidationDecorator instanceof ValidationDecorator);
    }

    public function testGetSortingAndPaginationParamsRules(): void
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $this->assertCount(4, $endpointSortingAndPaginationParamsRules);

        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules([[], true]);
        $this->assertCount(3, $endpointSortingAndPaginationParamsRules);
    }

    /**
     * @return ParamRule[]
     */
    private function getSortingAndPaginationParamsRules(array $methodParams = []): array
    {
        $endpoint = $this->getMockEndpoint();
        return $this->invokeProtectedMethodOnMock(
            Endpoint::class,
            $endpoint,
            'getSortingAndPaginationParamsRules',
            $methodParams
        );
    }

    public function testSortOrderParamsRules(): void
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $sortOrderParamRule = $endpointSortingAndPaginationParamsRules[0];
        $rules = new ParamRuleCollection($sortOrderParamRule);
        $this->assertTrue($this->validate([], $rules));
        $this->assertTrue($this->validate(['sortOrder' => null], $rules));
        $this->assertTrue($this->validate(['sortOrder' => 'ASC'], $rules));
        $this->assertTrue($this->validate(['sortOrder' => 'DESC'], $rules));

        $this->expectException(InvalidParamException::class);
        // empty string not acceptable
        $this->validate(['sortOrder' => ''], $rules);
    }

    public function testLimitParamsRules(): void
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $limitParamRule = $endpointSortingAndPaginationParamsRules[1];
        $rules = new ParamRuleCollection($limitParamRule);
        $this->assertTrue($this->validate([], $rules));
        $this->assertTrue($this->validate(['limit' => null], $rules));
        $this->assertTrue($this->validate(['limit' => '0'], $rules));
        $this->assertTrue($this->validate(['limit' => 0], $rules));
        $this->assertTrue($this->validate(['limit' => 1], $rules));
        $this->assertTrue($this->validate(['limit' => 2147483647], $rules));
        $this->assertTrue($this->validate(['limit' => '2147483647'], $rules));
        $this->assertTrue($this->validate(['limit' => PHP_INT_MAX], $rules));
        $this->assertTrue($this->validate(['limit' => ''], $rules));
    }

    /**
     * @dataProvider limitParamsRulesExceptionDataProvider
     * @param ParamRuleCollection $rules
     * @param $value
     */
    public function testLimitParamsRulesExceptions(ParamRuleCollection $rules, $value): void
    {
        $this->expectException(InvalidParamException::class);
        $this->validate(['limit' => $value], $rules);
    }

    /**
     * @return Generator
     */
    public function limitParamsRulesExceptionDataProvider(): Generator
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $limitParamRule = $endpointSortingAndPaginationParamsRules[1];
        $rules = new ParamRuleCollection($limitParamRule);
        yield [$rules, 'string'];
        yield [$rules, '-1'];
        yield [$rules, -1];
    }

    public function testOffsetParamsRules(): void
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $offsetParamRule = $endpointSortingAndPaginationParamsRules[2];
        $rules = new ParamRuleCollection($offsetParamRule);
        $this->assertTrue($this->validate([], $rules));
        $this->assertTrue($this->validate(['offset' => null], $rules));
        $this->assertTrue($this->validate(['offset' => '0'], $rules));
        $this->assertTrue($this->validate(['offset' => 0], $rules));
        $this->assertTrue($this->validate(['offset' => 1], $rules));
        $this->assertTrue($this->validate(['offset' => 2147483647], $rules));
        $this->assertTrue($this->validate(['offset' => '2147483647'], $rules));
        $this->assertTrue($this->validate(['offset' => PHP_INT_MAX], $rules));
        $this->assertTrue($this->validate(['offset' => ''], $rules));
    }

    /**
     * @dataProvider offsetParamsRulesExceptionDataProvider
     * @param ParamRuleCollection $rules
     * @param $value
     */
    public function testOffsetParamsRulesExceptions(ParamRuleCollection $rules, $value): void
    {
        $this->expectException(InvalidParamException::class);
        $this->validate(['offset' => $value], $rules);
    }

    /**
     * @return Generator
     */
    public function offsetParamsRulesExceptionDataProvider(): Generator
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules();
        $offsetParamRule = $endpointSortingAndPaginationParamsRules[2];
        $rules = new ParamRuleCollection($offsetParamRule);
        yield [$rules, 'string'];
        yield [$rules, '-1'];
        yield [$rules, -1];
    }

    public function testSortFieldParamsRules(): void
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules([['e.firstName']]);
        $sortFieldParamRule = $endpointSortingAndPaginationParamsRules[3];
        $rules = new ParamRuleCollection($sortFieldParamRule);
        $this->assertTrue($this->validate([], $rules));
        $this->assertTrue($this->validate(['sortField' => null], $rules));
        $this->assertTrue($this->validate(['sortField' => 'e.firstName'], $rules));
        $this->assertTrue($this->validate(['sortField' => ''], $rules));
    }

    /**
     * @dataProvider sortFieldParamsRulesExceptionDataProvider
     * @param ParamRuleCollection $rules
     * @param $value
     */
    public function testSortFieldParamsRulesExceptions(ParamRuleCollection $rules, $value): void
    {
        $this->expectException(InvalidParamException::class);
        $this->validate(['sortField' => $value], $rules);
    }

    /**
     * @return Generator
     */
    public function sortFieldParamsRulesExceptionDataProvider(): Generator
    {
        $endpointSortingAndPaginationParamsRules = $this->getSortingAndPaginationParamsRules([['e.firstName']]);
        $sortFieldParamRule = $endpointSortingAndPaginationParamsRules[3];
        $rules = new ParamRuleCollection($sortFieldParamRule);
        yield [$rules, 'e.lastName'];
        yield [$rules, '-1'];
        yield [$rules, -1];
    }

    public function testSetRequestParams(): void
    {
        $endpoint = $this->getMockEndpoint();
        $httpRequest = $this->invokeProtectedMethodOnMock(
            Endpoint::class,
            $endpoint,
            'getRequest'
        );
        $this->invokeProtectedMethodOnMock(
            Endpoint::class,
            $endpoint,
            'setRequestParams',
            [new RequestParams($httpRequest)]
        );
        $this->assertTrue(
            $this->invokeProtectedMethodOnMock(
                Endpoint::class,
                $endpoint,
                'getRequestParams'
            ) instanceof RequestParams
        );
    }

    /**
     * @dataProvider setSortingAndPaginationParamsDataProvider
     * @param array $query
     * @param $sortField
     * @param $sortOrder
     * @param $limit
     * @param $offset
     */
    public function testSetSortingAndPaginationParams(
        array $query,
        $sortField,
        $sortOrder,
        $limit,
        $offset,
        $defaultSortField = null
    ): void {
        $endpoint = $this->getMockEndpoint($query);
        $filterParams = new FilterParams();
        /** @var FilterParams $updatedFilterParams */
        $updatedFilterParams = $this->invokeProtectedMethodOnMock(
            Endpoint::class,
            $endpoint,
            'setSortingAndPaginationParams',
            [$filterParams, $defaultSortField]
        );
        $this->assertEquals($sortField, $updatedFilterParams->getSortField());
        $this->assertEquals($sortOrder, $updatedFilterParams->getSortOrder());
        $this->assertEquals($limit, $updatedFilterParams->getLimit());
        $this->assertEquals($offset, $updatedFilterParams->getOffset());
    }

    /**
     * @return Generator
     */
    public function setSortingAndPaginationParamsDataProvider(): Generator
    {
        yield [[], null, 'ASC', 50, 0];
        yield [[CommonParams::PARAMETER_SORT_ORDER => 'DESC'], null, 'DESC', 50, 0];
        yield [[CommonParams::PARAMETER_SORT_ORDER => 'ASC'], null, 'ASC', 50, 0];

        yield [[CommonParams::PARAMETER_SORT_FIELD => 'e.firstName'], 'e.firstName', 'ASC', 50, 0];
        yield [[CommonParams::PARAMETER_SORT_FIELD => null], null, 'ASC', 50, 0];
        yield [[CommonParams::PARAMETER_SORT_FIELD => ''], null, 'ASC', 50, 0];
        yield [[], 'e.firstName', 'ASC', 50, 0, 'e.firstName'];

        yield [[CommonParams::PARAMETER_LIMIT => null], null, 'ASC', 0, 0];
        yield [[CommonParams::PARAMETER_LIMIT => '5'], null, 'ASC', 5, 0];
        yield [[CommonParams::PARAMETER_LIMIT => '2147483647'], null, 'ASC', 2147483647, 0];

        yield [[CommonParams::PARAMETER_OFFSET => '100'], null, 'ASC', 50, 100];
        yield [[CommonParams::PARAMETER_OFFSET => null], null, 'ASC', 50, 0];
    }

    /**
     * @return Endpoint
     */
    private function getMockEndpoint(array $query = [])
    {
        $httpRequest = new HttpRequest($query);
        $request = new Request($httpRequest);
        return $this->getMockBuilder(Endpoint::class)
            ->setConstructorArgs([$request])
            ->getMockForAbstractClass();
    }
}
