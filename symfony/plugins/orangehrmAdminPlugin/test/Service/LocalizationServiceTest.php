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

namespace OrangeHRM\Admin\test\Service;

use Exception;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Service\I18NService;
use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Admin
 * @group Service
 */
class LocalizationServiceTest extends TestCase
{
    private LocalizationService $localizationService;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->localizationService = new LocalizationService();
    }

    public function testGetLocalizationDateFormats(): void
    {
        $this->assertCount(11, $this->localizationService->getLocalizationDateFormats());
    }

    public function testGetSupportedLanguages(): void
    {
        $expectedResult = ['A', 5, '%'];
        $i18NService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getLanguagesArray'])
            ->getMock();
        $i18NService->expects($spy = $this->once())
            ->method('getLanguagesArray')
            ->will($this->returnValue($expectedResult));
        $this->localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getI18NService'])
            ->getMock();
        $this->localizationService->expects($this->once())
            ->method('getI18NService')
            ->will($this->returnValue($i18NService));
        $this->assertEquals($expectedResult, $this->localizationService->getSupportedLanguages());
    }
}
