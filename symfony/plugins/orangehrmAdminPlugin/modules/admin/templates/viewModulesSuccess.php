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

<style type="text/css">
    form ol li.checkbox label {
        width:15%
    }
</style>

<div id="saveFormDiv" class="box">
    
    <div class="head">
        <h1 id="saveFormHeading"> <?php echo __('Module Configuration') ?> </h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>

        <form name="frmSave" id="frmSave" method="post" action="<?php echo url_for('admin/viewModules'); ?>">
            
            <?php echo $form['_csrf_token']; ?>
            
            <fieldset>
                
                <ol>
                    
                    <li class="checkbox">
                        <?php echo $form['admin']->renderLabel(__('Enable Admin module') . ' <em>*</em>'); ?>
                        <?php echo $form['admin']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['pim']->renderLabel(__('Enable PIM module') . ' <em>*</em>'); ?>
                        <?php echo $form['pim']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['leave']->renderLabel(__('Enable Leave module')); ?>
                        <?php echo $form['leave']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['time']->renderLabel(__('Enable Time module')); ?>
                        <?php echo $form['time']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['recruitment']->renderLabel(__('Enable Recruitment module')); ?>
                        <?php echo $form['recruitment']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['performance']->renderLabel(__('Enable Performance module')); ?>
                        <?php echo $form['performance']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['directory']->renderLabel(__('Enable Directory module')); ?>
                        <?php echo $form['directory']->render(); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __('Edit'); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
    
    </div>
    
</div> <!-- saveFormDiv -->



<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewModulesSuccess')); ?>

<script type="text/javascript">
//<![CDATA[	    
    
    var lang_edit = "<?php echo __('Edit'); ?>";
    var lang_save = "<?php echo __('Save'); ?>";
    
//]]>	
</script>