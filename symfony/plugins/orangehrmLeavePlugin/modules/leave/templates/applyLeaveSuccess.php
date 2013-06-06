<?php
use_javascripts_for_form($applyLeaveForm);
use_stylesheets_for_form($applyLeaveForm);
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/assignLeaveSuccess.css'));
?>

<?php include_partial('overlapping_leave', array('overlapLeave' => $overlapLeave, 'workshiftLengthExceeded' => $workshiftLengthExceeded));?>

<div class="box" id="apply-leave">
    <div class="head">
        <h1><?php echo __('Apply Leave') ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <?php if ($applyLeaveForm->hasErrors()): ?>
                <?php include_partial('global/form_errors', array('form' => $applyLeaveForm)); ?>
        <?php endif; ?>        
        <?php if (count($leaveTypes) > 1) : ?>           
        <form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'apply-leave-form-elements'))?>
            <fieldset>                
                <ol>
                    <?php echo $applyLeaveForm->render(); ?>
                    <li class="required new">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>                      
                </ol>            
                
                <p>
                    <input type="button" id="applyBtn" value="<?php echo __("Apply") ?>"/>
                </p>                
            </fieldset>
            
        </form>
        <?php endif ?>           
    </div> <!-- inner -->
    
</div> <!-- apply leave -->

