<?php

/**
 * @group SecurityAuthentication
 */
class PasswordHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PasswordHelper
     */
    protected $passwordHelper;
    protected $securityAuthConfigService;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->passwordHelper = new PasswordHelper();
        $this->securityAuthConfigService = new SecurityAuthenticationConfigService();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @test Implement testCalculatePasswordStrength().
     */
    public function testCalculatePasswordStrength()
    {
        $this->assertEquals(0, $this->passwordHelper->calculatePasswordStrength('admin'));
        $this->assertEquals(1, $this->passwordHelper->calculatePasswordStrength('admin12'));
        $this->assertEquals(2, $this->passwordHelper->calculatePasswordStrength('admin12D'));
        $this->assertEquals(3, $this->passwordHelper->calculatePasswordStrength('Tr0ub4dour&3'));
        $this->assertEquals(4, $this->passwordHelper->calculatePasswordStrength('bNq8y;a'));
        $this->assertEquals(5, $this->passwordHelper->calculatePasswordStrength('DP55W%u?tC!ftB&'));
    }

    /**
     * @test Implement testGetPasswordStrength().
     */
    public function testGetPasswordStrength()
    {
        $this->assertEquals(3, $this->passwordHelper->getPasswordStrength('Tr0ub4dour&3'));
        $this->assertEquals(4, $this->passwordHelper->getPasswordStrength('bNq8y;a'));
        $this->assertEquals(2, $this->passwordHelper->getPasswordStrength('_ohrmSysAdmin_'));
    }

    /**
     * @test Implement testIsPasswordStrongWithEnforcement().
     */
    public function testIsPasswordStrongWithEnforcement()
    {
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['isPasswordStengthEnforced','getRequiredPasswordStength'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(3))
            ->method('isPasswordStengthEnforced')
            ->will($this->onConsecutiveCalls(true, true, false));
        $this->securityAuthConfigService->expects($this->exactly(2))
            ->method('getRequiredPasswordStength')
            ->will($this->returnValue(4));
        $this->passwordHelper->setSecurityAuthenticationConfigService($this->securityAuthConfigService);


        $this->assertFalse($this->passwordHelper->isPasswordStrongWithEnforcement('admin'));
        $this->assertTrue($this->passwordHelper->isPasswordStrongWithEnforcement('DP55W%u?tC!ftB&'));
        $this->assertTrue($this->passwordHelper->isPasswordStrongWithEnforcement('admin'));
    }
    /**
     * @test Implement testGetColorClass().
     */
    public function testGetColorClass()
    {
        $strengths = ["veryWeak", "weak", "better", "medium", "strong", "strongest"];
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['getPasswordStrengths'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(6))
            ->method('getPasswordStrengths')
            ->will($this->returnValue($strengths));
        $this->passwordHelper->setSecurityAuthenticationConfigService($this->securityAuthConfigService);

        $this->assertEquals('veryWeak', $this->passwordHelper->getColorClass(0));
        $this->assertEquals('weak', $this->passwordHelper->getColorClass(1));
        $this->assertEquals('better', $this->passwordHelper->getColorClass(2));
        $this->assertEquals('medium', $this->passwordHelper->getColorClass(3));
        $this->assertEquals('strong', $this->passwordHelper->getColorClass(4));
        $this->assertEquals('strongest', $this->passwordHelper->getColorClass(5));
    }
}
