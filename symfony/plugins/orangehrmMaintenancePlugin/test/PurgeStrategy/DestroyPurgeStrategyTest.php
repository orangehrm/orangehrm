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
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\PurgeStrategy\DestroyPurgeStrategy;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class DestroyPurgeStrategyTest extends TestCase
{
    use EntityManagerHelperTrait;

    private DestroyPurgeStrategy $destroyPurgeStrategy;

    protected function setUp(): void
    {
        $entityClassName = 'EmpPicture';
        $strategyInfoArray = [
            'match_by' => [
                ['match' => 'employee']
            ],
        ];
        $infoArray = new InfoArray($strategyInfoArray);

        $this->destroyPurgeStrategy = new DestroyPurgeStrategy($entityClassName, $infoArray);
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmMaintenancePlugin/test/fixtures/DestroyPurgeStrategy.yml';
        TestDataService::populate($this->fixture);
    }

    public function testPurge(): void
    {
        $this->destroyPurgeStrategy->purge(1);
        $this->getEntityManager()->flush();

        $empPictures = $this->getRepository(EmpPicture::class)->findBy(['employee' => 1]);
        $this->assertEmpty($empPictures);
    }
}
