<?php
define('ROOT_PATH', dirname(__FILE__));

require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/Language.php';

$styleSheet = CommonFunctions::getTheme();

$case = isset($_GET['case']) ? htmlentities($_GET['case']) : null;
$type = isset($_GET['type']) ? htmlentities($_GET['type']) : 'notice';

$lang = new Language();

require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lang->getLangPath("full.php"));

$typeString = ucfirst($type);
$caseString = str_replace('-', ' ', $case);
$caseString = ucwords($caseString);
$caseString = str_replace(' ', '', $caseString);

$langVar = "lang_{$typeString}_{$caseString}";

$message = isset($$langVar) ? $$langVar : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>OrangeHRM</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
	body {
	    margin:10px 10px 10px 10px;
	}

	span#message {
		margin:10px 10px 10px 10px;
	    font-size:14px;
	    font-weight:bold;
	}
</style>

</head>

<body>
<span id="message" class="<?php echo $type; ?>"><?php echo $message; ?></span>
</body>

</html>
