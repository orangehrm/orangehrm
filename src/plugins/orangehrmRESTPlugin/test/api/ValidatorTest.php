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
use Orangehrm\Rest\Api\Validator;

/**
 *
 * @group API
 */

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testValidateWithOneProperty() {
        $rule = array(
            'name'=> array('Length'=>array(0,10))
        );
        $values = array('name'=>'Mike');
        $result = Validator::validate($values,$rule);
        $this->assertTrue($result);
    }

    public function testValidationWithMoreProperty() {
        $rule = array(
            'firstName'=> array('StringType'=>true,'NoWhitespace'=>true,'Length'=>array(1,10)),
            'dob'=>array('Date'=>true)
        );
        $values = array('firstName'=>'Mike','dob'=>'1979-04-04');
        $result = Validator::validate($values,$rule);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Orangehrm\Rest\Api\Exception\InvalidParamException
     */
    public function testValidationWithWhiteSpace() {
        $rule = array(
            'firstName'=> array('StringType'=>true,'notEmpty' => true,'NoWhitespace'=>true,'Length'=>array(1,10)),
            'dob'=>array('Date'=>true,'notEmpty' => true)
        );
        $values = array('firstName'=>'Mike J','dob'=>'1979-04-04');
        Validator::validate($values,$rule);
    }

    public function testValidationWhenValueIsEmpty() {
        $rule = array(
            'firstName'=> array('StringType'=>true,'NoWhitespace'=>true,'Length'=>array(1,10)),
            'dob'=>array('Date'=>true)
        );
        $values = array('dob'=>'1979-04-04');
        $result = Validator::validate($values,$rule);
        $this->assertTrue($result);
    }
}
