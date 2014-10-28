<div id="profile-pic">
    
<h1><?php echo $fullName; ?></h1>
  <div class="imageHolder">

<?php if ($photographPermissions->canUpdate() || $photographPermissions->canDelete()) : ?>
  <a href="<?php echo url_for('pim/viewPhotograph?empNumber=' . $empNumber); ?>" title="<?php echo __('Change Photo'); ?>" class="tiptip">
<?php else: ?>
  <a href="#">
<?php endif; ?>

    <img alt="Employee Photo" src="<?php echo url_for("pim/viewPhoto?empNumber=". $empNumber); ?>" border="0" id="empPic" 
     width="<?php echo $width; ?>" height="<?php echo $height; ?>"/>
  </a>

  </div>    
</div> <!-- profile-pic -->