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

namespace OrangeHRM\Tests\Recruitment\Controller;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\PublicController\VacancyListRestController;
use OrangeHRM\Recruitment\Controller\PublicController\VacancyRestController;
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

    /**
     * @throws SearchParamException
     */
    public function testHandleGetRequestWithLimit(): void
    {
        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
        ]);
        $controller = new VacancyListRestController();
        $httpRequest = $this->getHttpRequest(['limit'=> 2]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $iteratableResponse = json_decode($response->formatData(), false);
        $this->assertCount(2, $iteratableResponse->data);
    }

    /**
     * @throws SearchParamException
     */
    public function testHandleGetRequestWithOffset(): void
    {
        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
        ]);
        $controller = new VacancyListRestController();
        $httpRequest = $this->getHttpRequest(['offset'=> 2]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $iteratableResponse = json_decode($response->formatData(), false);
        $this->assertEquals('Charted Engineer', $iteratableResponse->data[0]->name);
    }


    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testHandlePutRequest(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->handlePutRequest($request);
    }

    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testInitPutValidationRule(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->initPutValidationRule($request);
    }

    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testHandlePostRequest(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->handlePutRequest($request);
    }

    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testInitPostValidationRule(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->initPutValidationRule($request);
    }

    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testHandleDeleteRequest(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->handlePutRequest($request);
    }

    /**
     * @return void
     * @throws NotImplementedException
     */
    public function testInitDeleteValidationRule(): void
    {
        $request = new Request($this->getHttpRequest());
        $controller = new VacancyRestController();
        $this->expectException(NotImplementedException::class);
        $controller->initPutValidationRule($request);
    }
}
