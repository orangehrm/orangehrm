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

/**
 * Test case for SchemaIncrementTask55
 *
 */
class SchemaIncrementTask58Test extends PHPUnit_Framework_TestCase {

    protected $schema;
    private $databaseValues;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->databaseValues = sfYaml::load(sfConfig::get('sf_apps_dir') . '/upgrader/config/test_db.yml');
        $this->schema = new SchemaIncrementTask58($this->databaseValues);
        $this->schema->initDB();
    }
    
    public function testGetNextScreenId() {
        //expected values are hard coded at the time this was created and actual expected value may differ according to the plugins, etc
        $expected = 108;
        $result = $this->schema->getNextScreenId();
        $this->assertEquals($expected, $result);
    }
    
    public function testGetNextDataGroupId() {
        //expected values are hard coded at the time this was created and actual expected value may differ according to the plugins, etc
        $expected = 60;
        $result = $this->schema->getNextDataGroupId();
        $this->assertEquals($expected, $result);
    }

   
}

