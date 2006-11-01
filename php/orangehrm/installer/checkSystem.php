<?php 
function check_php_version($sys_php_version = '') {

	$sys_php_version = empty($sys_php_version) ? constant('PHP_VERSION') : $sys_php_version;
	// versions below $min_considered_php_version considered invalid by default,
	// versions equal to or above this ver will be considered depending
	// on the rules that follow 
	$min_considered_php_version = '4.1.0';

	// only the supported versions,
	// should be mutually exclusive with $invalid_php_versions
	$supported_php_versions = array (
		'4.3.10', '4.3.11',
		'4.4.1', '4.4.2',
		'5.0.1', '5.0.2', '5.0.3', '5.0.4',
		'5.1.0', '5.1.1', '5.1.2', '5.1.3',
		'5.1.4', '5.1.5', '5.1.6', '5.1.7'
	);
	
	sort($supported_php_versions);
	
	// invalid versions above the $min_considered_php_version,
	// should be mutually exclusive with $supported_php_versions
	$invalid_php_versions = array('5.0.0', '5.0.5');

	// default unsupported
	$retval = 0;

	// versions below $min_considered_php_version are invalid
	if(1 == version_compare($sys_php_version, $min_considered_php_version, '<')) {
		$retval = -1;
	}

	// supported version check overrides default unsupported
	foreach($supported_php_versions as $ver) {
		if(1 == version_compare($sys_php_version, $ver, 'eq')) {
			$retval = 1;
			break;
		}
	}
		
	if (($retval != 1) && (1 == version_compare($sys_php_version, $ver, '>'))) {
		$retval = 1;				
	}

	// invalid version check overrides default unsupported
	foreach($invalid_php_versions as $ver) {
		if(1 == version_compare($sys_php_version, $ver, 'eq')) {
			$retval = -1;
			break;
		}
	}

	return $retval;
}

function chk_memory($limit=9, $recommended=16) {

	$msg = '';
	$type = '';
	
	$max_memory = ini_get('memory_limit');
	
	if ($max_memory == "") {
	
		$msg = "OK (No Limit)";
		$type = "done";
		
	} else if ($max_memory === "-1") {
	
		$msg = "OK (Unlimited)";
		$type = "done";
		
	} else {
	
		$max_memory = rtrim($max_memory, "M");
		$max_memory_int = (int) $max_memory;
		
		if ($max_memory_int < $limit) {
			
			$msg = "Warning at least $limit M required ($max_memory M available, Recommended $recommended M)";
			$type = "error";
			
		} elseif ($max_memory_int < $recommended) {
		
				$msg = "OK (Recommended $recommended M)";
				$type = "pending";
		
		} else {			
				$msg = "OK";
				$type = "done";			
		}
		
	}
		
	$msg = "<b class='$type'>".$msg."</b>";	
		
return $msg;
}

?>

<script language="JavaScript">
function sysCheckPassed() {
	document.frmInstall.actionResponse.value  = 'SYSCHECKOK';
	document.frmInstall.submit();
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />


<div id="content">

  <h2>Step 2: System Check</h2>  
  
  <p>In order for your OrangeHRM installation to function properly,
  please ensure all of the system check items listed below are green. If
  any are red, please take the necessary steps to fix them.</p>

        <table cellpadding="0" cellspacing="0" border="0" class="table">
          <tr>
            <th align="left" class="th">Component</th>

            <th class="th" style="text-align: right;">Status</th>
          </tr>

		<tr>
            <td class="tdComponent">PHP version</td>

            <td align="right" class="tdValues"><strong>
            <?php

            	$error_found = false;

            	$php_version = constant('PHP_VERSION');
            	$check_php_version_result = check_php_version($php_version);
            	switch($check_php_version_result)
            	{
            		case -1:
	                  echo "<b><font color='red'>Invalid version ($php_version) Installed</font></b>";
   	               $error_found = true;
            			break;
            		case 0:
      	            echo "<b><font color='red'>Unsupported (ver $php_version)</font></b>";
            			break;
            		case 1:
      	            echo "<b><font color='green'>OK (ver $php_version)</font></b>";
            			break;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">MySQL Client</td>

            <td align="right" class="tdValues"><strong>
            <?php

            	$mysqlClient = mysql_get_client_info();

               if(function_exists('mysql_connect')) {
                  
                  if(intval(substr($mysqlClient,0,1)) < 4 || substr($mysqlClient,0,3) == '4.0') {
	                  echo "<b><font color='#C4C781'>ver 4.1.x or later recommended (reported ver " .$mysqlClient. ')</font></b>';
                  } else echo "<b><font color='green'>OK (ver " .$mysqlClient. ')</font></b>';
               } else {
                  echo "<b><font color='red'>Not Available</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">OrangeHRM Configuration File Writable</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable(ROOT_PATH . '/lib/confs')) {
                  echo "<b><font color='green'>OK</font></b>";
				} else {
                  echo "<b><font color='red'>Not Writeable</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
		  <tr>
            <td class="tdComponent">Maximum Session Idle Time before Timeout</td>

            <td align="right" class="tdValues"><strong>
            <?php
			   $gc_maxlifetime_min = floor(ini_get("session.gc_maxlifetime")/60);
			   $gc_maxlifetime_sec = ini_get("session.gc_maxlifetime") % 60;
               if ($gc_maxlifetime_min > 15) {
                  echo "<b><font color='green'>OK</font></b>";
				} else if ($gc_maxlifetime_min > 2){
					echo "<b><font color='#C4C781'>Short ($gc_maxlifetime_min minutes and $gc_maxlifetime_sec seconds)</font></b>";
				} else {
                  echo "<b><font color='red'>Too short ($gc_maxlifetime_min minutes and $gc_maxlifetime_sec seconds)</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
		  <tr>
            <td class="tdComponent">Memory allocated for PHP script</td>
            <td align="right" class="tdValues"><?php echo chk_memory(9, 16)?></td>
          </tr>		 
		</table>
		<br />
        <input class="button" type="button" value="Back" onclick="back();" tabindex="4">
		<input class="button" type="button" name="Re-check" value="Re-check" onclick="document.frmInstall.submit();" tabindex="3">
		<input class="button" type="button" value="Next" onclick="sysCheckPassed();" <?php echo  ($error_found) ? 'disabled' : '' ?> tabindex="2">
</div>