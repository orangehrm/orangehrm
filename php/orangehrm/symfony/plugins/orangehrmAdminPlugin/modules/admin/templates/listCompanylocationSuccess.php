<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<link href="../../themes/orange/css/style.css" rel="stylesheet" type="text/css"/>
<div class="outerbox">
<div class="maincontent">
	
	<div class="mainHeading"><h2><?php echo __("Company Info : Locations")?></h2></div>
<!-- 
	<div>
		<ul>
			<li>dahdadadhadh</li>
		</ul>
	</div> -->
	  <?php echo message()?>
	 <form name="frmSearchBox" id="frmSearchBox" method="post" action="">
	 	<input type="hidden" name="mode" value="search"></input>
		<div class="searchbox">
	        <label for="searchMode"><?php echo __("Search By")?></label>
	        <select name="searchMode" id="searchMode">
	            <option value="all"><?php echo __("--Select--")?></option>
	            <option value="loc_code" <?php if($searchMode == 'loc_code'){ echo "selected";}?>><?php echo __("ID")?></option>
	            <option value="loc_name" <?php if($searchMode == 'loc_name'){ echo "selected";}?>><?php echo __("Name")?></option>
	            <option value="loc_city" <?php if($earchMode == 'loc_city'){ echo "selected";}?>><?php echo __("City Name")?></option>
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
        <div class="pagingbar">
        
         </div>
    <br class="clear" />
    </div>
     <br class="clear" />
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/deleteCompanyLocation') ?>">
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('loc_code', __('Location ID'), '@location_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						<?php echo $sorter->sortLink('loc_name', __('Location Name'), '@location_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						<?php echo $sorter->sortLink('loc_city', __('City Name'), '@location_list', ESC_RAW); ?>
					</td>
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($locationList as $location){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
				
							<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $location->getLocCode()?>' />
						
					</td>
					
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateCompanyLocation?id='.$location->getLocCode())?>"><?php echo $location->getLocCode()?></a>
		 			</td>
					<td class="">
		 				<a href="<?php echo url_for('admin/updateCompanyLocation?id='.$location->getLocCode())?>"><?php echo $location->getLocName()?></a>
		 			</td>
		 			<td class="">
		 				<?php echo $location->getLocCity()?>
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveCompanyLocation')) ?>";

   });

	$("#allCheck").change(function() {
		if ($('#allCheck').attr('checked')) {
			$('.innercheckbox').attr('checked','checked');
		}else{
			$('.innercheckbox').removeAttr('checked');
		}
		
	});

	$("#buttonRemove").click(function() {
		$("#standardView").submit();
	});	

	
});


</script>

    