<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js') ?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/locationSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/locationSuccess'); ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="location">
    <div class="outerbox">

        <div class="mainHeading"><h2 id="locationHeading"><?php echo __("Add Location"); ?></h2></div>
        <form name="frmLocation" id="frmLocation" method="post" action="<?php echo url_for('admin/location'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>
	    
	    <?php echo $form['name']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
            <?php echo $form['name']->render(array("class" => "formInput", "maxlength" => 102)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['country']->renderLabel(__('Country') . ' <span class="required">*</span>'); ?>
            <?php echo $form['country']->render(array("class" => "formInput", "maxlength" => 3)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['state']->renderLabel(__('State/Province')); ?>
            <?php echo $form['state']->render(array("class" => "formInput", "maxlength" => 52)); ?>
            <?php echo $form['province']->render(array("class" => "formInput", "maxlength" => 52)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['city']->renderLabel(__('City')); ?>
            <?php echo $form['city']->render(array("class" => "formInput", "maxlength" => 52)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['address']->renderLabel(__('Address')); ?>
            <?php echo $form['address']->render(array("class" => "formInput", "maxlength" => 256)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['zipCode']->renderLabel(__('Zip/Postal Code')); ?>
            <?php echo $form['zipCode']->render(array("class" => "formInput", "maxlength" => 32)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['phone']->renderLabel(__('Phone')); ?>
            <?php echo $form['phone']->render(array("class" => "formInput", "maxlength" => 32)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['fax']->renderLabel(__('Fax')); ?>
            <?php echo $form['fax']->render(array("class" => "formInput", "maxlength" => 32)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <?php echo $form['notes']->renderLabel(__('Notes')); ?>
            <?php echo $form['notes']->render(array("class" => "formInput", "maxlength" => 256)); ?>
            <div class="errorHolder"></div>
	    <br class="clear"/>
	    
	    <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="button" class="cancelbutton" name="btnCancel" id="btnCancel"
                       value="<?php echo __("Cancel"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            </div>
	    
	</form>
    </div>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>

<script type="text/javascript">
	var locations = <?php echo str_replace('&#039;', "'", $form->getLocationListAsJson()) ?> ;
        var locationList = eval(locations);
	var lang_LocNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_CountryRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_ValidCountry = '<?php echo __(ValidationMessages::INVALID); ?>';
	var lang_validPhoneNo = '<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>';
	var lang_validFaxNo = '<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>';
	var lang_Max100Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
	var lang_Max50Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var lang_Max30Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>';
	var lang_Max255Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
	var lang_editLocation = "<?php echo __("Edit Location"); ?>";
	var locationId = "<?php echo $locationId ?>";
	var viewLocationUrl = "<?php echo url_for("admin/viewLocations"); ?>";
	var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
	var lang_save = "<?php echo __("Save"); ?>";
	var lang_edit = "<?php echo __("Edit"); ?>";
</script>