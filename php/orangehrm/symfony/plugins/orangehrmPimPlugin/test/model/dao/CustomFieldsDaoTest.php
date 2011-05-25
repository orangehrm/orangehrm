<?php
require_once 'PHPUnit/Framework.php';
/**
 * CustomFieldsDao Test Class
 * @author Sujith T
 *
 */
class CustomFieldsDaoTest extends PHPUnit_Framework_TestCase {
	private $testCases;
	private $customFieldsDao ;

	/**
	 * Set up method
	 */
	protected function setUp() {
		$this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/admin/customFields.yml');
		$this->customFieldsDao	= new CustomFieldsDao();
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
         $result = $this->customFieldsDao->saveCustomField($customFields);
         $this->assertTrue($result);
      }
   }

   /**
    * Testing readCustomField
    */
   public function testReadCustomField() {
      foreach($this->testCases['CustomFields'] as $k => $v) {
         $result = $this->customFieldsDao->readCustomField($v['field_num']);
         $this->assertTrue($result instanceof CustomFields);
      }
   }

   /**
    * Testing DeleteCustomField
    */
   public function testDeleteCustomField() {
      foreach($this->testCases['CustomFields'] as $k => $v) {
         $result = $this->customFieldsDao->deleteCustomField(array($v['field_num']));
         $this->assertTrue($result);
      }
   }
}
?>