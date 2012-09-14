<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/pimCsvImport'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/pimCsvImport'); ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="pimCsvImport">
            <div class="outerbox">

                <div class="mainHeading"><h2 id="pimCsvImportHeading"><?php echo __("CSV Data Import"); ?></h2></div>
                <form name="frmPimCsvImport" id="frmPimCsvImport" method="post" action="<?php echo url_for('admin/pimCsvImport'); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            <br class="clear"/>
	    <div class="newColumn">
                <?php echo $form['csvFile']->renderLabel(__('Select File').' <span class="required">*</span>',array("class" => "csvFileLabel")); ?>
                <?php echo $form['csvFile']->render(array("class" => "csvFile")); ?>
                <div class="errorHolder"></div>
            </div>
	    <br class="clear"/>
	    <div class="hrLine"></div>
            <ul id="ulInstructions">
                <li><?php echo __("Column order should not be changed"); ?></li>
                <li><?php echo __("First Name and Last Name are compulsory");?></li>
                <li><?php echo __("All date fields should be in yyyy-mm-dd format");?></li>
                <li><?php echo __("If gender is specified, value should be either") . ' <span class="boldText">Male</span> ' . __('or') . ' <span class="boldText">Female</span>'; ?></li>
                <li><?php echo __("Each import file should be configured for 100 records or less");?></li>
                <li><?php echo __("Multiple import files may be required");?></li>
                <li><?php echo __("Sample CSV file").': '; ?><a title="<?php echo __("Download"); ?>" target="_blank" class="download"
                       href="<?php echo url_for('admin/sampleCsvDownload');?>"><?php echo __("Download"); ?></a></li>
            </ul>

	    <div class="formbuttons">
                    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __("Upload"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
	    </div>
	    </div>
    </form>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<script type="text/javascript">
	var linkForDownloadCsv = '<?php url_for('admin/sampleCsvDownload');?>';
	var lang_csvRequired = '<?php echo __(ValidationMessages::REQUIRED);?>';
</script>