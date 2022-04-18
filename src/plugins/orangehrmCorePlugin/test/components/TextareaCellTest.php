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
 * @group Core
 * @group ListComponent
 */
class TextareaCellTest extends PHPUnit\Framework\TestCase
{
    private $textareaCell = null;

    protected function setUp(): void
    {
        $this->textareaCell = new TextareaCell();
    }

    public function test__toString()
    {
        $dataObject = new LabelCellTestDataObject();
        $dataObject->getName = 'Kayla Abbey';

        $this->textareaCell->setDataObject($dataObject);
        $this->textareaCell->setProperties(
            [
                'getter' => 'getDescription',
                'props' => [
                    'rows' => '2',
                    'cols' => '50',
                    'class' => 'translated-textarea',
                    'disabled' => true,
                    'maxlength' => 1500,
                ]
            ]
        );

        $this->assertEquals(
            '<textarea rows="2" cols="50" class="translated-textarea" disabled="1" maxlength="1500">Sample class</textarea>',
            $this->textareaCell->__toString()
        );
    }
}

class TextareaCellTestDataObject
{
    public function getId()
    {
        return 1;
    }

    public function getDescription()
    {
        return 'Sample class';
    }

    public function getObject()
    {
        return new self;
    }
}
