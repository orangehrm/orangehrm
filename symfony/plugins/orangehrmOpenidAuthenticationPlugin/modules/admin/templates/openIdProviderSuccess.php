<?php
/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
 */
?>
<style>
    .box {
        position: static
    }
</style>
<?php
use_javascript(plugin_web_path('orangehrmOpenidAuthenticationPlugin', 'js/openIdProviderSuccess'));
?>
<div id="openid" class="box">

    <div class="head"><h1 id="openidHeading"><?php echo __("Add Provider"); ?></h1></div>

    <div class="inner">

        <form name="frmOpenIdProvider" id="frmOpenIdProvider" method="post" action="<?php echo url_for('admin/openIdProvider'); ?>" >
            <fieldset>
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['id']; ?>
                <?php echo $form['status']; ?>
                <ol>
                    <li>
                        <?php echo $form['type']->renderLabel(__('Type')); ?>
                        <?php echo $form['type']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['name']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['url']->renderLabel(__('Url') . ' <em>*</em>'); ?>
                        <?php echo $form['url']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['clientId']->renderLabel(__('Client Id') . ' <em>*</em>'); ?>
                        <?php echo $form['clientId']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['clientSecret']->renderLabel(__('Client Secret') . ' <em>*</em>'); ?>
                        <?php echo $form['clientSecret']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['developerKey']->renderLabel(__('Developer Key') . ' <em>*</em>'); ?>
                        <?php echo $form['developerKey']->render(); ?>
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

<div id="openidList">  
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
<script type="text/javascript">
    var providers = <?php echo str_replace('&#039;', "'", $form->getOpenIdProviderListAsJson()) ?>;
    var providerList = eval(providers);
    var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_exceed40Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 40)); ?>';
    var providerInfoUrl = "<?php echo url_for("admin/getProviderJson?id="); ?>";
    var lang_editProvider = "<?php echo __("Edit Provider"); ?>";
    var lang_addProvider = "<?php echo __("Add Provider"); ?>";
    var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_url = '<?php echo __('Mal Formatted URL'); ?>';
    var lang_redirectUrlLabel = '<?php echo __('Redirect Url') . ' <em>*</em>'; ?>';
    var lang_urlLabel = '<?php echo __('Url') . ' <em>*</em>'; ?>';

</script>
