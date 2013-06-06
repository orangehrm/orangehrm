
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/pimCsvImport')); ?>

<div id="pimCsvImport" class="box">
    
    <div class="head">
        <h1 id="pimCsvImportHeading"><?php echo __("CSV Data Import"); ?></h1>
    </div>
            
    <div class="inner">
        
        <?php include_partial('global/flash_messages', array('prefix' => 'csvimport')); ?>
                
        <form name="frmPimCsvImport" id="frmPimCsvImport" method="post" action="<?php echo url_for('admin/pimCsvImport'); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            
            <fieldset>
                
                <ol class="normal">
                    
                    <li class="fieldHelpContainer">
                        <?php echo $form['csvFile']->renderLabel(__('Select File').' <em>*</em>'); ?>
                        <?php echo $form['csvFile']->render(); ?>
                        <label class="fieldHelpBottom"><?php echo __(CommonMessages::FILE_LABEL_SIZE); ?></label>
                    </li>
                    
                </ol>
                
                  <ul class="disc">
                    <li>
                        <?php echo __("Column order should not be changed"); ?>
                    </li>
                    <li>
                        <?php echo __("First Name and Last Name are compulsory");?>
                    </li>
                    <li>
                        <?php echo __("All date fields should be in YYYY-MM-DD format");?>
                    </li>
                    <li>
                        <?php echo __("If gender is specified, value should be either") . ' <span class="boldText">Male</span> ' . __('or') . ' <span class="boldText">Female</span>'; ?>
                    </li>
                    <li>
                        <?php echo __("Each import file should be configured for 100 records or less");?>
                    </li>
                    <li>
                        <?php echo __("Multiple import files may be required");?>
                    </li>
                    <li><?php echo __("Sample CSV file").': '; ?>
                        <a title="<?php echo __("Download"); ?>" target="_blank" class="download" 
                           href="<?php echo url_for('admin/sampleCsvDownload');?>"><?php echo __("Download"); ?></a>
                    </li>
                 </ul>
                
                <ol>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Upload"); ?>"/>
                </p>
                
            </fieldset>
    
        </form>
    
    </div>
    
</div>

<script type="text/javascript">
	var linkForDownloadCsv = '<?php url_for('admin/sampleCsvDownload');?>';
	var lang_csvRequired = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
</script>