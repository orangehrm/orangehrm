
<?php $i = 0; ?>

<?php if ($activityList == "All"): ?> 
<option value="-1">
    <?php echo __("All"); ?>
</option>
<?php else:?>

<?php if ($noProjectActivity == "yes"):?>

<option value=<?php echo null?>>
    <?php echo "--".__("No Project Activities")."--"; ?>
</option>

<?php else:?>

<?php if($activityList == null):?>
<option value="-1">
    <?php echo __("All"); ?>
</option>
<?php else:?>
<option value="-1">
    <?php echo __("All"); ?>
</option>
<?php foreach ($activityList as $activity): ?>
            <option value="<?php echo $activity->getActivityId(); ?>">
    <?php echo $activity->getName();
            $i++; ?>
        </option>
<?php endforeach; ?>

<?php endif;?>

<?php endif;?>

<?php endif;?>





