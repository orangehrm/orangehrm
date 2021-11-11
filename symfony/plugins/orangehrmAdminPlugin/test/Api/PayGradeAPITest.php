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

use OrangeHRM\Admin\Api\PayGradeAPI;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
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
class PayGradeAPITest extends EndpointTestCase
{
    private PayGradeAPI $payGradeApi;

    protected function setUp(): void
    {
        $this->payGradeApi = new PayGradeAPI($this->getRequest());
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml'
        );
        $this->createKernelWithMockServices([Services::PAY_GRADE_SERVICE => new PayGradeService()]);
    }

    protected function getTestCasesByKey($testCaseKey): array
    {
        return TestDataService::loadFixtures(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/PayGradeAPI.yml',
            $testCaseKey
        );
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
