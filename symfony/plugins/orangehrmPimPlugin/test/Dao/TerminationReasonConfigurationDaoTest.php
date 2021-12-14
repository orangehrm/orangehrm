<?php
/**
 * OrangeLayed offM is a comprehensive Human Resource Management (Layed offM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeLayed offM Inc., http://www.orangehrm.com
 *
 * OrangeLayed offM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeLayed offM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\Pim\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Pim\Dao\TerminationReasonConfigurationDao;
use OrangeHRM\Pim\Dto\TerminationReasonConfigurationSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class TerminationReasonConfigurationDaoTest extends TestCase
{
    /**
     * @var TerminationReasonConfigurationDao
     */
    private TerminationReasonConfigurationDao $terminationReasonConfigurationDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->terminationReasonConfigurationDao = new TerminationReasonConfigurationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/TerminationReasonConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddTerminationReason(): void
    {
        $terminationReason = new TerminationReason();
        $terminationReason->setName('Deceased');

        $this->terminationReasonConfigurationDao->saveTerminationReason($terminationReason);

        $savedTerminationReason = TestDataService::fetchLastInsertedRecord('TerminationReason', 'a.id');

        $this->assertTrue($savedTerminationReason instanceof TerminationReason);
        $this->assertEquals('Deceased', $savedTerminationReason->getName());
    }

    public function testEditTerminationReason(): void
    {
        $terminationReason = TestDataService::fetchObject('TerminationReason', 3);
        $terminationReason->setName('2011 Layed off');

        $this->terminationReasonConfigurationDao->saveTerminationReason($terminationReason);

        $savedTerminationReason = TestDataService::fetchLastInsertedRecord('TerminationReason', 'a.id');

        $this->assertTrue($savedTerminationReason instanceof TerminationReason);
        $this->assertEquals('2011 Layed off', $savedTerminationReason->getName());
    }

    public function testGetTerminationReasonById(): void
    {
        $terminationReason = $this->terminationReasonConfigurationDao->getTerminationReasonById(1);

        $this->assertTrue($terminationReason instanceof TerminationReason);
        $this->assertEquals('Resigned', $terminationReason->getName());
    }

    public function testGetTerminationReasonList(): void
    {
        $terminationReasonConfigurationFilterParams = new TerminationReasonConfigurationSearchFilterParams();
        $result = $this->terminationReasonConfigurationDao->getTerminationReasonList($terminationReasonConfigurationFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof TerminationReason);
    }

    public function testDeleteTerminationReasons(): void
    {
        $result = $this->terminationReasonConfigurationDao->deleteTerminationReasons([1, 2]);
        $this->assertEquals(2, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->terminationReasonConfigurationDao->deleteTerminationReasons([4]);
        $this->assertEquals(0, $result);
    }

    public function testIsExistingTerminationReasonName(): void
    {
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('Resigned'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('RESIGNED'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('resigned'));
        $this->assertTrue($this->terminationReasonConfigurationDao->isExistingTerminationReasonName('  Resigned  '));
    }

    public function testReasonsInUse(): void
    {
        $result = $this->terminationReasonConfigurationDao->getReasonIdsInUse();
        $this->assertEquals(2, count($result));
        $this->assertEquals([0,1], $result);
    }

    public function testGetTerminationReasonByName(): void
    {
        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('Resigned');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());

        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('RESIGNED');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());

        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('resigned');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());

        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('  Resigned  ');
        $this->assertTrue($object instanceof TerminationReason);
        $this->assertEquals(1, $object->getId());

        $object = $this->terminationReasonConfigurationDao->getTerminationReasonByName('Fired');
        $this->assertFalse($object instanceof TerminationReason);
    }
}
