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
require('Messages.php');
class DetailsHandler{


function getMessages(){
    if (!isset($messageList)) {
        $messageList = new Messages();
    }
    return $messageList;
}


public function checkDetailsValidation(){
			
		$configurationDataSet = parse_ini_file("config.ini");

		$dbHostName = $configurationDataSet["HostName"];
		$dbHostPort = $configurationDataSet["HostPortOrSocket"];
		$dbName = $configurationDataSet["DatabaseName"];

		$adminUserName = $configurationDataSet["AdminUserName"];
	        $adminPassword = $configurationDataSet["AdminPassword"];

		$dbOHRMUserName = $configurationDataSet["OrangehrmDatabaseUser"];
		$dbOHRMPassword =  $configurationDataSet["OrangehrmDatabasePassword"];

		$dbUserName = $configurationDataSet["PrivilegedDatabaseUser"];
		$dbPassword = $configurationDataSet["PrivilegedDatabasePassword"]; 	

		$databaseRootPassword = $configurationDataSet["DatabaseRootPassword"];

		$encryption = $configurationDataSet["Encryption"]; //"true = Active"/"Failed"

		$dbCreateMethod = $configurationDataSet["IsExistingDatabase"]; //existing/new
		$sameOhrmUser  = $configurationDataSet["UseTheSameOhrmDatabaseUser"];

 		$companyName = $configurationDataSet["CompanyName"];
 		$wantSendData = $configurationDataSet["SendUsageDataToOrangeHRM"];
		$isPort = $configurationDataSet["IsPort"];
			
		$this->setConfigurationFromParameter($dbHostName,$dbHostPort,$dbName,$adminUserName,$adminPassword,$dbOHRMUserName,$dbOHRMPassword,$dbUserName ,$dbPassword,$databaseRootPassword, $encryption , $dbCreateMethod , $sameOhrmUser,$companyName,$wantSendData, $isPort);
}


/*
 *get details related Database connection and validation parts.
 *details assign from with config.ini file or user inputs.
 *$_SESSION['dbInfo'] validation part will do in BasicConfiguration.php file , BasicConfiguration class->dbConfigurationCheck()
 */
public function setConfigurationFromParameter($hostName, $hostPortOrSocket, $databaseName,$adminUserName,$adminPassword,$orangehrmDatabaseUser,$orangehrmDatabasePassword,$privilegedDatabaseUser,$privilegedDatabasePassword,$databaseRootPassword ,$encryption,$DatabaseToUse,$sameOhrmUser,$companyName,$wantSendData,$isPort){
		$_SESSION['dbInfo']['dbHostName']= $this->isFillInConfig($hostName, "Host name ");
		$_SESSION['dbInfo']['dbHostPort']=$this->isFillInConfig($hostPortOrSocket, "Port or Socket id ");
//port or socket - related to boolean configuration		
 		$_SESSION['dbInfo']['dbHostPortModifier'] = $this->IsSocketOrPort($isPort);
		$_SESSION['dbInfo']['dbName'] = $this->isFillInConfig($databaseName, "Database name ");
		
		
//boolean configurations
		$_SESSION['ENCRYPTION']=$this->IsNeedEncryption($encryption); //"true = Active"/"Failed"

		$_SESSION['cMethod'] = $_SESSION['dbCreateMethod'] = $this->IsExistingDB($DatabaseToUse); //existing/new
		$sameUser  = $this->IsSameOhrmUser($sameOhrmUser);
		
		$_POST['registerCompanyName'] = $this->IsSetCompanyName($companyName);
		$_POST['hearbeatSelect'] = $this->WantSendUsageDataToOrangeHRM($wantSendData);


//Admin and password
		$_SESSION['defUser']['AdminUserName']= $this->isFillInConfig($adminUserName, "Admin user name ");
	        $_SESSION['defUser']['AdminPassword'] = $this->getPasswordFromUser($adminPassword, "Default admin ");



		 	
//Root password set to an environment variable. Its use in plugin install page. - (root)
		$DatabaseRootPWD= $this->getPasswordFromUser($databaseRootPassword, "Database root "); 
		putenv("DatabaseRootPassword=$DatabaseRootPWD");

			$_SESSION['INSTALLING'] = 0;
			$_SERVER['HTTP_USER_AGENT']=0;

			if($sameUser){
			$_SESSION['chkSameUser'] = 1; //dbConfig.php SameUser check box
			}
			else{
//OHRMUserName and password
		$_SESSION['dbInfo']['dbOHRMUserName']=$this->isFillInConfig($orangehrmDatabaseUser, "OrangeHRM DB User ");
		$_SESSION['dbInfo']['dbOHRMPassword'] =  $this->getPasswordFromUser($orangehrmDatabasePassword, "Orangehrm database user ");
			}

//Set Privileged Database User and password
			if($_SESSION['dbCreateMethod'] == "existing"){			
		$_SESSION['dbInfo']['dbOHRMUserName'] = $_SESSION['dbInfo']['dbUserName']=$this->isFillInConfig($_SESSION['dbInfo']['dbOHRMUserName'], "OrangeHRM DB User ");
		$_SESSION['dbInfo']['dbOHRMPassword'] = $_SESSION['dbInfo']['dbPassword'] =  $this->getPasswordFromUser($_SESSION['dbInfo']['dbOHRMPassword'], "Orangehrm database user "); 			
			}
			else{ 
			$_SESSION['dbInfo']['dbUserName']=$this->isFillInConfig($privilegedDatabaseUser, "privileged Database User ");
			$_SESSION['dbInfo']['dbPassword'] = $this->getPasswordFromUser($privilegedDatabasePassword , "privileged Database ");
			}


		        $this->getMessages()->displayMessage(Messages::SEPERATOR);

			if ( $_SESSION['ENCRYPTION']) {
            		    $keyResult = createKeyFile('key.ohrm');              			 
            		}
			else{
			    $_SESSION['ENCRYPTION'] = 'Failed';
			}
}



function isFillInConfig($SessionStatus,$inputType){
	if(!isset($SessionStatus) || trim($SessionStatus)==='')
	{
	    $messages = getMessages();
	    echo "Please enter $inputType : ";
	    $userValue = fopen ("php://stdin","r");
	    $returnValue = fgets($userValue);

	}else{
	    $returnValue = $SessionStatus;
	}

	return trim($returnValue);
}

function getPasswordFromUser($SessionStatus , $passwordType){
   
    if(!isset($SessionStatus) || trim($SessionStatus)==='')
    {
        // Get the password
        fwrite(STDOUT, "Please enter $passwordType password: "); 	   
        return $this->getPassword(true);
    }else{
        return $SessionStatus;
    }
}
/**
 * Get a password from the shell.
 *
 * This function works on *nix systems only and requires shell_exec and stty.
 *
 * @param  boolean $stars Wether or not to output stars for given characters
 * @return string
 */
function getPassword($stars = false)
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    if ($stars === false) {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } else {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) {
            $char = fgetc(STDIN);

            if ($char === "\n") {
		$this->getMessages()->displayMessage("\n");
                break;
            } else if (ord($char) === 127) {
                if (strlen($password) > 0) {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } else {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style
    shell_exec('stty ' . $oldStyle);

    // Return the password
    return trim($password);
}

function IsExistingDB($SessionStatus,$message=": "){   
    //return existing/new
	
	if(!isset($SessionStatus) || trim($SessionStatus)==='')
	{
	  $SessionStatus = $this->takeUserInput("Are you using existing database?  Type y/N".$message);
	  $SessionStatus = strtolower(trim($SessionStatus));
	  return (trim($SessionStatus) == 'y') ? 'existing' : ($SessionStatus == 'n'? 'new' : $this->IsExistingDB(null," (previous insert invalid): "));		
	}
	$SessionStatus = strtolower(trim($SessionStatus));
	return $SessionStatus == 'y' ? 'existing':( $SessionStatus == 'n'? 'new' : $this->IsExistingDB(null));
}


function IsNeedEncryption($encryptNeed,$message=": "){
	if(!isset($encryptNeed) || $encryptNeed ===''){
	 $encryptNeed = $this->takeUserInput("Do you want data encryption? type y/N".$message);
	 $encryptNeed = strtolower(trim($encryptNeed));
	 return ($encryptNeed == 'y') ? true :  ($encryptNeed == 'n'? false : $this->IsNeedEncryption(null," (previous insert invalid): "));
	}
	$encryptNeed = strtolower(trim($encryptNeed));
	return ($encryptNeed == 'y' || $encryptNeed == 1) ? true : ($encryptNeed == 'n'? false : $this->IsNeedEncryption(null));
}
 function IsSameOhrmUser($SessionStatus,$message=": "){
    //same-true  /  new - false
	$sameOhrm = strtolower(trim($SessionStatus));
    	if(!isset($sameOhrm) || $sameOhrm ===''){
	  $sameOhrm = $this->takeUserInput("Are you using same database user to orangehrm? Type 'y' to same user / 'n' to new user".$message);
   	  return (trim($sameOhrm) == 'y') ? true :  ($sameOhrm == 'n' ? false :  $this->IsSameOhrmUser(null," (previous insert invalid): "));
        }
	return $sameOhrm == 'y' ? true : ($sameOhrm == 'n' ? false :  $this->IsSameOhrmUser(null));
}

function IsSetCompanyName($companyName){
	$companyName = trim($companyName);
    	if(!isset($companyName) || $companyName ===''){
	  return  $this->takeUserInput("Company name?  (optional) : ");		 
        }
	return $companyName;
}

function WantSendUsageDataToOrangeHRM($sendData,$message=": "){

    	if(!isset($sendData) || $sendData ===''){
          $sendData = $this->takeUserInput("Do you want to send usage data to orangehrm? Type y/N".$message);
	  $sendData = strtolower(trim($sendData));
	  return ($sendData == 'y') ? 'on' :   ($sendData == 'n'? 'off' : $this->WantSendUsageDataToOrangeHRM(null," (previous insert invalid): "));		 
        }
	$sendData = strtolower(trim($sendData));
	return ($sendData == 'y') ? 'on' : ($sendData == 'n'? 'off' : $this->WantSendUsageDataToOrangeHRM(null));	
}


function takeUserInput($textToGetInput){
	$messages = getMessages();
	echo $textToGetInput;
	$handle = fopen ("php://stdin","r");
   	$line = fgets($handle);
	return  trim($line);
}

function IsSocketOrPort($socketOrPort,$message=": "){
  	 $socketOrPort = trim($socketOrPort);
    	if(!isset($socketOrPort) || $socketOrPort ===''){
          $socketOrPort = $this->takeUserInput("Are you using port? Type y/N".$message);
	  $socketOrPort = strtolower(trim($socketOrPort));
	  return ($socketOrPort == 'y') ? "port" :  ($socketOrPort == 'n'? 'socket' : $this->IsSocketOrPort(null," (previous insert invalid): "));		 
        }
	$socketOrPort = strtolower($socketOrPort);
	return ($socketOrPort == 'y') ? 'port' : ($socketOrPort == 'n'? 'socket' : $this->IsSocketOrPort(null));	
}

}
?>

