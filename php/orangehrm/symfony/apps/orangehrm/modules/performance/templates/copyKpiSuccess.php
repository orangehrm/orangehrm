<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js'); ?>"></script>
<div id="content">
    <div id="contentContainer">
        <?php if (count($listJobTitle) == 0) {
        ?>
            <div id="messageBalloon_notice" class="messageBalloon_notice">
                <?php echo __("No Defined Job Titles") ?> <a href="<?php echo url_for('admin/viewJobTitleList') ?>"><?php echo __("Define Now") ?></a>
            </div>
        <?php } ?>
        <?php if ($confirm) {
 ?>
            <div id="messageBalloon_notice" class="messageBalloon_notice">
                <?php echo __("KPI Already Exists, This Operation Deletes Existing KPI") ?> &nbsp;&nbsp;<a href="javascript:confirmOverwrite();"><?php echo __("Ok") ?></a> &nbsp;&nbsp;<a href="javascript:cancelOverwrite();"><?php echo __("Cancel") ?></a>
            </div>
<?php } ?>

        <div class="outerbox">
            <div id="formHeading" class="mainHeading"><h2><?php echo __("Copy Key Performance Indicators") ?></h2></div>
            <form action="#" id="frmSave" class="content_inner" method="post">

<?php echo $form['_csrf_token']; ?>

                <input type="hidden" id="txtConfirm" name="txtConfirm" value="0">

                <div id="formWrapper">
                    <label for="txtLocationCode"><?php echo __("Copy From") ?><span class="required">*</span></label>
                    <select name="txtJobTitle" id="txtJobTitle" class="formSelect" tabindex="1">
                        <option value="">--<?php echo __("Select") ?>--</option>
<?php foreach ($listAllJobTitle as $jobTitle) { ?>
                        <option value="<?php echo $jobTitle->getId() ?>" <?php
                        if ($fromJobTitle == $jobTitle->getId()) {
                            print("selected");
                        }
?>><?php echo htmlspecialchars_decode($jobTitle->getJobTitleName()) ?><?php echo ($jobTitle->getIsDeleted() == JobTitle::DELETED) ? ' ('.__('Deleted').')' : '' ?></option>
<?php } ?>
                    </select>
                    <br class="clear"/>
                    <label for="txtLocationCode"><?php echo __("Copy To") ?><span class="required">*</span></label>
                    <select name="txtCopyJobTitle" id="txtCopyJobTitle" class="formSelect" tabindex="1">
                        <option value="">--<?php echo __("Select") ?>--</option>
                                <?php foreach ($listJobTitle as $jobTitle) {
 ?>
                            <option value="<?php echo $jobTitle->getId() ?>" <?php
                                    if ($toJobTitle == $jobTitle->getId()) {
                                        print("selected");
                                    }
                                ?>><?php echo htmlspecialchars_decode($jobTitle->getJobTitleName()) ?></option>
<?php } ?>
                    </select>
                    <br class="clear"/>
                    <br class="clear"/>
                </div>
                <div id="buttonWrapper" class="formbuttons">
                    <input type="button" class="savebutton" id="saveBtn"
                           value="<?php echo __('Save')?>" tabindex="6" />

                    <input type="button" class="savebutton" id="resetBtn"
                           value="<?php echo __('Reset')?>" tabindex="7" />

                </div>

            </form>
        </div>
        <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    </div>

    <style type="text/css">
        form#frmSave.content_inner div#formWrapper .formSelect{
            width: 200px;
        }
    </style>


    <script type="text/javascript">
	    

        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
            //roundBorder('outerboxList');
        }
	
    </script>

    <script type="text/javascript">

        $(document).ready(function() {

            //Validate the form
            $("#frmSave").validate({
				
                rules: {
                    txtJobTitle: { required: true },
                    txtCopyJobTitle: { required: true, notEqual: true }
                },
                messages: {
                    txtJobTitle: {
                            required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                        },
                    txtCopyJobTitle: {
                            required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                            notEqual: '<?php echo __(ValidationMessages::INVALID); ?>'
                        }
			 		
                }
            });

            $.validator.addMethod("notEqual", function(value, element, param) {
                    var fromJobTitleValue = $('#txtJobTitle').val();
                    var toJobTitleValue = $('#txtCopyJobTitle').val();
                    return this.optional(element) || fromJobTitleValue != toJobTitleValue;
                  }, 
                  '<?php echo __(ValidationMessages::INVALID); ?>'
            );
            
            // when click Save button
            $('#saveBtn').click(function(){
                $('#frmSave').submit();
            });

            // when click reset button
            $('#resetBtn').click(function(){
                $("label.error").each(function(i){
                    $(this).remove();
                });
                document.forms[0].reset('');
            });

        });
		
        function confirmOverwrite(){
            $('#txtConfirm').val('1');
            $('#frmSave').submit();
        }

        function cancelOverwrite(){
            location.href = "<?php echo url_for('performance/listDefineKpi') ?>";
        }
    </script>
