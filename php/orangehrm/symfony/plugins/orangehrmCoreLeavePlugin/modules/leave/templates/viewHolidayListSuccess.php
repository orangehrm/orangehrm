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
<?php use_stylesheet('../orangehrmCoreLeavePlugin/css/viewHolidayListSuccess'); ?>

<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>

<div id="flash_message_wrapper" style="width: 630px;">
    <?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>
</div>
<div id="errorDiv">
<?php
    if ($searchForm->hasErrors()) { 
        echo __("Please Correct The Following Errors");
    }
?>
</div>

<div class="outerbox" style="width: 600px;">
    <div class="mainHeading"><h2><?php echo __('Holidays'); ?></h2></div>
    
    <form id="frmHolidaySearch" name="frmHolidaySearch" method="post" action="<?php echo url_for('leave/viewHolidayList') ?>" >            
        <?php echo $searchForm->render() ?>
        <br class="clear"/>

        <div class="formbuttons paddingLeft">
        <input type="button" name="btnSearch" id="btnSearch" value="<?php echo __("Search") ?>" class="savebutton" />
        </div>
    </form>

</div>
   <br class="clear" />
<?php include_component('core', 'ohrmList'); ?>

   <!--
                               <?php //echo set_datepicker_date_format($holiday->getDate()); ?>
                            <?php //echo __($daysLenthList[$holiday->getLength()]); ?>
                            <?php //echo __($yesNoList[$holiday->getRecurring()]); ?>
   -->

<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
    <?php echo __("Do you want to delete the specific holiday(s)") . "?"; ?>
    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Delete'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

<script type="text/javascript"> 
//<![CDATA[    
    var defineHolidayUrl = '<?php echo url_for('leave/defineHoliday'); ?>';
//]]>    
</script>
    
<script type="text/javascript"> 
//<![CDATA[ 
    $(document).ready(function() {

        $("#btnSearch").click(function(){
            $("#frmHolidaySearch").submit();
        });

        // Add button
        $('#btnAdd').click(function(){
            window.location.href = defineHolidayUrl;
        });

        /* Delete button */
        $('#btnDel').click(function(){

            $('#deleteConfirmation').dialog('open');
            return false;

        });
        $("#deleteConfirmation").dialog({
            autoOpen: false,
            modal: true,
            width: 325,
            height: 20,
            position: 'middle',
            open: function() {
                $('#dialogCancelBtn').focus();
            }
        });
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        $('#dialogCancelBtn').click(function() {    
            $("#deleteConfirmation").dialog("close");
        });

    }); // ready():Ends

//]]>
</script>
