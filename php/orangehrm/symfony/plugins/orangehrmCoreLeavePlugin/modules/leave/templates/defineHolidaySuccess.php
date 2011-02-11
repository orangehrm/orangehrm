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
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>

<!-- Datepicker Plugin-->
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<div class="formpageNarrow">

    <?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>

    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo ($editMode) ? __('Edit') . " " . __('Holiday') : __('Add') . " ".  __('Holiday'); ?></h2></div>

        <div id="errorDiv"> </div>
        <?php if($form->hasErrors())
        {?>
            <?php echo $form['hdnHolidayId']->renderError(); ?>
            <?php echo $form['txtDescription']->renderError(); ?>
            <?php echo $form['txtDate']->renderError(); ?>
            <?php echo $form['chkRecurring']->renderError(); ?>
            <?php echo $form['selLength']->renderError(); ?>
    <?php }?>


        <form id="frmHoliday" name="frmHoliday" method="post" action="<?php echo url_for('leave/defineHoliday') ?>" >
            <?php echo $form['_csrf_token']?>
            <?php if ($editMode)
            { ?>
            <input type="hidden" name="hdnEditMode" id="hdnEditMode" value="yes" />
                <?php } else
            { ?>
            <input type="hidden" name="hdnEditMode" id="hdnEditMode" value="no" />
                <?php } ?>
            <?php echo $form['hdnHolidayId']->render(); ?>
            <?php echo $form['txtDescription']->renderLabel(__('Name') .' <span class="required">*</span>'); ?>
<?php echo $form['txtDescription']->render(array("class" => "formInputText")); ?>
            <div class="errorContainer"></div>
            <br class="clear"/>

            <?php echo $form['txtDate']->renderLabel(__('Date').' <span class="required">*</span>'); ?>
<?php echo $form['txtDate']->render(array("class" => "formDateInput")); ?>
            <input id="DateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
            <div class="errorContainer"></div>
            <br class="clear"/>

            <?php echo $form['chkRecurring']->renderLabel(__('Repeats Annually')); ?>
<?php echo $form['chkRecurring']->render( array('class'=>'formCheckbox')); ?>
            <br class="clear"/>

            <?php echo $form['selLength']->renderLabel(__('Full Day') . '/' . __('Half Day')); ?>
<?php echo $form['selLength']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <div class="formbuttons">
                <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Save'); ?>" />
                <input type="reset" class="resetbutton" value="<?php echo __('Reset'); ?>" id="btnReset" />
                <input type="button" class="savebutton" id="btnBack" value="<?php echo __('Back'); ?>" />
            </div>
        </form>
    </div>
    <div class="requirednotice"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
</div>

    <script type="text/javascript">
    //<![CDATA[
    
    $(document).ready(function() {
        
        var dateFormat	=	'YYYY-MM-DD';

        //Load default Mask if empty
        var hDate 	= 	trim($("#holiday_txtDate").val());
        if(hDate == ''){
            $("#holiday_txtDate").val(dateFormat);
        }
               
        //Validation
        $("#frmHoliday").validate({
            rules: {
                'holiday[txtDate]': { required: true , dateISO: true, validDate: true},
                'holiday[txtDescription]': {required: true, maxlength: 200}
            },
            messages: {
                'holiday[txtDate]':{
                    required:  "<?php echo __("Date is required"); ?>",
                    dateISO:"<?php echo __("Date should be filled in") ?> "+ dateFormat + " <?php echo __("format"); ?>",
                    validDate: "<?php echo __("Invalid Date") ?> "
                },
                'holiday[txtDescription]':{
                    required:  "<?php echo __("Name is required"); ?>",
                    maxlength:"<?php echo __("Name should be less than 200 characters"); ?>"
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.next().next(".errorContainer"));
                error.appendTo(element.next(".errorContainer"));
            },
            invalidHandler: function(form, validator) {
                clearTemplateMessages();
            }
        });

        //clearing error messages after reset
        $("#btnReset").click(function() {
            $("#holiday_txtDescription").attr("class", "formInputText");
            $("#holiday_txtDate").attr("class", "formDateInput hasDatepicker");
            $(".errorContainer").html("");
        });

        /* Valid From Date */
        $.validator.addMethod("validDate", function(value, element) {
        	
            var holiday	=	$('#holiday_txtDate').val();
            holiday = (holiday).split("-");
            if(!validateDate(parseInt(holiday[2],10), parseInt(holiday[1],10), parseInt(holiday[0],10))) {
               return false;
            }else
            	return true;

        });
        
        //Bind date picker
        daymarker.bindElement("#holiday_txtDate", {onSelect: function(date){
                $("#holiday_txtDate").valid();
        }});

        $('#DateBtn').click(function(){
           daymarker.show("#holiday_txtDate");
        });
        
        // Back button
        $('#btnBack').click(function(){            
            window.location.href = '<?php echo url_for('leave/viewHolidayList'); ?>';
        });

        $("#saveBtn").click(function(){
            $("#frmHoliday").submit();
        });

    }); // ready():Ends



    //]]>
    </script>
