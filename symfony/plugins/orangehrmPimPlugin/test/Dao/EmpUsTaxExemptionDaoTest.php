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

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmpUsTaxExemption;
use OrangeHRM\Pim\Dao\EmpUsTaxExemptionDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmpUsTaxExemptionDaoTest extends TestCase
{
    private EmpUsTaxExemptionDao $empUsTaxExemptionDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->empUsTaxExemptionDao = new EmpUsTaxExemptionDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpUsTaxExemptionDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeTaxExemption(): void
    {
        $result = $this->empUsTaxExemptionDao->getEmployeeTaxExemption(1);
        $this->assertEquals('Single', $result->getFederalStatus());
        $this->assertEquals(2, $result->getFederalExemptions());
        $this->assertEquals('AK', $result->getState());
        $this->assertEquals('Single', $result->getStateStatus());
        $this->assertEquals(1, $result->getStateExemptions());
        $this->assertEquals('AK', $result->getUnemploymentState());
        $this->assertEquals('AK', $result->getWorkState());
    }

    public function testSaveEmployeeTaxExemption(): void
    {
        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->getDecorator()->setEmployeeByEmpNumber(3);
        $empUsTaxExemption->setFederalStatus('Single');
        $empUsTaxExemption->setFederalExemptions(2);
        $empUsTaxExemption->setState('AK');
        $empUsTaxExemption->setStateStatus('Single');
        $empUsTaxExemption->setStateExemptions(1);
        $empUsTaxExemption->setUnemploymentState('AK');
        $empUsTaxExemption->setWorkState('AK');
        $result = $this->empUsTaxExemptionDao->saveEmployeeTaxExemption($empUsTaxExemption);
        $this->assertTrue($result instanceof EmpUsTaxExemption);
        $this->assertEquals('Single', $result->getFederalStatus());
        $this->assertEquals(2, $result->getFederalExemptions());
        $this->assertEquals('AK', $result->getState());
        $this->assertEquals('Single', $result->getStateStatus());
        $this->assertEquals(1, $result->getStateExemptions());
        $this->assertEquals('AK', $result->getUnemploymentState());
        $this->assertEquals('AK', $result->getWorkState());
    }

    public function testEditEmployeeTaxExemption(): void
    {
        $empUsTaxExemption = $this->empUsTaxExemptionDao->getEmployeeTaxExemption(1);
        $empUsTaxExemption->setFederalStatus('Married');
        $empUsTaxExemption->setFederalExemptions(3);
        $empUsTaxExemption->setState('AL');
        $empUsTaxExemption->setStateStatus('Married');
        $empUsTaxExemption->setStateExemptions(2);
        $empUsTaxExemption->setUnemploymentState('AL');
        $empUsTaxExemption->setWorkState('AL');
        $result = $this->empUsTaxExemptionDao->saveEmployeeTaxExemption($empUsTaxExemption);
        $this->assertTrue($result instanceof EmpUsTaxExemption);
        $this->assertEquals('Married', $empUsTaxExemption->getFederalStatus());
        $this->assertEquals(3, $empUsTaxExemption->getFederalExemptions());
        $this->assertEquals('AL', $empUsTaxExemption->getState());
        $this->assertEquals('Married', $empUsTaxExemption->getStateStatus());
        $this->assertEquals(2, $empUsTaxExemption->getStateExemptions());
        $this->assertEquals('AL', $empUsTaxExemption->getUnemploymentState());
        $this->assertEquals('AL', $empUsTaxExemption->getWorkState());
    }
}
