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

$uploadStatus = $this->popArr['uploadStatus'];
$recordLimit = $this->popArr['recordLimit'];
$delimiterLevels = $this->popArr['delimiterLevels'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_DataImportStatus_ContinuingDataImport; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css");
</style>
<style type="text/css">
<!--
	.roundbox {
		margin-top: 10px;
		margin-left: 0px;
		width: 700px;
	}

	.roundbox_content {
		padding:15px;
	}

	.statusLabel {
		width: 200px;
		text-align: left;
		float: left;
		font-weight: bold;
	}

	.statusData {
		width: 150px;
		text-align: left;
		font-weight: normal;
		float: left;
	}

	#progressBar {
		background-color: #FFCC00;
		display: block;
		height: 10px;
	}
-->
</style>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script language="javascript">

	<?php
		$tempFiles = $uploadStatus->getTempFileList();
		$importType = $uploadStatus->getImportType();
	?>

	var requestLinkPrefix = "./CentralController.php?uniqcode=IMPAJAX&importType=<?php echo $importType; ?>&file=";
	var requestFiles = new Array();
	var fileIndex = 0;

	var totalNoOfRecords = <?php echo $uploadStatus->getNoOfRecords(); ?>;
	var noOfRecordsProcessed = 0;
	var noOfRecordsImported	 = 0;
	var noOfRecordsFailed	 = 0;
	var noOfRecordsSkipped	 = 0;
	var startTime = null;
	var rowStyleEven = false;
	var delimiterLevels = new Array('<?php echo implode("', '", $delimiterLevels); ?>');

	<?php
		$i = 0;

		foreach($tempFiles as $file) {
	?>
	requestFiles[<?php echo $i; ?>] = "<?php echo base64_encode($file); ?>";
	<?php
			$i++;
		}
	?>

	msg_INCORRECT_COLUMN_NUMBER 		= '<?php echo $lang_DataImportStatus_Error_INCORRECT_COLUMN_NUMBER; ?>';
	msg_MISSING_WORKSTATION 		= '<?php echo $lang_DataImportStatus_Error_MISSING_WORKSTATION; ?>';
	msg_COMPULSARY_FIELDS_MISSING_DATA 	= '<?php echo $lang_DataImportStatus_Error_COMPULSARY_FIELDS_MISSING_DATA; ?>';
	msg_DD_DATA_INCOMPLETE 			= '<?php echo $lang_DataImportStatus_Error_DD_DATA_INCOMPLETE; ?>';
	msg_INVALID_TYPE 			= '<?php echo $lang_DataImportStatus_Error_INVALID_TYPE; ?>';
	msg_DUPLICATE_EMPLOYEE_ID 		= '<?php echo $lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_ID; ?>';
	msg_DUPLICATE_EMPLOYEE_NAME 		= '<?php echo $lang_DataImportStatus_Error_DUPLICATE_EMPLOYEE_NAME; ?>';
	msg_FIELD_TOO_LONG 			= '<?php echo $lang_DataImportStatus_Error_FIELD_TOO_LONG; ?>';

