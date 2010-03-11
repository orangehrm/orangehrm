<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Job : Pay Grades")?></h2></div>
		<?php echo message()?>
	 <form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="sal_grd_code" <?php if($searchMode == 'sal_grd_code'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="sal_grd_name" <?php if($searchMode == 'sal_grd_name'){ echo "selected";}?>><?php echo __("Name")?></option>
	            
	        </select>
	
	        <label for="searchValue"><?php echo __("Search For:")?></label>
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
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteSaleryGrade') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('sal_grd_code', __('Pay Grade ID '), '@salerygrade_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						  <?php echo $sorter->sortLink('sal_grd_name', __('Pay Grade Name'), '@salerygrade_list', ESC_RAW); ?>
						 
					</td>
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($saleryGradeList as $saleryGrade){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $saleryGrade->getSalGrdCode()?>' />
					</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateSaleryGrade?id='.$saleryGrade->getSalGrdCode())?>"><?php echo  $saleryGrade->getSalGrdCode()?></a>
		 			</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateSaleryGrade?id='.$saleryGrade->getSalGrdCode())?>"><?php echo  $saleryGrade->getSalGrdName()?></a>
		 			</td>
		 			
		 	</tr>
			 	<?php }?>
            </tbody>
 		</table>
</form>
</div>
</div>
<script type="text/javascript">

function selectItem() {
	alert('sss');
	}

$(document).ready(function() {

	//When click add button 
	$("#buttonAdd").click(function() {
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveSaleryGrade')) ?>";

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

	//Validate the search form
	 $("#frmSearchBox").validate({
		
		 rules: {
		 	searchValue: { required: true }
	 	 },
	 	 messages: {
	 		searchValue: "Search Value is required" 
	 	 }
	 });
	  	
});


</script>

    