<?php if($projectReportPermissions->canRead()){?>

<div class="box">
    <div class="head"><h1 id="reportToHeading"><?php echo __($reportName); ?></h1></div>
    <div class="inner">
    <?php include_partial('global/flash_messages'); ?>
            
        <form action="<?php echo url_for("time/displayProjectReportCriteria?reportId=1"); ?>" id="reportForm" method="post">
                <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>             
                    <?php foreach ($sf_data->getRaw('runtimeFilterFieldWidgetNamesAndLabelsList') as $label) : ?>
                        <?php echo $reportForm->renderHiddenFields(); ?>
                        <li>
                            <?php echo $reportForm[$label['labelName']]->renderLabel(); ?>
                            <?php echo $reportForm[$label['labelName']]->render(); ?>
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
    $(document).ready(function() {
        $('#viewbutton').click(function() {
//            if($('#project_date_range_from_date').val() == datepickerDateFormat){
//                var parsedDate = $.datepicker.parseDate("yy-mm-dd", "1970-01-01");
//                $('#project_date_range_from_date').val($.datepicker.formatDate(datepickerDateFormat, parsedDate))
//            }
//            if($('#project_date_range_to_date').val() == datepickerDateFormat){
//               var parsedDate = $.datepicker.parseDate("yy-mm-dd", Date_toYMD());
//                $('#project_date_range_to_date').val($.datepicker.formatDate(datepickerDateFormat, parsedDate))
//            }
            $('#reportForm').submit();
        });

        var validator = $("#reportForm").validate({

            rules: {
                'time[project_name]' : {
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
                'time[project_name]': {
                    required: lang_required
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