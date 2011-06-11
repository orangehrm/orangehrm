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
 */

require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
$lan = new Language();

require_once ROOT_PATH . '/language/default/lang_default_full.php';
require_once($lan->getLangPath("full.php"));

$lang_Template_rep_headName = array(
		'EMPNO' => $lang_rep_EmployeeNo,
		'EMPFIRSTNAME' => $lang_rep_EmployeeFirstName,
		'EMPLASTNAME' => $lang_rep_EmployeeLastName,
		'ADDRESS1' => $lang_rep_Address,
		'TELENO' => $lang_rep_Telephone,
		'AGE' => $lang_rep_AgeGroup,
		'REPORTTO' => $lang_rep_ReportTo,
		'REPORTINGMETHOD' => $lang_rep_ReportingMethod,
		'JOBTITLE' => $lang_rep_JobTitle,
		'SERPIR' => $lang_rep_JoinedDate,
		'SUBDIVISION' => $lang_rep_SubDivision,
		'QUL' => $lang_rep_Qualification,
		'YEAROFPASSING' => $lang_rep_YearOfPassing,
		'EMPSTATUS' => $lang_rep_EmployeeStatus,
		'PAYGRD' => $lang_rep_SalaryGrade,
		'LANGUAGES' => $lang_rep_Languages,
		'SKILLS' => $lang_rep_Skills,
		'CONTRACT' => $lang_rep_Contract,
		'WORKEXPERIENCE' => $lang_rep_WorkExperience);

$styleSheet = CommonFunctions::getTheme();

$replacements = array('REPORTINGMETHOD' => $records['reportingMethods']);

function formatValue($string, $key, $replacements) {
	if ($string == ReportField::EMPTY_MARKER) {
	    return $string;
	}
	
	if (array_key_exists($key, $replacements)) {
	    $string = $replacements[$key][$string];
	}
	
	if ($key === 'AGE' || $key === 'SERPIR') {
	    $duration = $string;
	} elseif ($key === 'CONTRACT') {
	    list($start, $end) = explode(' - ', $string);
	    $string = LocaleUtil::getInstance()->formatDate($start) . ' - ' . LocaleUtil::getInstance()->formatDate($end);
	}
	
    return $string;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<script language="JavaScript">
function goBack() {
	location.href = "./CentralController.php?repcode=EMPVIEW&VIEW=MAIN";
	}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css" media="all">
body {
    margin:4px 4px 4px 0px;
}

ul {
	margin: 0px 0px 0px 12px;
	left: -6px;
	position: relative;
	top: 0px;
    padding-top: 2px;
    padding-left: 0px;
}

h2 {
    display:block;
    text-align:center;
}

th {
    text-align:left;
    vertical-align:top;
}

td {
    vertical-align:top;
    padding: 2px 4px 2px 4px;
}

li {
    list-style-type:disc;
}

</style>
</head>
<body>
<div class="navigation">
	<input type="button" value="<?php echo $lang_Common_Back; ?>" class="backbutton"
		onmouseover="moverButton(this)" onmouseout="moutButton(this)"
		onclick="goBack();" />
</div>
<h2><?php echo "{$lang_rep_Report}: {$records['reportName']}"; ?></h2>
<table class="simpleList" style="margin: 8px 8px 8px 8px; min-width: 99%; width: <?php echo count($records['headerNames']) * 10; ?>%;">
	<thead>
		<tr>
<?php
	foreach ($records['headerNames'] as $headerName){
		$isHeaderSet = (isset($lang_Template_rep_headName) && isset($lang_Template_rep_headName[$headerName]));
		$colHead = ($isHeaderSet) ? $lang_Template_rep_headName[$headerName] : $headerName;
?>
	<th><?php echo $colHead; ?></th>
<?php
}
?>
		</tr>
	</thead>
	<tbody>
<?php
$repDetails = $records['arrayDispList'];

if (is_array($repDetails)) {
	$odd = true;
	foreach ($repDetails as $key => $records) {
		$className = ($odd) ? 'odd' : 'even';
		$odd = !$odd;
?>
	<tr>
<?php foreach ($records as $columnName => $columnValue) { ?>
    	<td class="<?php echo $className; ?>">
<?php
	if (is_array($columnValue)) {
		$emptyList = true;
		echo '<ul>';
		foreach ($columnValue as $item) {
			if (empty($item) || $item == ReportField::EMPTY_MARKER) {
				$emptyValueFound = true;
			    continue;
			}
		    echo "<li>" . formatValue($item, $columnName, $replacements) . "</li>\n";
		    $emptyList = false;
		}
		echo '</ul>';
		echo ($emptyList) ? ReportField::EMPTY_MARKER : '';
	} else {
		echo (empty($columnValue)) ? ReportField::EMPTY_MARKER : formatValue($columnValue, $columnName, $replacements);
	} 
?>
    	</td>
<?php } ?>
	</tr>
<?php
	}
}
?>
	</tbody>
</table>
</body>
</html>
