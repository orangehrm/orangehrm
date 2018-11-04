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


/**
 *Related class to web installer.
 *This class will validate server configurations. 
 */
class BasicConfigurations{

private $interuptContinue;
function __construct()
{
  require_once(ROOT_PATH . '/installer/environmentCheck/SystemValidator.php');
  $this->interuptContinue = false;
  $this->systemValidator = new SystemValidator();
}

function getMessages(){
    if (!isset($messageList)) {
        $messageList = new Messages();
    }
    return $messageList;
}

/**
 * This class will validate server configurations. 
 * If any thing not suitable return true from variable '$interuptContinue'
 * '$interuptContinue' is variable. Its true means error found and interupt continue to next level.
 * If it is false then not interupt and continue installation.
 * If error found set '$interuptContinue' as "TRUE".
 */
public function isFailBasicConfigurations(){


	//01 important to check. If false interupt
	$this->IsPHPVersionCompatible();
	//02 important to check. If false interupt
	$this->IsMySqlClientCompatible();
	//03 important to check. If false interupt
	$this->IsMySqlServerCompatible();
	//04 important to check. If false interupt
	$this->IsInnoDBSupport();
	//05 important to check. If false interupt
	$this->IsWritableLibConfs();

	//06-function
	$this->IsWritableSymfonyConfig();
	//07-function
	$this->IsWritableSymfonyCache();
	//08-function
	$this->IsWritableSymfonyLog();
	//09-function
	$this->IsMaximumSessionIdle();
	//10-function
	$this->IsRegisterGlobalsOff();
    
	//11 Display messages with case statement filter.
	$this->checkMemory();

	//12-function
	$this->IsGgExtensionEnable();
	
	if($this->isApacheServer()) {
	    //17- function
	    $this->IsApacheExpiresModule();
	    //18- function
	    $this->IsApacheHeadersModule();
	    //19 - function
	    $this->IsEnableRewriteMod();
	}

	//20 - function
//	$this->MySQLEventStatus(); // removed my sql status check because it is not needed for OS

	//21 -function
	$this->isCurlEnabled();

	//22 -function
	$this->isSimpleXMLEnabled();

	$this->getMessages()->displayMessage(Messages::SEPERATOR);
	$this->dbConfigurationCheck();
	$this->getMessages()->displayMessage(Messages::SEPERATOR);

	return $this->interuptContinue;
}

//01-function
function IsPHPVersionCompatible() {
               if (version_compare(PHP_VERSION, Messages::PHP_MIN_VERSION) < 0) {
                   $this->interuptContinue = true;
		   $this->getMessages()->displayMessage(Messages::PHP_FAIL_MESSAGE." Installed version is ".PHP_VERSION);
               } else {
		   $this->getMessages()->displayMessage(Messages::PHP_OK_MESSAGE." (ver ".PHP_VERSION.")");
	       }            
}

//02-function
function IsMySqlClientCompatible() {

                if(function_exists('mysqli_get_client_info')) {
            $mysqlClient = mysqli_get_client_info();
                    $versionPattern = '/[0-9]+\.[0-9]+\.[0-9]+/';

                    preg_match($versionPattern, $mysqlClient, $matches);
                    $mysql_client_version = $matches[0];

                    if (version_compare($mysql_client_version, Messages::MYSQL_MIN_VERSION) < 0) {
		       $this->getMessages()->displayMessage(Messages::MYSQL_CLIENT_RECOMMEND_MESSAGE."(reported ver ".$mysqlClient.")");
                    } else
		       $this->getMessages()->displayMessage(Messages::MYSQL_CLIENT_OK_MESSAGE);
                } else {
		    $this->getMessages()->displayMessage(Messages::MYSQL_CLIENT_FAIL_MESSAGE);
                    $this->interuptContinue = true; 		    
                }
                    
       }
//03-function
function IsMySqlServerCompatible() {
        $conn = $this->getConnection($_SESSION['dbInfo']);
        if ($conn) {
            $mysqlServer = mysqli_get_server_info($conn);

            if (version_compare($mysqlServer, "5.1.6") >= 0) {
                $this->getMessages()->displayMessage(Messages::MYSQL_SERVER_OK_MESSAGE . " ($mysqlServer)");
            } else {
                $this->getMessages()->displayMessage(Messages::MYSQL_SERVER_RECOMMEND_MESSAGE . " (reported ver " . $mysqlServer . ")");
            }
        } else {
            $this->getMessages()->displayMessage(Messages::MYSQL_SERVER_FAIL_MESSAGE);
            $this->interuptContinue = true;
        }

}

//04-function
function IsInnoDBSupport() {
               $conn = $this->getConnection($_SESSION['dbInfo']);

               if ($conn) {
		            $mysqlServer = mysqli_query($conn, "show engines");
			   

		            while ($engines = mysqli_fetch_assoc($mysqlServer)) {
		                if ($engines['Engine'] == 'InnoDB') {
		                    if ($engines['Support'] == 'DISABLED') {
		                        $this->getMessages()->displayMessage("MySQL InnoDB Support - Disabled!");
					$this->interuptContinue = true;
		                    } elseif ($engines['Support'] == 'DEFAULT') {
		                        $this->getMessages()->displayMessage("MySQL InnoDB Support - Default");
		                    } elseif ($engines['Support'] == 'YES') {
		                        $this->getMessages()->displayMessage("MySQL InnoDB Support - Enabled");
		                    } elseif ($engines['Support'] == 'NO') {
		                        $this->getMessages()->displayMessage("MySQL InnoDB Support - available!");
					$this->interuptContinue = true;
		                    } else {
		                        $this->getMessages()->displayMessage("MySQL InnoDB Support - Unknown Error!");
					$this->interuptContinue = true;
		                    }
		                }
		            }

               } else {
                 $this->getMessages()->displayMessage("MySQL InnoDB Support - Cannot connect to the database");
                 $this->interuptContinue = true;
               }
            }
//05-function
function IsWritableLibConfs() {
               if(is_writable(ROOT_PATH . '/lib/confs')) {
		   $this->getMessages()->displayMessage(Messages::WritableLibConfs_OK_MESSAGE);
				} else {
		  $this->getMessages()->displayMessage(Messages::WritableLibConfs_FAIL_MESSAGE);
                  $this->interuptContinue = true;
               }
           }
//06-function
function IsWritableSymfonyConfig(){
               if(is_writable(ROOT_PATH . '/symfony/config')) {
                  $this->getMessages()->displayMessage(Messages::WritableSymfonyConfig_OK_MESSAGE);
				} else {
                  $this->getMessages()->displayMessage(Messages::WritableSymfonyConfig_FAIL_MESSAGE);
                  $this->interuptContinue = true;
               }	      
         }
//07-function
function IsWritableSymfonyCache(){
               if(is_writable(ROOT_PATH . '/symfony/cache')) {
		  $this->getMessages()->displayMessage(Messages::WritableSymfonyCache_OK_MESSAGE);
				} else {
		  $this->getMessages()->displayMessage(Messages::WritableSymfonyCache_FAIL_MESSAGE);
                  $this->interuptContinue = true;
               }
           }
//08-function
 function IsWritableSymfonyLog(){
               if(is_writable(ROOT_PATH . '/symfony/log')) {
		  $this->getMessages()->displayMessage(Messages::WritableSymfonyLog_OK_MESSAGE);
		  }
	       else{
                  $this->getMessages()->displayMessage(Messages::WritableSymfonyLog_FAIL_MESSAGE);
                  $this->interuptContinue = true;
               }
            }

//09-function
 function IsMaximumSessionIdle(){
	       $gc_maxlifetime_min = floor(ini_get("session.gc_maxlifetime")/60);
	       $gc_maxlifetime_sec = ini_get(" session.gc_maxlifetime") % 60;
	       $time_span = "($gc_maxlifetime_min minutes and $gc_maxlifetime_sec seconds)";
               if ($gc_maxlifetime_min > 15) {
                  $this->getMessages()->displayMessage(Messages::MaximumSessionIdle_OK_MESSAGE.$time_span);
	       } else if ($gc_maxlifetime_min > 2){		  
		  $this->getMessages()->displayMessage(Messages::MaximumSessionIdle_SHORT_MESSAGE.$time_span);
	       } else {
                  $this->getMessages()->displayMessage(Messages::MaximumSessionIdle_TOO_SHORT_MESSAGE.$time_span);
                  $this->interuptContinue = true;
               }
            }

//10-function
 function IsRegisterGlobalsOff(){
	       echo "Register Globals turned-off -";
	       $registerGlobalsValue = (bool) ini_get("register_globals");
               if ($registerGlobalsValue) {
                  $this->getMessages()->displayMessage(Messages::RegisterGlobalsOff_FAIL_MESSAGE);
		  $this->interuptContinue = true;
	       } else {
		  $this->getMessages()->displayMessage(Messages::RegisterGlobalsOff_OK_MESSAGE);
               }
            }

//11 - function
function checkMemory() {
    $limit = 9;
    $recommended = 16;
    $maxMemory = null;

    $status = '';

    $result = checkPHPMemory($limit, $recommended, $maxMemory);

	switch ($result) {
		case INSTALLUTIL_MEMORY_NO_LIMIT:
			$status = "OK (No Limit)";
			break;

		case INSTALLUTIL_MEMORY_UNLIMITED:
			$status = "OK (Unlimited)";
			break;

		case INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL:
			$status = "Warning at least ${limit}M required (${maxMemory} available, Recommended ${recommended}M)";
			$this->interuptContinue = true;
			break;

		case INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL:
			$status= "OK (Recommended ${recommended}M)";
			break;

		case INSTALLUTIL_MEMORY_OK:
			$status = "OK";
			break;
	}

	$this->getMessages()->displayMessage("Memory allocated for PHP script - ".$status);
}

//12-function
 function IsGgExtensionEnable(){ 
            if (extension_loaded('gd') && function_exists('gd_info')) { 
		   $this->getMessages()->displayMessage(Messages::GgExtensionEnable_OK_MESSAGE);
           } else  { 
		   $this->getMessages()->displayMessage(Messages::GgExtensionEnable_FAIL_MESSAGE);
                   $this->interuptContinue = true;		  
           } 
}   
//13- function
 function IsPHPExifEnable(){
            if (function_exists('exif_read_data')) {           
                    $this->getMessages()->displayMessage(Messages::PHPExifEnable_OK_MESSAGE);
            } else  {               
		    $this->getMessages()->displayMessage(Messages::PHPExifEnable_FAIL_MESSAGE);
		    $this->interuptContinue = true;
            }
}

//14- function
  function IsPHPAPCEnable(){ 
            if (extension_loaded('apc') && ini_get('apc.enabled')) {            
                   $this->getMessages()->displayMessage(Messages::PHPAPCEnable_OK_MESSAGE);
             } else  { 
           	   $this->getMessages()->displayMessage(Messages::PHPAPCEnable_FAIL_MESSAGE);
           }  
} 

//16- function - execute in IsApacheExpiresModule().
  function getAppacheModules(){ 
          if (function_exists('apache_get_modules')) {
              $apacheModules = apache_get_modules(); 
		return $apacheModules; 
          }

  }


//17- function
 function IsApacheExpiresModule(){
     $apacheModules = $this->getAppacheModules();

     if (empty($apacheModules)) {
         $this->getMessages()->displayMessage(Messages::ApacheExpiresModule_UNABLE_MESSAGE);
     } else if (in_array('mod_expires', $apacheModules)) {
         $this->getMessages()->displayMessage(Messages::ApacheExpiresModule_OK_MESSAGE);
     } else  {
         $this->getMessages()->displayMessage(Messages::ApacheExpiresModule_DISABLE_MESSAGE);
     }


}

//18- function
 function IsApacheHeadersModule(){
     if (empty($apacheModules)) {
         $this->getMessages()->displayMessage(Messages::ApacheHeadersModule_UNABLE_MESSAGE);
     } else if (in_array('mod_headers', $apacheModules)) {
         $this->getMessages()->displayMessage(Messages::ApacheHeadersModule_ENABLE_MESSAGE);
     } else{
         $this->getMessages()->displayMessage(Messages::ApacheHeadersModule_DISABLE_MESSAGE);
     }
}

//19 - function
function IsEnableRewriteMod(){
    if (empty($apacheModules)) {
        $this->getMessages()->displayMessage(Messages::EnableRewriteMod_UNABLE_MESSAGE);
    } else if (in_array('mod_rewrite', $apacheModules)) {
        $this->getMessages()->displayMessage(Messages::EnableRewriteMod_OK_MESSAGE);
    } else  {
        $this->interuptContinue = true;
        $this->getMessages()->displayMessage(Messages::EnableRewriteMod_DISABLE_MESSAGE);
    }
}

//20 - function
function MySQLEventStatus(){                       
        $conn = $this->getConnection($_SESSION['dbInfo']);
        if ($conn) {   
            $result = mysqli_query($conn, "SHOW VARIABLES LIKE 'EVENT_SCHEDULER'");
            $row = mysqli_fetch_assoc($result);
                    $schedulerStatus = $row['Value'];
                    
                    if ($schedulerStatus == 'ON') {
			$this->getMessages()->displayMessage(Messages::MySQLEventStatus_OK_MESSAGE);                       
                    } else {
			$this->getMessages()->displayMessage(Messages::MySQLEventStatus_DISABLE_MESSAGE);                      
                    }

               } else {
		    $this->getMessages()->displayMessage(Messages::MySQLEventStatus_FAIL_MESSAGE);
		    $this->interuptContinue = true;
               }

 }

