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

$vacancies = $records['vacancies'];
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

function apply(jobId) {
    window.location = "?recruitcode=ApplicantViewApplication&id=" + jobId;
}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
<style type="text/css">
.jobTitle {
	font-weight: bold;
	font-size: 15px;
    color: black;
}

.jobDescription {
	background: #b1fdf7;
	padding: 5px 10px 5px 10px;
	margin: 5px 0px 5px 0px;
}

.roundbox {
    margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
    width: 760px;
}

body {
	margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
    width: 780px;
}

.roundbox_content {
    padding:15px;
}
</style>
</head>
<body>
	<p><h2 class="moduleTitle"><?php echo $lang_Recruit_ApplicantVacancyList_Heading; ?></h2></p>
<?php
	if (empty($vacancies)) {
?>
<div class="roundbox">
	<?php echo $lang_Recruit_Applicant_NoVacanciesFound;?>
</div>
<?php
	}
	foreach ($vacancies as $vacancy) {
?>
<div class="roundbox">
<div class="jobTitle"><?php echo htmlspecialchars($vacancy->getJobTitleName()); ?></div>
<div class="jobDescription"><?php echo nl2br(htmlspecialchars($vacancy->getDescription())); ?></div>
<?php
	$iconDir = "../../themes/{$styleSheet}/icons/";
	$applyIcon = $iconDir . 'apply.gif';
	$applyIconPressed = $iconDir . 'apply_o.gif';
?>

<img class="applyButton" src="<?php echo $applyIcon; ?>"
	onMouseOut="this.src='<?php echo $applyIcon; ?>';"
	onMouseOver="this.src='<?php echo $applyIconPressed; ?>';" onClick="apply(<?php echo $vacancy->getId(); ?>)">
</div>
<?php
	}
?>

    <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
    </script>
</body>
</html>
