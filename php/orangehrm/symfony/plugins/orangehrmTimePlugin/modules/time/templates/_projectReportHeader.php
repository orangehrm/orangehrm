<?php
$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
$datepickerDateFormat = get_datepicker_date_format($inputDatePattern);
?>

<table>
    <tr><td style="width: 80px;"><?php echo " ".__("Project Name").""; ?></td><td><?php echo $projectName; ?></td></tr>
    <?php if (!(($projectDateRangeFrom == $datepickerDateFormat) || ($projectDateRangeFrom == ""))) {
 ?><tr><td style="width: 80px;"><?php echo " ".__("From")." "; ?></td><td><?php echo set_datepicker_date_format($projectDateRangeFrom); ?></td></tr><?php } ?>
<?php if (!(($projectDateRangeTo == $datepickerDateFormat) || ($projectDateRangeTo == ""))) { ?><tr><td style="width: 80px;"><?php echo " ".__("To")." "; ?></td><td><?php echo set_datepicker_date_format($projectDateRangeTo); ?></td></tr><?php } ?>
</table>

