
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/saveSubscriberSuccess')); ?>

<div id="subscriber" class="box">
    
    <div class="head">
        <h1 id="subscriberHeading"><?php echo __("Add Subscriber"); ?></h1>
    </div>
    
    <div class="inner">
        
        <form name="frmSubscriber" id="frmSubscriber" method="post" action="<?php echo url_for('admin/saveSubscriber?notificationId='.$notificationId); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['name']->renderLabel(__('Name'). ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("maxlength" => 100)); ?>
                    </li>
                    
                    <li>
                        <?php echo $form['email']->renderLabel(__('Email'). ' <em>*</em>'); ?>
                        <?php echo $form['email']->render(array("maxlength" => 100)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>

        </form>
        
    </div>
    
</div>


<div id="subscriberList">
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
	var subscribers = <?php echo str_replace('&#039;', "'", $form->getSubscriberListForNotificationAsJson()) ?> ;
    var subscriberList = eval(subscribers);
	var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_EmailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
	var subscriberInfoUrl = "<?php echo url_for("admin/getSubscriberJson?id="); ?>";
	var backBtnUrl = "<?php echo url_for("admin/viewEmailNotification"); ?>";
	var lang_editSubscriber = "<?php echo __("Edit Subscriber"); ?>";
	var lang_addSubscriber = "<?php echo __("Add Subscriber"); ?>";
	var lang_uniqueEmail = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
</script>