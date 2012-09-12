<?php use_javascript('jquery.js') ?>
<?php use_javascript('executeSystemCheckSuccess.js') ?>
<?php use_stylesheet('style.css') ?>
<script language="JavaScript">
function sysCheckPassed() {
    document.frmInstall.actionResponse.value  = 'SYSCHECKOK';
    document.frmInstall.submit();
}
</script>


<div id="content" style="width:1000px">

  <h2>System Check</h2>

  <p>For OrangeHRM to function properly, please ensure that all the items listed below are green. <br />
      If any of them are red, please take the necessary steps to fix them.</p>

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
                $phpVersion = PHP_VERSION;
               
               if (version_compare(PHP_VERSION, '5.3.0') < 0) {
                   $error_found = true;
                   echo "<b><font color='red'>PHP 5.3.0 or higher is required. Installed version is $phpVersion</font></b>";
               } else {
                   echo "<b><font color='green'>OK (ver $phpVersion)</font></b>";
               }               
              
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">MySQL Client</td>

            <td align="right" class="tdValues"><strong>
            <?php

                if(function_exists('mysql_connect')) {
                    $mysqlClient = mysql_get_client_info();

                    // Regular expression that will detects any 
                    // <digit+>.<digit+>.<digit+> pattern in any string. 
                    // also assumes that only one occurrence would be 
                    // found on the $mysqlClient version string.
                    $versionPattern = '/[0-9]+\.[0-9]+\.[0-9]+/';

                    preg_match($versionPattern, $mysqlClient, $matches);
                    $mysql_client_version = $matches[0];

                    if (version_compare($mysql_client_version, '4.1.0') < 0) {
                        echo "<b><font color='#9E6D6D'>ver 4.1.x or later recommended (reported ver " . $mysqlClient . ')</font></b>';
                    } else
                        echo "<b><font color='green'>OK (ver " . $mysqlClient . ')</font></b>';
                } else {
                    echo "<b><font color='red'>MySQL support not available in PHP settings</font></b>";
                    $error_found = true;
                }
                    
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">MySQL Server</td>

            <td align="right" class="tdValues"><strong>
            <?php
               $dbInfo = $sf_user->getAttribute('dbInfo');
               if(function_exists('mysql_connect') && (@mysql_connect($dbInfo['host'].':'.$dbInfo['port'], $dbInfo['username'], $dbInfo['password']))) {

                  $mysqlServer = mysql_get_server_info(); 

                  if(version_compare($mysqlServer, "5.1.6") >= 0) {
                     echo "<b><font color='green'>OK (ver " .$mysqlServer. ')</font></b>';
                  } else {
                    echo "<b><font color='#9E6D6D'>ver 5.1.6 or later recommended (reported ver " .$mysqlServer. ')</font></b>';
                  }
               } else {
                  echo "<b><font color='red'>Not Available</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">MySQL InnoDB Support</td>

            <td align="right" class="tdValues"><strong>
            <?php
            if(function_exists('mysql_connect') && (@mysql_connect($dbInfo['host'].':'.$dbInfo['port'], $dbInfo['username'], $dbInfo['password']))) {

                    $mysqlServer = mysql_query("show engines");

                    while ($engines = mysql_fetch_assoc($mysqlServer)) {
                        if ($engines['Engine'] == 'InnoDB') {
                            if ($engines['Support'] == 'DISABLED') {
                                echo "<b><font color='red'>Disabled!</font></b>";
                                $error_found = true;
                            } elseif ($engines['Support'] == 'DEFAULT') {
                                echo "<b><font color='green'>Default</font></b>";
                            } elseif ($engines['Support'] == 'YES') {
                                echo "<b><font color='green'>Enabled</font></b>";
                            } elseif ($engines['Support'] == 'NO') {
                                echo "<b><font color='red'>Not available!</font></b>";
                                $error_found = true;
                            } else {
                                echo "<b><font color='red'>Unknown Error!</font></b>";
                                $error_found = true;
                            }
                        }
                    }

               } else {
                  echo "<b><font color='red'>Cannot connect to the database</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "lib/confs"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/lib/confs')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "lib/logs"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/lib/logs')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "symfony/config"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/symfony/config')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "symfony/apps/orangehrm/config"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/symfony/apps/orangehrm/config')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "symfony/cache"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/symfony/cache')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Write Permissions for "symfony/log"</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(is_writable($root_path . '/symfony/log')) {
                  echo "<b><font color='green'>OK</font></b>";
                } else {
                  echo "<b><font color='red'>Not Writeable</font>";
                  echo "<b><font color='red'><sup>*</sup></font></b>";
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
                    echo "<b><font color='#9E6D6D'>Short ($gc_maxlifetime_min minutes and $gc_maxlifetime_sec seconds)</font></b>";
                } else {
                  echo "<b><font color='red'>Too short ($gc_maxlifetime_min minutes and $gc_maxlifetime_sec seconds)</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Register Globals turned-off</td>

            <td align="right" class="tdValues"><strong>
            <?php
               $registerGlobalsValue = (bool) ini_get("register_globals");
               if ($registerGlobalsValue) {
                  echo "<font color='red'>On <sup>#</sup></font>";
                  $error_found = true;
                } else {
                  echo "<font color='green'>OK</font>";
               }
            ?>
            </strong></td>
          </tr>
          <tr>
            <td class="tdComponent">Memory allocated for PHP script</td>
            <td align="right" class="tdValues"><?php echo htmlspecialchars_decode($systemCheckUtility->checkMemory(), ENT_QUOTES)?></td>
          </tr>

          <tr>
            <td class="tdComponent">Web server allows .htaccess files</td>
            <td align="right" class="tdValues"><strong><b id="htaccess" class="pending">Checking...</b></strong></td>
          </tr>
          
          <tr>
            <td class="tdComponent">MySQL Event Scheduler status</td>

            <td align="right" class="tdValues"><strong>
            <?php
               if(function_exists('mysql_connect') && (@mysql_connect($dbInfo['host'].':'.$dbInfo['port'], $dbInfo['username'], $dbInfo['password']))) {

		            $result = mysql_query("SHOW VARIABLES LIKE 'EVENT_SCHEDULER'");
                    $row = mysql_fetch_assoc($result);
                    $schedulerStatus = $row['Value'];
                    
                    if ($schedulerStatus == 'ON') {
                        echo "<b><font color='green'>Enabled</font></b>";
                    } else {
                        echo "<b><font color='#9E6D6D'>Disabled. This is required for automatic leave status changes of Leave module.</font></b>";
                    }

               } else {
                  echo "<b><font color='red'>Cannot connect to the database</font></b>";
                  $error_found = true;
               }
            ?>
            </strong></td>
          </tr>          

          <?php
            $printMoreInfoLink = false;

            if(!(is_writable($root_path . '/lib/confs'))){

                echo "<tr> <td colspan='2'> ";
                echo "<font color='red'>* Web server requires write privilege to the following directory</font> ";
                echo "</td> </tr>";
                $printMoreInfoLink = true;
            }

             if ($registerGlobalsValue) {
                echo "<tr> <td colspan='2'> ";
                echo "<font color='red'><sup>#</sup> The value of <strong>register_globals</strong> should be <strong>Off</strong> in php.ini file</font> ";
                echo "</td> </tr>";
                $printMoreInfoLink = true;
             }

             if ($printMoreInfoLink) {
                print ' <a href="./guide/#systemChk" id="help" target="_blank">[ For More Information ?]</a>';
             }
          ?>
        </table>
        <br />
        <form action="<?php echo url_for('upgrade/executeSystemCheck');?>" name="systemCheckForm" method="post">
            <?php echo $form->renderHiddenFields();?>
            <input class="button" id="recheckButton" type="button" name="Re-check" value="Re-check" tabindex="3">
            <input class="button" id="nextButton" type="submit" value="Proceed" <?php echo  ($error_found) ? 'disabled' : '' ?> tabindex="2">
        </form>
</div>

<br />

<script type="text/javascript">
//<![CDATA[
    /**
     * Tries to acesss installer/images/dummy.jpg which should be blocked
     * by the .htaccess file in install/images directory.
     *
     * if file was loaded successfully, .htaccess files are not enabled in apache
     * else htaccess files are enabled.
     */
    function checkHTAccessFiles() {
        var testImage = new Image();
        testImage.src = "<?php echo sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/images/dummy.jpg';?>";
        
        testImage.onload = htAcessDisabled;
        testImage.onerror = htAccessEnabled;
        
    }

    function htAccessEnabled() {
        var element = document.getElementById("htaccess");
        element.innerHTML = "OK";
        element.className = "done";
    }

    function htAcessDisabled() {
        var element = document.getElementById("htaccess");
        element.innerHTML = "Not enabled! This makes OrangeHRM vulnerable to security attacks.";
        element.className = "error";
    }


    checkHTAccessFiles();

//]]>
</script>