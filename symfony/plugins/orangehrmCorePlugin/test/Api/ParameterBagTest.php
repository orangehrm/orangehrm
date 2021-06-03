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

namespace OrangeHRM\Tests\Core\Api;

use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group APIv2
 */
class ParameterBagTest extends TestCase
{
    public function testParameterBag(): void
    {
        $parameterBag = new ParameterBag(['test' => 'param']);
        $this->assertCount(1, $parameterBag);

        $this->assertEquals('param', $parameterBag->get('test'));
        $this->assertEquals(['test' => 'param'], $parameterBag->all());
        $this->assertEquals(['test'], $parameterBag->keys());

        $this->assertNull($parameterBag->get('not-exist'));
        $parameterBag->add(['new' => 'value']);
        $this->assertCount(2, $parameterBag);
        $this->assertEquals('value', $parameterBag->get('new'));
        $parameterBag->replace(['new' => 'new-value']);
        $this->assertEquals('new-value', $parameterBag->get('new'));
        $parameterBag->set('new', 'value');
        $this->assertEquals('value', $parameterBag->get('new'));
        $this->assertTrue($parameterBag->has('new'));
        $parameterBag->remove('new');
        $this->assertFalse($parameterBag->has('new'));

        $parameterBag->set('int', '5');
        $this->assertTrue(is_int($parameterBag->getInt('int')));
        $this->assertEquals(5, $parameterBag->getInt('int'));

        $parameterBag->set('int', '');
        $this->assertTrue(is_int($parameterBag->getInt('int')));
        $this->assertEquals(0, $parameterBag->getInt('int'));

        $parameterBag->set('bool', 'false');
        $this->assertTrue(is_bool($parameterBag->getBoolean('bool')));
        $this->assertEquals(false, $parameterBag->getBoolean('bool'));
    }
}
