<option value="">
    <?php echo "-- " . __('Select Membership') . " --"; ?>
</option>
<?php foreach ($relatedMemberships as $relatedMembership): ?>
        <option value="<?php echo $relatedMembership->membershipCode; ?>">
    <?php echo $relatedMembership->membershipName;?>
    </option>
<?php endforeach; ?>
