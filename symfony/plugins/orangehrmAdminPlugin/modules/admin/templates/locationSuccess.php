
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/locationSuccess')); ?>

<div id="location" class="box">
    
    <div class="head">
        <h1 id="locationHeading"><?php echo __("Add Location"); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>

        <form name="frmLocation" id="frmLocation" method="post" action="<?php echo url_for('admin/location'); ?>" >

            <fieldset>

                <ol>
                    <?php echo $form->render(); ?>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>

                <p>
                    <input type="button" class="addbutton" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
        
    </div>
    
</div>

<script type="text/javascript">
	var locations = <?php echo str_replace('&#039;', "'", $form->getLocationListAsJson()) ?> ;
    var locationList = eval(locations);
	var lang_LocNameRequired = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
	var lang_CountryRequired = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
	var lang_ValidCountry = '<?php echo __js(ValidationMessages::INVALID); ?>';
	var lang_validPhoneNo = '<?php echo __js(ValidationMessages::TP_NUMBER_INVALID); ?>';
	var lang_validFaxNo = '<?php echo __js(ValidationMessages::TP_NUMBER_INVALID); ?>';
	var lang_Max100Chars = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
	var lang_Max50Chars = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var lang_Max30Chars = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>';
	var lang_Max255Chars = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
	var lang_editLocation = "<?php echo __js("Edit Location"); ?>";
	var locationId = "<?php echo $locationId ?>";
	var viewLocationUrl = "<?php echo url_for("admin/viewLocations"); ?>";
	var lang_uniqueName = '<?php echo __js(ValidationMessages::ALREADY_EXISTS); ?>';
	var lang_save = "<?php echo __js("Save"); ?>";
	var lang_edit = "<?php echo __js("Edit"); ?>";
</script>
