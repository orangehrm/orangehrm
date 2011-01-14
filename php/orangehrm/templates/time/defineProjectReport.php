<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

$projects=$records[0];

$customerObj = new Customer();

//create a two-dimensional array to sort the project's dropdown list
if(isset($projects)) {
	foreach($projects as $project) {
		
		$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
		
		$projectAndCustomers = array();
		
		$projectAndCustomers['concat'] = $customerDet->getCustomerName()." - ".$project->getProjectName();
		$projectAndCustomers['project'] = $project;
		$projectAndCustomers['customer'] = $customerDet;
		
		$arrayProjectAndCustomers[] = $projectAndCustomers;
	}
	
	//sort the array by customer name - project name
	usort($arrayProjectAndCustomers, "compareConcatenatedName");
}

function compareConcatenatedName($a, $b){
    return strcmp($a["concat"], $b["concat"]);
}

function getDeletedProjects($val){

    $timeController = new TimeController();
    $objResponse = new xajaxResponse();
    $xajaxFiller = new xajaxElementFiller();

     $element="cmbProject";

    if ($val==1) {
        $projectList=$timeController->fetchIncludingDeletedProjects(1);
        $Response = $xajaxFiller->cmbFillerById($objResponse,$projectList, 0,'frmReport',$element, 0);
    } else {
        $projectList=$timeController->fetchIncludingDeletedProjects(0);
        $Response = $xajaxFiller->cmbFillerById($objResponse,$projectList, 0,'frmReport',$element, 0);
    }

    return $objResponse->getXML();

}

$objAjax = new xajax();
$objAjax->registerFunction('getDeletedProjects');
$objAjax->processRequests();

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<?php $objAjax->printJavascript(); ?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php include ROOT_PATH."/lib/common/calendar.php"; ?>
<script type="text/javascript">
var initialAction = "?timecode=Time&action=";

function getProjectlist(check){
    if (check) {
        xajax_getDeletedProjects(1);
    } else {
        xajax_getDeletedProjects(0);
        
    }

}

function viewProjectReport() {
	action = "Project_Report";

	if (validate()) {
		$('frmReport').action = initialAction+action;
		$('frmReport').submit();
	}

	return false;
}

function validate() {
	startDate = strToDate($("txtFromDate").value, YAHOO.OrangeHRM.calendar.format);
	endDate = strToDate($("txtToDate").value, YAHOO.OrangeHRM.calendar.format);

	errFlag=false;
	errors = new Array();

	if (-1 > $("cmbProject").value) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_ProjectNotSpecified_ERROR; ?>";
		errFlag=true;
	}

	if (!startDate || !endDate || (startDate > endDate)) {
		errors[errors.length] = "<?php echo $lang_Time_Errors_InvalidDateOrZeroOrNegativeRangeSpecified; ?>";
		errFlag=true;
	}

	if (errFlag) {
		errStr="<?php echo $lang_Common_EncounteredTheFollowingProblems; ?>\n";
		for (i in errors) {
			errStr+=" - "+errors[i]+"\n";
		}
		alert(errStr);

		return false;
	}

	return true;
}

function setProjects(check){
	
	var obj = document.getElementById('cmbProject');
	obj.length = 0;
	
	if(check == true) {
		
		<?php 
			if(count($projects)){
				
		   		$customerObj = new Customer();
				$count = 0; 
				
		    	foreach ($projects as $project) {
		    		
					if($project->getProjectId()  != ""){
						
						$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
		  ?>	
		  obj.options[<?php echo $count ?>] = new Option( '<?php  echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}" ?>' ,'<?php echo $project->getProjectId()  ?>');
		  <?php
		  			$count++;
		  			
		  			}
		  			
		  		}
		  		
			}else{
			?>
			  obj.options[-2] = new Option( '- <?php echo $lang_Time_Timesheet_NoProjects;?> - ' ,' -2 ');
			<?php	
			}
		  ?>
	}else{
		  <?php
		  if(count($projects)){
		  	
		  		$customerObj = new Customer();
		  		$count = 0; 
		  		
		    	foreach ($projects as $project) {
		    		
					if($project->getDeleted() == 0 ){
						
                     	$customerDet = $customerObj->fetchCustomer($project->getCustomerId(), true);
		  ?>	
		  obj.options[<?php echo $count?>] = new Option( '<?php  echo "{$customerDet->getCustomerName()} - {$project->getProjectName()}" ?>' ,  <?php  echo $project->getProjectId() ?>);
		  <?php
		  			$count++;
		  			
		  			} 
		  			
				}
				
		  	}else{
			?>
			   obj.options[-2] = new Option( '- <?php echo $lang_Time_Timesheet_NoProjects;?> - ' ,' -2 ');
			<?php		
			}
		  ?>
	}
}

