<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>

<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>
<div class="outerbox">
<div class="maincontent">

	<div class="mainHeading"><h2><?php echo __("Company Info: Company Property")?></h2></div>
	<?php echo message()?>
	
      <div class="actionbar">
        <div class="actionbuttons">
       
            <input type="button" class="plainbtn" id="buttonAdd"
                value="<?php echo __("Add")?>" />
                
            <input type="button" class="plainbtn" id="buttonSave"
                    value="<?php echo __("Save")?>" />
                 
             <input type="button" class="plainbtn" id="buttonRemove"
                    value="<?php echo __("Delete")?>" />    
        
        </div>
        <div class="noresultsbar"></div>
        <div class="pagingbar"> </div>
    <br class="clear" />
    </div>
     <br class="clear" />
     <form name="standardView" id="standardView" method="post" action="<?php echo url_for('admin/processCompnayProperty') ?>">
     <input type="hidden" name="mode" id="mode" value=""></input>
    	<table cellpadding="0" cellspacing="0" class="data-table">
			<thead>
            <tr>
				<td width="50">
				
					<input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
				
				</td>
				
					<td scope="col">
						 <?php echo $sorter->sortLink('prop_id', __('Property Name '), '@comproperty_list', ESC_RAW); ?>
					</td>
					<td scope="col">
						 <?php echo __('Property Name ') ?>
						 
					</td>
					
            </tr>
    		</thead>

            <tbody>
    		<?php 
    		 $row = 0;
    		foreach($proportyList as $proporty){
    			$cssClass = ($row %2) ? 'even' : 'odd';
				$row = $row + 1;
    			?>
				<tr class="<?php echo $cssClass?>">
       				<td >
						<input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $proporty->getPropId()?>' />
					</td>
		 			<td class="">
		 				<a href="<?php echo url_for('admin/updateCompanyProporty?id='.$proporty->getPropId())?>"><?php echo $proporty->getPropName()?></a>
		 			</td>
					<td class="">
						<select name="txtProperty[<?=$proporty->getPropId()?>]">
							
							<option value="0" selected="selected">Not Assigned</option>
							<?php foreach($employeeList as $employee){?>
								<option value="<?php echo $employee->getEmpNumber()?>" <?php if($proporty->getEmpId() == $employee->getEmpNumber()){echo "selected";}?>><?php echo $employee->getFirstName()?></option>
							<?php }?>
						</select>
		 				
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
		location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveCompanyProporty')) ?>";

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
	$("#buttonSave").click(function() {
		$("#mode").attr('value', 'save');
		$("#standardView").submit();
	});	


	  	
});


</script>

    