<link href="<?php echo public_path('../../themes/orange/css/leave.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<style type="text/css">
    .error_list {
        color: #ff0000;
    }
</style>

<?php use_stylesheets_for_form($applyLeaveForm); ?>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/applyLeaveSuccess'); ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<?php if (!empty($overlapLeave)) {
    ?>
    <div id="duplicateWarning" class="confirmBox" style="margin-left:18px;">
        <div class="confirmInnerBox">
            <?php echo __('Overlapping Leave Request Found') ?>
        </div>
    </div>

    <table border="0" cellspacing="0" cellpadding="0" style="margin-left: 18px;" class="simpleList">
        <thead>
            <tr>
                <th width="100px" class="tableMiddleMiddle"><?php echo __("Date") ?></th>
                <th width="50px" class="tableMiddleMiddle"><?php echo __("No of Hours") ?></th>
                <th width="100px" class="tableMiddleMiddle"><?php echo __("Leave Period") ?></th>
                <th width="90px" class="tableMiddleMiddle"><?php echo __("Leave Type") ?></th>
                <th width="100px" class="tableMiddleMiddle"><?php echo __("Status") ?></th>
                <th width="150px" class="tableMiddleMiddle"><?php echo __("Comments") ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($overlapLeave as $leave) {
                ?>
                <tr>
                    <td class="odd"><?php echo set_datepicker_date_format($leave->getLeaveDate()) ?></td>
                    <td class="odd"><?php echo $leave->getLeaveLengthHours() ?></td>
                    <td class="odd"><?php echo set_datepicker_date_format($leave->getLeaveRequest()->getLeavePeriod()->getStartDate()) ?></td>
                    <td class="odd"><?php echo $leave->getLeaveRequest()->getLeaveTypeName() ?></td>
                    <td class="odd"><?php echo __($leave->getTextLeaveStatus()); ?></td>
                    <td class="odd"><?php echo $leave->getLeaveComments() ?></td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
<?php } ?>
<div class="formpage">
    <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
    <div id="processing"></div>
    <?php if (count($applyLeaveForm->leaveTypeList) > 1) {
        ?>
        <div class="outerbox">
            <div class="mainHeading"><h2 class="paddingLeft"><?php echo __('Apply Leave') ?></h2></div>

            <?php if ($applyLeaveForm->hasErrors()) {
                ?>
                <?php echo $applyLeaveForm['txtEmpID']->renderError(); ?>
                <?php echo $applyLeaveForm['txtLeaveType']->renderError(); ?>
                <?php echo $applyLeaveForm['txtFromDate']->renderError(); ?>
                <?php echo $applyLeaveForm['txtToDate']->renderError(); ?>
                <?php echo $applyLeaveForm['txtLeaveTotalTime']->renderError(); ?>
                <?php echo $applyLeaveForm['txtComment']->renderError(); ?>
                <?php echo $applyLeaveForm['txtFromTime']->renderError(); ?>
            <?php } ?>
            <form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">
                <?php echo $applyLeaveForm->render(); ?>
                <!-- here we have the button -->
                <div class="formbuttons paddingLeft">
                    <input type="button" class="applybutton" id="saveBtn" value="<?php echo __('Apply'); ?>" title="<?php echo __('Apply'); ?>"/>
                </div>
            </form>
        </div>
        <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    <?php } ?>
