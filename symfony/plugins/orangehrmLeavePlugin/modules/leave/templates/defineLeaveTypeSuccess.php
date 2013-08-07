<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>

<?php if($leaveTypePermissions->canRead()){ ?>
<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>

<div class="box" id="add-leave-type">
    <div class="head">
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <form id="frmLeaveType" name="frmLeaveType" method="post" action="<?php echo url_for('leave/defineLeaveType'); ?>">

            <fieldset>                
                    <ol>
                        <?php echo $form;?>                                              
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li> 
                    </ol>
            </fieldset>



            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'define-leave-type-extra-fields')); ?>

            <p>
                <?php
                $actionButtons = $form->getActionButtons($leaveTypeId);

                foreach ($actionButtons as $button) {
                    echo $button->render(null), "\n";
                }
                ?>                    
            </p>                

        </form>

    </div> <!-- inner -->

</div> <!-- add-leave-type -->


<!-- Undelete Dialog: Begins -->
<div class="modal hide" id="undeleteDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('This is a deleted leave type. Reactivate it?'); ?><br /><br />
            <strong><?php echo __('Yes'); ?></strong> - <?php echo __('Leave type will be undeleted'); ?><br />
            <strong><?php echo __('No'); ?></strong> - 
            <?php
            echo $form->isUpdateMode() ? __('This leave type will be renamed to the same name as the deleted leave type') :
                    __('A new leave type will be created with same name');
            ?>
            <br />
            <strong><?php echo __('Cancel'); ?></strong> - <?php echo __('Will take no action'); ?><br /><br />    
        </p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="undeleteYes" value="<?php echo __('Yes'); ?>" />
        <input type="button" class="btn" data-dismiss="modal" id="undeleteNo" value="<?php echo __('No'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Undelete Dialog: Ends -->
<!-- Exclude Info Dialog: Begins -->
<div class="modal hide" id="excludeInfoDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM'); ?></h3>
    </div>
    <div class="modal-body">
        <p><strong><?php echo __('Is entitlement situational'); ?>:</strong><br/><br/>
            <?php echo __('These leave will be excluded from reports unless there\'s some activity. E.g. maternity leave, jury duty leave.'); ?>
        </p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" value="<?php echo __('OK'); ?>" />
    </div>
</div>
<!-- Undelete Dialog: Ends -->

<form name="frmUndeleteLeaveType" id="frmUndeleteLeaveType" 
      action="<?php echo url_for('leave/undeleteLeaveType'); ?>" method="post">
          <?php echo $undeleteForm; ?>
</form>
<?php }?>
<script type="text/javascript">
    //<![CDATA[

    var activeLeaveTypes = [];
    var deletedLeaveTypes = [];

    var lang_LeaveTypeNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_LeaveTypeExists = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_LeaveTypeNameTooLong = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>'; 
    
    var backButtonUrl = '<?php echo url_for('leave/leaveTypeList'); ?>';

    //]]>
</script>
