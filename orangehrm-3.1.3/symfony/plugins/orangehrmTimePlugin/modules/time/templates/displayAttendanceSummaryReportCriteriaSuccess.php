<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php if($attendancePermissions->canRead()){?>
<div class="box" id="attendance-summary">
       <div class="head"><h1><?php echo __('Attendance Total Summary Report'); ?></h1></div>
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            
        <form action="<?php echo url_for("time/displayAttendanceSummaryReport?reportId=" . $reportId); ?>" id="attendanceTotalSummaryReportForm" method="post">
            
            <fieldset>
                <ol>
                                                                   
                    <li> 
                        <label><?php echo __('Employee Name'.' <em>*</em>') ?></label>
                        <?php echo $form['empName']->render(); ?>
                    
                    </li>
                            
                    <li>
                        <label><?php echo __('Job Title') ?></label>
                        <?php echo $form['jobTitle']->renderError() ?><?php echo $form['jobTitle']->render(); ?>
                    </li>
                            
                    <li>
                        <label><?php echo __('Sub Unit') ?></label>
                        <?php echo $form['subUnit']->renderError() ?><?php echo $form['subUnit']->render(); ?>
                    </li>
                            
                    <li>
                        <label><?php echo __('Employment Status') ?></label>
                        <?php echo $form['employeeStatus']->renderError() ?><?php echo $form['employeeStatus']->render(); ?>
                    </li> 
                    <li>
                        <label><?php echo __('From') ?></label>
                       <?php echo $form['fromDate']->render(); ?>
                    </li> 
                    <li>
                        <label><?php echo __('To') ?></label>
                        <?php echo $form['toDate']->render(); ?>
                    </li> 
                            
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>

                </ol>

                <p>
                    <input type="submit" id="viewbutton" value="<?php echo __('View') ?>" />
                    
                </p>
              
              
            
            <?php echo $form->renderHiddenFields(); ?>
            
            </fieldset> 
        </form>
    </div>
</div>
<?php }?>
<script type="text/javascript">

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_emptyEmployee = '<?php echo __('Select an Employee')?>';
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_invalid = '<?php echo __(ValidationMessages::INVALID);?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var employeeFlag;

    $(document).ready(function() {

        if(<?php echo $lastEmpNumber; ?> == '-1'){
            $("#employee_name").val('<?php echo __('All')?>');
            $('#attendanceTotalSummary_employeeId').val('-1');
        }else{
            if ($("#employee_name").val() == '') {
                $("#employee_name").val('<?php echo __("Type for hints") . "..."; ?>').addClass("inputFormatHint");
            }

            $('#viewbutton').click(function() {
                $('#attendanceTotalSummaryReportForm input.inputFormatHint').val('');
                $('#attendanceTotalSummaryReportForm').submit();
            });

            $("#employee_name").one('focus', function() {

                if ($(this).hasClass("inputFormatHint")) {
                    $(this).val("");
                    $(this).removeClass("inputFormatHint");
                }
            });
        }

        $("#employee_name").autocomplete(employees, {

            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }              
            ,matchContains:true
        }).result(function(event, item) {
            $(this).valid();
        }
    );

        $('#attendanceTotalSummaryReportForm').submit(function(){
            $('#validationMsg').removeAttr('class');
            $('#validationMsg').html("");
            var employeeFlag = validateInput();
            if(!employeeFlag) {
                if(errorMsge) {
                    $('#validationMsg').attr('class', "messageBalloon_failure");
                    $('#validationMsg').html(errorMsge);
                }
                return false;
            }
        });

        //Validation
        $("#attendanceTotalSummaryReportForm").validate({
            rules: {
                'attendanceTotalSummary[empName]': {
                    required:true,
                    employeeValidation: true,
                    onkeyup: false
                },
                'attendanceTotalSummary[fromDate]':{
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            required:false,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'attendanceTotalSummary[toDate]':{
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            required:false,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$('#from_date').val()
                        }
                    }
                }
            },
            messages: {
                'attendanceTotalSummary[empName]': {
                    required: lang_required,
                    employeeValidation: lang_invalid
                },
                'attendanceTotalSummary[fromDate]': {
                    valid_date: lang_invalidDate
                },
                'attendanceTotalSummary[toDate]': {
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                }
            }
        });

 

        if ($("#employee_name").val() == '') {
            $("#employee_name").val('<?php echo __("Type for hints") . "..."; ?>')
            .addClass("inputFormatHint");
        }

        $('#viewbutton').click(function() {
            $('#attendanceTotalSummaryReportForm input.inputFormatHint').val('');
            $('#attendanceTotalSummaryReportForm').submit();
        });

        $("#employee_name").one('focus', function() {

            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
        });
    });

    function validateInput(){
     
        var errorStyle = "background-color:#FFDFDF;";
        var empDateCount = employeesArray.length;
        var temp = false;
        var i;
        errorMsge = null;
        if(empDateCount==0){

            errorMsge = '<?php echo __("No Employees Available");?>';
            return false;
        }
        for (i=0; i < empDateCount; i++) {
            empName = $.trim($('#employee_name').val()).toLowerCase();
            arrayName = employeesArray[i].name.toLowerCase();

            if (empName == arrayName) {
                $('#attendanceTotalSummary_employeeId').val(employeesArray[i].id);
                temp = true;
                break;
            }
        }
        if(temp){
            return true;
        }
    }

    $.validator.addMethod("employeeValidation", function(value, element, params) {
        
        var empCount = employeesArray.length;
        var isValid = false;
        var empName = $('#employee_name').val();
        var inputName = $.trim(empName).toLowerCase();
        if(inputName != ""){
            var i;
            for (i=0; i < empCount; i++) {
                var arrayName = employeesArray[i].name.toLowerCase();
                if (inputName == arrayName) {
                    isValid =  true;
                    break;
                }
            }
        }
        return isValid;
    }); 
</script>

