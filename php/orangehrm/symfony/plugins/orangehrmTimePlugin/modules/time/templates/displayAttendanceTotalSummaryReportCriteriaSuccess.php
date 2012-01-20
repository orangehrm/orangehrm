<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<div class="outerbox" id="outerbox" style="width: 60%">
    <div class="mainHeading"><h2 id="reportToHeading"><?php echo __($reportName); ?></h2></div>
    <form action="<?php echo url_for("time/displayAttendanceTotalSummaryReportCriteria?reportId=4"); ?>" id="reportForm" method="post">

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
            <td colspan="2"><input type="submit" class="viewbutton" value="<?php echo __('View') ?>"/></td>
        </div>
    </form>
</div>

<style type="text/css">
    form#reportForm label {
        margin-top: 6px;
        width: 140px;
    }

    .viewbutton {
        margin-left: 20px;
    }
</style>