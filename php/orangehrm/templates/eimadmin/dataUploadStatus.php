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

$xajaxObj = new xajax();

$reqResults = $xajaxObj->registerExternalFunction('callAjax', 'CentralController.php', XAJAX_GET);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo /*$lang_DataUploadStatus_Title*/'Upload Successful. Continuing with Data Import'; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/<?php echo $styleSheet;?>/css/leave.css" rel="stylesheet" type="text/css" />
<style type="text/css">
@import url("../../themes/<?php echo $styleSheet;?>/css/style.css");
</style>
<style type="text/css">
<!--
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
	var startTime = null;
	
	<?php
		$i = 0;

		foreach($tempFiles as $file) {
	?>
	requestFiles[<?php echo $i; ?>] = "<?php echo base64_encode($file); ?>";
	<?
			$i++;
		}
	?>

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
        		results = response.split(',');
        		
        		results[0] = parseInt(results[0]);
        		results[1] = parseInt(results[1]);
        		
        		completedRecords = results[0] + results[1]; 
        		noOfRecordsProcessed += completedRecords;
        		noOfRecordsImported	 += results[0];
				noOfRecordsFailed	 += results[1];
        		
        		progressPercentage = Math.ceil((noOfRecordsProcessed / totalNoOfRecords) * 100);
        		
        		changeProgressBar(progressPercentage);
        		
        		$('noOfRecordsImported').innerHTML = noOfRecordsImported + "/" + totalNoOfRecords;
        		$('noOfRecordsFailed').innerHTML = noOfRecordsFailed;
        	
        		if (startTime == null) {
        			startTime = new Date();
        		} else {
        			timeElasped = new Date() - startTime;
        			
        			//secondsElapsed = timeElasped.getSeconds();
        			timeRemaining =  (1 - (progressPercentage / 100)) * timeElasped;
        			$('ETA').innerHTML = timeRemaining.toFixed(2); 
        		}
        		
        		$('resultPane').innerHTML = '';
        		
        		if (fileIndex < requestFiles.length - 1) {
        			fileIndex++;
        			initImport(fileIndex);
        		}
        	} else {
        		
        	}
		}

		xmlHTTPObject.open('GET', requestLinkPrefix + requestFiles[index], true);
        xmlHTTPObject.send(null);

	}
	
	function changeProgressBar(pecentage) {
		$('progressBar').style.width = pecentage + '%';
		$('progressPercentage').innerHTML = pecentage + '%';
	}

</script>
</head>
<body>
<h2><?php echo /*$lang_DataImportStatus_Title*/'Upload Successful. Continuing with Data Import'; ?><hr/></h2>
<?php
/*
	if ($importStatus->getNumFailed() == 0) {
		$style = "success";
		if ($importStatus->getNumImported() == 0) {
			// No failures, nothing to import
			$message = $lang_DataImportStatus_NothingImported;
		} else {
			// import success
			$message = $lang_DataImportStatus_ImportSuccess;
		}
	} else {
		$style = "error";
		if ($importStatus->getNumImported() == 0) {
			// all failures
			$message = $lang_DataImportStatus_ImportFailed;
		} else {
			// some successes, some failures
			$message = $lang_DataImportStatus_ImportSomeFailed;
		}
	}*/
	
?>
<div class="message">
	<font class="<?php echo /*$style*/'';?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
		<?php echo /*$message*/''; ?>
	</font>
</div>

<div>
	<div class="statusLabel">No. of Records</div>
	<div id="noOfRecords" class="statusValue"><?php echo $uploadStatus->getNoOfRecords(); ?></div>
	<br />
	<div class="statusLabel">ETA</div>
	<div id="ETA" class="statusValue"><?php echo 'Estimating...'; ?></div>
	<br />
	<div class="statusLabel">Progress</div>
	<div id="progressBarContainer" class="statusValue">
		<span style="width:200px; display: block; float: left; height: 10px; border: solid 1px #000000;">
			<span id="progressBar" style="width: 0%;">&nbsp;</span>
		</span>
		&nbsp;
		<span id="progressPercentage">0%</span>
	</div>
	<br />
	<div class="statusLabel">No. of Records Imported</div>
	<div id="noOfRecordsImported" class="statusValue">-</div>
	<br/>
	<div class="statusLabel">No. of Records Failed Import</div>
	<div id="noOfRecordsFailed" class="statusValue">-</div>
</div>
<div id="resultPane">
</div>

<!-- Import status summary -->
<h3><?php echo $lang_DataImportStatus_Summary; ?></h3>

<script type="text/javascript">
<!--
	initImport(fileIndex);
-->
</script>
</body>
</html>