</div>
<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var leaveBalanceUrl = '<?php echo url_for('leave/getLeaveBalanceAjax'); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';

    $(document).ready(function() {
        $.datepicker.setDefaults({showOn: 'click'});

        showTimeControls(false);

        var rDate = trim($("#applyleave_txtFromDate").val());
        if (rDate == '') {
            $("#applyleave_txtFromDate").val(displayDateFormat);
        }

        updateLeaveBalance();
        
        $('#applyleave_txtFromDate').change(function() {
            updateLeaveBalance();
        });

        //Bind date picker
        daymarker.bindElement("#applyleave_txtFromDate",
        {
            onSelect: function(date){
                fromDateBlur(date);
                updateLeaveBalance();
            },
            dateFormat : datepickerDateFormat,
            onClose: function() {
                $(this).valid();
            }
        });

        $('#applyleave_txtFromDate_Button').click(function(){
            daymarker.show("#applyleave_txtFromDate");

        });
        $('#applyleave_txtFromDate').click(function(){
            daymarker.show("#applyleave_txtFromDate");

        });

        var tDate = trim($("#applyleave_txtToDate").val());
        if (tDate == '') {
            $("#applyleave_txtToDate").val(displayDateFormat);
        }

        //Bind date picker
        daymarker.bindElement("#applyleave_txtToDate",
        {
            onSelect: function(date){
                toDateBlur(date)
            },
            dateFormat : datepickerDateFormat,
            onClose: function() {
                $(this).valid();
            }
        });

        $('#applyleave_txtToDate_Button').click(function(){
            daymarker.show("#applyleave_txtToDate");

        });
        $('#applyleave_txtToDate').click(function(){
            daymarker.show("#applyleave_txtToDate");

        });

        //Show From if same date
        if(trim($("#applyleave_txtFromDate").val()) != displayDateFormat && trim($("#applyleave_txtToDate").val()) != displayDateFormat){
            if( trim($("#applyleave_txtFromDate").val()) == trim($("#applyleave_txtToDate").val())) {
                showTimeControls(true);
            }
        }

        // Bind On change event of From Time
        $('#applyleave_txtFromTime').change(function() {
            fillTotalTime();
        });

        // Bind On change event of To Time
        $('#applyleave_txtToTime').change(function() {
            fillTotalTime();
        });

        function updateLeaveBalance() {
            var leaveType = $('#applyleave_txtLeaveType').val();
            var startDate = $('#applyleave_txtFromDate').val();
            if (leaveType == "") {
                $('#applyleave_leaveBalance').text('--');
            } else {
                $('#applyleave_leaveBalance').append('');
                $.ajax({
                    type: 'GET',
                    url: leaveBalanceUrl,
                    data: '&leaveType=' + leaveType + '&startDate=' + startDate,
                    dataType: 'json',
                    success: function(data) {
                        if ($('#leaveBalance').length == 0) {
                            $('#applyleave_leaveBalance').text(data);
                        }

                    }
                });
            }
        }

        // Fetch and display available leave when leave type is changed
        $('#applyleave_txtLeaveType').change(function() {
            updateLeaveBalance();
        });

        //Validation
        $("#frmLeaveApply").validate({
            rules: {
                'applyleave[txtLeaveType]':{required: true },
                'applyleave[txtFromDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'applyleave[txtToDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$("#applyleave_txtFromDate").val()
                        }
                    }
                },
                'applyleave[txtComment]': {maxlength: 250},
                'applyleave[txtLeaveTotalTime]':{ required: false , number: true , min: 0.01, validWorkShift : true,validTotalTime : true},
                'applyleave[txtToTime]': {validToTime: true}
            },
            messages: {
                'applyleave[txtLeaveType]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'applyleave[txtFromDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate
                },
                'applyleave[txtToDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                },
                'applyleave[txtComment]':{
                    maxlength:"<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>"
                },
                'applyleave[txtLeaveTotalTime]':{
                    number:"<?php echo __('Should be a number'); ?>",
                    min : "<?php echo __("Should be greater than %amount%", array("%amount%" => '0.01')); ?>",
                    max : "<?php echo __("Should be less than %amount%", array("%amount%" => '24')); ?>",
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift : "<?php echo __('Should be less than work shift length'); ?>"
                },
                'applyleave[txtToTime]':{
                    validToTime:"<?php echo __('From time should be less than To time'); ?>"
                }
            },
            errorElement : 'div',
            errorPlacement: function(error, element) {
                if (element.css('display') != 'none') {
                    error.insertAfter(element.next());
                }
            }
        });

        $.validator.addMethod("validTotalTime", function(value, element) {
            var totalTime	=	$('#applyleave_txtLeaveTotalTime').val();
            var fromdate	=	$('#applyleave_txtFromDate').val();
            var todate		=	$('#applyleave_txtToDate').val();

            if((fromdate==todate) && (totalTime==''))
                return false;
            else
                return true;

        });

        $.validator.addMethod("validWorkShift", function(value, element) {
            var totalTime	=	$('#applyleave_txtLeaveTotalTime').val();
            var fromdate	=	$('#applyleave_txtFromDate').val();
            var todate		=	$('#applyleave_txtToDate').val();
            var workShift	=	$('#applyleave_txtEmpWorkShift').val();

            if((fromdate==todate) && (parseFloat(totalTime) > parseFloat(workShift)))
                return false;
            else
                return true;

        });

        $.validator.addMethod("validToTime", function(value, element) {

            var fromdate	=	$('#applyleave_txtFromDate').val();
            var todate		=	$('#applyleave_txtToDate').val();
            var fromTime	=	$('#applyleave_txtFromTime').val();
            var toTime		=	$('#applyleave_txtToTime').val();

            var fromTimeArr = (fromTime).split(":");
            var toTimeArr = (toTime).split(":");
            var fromdateArr	=	(fromdate).split("-");

            var fromTimeobj	=	new Date(fromdateArr[0],fromdateArr[1],fromdateArr[2],fromTimeArr[0],fromTimeArr[1]);
            var toTimeobj	=	new Date(fromdateArr[0],fromdateArr[1],fromdateArr[2],toTimeArr[0],toTimeArr[1]);

            if((fromdate==todate) && (fromTime !='') && (toTime != '') && (fromTimeobj>=toTimeobj))
                return false;
            else
                return true;

        });

        $('#processing').html('');

        //Click Submit button
        $('#saveBtn').click(function(){
            $('#processing').html('');
            $('#messageBalloon_success').remove();
            $('#messageBalloon_warning').remove();
            if($('#applyleave_txtFromDate').val() == displayDateFormat){
                $('#applyleave_txtFromDate').val("");
            }
            if($('#applyleave_txtToDate').val() == displayDateFormat){
                $('#applyleave_txtToDate').val("");
            }
            
            if($('#frmLeaveApply').valid()) {
                $('#processing').html('<div class="messageBalloon_success">'+"<?php echo __('Processing') ;?>"+'...</div>');
            }            
            
            $('#frmLeaveApply').submit();
        });
    });

    function showTimeControls(show) {

        var timeControlIds = ['applyleave_txtFromTime', 'applyleave_txtToTime', 'applyleave_txtLeaveTotalTime'];
        
        $.each(timeControlIds, function(index, value) {

            if (show) {
                $('#' + value).show();
                $('label[for="' + value + '"]').show();
                $('#' + value).next('br').show();
            } else {
                $('#' + value).hide();
                $('label[for="' + value + '"]').hide();
                $('#' + value).next('br').hide();
            }
        });
    }

    function showTimepaneFromDate(theDate, displayDateFormat){
        var Todate = trim($("#applyleave_txtToDate").val());
        if(Todate == displayDateFormat) {
            $("#applyleave_txtFromDate").val(theDate);
            $("#applyleave_txtToDate").val(theDate);
            showTimeControls(true);
        } else {
            showTimeControls((Todate == theDate));
        }
        $("#applyleave_txtFromDate").valid();
        $("#applyleave_txtToDate").valid();
    }

    function showTimepaneToDate(theDate){
        var fromDate	=	trim($("#applyleave_txtFromDate").val());

        showTimeControls((fromDate == theDate));

        $("#applyleave_txtFromDate").valid();
        $("#applyleave_txtToDate").valid();
    }

    //Calculate Total time
    function fillTotalTime(){
        var fromTime = ($('#applyleave_txtFromTime').val()).split(":");
        var fromdate = new Date();
        fromdate.setHours(fromTime[0],fromTime[1]);

        var toTime = ($('#applyleave_txtToTime').val()).split(":");
        var todate = new Date();
        todate.setHours(toTime[0],toTime[1]);


        if (fromdate < todate) {
            var difference = todate - fromdate;
            var floatDeference	=	parseFloat(difference/3600000) ;
            $('#applyleave_txtLeaveTotalTime').val(Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2));
        }

        $("#applyleave_txtToTime").valid();
    }

    function fromDateBlur(date){
        var fromDateValue 	= 	trim(date);
        if(fromDateValue != displayDateFormat && fromDateValue != "") {
            var toDateValue	=	trim($("#applyleave_txtToDate").val());
            if(validateDate(fromDateValue, datepickerDateFormat)){
                if(fromDateValue == toDateValue) {
                    showTimeControls(true);
                }

                if(!validateDate(toDateValue, datepickerDateFormat)){
                    $('#applyleave_txtToDate').val(fromDateValue);
                    showTimeControls(true);
                }
            } else {
                showTimeControls(false);
                $('#applyleave_txtLeaveTotalTime').show();
            }
        }
    }

    function toDateBlur(date){
        var toDateValue	=	trim(date);
        if(toDateValue != displayDateFormat && toDateValue != ""){
            var fromDateValue = trim($("#applyleave_txtFromDate").val());

            if(validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)){

                showTimeControls((fromDateValue == toDateValue));
            }
        }
    }

</script>