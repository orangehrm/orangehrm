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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\PayGradeDao;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;
use OrangeHRM\Entity\PayPeriod;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class PayGradeServiceTest extends TestCase
{
    private PayGradeService $payGradeService;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->payGradeService = new PayGradeService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPayGradeDao(): void
    {
        $this->assertTrue($this->payGradeService->getPayGradeDao() instanceof PayGradeDao);
    }

    public function testGetPayGradeList(): void
    {
        $payGradeList = TestDataService::loadObjectList(PayGrade::class, $this->fixture, 'PayGrade');

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getPayGradeList')
            ->will($this->returnValue($payGradeList));

        $this->payGradeService->setPayGradeDao($payGradeDao);

        $payGradeSearchFilterParams = new PayGradeSearchFilterParams();
        $result = $this->payGradeService->getPayGradeList($payGradeSearchFilterParams);
        $this->assertEquals($result, $payGradeList);
    }

    public function testGetPayGradeById(): void
    {
        $payGradeList = TestDataService::loadObjectList(PayGrade::class, $this->fixture, 'PayGrade');

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getPayGradeById')
            ->with(1)
            ->will($this->returnValue($payGradeList[0]));

        $this->payGradeService->setPayGradeDao($payGradeDao);

        $result = $this->payGradeService->getPayGradeById(1);
        $this->assertEquals($result, $payGradeList[0]);
    }

    public function testGetCurrencyListByPayGradeId(): void
    {
        $payGradeCurrencyList = TestDataService::loadObjectList(
            PayGradeCurrency::class,
            $this->fixture,
            'PayGradeCurrency'
        );
        $payGradeCurrencyList = [$payGradeCurrencyList[0], $payGradeCurrencyList[1]];

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getCurrencyListByPayGradeId')
            ->with(1)
            ->will($this->returnValue($payGradeCurrencyList));

        $this->payGradeService->setPayGradeDao($payGradeDao);

        $result = $this->payGradeService->getCurrencyListByPayGradeId(1);
        $this->assertEquals($result, $payGradeCurrencyList);
    }

    public function testGetCurrencyByCurrencyIdAndPayGradeId(): void
    {
        $payGradeCurrencyList = TestDataService::loadObjectList(
            PayGradeCurrency::class,
            $this->fixture,
            'PayGradeCurrency'
        );

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getCurrencyByCurrencyIdAndPayGradeId')
            ->with('USD', 1)
            ->will($this->returnValue($payGradeCurrencyList[0]));

        $this->payGradeService->setPayGradeDao($payGradeDao);

        $result = $this->payGradeService->getCurrencyByCurrencyIdAndPayGradeId('USD', 1);
        $this->assertEquals($result, $payGradeCurrencyList[0]);
    }

    public function testGetPayPeriodArray(): void
    {
        $payPeriod = new PayPeriod();
        $payPeriod->setCode(1);
        $payPeriod->setName('Weekly');

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getPayPeriods')
            ->will($this->returnValue([$payPeriod]));

        $payGradeService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getNormalizerService'])
            ->getMock();
        $payGradeService->expects($this->once())
            ->method('getNormalizerService')
            ->will($this->returnValue(new NormalizerService()));
        $payGradeService->setPayGradeDao($payGradeDao);

        $result = $payGradeService->getPayPeriodArray();
        $this->assertEquals([['id' => 1, 'label' => 'Weekly']], $result);
    }

    public function testGetPayGradeArray(): void
    {
        $payGrade = new PayGrade();
        $payGrade->setId(1);
        $payGrade->setName('Test');

        $payGradeService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getNormalizerService', 'getPayGradeList'])
            ->getMock();
        $payGradeService->expects($this->once())
            ->method('getNormalizerService')
            ->will($this->returnValue(new NormalizerService()));

        $payGradeService->expects($this->once())
            ->method('getPayGradeList')
            ->will($this->returnValue([$payGrade]));

        $result = $payGradeService->getPayGradeArray();
        $this->assertEquals([['id' => 1, 'label' => 'Test']], $result);
    }

    public function testGetCurrencyArray(): void
    {
        $currencyType = new CurrencyType();
        $currencyType->setId(151);
        $currencyType->setId('USD');
        $currencyType->setName('United States Dollar');

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getCurrencies')
            ->will($this->returnValue([$currencyType]));

        $payGradeService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getNormalizerService'])
            ->getMock();
        $payGradeService->expects($this->once())
            ->method('getNormalizerService')
            ->will($this->returnValue(new NormalizerService()));
        $payGradeService->setPayGradeDao($payGradeDao);

        $result = $payGradeService->getCurrencyArray();
        $this->assertEquals([['id' => 'USD', 'label' => 'United States Dollar']], $result);
    }

    public function testGetPayGradeCurrencyListCount(): void
    {
        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getPayGradeCurrencyListCount')
            ->will($this->returnValue(2));

        $this->payGradeService->setPayGradeDao($payGradeDao);
        $payGradeSearchFilterParams = new PayGradeCurrencySearchFilterParams();
        $result = $this->payGradeService->getPayGradeCurrencyListCount($payGradeSearchFilterParams);
        $this->assertEquals($result, 2);
    }

    public function testGetPayGradeCurrencyList(): void
    {
        $payGradeCurrencyList = TestDataService::loadObjectList(
            PayGradeCurrency::class,
            $this->fixture,
            'PayGradeCurrency'
        );
        $payGradeCurrencyList = [$payGradeCurrencyList[0], $payGradeCurrencyList[1]];
        $payGradeSearchFilterParams = new PayGradeCurrencySearchFilterParams();
        $payGradeSearchFilterParams->setPayGradeId(1);
        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getPayGradeCurrencyList')
            ->with($payGradeSearchFilterParams)
            ->will($this->returnValue($payGradeCurrencyList));

        $this->payGradeService->setPayGradeDao($payGradeDao);

        $result = $this->payGradeService->getPayGradeCurrencyList($payGradeSearchFilterParams);
        $this->assertEquals($result, $payGradeCurrencyList);
    }

    public function testSavePayGrade(): void
    {
        $payGrade = new PayGrade();
        $payGrade->setName('Executive');

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('savePayGrade')
            ->with($payGrade)
            ->will($this->returnValue($payGrade));
        $this->payGradeService->setPayGradeDao($payGradeDao);
        $savedPayGrade = $this->payGradeService->savePayGrade($payGrade);

        $this->assertTrue($savedPayGrade instanceof PayGrade);
    }

    public function testSavePayGradeCurrency(): void
    {
        $payGradeCurrency = new PayGradeCurrency();
        $payGradeCurrency->setMaxSalary(1000);
        $payGradeCurrency->setMinSalary(1000);

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('savePayGradeCurrency')
            ->with($payGradeCurrency)
            ->will($this->returnValue($payGradeCurrency));
        $this->payGradeService->setPayGradeDao($payGradeDao);
        $result = $this->payGradeService->savePayGradeCurrency($payGradeCurrency);

        $this->assertTrue($result instanceof PayGradeCurrency);
    }

    public function testDeletePayGrades(): void
    {
        $payGradeIds = [1];

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('deletePayGrades')
            ->with($payGradeIds)
            ->will($this->returnValue(1));
        $this->payGradeService->setPayGradeDao($payGradeDao);
        $result = $this->payGradeService->deletePayGrades($payGradeIds);
        $this->assertEquals(1, $result);
    }

    public function testGetAllowedPayCurrencies(): void
    {
        $currencyType = new CurrencyType();
        $currencyType->setId(151);
        $currencyType->setId('USD');
        $currencyType->setName('United States Dollar');

        $payGradeSearchFilterParams = new PayGradeCurrencySearchFilterParams();

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getAllowedPayCurrencies')
            ->with($payGradeSearchFilterParams)
            ->will($this->returnValue([$currencyType]));
        $this->payGradeService->setPayGradeDao($payGradeDao);
        $result = $this->payGradeService->getAllowedPayCurrencies($payGradeSearchFilterParams);
        $this->assertEquals($result, [$currencyType]);
    }

    public function testGetAllowedPayCurrenciesCount(): void
    {
        $currencyType = new CurrencyType();
        $currencyType->setId(151);
        $currencyType->setId('USD');
        $currencyType->setName('United States Dollar');

        $payGradeSearchFilterParams = new PayGradeCurrencySearchFilterParams();

        $payGradeDao = $this->getMockBuilder(PayGradeDao::class)->getMock();
        $payGradeDao->expects($this->once())
            ->method('getAllowedPayCurrenciesCount')
            ->with($payGradeSearchFilterParams)
            ->will($this->returnValue(1));
        $this->payGradeService->setPayGradeDao($payGradeDao);
        $result = $this->payGradeService->getAllowedPayCurrenciesCount($payGradeSearchFilterParams);
        $this->assertEquals($result, 1);
    }
}
