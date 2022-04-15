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

namespace Entity;

use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class I18NLanguageTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([I18NLanguage::class]);
    }

    public function testI18NLanguageEntity(): void
    {
        $language = new I18NLanguage();
        $language->setName('Valerian');
        $language->setCode('VLR');
        $language->setEnabled(true);
        $language->setAdded(true);
        $language->setModifiedAt(new \DateTime('2021-08-01T10:00:00'));
        $this->persist($language);

        /** @var I18NLanguage $language */
        $language = $this->getRepository(I18NLanguage::class)->find(1);
        $this->assertEquals('Valerian', $language->getName());
        $this->assertEquals('VLR', $language->getCode());
        $this->assertEquals(true, $language->isEnabled());
        $this->assertEquals(true, $language->isAdded());
        $this->assertEquals('2021-08-01 10:00:00', $language->getModifiedAt()->format('Y-m-d H:i:s'));
    }
}
