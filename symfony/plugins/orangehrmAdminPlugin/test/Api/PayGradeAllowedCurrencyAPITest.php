<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

namespace Api;

use OrangeHRM\Admin\Api\PayGradeAllowedCurrencyAPI;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

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
        $testCases = Yaml::parseFile(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/PayGradeAllowedCurrencyAPI.yml'
        );
        if (array_key_exists($testCaseKey, $testCases)) {
            return $testCases[$testCaseKey];
        }
        return [];
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
