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

class SassTest extends PHPUnit_Framework_TestCase {

    public function testCompileInteriorPageCSS() {
        $result = Sass::instance()->compileSCSS(['primaryColor'=>'#5ffffe', 'secondaryColor'=>'#74db77','buttonSuccessColor'=>'#a75b5b','login-social-links-display'=>'none','buttonCancelColor'=>'#17fde3', 'imagesPath' => '"../../webres_61823fa1113264.44536023/themes/default/images/"']);
        $this->assertContains('background-color: #5ffffe;', $result);
        $this->assertContains('background: #74db77;', $result);
        $this->assertContains('background-color: #a75b5b;', $result);
        $this->assertContains('display: none;', $result);
        $this->assertContains('background-color: #17fde3;', $result);
    }

    public function testCompileLoginPageCSS() {
        $result = Sass::instance()->compileLoginSCSS(['login-logo-inner-color'=>'#b5a869','login-logo-outer-color'=>'#9a67d5', 'imagesPath' => '"../images/"']);
        $this->assertContains('stop-color: #b5a869;', $result);
        $this->assertContains('stop-color: #9a67d5;', $result);
    }
}
