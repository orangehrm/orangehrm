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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Recruitment\Controller;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\VacancyListRestController;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class VacancyListRestControllerTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            .'/orangehrmRecruitmentPlugin/test/fixtures/VacancyListRestController.yaml';
        TestDataService::populate($fixture);
    }

    /**
     * @throws SearchParamException
     */
    public function testHandleGetRequest(): void
    {
        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
        ]);

        $controller = new VacancyListRestController();
        $httpRequest = $this->getHttpRequest();
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $iteratableResponse = json_decode($response->formatData(), false);
        $this->assertEquals('Electrical Engineer Officer', $iteratableResponse->data[0]->name);
    }

    /**
     * @throws SearchParamException
     */
    public function testHandleGetRequestCount(): void
    {
        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
        ]);
        $controller = new VacancyListRestController();
        $httpRequest = $this->getHttpRequest();
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $iteratableResponse = json_decode($response->formatData(), false);
        $this->assertCount(5, $iteratableResponse->data);
    }
}
