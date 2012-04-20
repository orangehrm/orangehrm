<div>
    
    <h2>Version Information</h2>
    
    <p>
        You are going to upgrade to OrangeHRM version <?php echo $newVersion; ?>. Following is a list of changes and manual steps that you need to take into consideration after upgrading. Make sure you understand them thoroughly before upgrading.
    </p>
    
    <?php if (!empty($notes)) : ?>
    
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
