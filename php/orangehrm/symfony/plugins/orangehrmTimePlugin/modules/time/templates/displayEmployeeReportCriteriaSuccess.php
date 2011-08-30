
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

        <table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
             <?php foreach($sf_data->getRaw('runtimeFilterFieldWidgetNamesAndLabelsList')  as $label):?>
            <tr>
            <th><?php echo $reportForm[$label['labelName']]->renderLabel();?></th>
			<td><?php echo $reportForm[$label['labelName']]->render();?><?php echo $reportForm[$label['labelName']]->renderError();?></td>
            </tr>
            <?php endforeach;?>
            <?php echo $reportForm->renderHiddenFields(); ?>
        </table>
        <div class="formbuttons">
            <td colspan="2"><input type="submit" id="viewbutton" class="viewbutton" value="<?php echo __('View') ?>"/></td>
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

    .viewbutton {
        margin-left: 20px;
    }

    .paddingLeftRequired{
        font-size: 8pt;
        padding-left: 15px;
        padding-top: 5px;
    }
</style>