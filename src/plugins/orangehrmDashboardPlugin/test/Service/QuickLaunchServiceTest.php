<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Dashboard\Service;

use OrangeHRM\Dashboard\Dao\QuickLaunchDao;
use OrangeHRM\Dashboard\Service\QuickLaunchService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Dashboard
 * @group Service
 */
class QuickLaunchServiceTest extends TestCase
{
    private QuickLaunchService $quickLaunchService;

    protected function setUp(): void
    {
        $this->quickLaunchService = new QuickLaunchService();
    }

    public function testGetQuickLaunchDao(): void
    {
        $this->assertInstanceOf(QuickLaunchDao::class, $this->quickLaunchService->getQuickLaunchDao());
    }
}
