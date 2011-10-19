<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Memberships : Membership Types")?></h2></div>
		<?php echo message()?>
	<form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="membtype_code" <?php if($searchMode == 'membtype_code'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="membtype_name" <?php if($searchMode == 'membtype_name'){ echo "selected";}?>><?php echo __("Name")?></option>
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
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteMembershipType') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('membtype_code', __('Membership Type ID'), '@membershipType_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo $sorter->sortLink('membtype_name', __('Membership Name'), '@membershipType_list', ESC_RAW); ?>
						 
					</td>  	  
				
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($listMembershipType as $membershipType){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $membershipType->getMembtypeCode()?>' />
					</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateMembershipType?id='.$membershipType->getMembtypeCode())?>"><?php echo $membershipType->getMembtypeCode()?></a>
		 			</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateMembershipType?id='.$membershipType->getMembtypeCode())?>"><?php echo $membershipType->getMembtypeName()?></a>
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveMembershipType')) ?>";

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

    