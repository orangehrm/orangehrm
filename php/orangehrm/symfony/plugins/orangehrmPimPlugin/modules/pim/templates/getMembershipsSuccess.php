<option value="">
    <?php echo "-- " . __('Select') . " --"; ?>
</option>
<?php foreach ($relatedMemberships as $relatedMembership): ?>
    <?php if(isset($selectedValue)){?>
        <?php if ($relatedMembership->membershipCode == $selectedValue) {?>
            <option selected="selected" value="<?php echo $relatedMembership->membershipCode; ?>">
                <?php echo $relatedMembership->membershipName; ?>
            </option>
        <?php } ?>
    <?php } ?>
        <option value="<?php echo $relatedMembership->membershipCode; ?>">
                <?php echo $relatedMembership->membershipName; ?>
        </option>
<?php endforeach; ?>
