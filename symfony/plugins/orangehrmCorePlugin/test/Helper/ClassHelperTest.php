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

namespace OrangeHRM\Tests\Core\Helper;

use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Helper
 */
class ClassHelperTest extends TestCase
{
    public function testGetClass()
    {
        $classHelper = new ClassHelper();
        $className = $classHelper->getClass('ClassHelperTestClass', 'OrangeHRM\\Tests\\Core\\Helper\\');
        $this->assertEquals('OrangeHRM\\Tests\\Core\\Helper\\ClassHelperTestClass', $className);

        $className = $classHelper->getClass('OrangeHRM\\Tests\\Core\\Helper\\ClassHelperTestClass');
        $this->assertEquals('OrangeHRM\\Tests\\Core\\Helper\\ClassHelperTestClass', $className);

        $className = $classHelper->getClass('ClassHelperTestClass');
        $this->assertNull($className);

        $className = $classHelper->getClass('ClassHelperTestClass_Test', 'OrangeHRM\\Tests\\Core\\Helper\\');
        $this->assertNull($className);
    }

    public function testClassExists()
    {
        $classHelper = new ClassHelper();
        $classExists = $classHelper->classExists('ClassHelperTestClass', 'OrangeHRM\\Tests\\Core\\Helper\\');
        $this->assertTrue($classExists);

        $classExists = $classHelper->classExists('OrangeHRM\\Tests\\Core\\Helper\\ClassHelperTestClass');
        $this->assertTrue($classExists);

        $classExists = $classHelper->classExists('ClassHelperTestClass');
        $this->assertFalse($classExists);

        $classExists = $classHelper->classExists('ClassHelperTestClass_Test', 'OrangeHRM\\Tests\\Core\\Helper\\');
        $this->assertFalse($classExists);
    }
}

class ClassHelperTestClass
{

}
