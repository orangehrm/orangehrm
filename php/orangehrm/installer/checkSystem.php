<?php 
function check_php_version($sys_php_version = '') {

	$sys_php_version = empty($sys_php_version) ? constant('PHP_VERSION') : $sys_php_version;
	// versions below $min_considered_php_version considered invalid by default,
	// versions equal to or above this ver will be considered depending
	// on the rules that follow 
	$min_considered_php_version = '4.4.1';

	// only the supported versions,
	// should be mutually exclusive with $invalid_php_versions
	$supported_php_versions = array (
		'4.3.10', '4.3.11',
		'4.4.1', '4.4.2',
		'5.0.1', '5.0.2', '5.0.3', '5.0.4',
		'5.1.0', '5.1.1', '5.1.2'
	);

	// invalid versions above the $min_considered_php_version,
	// should be mutually exclusive with $supported_php_versions
	$invalid_php_versions = array('4.4.0', '5.0.0', '5.0.5');

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

	// invalid version check overrides default unsupported
	foreach($invalid_php_versions as $ver) {
		if(1 == version_compare($sys_php_version, $ver, 'eq')) {
			$retval = -1;
			break;
		}
	}

	return $retval;
}

?>

<script language="JavaScript">
function sysCheckPassed() {
	document.frmInstall.actionResponse.value  = 'SYSCHECKOK';
	document.frmInstall.submit();
}
</script>

  <table cellspacing="0" align="center">
    <tr>
      <th width="400">Step 2: System Check</th>
    </tr>

    <tr>
      <td colspan="2" width="600">
        <p>In order for your OrangeHRM installation to function properly,
        please ensure all of the system check items listed below are green. If
        any are red, please take the necessary steps to fix them.</p>

        <table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr>
            <th align="left">Component</th>

            <th style="text-align: right;">Status</th>
          </tr>

		<tr>
            <td><b>PHP version</b></td>

            <td align="right"><?php

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
            ?></td>
          </tr>
          <tr>
            <td><strong>MySQL Client</strong></td>

            <td align="right"><?php

            	$mysqlClient = mysql_get_client_info();

               if(function_exists('mysql_connect')) {
                  
                  if(intval(substr($mysqlClient,0,1)) < 4 || substr($mysqlClient,0,3) == '4.0') {
	                  echo "<b><font color='#C4C781'>ver 4.1.x or later recommended (reported ver " .$mysqlClient. ')</font></b>';
                  } else echo "<b><font color='green'>OK (ver " .$mysqlClient. ')</font></b>';
               } else {
                  echo "<b><font color='red'>Not Available</font></b>";
                  $error_found = true;
               }
            ?></td>
          </tr>
          <tr>
            <td><b>OrangeHRM Configuration File Writable</b></td>

            <td align="right"><?php
               if(is_writable(ROOT_PATH . '/lib/confs')) {
                  echo "<b><font color='green'>OK</font></b>";
				} else {
                  echo "<b><font color='red'>Not Writeable</font></b>";
                  $error_found = true;
               }
            ?></td>
          </tr>
		</table>
        <table cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td><input class="button" type="button" name="Re-check" value="Re-check" onclick="document.frmInstall.submit();"></td>
            <td><input class="button" type="button" value="Next" onclick="sysCheckPassed();" <?= ($error_found) ? 'disabled' : '' ?>></td>
          </tr>
        </table>
