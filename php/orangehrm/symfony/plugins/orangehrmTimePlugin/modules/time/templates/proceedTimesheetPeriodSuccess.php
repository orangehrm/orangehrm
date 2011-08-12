<?php /**

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
 */ ?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
    <div class="outerbox" style="width: 35%">

        <div class="mainHeading"><h2 id="defineTimesheet"><?php echo __('Define Timesheet Period'); ?></h2></div>
            <div>
		<br class="clear">
		<b><?php echo __("Timesheet period start day has been set")?></b><br class="clear">
		<?php echo link_to(__('Proceed with Time Module'),'time/viewEmployeeTimesheet');?>
		<br class="clear">
		<br class="clear">
            </div>
    </div>

