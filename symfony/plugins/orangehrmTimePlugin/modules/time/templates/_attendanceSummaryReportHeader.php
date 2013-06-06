<dl class="search-params">

     <?php if(isset ($empName)):?>
     <dt><?php echo __("Employee Name");?></dt><dd><?php echo $empName;?></dd>
     <?php endif;?>

     <?php if(isset ($employeeStatus)):?>
     <dt><?php echo __("Employment Status")." ";?></dt>
     <dd><?php echo $employeeStatus;?></dd>
     <?php endif;?>

     <?php if(isset ($jobTitle)):?>
     <dt><?php echo __("Job Title");?></dt>
     <dd><?php echo $jobTitle;?></dd>
     <?php endif;?>

     <?php if(isset ($subUnit)):?>
     <dt><?php echo __("Sub Unit");?></dt>
     <dd><?php echo $subUnit;?></dd>
     <?php endif;?>

     <?php if(!(($attendanceDateRangeFrom == "YYYY-MM-DD") || ($attendanceDateRangeFrom  == ""))) :?>
     <dt><?php echo __("From");?></dt>
     <dd><?php echo set_datepicker_date_format($attendanceDateRangeFrom);?></dd>
     <?php endif; ?>
     <?php if(!(($attendanceDateRangeTo == "YYYY-MM-DD") || ($attendanceDateRangeTo  == ""))) :?>
     <dt><?php echo __("To");?></dt>
     <dd><?php echo set_datepicker_date_format($attendanceDateRangeTo);?></dd>
     <?php endif; ?>
</dl>
