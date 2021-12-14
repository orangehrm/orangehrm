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

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Entity\Config;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * ConfigDao Test Class
 * @group Core
 * @group Dao
 */
class ConfigDaoTest extends KernelTestCase
{
    private ConfigDao $configDao;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->configDao = new ConfigDao();
        $this->fixture = \OrangeHRM\Config\Config::get(\OrangeHRM\Config\Config::PLUGINS_DIR) .
            '/orangehrmCorePlugin/test/fixtures/ConfigDao.yml';
        TestDataService::populate($this->fixture);
        $this->createKernel();
    }

    /**
     * Testing setValue()
     */
    public function testSetValue(): void
    {
        // Set new value
        $key = 'test_new_value';
        $value = 'abc123';
        $this->configDao->setValue($key, $value);

        // Verify set
        $this->assertTrue($this->_isValueSet($key, $value));

        // Set existing value
        $value = 'xyz abc';
        $this->configDao->setValue($key, $value);
        $this->assertTrue($this->_isValueSet($key, $value));
    }

    /**
     * Testing getValue()
     */
    public function testGetValue(): void
    {
        // Test values in fixtures.yml
        $fixtureObjects = TestDataService::loadObjectList(Config::class, $this->fixture, 'Config');

        foreach ($fixtureObjects as $config) {
            $value = $this->configDao->getValue($config->getName());

            $this->assertEquals($config->getValue(), $value);
        }
    }

    public function testGetAllValues(): void
    {
        $result = $this->configDao->getAllValues();

        // Test values in fixtures.yml
        $fixtureObjects = TestDataService::loadObjectList(Config::class, $this->fixture, 'Config');

        foreach ($fixtureObjects as $config) {
            $this->assertTrue(isset($result[$config->getName()]));
            $this->assertEquals($config->getValue(), $result[$config->getName()]);
        }

        $this->assertEquals(count($fixtureObjects), count($result));
    }

    /**
     * Checks if value set
     *
     * @param string $key Key
     * @param string $value Value
     * @return bool
     */
    private function _isValueSet(string $key, string $value): bool
    {
        $q = Doctrine::getEntityManager()->getRepository(Config::class)->createQueryBuilder('c');
        $q->andWhere('c.name = :name');
        $q->setParameter('name', $key);
        $q->andWhere('c.value = :value');
        $q->setParameter('value', $value);

        $count = (new Paginator($q))->count();
        return ($count == 1);
    }
}
