<div>
    
    <h2>Version Information</h2>
    
    <p>
        You are going to upgrade to OrangeHRM version <?php echo $newVersion; ?>. 
        <?php if (count($notes) > 0): ?>
        Following is a list of changes and manual steps that you need to take into consideration after upgrading. Make sure you understand them thoroughly before upgrading.
        <?php endif; ?>
    </p>
    
    <?php if (count($notes) > 0) : ?>
    
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
