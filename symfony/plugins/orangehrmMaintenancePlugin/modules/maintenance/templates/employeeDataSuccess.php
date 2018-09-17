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
 * Boston, MA 02110-1301, USA
 */
?>
<div class="head">
    <h1><?php echo __('Selected Employee'); ?></h1>
</div>
<div class="inner">
    <div class="container">
        <div class="col s12 m3 l3 empImage">
            <img class="circle" style="width:128px; height:128px;"
                 src="<?php echo url_for("pim/viewPhoto?empNumber=" . $empNumber); ?>"/>
        </div>

        <div class="input-field col s12 m12 l6 empImage">
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $firstName; ?>">
            <lable><span>First name</span></lable>
        </div>

        <div class="input-field col s12 m12 l6 empImage">
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $middleName; ?>">
            <lable><span>Middle name</span></lable>
        </div>

        <div class="input-field col s12 m12 l6 empImage">
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $lastName; ?>">
            <lable><span>Last name</span></lable>
        </div>

        <div class="input-field col s12 m12 l6 empImage">
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $employeeId; ?>">
            <lable><span>Employee Id</span></lable>
        </div>
    </div>
</div>
<div class="input-field col s12 m12 l12 " id="purgeButton">
    <input type="submit" value="Purge">
</div>
