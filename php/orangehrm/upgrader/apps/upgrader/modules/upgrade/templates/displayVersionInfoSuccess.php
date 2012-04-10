<div>
    
    <h2>Version Info</h2>
    
    <p>
        You are going to upgrade to OrangeHRM <?php echo $newVersion; ?>
    </p>
    
    <?php if (!empty($notes)) : ?>
    
    <h3>Notes</h3>
    
    <ul>
        
        <?php foreach ($notes as $note): ?>
        
        <li><?php echo $note; ?></li>
        
        <?php endforeach; ?>
        
    </ul>
    
    <?php endif; ?>
    
    <form action="<?php echo url_for('upgrade/displayVersionInfo');?>" name="versionInfoForm" method="post">
        <input type="submit" value="Proceed"/>
    </form>    
    
</div>
