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

namespace OrangeHRM\Tests\Core\Essential;

use Exception;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Essential
 */
class UnitTestsNamespaceTest extends TestCase
{
    public function testUnitTestsNamespace(): void
    {
        $invalidNamespaceClasses = [];
        foreach (get_declared_classes() as $class) {
            preg_match('/^OrangeHRM\\\[\p{L}\p{N}\p{S}]+\\\test\\\[\p{L}\p{N}\p{S}\\\]+$/', $class, $matches);
            if (!empty($matches)) {
                $invalidNamespaceClasses[] = $class;
            }
        }
        if (empty($invalidNamespaceClasses)) {
            $this->assertTrue(true);
        } else {
            $invalidClasses = implode(", \n", $invalidNamespaceClasses);
            throw new Exception(
                "Following test classes having invalid namespace;\n\n" .
                $invalidClasses . "\n\n" . str_repeat('_ ', 20)
            );
        }
    }
}
