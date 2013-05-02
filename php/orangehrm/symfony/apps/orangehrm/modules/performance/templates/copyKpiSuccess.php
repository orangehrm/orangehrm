<div class="box" >
        
    <div class="head"><h1><?php echo __("Copy Key Performance Indicators") ?></h1></div>

    <div class="inner">

        <?php if (count($listJobTitle) == 0) : ?>
            <div class="message warning">
                <?php echo __("No Defined Job Titles") ?> 
                <a href="<?php echo url_for('admin/viewJobTitleList') ?>"><?php echo __("Define Now") ?></a>
                <a href="#" class="messageCloseButton"><?php echo __('Close');?></a>
            </div>
        <?php endif; ?>
        <?php if ($confirm) : ?>
            <div class="message warning">
                <?php echo __("KPI Already Exists, This Operation Deletes Existing KPI") ?> 
                <a href="javascript:confirmOverwrite();"><?php echo __("Ok") ?></a> 
                <a href="javascript:cancelOverwrite();"><?php echo __("Cancel") ?></a> 
                <a href="#" class="messageCloseButton"><?php echo __('Close');?></a>
            </div>
        <?php endif; ?>        
        
        <?php include_partial('global/flash_messages'); ?>

        <form id="frmSave" method="post">

            <?php echo $form['_csrf_token']; ?>
                
            <fieldset>

                <ol>

                    <li>
                        <input type="hidden" id="txtConfirm" name="txtConfirm" value="0">
                        <label for="txtLocationCode"><?php echo __("Copy From" . ' <em>*</em>') ?></label>
                        <select name="txtJobTitle" id="txtJobTitle" tabindex="1">
                         <option value="">--<?php echo __("Select") ?>--</option>
                          <?php foreach ($listAllJobTitle as $jobTitle) { ?>
                         <option value="<?php echo $jobTitle->getId() ?>" <?php if ($fromJobTitle == $jobTitle->getId()) { print("selected"); } ?>>
                          <?php echo $jobTitle->getJobTitleName(); ?>
                          <?php echo ($jobTitle->getIsDeleted() == JobTitle::DELETED) ? ' ('.__('Deleted').')' : '' ?>
                         </option>
                        <?php } ?>
                        </select>
                    </li>

                    <li> 
                        <label for="txtLocationCode"><?php echo __("Copy To" . ' <em>*</em>') ?></label>
                        <select name="txtCopyJobTitle" id="txtCopyJobTitle" tabindex="1">
                         <option value="">--<?php echo __("Select") ?>--</option>
                          <?php foreach ($listJobTitle as $jobTitle) { ?>
                         <option value="<?php echo $jobTitle->getId() ?>"<?php if ($toJobTitle == $jobTitle->getId()) { print("selected"); } ?>>
                         <?php echo $jobTitle->getJobTitleName(); ?></option>
                         <?php } ?>              
                        </select>
                    </li>

                   <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                   </li>

                </ol>

                <p>
                    <input type="button" id="saveBtn" value="<?php echo __('Copy') ?>" tabindex="3" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __('Reset') ?>" tabindex="4" />
                </p>

            </fieldset>  
                
        </form>            
    </div>
</div>

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
