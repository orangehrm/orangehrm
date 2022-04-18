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

namespace OrangeHRM\Tests\Core\Service;

use Exception;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * ConfigService Test Class
 * @group Core
 * @group Service
 */
class ConfigServiceTest extends KernelTestCase
{
    private ConfigService $configService;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->configService = new ConfigService();
        $this->createKernel();
    }

    /**
     * Test the getConfigDao() and setConfigDao() method
     */
    public function testGetSetConfigDao(): void
    {
        $dao = $this->configService->getConfigDao();
        $this->assertTrue($dao instanceof ConfigDao);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $this->configService->setConfigDao($mockDao);
        $dao = $this->configService->getConfigDao();
        $this->assertEquals($dao, $mockDao);
    }

    /**
     * Test setShowPimDeprecatedFields() method
     */
    public function testSetShowPimDeprecatedFields(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimDeprecatedFields(true);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimDeprecatedFields(false);
    }

    /**
     * Test showPimDeprecatedFields() method
     */
    public function testShowPimDeprecatedFields(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED)
            ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimDeprecatedFields();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_DEPRECATED)
            ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimDeprecatedFields();
        $this->assertFalse($returnVal);
    }

    public function testSetShowPimSSN(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_SSN, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSSN(true);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_SSN, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSSN(false);
    }

    public function testShowPimSSN(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_SSN)
            ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSSN();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_SSN)
            ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSSN();
        $this->assertFalse($returnVal);
    }

    public function testSetShowPimSIN(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_SIN, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSIN(true);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_SIN, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimSIN(false);
    }

    public function testShowPimSIN(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_SIN)
            ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSIN();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_SIN)
            ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimSIN();
        $this->assertFalse($returnVal);
    }

    public function testSetShowPimTaxExemptions(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS, 1);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimTaxExemptions(true);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS, 0);

        $this->configService->setConfigDao($mockDao);

        $this->configService->setShowPimTaxExemptions(false);

        // Exception
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
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

    public function testShowPimTaxExemptions(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS)
            ->will($this->returnValue('1'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimTaxExemptions();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_PIM_SHOW_TAX_EXEMPTIONS)
            ->will($this->returnValue('0'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->showPimTaxExemptions();
        $this->assertFalse($returnVal);

        // Exception
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
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

    public function testSetSupervisorChainSupported(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN, 'Yes');

        $this->configService->setConfigDao($mockDao);

        $this->configService->setSupervisorChainSupported(true);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN, 'No');

        $this->configService->setConfigDao($mockDao);

        $this->configService->setSupervisorChainSupported(false);
    }

    public function testIsSupervisorChainSupported(): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN)
            ->will($this->returnValue('Yes'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->isSupervisorChainSupported();
        $this->assertTrue($returnVal);

        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with(ConfigService::KEY_INCLUDE_SUPERVISOR_CHAIN)
            ->will($this->returnValue('No'));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->isSupervisorChainSupported();
        $this->assertFalse($returnVal);
    }

    public function testGetDefaultWorkShiftStartTime(): void
    {
        $startTime = '09:30';
        $this->validateGetMethod(
            'getDefaultWorkShiftStartTime',
            ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME,
            $startTime
        );
    }

    public function testSetDefaultWorkShiftStartTime(): void
    {
        $startTime = '11:30';
        $this->validateSetMethod(
            'setDefaultWorkShiftStartTime',
            ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME,
            $startTime
        );
    }

    public function testGetDefaultWorkShiftEndTime(): void
    {
        $startTime = '09:30';
        $this->validateGetMethod(
            'getDefaultWorkShiftEndTime',
            ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME,
            $startTime
        );
    }

    public function testSetDefaultWorkShiftEndTime(): void
    {
        $startTime = '11:30';
        $this->validateSetMethod(
            'setDefaultWorkShiftEndTime',
            ConfigService::KEY_ADMIN_DEFAULT_WORKSHIFT_END_TIME,
            $startTime
        );
    }

    public function testGetAllValues(): void
    {
        $allValues = ['k1' => 'v1', 'k2' => 'v2'];
        $mockDao = $this->getMockBuilder(ConfigDao::class)
            ->setMethods(['getAllValues'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getAllValues')
            ->will($this->returnValue($allValues));

        $this->configService->setConfigDao($mockDao);
        $this->assertEquals($allValues, $this->configService->getAllValues());
    }

    protected function validateGetMethod($method, $key, $expected): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('getValue')
            ->with($key)
            ->will($this->returnValue($expected));

        $this->configService->setConfigDao($mockDao);

        $returnVal = $this->configService->$method();
        $this->assertEquals($returnVal, $expected);
    }

    protected function validateSetMethod($method, $key, $value): void
    {
        $mockDao = $this->getMockBuilder(ConfigDao::class)->getMock();
        $mockDao->expects($this->once())
            ->method('setValue')
            ->with($key, $value);

        $this->configService->setConfigDao($mockDao);

        $this->configService->$method($value);
    }

    public function testSetOpenIdProviderAdded(): void
    {
        $value = 'on';
        $this->validateSetMethod('setOpenIdProviderAdded', ConfigService::KEY_OPENID_PROVIDER_ADDED, $value);
    }

    public function testGetOpenIdProviderAdded(): void
    {
        $value = 'off';
        $this->validateGetMethod('getOpenIdProviderAdded', ConfigService::KEY_OPENID_PROVIDER_ADDED, $value);
    }

    public function testGetTimeSheetPeriodConfig(): void
    {
        $startDay = '1';
        $this->validateGetMethod(
            'getTimeSheetPeriodConfig',
            ConfigService::KEY_TIMESHEET_PERIOD_AND_START_DATE,
            $startDay
        );
    }

    public function testSetTimeSheetPeriodConfig(): void
    {
        $startDay = '2';
        $this->validateSetMethod(
            'setTimeSheetPeriodConfig',
            ConfigService::KEY_TIMESHEET_PERIOD_AND_START_DATE,
            $startDay
        );
    }

    public function testSetTimeSheetPeriodSetValue(): void
    {
        $startDay = 'Yes';
        $this->validateSetMethod(
            'setTimeSheetPeriodSetValue',
            ConfigService::KEY_TIMESHEET_PERIOD_SET,
            $startDay
        );
    }
}
