<table>
     <tr><td style="width: 80px;"><?php echo " ".__("Project Name")." ";?></td><td><?php echo $projectName;?></td></tr>
     <tr><td style="width: 80px;"><?php echo " ".__("Activity Name")." ";?></td><td><?php echo $activityName;?></td></tr>
     <?php if(!(($projectDateRangeFrom == "YYYY-MM-DD") || ($projectDateRangeFrom  == ""))) {?><tr><td style="width: 80px;"><?php echo " ".__("From")." ";?></td><td><?php echo set_datepicker_date_format($projectDateRangeFrom);?></td></tr><?php } ?>
     <?php if(!(($projectDateRangeTo == "YYYY-MM-DD") || ($projectDateRangeTo  == ""))) {?><tr><td style="width: 80px;"><?php echo " ".__("To")." ";?></td><td><?php echo set_datepicker_date_format($projectDateRangeTo);?></td></tr><?php } ?>
     <tr><td style="width: 80px;"><?php echo " ".__("Total")." (".__("hours").") ";?></td><td><?php echo $total;?></td></tr>
</table>
