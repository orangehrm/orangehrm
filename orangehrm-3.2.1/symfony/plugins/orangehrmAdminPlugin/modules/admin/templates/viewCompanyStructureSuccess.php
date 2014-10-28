<?php echo javascript_include_tag('jquery.tooltip.js') ?>
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', '/js/viewCompanyStructureSuccess')); ?>

<style type="text/css">
    p {
        margin-top: 10px;
    }
</style>

<div class="box">
    <div class="head">
        <h1><?php echo __("Organization Structure") ?></h1>
    </div>
    
    <div class="inner" >
        <div id="messageDiv"></div>
        <?php echo $listForm ?>
        <ol id="divCompanyStructureContainer">            
            <?php $tree->render(); ?>
        </ol>
        <p><input type="button" class="" name="btnEdit" id="btnEdit" value="<?php echo __("Edit"); ?>"/></p>
    </div>
</div>

<!-- unitDialog-Dialog -->
<div class="modal hide" id="unitDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3 id="title"><?php echo "OrangeHRM - ".__("Edit Unit"); ?></h3>
    </div>
    <div class="modal-body">
        <form  id="ohrmFormComponent_Form" action=""  method="post">
            <?php echo $form['_csrf_token']->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <input type="hidden" name="hdnId" id="hdnId">
                        <label for="txtUnit_Id"><?php echo __('Unit Id');?></label>
                        <?php echo $form['txtUnit_Id']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li>
                        <label for="txtName"><?php echo __('Name').' <em>*</em>'; ?></label>
                        <?php echo $form['txtName']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li class="largeTextBox">
                        <label for="txtDescription"><?php echo __('Description');?></label>
                        <?php echo $form['txtDescription']->render(array("class" => "formInputText")); ?>
                        <input type="hidden" id="hdnParent" name="hdnParent">
                    </li>
                    <li id="lastElement" class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
            </fieldset>
        </form> 
    </div>
    <div class="modal-footer">
        <input type="button" id="btnSave" class="" value="<?php echo __('Save'); ?>"/>
        <input type="button" id="btnCancel" class="reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>
</div> <!-- unitDialog -->

<!-- dltDialog-Dialog -->
<div class="modal hide" id="dltDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __("OrangeHRM - Confirmation Required"); ?></h3>
    </div>
    <div class="modal-body">
        <form  id="unitDeleteFrm" action=""  method="post">
            <input type="hidden" id="dltNodeId" value=""/>
        </form>
        <p><?php echo __("Units under selected unit will also be deleted"); ?></p>
        <p><?php echo __("Delete?"); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" id="dialogYes" class="" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogNo" class="reset" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- dltDialog -->

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
    var closeText = '<?php echo __('Close');?>';
</script>

<?php $tree->printJavascript(); ?>