 //21 - function
function isCurlEnabled() {
    if (extension_loaded('curl')) {
        $this->getMessages()->displayMessage(Messages::CURLStatus_OK_MESSAGE);
    } else {
        $this->getMessages()->displayMessage(Messages::CURLStatus_DISABLE_MESSAGE);
        $this->interuptContinue = true;
    }
}

    //22 - function
    function isSimpleXMLEnabled() {
        if (extension_loaded('SimpleXML') && extension_loaded('libxml') && extension_loaded('xml')) {
            $this->getMessages()->displayMessage(Messages::SimpleXMLStatus_OK_MESSAGE);
        } else {
            $this->getMessages()->displayMessage(Messages::SimpleXMLStatus_DISABLE_MESSAGE);
            $this->interuptContinue = true;
        }
    }

    /**
     * Check script execute in apache server
     * @return bool
     */
 private function isApacheServer() {
    $isApache = false;
    $sapiName = php_sapi_name();
    switch ($sapiName) {
        case 'apache':
        case 'apache2filter':
        case 'apache2handler':
            $isApache = true;
        break;
    }
    return $isApache;
 }

/*
 *check Database connection and validation parts.
 *details compare with config.ini file or user inputs.
 *$_SESSION['dbInfo'] set this variable in DetailsHandler.php file , DetailsHandler class->setConfigurationFromParameter()
 */
function dbConfigurationCheck()
{	

	 $dbInfo = $_SESSION['dbInfo'];
        $conn = $this->getConnection($dbInfo);
        if ($conn) {
                if (!($this->systemValidator->isMySqlCompatible($dbInfo['dbHostName'], $dbInfo['dbUserName'], $dbInfo['dbPassword'], $dbInfo['dbHostPort']))){
                    $_SESSION['dbError'] = $this->systemValidator->getMysqlErrorMessage($dbInfo['dbHostName'], $dbInfo['dbUserName'], $dbInfo['dbPassword'], $dbInfo['dbHostPort']);
		}

                elseif ($_SESSION['dbCreateMethod'] == 'new' && mysqli_select_db($conn, $dbInfo['dbName'])){
                    $_SESSION['dbError'] = "Database (" . $_SESSION['dbInfo']['dbName'] . ") already exists";
		    $this->interuptContinue = true;
                    }
                elseif ($_SESSION['dbCreateMethod'] == 'new' && !isset($_SESSION['chkSameUser'])) {
			mysqli_select_db($conn, 'mysql');
	                $rset = mysqli_query($conn, "SELECT USER FROM user WHERE USER = '" . $dbInfo['dbOHRMUserName'] . "'");

                    if (mysqli_num_rows($rset) > 0){
                        $_SESSION['dbError'] = 'OrangehrmDatabase User name already exists'; 
			$this->interuptContinue = true; 
		    }                 
                } 
            } else
                $_SESSION['dbError'] = Messages::DB_WRONG_INFO;

	 if(isset($_SESSION['dbError']))
	 {
		$this->getMessages()->displayMessage($_SESSION['dbError']);
		$this->interuptContinue = true;
	 }else
	 {
		 $this->getMessages()->displayMessage(Messages::DB_CONFIG_SUCCESS);
	 }
   }

    /**
     * @param $dbInfo
     * @return bool|mysqli
     */
    private function getConnection($dbInfo)
    {
        $conn = false;

        if (function_exists('mysqli_connect')) {

            $conn = mysqli_connect(
                $dbInfo['dbHostName'],
                $dbInfo['dbUserName'],
                $dbInfo['dbPassword'],
                null,
                $dbInfo['dbHostPort']
            );

            if (mysqli_connect_error()) {
                print_r($dbInfo);
            }
        }

        return $conn;
    }
}


