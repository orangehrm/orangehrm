<?php use_javascripts_for_form($workWeekForm); ?>
<?php use_stylesheets_for_form($workWeekForm); ?>
<?php if($workWeekPermissions->canRead()){?>
<div id="location" class="box single">

    <div class="head">
        <h1 id="locationHeading"><?php echo __('Work Week'); ?></h1>
    </div>

    <div class="inner">

        <?php include_partial('global/flash_messages'); ?>
            <form id="frmWorkWeek" name="frmWorkWeek" method="post" action="<?php echo url_for('leave/defineWorkWeek') ?>" >  

            <fieldset>

                <ol>
                    <?php echo $workWeekForm->render(); ?>
                    </ol>

                <p>
                    <?php 
                        if ($workWeekPermissions->canUpdate()) {
                    ?>
                                <div class="formbuttons">            
                                    <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Edit'); ?>" />
                                    <input type="button" class="reset hide" name="btnReset" id="btnReset"  onclick="reset();" value="<?php echo __('Reset'); ?>" />                
                                </div>
                    <?php } ?> 
                </p>

            </fieldset>

        </form>
        </div>
</div>
<?php include_slot('define_work_week_end');?>
<?php }?>
<script type="text/javascript">
        //<![CDATA[
        var permissions = {
            canRead: <?php echo $workWeekPermissions->canRead() ? 'true' : 'false';?>,
            canCreate: <?php echo $workWeekPermissions->canCreate() ? 'true' : 'false';?>,            
            canUpdate: <?php echo $workWeekPermissions->canUpdate() ? 'true' : 'false';?>,
            canDelete: <?php echo $workWeekPermissions->canDelete() ? 'true' : 'false';?>
        };
        
        var lang_Save = "<?php echo __('Save') ?>";
        var lang_Edit = "<?php echo __('Edit') ?>";
        var lang_AtLeastOneWorkDay = "<?php echo __('At Least One Day Should Be a Working Day') ?>";
        var lang_Required = "<?php echo __(ValidationMessages::REQUIRED);?>";
        //]]>
    </script>

