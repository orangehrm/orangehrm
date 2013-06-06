<?php

/**
 * Test class of CustomFieldsService
 *
 * @group Pim
 */
class CustomFieldsServiceTest extends PHPUnit_Framework_TestCase {

   private $testCases;
	private $customFieldsService;
   private $customFieldsDao;

	/**
	 * Set up method
	 */
	protected function setUp() {
		$this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/admin/customFields.yml');
		$this->customFieldsService	= new CustomFieldConfigurationService();
	}

   /**
    * Testing saveCustomField
    */
   public function testSaveCustomField() {
      foreach($this->testCases['CustomFields'] as $k => $v) {
         $customFields = new CustomFields();
         $customFields->setFieldNum($v['field_num']);
         $customFields->setName($v['name']);
         $customFields->setType($v['type']);
         $customFields->setExtraData($v['extra_data']);

         $this->customFieldsDao  =	$this->getMock('CustomFieldsDao');
         $this->customFieldsDao->expects($this->once())
            ->method('saveCustomField')
            ->will($this->returnValue(true));
         $this->customFieldsService->setCustomFieldsDao($this->customFieldsDao);
         
         $result = $this->customFieldsService->saveCustomField($customFields);
         $this->assertTrue($result);
      }
   }

   /**
    * Testing getCustomFieldList
    */
   public function testGetCustomFieldList() {
      $customFieldsDao = new CustomFieldConfigurationDao();
      $list = $customFieldsDao->getCustomFieldList();

      $this->customFieldsDao  =	$this->getMock('CustomFieldsDao');
      $this->customFieldsDao->expects($this->once())
         ->method('getCustomFieldList')
         ->will($this->returnValue($list));
      $this->customFieldsService->setCustomFieldsDao($this->customFieldsDao);
      $customFieldsList = $this->customFieldsService->getCustomFieldList();
      $this->assertEquals($list, $customFieldsList);
   }

   /**
    * Testing readCustomField
    */
   public function testReadCustomField() {
      foreach($this->testCases['CustomFields'] as $k => $v) {
         $customFields = new CustomFields();
         $customFields->setFieldNum($v['field_num']);
         $customFields->setName($v['name']);
         $customFields->setType($v['type']);
         $customFields->setExtraData($v['extra_data']);

         $this->customFieldsDao  =	$this->getMock('CustomFieldsDao');
         $this->customFieldsDao->expects($this->once())
               ->method('readCustomField')
               ->will($this->returnValue($customFields));
         $this->customFieldsService->setCustomFieldsDao($this->customFieldsDao);
         $result = $this->customFieldsService->readCustomField($v['field_num']);
         $this->assertTrue($result instanceof CustomFields);
      }
   }

   /**
    * Testing DeleteCustomField
    */
   public function testDeleteCustomField() {
      foreach($this->testCases['CustomFields'] as $k => $v) {
         $this->customFieldsDao  =	$this->getMock('CustomFieldsDao');
         $this->customFieldsDao->expects($this->once())
               ->method('deleteCustomField')
               ->will($this->returnValue(true));
         $this->customFieldsService->setCustomFieldsDao($this->customFieldsDao);
         $result = $this->customFieldsService->deleteCustomFields(array($v['field_num']));
         $this->assertTrue($result);
      }
   }
}
?>
