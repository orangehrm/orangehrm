<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<?php echo javascript_include_tag('jquery.tooltip.js') ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>

<?php use_stylesheet('../orangehrmAdminPlugin/css/viewCompanyStructureSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/viewCompanyStructureSuccess'); ?>

<div id="messageDiv"></div>
<br class="clear"/>
<label id="heading"><?php echo __("Organization Structure") ?></label>
<br class="clear"/>
<br class="clear"/>
<div id="divCompanyStructureContainer"><?php $tree->render(); ?></div>
<br class="clear"/>
<br class="clear"/>
<input type="button" class="editbutton" name="btnEdit" id="btnEdit"
       value="<?php echo __("Edit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>

<div id="unitDialog" title="">
    <div id="divSubunitFormContainer"><?php $form->render();
$form->printRequiredFieldsNotice(); ?></div>
</div>

<div id="dltDialog" title="<?php echo __("OrangeHRM - Confirmation Required"); ?>"  style="display:none;">
    <br class="clear"/>
    <div id="dltConfirmationMsg"></div>
    <input type="hidden" id="dltNodeId" value=""/>
    <div class="dialogButtons">
        <input type="button" id="dialogYes" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogNo" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>


<script type="text/javascript">
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_done = "<?php echo __("Done"); ?>";
    var lang_addUnit = "<?php echo "OrangeHRM - ".__("Add Unit"); ?>";
    var lang_editUnit = "<?php echo "OrangeHRM - ".__("Edit Unit"); ?>";
    var lang_delete_warning = "<?php echo __("Units under selected unit will also be deleted"); ?>";
    var lang_delete_confirmation = "<?php echo __("Delete?"); ?>";
    var lang_addNote = "<?php echo __("This unit will be added under"); ?>";
    var lang_nameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_max_100 = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
    var lang_max_400 = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 400)); ?>';
    var lang_noDescriptionSpecified = "<?php echo __("Description is not specified"); ?>";
    var deleteSubunitUrl = '<?php echo public_path('index.php/admin/deleteSubunit'); ?>';
    var getSubunitUrl = '<?php echo public_path('index.php/admin/getSubunit'); ?>';
    var saveSubunitUrl = '<?php echo public_path('index.php/admin/saveSubunit'); ?>';
    var viewCompanyStructureHtmlUrl = '<?php echo public_path('index.php/admin/viewCompanyStructureHtml'); ?>/seed/';
</script>

<?php $tree->printJavascript(); ?>