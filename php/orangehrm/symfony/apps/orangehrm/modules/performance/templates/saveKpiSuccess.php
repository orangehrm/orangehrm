<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js');?>"></script>
<div id="content">
	<div id="contentContainer">
	<?php if(count($listJobTitle) == 0){?>
			<div id="messageBalloon_notice" class="messageBalloon_notice">
				<ul><li><?php echo __("Job titles haven't been defined")?> <a href="<?php echo '../../../.././lib/controllers/CentralController.php?uniqcode=JOB&amp;VIEW=MAIN' ?>"><?php echo __("Define Now")?></a></li></ul>
			</div>
		<?php }?>
		<?php echo message()?>
        <div class="outerbox">
            <div id="formHeading" class="mainHeading"><h2>Add Key Performance Indicator</h2></div>
			
			<form action="#" id="frmSave" class="content_inner" method="post">

                        <?php echo $form['_csrf_token']; ?>

              <div id="formWrapper">
                       <label for="txtLocationCode"><?php echo __('Job Title')?><span class="required">*</span></label>
                     <select name="txtJobTitle" id="txtJobTitle" class="formSelect" tabindex="1" >
                     	<option value=""><?php echo __('Select Job Title')?></option>
	                     <?php foreach($listJobTitle as $jobTitle){?>
	                     	<option value="<?php echo $jobTitle->getId()?>"><?php echo htmlspecialchars_decode($jobTitle->getName())?></option>
	                     <?php }?>
                     </select>
                   <br class="clear"/>
                   <label for="txtDescription"><?php echo __('Key Performance Indicator')?><span class="required">*</span></label>
                   <textarea id='txtDescription' name='txtDescription'
                    rows="4" cols="40" tabindex="2"></textarea>
             		 <br class="clear"/>
             		 <label for="txtMinRate"><?php echo __('Minimum Rating')?></label>
                    <input id="txtMinRate"  name="txtMinRate" type="text"  class="formInputText" value="<?php echo $defaultRate['min']?>" tabindex="3" />
             		 <br class="clear"/>
             		 <label for="txtMaxRate"><?php echo __('Maximum Rating')?></label>
                    <input id="txtMaxRate"  name="txtMaxRate" type="text"  class="formInputText" value="<?php echo $defaultRate['max']?>" tabindex="4" />
             		 <br class="clear"/>
             		  <label for="chkDefaultScale"><?php echo __('Make Default Scale')?></label>
                    <input type="checkbox"  name="chkDefaultScale" id="chkDefaultScale" class="alignCheckbox" tabindex="5"  value="1"></input>
             		 <br class="clear"/>
             	</div>
				<div id="buttonWrapper" class="formbuttons">
                    <input type="button" class="savebutton" id="saveBtn"
                        value="Save" tabindex="6" />
                        
                     <input type="button" class="savebutton" id="resetBtn"
                        value="Reset" tabindex="7" />
                    
                </div>  
              
            </form>
        </div>
 	</div>
</div>

 	   <script type="text/javascript">


</script>
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
				 	txtDescription: { required: true ,maxlength: 200},
				 	txtMinRate: { number: true,minmax:true,maxlength: 5},
				 	txtMaxRate: { number: true,minmax:true ,maxlength: 5}
			 	 },
			 	 messages: {
			 		txtJobTitle: "<?php echo __('Job Title is required')?>", 
			 		txtDescription:{ 
			 			required:"<?php echo __('Key Performance Indicator is required')?>",
			 			maxlength:"<?php echo __('Key Performance Indicator length exceeded')?>"
			 		},
			 		txtMinRate:{ 
				 		number:"<?php echo __("Minimum Rating should be numeric")?>",
				 		minmax:"<?php echo __("Minimum Rating should be lesser than Maximum Rating")?>",
				 		maxlength:"<?php echo __('Minimum Rating length exceed')?>"
			 		},
			 		txtMaxRate: {
				 		number:"<?php echo __("Maximum Rating should be numeric")?>", 
				 		minmax:"<?php echo __("Minimum Rating should be lesser than Maximum Rating")?>",
				 		maxlength:"<?php echo __('Maximum Rating length exceed')?>"
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
