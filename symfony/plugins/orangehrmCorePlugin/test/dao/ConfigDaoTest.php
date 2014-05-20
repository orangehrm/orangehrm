<?php



/**
 * ConfigDao Test Class
 * @group Core
 */
class ConfigDaoTest extends PHPUnit_Framework_TestCase {

    private $configDao;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->configDao = new ConfigDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ConfigDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing setValue()
     */
    public function testSetValue() {
        
        // Set new value
        $key = 'test_new_value';
        $value = 'abc123';
        $this->configDao->setValue($key, $value);
        
        // Verify set
        $this->assertTrue($this->_isValueSet($key, $value));
        
        
        // Set existing value
        $value = 'xyz abc';
        $this->configDao->setValue($key, $value);
        $this->assertTrue($this->_isValueSet($key, $value));
        
    }

    /**
     * Testing getValue()
     */
    public function testGetValue() {
        
        // Test values in fixtures.yml
        $fixtureObjects = TestDataService::loadObjectList('Config', $this->fixture, 'Config');
        
        foreach($fixtureObjects as $config) {
            $value = $this->configDao->getValue($config->key);
            
            $this->assertEquals($config->value, $value);            
        }

    }
    
    public function testGetAllValues() {
        $result = $this->configDao->getAllValues();

        // Test values in fixtures.yml
        $fixtureObjects = TestDataService::loadObjectList('Config', $this->fixture, 'Config');
        
        foreach($fixtureObjects as $config) {
            $this->assertTrue(isset($result[$config->key]));
            $this->assertEquals($config->value, $result[$config->key]);            
        }        
        
        $this->assertEquals(count($fixtureObjects), count($result));
        
    }

    /**
     * Checks if value set
     * 
     * @param type $key Key
     * @param type $value Value
     */
    private function _isValueSet($key, $value) {
        
        $q = Doctrine_Query::create()
             ->select('COUNT(c.value)')
             ->from('Config c')
             ->where('c.key = ?', $key)
             ->andWhere('c.value = ?', $value);

        $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);        

        return ($value == 1);
    }
}
