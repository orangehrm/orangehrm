<?php

/**
 * @group SecurityAuthentication
 */
class SecurityAuthenticationConfigServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityAuthenticationConfigService
     */
    protected $securityAuthConfigService;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->securityAuthConfigService = new SecurityAuthenticationConfigService();
        $this->securityAuthConfigService = new ConfigService();
        $user = new SystemUser();
        $user->setId(5);
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['getUser'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->any())
                ->method('getUser')
                ->will($this->returnValue($user));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @test Implement testIsPluginEnabled().
     */
    public function testIsPluginEnabled()
    {
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['_getConfigValue'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(2))
            ->method('_getConfigValue')
            ->with('authentication.status')
            ->will($this->onConsecutiveCalls('Enable', 'Disable'));

        $this->assertTrue($this->securityAuthConfigService->isPluginEnabled());
        $this->assertFalse($this->securityAuthConfigService->isPluginEnabled());
    }

    /**
     * @test testIsPasswordStrengthEnforced().
     */
    public function testIsPasswordStrengthEnforced()
    {
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['_getConfigValue','isPluginEnabled'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(2))
            ->method('_getConfigValue')
            ->with('authentication.enforce_password_strength')
            ->will($this->onConsecutiveCalls('on', 'off'));
        $this->securityAuthConfigService->expects($this->exactly(3))
            ->method('isPluginEnabled')
            ->will($this->onConsecutiveCalls(true, true, false));

        $this->assertTrue($this->securityAuthConfigService->isPasswordStengthEnforced());
        $this->assertFalse($this->securityAuthConfigService->isPasswordStengthEnforced());
        $this->assertFalse($this->securityAuthConfigService->isPasswordStengthEnforced());
    }

    /**
     * @test testGetRequiredPasswordStrength().
     */
    public function testGetRequiredPasswordStrength()
    {
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['_getConfigValue'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(7))
            ->method('_getConfigValue')
            ->with('authentication.default_required_password_strength')
            ->will($this->onConsecutiveCalls('', "veryWeak", "weak", "better", "medium", "strong", "strongest"));

        $this->assertEquals(0, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(0, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(1, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(2, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(3, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(4, $this->securityAuthConfigService->getRequiredPasswordStength());
        $this->assertEquals(5, $this->securityAuthConfigService->getRequiredPasswordStength());
    }

    /**
     * @test testGetCurrentPasswordStrength().
     */
    public function testGetCurrentPasswordStrength()
    {
        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
            ->setMethods(['_getConfigValue','getRequiredPasswordStength'])
            ->getMock();
        $this->securityAuthConfigService->expects($this->exactly(6))
            ->method('getRequiredPasswordStength')
            ->will($this->onConsecutiveCalls(0, 1, 2, 3, 4, 5));

        $this->assertEquals("veryWeak", $this->securityAuthConfigService->getCurrentPasswordStrength());
        $this->assertEquals("weak", $this->securityAuthConfigService->getCurrentPasswordStrength());
        $this->assertEquals("better", $this->securityAuthConfigService->getCurrentPasswordStrength());
        $this->assertEquals("medium", $this->securityAuthConfigService->getCurrentPasswordStrength());
        $this->assertEquals("strong", $this->securityAuthConfigService->getCurrentPasswordStrength());
        $this->assertEquals("strongest", $this->securityAuthConfigService->getCurrentPasswordStrength());
    }

    /**
     * @test testGetBlockedDuration().
     */
//    public function testGetBlockedDuration() {
//        $this->securityAuthConfigService = $this->getMockBuilder('SecurityAuthenticationConfigService')
//            ->setMethods(array('_getConfigValue','isPluginEnabled','isBlockingEnabled'))
//            ->getMock();
//        $this->securityAuthConfigService->expects($this->once())
//            ->method('_getConfigValue')
//            ->with('authentication.blocked_duration')
//            ->will($this->onConsecutiveCalls('1:13:21'));
//        $this->securityAuthConfigService->expects($this->exactly(3))
//            ->method('isPluginEnabled')
//            ->will($this->onConsecutiveCalls(true, true, false));
//        $this->securityAuthConfigService->expects($this->exactly(2))
//            ->method('isBlockingEnabled')
//            ->will($this->onConsecutiveCalls(true, false));
//
//        $this->assertEquals('1:13:21', $this->securityAuthConfigService->getBlockedDuration());
//        $this->assertEquals('', $this->securityAuthConfigService->getBlockedDuration());
//        $this->assertEquals('', $this->securityAuthConfigService->getBlockedDuration());
//
//    }
}
