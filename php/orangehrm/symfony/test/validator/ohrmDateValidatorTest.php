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

class ohrmDateValidatorTest extends PHPUnit_Framework_TestCase {
    public function testValidate() {

        // Long date UK format
        $validator = new ohrmDateValidator(array('date_format'=>'dd-MM-yyyy'));
        $res = $validator->clean('21-11-2001');
        $this->assertEquals('2001-11-21', $res);

        // invalid month no
        try {
            $res = $validator->clean('21-13-2001');
            $this->fail("invalid month should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }

        // invalid date
        try {
            $res = $validator->clean('32-10-2001');
            $this->fail("invalid date should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }

        // invalid date 2
        try {
            $res = $validator->clean('30-02-2001');
            $this->fail("invalid date should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }

        // wrong format
        try {
            $res = $validator->clean('2001-11-01');
            $this->fail("invalid format should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }


        // Long date US format
        $validator = new ohrmDateValidator(array('date_format'=>'MM-dd-yyyy'));
        $res = $validator->clean('01-31-1990');
        $this->assertEquals('1990-01-31', $res);


        // Long date 'international' format
        $validator = new ohrmDateValidator(array('date_format'=>'yyyy-MM-dd'));
        $res = $validator->clean('1993-03-20');
        $this->assertEquals('1993-03-20', $res);
        
        // short year
        $validator = new ohrmDateValidator(array('date_format'=>'MM/dd/yy'));
        $res = $validator->clean('03-01-03');
        $this->assertEquals('2003-03-01', $res);

        // invalid year - 3 digit when 4 digit required
        $validator = new ohrmDateValidator(array('date_format'=>'MM/dd/yyyy'));
        try {
            $res = $validator->clean('03-01-201');
            $this->fail("invalid format should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }

        // invalid year 5 digit when 4 digit required
        $validator = new ohrmDateValidator(array('date_format'=>'MM/dd/yyyy'));
        try {
            $res = $validator->clean('03-01-20112');
            $this->fail("invalid format should throw validation error");
        } catch (sfValidatorError $e) {
            // expected
        }

    }
}