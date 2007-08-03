<?php
function sockComm($data) {

	$host = 'www.orangehrm.com';
	$method = 'POST';
	$path = '/registration/registerAcceptor.php';
	/*$data = "userName=".$postArr['userName']
			."&userEmail=".$postArr['userEmail']
			."&userComments=".$postArr['userComments']
			."&updates=".$postArr['chkUpdates'];*/

	$fp = @fsockopen($host, 80);

	if(!$fp)
	    	return false;

	    fputs($fp, "POST $path HTTP/1.1\r\n");
	    fputs($fp, "Host: $host\r\n");
	    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
	    fputs($fp, "User-Agent: OrangeHRM Appliance Installer\r\n");
	    fputs($fp, "Connection: close\r\n\r\n");
	    fputs($fp, $data);

	    $resp = '';
	    while (!feof($fp)) {
	        $resp .= fgets($fp,128);
	    }

	    fclose($fp);

	    if(strpos($resp, 'SUCCESSFUL') === false)
	    	return false;

	return true;
}

if (sockComm($argv[1])) {
	exit(0);
}

exit(1);
?>