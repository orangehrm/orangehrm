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
        <div class="inner empImage">
            <label id="image_label"><span id="full_name"><?php echo $firstName . ' ' . $lastName; ?></span></label>
            <img class="circle" style="width:128px; height:128px;"
                 src="<?php echo url_for("pim/viewPhoto?empNumber=" . $empNumber); ?>"/>
        </div>

        <div class="empImage">
            <label><span><?php echo __('First Name'); ?></span></label>
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $firstName; ?>">
        </div>

        <div class="empImage">
            <label><span><?php echo __('Middle Name'); ?></span></label>
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $middleName; ?>">
        </div>

        <div class="empImage">
            <label><span><?php echo __('Last Name'); ?></span></label>
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $lastName; ?>">
        </div>

        <div class="empImage">
            <label><span><?php echo __('Employee Id'); ?></span></label>
            <input id="first_name" type="text" disabled="disabled" value="<?php echo $employeeId; ?>">
        </div>
    </div>
</div>
<div class="" id="purgeButton">
    <input type="submit" id="btnDelete" name="btnDelete" value="<?php echo __('Purge'); ?>" data-toggle="modal"
           data-target="#deleteConfModal">
</div>


