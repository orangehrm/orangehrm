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

use Orangehrm\Rest\Api\PublicApi\ApiDefinitionAPI;
use Orangehrm\Rest\Http\JsonResponse;
use Orangehrm\Rest\Http\Request;

/**
 * @group API
 */
class ApiDefinitionAPITest extends PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider paramProvider
     * @param $returnParams
     */
    public function testGetOpenApiDefinition($returnParams)
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $apiDefinitionApi = $this->getMockBuilder('Orangehrm\Rest\Api\PublicApi\ApiDefinitionAPI')
            ->setMethods(['filterParameters'])
            ->setConstructorArgs([$request])
            ->getMock();
        $apiDefinitionApi->expects($this->once())
            ->method('filterParameters')
            ->willReturn($returnParams);

        $openApiDefinitionResponse = $apiDefinitionApi->getOpenApiDefinition();
        $this->assertTrue($openApiDefinitionResponse instanceof JsonResponse);
        $def = json_decode($openApiDefinitionResponse->formatData(), true);
        $this->assertEquals('3.0.3', $def['openapi']);
        $this->assertEquals('/api/v1', $def['info']['x-base-path']);
    }

    /**
     * @return Generator
     */
    public function paramProvider()
    {
        yield [[ApiDefinitionAPI::PARAMETER_MINIFIED => true]];
        yield [[ApiDefinitionAPI::PARAMETER_MINIFIED => false]];
    }
}
