<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/saveProjectSuccess')); 
?>

<?php if($projectPermissions->canRead()){?>
<div id="addProject" class="box">
    
    <div class="head">
        <h1 id="addProjectHeading"><?php echo __("Add Project"); ?></h1>
    </div>
    
    <div class="inner">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
        <?php include_partial('global/flash_messages', array('prefix' => 'project')); ?>
        
        <form name="frmAddProject" id="frmAddProject" method="post" action="<?php echo url_for('admin/saveProject'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['customerName']->renderLabel(__('Customer Name') . ' <em>*</em>'); ?>
                        <?php echo $form['customerName']->render(array("class" => "formInputCustomer", "maxlength" => 52)); ?>
                        <?php if($customerPermissions->canCreate()){?>
                        <a id="addCustomerLink" class="btn2 fieldHelpRight" data-toggle="modal" href="#customerDialog" ><?php echo __('Add Customer') ?></a>
                        <?php }?>
                    </li>
                    
                    <li>
                        <?php echo $form['projectName']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                        <?php echo $form['projectName']->render(array("maxlength" => 52)); ?>
                    </li>
                    
                    <?php for ($i=1; $i <= $form->numberOfProjectAdmins; $i++) { ?>
                    <li id="<?php echo "projectAdmin_" . $i ?>" class="<?php echo ($i == 1) ?'':'noLabel'; ?>">
                        <?php if ($i == 1) : ?>
                        <label><?php echo __('Project Admin'); ?></label>
                        <?php endif; ?>
                        <?php echo $form['projectAdmin_' . $i]->render(array("class" => "formInputProjectAdmin", "maxlength" => 100)); ?>
                        <?php if($i != 1) { ?>
                            <a class="removeText fieldHelpRight" id=<?php echo "removeButton" . $i ?>><?php echo __('Remove'); ?></a>
                        <?php } else { ?>
                            <a class="addText fieldHelpRight" id='addButton'><?php echo __('Add Another'); ?></a>
                        <?php } ?>
                    </li>
                    <?php } ?>
                        
                    <li class="largeTextBox">
                        <?php echo $form['description']->renderLabel(__('Description')); ?>
                        <?php echo $form['description']->render(array("maxlength" => 256)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <?php if(($projectPermissions->canCreate() && empty($projectId)) || ($projectPermissions->canUpdate() && $projectId > 0)){?>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <?php }?>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
        
        </form>
    
    </div>

</div>

<?php if (!empty($projectId)) { ?>

<!-- Add-Activity -->
<div id="addActivity" class="box">
    
    <div class="head">
        <h1 id="addActivityHeading"><?php echo __("Add Project Activity"); ?></h1>
    </div>
    
    <div class="inner">
        
        <form name="frmAddActivity" id="frmAddActivity" method="post" action="<?php echo url_for('admin/addProjectActivity'); ?>" >

            <?php echo $activityForm['_csrf_token']; ?>
            <?php echo $activityForm->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $activityForm['activityName']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                        <?php echo $activityForm['activityName']->render(array("maxlength" => 102)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnActSave" id="btnActSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnActCancel" id="btnActCancel" value="<?php echo __("Cancel");?>"/>
                </p>
                
            </fieldset>
        
        </form>
        
    </div>

</div>
<!-- End-of-Add-Project-Activity -->
<a id="ProjectActivities"></a>
<?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>

<?php } ?>

<!-- Add customer window -->
<?php if($customerPermissions->canCreate()){?>
<div class="modal hide" id="customerDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Add Customer') ?></h3>
    </div>
    <div class="modal-body">
        <form name="frmAddCustomer" id="frmAddCustomer" method="post" action="" >
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $customerForm['customerName']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                        <?php echo $customerForm['customerName']->render(array("maxlength" => 52)); ?>
                    </li>
                    <li class="largeTextBox">
                        <?php echo $customerForm['description']->renderLabel(__('Description')); ?>
                        <?php echo $customerForm['description']->render(array("maxlength" => 255)); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <input type="button"  id="dialogSave" name="dialogSave" class="btn" value="<?php echo __('Save'); ?>" />
        <input type="button"  id="dialogCancel" name="dialogCancel" class="btn reset" data-dismiss="modal" 
               value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<?php }?>
<!-- End-of-Add-customer-window -->

<!-- undeleted project form -->
<form name="frmUndeleteCustomer" id="frmUndeleteCustomer" action="<?php echo url_for('admin/undeleteCustomer'); ?>" method="post">
    <?php echo $undeleteForm; ?>
</form>

<!-- undelete message dialog -->
<div class="modal hide" id="undeleteDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('This is a deleted customer. Reactivate again?'); ?></p>
        <p></p>
        <p><?php echo __('Yes'); ?> - <?php echo __('Customer will be undeleted'); ?></p>
        <p>
            <?php echo __('No'); ?> - 
            <?php echo __('A new customer will be created with same name'); ?>
        </p>
        <p><?php echo __('Cancel'); ?> - <?php echo __('Will take no action'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" id="undeleteYes" class="btn" data-dismiss="modal" value="<?php echo __('Yes'); ?>" />
        <input type="button" id="undeleteNo" class="btn" data-dismiss="modal" value="<?php echo __('No'); ?>" />
        <input type="button" id="undeleteCancel" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- undeleteDialog -->

<!-- Copy activity -->
<div class="modal hide" id="copyActivityModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Copy Activity') ?></h3>
    </div>
    <div class="modal-body">
        <form name="frmCopyAct" id="frmCopyAct" method="post" action="<?php echo url_for('admin/copyActivity?projectId=' . $projectId); ?>">
            <?php echo $copyActForm['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                        <label for="addProjectActivity_activityName"><?php echo __("Project Name"); ?> <em>*</em></label>
                        <input type="text" id="projectName" maxlength="52" class="project" name="projectName">
                        <span id="errorHolderCopy"></span>
                    </li>
                    <li>
                        <ul id="copyActivityList" class="checkList"> <!-- For adding checkboxes with activities -->

                        </ul>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" name="btnCopyDig" id="btnCopyDig" value="<?php echo __("Copy"); ?>" />
        <input type="button" class="btn reset" name="btnCopyCancel" id="btnCopyCancel" data-dismiss="modal" value="<?php echo __("Cancel"); ?>" />
    </div>
</div>
<!-- End-of-copy-activity -->

<!-- Delete-confirmation -->
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

<?php }?>

<script type="text/javascript">
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var customers = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()); ?> ;
    var customerList = eval(customers);
    var customerProjects = <?php echo str_replace('&#039;', "'", $form->getCustomerProjectListAsJson()); ?> ;
    var customerProjectsList = eval(customerProjects);
    var deletedCustomers = <?php echo str_replace('&#039;', "'", $customerForm->getDeletedCustomerListAsJson()); ?> ;
    <?php if ($projectId > 0) { ?>
        var activityList = <?php echo str_replace('&#039;', "'", $form->getActivityListAsJson($projectId)); ?>;
    <?php } ?>
    var numberOfProjectAdmins = <?php echo $form->numberOfProjectAdmins; ?>;
    var lang_typeHint = '<?php echo __("Type for hints") . "..."; ?>';
    var lang_nameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_activityNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validCustomer = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_projectRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_exceed50Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
    var lang_exceed255Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var lang_exceed100Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
    var custUrl = '<?php echo url_for("admin/saveCustomerJson"); ?>';
    var projectUrl = '<?php echo url_for("admin/saveProject"); ?>';
    var urlForGetActivity = '<?php echo url_for("admin/getActivityListJason?projectId="); ?>';
    var urlForGetProjectList = '<?php echo url_for("admin/getProjectListJson?customerId="); ?>';
    var deleteActivityUrl = '<?php echo url_for("admin/deleteProjectActivity"); ?>';
    var cancelBtnUrl = '<?php echo url_for("admin/viewProjects"); ?>';
    var lang_enterAValidEmployeeName = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_identical_rows = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_noActivities = "<?php echo __("No assigned activities"); ?>";
    var lang_noActivitiesSelected = "<?php echo __("No activities selected"); ?>";
    var projectId = '<?php echo $projectId; ?>';
    var custId = '<?php echo $custId; ?>';
    var lang_edit = '<?php echo __("Edit"); ?>';
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_editProject = '<?php echo __("Edit Project"); ?>';
    var lang_Project = '<?php echo __("Project"); ?>';
    var lang_uniqueCustomer = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_editActivity = '<?php echo __("Edit Project Activity"); ?>';
    var lang_addActivity = '<?php echo __("Add Project Activity"); ?>';
    var isProjectAdmin = '<?php echo $isProjectAdmin; ?>';
    var dontHavePermission = '<?php echo (!$projectPermissions->canCreate() || !$projectPermissions->canUpdate()); ?>';
</script>
