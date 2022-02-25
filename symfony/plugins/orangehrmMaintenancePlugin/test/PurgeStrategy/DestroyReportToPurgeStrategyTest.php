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

namespace OrangeHRM\Tests\Maintenance\PurgeStrategy;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\PurgeStrategy\DestroyReportToPurgeStrategy;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class DestroyReportToPurgeStrategyTest extends TestCase
{
    use EntityManagerHelperTrait;

    private DestroyReportToPurgeStrategy $destroyReportToPurgeStrategy;
    protected string $fixture;

    protected function setUp(): void
    {
        $entityClassName = 'ReportTo';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'THIS IS A EXCEPTION']
            ]
        ];
        $infoArray = new InfoArray($strategyInfoArray);

        $this->destroyReportToPurgeStrategy = new DestroyReportToPurgeStrategy($entityClassName, $infoArray);
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/DestroyReportToPurgeStrategy.yml';
        TestDataService::populate($this->fixture);
    }

    public function testPurge(): void
    {
        $this->destroyReportToPurgeStrategy->purge(1);
        $this->getEntityManager()->flush();

        $empReportTo = $this->getRepository(ReportTo::class);
        $this->assertEmpty($empReportTo->findBy(['subordinate' => 1]));
        $this->assertEmpty($empReportTo->findBy(['supervisor' => 1]));
    }
}
