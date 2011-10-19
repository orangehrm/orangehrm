<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Nationality & Race :Ethnic Races")?></h2></div>
	<?php echo message()?>
	<form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="ethnic_race_code" <?php if($searchMode == 'ethnic_race_code'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="ethnic_race_desc" <?php if($searchMode == 'ethnic_race_desc'){ echo "selected";}?>><?php echo __("Name")?></option>
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
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteEthnicRace') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('ethnic_race_code', __('Ethnic Race ID'), '@ethnicRace_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo $sorter->sortLink('ethnic_race_desc', __('Ethnic Race Name '), '@ethnicRace_list', ESC_RAW); ?>
						 
					</td>  	  
				
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($listEthnicRace as $ethnicRace){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $ethnicRace->getEthnicRaceCode()?>' />
					</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateEthnicRace?id='.$ethnicRace->getEthnicRaceCode())?>"><?php echo $ethnicRace->getEthnicRaceCode()?></a>
		 			</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateEthnicRace?id='.$ethnicRace->getEthnicRaceCode())?>"><?php echo $ethnicRace->getEthnicRaceDesc()?></a>
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveEthnicRace')) ?>";

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

    