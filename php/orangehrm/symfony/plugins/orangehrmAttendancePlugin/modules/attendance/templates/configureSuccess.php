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
?>
<?php echo javascript_include_tag('../orangehrmAttendancePlugin/js/configure'); ?>
<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/configureSuccess'); ?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 470px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div class="outerbox"  style="width: 500px" >
    <div class="maincontent">
        <div class="mainHeading">
            <h2><?php echo __('Attendance Configuration'); ?></h2>
        </div>
        <br class="clear">
        <form  id="configureForm" action=""  method="post">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['configuration1']->render(); ?>
            <?php echo $form['configuration1']->renderLabel(__('Employee can change current time when punching in/out')); ?>
            <br class="clear"/>

            <?php echo $form['configuration2']->render(); ?>
            <?php echo $form['configuration2']->renderLabel(__('Employee can edit/delete own attendance records')); ?>
            <br class="clear"/>

            <?php echo $form['configuration3']->render(); ?>
            <?php echo $form['configuration3']->renderLabel(__('Supervisor can add/edit/delete attendance records of subordinates')); ?>
            <br class="clear"/>
            <br class="clear"/>

            &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" class="saveConfiguration" name="button" id="btnSave"
                                            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                            value="<?php echo __('Save'); ?>" />

        </form>
        <br class="clear">
    </div>
</div>