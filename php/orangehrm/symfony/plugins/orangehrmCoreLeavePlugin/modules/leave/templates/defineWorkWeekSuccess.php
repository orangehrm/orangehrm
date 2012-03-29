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
 */
?>

<?php use_stylesheet('../orangehrmCoreLeavePlugin/css/defineWorkWeekSuccess'); ?>
<?php use_javascripts_for_form($workWeekForm); ?>
<?php use_stylesheets_for_form($workWeekForm); ?>

<div id="messageBalloonContainer" style="width:380px;">
    <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
</div>
<div class="formpageNarrow">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __('Work Week'); ?></h2></div>

        <div id="errorDiv"></div>
        
        <form id="frmWorkWeek" name="frmWorkWeek" method="post" action="<?php echo url_for('leave/defineWorkWeek') ?>" >            
            <?php echo $workWeekForm->render() ?>
            <br class="clear"/>
<?php 
    $permissions = $sf_context->get('screen_permissions');
    if ($permissions->canUpdate()) {
?>
            <div class="formbuttons">            
                <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Edit'); ?>" />
                <input type="button" class="clearbutton" onclick="reset();" value="<?php echo __('Reset'); ?>" />                
            </div>
<?php } ?>            
        </form>
    </div>
    <script type="text/javascript">
        //<![CDATA[
        var permissions = {
            canRead: <?php echo $permissions->canRead() ? 'true' : 'false';?>,
            canCreate: <?php echo $permissions->canCreate() ? 'true' : 'false';?>,            
            canUpdate: <?php echo $permissions->canUpdate() ? 'true' : 'false';?>,
            canDelete: <?php echo $permissions->canDelete() ? 'true' : 'false';?>
        };
        
        var lang_Save = "<?php echo __('Save') ?>";
        var lang_Edit = "<?php echo __('Edit') ?>";
        var lang_AtLeastOneWorkDay = "<?php echo __('At Least One Day Should Be a Working Day') ?>";
        var lang_Required = "<?php echo __(ValidationMessages::REQUIRED);?>";
        //]]>
    </script>
</div>
