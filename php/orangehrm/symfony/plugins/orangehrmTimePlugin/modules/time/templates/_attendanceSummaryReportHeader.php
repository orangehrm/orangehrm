<table>
     <?php if(isset ($empName)):?>
     <tr><td style="width: 80px;"><?php echo " ".__("Employee Name");?></td><td><?php echo $empName;?></td></tr>
     <?php endif;?>

     <?php if(isset ($employeeStatus)):?>
     <tr><td style="width: 80px;"><?php echo " ".__("Employment Status")." ";?></td><td><?php echo $employeeStatus;?></td></tr>
     <?php endif;?>

     <?php if(isset ($jobTitle)):?>
     <tr><td style="width: 80px;"><?php echo " ".__("Job Title");?></td><td><?php echo $jobTitle;?></td></tr>
     <?php endif;?>

     <?php if(isset ($subUnit)):?>
     <tr><td style="width: 80px;"><?php echo " ".__("Sub Unit");?></td><td><?php echo $subUnit;?></td></tr>
     <?php endif;?>

     <?php if(!(($attendanceDateRangeFrom == "YYYY-MM-DD") || ($attendanceDateRangeFrom  == ""))) {?><tr><td style="width: 80px;"><?php echo " ".__("From");?></td><td><?php echo set_datepicker_date_format($attendanceDateRangeFrom);?></td></tr><?php } ?>
     <?php if(!(($attendanceDateRangeTo == "YYYY-MM-DD") || ($attendanceDateRangeTo  == ""))) {?><tr><td style="width: 80px;"><?php echo " ".__("To");?></td><td><?php echo set_datepicker_date_format($attendanceDateRangeTo);?></td></tr><?php } ?>
</table>
