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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Help\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Help\Service\HelpConfigService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Help
 * @group Service
 */
class HelpConfigServiceTest extends KernelTestCase
{
    private HelpConfigService $helpConfigService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->helpConfigService = new HelpConfigService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmHelpPlugin/test/fixtures/HelpServiceTest.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetHelpProcessorClass(): void
    {
        $helpProcessorClass = $this->helpConfigService->getHelpProcessorClass();
        $this->assertEquals('ZendeskHelpProcessor', $helpProcessorClass);
    }

    public function testGetBaseHelpUrl(): void
    {
        $baseHelpUrl = $this->helpConfigService->getBaseHelpUrl();
        $this->assertEquals("https://starterhelp.orangehrm.com", $baseHelpUrl);
    }
}
