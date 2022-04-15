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

namespace Api;

use OrangeHRM\Admin\Api\PayGradeCurrencyAPI;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group APIv2
 */
class PayGradeCurrencyAPITest extends EndpointTestCase
{
    private PayGradeCurrencyAPI $payGradeCurrencyApi;

    protected function setUp(): void
    {
        $this->payGradeCurrencyApi = new PayGradeCurrencyAPI($this->getRequest());
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml'
        );
        $this->getContainer()->register(
            Services::PAY_GRADE_SERVICE,
            PayGradeService::class
        );
    }

    protected function getTestCasesByKey($testCaseKey): array
    {
        return TestDataService::loadFixtures(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/PayGradeCurrencyAPI.yml',
            $testCaseKey
        );
    }

    public function dataProviderForTestGetValidationRuleForGetOne(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetOne
     */
    public function testGetValidationRuleForGetOne($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->payGradeCurrencyApi->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCasesByKey('GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->payGradeCurrencyApi = new PayGradeCurrencyAPI(
            $this->getRequest(
                [],
                [],
                [
                    CommonParams::PARAMETER_ID => $params['id'],
                    PayGradeCurrencyAPI::PARAMETER_PAY_GRADE_ID => $params['payGradeId']
                ]
            )
        );
        $payGradeCurrency = $this->payGradeCurrencyApi->getOne();
        $this->assertEquals($result, $payGradeCurrency->normalize());
    }

    public function dataProviderForTestGetValidationRuleForGetAll(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetAll
     */
    public function testGetValidationRuleForGetAll($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->payGradeCurrencyApi->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCasesByKey('GetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->payGradeCurrencyApi = new PayGradeCurrencyAPI(
            $this->getRequest(
                $params['query']?? [],
                [],
                $params['attribute']?? []
            )
        );
        $payGradeCurrencies = $this->payGradeCurrencyApi->getAll();
        $this->assertEquals($result, $payGradeCurrencies->normalize());
    }

    public function dataProviderForTestGetValidationRuleForCreate(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForCreate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForCreate
     */
    public function testGetValidationRuleForCreate($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->payGradeCurrencyApi->getValidationRuleForCreate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCasesByKey('Create');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->paygradeCurrencyApi = new PayGradeCurrencyAPI(
            $this->getRequest(
                [],
                $params['body']?? [],
                $params['attribute']?? []
            )
        );
        $paygradeCurrency = $this->paygradeCurrencyApi->create();
        $this->assertEquals($result, $paygradeCurrency->normalize());
    }

    public function dataProviderForTestGetValidationRuleForUpdate(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForUpdate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForUpdate
     */
    public function testGetValidationRuleForUpdate($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->payGradeCurrencyApi->getValidationRuleForUpdate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCasesByKey('Update');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->payGradeCurrencyApi = new PayGradeCurrencyAPI(
            $this->getRequest(
                [],
                $params['body'] ?? [],
                $params['attribute'] ?? []
            )
        );
        $payGradeCurrency = $this->payGradeCurrencyApi->update();
        $this->assertEquals($result, $payGradeCurrency->normalize());
    }

    public function dataProviderForTestGetValidationRuleForDelete(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForDelete');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForDelete
     */
    public function testGetValidationRuleForDelete($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->payGradeCurrencyApi->getValidationRuleForDelete();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCasesByKey('Delete');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->payGradeCurrencyAPI = new PayGradeCurrencyAPI(
            $this->getRequest(
                [],
                $params['body'] ?? [],
                $params['attribute'] ?? []
            )
        );
        $payGradeCurrencySearchFilterParams = new PayGradeCurrencySearchFilterParams();
        $payGradeCurrencySearchFilterParams->setPayGradeId($params['attribute']['payGradeId']);
        $this->assertEquals(
            $result['preCount'],
            $this->payGradeCurrencyAPI->getPayGradeService()->getPayGradeCurrencyListCount(
                $payGradeCurrencySearchFilterParams
            )
        );
        $location = $this->payGradeCurrencyAPI->delete();
        $this->assertEquals(
            $result['postCount'],
            $this->payGradeCurrencyAPI->getPayGradeService()->getPayGradeCurrencyListCount(
                $payGradeCurrencySearchFilterParams
            )
        );
        $this->assertEquals($result['ids'], $location->normalize());
    }
}
