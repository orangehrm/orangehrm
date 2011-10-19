<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Nationality & Race : Nationality")?></h2></div>
		<?php echo message()?>
	<form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="nat_code" <?php if($searchMode == 'nat_code'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="nat_name" <?php if($searchMode == 'nat_name'){ echo "selected";}?>><?php echo __("Name")?></option>
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
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteNationality') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('nat_code', __('Nationality ID'), '@nationality_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo $sorter->sortLink('nat_name', __('Nationality Name '), '@nationality_list', ESC_RAW); ?>
						 
					</td>  	  
				
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($listNationality as $nationality){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $nationality->getNatCode()?>' />
					</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateNationality?id='.$nationality->getNatCode())?>"><?php echo $nationality->getNatCode()?></a>
		 			</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateNationality?id='.$nationality->getNatCode())?>"><?php echo $nationality->getNatName()?></a>
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveNationality')) ?>";

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

    