
<?php $i = 0; ?>
<option value="-1">
    <?php echo "Select an Activity..."; ?>
</option>
<?php foreach ($activityList as $activity): ?>
        <option value="<?php echo $activity->getActivityId(); ?>">
    <?php echo $activity->getName();
        $i++; ?>
    </option>
<?php endforeach; ?>
