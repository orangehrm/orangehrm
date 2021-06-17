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

use Exception;
use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Admin\Dto\NationalitySearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class NationalityDaoTest extends TestCase
{
    private NationalityDao $nationalityDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->nationalityDao = new NationalityDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/NationalityDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddNationality(): void
    {
        $nationality = new Nationality();
        $nationality->setName('Nationality 2');

        $this->nationalityDao->saveNationality($nationality);

        $savedNationality = TestDataService::fetchLastInsertedRecord('Nationality', 'a.id');

        $this->assertTrue($savedNationality instanceof Nationality);
        $this->assertEquals('Nationality 2', $savedNationality->getName());
    }

    public function testEditNationality(): void
    {
        $nationality = TestDataService::fetchObject('Nationality', 3);
        $nationality->setName('American');

        $this->nationalityDao->saveNationality($nationality);

        $savedNationality = TestDataService::fetchLastInsertedRecord('Nationality', 'a.id');

        $this->assertTrue($savedNationality instanceof Nationality);
        $this->assertEquals('American', $savedNationality->getName());
    }

    public function testGetNationalityList(): void
    {
        $nationalityFilterParams = new NationalitySearchFilterParams();
        $result = $this->nationalityDao->getNationalityList($nationalityFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Nationality);
    }

    public function testGetNationalityById(): void
    {
        $nationality = $this->nationalityDao->getNationalityById(1);

        $this->assertTrue($nationality instanceof Nationality);
        $this->assertEquals('nationality 1', $nationality->getName());
    }

    public function testGetNationalityByName(): void
    {
        $object = $this->nationalityDao->getNationalityByName('nationality 1');
        $this->assertTrue($object instanceof Nationality);
        $this->assertEquals(1, $object->getId());

        $object = $this->nationalityDao->getNationalityByName('NATIONALITY 1');
        $this->assertTrue($object instanceof Nationality);
        $this->assertEquals(1, $object->getId());

        $object = $this->nationalityDao->getNationalityByName(' nationality 1 ');
        $this->assertTrue($object instanceof Nationality);
        $this->assertEquals(1, $object->getId());

        $object = $this->nationalityDao->getNationalityByName('nationality 2');
        $this->assertTrue($object instanceof Nationality);
        $this->assertEquals(2, $object->getId());

        $object = $this->nationalityDao->getNationalityByName('Bike Riding');
        $this->assertFalse($object instanceof Nationality);
    }

    public function testDeleteNationalities(): void
    {
        $result = $this->nationalityDao->deleteNationalities([1, 2, 3]);
        $this->assertEquals(3, $result);
    }

    public function testIsExistingNationalityName(): void
    {
        $this->assertTrue($this->nationalityDao->isExistingNationalityName('nationality 1'));
        $this->assertTrue($this->nationalityDao->isExistingNationalityName('NATIONALITY 1'));
        $this->assertTrue($this->nationalityDao->isExistingNationalityName(' nationality 1 '));
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->nationalityDao->deleteNationalities([4]);

        $this->assertEquals(0, $result);
    }
}
