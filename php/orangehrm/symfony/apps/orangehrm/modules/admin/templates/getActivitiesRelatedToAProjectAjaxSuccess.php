
<?php $i = 0; ?>
<option value="-1">
    <?php echo "All"; ?>
</option>
<?php if ($activityList != "All"): ?> {
<?php foreach ($activityList as $activity): ?>
            <option value="<?php echo $activity->getActivityId(); ?>">
    <?php echo $activity->getName();
            $i++; ?>
        </option>
<?php endforeach; ?>
<?php endif; ?>