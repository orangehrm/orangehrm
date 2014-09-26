<?php
$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
$datepickerDateFormat = get_datepicker_date_format($inputDatePattern);
?>

<dl class="search-params">
    <dt><?php echo " ".__("Project Name").""; ?></dt>
    <dd><?php echo $projectName; ?></dd>
<?php if (!(($projectDateRangeFrom == $datepickerDateFormat) || ($projectDateRangeFrom == ""))) {?>
    <dt><?php echo " ".__("From")." "; ?></dt>
    <dd><?php echo set_datepicker_date_format($projectDateRangeFrom); ?></dd>
<?php } ?>
<?php if (!(($projectDateRangeTo == $datepickerDateFormat) || ($projectDateRangeTo == ""))) { ?>
    <dt><?php echo " ".__("To")." "; ?></dt>
    <dd><?php echo set_datepicker_date_format($projectDateRangeTo); ?></dd>
<?php } ?>
</dl>

