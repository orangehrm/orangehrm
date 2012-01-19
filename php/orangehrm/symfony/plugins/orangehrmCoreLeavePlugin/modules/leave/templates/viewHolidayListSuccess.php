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
    <?php //echo isset($templateMessage)?templateMessage($templateMessage):''; ?>
</div>
<div id="errorDiv"></div>
<?php
    if ($searchForm->hasErrors()) { 

        $widgets = $searchForm->getWidgetSchema()->getFields();

        foreach ($widgets as $identifier => $wisget) {
            //echo $searchForm[$identifier]->renderError();
        }
    }
?>

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

<div class="outerbox">

    <form method="post" name="frmHolidayList" id="frmHolidayList" name ="frmHolidayList" action="<?php echo url_for('leave/holidayList'); ?>">

        <div class="actionbar">

            <div class="actionbuttons">

                <input type="button" class="addbutton" name="btnAdd" id="btnAdd" value="<?php echo __('Add'); ?>" />
                <input type="button" class="delbutton" name="btnDel" id="btnDel" value="<?php echo __('Delete'); ?>" />
                <input type="hidden" name="hdnEditId" id="hdnEditId" value="" />
            </div> <!-- End of actionbuttons -->

        </div> <!-- End of actionbar -->

        <br class="clear" />

        <table border="0" cellpadding="0" cellspacing="0" class="data-table">

            <thead>
                <tr>
                    <td width="50">
                        <input type="checkbox" class="innercheckbox" name="allCheck" id="allCheck" value="" />
                    </td>
                    <td><?php echo __('Name of Holiday'); ?></td>
                    <td><?php echo __('Date'); ?></td>
                    <td><?php echo __('Full Day') . "/" . __('Half Day'); ?></td>
                    <td><?php echo __('Repeats Annually'); ?></td>
                </tr>
            </thead>

            <tbody>

                <?php $rowClass = 'odd' ?>
                <?php

                ?>

                <?php foreach ($holidayList as $holiday)
                { ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                

                <tr class="<?php echo $rowClass; ?>">
                    <td>
                        <input type="checkbox" class="innercheckbox" name="chkHolidayId[]" value="<?php echo $holiday->getId(); ?>" />
                    </td>
                    <td>
                       <a href="<?php echo url_for('leave/defineHoliday/?hdnEditId=' . $holiday->getId());?>"><?php echo $holiday->getDescription(); ?></a>
                    </td>
                    <td>
                            <?php echo set_datepicker_date_format($holiday->getDate()); ?>
                    </td>

                    <td>
                            <?php echo __($daysLenthList[$holiday->getLength()]); ?>
                    </td>
                    <td>
                            <?php echo __($yesNoList[$holiday->getRecurring()]); ?>
                    </td>
                        <?php $rowClass = $rowClass=='odd'?'even':'odd'; ?>

                </tr>

                    <?php } // End of $leaveTypeList foreach ?>

            </tbody>
            
        </table>

    </form>
    
</div> <!-- End of outerbox -->

    <div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
        <?php echo __("Do you want to delete the specific holiday(s)") . "?"; ?>
        <div class="dialogButtons">
            <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Delete'); ?>" />
            <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
        </div>
    </div>

<script type="text/javascript"> 

    $(document).ready(function() {

        $("#btnSearch").click(function(){
            $("#frmHolidaySearch").submit();
        });

        // Add button
        $('#btnAdd').click(function(){
            window.location.href = '<?php echo url_for('leave/defineHoliday'); ?>';
        });

        /* Delete button */
        $('#btnDel').click(function(){

            $('#frmHolidayList').attr('action', '<?php echo url_for('leave/deleteHoliday'); ?>');
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
            document.frmHolidayList.submit();
        });
        $('#dialogCancelBtn').click(function() {    
            $("#deleteConfirmation").dialog("close");
        });


        /* Checkbox behavior */
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')) {
                $('.innercheckbox').attr('checked', true);
                $('#btnDel').attr('disabled', false);
            } else {
                $('.innercheckbox').attr('checked', false);
            }
        });

        $(".innercheckbox").click(function() {
            if(!($(this).attr('checked'))) {
                $('#allCheck').attr('checked', false);
            }
            $('#btnDel').attr('disabled', false);
        });



    }); // ready():Ends


</script>