/*
*/
	function $($id) {
		return document.getElementById($id);
	}

	function initImport(index) {

	    	xmlHTTPObject = null;

		try {
  			xmlHTTPObject = new XMLHttpRequest();
		} catch (e) {
			try {
			    xmlHTTPObject = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				xmlHTTPObject = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		if (xmlHTTPObject == null)
			alert("Your browser does not support AJAX!");

	        xmlHTTPObject.onreadystatechange = function() {

        	if (xmlHTTPObject.readyState == 4){
        		response = xmlHTTPObject.responseText;
        		results = response.split(delimiterLevels[0]);

        		results[0] = parseInt(results[0]);
        		results[1] = parseInt(results[1]);
        		results[2] = parseInt(results[2]);

        		completedRecords = results[0] + results[1];
        		noOfRecordsProcessed += completedRecords;
        		noOfRecordsImported += results[0];
				noOfRecordsFailed += results[1];

			if (results[3] && results[3] != '') {
				errors = results[3].split(delimiterLevels[1]);
				for (i in errors) {
					error = errors[i].split(delimiterLevels[2]);
					error[0] = parseInt(error[0]) + (fileIndex * <?php echo $recordLimit; ?>);
					error[1] = eval('msg_' + error[1]);
					displayErrors(error);
				}
			}

			if (totalNoOfRecords > 0) {
	        	progressPercentage = Math.ceil((noOfRecordsProcessed / totalNoOfRecords) * 100);
			} else {
				progressPercentage = 0;
			}

        		changeProgressBar(progressPercentage);

        		$('divNoOfRecordsImported').innerHTML = noOfRecordsImported + '/' + totalNoOfRecords;
        		$('divNoOfRecordsFailed').innerHTML = noOfRecordsFailed;

        		if (startTime == null) {
        			startTime = new Date();
        		} else {
        			timeElasped = new Date() - startTime;
        			timeRemaining = ((timeElasped / progressPercentage) * (100 - progressPercentage)) / 1000;

					if (timeRemaining != 0) {
	        			$('divETA').innerHTML = Math.ceil(timeRemaining) + ' <?php echo $lang_DataImportStatus_TimeRemainingSeconds; ?>';
					} else {
	        			$('divETA').innerHTML = '<?php echo $lang_DataImportStatus_ImportCompleted; ?>';
					}
        		}

        		if (fileIndex < requestFiles.length - 1) {
        			fileIndex++;
        			initImport(fileIndex);
        		} else {
				showFinalResults();
			}

			}
		}

		xmlHTTPObject.open('GET', requestLinkPrefix + requestFiles[index], true);
		xmlHTTPObject.send(null);

	}

	function displayErrors(error) {

		$('failureDetails').style.display = 'block';

		tbody = $('importStatusResults');

		var tableRow = document.createElement('tr');
		tableData = new Array();

		for (i in error) {
			tableData[i] = document.createElement('td');
			tableData[i].className = (rowStyleEven) ? 'even' : 'odd';
			tableData[i].innerHTML = error[i];
			tableRow.appendChild(tableData[i]);
		}

		tbody.appendChild(tableRow);

		rowStyleEven = !rowStyleEven;

	}

	function showFinalResults() {

		if (noOfRecordsFailed == 0) {
			style = "success";

			if (noOfRecordsImported == 0) {
				// No failures, nothing to import
				finalResult = '<?php echo $lang_DataImportStatus_NothingImported; ?>';
			} else {
				// import success
				finalResult = '<?php echo $lang_DataImportStatus_ImportSuccess; ?>';
			}
		} else {
			style = "error";

			if (noOfRecordsImported == 0) {
				// all failures
				finalResult = '<?php echo $lang_DataImportStatus_ImportFailed; ?>';
			} else {
				// some successes, some failures
				finalResult = '<?php echo $lang_DataImportStatus_ImportSomeFailed; ?>';
			}
		}

		$('divFinalResult').className = style;
		$('divFinalResult').innerHTML = finalResult;
	       	$('divETA').innerHTML = '<?php echo $lang_DataImportStatus_ImportCompleted; ?>';
	}

	function changeProgressBar(pecentage) {
		$('progressBar').style.width = pecentage + '%';
		$('spanProgressPercentage').innerHTML = pecentage + '%';
	}

</script>
</head>
<body>
<h2><?php echo $lang_DataImportStatus_ContinuingDataImport; ?><hr/></h2>
<h3><?php echo $lang_DataImportStatus_Summary; ?></h3>
<div class="roundbox" style="width: 500px;">
	<div class="statusLabel"><?php echo $lang_DataImportStatus_ETA; ?></div>
	<div id="divETA" class="statusValue"><?php echo 'Estimating...'; ?></div>
	<br />
	<div class="statusLabel"><?php echo $lang_DataImportStatus_Progress; ?></div>
	<div id="divProgressBarContainer" class="statusValue">
		<span style="width:200px; display: block; float: left; height: 10px; border: solid 1px #000000;">
			<span id="progressBar" style="width: 0%;">&nbsp;</span>
		</span>
		&nbsp;
		<span id="spanProgressPercentage">0%</span>
	</div>
	<br />
	<div class="statusLabel"><?php echo $lang_DataImportStatus_NumImported; ?></div>
	<div id="divNoOfRecordsImported" class="statusValue">-</div>
	<br/>
	<div class="statusLabel"><?php echo $lang_DataImportStatus_NumFailed; ?></div>
	<div id="divNoOfRecordsFailed" class="statusValue">-</div>
	<br/>
	<div class="statusLabel"><?php echo $lang_DataImportStatus_FinalResult; ?></div>
	<div id="divFinalResult" class="statusValue"><?php echo $lang_DataImportStatus_ImportInProgress; ?></div>
</div>

<div id="failureDetails" style="display: none">
	<h3><?php echo $lang_DataImportStatus_Details; ?></h3>
		<div class="roundbox">
		<table cellspacing="0" cellpadding="4" style="border: none; with: 700px;">
			<thead>
				<tr>
				<th width="50px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Row; ?></th>
				<th width="275px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Error; ?></th>
				<th width="350px" class="tableMiddleMiddle"><?php echo $lang_DataImportStatus_Heading_Comments; ?></th>
				</tr>
			</thead>
			<tbody id="importStatusResults">
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
<!--
	if (document.getElementById && document.createElement) {
		initOctopus();
	}

	initImport(fileIndex);
-->
</script>
</body>
</html>
