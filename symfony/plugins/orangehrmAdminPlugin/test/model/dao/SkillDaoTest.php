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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class SkillDaoTest extends PHPUnit_Framework_TestCase {

	private $skillDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->skillDao = new SkillDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/SkillDao.yml';
		TestDataService::populate($this->fixture);
	}

    public function testAddSkill() {
        
        $skill = new Skill();
        $skill->setName('Gardening');
        $skill->setDescription('Flower arts');
        
        $this->skillDao->saveSkill($skill);
        
        $savedSkill = TestDataService::fetchLastInsertedRecord('Skill', 'id');
        
        $this->assertTrue($savedSkill instanceof Skill);
        $this->assertEquals('Gardening', $savedSkill->getName());
        $this->assertEquals('Flower arts', $savedSkill->getDescription());
        
    }
    
    public function testEditSkill() {
        
        $skill = TestDataService::fetchObject('Skill', 3);
        $skill->setDescription('Ability to help disabled people');
        
        $this->skillDao->saveSkill($skill);
        
        $savedSkill = TestDataService::fetchLastInsertedRecord('Skill', 'id');
        
        $this->assertTrue($savedSkill instanceof Skill);
        $this->assertEquals('Sign Language', $savedSkill->getName());
        $this->assertEquals('Ability to help disabled people', $savedSkill->getDescription());
        
    }
    
    public function testGetSkillById() {
        
        $skill = $this->skillDao->getSkillById(1);
        
        $this->assertTrue($skill instanceof Skill);
        $this->assertEquals('Driving', $skill->getName());
        $this->assertEquals('Ability to drive', $skill->getDescription());     
        
    }
    
    public function testGetSkillList() {
        
        $skillList = $this->skillDao->getSkillList();
        
        foreach ($skillList as $skill) {
            $this->assertTrue($skill instanceof Skill);
        }
        
        $this->assertEquals(3, count($skillList));        
        
        /* Checking record order */
        $this->assertEquals('Driving', $skillList[0]->getName());
        $this->assertEquals('Skydiving', $skillList[2]->getName());
        
    }
    
    public function testDeleteSkills() {
        
        $result = $this->skillDao->deleteSkills(array(1, 2));
        
        $this->assertEquals(2, $result);
        $this->assertEquals(1, count($this->skillDao->getSkillList()));       
        
    }
    
    public function testDeleteWrongRecord() {
        
        $result = $this->skillDao->deleteSkills(array(4));
        
        $this->assertEquals(0, $result);
        
    }
    
    public function testIsExistingSkillName() {
        
        $this->assertTrue($this->skillDao->isExistingSkillName('Driving'));
        $this->assertTrue($this->skillDao->isExistingSkillName('DRIVING'));
        $this->assertTrue($this->skillDao->isExistingSkillName('driving'));
        $this->assertTrue($this->skillDao->isExistingSkillName('  Driving  '));
        
    }
    
    public function testGetSkillByName() {
        
        $object = $this->skillDao->getSkillByName('Driving');
        $this->assertTrue($object instanceof Skill);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->skillDao->getSkillByName('DRIVING');
        $this->assertTrue($object instanceof Skill);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->skillDao->getSkillByName('driving');
        $this->assertTrue($object instanceof Skill);
        $this->assertEquals(1, $object->getId());

        $object = $this->skillDao->getSkillByName('  Driving  ');
        $this->assertTrue($object instanceof Skill);
        $this->assertEquals(1, $object->getId());
        
        $object = $this->skillDao->getSkillByName('Climbing');
        $this->assertFalse($object);        
        
    }    
    
}
