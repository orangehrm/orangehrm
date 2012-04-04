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
<?php echo stylesheet_tag('../orangehrmTimePlugin/css/defineTimesheetPeriodSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/defineTimesheetPeriod'); ?>

<div id="messagebar" style="margin-left: 16px;width: 450px;" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
    <div class="outerbox" style="width: 35%">

        <div class="mainHeading"><h2 id="defineTimesheet"><?php echo __('Define Timesheet Period'); ?></h2></div>
        <form id="definePeriod" method="post">

            <?php echo $form['_csrf_token']; ?>
            <div>
		<table><tr>
		<?php if($isAllowed){?>
		<td id="startDayLabel"><?php  echo __('First Day of Week').' <span class=required>*</span>';?></td>
                <td id="startDays"><?php echo $form['startingDays']->render(array("class" => "drpDown", "maxlength" => 20)); ?></td></tr>
		<?php }else{ ?>

                <br class="clear"/>
		<tr>
            <td><b><?php echo __("Timesheet period start day has not been defined. Please contact HR Admin"); ?></b></td>
		<?php } ?>
		</tr>
		</table>
            </div>
            <?php if($isAllowed){?>
	    <div class="formbuttons">
            
		    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
               
            </div> 
            <?php } ?>
        </form>
    </div>
 <?php if($isAllowed){?>
    <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
 <?php } ?>
    <script type="text/javascript">

    var linkTodefineTimesheetPeriod="<?php echo url_for('time/defineTimesheetPeriod')?>";
    var required_msge = '<?php echo __(ValidationMessages::REQUIRED); ?>';

</script>