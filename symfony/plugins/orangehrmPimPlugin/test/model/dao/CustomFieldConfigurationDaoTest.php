<?php

/**
 * CustomFieldsDao Test Class
 * @group Pim
 */
class CustomFieldConfigurationDaoTest extends PHPUnit_Framework_TestCase {

	private $customFieldConfigurationDao ;

	protected function setUp() {
        
        $this->customFieldConfigurationDao = new CustomFieldConfigurationDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/CustomFieldConfigurationDao.yml';
        TestDataService::populate($this->fixture);        
        
	}
    
    public function testGetCustomFieldListDefault() {
        
        $fieldList = $this->customFieldConfigurationDao->getCustomFieldList();
        
        /* Checking the type*/
        foreach ($fieldList as $field) {
            $this->assertTrue($field instanceof CustomField);
        }
       
        /* Checking the count */
        $this->assertEquals(5, count($fieldList));
        
        /* Checking the order */
        $this->assertEquals('Age', $fieldList[0]->getName());
        $this->assertEquals('Medium', $fieldList[4]->getName());
       
    }

    public function testGetCustomFieldListSpecificScreen() {
        
        $fieldList = $this->customFieldConfigurationDao->getCustomFieldList('emergency');
        
        /* Checking the type*/
        foreach ($fieldList as $field) {
            $this->assertTrue($field instanceof CustomField);
        }
       
        /* Checking the count */
        $this->assertEquals(2, count($fieldList));
        
        /* Checking the order */
        $this->assertEquals('Emergency Type', $fieldList[0]->getName());
        $this->assertEquals('Level', $fieldList[1]->getName());
       
    }
    
    public function testSaveCustomField() {
        
        $customField = new CustomField();
        $customField->setName('Hobby');
        $customField->setType(0);
        $customField->setScreen('personal');

        $result = $this->customFieldConfigurationDao->saveCustomField($customField);
        
        $this->assertTrue($result instanceof CustomField);
        $this->assertEquals('Hobby', $result->getName());
        $this->assertEquals(6, $result->getId());
        
    }
    
    public function testDeleteCustomFields() {
        
        $result = $this->customFieldConfigurationDao->deleteCustomFields(array(1, 3));
        $this->assertEquals(2, $result);     
        
        /*Checking whether the correct fields were deleted*/
        
        $result = TestDataService::fetchObject('CustomField', 1);
        $this->assertTrue(empty($result));
        
        $result = TestDataService::fetchObject('CustomField', 3);
        $this->assertTrue(empty($result));   
        
        $result = TestDataService::fetchObject('CustomField', 2);
        $this->assertTrue($result instanceof CustomField);           
        
    }
    
    public function testGetCustomField() {
        
        $result = $this->customFieldConfigurationDao->getCustomField(1);
        $this->assertTrue($result instanceof CustomField);
        $this->assertEquals('Age', $result->getName());
        
        $result = $this->customFieldConfigurationDao->getCustomField(12);
        $this->assertNull($result);        
                
    }



//   /**
//    * Testing saveCustomField
//    */
//   public function testSaveCustomField() {
//      foreach($this->testCases['CustomFields'] as $k => $v) {
//         $customFields = new CustomFields();
//         $customFields->setFieldNum($v['field_num']);
//         $customFields->setName($v['name']);
//         $customFields->setType($v['type']);
//         $customFields->setExtraData($v['extra_data']);
//         $result = $this->customFieldsDao->saveCustomField($customFields);
//         $this->assertTrue($result);
//      }
//   }
//
//   /**
//    * Testing readCustomField
//    */
//   public function testReadCustomField() {
//      foreach($this->testCases['CustomFields'] as $k => $v) {
//         $result = $this->customFieldsDao->readCustomField($v['field_num']);
//         $this->assertTrue($result instanceof CustomFields);
//      }
//   }
//
//   /**
//    * Testing DeleteCustomField
//    */
//   public function testDeleteCustomField() {
//      foreach($this->testCases['CustomFields'] as $k => $v) {
//         $result = $this->customFieldsDao->deleteCustomFields(array($v['field_num']));
//         $this->assertTrue($result);
//      }
//   }
   
   
   
   
   
}
