<table>
     <?php if(isset ($empName)):?>
     <tr><td style="width: 80px;"><?php echo " Employee Name";?></td><td><?php echo $empName;?></td></tr>
     <?php endif;?>

     <?php if(isset ($empStatusName)):?>
     <tr><td style="width: 80px;"><?php echo " Employment Status ";?></td><td><?php echo $empStatusName;?></td></tr>
     <?php endif;?>

     <?php if(isset ($jobTitName)):?>
     <tr><td style="width: 80px;"><?php echo " Job Title";?></td><td><?php echo $jobTitName;?></td></tr>
     <?php endif;?>

     <?php if(isset ($subUnit)):?>
     <tr><td style="width: 80px;"><?php echo " Sub Unit";?></td><td><?php echo $subUnit;?></td></tr>
     <?php endif;?>
     
     <?php if(!(($attendanceDateRangeFrom == "YYYY-MM-DD") || ($attendanceDateRangeFrom  == ""))) {?><tr><td style="width: 80px;"><?php echo " From";?></td><td><?php echo $attendanceDateRangeFrom;?></td></tr><?php } ?>
     <?php if(!(($attendanceDateRangeTo == "YYYY-MM-DD") || ($attendanceDateRangeTo  == ""))) {?><tr><td style="width: 80px;"><?php echo " To";?></td><td><?php echo $attendanceDateRangeTo;?></td></tr><?php } ?>
</table>
