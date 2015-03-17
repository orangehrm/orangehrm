<?php



/**
 * ConfigService Test Class
 * @group Core
 */
class ConfigServiceTest extends PHPUnit_Framework_TestCase {

    private $configService;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->configService = new ConfigService();        
    }
    
    /**
     * Test the getConfigDao() and setConfigDao() method
     */
    public function testGetSetConfigDao() {
        $dao = $this->configService->getConfigDao();
        $this->assertTrue($dao instanceof ConfigDao);
        
        $mockDao = $this->getMock('ConfigDao');
        $this->configService->setConfigDao($mockDao);
        $dao = $this->configService->getConfigDao();
        $this->assertEquals($dao, $mockDao);
    }

    /**
     * Test the setIsLeavePeriodDefined() method
     */
    public function testSetIsLeavePeriodDefined() {
        
        $value = 'Yes';
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_LEAVE_PERIOD_DEFINED, $value);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setIsLeavePeriodDefined($value);
        
        // with invalid parameters        
        try {
            $this->configService->setIsLeavePeriodDefined('test');
            $this->fail("Exception expected when invalid value passed to setisLeavePeriodDefined()");
        } catch (Exception $e) {
            // expected
        }
    }

    /**
     * Test isLeavePeriodDefined()
     */
    public function testIsLeavePeriodDefined() {
        $value = true;
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_LEAVE_PERIOD_DEFINED)
                 ->will($this->returnValue($value));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->isLeavePeriodDefined();
        $this->assertEquals($value, $returnVal);        
    }

    /**
     * Test setShowPimDeprecatedFields() method
     */
    public function testSetShowPimDeprecatedFields() {
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimDeprecatedFields(true);
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimDeprecatedFields(false);
        
    }
    
    /**
     * Test showPimDeprecatedFields() method
     */
    public function testShowPimDeprecatedFields() {
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED)
                 ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimDeprecatedFields();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED)
                 ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimDeprecatedFields();
        $this->assertFalse($returnVal);
        
    }

    public function testSetShowPimSSN() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SSN, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSSN(true);
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SSN, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSSN(false);        
    }

    public function testShowPimSSN() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SSN)
                 ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSSN();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SSN)
                 ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSSN();
        $this->assertFalse($returnVal);        
    }

    public function testSetShowPimSIN() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SIN, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSIN(true);
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SIN, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSIN(false);          
    }

    public function testShowPimSIN() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SIN)
                 ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSIN();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_SIN)
                 ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSIN();
        $this->assertFalse($returnVal);         
    }

    public function testSetShowPimTaxExemptions() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimTaxExemptions(true);
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimTaxExemptions(false);      
        
        // Exception
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS, 0)
                 ->will($this->throwException(new DaoException()));                
        
        $this->configService->setConfigDao($mockDao);
        
        try {
            $this->configService->setShowPimTaxExemptions(false);      
            $this->fail("Exception expected");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof CoreServiceException);
        }
        
    }

    public function testShowPimTaxExemptions() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS)
                 ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimTaxExemptions();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS)
                 ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);
        
        $returnVal = $this->configService->showPimTaxExemptions();
        $this->assertFalse($returnVal);         
        
        // Exception
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS)
                 ->will($this->throwException(new DaoException()));
        
        $this->configService->setConfigDao($mockDao);
        
        try {
            $returnVal = $this->configService->showPimTaxExemptions();
            $this->fail("Exception expected");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof CoreServiceException);
        }

        
    }
    
    public function testSetSupervisorChainSuported() {
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN, 'Yes');

        $this->configService->setConfigDao($mockDao);

        $this->configService->setSupervisorChainSuported(true);
        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN, 'No');

        $this->configService->setConfigDao($mockDao);

        $this->configService->setSupervisorChainSuported(false);
        
    }
    
    public function testIsSupervisorChainSuported() {

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN)
                 ->will($this->returnValue('Yes'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->isSupervisorChainSuported();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN)
                 ->will($this->returnValue('No'));

        $this->configService->setConfigDao($mockDao);
        
        $returnVal = $this->configService->isSupervisorChainSuported();
        $this->assertFalse($returnVal);
    }
    
    public function testGetDefaultWorkShiftStartTime() {        
        $startTime = '09:30';
        $this->validateGetMethod('getDefaultWorkShiftStartTime', ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME, $startTime);
    }   
    
    public function testSetDefaultWorkShiftStartTime() {  
        $startTime = '11:30';        
        $this->validateSetMethod('setDefaultWorkShiftStartTime', ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME, $startTime);

    }    
    
    public function testGetDefaultWorkShiftEndTime() {        
        $startTime = '09:30';
        $this->validateGetMethod('getDefaultWorkShiftEndTime', ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME, $startTime);
    }   
    
    public function testSetDefaultWorkShiftEndTime() {  
        $startTime = '11:30';        
        $this->validateSetMethod('setDefaultWorkShiftEndTime', ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME, $startTime);

    }    
    
    public function testGetAllValues() {
        $allValues = array('k1' => 'v1', 'k2' => 'v2');
        $mockDao = $this->getMock('ConfigDao', array('getAllValues'));
        $mockDao->expects($this->once())
                 ->method('getAllValues')
                 ->will($this->returnValue($allValues));

        $this->configService->setConfigDao($mockDao);        
        $this->assertEquals($allValues, $this->configService->getAllValues());        
    }
    
    protected function validateGetMethod($method, $key, $expected) {        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('getValue')
                 ->with($key)
                 ->will($this->returnValue($expected));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->$method();
        $this->assertEquals($returnVal, $expected);        
    }
    
    protected function validateSetMethod($method, $key, $value) {        
        $mockDao = $this->getMock('ConfigDao');
        $mockDao->expects($this->once())
                 ->method('setValue')
                 ->with($key, $value);

        $this->configService->setConfigDao($mockDao);

        $this->configService->$method($value);        
    }
    
    public function testSetOpenIdProviderAdded(){
        $value = 'on';        
        $this->validateSetMethod('setOpenIdProviderAdded', ConfigService::KEY_OPENID_PROVIDER_ADDED, $value);

    }
    
    public function testGetOpenIdProviderAdded(){
        $value = 'off';
        $this->validateGetMethod('getOpenIdProviderAdded', ConfigService::KEY_OPENID_PROVIDER_ADDED, $value);
    }
    
}

