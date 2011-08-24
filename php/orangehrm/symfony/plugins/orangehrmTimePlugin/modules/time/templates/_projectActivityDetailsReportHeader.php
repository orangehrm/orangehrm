<table>
     <tr><td style="width: 80px;"><?php echo " Project Name ";?></td><td><?php echo $projectName;?></td></tr>
     <tr><td style="width: 80px;"><?php echo " Activity Name ";?></td><td><?php echo $activityName;?></td></tr>
     <?php if(!(($projectDateRangeFrom == "YYYY-MM-DD") || ($projectDateRangeFrom  == ""))) {?><tr><td style="width: 80px;"><?php echo " From ";?></td><td><?php echo $projectDateRangeFrom;?></td></tr><?php } ?>
     <?php if(!(($projectDateRangeTo == "YYYY-MM-DD") || ($projectDateRangeTo  == ""))) {?><tr><td style="width: 80px;"><?php echo " To ";?></td><td><?php echo $projectDateRangeTo;?></td></tr><?php } ?>
     <tr><td style="width: 80px;"><?php echo " Total (hours) ";?></td><td><?php echo $total;?></td></tr>
</table>
