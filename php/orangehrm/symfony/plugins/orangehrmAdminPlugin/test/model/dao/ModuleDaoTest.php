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
class ModuleDaoTest extends PHPUnit_Framework_TestCase {

	private $moduleDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->moduleDao = new ModuleDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/ModuleDao.yml';
		TestDataService::populate($this->fixture);
        
	}
    
    public function testGetDisabledModuleList() {
        
        $disabledModuleList = $this->moduleDao->getDisabledModuleList();
        
        $this->assertEquals(1, count($disabledModuleList));
        $this->assertTrue($disabledModuleList[0] instanceof Module);
        $this->assertEquals('benefits', $disabledModuleList[0]->getName());
        
    }

    public function testUpdateModuleStatusWithChange() {
        
        $moduleList = array('leave', 'time');
        $status = Module::DISABLED;
        $result = $this->moduleDao->updateModuleStatus($moduleList, $status);
        
        $this->assertEquals(2, $result);
        
        $module = TestDataService::fetchObject('Module', 3);
        $this->assertEquals(Module::DISABLED, $module->getStatus());

        $module = TestDataService::fetchObject('Module', 4);
        $this->assertEquals(Module::DISABLED, $module->getStatus());
        
    }

    public function testUpdateModuleStatusWithNoChange() {
        
        $moduleList = array('leave', 'time');
        $status = Module::ENABLED;
        $result = $this->moduleDao->updateModuleStatus($moduleList, $status);
        
        $this->assertEquals(0, $result);
        
        $module = TestDataService::fetchObject('Module', 3);
        $this->assertEquals(Module::ENABLED, $module->getStatus());

        $module = TestDataService::fetchObject('Module', 4);
        $this->assertEquals(Module::ENABLED, $module->getStatus());
        
    }
    
    
}
