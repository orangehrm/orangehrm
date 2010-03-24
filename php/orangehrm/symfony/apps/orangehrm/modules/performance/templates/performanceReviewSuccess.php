<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js');?>"></script>
<div id="content">
	<div id="contentContainer">
        <div class="outerbox">
            <div id="formHeading"><h2><?php echo __("Performance Review")?></h2></div>
			
			<form action="#" id="frmSave" class="content_inner" method="post">
			<input type="hidden" name="id" id="id" value="<?php echo $performanceReview->getId()?>"></input>
			<input type="hidden" name="saveMode" id="saveMode" value=""></input>
			
              	<div id="formWrapper">
                     <label class="detailHearder"><?php echo __("Employee")?></label>
                     <label class="detail"><?php echo $performanceReview->getEmployee()->getFirstName()?> <?php echo $performanceReview->getEmployee()->getLastName()?></label>
                   <br class="clear"/>
                   <label class="detailHearder"><?php echo __("Job Title")?></label>
                     <label class="detail"><?php echo htmlspecialchars_decode($performanceReview->getJobTitle()->getName())?> </label>
                   <br class="clear"/>
				     <label class="detailHearder"><?php echo __("Reviewer")?></label>
                     <label class="detail"><?php echo $performanceReview->getReviewer()->getFirstName()?> <?php echo $performanceReview->getReviewer()->getLastName()?></label>
                   <br class="clear"/>
				     <label class="detailHearder"><?php echo __("Review Period")?></label>
                     <label class="detail"><?php echo $performanceReview->getPeriodFrom()?>-<?php echo $performanceReview->getPeriodTo()?></label>
                   <br class="clear"/>
					 <label class="detailHearder"><?php echo __("Status")?></label>
                     <label class="detail"><?php echo $performanceReview->getTextStatus()?> </label>
                   <br class="clear"/>
					 <?php if( count($performanceReview->getPerformanceReviewComment()) > 0){?>
					 <label class="detailHearder"><?php echo __("Notes")?></label>
					 <label class="detail">
					 
						 <table width="400px">
						 <th>
						 	<tr>
						 		<td width="100px"><b><?php echo __("Date")?></b></td>
						 		<td width="150px"><b><?php echo __("Employee")?></b></td>
						 		<td width="150px"><b><?php echo __("Comment")?></b></td>
						 	</tr>
						 </th>
	                     <?php foreach( $performanceReview->getPerformanceReviewComment() as $comment){?>
	                     	<tr>
	                     		<td ><?php echo $comment->getCreateDate()?></td>
	                     		<td ><?php echo ($comment->getEmployee()->getFullName() != '')? $comment->getEmployee()->getFullName():'Admin'?></td>
	                     		<td ><?php echo $comment->getComment()?></td>
	                     	</tr>
	                     
	                     <?php }?>
	                     </table>
                     
                     </label>
                   <br class="clear"/>
                   <?php }?>
				   
				   <div id="tableWrapper">
				   <input type="hidden" name="validRate" id="validRate" value="1"></input>
				   <div id="performanceError" class="hide">
				   	<div id='messageBalloon_failure' class='messageBalloon_failure' ><ul></ul></div>
				   </div>
				<table cellpadding="0" cellspacing="0" class="data-table" align="center">
					<thead>
            		<tr>
            			
            			<td width="10px" scope="col">
						 
						</td>
						<td width="300" scope="col">
						<?php echo __("KPI/Question")?> 
						</td>
						<td scope="col" width="70">
						 <?php echo __("Min Rate")?>
						</td>
						<td scope="col" width="70">
						 <?php echo __("Max Rate")?>
						</td>  
						<td scope="col" width="50">
						 <?php echo __("Rating")?>
						</td>
						<td scope="col" width="150">
						 <?php echo __("Reviewer Comments")?>
						</td>
            		</tr>
    			</thead>
            	<tbody>
            	<?php foreach( $kpiList as $kpi){?>
            		<tr class="odd">
            				<td class="">
				 				
				 			</td>
		       				<td class="">
				 				<?php echo $kpi->getKpi()?>
				 			</td>
				 			<td>
				 				 <?php echo ($kpi->getMinRate()!= '')?$kpi->getMinRate():'-'?> 
				 			</td>
				 			<td>
				 				 <?php echo ($kpi->getMaxRate() !='')?$kpi->getMaxRate():'-'?> 
				 			</td>
							<td class="" >
								<input type="hidden" name="max<?php echo $kpi->getId()?>" id="max<?php echo $kpi->getId()?>" value="<?php echo $kpi->getMaxRate()?>"></input>
								<input type="hidden" name="min<?php echo $kpi->getId()?>" id="min<?php echo $kpi->getId()?>" value="<?php echo $kpi->getMinRate()?>"></input>
				 				 <input id="txtRate<?php echo $kpi->getId()?>"  name="txtRate[<?php echo $kpi->getId()?>]" type="text"  class="smallInput" value="<?php echo trim($kpi->getRate())?>"  maxscale="<?php echo $kpi->getMaxRate()?>" minscale="<?php echo $kpi->getMinRate()?>" valiadate="1" />
				 			</td>
							<td class="">
				 				<textarea id='txtComments' class="reviwerComment" name='txtComments[<?php echo $kpi->getId()?>]' 
                    rows="1" cols="20" ><?php echo htmlspecialchars_decode(trim($kpi->getComment()))?></textarea>
				 			</td>
				 				
				 	</tr>
				 <?php } ?>
					
					</tbody>
				</table>
				</div>
				<?php if(($isHrAdmin || $isReviwer) && ($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)){?>
				  <label class="detailHearder"><?php echo __("Note")?></label>
                     <textarea id='txtMainComment' name='txtMainComment' class="formTextArea"
                    rows="3" cols="20" tabindex="2"></textarea>
                   <br class="clear"/>
                 <?php }?>
               </div>  
            </form> 
				<div id="buttonWrapper">&nbsp;
                    <?php if(($isReviwer && ($performanceReview->getState() <= PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState()==PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) || ( $isHrAdmin && $performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)){?>  
                    <input type="button" class="savebutton" id="saveBtn"
                        value="<?php echo __("Edit")?>"  />
                      <?php }?>  
                      
                    <?php if( $isReviwer && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED ||  $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED ||  $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)){?>  
					<input type="button" class="savebutton" id="submitBtn"
                        value="<?php echo __("Submit")?>"  />
                     <?php } ?>
                     
                     <?php if( $isHrAdmin && $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED){?>  
                     <input type="button" class="savebutton" id="rejectBtn"
                        value="<?php echo __("Reject")?>"  />   
                      <?php } ?>
                      
                      <?php if( $isHrAdmin && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED )){?>   
                      <input type="button" class="savebutton" id="approveBtn"
                        value="<?php echo __("Approve")?>"  />  
                      <?php }?>
                      
                </div>  
              
			
				
        </div>
 	</div>
   
   
 </div>
  <script type="text/javascript">

	//Check autosave
  	function autosave() 
	  { 
	      var t = setTimeout("autosave()", 20000); 
	   
	      var title = $("#txt_title").val(); 
	      var content = $("#txt_content").val(); 
	   
	      if (title.length > 0 || content.length > 0) 
	      { 
	          $.ajax( 
	          { 
	              type: "POST", 
	              url: "autosave.php", 
	              data: "article_id=" + <?php echo $article_id ?> 
	  + "&title=" + title + "&content=" + content, 
	              cache: false, 
	              success: function(message) 
	              { 
	                  $("#timestamp").empty().append(message); 
	              } 
	          }); 
	      } 
	  }

      //Check submit 
	  function checkSubmit(){
		  var valid	=	true ;
		  var msg	=	'';
		  $("input.smallInput").each(function() {
			  max	=	parseFloat($(this).attr('maxscale'));
			  min =   parseFloat($(this).attr('minscale'));
			  rate =  parseFloat(this.value) ;
				
			  if( !isNaN(max) || !isNaN(min)){
				  if( isNaN(rate)){
					  valid = false;
				  }
			  }	  
		  });
		  if( !valid ){
			  msg	=	'Please add rating value ';
			  $("#messageBalloon_failure ul").html('<li>'+msg+'</li>');
			  $("#performanceError").show();
		  }
		  return valid ;
	  }
	  
		
	  $(document).ready(function(){ 
		  	var mode	=	'edit';
			
			//Disable all fields
			$('#frmSave :input').attr('disabled', true);
			$('#saveBtn').removeAttr('disabled');

			//When click edit button
			 $("#saveBtn").click(function() {
					if( mode == 'edit')
					{
						$('#saveBtn').attr('value', '<?php echo __("Save")?>'); 
						$('#frmSave :input').removeAttr('disabled');
						mode = 'save';
					}else
					{
						$('#saveMode').val('save');
						$('#frmSave').submit();
					}
				});
			
			//When Submit button click
				$("#submitBtn").click(function() {
					$('#frmSave :input').removeAttr('disabled');
					if(checkSubmit()){
						$('#saveMode').val('submit');
						$('#frmSave').submit();
					}
				});

			//When Submit button click
				$("#rejectBtn").click(function() {
					$('#frmSave :input').removeAttr('disabled');
					$('#saveMode').val('reject');
					$('#frmSave').submit();
				});

			//When Submit button click
				$("#approveBtn").click(function() {
					$('#frmSave :input').removeAttr('disabled');
					$('#saveMode').val('approve');
					$('#frmSave').submit();
				});

			//Validate search form 
				 $("#frmSave").validate({
						
					 rules: {
					 	txtMainComment: {maxlength: 200},
					 	validRate: {minmax:true	}
					 	
				 	 },
				 	 messages: {
				 		txtMainCommet: "<?php echo __('Comments length exceed')?>",
				 		validRate: ""
				 	 }
				 });


				 $.validator.addMethod("minmax", function(value, element) {
						
					 	if($('#validRate').val() == '1' )
							return true;
					 	else
					 		return false;
					});

				// check keyup on scale inputs 
					$("#frmSave").delegate("keyup", "input.smallInput", function(event) {
						var id ;
						var max ;
						var min ;
						var rate ;
						var msg ;
						var error = false;
						$("input.smallInput").each(function() {

							id	=	$(this).attr('id');
							max	=	parseFloat($(this).attr('maxscale'));
							min =   parseFloat($(this).attr('minscale'));
							rate =  parseFloat(this.value) ;
							if(!isNaN(this.value)){
								
								if( (rate > max) || (rate <min) ){
									$(this).css('background-color', '#ffeeee');
									$(this).css('border', 'solid 1px #ffdddd');
									msg = 'Rate is not in max-min range';
									error = true;
	
								}else{
									$(this).css('background-color', '#ffffff');
									$(this).css('border', 'solid 1px #000000');	
								}
							}else{
								$(this).css('background-color', '#ffeeee');
								$(this).css('border', 'solid 1px #ffdddd');
								msg = 'Rate is not numeric';
								error = true;
							}
						});

						if(error){
							$("#messageBalloon_failure ul").html('<li>'+msg+'</li>');
							$("#performanceError").show();
							$('#validRate').val('0');									
						}else
						{
							$("#performanceError").hide();
							$('#validRate').val('1');
						}
						
						return false;
					});

					//Check Reviwer comment
					$("#frmSave").delegate("keyup", "textarea.reviwerComment", function(event) {
						var error = false;
						var msg ;
						
						$("textarea.reviwerComment").each(function() {
							if(this.value.length >= 100 ){
								error = true;
							}
						});

						if(error){
							$("#messageBalloon_failure ul").html('<li>Comments length exceed</li>');
							$("#performanceError").show();
							$('#validRate').val('0');									
						}else{
							$("#performanceError").hide();
							$('#validRate').val('1');
						}
					});
						
							
		}); 
  </script>