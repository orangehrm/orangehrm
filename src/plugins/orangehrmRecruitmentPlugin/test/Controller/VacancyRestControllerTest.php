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
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\VacancyRestController;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class VacancyRestControllerTest extends KernelTestCase
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
     * @return void
     */
    public function testHandleGetRequest(): void
    {
        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::NORMALIZER_SERVICE => new NormalizerService(),
        ]);

        $controller = new VacancyRestController();
        $httpRequest = $this->getHttpRequest([], [], ['id'=>1]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $decodedResponse = json_decode($response->formatData(), false);
        $this->assertEquals('Technical Assistant Intern', $decodedResponse->data->name);

        $controller = new VacancyRestController();
        $httpRequest = $this->getHttpRequest([], [], ['id'=>2]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $decodedResponse = json_decode($response->formatData(), false);
        $this->assertNotEquals('Technical Assistant Intern', $decodedResponse->data->name);

        $controller = new VacancyRestController();
        $httpRequest = $this->getHttpRequest([], [], ['id'=>3]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $decodedResponse = json_decode($response->formatData(), false);
        $this->assertEquals('', $decodedResponse->data->description);

        $controller = new VacancyRestController();
        $httpRequest = $this->getHttpRequest([], [], ['id'=>4]);
        $request = new Request($httpRequest);
        $response = $controller->handleGetRequest($request);
        $decodedResponse = json_decode($response->formatData(), false);
        $this->assertEquals('Manages Engineers', $decodedResponse->data->description);
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
