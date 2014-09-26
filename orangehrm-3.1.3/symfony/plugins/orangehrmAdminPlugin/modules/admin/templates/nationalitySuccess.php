
<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/nationalitySuccess')); 
?>

<div id="nationality" class="box">
    
    <div class="head"><h1 id="nationalityHeading"><?php echo __("Add Nationality"); ?></h1></div>
    
    <div class="inner">

        <form name="frmNationality" id="frmNationality" method="post" action="<?php echo url_for('admin/nationality'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['name']->renderLabel(__('Name'). ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("class" => "formInput", "maxlength" => 100)); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                
                <p>
                    <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel");?>"/>
                </p>
            </fieldset>
        </form>
    </div>
</div>

<div id="nationalityList">
    <!-- List component  -->
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
	var nationalities = <?php echo str_replace('&#039;', "'", $form->getNationalityListAsJson()) ?> ;
        var nationalityList = eval(nationalities);
	var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
	var nationalityInfoUrl = "<?php echo url_for("admin/getNationalityJson?id="); ?>";
	var lang_editNationality = "<?php echo __("Edit Nationality"); ?>";
	var lang_addNationality = "<?php echo __("Add Nationality"); ?>";
	var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
</script>