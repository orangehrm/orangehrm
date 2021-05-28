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

use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Vue
 */
class PropTest extends TestCase
{
    public function testProp(): void
    {
        $propName = 'test-vue-prop';
        $prop = new Prop($propName);
        $this->assertEquals($propName, $prop->getName());

        $prop->setName('vue-prop');
        $this->assertEquals('vue-prop', $prop->getName());

        $prop->setType(Prop::TYPE_STRING);
        $this->assertEquals(Prop::TYPE_STRING, $prop->getType());

        $prop->setType(Prop::TYPE_NUMBER);
        $this->assertEquals(Prop::TYPE_NUMBER, $prop->getType());

        $prop->setType(Prop::TYPE_BOOLEAN);
        $this->assertEquals(Prop::TYPE_BOOLEAN, $prop->getType());

        $prop->setType(Prop::TYPE_ARRAY);
        $this->assertEquals(Prop::TYPE_ARRAY, $prop->getType());

        $prop->setType(Prop::TYPE_OBJECT);
        $this->assertEquals(Prop::TYPE_OBJECT, $prop->getType());
    }

    public function testPropWithValue(): void
    {
        $propName = 'test-vue-prop';
        $prop = new Prop($propName, Prop::TYPE_STRING, 'test');
        $this->assertEquals('test', $prop->getRawValue());
        $this->assertEquals('"test"', $prop->getValue());

        $prop = new Prop($propName, Prop::TYPE_NUMBER, 1);
        $this->assertEquals(1, $prop->getRawValue());
        $this->assertEquals('1', $prop->getValue());

        $prop = new Prop($propName, Prop::TYPE_NUMBER, 1000.9999);
        $this->assertEquals(1000.9999, $prop->getRawValue());
        $this->assertEquals('1000.9999', $prop->getValue());

        $prop = new Prop($propName, Prop::TYPE_BOOLEAN, true);
        $this->assertEquals(true, $prop->getRawValue());
        $this->assertEquals('true', $prop->getValue());
        $prop->setRawValue(false);
        $this->assertEquals(false, $prop->getRawValue());
        $this->assertEquals('false', $prop->getValue());

        $prop = new Prop($propName, Prop::TYPE_ARRAY, [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $prop->getRawValue());
        $this->assertEquals('[1,2,3]', $prop->getValue());

        $prop = new Prop($propName, Prop::TYPE_OBJECT, ['id' => 1, 'name' => 'test']);
        $this->assertEquals(['id' => 1, 'name' => 'test'], $prop->getRawValue());
        $this->assertEquals('{"id":1,"name":"test"}', $prop->getValue());
    }
}
