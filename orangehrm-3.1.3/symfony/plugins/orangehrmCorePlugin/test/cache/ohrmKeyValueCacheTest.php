<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

/**
 * @group Core
 */
class ohrmKeyValueCacheTest extends PHPUnit_Framework_TestCase {

    
    protected function setUp() {
    }
    
    protected function tearDown() {
        $fileSystem = new sfFilesystem();
        $fileSystem->remove(sfFinder::type('file')->discard('.sf')->in(sfConfig::get('sf_cache_dir') . '/ohrmKeyValueCache'));       
    }

    public function testGetNoValues() {
        $keyValueCache = new ohrmKeyValueCache('testGetNoValues', function() { return array(); });
        $this->assertNull($keyValueCache->get("ABC"));        
    }
    
    public function testGetManyValues() {
        $keyValueCache = new ohrmKeyValueCache('testGetNoValues', function() { 
            return array('age' => 12, 'name' => 'john major', 'height' => 5);             
        });
        
        $this->assertEquals(12, $keyValueCache->get("age"));
        $this->assertEquals('john major', $keyValueCache->get("name"));
        $this->assertEquals(5, $keyValueCache->get('height'));
    }    

}

