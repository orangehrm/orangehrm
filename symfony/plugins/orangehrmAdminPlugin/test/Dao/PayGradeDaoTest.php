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

namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\PayGradeDao;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class PayGradeDaoTest extends TestCase
{

    private PayGradeDao $payGradeDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->payGradeDao = new PayGradeDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/PayGradeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPayGradeList(): void
    {
        $payGradeSearchFilterParams = new PayGradeSearchFilterParams();
        $result = $this->payGradeDao->getPayGradeList($payGradeSearchFilterParams);
        $this->assertCount(3, $result);
        $this->assertEquals('Pay Grade 1', $result[0]->getName());
        $this->assertEquals('Pay Grade 3', $result[2]->getName());
    }

    public function testGetPayGradeById(): void
    {
        $result = $this->payGradeDao->getPayGradeById(1);
        $this->assertEquals($result->getName(), 'Pay Grade 1');
    }

    public function testGetCurrencyListByPayGradeId(): void
    {
        $result = $this->payGradeDao->getCurrencyListByPayGradeId(1);
        $this->assertCount(2, $result);
        $this->assertEquals('AUD', $result[0]->getCurrencyType()->getId());
        $this->assertEquals('USD', $result[1]->getCurrencyType()->getId());
    }

    public function testGetCurrencyByCurrencyIdAndPayGradeId(): void
    {
        $result = $this->payGradeDao->getCurrencyByCurrencyIdAndPayGradeId('USD', 1);
        $this->assertEquals($result->getMinSalary(), 5000);
    }

    public function testGetPayPeriods(): void
    {
        $result = $this->payGradeDao->getPayPeriods();
        $this->assertCount(6, $result);
        $this->assertEquals('Bi Weekly', $result[0]->getName());
        $this->assertEquals('Weekly', $result[5]->getName());
    }

    public function testGetCurrencies(): void
    {
        $result = $this->payGradeDao->getCurrencies();
        $this->assertCount(2, $result);
        $this->assertEquals('AUD', $result[0]->getId());
        $this->assertEquals('USD', $result[1]->getId());
    }

    public function testGetCurrencyById(): void
    {
        $result = $this->payGradeDao->getCurrencyById('AUD');
        $this->assertEquals(2, $result->getCode());
        $this->assertEquals('Australian Dollar', $result->getName());
    }
}
