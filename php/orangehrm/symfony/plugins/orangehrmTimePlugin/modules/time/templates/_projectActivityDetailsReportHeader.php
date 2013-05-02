<dl class="search-params">
     <dt><?php echo " ".__("Project Name")." ";?></dt>
     <dd><?php echo $projectName;?></dd>
     <dt><?php echo " ".__("Activity Name")." ";?></dt>
     <dd><?php echo $activityName;?></dd>
     <?php if(!(($projectDateRangeFrom == "YYYY-MM-DD") || ($projectDateRangeFrom  == ""))) {?>
        <dt><?php echo " ".__("From")." ";?></dt>
        <dd><?php echo set_datepicker_date_format($projectDateRangeFrom);?></dd>
     <?php } ?>
     <?php if(!(($projectDateRangeTo == "YYYY-MM-DD") || ($projectDateRangeTo  == ""))) {?>
        <dt><?php echo " ".__("To")." ";?></dt>
        <dd><?php echo set_datepicker_date_format($projectDateRangeTo);?></dd>
    <?php } ?>
     <dt class="total"><?php echo " ".__("Total")." (".__("hours").") ";?></dt>
     <dd class="total"><?php echo $total;?></dd>
</dl>
