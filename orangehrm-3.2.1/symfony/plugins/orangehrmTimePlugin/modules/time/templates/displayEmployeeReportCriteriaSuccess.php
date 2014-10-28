<?php
stylesheet_tag(theme_path('css/orangehrm.datepicker.css'));
use_javascript('orangehrm.datepicker.js');

if($employeeReportsPermissions->canRead()){
?>
<div class="box">
    <div class="head"><h1 id="reportToHeading"><?php echo __($reportName); ?></h1></div>
    <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            <form action="<?php echo url_for("time/displayEmployeeReportCriteria?reportId=2"); ?>" id="reportForm" method="post">
                <?php echo $form['_csrf_token']; ?>
                 <fieldset>
                <ol>             
                 <?php foreach ($sf_data->getRaw('runtimeFilterFieldWidgetNamesAndLabelsList') as $label): ?>
                 <?php echo $reportForm->renderHiddenFields(); ?>
                <li>   
                    <?php echo $reportForm[$label['labelName']]->renderLabel(); ?>
                    <?php echo $reportForm[$label['labelName']]->render(); ?><?php echo $reportForm[$label['labelName']]->renderError(); ?>
                </li>
                    <?php endforeach; ?>
                <li class="required">
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                </li>

                </ol>
                    <p>
                    <input type="button" id="viewbutton" value="<?php echo __('View') ?>" />
                    </p>

                </fieldset> 
          </form>
    </div>
</div>
<?php }?>
     <script type="text/javascript">
            var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
            var lang_dateError = '<?php echo __("To date should be after from date") ?>';
            var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
            var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
            var lang_empNamerequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
            var lang_activityRequired = '<?php echo __(ValidationMessages::REQUIRED)?>';
            var lang_validEmployee = '<?php echo __(ValidationMessages::INVALID); ?>';
        $(document).ready(function() {


         $('#viewbutton').click(function() {
            $('#reportForm').submit();
        });
        
        $('#employee_empName').result(function(event, item) {
            $(this).valid();
        });
        
        $.validator.addMethod("validEmployee", function(value, element) {
            var defaultValue = $('#employee_empName').data('typeHint');
            validEmployee = true;
            
            if (value != '' && value != defaultValue) {
                var matchFound = false;
                var empId = $('#employee_empId').val();
                
                if (empId != '') {
                    var lowerCaseName = value.toLowerCase();

                    for (i = 0; i < employeesArray.length; i++) {
                        if (empId == employeesArray[i].id) {
                            var arrayName = employeesArray[i].name.toLowerCase();

                            if (lowerCaseName == arrayName) {
                                matchFound = true;
                            }
                            break;
                        }
                    }
                }                
                if (!matchFound) {
                    validEmployee = false;
                }
            }
            return validEmployee;
        });        


        var validator = $("#reportForm").validate({

            rules: {
                'time[employee][empName]' : {
                    required: true,
                    no_default_value: function() {
                      return {
                       defaults: $('#employee_empName').data('typeHint')
                      }
                    },
                    validEmployee:true,
                    onkeyup: false
                },
                'time[activity_name]' : {
                    required:true
                },
                'time[project_date_range][from]' : {
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            required:false,
                            displayFormat:displayDateFormat
                        }
                    }},
                'time[project_date_range][to]' : {
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
                            fromDate:$('#project_date_range_from_date').val()
                        }
                    }
                }
            },
            messages: {
                'time[employee][empName]' : {
                    required: lang_empNamerequired,
                    no_default_value: lang_empNamerequired,
                    validEmployee: lang_validEmployee
                },
                'time[activity_name]' : {
                    required: lang_activityRequired
                },
                'time[project_date_range][from]' : {
                    valid_date: lang_validDateMsg
                },
                'time[project_date_range][to]' : {
                    valid_date: lang_validDateMsg,
                    date_range:lang_dateError
                }

            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "time[project_date_range][to]") {
                    var toDatePos = $('#project_date_range_to_date').position();
                    error.css('left', toDatePos.left);
                }
                error.insertAfter(element);
            }              
        });
    });

        function Date_toYMD() {
    var dt=new Date();
    var year, month, day;
    year = String(dt.getFullYear());
    month = String(dt.getMonth() + 1);
    if (month.length == 1) {
        month = "0" + month;
    }
    day = String(dt.getDate());
    if (day.length == 1) {
        day = "0" + day;
    }
    return year + "-" + month + "-" + day;
}
</script>