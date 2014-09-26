
<?php
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/employmentStatusSuccess')); ?>

<div class="box" id="empStatus">
    
    <div class="head">
            <h1 id="empStatusHeading"><?php echo __('Add Employment Status'); ?></h1>
    </div>
    
    <div class="inner">
        
        <form name="frmEmpStatus" id="frmEmpStatus" method="post" action="<?php echo url_for('admin/employmentStatus'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['name']->renderLabel(__('Name'). ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
	    
        </form>
        
    </div>

</div>


<div id="customerList" class="">
    
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
	var empStatuses = <?php echo str_replace('&#039;', "'", $form->getEmploymentStatusListAsJson()) ?> ;
    var empStatusList = eval(empStatuses);
	var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var empStatusInfoUrl = "<?php echo url_for("admin/getEmploymentStatusJson?id="); ?>";
	var lang_editEmpStatus = "<?php echo __("Edit Employment Status"); ?>";
	var lang_addEmpStatus = "<?php echo __("Add Employment Status"); ?>";
	var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
</script>