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

use OrangeHRM\Admin\Dao\EducationDao;
use OrangeHRM\Admin\Dto\QualificationEducationSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Education;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Exception;

/**
 * @group Admin
 * @group Dao
 */
class EducationDaoTest extends TestCase
{
    private EducationDao $educationDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->educationDao = new EducationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/EducationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddEducation(): void
    {
        $education = new Education();
        $education->setName('PMP');

        $this->educationDao->saveEducation($education);

        $savedEducation = TestDataService::fetchLastInsertedRecord('Education', 'a.id');

        $this->assertTrue($savedEducation instanceof Education);
        $this->assertEquals('PMP', $savedEducation->getName());
    }

    public function testEditEducation(): void
    {
        $education = TestDataService::fetchObject('Education', 3);
        $education->setName('MSc New');

        $this->educationDao->saveEducation($education);

        $savedEducation = TestDataService::fetchLastInsertedRecord('Education', 'a.id');

        $this->assertTrue($savedEducation instanceof Education);
        $this->assertEquals('MSc New', $savedEducation->getName());
    }

    public function testGetEducationById(): void
    {
        $education = $this->educationDao->getEducationById(1);

        $this->assertTrue($education instanceof Education);
        $this->assertEquals('PhD', $education->getName());
    }

    public function testGetEducationList(): void
    {
        $educationFilterParams = new QualificationEducationSearchFilterParams();
        $result = $this->educationDao->getEducationList($educationFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Education);
    }

    public function testDeleteEducations(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->educationDao->deleteEducations($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->educationDao->deleteEducations([4]);

        $this->assertEquals(0, $result);
    }

    public function testIsExistingEducationName(): void
    {
        $this->assertTrue($this->educationDao->isExistingEducationName('PhD'));
        $this->assertTrue($this->educationDao->isExistingEducationName('PHD'));
        $this->assertTrue($this->educationDao->isExistingEducationName('phd'));
        $this->assertTrue($this->educationDao->isExistingEducationName('  PhD  '));
    }

    public function testGetEducationByName(): void
    {
        $object = $this->educationDao->getEducationByName('PhD');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());

        $object = $this->educationDao->getEducationByName('PHD');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());

        $object = $this->educationDao->getEducationByName('phd');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());

        $object = $this->educationDao->getEducationByName('  PhD  ');
        $this->assertTrue($object instanceof Education);
        $this->assertEquals(1, $object->getId());

        $object = $this->educationDao->getEducationByName('MBA');
        $this->assertFalse($object instanceof Education);
    }
}
