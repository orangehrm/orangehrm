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

namespace OrangeHRM\Tests\I18N\Dao;

use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\I18N\Dao\I18NDao;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group I18N
 * @group Dao
 */
class I18NDaoTest extends KernelTestCase
{
    private I18NDao $i18NDao;

    protected function setUp(): void
    {
        $this->i18NDao = new I18NDao();
    }

    public function testGetI18languages(): void
    {
        $i18languages = $this->i18NDao->getI18Languages();


        $this->assertInstanceOf(I18NLanguage::class, $i18languages[0]);
        $this->assertCount(8, $i18languages);
        $this->assertEquals('Chinese (Simplified, China) - 中文（简体，中国）', $i18languages[0]->getName());
    }
}
