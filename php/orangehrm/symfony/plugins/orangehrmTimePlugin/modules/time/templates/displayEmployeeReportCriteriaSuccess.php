
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<div id="validationMsg" style="margin-left: 16px; width: 470px"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>
<div class="outerbox" id="outerbox" style="width: 60%">
    <div class="mainHeading"><h2 id="reportToHeading"><?php echo __($reportName); ?></h2></div>
    <form action="<?php echo url_for("time/displayEmployeeReportCriteria?reportId=2"); ?>" id="reportForm" method="post">

        <div class="employeeTable">
            <br class="clear"/>

            <?php foreach ($sf_data->getRaw('runtimeFilterFieldWidgetNamesAndLabelsList') as $label): ?>
                <!--                <div>-->
            <?php echo $reportForm[$label['labelName']]->renderLabel(); ?>
            <?php echo $reportForm[$label['labelName']]->render(); ?><?php echo $reportForm[$label['labelName']]->renderError(); ?>
                <div class="errorDiv" style="padding-right: 165px; float: right"></div>
                <!--            </div>-->
                <br class="clear"/>
                <br class="clear"/>
            <?php endforeach; ?>
            <?php echo $reportForm->renderHiddenFields(); ?>
            </div>

            <div class="formbuttons">
                <td colspan="2"><input type="button" id="viewbutton" class="viewbutton" value="<?php echo __('View') ?>"/></td>
            </div>
        </form>
    </div>
    <div class="paddingLeftRequired">Fields marked with an asterisk <span class="required"> * </span> are required.</div>

    <style type="text/css">
        form#reportForm label {
            margin-top: 6px;
            width: 140px;
            font-weight: normal;
        }
        #time_activity_name{
            width: 160px;
        }
        #time_project_name{
            width: 160px;
        }

        .viewbutton {
            margin-left: 20px;
        }

        .paddingLeftRequired{
            font-size: 8pt;
            padding-left: 15px;
            padding-top: 5px;
        }
        label.error{
            width: 230px !important;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
//            $('#viewbutton').click(function() {
//                if(isValidForm()) {
//                    $('#reportForm').submit();
//                }
//            });
//        });

//        function isValidForm(){

            var dateFormat	= '<?php echo $sf_user->getDateFormat(); ?>';
            var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
            var dateDisplayFormat = dateFormat.toUpperCase();
            var lang_dateError = '<?php echo __("From date should be less than To date") ?>';
            var lang_validDateMsg = '<?php echo __("Please enter a valid date in %format% format", array('%format%' => strtoupper($sf_user->getDateFormat()))) ?>'

        $.validator.addMethod("dateComparison", function(value, element, params) {
            var temp = false;

            var fromdate	=	$('#project_date_range_from_date').val();
            var todate	=	$('#project_date_range_to_date').val();

            if(fromdate.trim() == "" || todate.trim() == "" || fromdate == dateDisplayFormat || todate == dateDisplayFormat){
                temp = true;
            }else{
                fromdate = (fromdate).split("-");
                var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));

                todate = (todate).split("-");
                var todateObj	=	new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));

                if ( fromdate <= todate){
                    temp = true;
                }
            }
            return temp;

        });

        var validator = $("#reportForm").validate({

            rules: {
                'time[project_date_range][from]' : {
                    valid_date: function() {
                        return {
                            format:jsDateFormat,
                            displayFormat:dateDisplayFormat,
                            required:false
                        }
                    },
                    dateComparison:true
                },
                'time[project_date_range][to]' : {
                    valid_date: function() {
                        return {
                            format:jsDateFormat,
                            displayFormat:dateDisplayFormat,
                            required:false
                        }
                    }
                }
            },
            messages: {
                'time[project_date_range][from]' : {
                    dateComparison : lang_dateError,
                    valid_date: lang_validDateMsg
                },
                'time[project_date_range][to]' : {
                    valid_date: lang_validDateMsg
                }

            },
            errorPlacement: function(error, element) {
                error.appendTo( element.prev('label') );
                error.appendTo(element.next().next().next('div.errorDiv'));
            }

        });
        });
//        return true;
//    }
</script>