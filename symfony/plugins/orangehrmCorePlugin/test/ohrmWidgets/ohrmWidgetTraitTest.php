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

/**
 * @group ohrmWidget
 */
class ohrmWidgetTraitTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var ohrmWidgetTestWidget|null
     */
    private $ohrmWidgetTestWidget = null;

    protected function setUp(): void
    {
        $this->ohrmWidgetTestWidget = new ohrmWidgetTestWidget();
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEscapedStringWithInteger()
    {
        $getEscapedString = self::getEscapedMethod();
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, [1]);
        $this->assertEquals("'1'", $escapedString);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEscapedStringWithFloat()
    {
        $getEscapedString = self::getEscapedMethod();
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, [98.1111111111]);
        $this->assertEquals("'98.1111111111'", $escapedString);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEscapedStringWithString()
    {
        $getEscapedString = self::getEscapedMethod();
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['String']);
        $this->assertEquals("'String'", $escapedString);

        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1;DROP DATABASE USERS;']);
        $this->assertEquals("'1;DROP DATABASE USERS;'", $escapedString);
    }

    public function testGetEscapedStringWithMultiByteString()
    {
        $getEscapedString = self::getEscapedMethod();
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['测试']);
        $this->assertEquals("'测试'", $escapedString);
    }

    public function testGetEscapedStringWithSpecialChars()
    {
        $getEscapedString = self::getEscapedMethod();
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ["0x5c"]);
        $this->assertEquals("'0x5c'", $escapedString);

        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1;"DROP DATABASE USERS;"']);
        $this->assertEquals('\'1;\"DROP DATABASE USERS;\"\'', $escapedString);

        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1;"DROP DATABASE USERS;"']);
        $this->assertEquals('\'1;\"DROP DATABASE USERS;\"\'', $escapedString);
    }

    public function testGetEscapedCommaSeparated()
    {
        $getEscapedString = self::getEscapedMethod('getEscapedCommaSeparated');
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1,2,3']);
        $this->assertEquals("'1','2','3'", $escapedString);

        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1']);
        $this->assertEquals("'1'", $escapedString);

        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ['1,-1']);
        $this->assertEquals("'1','-1'", $escapedString);

        // if we get this kind of comma within the string it also will split
        $escapedString = $getEscapedString->invokeArgs($this->ohrmWidgetTestWidget, ["'1,4',2,3"]);
        $this->assertEquals("'\'1','4\'','2','3'", $escapedString);
    }

    /**
     * @param string $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private static function getEscapedMethod($methodName = 'getEscapedString'): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass(ohrmWidgetTestWidget::class);
        $method = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }
}

class ohrmWidgetTestWidget implements ohrmEmbeddableWidget
{
    use ohrmWidgetTrait;

    public function embedWidgetIntoForm(sfForm &$form)
    {
    }
}
