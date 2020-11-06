<style>
    .oauthSelected {
        color: green;
    }
</style>

<div class="box">

    <?php
    use_javascript(plugin_web_path('orangehrmCoreOAuthPlugin', 'js/registerOAuthClientSuccess'));
    ?>

<?php if (!$authorized) { ?>

    <?php
    use_javascript(plugin_web_path('orangehrmCoreOAuthPlugin', 'js/registerOAuthClientSuccess'));
    ?>

<div class="message warning">
    <?php echo __(CommonMessages::CREDENTIALS_REQUIRED) ?> 
</div>

<?php } else { ?>

    <div class="head">
        <h1><?php echo __('Add OAuth Client'); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>        
        <form id="oAuthClientRegistrationForm" method="post" action="<?php echo url_for('admin/registerOAuthClient'); ?>" 
              enctype="multipart/form-data">
            <fieldset>

                <ol>
                    <?php echo $form->render(); ?>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <ol>
                    <li style="float: left;width: 425px;margin-top: 9px;font-size: 12px;" >
                        <p> <b><?php echo __('API Documentation'); ?> : </b> <a  target="_blank"  href="https://orangehrm.github.io/orangehrm-api-doc/"> [ https://orangehrm.github.io/orangehrm-api-doc ]</a>.</p>
                        <br>
                        <p><b><?php echo __('PHP Sample App'); ?>  :</b><a  target="_blank"  href="https://github.com/orangehrm/api-sample-app-php">[ https://github.com/orangehrm/api-sample-app-php ]</a>.</p>
                    </li>
                </ol>
                <p>
                    <input type="submit" class="" id="btnSave" value="<?php echo __("Save"); ?>"  />
                    <input type="button" id = "resetOauth" onclick="resetFields()" class="btn reset"  value="<?php echo __('Cancel'); ?>" />
                </p>
            </fieldset>
        </form>

    </div>

    <div id="clientList">
        <!-- List component -->
        <?php include_component('core', 'ohrmList'); ?>
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
<?php } ?>
    
</div>

<script>
    var lang_required = "<?php echo __js(ValidationMessages::REQUIRED); ?>";
    var lang_LengthExceeded80 = "<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 80)); ?>";
    var lang_LengthExceeded2000 = "<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
</script>
