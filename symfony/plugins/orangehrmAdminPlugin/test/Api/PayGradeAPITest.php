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

use OrangeHRM\Admin\Api\PayGradeAPI;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

class PayGradeAPITest extends EndpointTestCase
{
    private PayGradeAPI $payGradeApi;

    protected function setUp(): void
    {
        $this->payGradeApi = new PayGradeAPI($this->getRequest());
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
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/PayGradeAPI.yml'
        );
        if (array_key_exists($testCaseKey, $testCases)) {
            return $testCases[$testCaseKey];
        }
        return [];
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
        $this->payGradeApi = new PayGradeAPI($this->getRequest([], [], [CommonParams::PARAMETER_ID => $params['id']]));
        $payGrade = $this->payGradeApi->getOne();
        $this->assertEquals($result, $payGrade->normalize());
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
        $validationRule = $this->payGradeApi->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($params, $validationRule));
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
        $validationRule = $this->payGradeApi->getValidationRuleForGetAll();
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
        $this->payGradeApi = new PayGradeAPI($this->getRequest($params));
        $payGrades = $this->payGradeApi->getAll();
        $this->assertEquals($result, $payGrades->normalize());
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
        $validationRule = $this->payGradeApi->getValidationRuleForCreate();
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
        $this->paygradeApi = new PayGradeAPI($this->getRequest([], $params));
        $location = $this->paygradeApi->create();
        $this->assertEquals($result, $location->normalize());
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
        $validationRule = $this->payGradeApi->getValidationRuleForUpdate();
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
        $id = $params['id'];
        unset($params['id']);
        $this->payGradeApi = new PayGradeAPI($this->getRequest([], $params, ['id' => $id]));
        $payGrade = $this->payGradeApi->update();
        $this->assertEquals($result, $payGrade->normalize());
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
        $validationRule = $this->payGradeApi->getValidationRuleForDelete();
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
        $this->payGradeApi = new PayGradeAPI($this->getRequest([], $params));
        $payGradeSearchFilterParams = new PayGradeSearchFilterParams();
        $this->assertEquals(
            $result['preCount'],
            count(
                $this->payGradeApi->getPayGradeService()->getPayGradeList($payGradeSearchFilterParams)
            )
        );
        $payGrade = $this->payGradeApi->delete();
        $this->assertEquals(
            $result['postCount'],
            count(
                $this->payGradeApi->getPayGradeService()->getPayGradeList($payGradeSearchFilterParams)
            )
        );

        $this->assertEquals($result['ids'], $payGrade->normalize());
    }
}
