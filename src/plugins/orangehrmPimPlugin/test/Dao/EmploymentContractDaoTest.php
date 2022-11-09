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

namespace OrangeHRM\Tests\Pim\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Pim\Dao\EmploymentContractDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmploymentContractDaoTest extends TestCase
{
    /**
     * @var EmploymentContractDao
     */
    private EmploymentContractDao $employmentContractDao;

    protected function setUp(): void
    {
        $this->employmentContractDao = new EmploymentContractDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmploymentContractDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmploymentContractByEmpNumber(): void
    {
        $empContract = $this->employmentContractDao->getEmploymentContractByEmpNumber(1);
        $this->assertTrue($empContract instanceof EmpContract);
        $this->assertEquals(1, $empContract->getContractId());
        $this->assertEquals('2020-05-23', $empContract->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2021-05-23', $empContract->getEndDate()->format('Y-m-d'));
        $this->assertEquals('Kayla', $empContract->getEmployee()->getFirstName());

        $empContract = $this->employmentContractDao->getEmploymentContractByEmpNumber(3);
        $this->assertNull($empContract);

        $empContract = $this->employmentContractDao->getEmploymentContractByEmpNumber(100);
        $this->assertNull($empContract);
    }
}
