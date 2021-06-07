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
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;
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
        $payGradeCurrencyList = array($payGradeCurrencyList[0], $payGradeCurrencyList[1]);

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
}
