<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Users : Admin User Groups")?></h2></div>
		<?php echo message()?>
	<form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="userg_id" <?php if($searchMode == 'userg_id'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="userg_name" <?php if($searchMode == 'userg_name'){ echo "selected";}?>><?php echo __("Name")?></option>
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
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteUserGroup') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('userg_id', __('User Group ID'), '@usergroup_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo $sorter->sortLink('userg_name', __('User Group Name'), '@usergroup_list', ESC_RAW); ?>
						 
					</td>  	  
				
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($listUserGroup as $userGroup){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $userGroup->getUsergId()?>' />
					</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateUserGroup?id='.$userGroup->getUsergId())?>"><?php echo $userGroup->getUsergId()?></a>
		 			</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateUserGroup?id='.$userGroup->getUsergId())?>"><?php echo $userGroup->getUsergName()?></a>
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveUserGroup')) ?>";

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

    