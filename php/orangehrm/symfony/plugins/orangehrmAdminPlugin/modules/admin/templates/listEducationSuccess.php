<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>

<div class="outerbox">
<div class="maincontent">
	<?php echo message()?>
	<div class="mainHeading"><h2><?php echo __("Qualification : Education")?></h2></div>
	<form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="eduCode" <?php if($searchMode == 'eduCode'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="edu_uni" <?php if($searchMode == 'edu_uni'){ echo "selected";}?>><?php echo __("Name")?></option>
	            <option value="edu_deg" <?php if($searchMode == 'edu_deg'){ echo "selected";}?>><?php echo __("Name")?></option>
	        </select>
	
	        <label for="searchValue">Search For:</label>
	        <input type="text" size="20" name="searchValue" id="searchValue" value="<?php echo $searchValue?>" />
	        <input type="submit" class="plainbtn" 
	            value="<?php echo __("Search")?>" />
	        <input type="reset" class="plainbtn" 
	             value="<?php echo __("Reset")?>" />
	        <br class="clear"/>
	    </div>
    </form>
      <div class="actionbar">
        <div class="actionbuttons">
       
            <input type="button" class="plainbtn" id="buttonAdd"
                value="<?php echo __("Add")?>" />
                
                 
             <input type="button" class="plainbtn" id="buttonRemove"
                    value="<?php echo __("Delete")?>" />    
        
        </div>
        <div class="noresultsbar"></div>
        <div class="pagingbar"> </div>
    <br class="clear" />
    </div>
     <br class="clear" />
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteEducation') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('eduCode', __('Education ID'), '@education_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo $sorter->sortLink('edu_uni', __('Course'), '@education_list', ESC_RAW); ?>
						 
					</td>  	  
					<td scope="col">
						 <?php echo $sorter->sortLink('edu_deg', __('Institute'), '@education_list', ESC_RAW); ?>
						 
					</td> 
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($listEducation as $education){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $education->getEduCode()?>' />
					</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateEducation?id='.$education->getEduCode())?>"><?php echo $education->getEduCode()?></a>
		 			</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateEducation?id='.$education->getEduCode())?>"><?php echo $education->getEduUni()?></a>
		 			</td>
					<td class="">
		 				<?php echo $education->getEduDeg()?>
		 			</td>
		 			
		 	</tr>
			 	<?php }?>
            </tbody>
 		</table>
</form>
</div>
</div>
<script type="text/javascript">

$(document).ready(function() {

	//When click add button 
	$("#buttonAdd").click(function() {
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveEducation')) ?>";

   });

	// When Click Main Tick box
	$("#allCheck").change(function() {
		if ($('#allCheck').attr('checked')) {
			$('.innercheckbox').attr('checked','checked');
		}else{
			$('.innercheckbox').removeAttr('checked');
		}
		
	});

	//When click remove button
	$("#buttonRemove").click(function() {
		$("#mode").attr('value', 'delete');
		$("#standardView").submit();
	});	

	//When click Save Button 
	$("#buttonRemove").click(function() {
		$("#mode").attr('value', 'save');
		$("#standardView").submit();
	});	


	  	
});


</script>

    