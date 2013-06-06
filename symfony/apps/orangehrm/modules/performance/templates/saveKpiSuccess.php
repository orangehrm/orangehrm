<div class="box" >
    
    <div class="head"><h1><?php echo __("Add Key Performance Indicator") ?></h1></div>
    
	<div class="inner">
        
        <?php if(count($listJobTitle) == 0) : ?>
            <div class="message warning">
                <?php echo __("No Defined Job Titles") ?> 
                <a href="<?php echo url_for('admin/viewJobTitleList') ?>"><?php echo __("Define Now") ?></a>
                <a href="#" class="messageCloseButton"><?php echo __('Close');?></a>
            </div>
        <?php endif; ?>        
        
        <?php include_partial('global/flash_messages'); ?>
        
	    <form id="frmSave" method="post">

        <?php echo $form['_csrf_token']; ?>
              
                <fieldset>
                    
                    <ol>
                        
                        <li>
                            <label for="txtLocationCode"><?php echo __('Job Title'.' <em>*</em>')?></label>
                             <select name="txtJobTitle" id="txtJobTitle" tabindex="1" >
                     	       <option value=""><?php echo '--'.__('Select').'--'?></option>
	                           <?php foreach($listJobTitle as $jobTitle){?>
	                     	   <option value="<?php echo $jobTitle->getId()?>"><?php echo $jobTitle->getJobTitleName() ?></option>
	                           <?php }?>
                             </select>
                        </li>
                        
                        <li class="largeTextBox"> 
                            <label for="txtDescription"><?php echo __('Key Performance Indicator'.' <em>*</em>')?></label>
                            <textarea id='txtDescription' name='txtDescription' rows="4" cols="40" tabindex="2"></textarea>
                        </li>
                        
                        <li>
                            <label for="txtMinRate"><?php echo __('Minimum Rating')?></label>
                            <input id="txtMinRate"  name="txtMinRate" type="text" value="<?php echo $defaultRate['min']?>" tabindex="3" />
             		    </li>
                        
                        <li>
                            <label for="txtMaxRate"><?php echo __('Maximum Rating')?></label>
                            <input id="txtMaxRate"  name="txtMaxRate" type="text" value="<?php echo $defaultRate['max']?>" tabindex="4" />
             		    </li>
                        
                        <li>
                            <label for="chkDefaultScale"><?php echo __('Make Default Scale')?></label>
                            <input type="checkbox"  name="chkDefaultScale" id="chkDefaultScale" tabindex="5"  value="1"></input>
             		    </li>  
                        
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                            
                    </ol>
                        
                    <p>
                        <input type="button" id="saveBtn" value="<?php echo __('Save')?>" tabindex="6" />
                        <input type="button" class="reset" id="resetBtn" value="<?php echo __('Reset')?>" tabindex="7" />
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
				 	txtDescription: { required: true ,maxlength: 250},
				 	txtMinRate: { number: true,minmax:true,maxlength: 5},
				 	txtMaxRate: { number: true,minmax:true ,maxlength: 5}
			 	 },
			 	 messages: {
			 		txtJobTitle: '<?php echo __(ValidationMessages::REQUIRED); ?>', 
			 		txtDescription:{ 
			 			required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
			 			maxlength:"<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250))?>"
			 		},
			 		txtMinRate:{ 
				 		number:"<?php echo __("Should be a number")?>",
				 		minmax:"<?php echo __("Minimum Rating should be less than Maximum Rating")?>",
				 		maxlength:"<?php echo __("Should be less than %number% digits", array('%number%' => 5))?>"
			 		},
			 		txtMaxRate: {
				 		number:"<?php echo __("Should be a number")?>", 
				 		minmax:"<?php echo __("Minimum Rating should be less than Maximum Rating")?>",
				 		maxlength:"<?php echo __("Should be less than %number% digits", array('%number%' => 5))?>"
				 		}
			 	 }
			 });

			//Add custom function to validator
				$.validator.addMethod("minmax", function(value, element) {
					if( $('#txtMinRate').val() !='' && $('#txtMaxRate').val() !='')
				    	return ((parseFloat($('#txtMinRate').val())) < (parseFloat($('#txtMaxRate').val())));
					else
						return true;
				});

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

		
		
	</script>
