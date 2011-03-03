<?php
require_once 'PHPUnit/Framework.php';

class EncryptionListenerTest extends PHPUnit_Framework_TestCase {

    private $key1;
    private $key2;

    public function setup() {

        $this->key1 = '31fae626564189808b80f3fc06e58e5f0b6c64ddac1bc8ad298d8123229628421062b42e9e3304d5c554b94f5e32a88a72e4cbed76d9ac5c6c9dabc1d8db72d4';
        $this->key2 = '31fae626564184892ea403fc44448e52226c64dda44bc1aa298d81232291283314624424943404d4444444444444444444444444444444444c9dabc1d8db72d4';
    }
	  
    /**
    * Tests the encrypt, decrypt functions
    */
    public function testEncryptDecrypt() {

        $listener = new EncryptionListener("field1", $this->key1);

        $data = 'test is a test string';
        $enc = $listener->encrypt($data, $this->key1);
        $this->assertTrue($data != $enc);
        $dec = $listener->decrypt($enc, $this->key1);
        $this->assertEquals($data, $dec);

        $data = '';
        $enc = $listener->encrypt($data, $this->key1);
        $this->assertTrue($enc === '');
        $dec = $listener->decrypt($enc, $this->key1);
        $this->assertTrue($dec == '');

        $data = null;
        $enc = $listener->encrypt($data, $this->key1);
        $this->assertTrue($enc === null);
        $dec = $listener->decrypt($enc, $this->key1);
        $this->assertTrue($dec === null);

    }

    /**
     * Test encrypt/decrypt compatibility with MySQL
     */
    public function testEncryptDecryptMySQLCompatibility() {
        $conn = Doctrine_Manager::connection();
        

        $data = "Compatibility check string";
        $listener = new EncryptionListener("field1", $this->key1);
        $enc = $listener->encrypt($data, $this->key1);

        $mysqlEnc = $conn->fetchOne("SELECT hex(aes_encrypt('" . $data . "', '" . $this->key1 . "'))");

        $this->assertEquals($mysqlEnc, $enc);
    }

    /**
     * Test that the preSave() encrypts
     */
    public function testPreSave() {
        $value = 'abcd';
        $invoker = array('fieldX' => 'xyz', 'field1' => $value, 'field3' => '3pd');
        
        $event = new Doctrine_Event($invoker, "a");

        $listener = new EncryptionListener("field1", $this->key1);

        $listener->preSave($event);

        // Compare field value to encrypted value
        $enc = $listener->encrypt($value, $this->key1);
        $invokerAfter = $event->getInvoker();

        $valueAfterPreSave = $invokerAfter['field1'];

        $this->assertNotEquals($valueAfterPreSave, $value);
        $this->assertEquals($valueAfterPreSave, $enc);

        // no change if field not found.
        $invoker = array('fieldX' => 'xyz', 'field1' => $value, 'field3' => '3pd');
        $event = new Doctrine_Event($invoker, "a");
        $listener = new EncryptionListener("invkey", $this->key1);
        $listener->preSave($event);
        $invokerAfter2 = $event->getInvoker();
        $this->assertEquals($invokerAfter2, $invoker);
    }

    /** Test that preHydrate decrypts data */
    public function testPreHydrate() {
        $value = 'abcd';

        $listener = new EncryptionListener("field1", $this->key2);
        $enc = $listener->encrypt($value, $this->key2);
        $data = array('fieldX' => 'xyz', 'field1' => $enc, 'field3' => '3pd');

        $event = new Doctrine_Event(null, '');
        $event->set('data', $data);
        $listener->preHydrate($event);

        // Compare field value to decrypted value
        $dataAfter = $event->data;

        $valueAfterPreHydrate = $dataAfter['field1'];

        $this->assertNotEquals($valueAfterPreHydrate, $enc);
        $this->assertEquals($valueAfterPreHydrate, $value);

        // no change if field not found.
        $data = array('fieldX' => 'xyz', 'field1' => $enc, 'field3' => '3pd');
        $event = new Doctrine_Event(null, '');
        $event->set('data', $data);;
        
        $listener = new EncryptionListener("invkey", $this->key2);
        $listener->preHydrate($event);
        $dataAfter2 = $event->data;
        $this->assertEquals($dataAfter2, $data);
    }

    public function testPreDqlUpdate() {

        $ssn = '9299-29992-2222';
    	$query = Doctrine_Query::create()
                 ->update('Employee')
                 ->set('firstName = ?', 'John')
                 ->set('lastName = ?', 'Anthony')
                 ->set('ssn = ? ', $val)
                 ->set('nickName = ?', 'J')
                 ->where('emp_number = ?', 1);

        $listener = new EncryptionListener("ssn", $this->key2);
        $event = new Doctrine_Event(null, '', $query);
        $listener->preDqlUpdate($event);

        $encSSN = $listener->encrypt($ssn, $this->key2);
        $queryAfter = $event->getQuery();

        // Validate that ssn is not in query
        $this->assertFalse($queryAfter->contains($ssn));

        // Validate that encrypted ssn is in query
        $this->assertFalse($queryAfter->contains($encSSN));
        

    }

}