YAHOO.OrangeHRM.container.init();
YAHOO.util.Event.addListener($("frmReport"), "submit", viewProjectReport);

</script>
<div id="status"></div>
<div class="formpage">
    <div class="outerbox" style="width:600px;">
        <div class="mainHeading"><h2><?php echo $lang_Time_ProjectReportTitle;?></h2></div>
    
    <?php if (isset($_GET['message'])) {    
            $message =  $_GET['message'];
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = 'lang_Time_Errors_' . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>  
    <?php } ?>
<form name="frmReport" id="frmReport" method="post" action="?timecode=Time&action=" onsubmit="viewProjectReport(); return false;">
<input type="hidden" name="token" value="<?php echo $token;?>" />
   <table border="0" cellpadding="5" cellspacing="0">
	<thead>
		<tr>
		<th></th>
	    	<th></th>
	    	<th></th>
	    	<th></th>
		<th></th>
                <th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Timesheet_Project; ?></td>
			<td ></td>
			<td >
				<select id="cmbProject" name="cmbProject" >
				<?php if (isset($arrayProjectAndCustomers) && is_array($arrayProjectAndCustomers)) {                        

                          for($a = 0;$a <count($arrayProjectAndCustomers); $a++) {
							$objProject = $arrayProjectAndCustomers[$a]['project'];
                          	
                          	if($objProject->getDeleted() == 0){                          		
                            	
								$selected = "";
								
                              	if (isset($projectId) && ($projectId == $objProject->getProjectId())) {
							  	    $selected = "selected";
							  	}
				?>
						<option value="<?php echo $objProject->getProjectId(); ?>" <?php echo $selected; ?> ><?php echo $arrayProjectAndCustomers[$a]['concat']; ?></option>
				<?php 		} 
						}
					} else { ?>
						<option value="-2">- <?php echo $lang_Time_Timesheet_NoProjects;?> -</option>
				<?php } ?>
				</select>
			</td>
                        <td></td>
		</tr>
                <tr>
                    <td></td>
                    <td><?php echo $lang_Time_Timesheet_Project_Deleted; ?></td>
                    <td></td>
                    <td><input type="checkbox" id="cbxDeleted" name="cbxDeleted" onClick="getProjectlist(this.checked)" ></td>
                    <td></td>
                    <td></td>
                </tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Common_FromDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtFromDate" name="txtFromDate" value="" size="10"/>
				<input type="button" id="btnFromDate" name="btnFromDate" value="  " class="calendarBtn" 
                    style="display:inline;margin:0;float:none;"/>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ><?php echo $lang_Time_Common_ToDate; ?></td>
			<td ></td>
			<td >
				<input type="text" id="txtToDate" name="txtToDate" value="" size="10"/>
				<input type="button" id="btnToDate" name="btnToDate" value="  " class="calendarBtn" 
                    style="display:inline;margin:0;float:none;"/>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td ></td>
			<td ></td>
			<td >
			</td>
			<td></td>
		</tr>
	</tbody>
	<tfoot>
	  	<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
  	</tfoot>
</table>
<div class="formbuttons">                
    <input type="submit" class="viewbutton" id="viewBtn" 
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
        value="<?php echo $lang_Common_View;?>" />                                  
</div>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');                
    }
//]]>
</script>
</div>
<div id="cal1Container" style="position:absolute;" ></div>