<!-- leave balance details HTML: Begins -->
<div class="modal hide" id="balance_details">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo 'OrangeHRM - ' . __('Leave Balance Details'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __('As of Date') . ':'; ?> <span id="balance_as_of"></span></p>
    <table border="0" cellspacing="0" cellpadding="0" class="table">
        <tbody>
                <tr class="odd">
                    <td><?php echo __('Entitled'); ?></td>
                    <td id="balance_entitled">0.00</td>
                </tr>
                <tr class="odd" id="container-adjustment">
                    <td><?php echo __('Adjustment'); ?></td>
                    <td id="balance_adjustment">0.00</td>
                </tr>
                <tr class="even">
                    <td><?php echo __('Taken'); ?></td>
                    <td id="balance_taken">0.00</td>
                </tr>
                <tr class="odd">
                    <td><?php echo __('Scheduled'); ?></td>
                    <td id="balance_scheduled">0.00</td>
                </tr>
                <tr class="even">
                    <td><?php echo __('Pending Approval'); ?></td>
                    <td id="balance_pending">0.00</td>
                </tr>                    
        </tbody>
        <tfoot>
            <tr class="total">
                <td><?php echo __('Balance');?></td>
                <td id="balance_total"></td>
            </tr>
        </tfoot>     
    </table>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="closeButton" value="<?php echo __('Ok'); ?>" />
  </div>
</div>
<!-- leave balance details HTML: Ends -->

<!-- leave balance details HTML: Begins -->
<div class="modal hide" id="multiperiod_balance">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo 'OrangeHRM - ' . __('Leave Balance Details'); ?></h3>
  </div>
  <div class="modal-body">
    <table border="0" cellspacing="0" cellpadding="0" class="table">
        <thead>
            <tr>
                <th><?php echo __('Leave Period');?></th>
                <th><?php echo __('Initial Balance');?></th>
                <th><?php echo __('Leave Date');?></th>
                <th><?php echo __('Available Balance');?></th>
            </tr>
        </thead>
        <tbody>
                <tr class="odd">
                    <td></td>
                </tr>                    
        </tbody>       
    </table>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="closeButton" value="<?php echo __('Ok'); ?>" />
  </div>
</div>
    <script type="text/javascript">
    //<![CDATA[        
        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
        var leaveBalanceUrl = '<?php echo url_for('leave/getLeaveBalanceAjax');?>';
        var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
        var lang_dateError = '<?php echo __("To date should be after from date") ?>';
        var lang_details = '<?php echo __("view details") ?>';
        var lang_BalanceNotSufficient = "<?php echo __("Balance not sufficient");?>";
        var lang_Duration = "<?php echo __('Duration');?>";
        var lang_StartDay = "<?php echo __('Start Day');?>";
        var lang_EndDay = "<?php echo __('End Day');?>";

        $(document).ready(function() {            

            showTimeControls(false, false);


        updateLeaveBalance();
        
        $('#applyleave_txtFromDate').change(function() {
            fromDateBlur($(this).val());
            updateLeaveBalance();
        });
        
        $('#applyleave_txtToDate').change(function() {
            toDateBlur($(this).val());
            updateLeaveBalance();
        });        

        $('#applyleave_partialDays').change(function() {
            handlePartialDayChange(true);
        });
        
        if(trim($("#applyleave_txtFromDate").val()) == displayDateFormat || trim($("#applyleave_txtToDate").val()) == displayDateFormat 
            || trim($("#applyleave_txtFromDate").val()) == '' || trim($("#applyleave_txtToDate").val()) == '') {
                showTimeControls(false, false);
        } else if (trim($("#applyleave_txtFromDate").val()) == trim($("#applyleave_txtToDate").val())) {
            showTimeControls(true, false);
        } else {
            showTimeControls(false, true);
        }
        
        // Bind On change event of time elements
        $('select.timepicker').change(function() {
            fillTotalTime($(this));
        });        
        
        $('#applyleave_txtLeaveType').change(function() {
            updateLeaveBalance();
        });
        
        function updateLeaveBalance() {
            var leaveType = $('#applyleave_txtLeaveType').val();
            var startDate = $('#applyleave_txtFromDate').val();
            var endDate =  $('#applyleave_txtToDate').val();
            $('#applyleave_leaveBalance').text('--');
            $('#leaveBalance_details_link').remove();  
            
            if (leaveType == "") {
                //$('#applyleave_leaveBalance').text('--');
            } else {
                $('#applyleave_leaveBalance').text('').addClass('loading_message');
                $.ajax({
                    type: 'GET',
                    url: leaveBalanceUrl,
                    data: '&leaveType=' + leaveType + '&startDate=' + startDate + '&endDate=' + endDate,
                    dataType: 'json',
                    success: function(data) {
                        
                        if (data.multiperiod == true) {

                            var leavePeriods = data.data;
                            var leavePeriodCount = leavePeriods.length;

                            var linkTxt = data.negative ? lang_BalanceNotSufficient : lang_details;
                            var balanceTxt = leavePeriodCount == 1 ? leavePeriods[0].balance.balance.toFixed(2) : '';
                            var linkCss = data.negative ? ' class="error" ' : "";

                            $('#applyleave_leaveBalance').text(balanceTxt)
                            .append('<a href="#multiperiod_balance" data-toggle="modal" id="leaveBalance_details_link"' + linkCss + '>' + 
                                linkTxt + '</a>');

                            var html = '';

                            var rows = 0;
                            for (var i = 0; i < leavePeriodCount; i++) {
                                var leavePeriod = leavePeriods[i];
                                var days = leavePeriod['days'];
                                var leavePeriodFirstRow = true;                        

                                for (var leaveDate in days) {
                                    if (days.hasOwnProperty(leaveDate)) {
                                        var leaveDateDetails = days[leaveDate];

                                        rows++;                        
                                        var css = rows % 2 ? "even" : "odd";                                

                                        var thisLeavePeriod = leavePeriod['period'];
                                        var leavePeriodTxt = '';
                                        var leavePeriodInitialBalance = '';

                                        if (leavePeriodFirstRow) {
                                            leavePeriodTxt = thisLeavePeriod[0] + ' - ' + thisLeavePeriod[1];
                                            leavePeriodInitialBalance = leavePeriod.balance.balance.toFixed(2);
                                            leavePeriodFirstRow = false;                                    
                                        }

                                        var balanceValue = leaveDateDetails.balance === false ? leaveDateDetails.desc : leaveDateDetails.balance.toFixed(2);

                                        html += '<tr class="' + css + '"><td>' + leavePeriodTxt + '</td><td class="right">' + leavePeriodInitialBalance +
                                            '</td><td>' + leaveDate + '</td><td class="right">' + balanceValue + '</td></tr>';                                
                                    }
                                }                    

                                $('div#multiperiod_balance table.table tbody').html('').append(html);
                            }

                        } else {       
                            var balance = data.balance;
                            var asAtDate = data.asAtDate;
                            var balanceDays = balance.balance;
                            $('#applyleave_leaveBalance').text(balanceDays.toFixed(2))
                                .append('<a href="#balance_details" data-toggle="modal" id="leaveBalance_details_link">' + 
                                    lang_details + '</a>');

                            $('#balance_as_of').text(asAtDate);
                            $('#balance_entitled').text(Number(balance.entitled).toFixed(2));
                            $('#balance_taken').text(Number(balance.taken).toFixed(2));
                            $('#balance_scheduled').text(Number(balance.scheduled).toFixed(2));
                            $('#balance_pending').text(Number(balance.pending).toFixed(2));
                            $('#balance_adjustment').text(Number(balance.adjustment).toFixed(2));
                            $('#balance_total').text(balanceDays.toFixed(2));

                            if(Number(balance.adjustment) == 0 ){
                                $('#container-adjustment').hide();
                            }
                        }
                        $('#applyleave_leaveBalance').removeClass('loading_message');                         
                    }
                });
           }
        }

        // Fetch and display available leave when leave type is changed
        $('#applyleave_leaveBalance').change(function() {
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
                            required: true,
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'applyleave[txtToDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            required: true,
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
                'applyleave[duration][time][from]':{ required: false, validWorkShift : true, validTotalTime: true, validToTime: true},
                'applyleave[duration][time][to]':{ required: false,validTotalTime: true},
                'applyleave[firstDuration][time][from]':{ required: false, validWorkShift : true, validTotalTime: true, validToTime: true},
                'applyleave[firstDuration][time][to]':{ required: false,validTotalTime: true},
                'applyleave[secondDuration][time][from]':{ required: false, validWorkShift : true, validTotalTime: true, validToTime: true},
                'applyleave[secondDuration][time][to]':{ required: false,validTotalTime: true}                
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
                'applyleave[duration][time][from]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift : "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime:"<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[duration][time][to]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>"
                },
                'applyleave[firstDuration][time][from]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift : "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime:"<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[firstDuration][time][to]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>"
                },
                'applyleave[secondDuration][time][from]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>",
                    validWorkShift : "<?php echo __('Duration should be less than work shift length'); ?>",
                    validToTime:"<?php echo __('From time should be less than To time'); ?>"
                },
                'applyleave[secondDuration][time][to]':{
                    validTotalTime : "<?php echo __(ValidationMessages::REQUIRED); ?>"
                }                         
            }
        });
        
        $.validator.addMethod("validTotalTime", function(value, element) {
            var valid = true;
            
           if ($(element).is(':visible')) { 
                             
                if (value == '') {
                    valid = false;
                }
            }
            
            return valid;
        });
        
        $.validator.addMethod("validWorkShift", function(value, element) {
            
            var valid = true;
            
            if ($(element).is(':visible')) {            
                var fromElement = $(element).parent('span').children('select.timepicker').first();    
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                var workShift = $('#applyleave_txtEmpWorkShift').val();
                if (parseFloat(totalTime) > parseFloat(workShift)) {
                    valid = false;
                }
            }
            return valid;            
        });        
        
        $.validator.addMethod("validToTime", function(value, element) {
            var valid = true;
            
            if ($(element).is(':visible')) {            
                var fromElement = $(element).parent('span').children('select.timepicker').first();    
                var toElement = fromElement.siblings('select.timepicker').first();

                var totalTime = getTotalTime(fromElement.val(), toElement.val());
                if (parseFloat(totalTime) <= 0) {
                    valid = false;
                }
            }

            return valid;  
        });        

        //Click Submit button
        $('#applyBtn').click(function(){
            if($('#applyleave_txtFromDate').val() == displayDateFormat){
                $('#applyleave_txtFromDate').val("");
            }
            if($('#applyleave_txtToDate').val() == displayDateFormat){
                $('#applyleave_txtToDate').val("");
            }
            $('#frmLeaveApply').submit();
        });
    });
    
    function showTimeControls(showOneDay, showMultiDay) {
        
        var oneDayControlIds = ['applyleave_duration_duration'];

        $.each(oneDayControlIds, function(index, value) {

            if (showOneDay) {
                $('#' + value).parent('li').show();
            } else {
                $('#' + value).parent('li').hide();
            }
        });

        var multiDayControlIds = ['applyleave_partialDays'];


        $.each(multiDayControlIds, function(index, value) {

            if (showMultiDay) {
                $('#' + value).parent('li').show();
            } else {
                $('#' + value).parent('li').hide();
            }
        }); 

        handlePartialDayChange($('#applyleave_partialDays').is(':visible'));
    } 
    
    function handlePartialDayChange(showMultiDay) {

        var partialDay = $('#applyleave_partialDays').val();
        var startLabel = false;
        var endLabel = false;

        if (!showMultiDay || partialDay === '') {
            $('#applyleave_firstDuration_duration').parent('li').hide();
            $('#applyleave_secondDuration_duration').parent('li').hide();
        } else if (partialDay === 'all' || partialDay === 'start') {
            $('#applyleave_firstDuration_duration').parent('li').show();
            $('#applyleave_secondDuration_duration').parent('li').hide();
            startLabel = partialDay === 'all' ? lang_Duration : lang_StartDay;
        } else if (partialDay === 'end') {
            $('#applyleave_firstDuration_duration').parent('li').hide();
            $('#applyleave_secondDuration_duration').parent('li').show();   
            endLabel = lang_EndDay;
        } else if (partialDay === 'start_end') {
            $('#applyleave_firstDuration_duration').parent('li').show();
            $('#applyleave_secondDuration_duration').parent('li').show(); 
            startLabel = lang_StartDay;
            endLabel = lang_EndDay;
        } 

        if (startLabel) {
            $('#applyleave_firstDuration_duration').parent('li').children('label:first-child').text(startLabel);
        }
        if (endLabel) {
            $('#applyleave_secondDuration_duration').parent('li').children('label:first-child').text(endLabel);
        }

    }    

    function fillTotalTime(element) {

        var fromElement = element.parent('span').children('select.timepicker').first();    
        var toElement = fromElement.siblings('select.timepicker').first();
        var durationElement = fromElement.siblings('input.time_range_duration').first();

        var total = getTotalTime(fromElement.val(), toElement.val());
        if (isNaN(total)) {
            total = '';
        }

        durationElement.val(total);
        fromElement.valid();
        toElement.valid();
    }

    function getTotalTime(from, to) {
        var total = 0;
        var fromTime = from.split(":");
        var fromdate = new Date();
        fromdate.setHours(fromTime[0],fromTime[1]);

        var toTime = to.split(":");
        var todate = new Date();
        todate.setHours(toTime[0],toTime[1]);        

        var difference = todate - fromdate;
        var floatDeference	=	parseFloat(difference/3600000) ;
        total = Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2);

        return total;        
    }

    function fromDateBlur(date) {

        var fromDateValue = trim(date);
        if (fromDateValue != displayDateFormat && fromDateValue != "") {
            var singleDayLeaveRequest = false;        
            var toDateValue = trim($("#applyleave_txtToDate").val());
            if (validateDate(fromDateValue, datepickerDateFormat)) {
                if (fromDateValue == toDateValue) {
                    singleDayLeaveRequest = true;
                }

                if (!validateDate(toDateValue, datepickerDateFormat)) {
                    $('#applyleave_txtToDate').val(fromDateValue);
                    singleDayLeaveRequest = true;
                }
            }
            showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
        } else {
            showTimeControls(false, false);
        }


    }

    function toDateBlur(date) {
        var singleDayLeaveRequest = false;
        var toDateValue = trim(date);
        if (toDateValue != displayDateFormat && toDateValue != "") {
            var fromDateValue = trim($("#applyleave_txtFromDate").val());

            if (validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)) {
                singleDayLeaveRequest = (fromDateValue == toDateValue);
                showTimeControls(singleDayLeaveRequest, !singleDayLeaveRequest);
            } else {
                showTimeControls(false, false);
            }
        } else {
            showTimeControls(false, false);
        }
    }   
    //]]>
</script>