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

echo stylesheet_tag('../orangehrmPimPlugin/css/configPimSuccess'); ?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 470px;">
	<span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Configure PIM'); ?></h2></div>
    <div>
        <form id="frmConfigPim" name="frmConfigPim" method="post" action="<?php echo url_for('pim/configPim') ?>" >
            <table cellspacing="0" cellpadding="0" border="0" class="tableArrange">
                <tr>
                    <td width="40%"><?php echo __('Show Deprecated Fields'); ?></td>
                    <td><?php echo $form['chkDeprecateFields']->render(); ?></td>
                </tr>
            </table>
            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
//we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
$(document).ready(function() {
    $("#configPim_chkDeprecateFields").attr('disabled', 'disabled');
    
    $("#btnSave").click(function() {
        if($("#btnSave").attr('value') == "<?php echo __("Edit"); ?>") {
            $("#btnSave").attr('value', "<?php echo __("Save"); ?>");
            $("#configPim_chkDeprecateFields").removeAttr('disabled');
            return;
        }

        if($("#btnSave").attr('value') == "<?php echo __("Save"); ?>") {
            $("#frmConfigPim").submit();
        }
    });
});
//]]>
</script>
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

echo stylesheet_tag('../orangehrmPimPlugin/css/configPimSuccess'); ?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 470px;">
	<span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Configure PIM'); ?></h2></div>
    <div>
        <form id="frmConfigPim" name="frmConfigPim" method="post" action="<?php echo url_for('pim/configPim') ?>" >
            <table cellspacing="0" cellpadding="0" border="0" class="tableArrange">
                <tr>
                    <td width="40%"><?php echo __('Show Deprecated Fields'); ?></td>
                    <td><?php echo $form['chkDeprecateFields']->render(); ?></td>
                </tr>
            </table>
            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
//we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
$(document).ready(function() {
    $("#configPim_chkDeprecateFields").attr('disabled', 'disabled');
    
    $("#btnSave").click(function() {
        if($("#btnSave").attr('value') == "<?php echo __("Edit"); ?>") {
            $("#btnSave").attr('value', "<?php echo __("Save"); ?>");
            $("#configPim_chkDeprecateFields").removeAttr('disabled');
            return;
        }

        if($("#btnSave").attr('value') == "<?php echo __("Save"); ?>") {
            $("#frmConfigPim").submit();
        }
    });
});
//]]>
</script>