<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>

<style type="text/css">
    .error_list {
        color: #ff0000;
    }
</style>

<?php use_stylesheets_for_form($assignLeaveForm); ?>

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
    <?php if (count($assignLeaveForm->leaveTypeList) > 0) {
        ?>
        <div class="outerbox">
            <div class="mainHeading"><h2 class="paddingLeft"><?php echo __('Assign Leave') ?></h2></div>

            <?php if ($assignLeaveForm->hasErrors()) {
                ?>
                <?php echo $assignLeaveForm['txtEmployee']->renderError(); ?>
                <?php echo $assignLeaveForm['txtLeaveType']->renderError(); ?>
                <?php echo $assignLeaveForm['txtFromDate']->renderError(); ?>
                <?php echo $assignLeaveForm['txtToDate']->renderError(); ?>
                <?php echo $assignLeaveForm['txtLeaveTotalTime']->renderError(); ?>
                <?php echo $assignLeaveForm['txtComment']->renderError(); ?>
                <?php echo $assignLeaveForm['txtFromTime']->renderError(); ?>
            <?php } ?>
            <form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">
                <br class="clear"/>
                <?php echo $assignLeaveForm->render(); ?>
                <!-- here we have the button -->
                <div class="formbuttons paddingLeft">
                    <input type="button" class="applybutton" id="saveBtn" value="<?php echo __('Assign'); ?>" title="<?php echo __('Assign'); ?>"/>
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

        // Auto complete
        $("#assignleave_txtEmployee_empName").autocomplete(employees_assignleave_txtEmployee, {
            formatItem: function(item) {
                return item.name;
            }
            ,matchContains:true
        }).result(function(event, item) {
            $('#assignleave_txtEmployee_empId').val(item.id);
            setEmployeeWorkshift(item.id);
            updateLeaveBalance();
        }
    );

        var rDate = trim($("#assignleave_txtFromDate").val());
        if (rDate == '') {
            $("#assignleave_txtFromDate").val(displayDateFormat);
        }

        updateLeaveBalance();

        $('#assignleave_txtFromDate').change(function() {
            fromDateBlur($(this).val());
            updateLeaveBalance();
        });

        //Bind date picker
        daymarker.bindElement("#assignleave_txtFromDate",
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

        $('#assignleave_txtFromDate_Button').click(function(){
            daymarker.show("#assignleave_txtFromDate");

        });
        $('#assignleave_txtFromDate').click(function(){
            daymarker.show("#assignleave_txtFromDate");

        });

        var tDate = trim($("#assignleave_txtToDate").val());
        if (tDate == '') {
            $("#assignleave_txtToDate").val(displayDateFormat);
        }

        //Bind date picker
        daymarker.bindElement("#assignleave_txtToDate",
        {
            onSelect: function(date){
                toDateBlur(date)
            },
            dateFormat : datepickerDateFormat,
            onClose: function() {
                $(this).valid();
            }
        });

        $('#assignleave_txtToDate_Button').click(function(){
            daymarker.show("#assignleave_txtToDate");

        });
        $('#assignleave_txtToDate').click(function(){
            daymarker.show("#assignleave_txtToDate");

        });

        //Show From if same date
        if(trim($("#assignleave_txtFromDate").val()) != displayDateFormat && trim($("#assignleave_txtToDate").val()) != displayDateFormat){
            if( trim($("#assignleave_txtFromDate").val()) == trim($("#assignleave_txtToDate").val())) {
                showTimeControls(true);
            }
        }

        // Bind On change event of From Time
        $('#assignleave_txtFromTime').change(function() {
            fillTotalTime();
        });

        // Bind On change event of To Time
        $('#assignleave_txtToTime').change(function() {
            fillTotalTime();
        });

        // Fetch and display available leave when leave type is changed
        $('#assignleave_txtLeaveType').change(function() {
            updateLeaveBalance();
        });

        function updateLeaveBalance() {
            var leaveType = $('#assignleave_txtLeaveType').val();
            var empId = $('#assignleave_txtEmployee_empId').val();
            var startDate = $('#assignleave_txtFromDate').val();
            if (leaveType == "" || empId == "") {
                $('#assignleave_leaveBalance').text('--');
            } else {
                $('#assignleave_leaveBalance').append('');
                $.ajax({
                    type: 'GET',
                    url: leaveBalanceUrl,
                    data: '&leaveType=' + leaveType+'&empNumber=' + empId + '&startDate=' + startDate,
                    dataType: 'json',
                    success: function(data) {
                        if ($('#leaveBalance').length == 0) {
                            $('#assignleave_leaveBalance').text(data);
                        }

                    }
                });
            }
        }

        //Validation
        $("#frmLeaveApply").validate({
            rules: {
                'assignleave[txtEmployee][empName]':{validEmployeeName: true },
                'assignleave[txtLeaveType]':{required: true },
                'assignleave[txtFromDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'assignleave[txtToDate]': {
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
                            fromDate:$("#assignleave_txtFromDate").val()
                        }
                    }
                },
                'assignleave[txtComment]': {maxlength: 250},
                'assignleave[txtLeaveTotalTime]':{ required: false , number: true , min: 0.01, validWorkShift : true,validTotalTime : true},
                'assignleave[txtToTime]': {validToTime: true}
            },
            messages: {
                'assignleave[txtEmployee][empName]':{
                    validEmployeeName:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'assignleave[txtLeaveType]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'assignleave[txtFromDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate
                },
                'assignleave[txtToDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                },
                'assignleave[txtComment]':{
                    maxlength:"<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>"
                },
                'assignleave[txtLeaveTotalTime]':{
                    number:"<?php echo __('Should be a number'); ?>",
                    min : "<?php echo __("Should be greater than %amount%", array("%amount%" => '0.01')); ?>",
                    max : "<?php echo __("Should be less than %amount%", array("%amount%" => '24')); ?>"	,
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift : "<?php echo __('Should be less than work shift length'); ?>"
                },
                'assignleave[txtToTime]':{
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

        $.validator.addMethod("validEmployeeName", function(value, element) {
            
            var empName = $('#assignleave_txtEmployee_empName').val();
            
            if (empName == '' || empName == '<?php echo __('Type for hints') . '...'; ?>') {
                return false;
            }
            
            return true;

        });

        $.validator.addMethod("validTotalTime", function(value, element) {
            var totalTime	=	$('#assignleave_txtLeaveTotalTime').val();
            var fromdate	=	$('#assignleave_txtFromDate').val();
            var todate		=	$('#assignleave_txtToDate').val();

            if((fromdate==todate) && (totalTime==''))
                return false;
            else
                return true;

        });

        $.validator.addMethod("validWorkShift", function(value, element) {
            var totalTime	=	$('#assignleave_txtLeaveTotalTime').val();
            var fromdate	=	$('#assignleave_txtFromDate').val();
            var todate		=	$('#assignleave_txtToDate').val();
            var workShift	=	$('#assignleave_txtEmpWorkShift').val();

            if((fromdate==todate) && (parseFloat(totalTime) > parseFloat(workShift)))
                return false;
            else
                return true;

        });

        $.validator.addMethod("validToTime", function(value, element) {

            var fromdate	=	$('#assignleave_txtFromDate').val();
            var todate		=	$('#assignleave_txtToDate').val();
            var fromTime	=	$('#assignleave_txtFromTime').val();
            var toTime		=	$('#assignleave_txtToTime').val();

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
        $('#saveBtn').click(function() {
            $('#processing').html('');
            $('#messageBalloon_success').remove();
            $('#messageBalloon_warning').remove();
            if($('#assignleave_txtFromDate').val() == displayDateFormat ){
                $('#assignleave_txtFromDate').val("");
            }
            if($('#assignleave_txtToDate').val() == displayDateFormat ){
                $('#assignleave_txtToDate').val("");
            }
            
            if($('#frmLeaveApply').valid()) {
                $('#processing').html('<div class="messageBalloon_success">'+"<?php echo __('Processing') ;?>"+'...</div>');
            }
            
            $('#frmLeaveApply').submit();
            
        });

        $("#assignleave_txtEmployee_empName").change(function(){
            autoFill('assignleave_txtEmployee_empName', 'assignleave_txtEmployee_empId', employees_assignleave_txtEmployee);
            updateLeaveBalance();
        });

        function autoFill(selector, filler, data) {
            $("#" + filler).val("");
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    return true;
                }
            });
        }
    });

    function showTimeControls(show) {

        var timeControlIds = ['assignleave_txtFromTime', 'assignleave_txtToTime', 'assignleave_txtLeaveTotalTime'];
        
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

    function showTimepaneFromDate(theDate, datepickerDateFormat){
        var Todate = trim($("#assignleave_txtToDate").val());
        if(Todate == datepickerDateFormat) {
            $("#assignleave_txtFromDate").val(theDate);
            $("#assignleave_txtToDate").val(theDate);
        } else{
            showTimeControls((Todate == theDate));
        }
        $("#assignleave_txtFromDate").valid();
        $("#assignleave_txtToDate").valid();
    }

    function showTimepaneToDate(theDate){
        var fromDate	=	trim($("#assignleave_txtFromDate").val());

        showTimeControls((fromDate == theDate));

        $("#assignleave_txtFromDate").valid();
        $("#assignleave_txtToDate").valid();
    }

    //Calculate Total time
    function fillTotalTime(){
        var fromTime = ($('#assignleave_txtFromTime').val()).split(":");
        var fromdate = new Date();
        fromdate.setHours(fromTime[0],fromTime[1]);

        var toTime = ($('#assignleave_txtToTime').val()).split(":");
        var todate = new Date();
        todate.setHours(toTime[0],toTime[1]);


        if (fromdate < todate) {
            var difference = todate - fromdate;
            var floatDeference	=	parseFloat(difference/3600000) ;
            $('#assignleave_txtLeaveTotalTime').val(Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2));
        }

        $("#assignleave_txtToTime").valid();
    }

    function fromDateBlur(date){
        var fromDateValue 	= 	trim(date);
        if(fromDateValue != displayDateFormat && fromDateValue != ""){
            var toDateValue	=	trim($("#assignleave_txtToDate").val());
            if(validateDate(fromDateValue, datepickerDateFormat)){
                if(fromDateValue == toDateValue) {
                    showTimeControls(true);
                }

                if(!validateDate(toDateValue, datepickerDateFormat)){
                    $('#assignleave_txtToDate').val(fromDateValue);
                    showTimeControls(true);
                }
            } else {
                showTimeControls(false);
            }
        }
    }

    function toDateBlur(date){
        var toDateValue	=	trim(date);
        if(toDateValue != displayDateFormat && toDateValue != ""){
            var fromDateValue = trim($("#assignleave_txtFromDate").val());

            if(validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)){

                showTimeControls((fromDateValue == toDateValue));
            }
        }
    }
    
    function setEmployeeWorkshift(empNumber) {

        $.ajax({
            url: "getWorkshiftAjax",
            data: "empNumber="+empNumber,
            dataType: 'json',
            success: function(data){
                $('#assignleave_txtEmpWorkShift').val(data.workshift);
            }
        });

    }    

</script>