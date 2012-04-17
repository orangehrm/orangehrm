<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
?>

<?php use_stylesheet('../orangehrmAdminPlugin/css/viewModulesSuccess'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

<div id="saveFormDiv">
    <div class="outerbox">

    <div class="mainHeading"><h2 id="saveFormHeading"><?php echo __('Module Configuration') ?></h2></div>

        <form name="frmSave" id="frmSave" method="post" action="<?php echo url_for('admin/viewModules'); ?>">
            
            <?php echo $form['_csrf_token']; ?>
            
            <!--<div class="errorHolder"></div>-->
            
            <?php echo $form['admin']->render(); ?>
            <?php echo $form['admin']->renderLabel(__('Enable Admin module') . ' <span class="required">*</span>'); ?>
            <br class="clear"/>   
            
            <?php echo $form['pim']->render(); ?>
            <?php echo $form['pim']->renderLabel(__('Enable PIM module') . ' <span class="required">*</span>'); ?>
            <br class="clear"/>          
            
            <?php echo $form['leave']->render(); ?>
            <?php echo $form['leave']->renderLabel(__('Enable Leave module')); ?>
            <br class="clear"/>  
            
            <?php echo $form['time']->render(); ?>
            <?php echo $form['time']->renderLabel(__('Enable Time module')); ?>
            <br class="clear"/>  
            
            <?php echo $form['recruitment']->render(); ?>
            <?php echo $form['recruitment']->renderLabel(__('Enable Recruitment module')); ?>
            <br class="clear"/>  
            
            <?php echo $form['performance']->render(); ?>
            <?php echo $form['performance']->renderLabel(__('Enable Performance module')); ?>
            <br class="clear"/>  
            
            <?php echo $form['help']->render(); ?>
            <?php echo $form['help']->renderLabel(__('Enable Help') . ' <span class="required">*</span>'); ?>
            <br class="clear"/>            
            
            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __('Edit'); ?>"
                       title="<?php echo __('Edit'); ?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            </div>

        </form>
    
    </div>
    
    <div class="helpText"><span class="required">*</span> <?php echo __('compulsory'); ?></div>
    
</div> <!-- saveFormDiv -->



<?php use_javascript('../orangehrmAdminPlugin/js/viewModulesSuccess'); ?>

<script type="text/javascript">
//<![CDATA[	    
    
    var lang_edit = "<?php echo __('Edit'); ?>";
    var lang_save = "<?php echo __('Save'); ?>";
    var reloadParent = <?php echo isset($templateMessage)?'true':'false'; ?>;
    
//]]>	
</script>