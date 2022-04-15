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

use OrangeHRM\Admin\Api\PayGradeAllowedCurrencyAPI;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group APIv2
 */
class PayGradeAllowedCurrencyAPITest extends EndpointTestCase
{
    private PayGradeAllowedCurrencyAPI $payGradeAllowedCurrencyAPI;

    protected function setUp(): void
    {
        $this->payGradeAllowedCurrencyAPI = new PayGradeAllowedCurrencyAPI($this->getRequest());
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
            Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAdminPlugin/test/fixtures/testcases/PayGradeAllowedCurrencyAPI.yml',
            $testCaseKey
        );
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
        $validationRule = $this->payGradeAllowedCurrencyAPI->getValidationRuleForGetAll();
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
        $this->payGradeAllowedCurrencyAPI = new PayGradeAllowedCurrencyAPI(
            $this->getRequest(
                $params['query']?? [],
                [],
                $params['attribute']?? []
            )
        );
        $payGradeCurrencies = $this->payGradeAllowedCurrencyAPI->getAll();
        $this->assertEquals($result, $payGradeCurrencies->normalize());
    }
}
