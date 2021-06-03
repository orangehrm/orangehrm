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

namespace OrangeHRM\Tests\Core\Vue;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Vue
 */
class ComponentTest extends TestCase
{
    public function testComponent(): void
    {
        $componentName = 'test-vue-page';
        $component = new Component($componentName);
        $this->assertEquals($componentName, $component->getName());

        $component = new Component($componentName, []);
        $component->setName('vue-page');
        $this->assertEquals('vue-page', $component->getName());
        $this->assertEmpty($component->getProps());
    }

    public function testComponentWithProps(): void
    {
        $componentName = 'test-vue-page';
        $component = new Component($componentName, [new Prop('id'), new Prop('name')]);
        $this->assertEquals(2, count($component->getProps()));

        $component->addProp(new Prop('description'));
        $this->assertEquals(3, count($component->getProps()));

        $component->setProps([new Prop('id')]);
        $this->assertEquals(1, count($component->getProps()));
    }
}
