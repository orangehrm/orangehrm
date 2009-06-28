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

if (!empty($records['error']['resumeUploadError'])) { // There was an error when uploading resume

	$heading = $lang_Recruit_ApplicationStatus_FailureHeading;
	$message = $lang_Recruit_ApplyFailure_UploadError;

} elseif (!empty($records['error']['resumeCompatibleError'])) { // Uploaded resume is not compatible

	$heading = $lang_Recruit_ApplicationStatus_FailureHeading;

	if ($records['error']['resumeCompatibleError'] == 'size-error') { // Size of uploaded resume exceeds the limit
		$message = $lang_Recruit_ApplyFailure_UploadSizeError;
	} elseif ($records['error']['resumeCompatibleError'] == 'type-error') { // Type of uploaded resume is not allowed
		$message = $lang_Recruit_ApplyFailure_UploadTypeError;
	}

} elseif (!is_null($records['savingStatus']) && !$records['savingStatus']) { // There was an error when saving the application

    $heading = $lang_Recruit_ApplicationStatus_FailureHeading;
    $message = $lang_Recruit_ApplyFailure;

} elseif (!is_null($records['savingStatus']) && $records['savingStatus']) { // Application saved successfully

	$heading = $lang_Recruit_ApplicationStatus_SuccessHeading;

	if (empty($records['error']['applicantEmailError'])) { // An email was sent to the applicant informing submission
	    $message = str_replace('#jobtitle#', $records['vacancy']->getJobTitleName(), $lang_Recruit_ApplySuccess);
	    $message .= '. ';
	    $message .= str_replace('#email#', $records['application']->getEmail(), $lang_Recruit_ApplicantEmailedSuccess);
	} else { // Emailing the applicant failed
	    $message = str_replace('#jobtitle#', $records['vacancy']->getJobTitleName(), $lang_Recruit_ApplySuccess);
	}

}

$picDir = "../../themes/{$styleSheet}/pictures/";

$backImg = $picDir . 'btn_back.gif';
$backImgPressed = $picDir . 'btn_back_02.gif';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script>
    function goBack() {
        location.href = "<?php echo "{$_SERVER['PHP_SELF']}?recruitcode=ApplicantViewJobs"; ?>";
    }
</script>

<style type="text/css">
<!--
body {
	margin-top: 10px;
    margin-left: auto;
    margin-right: auto;
    width: 780px;
}
-->
</style>

    <script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 6]>
    <link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
    <![endif]-->
    <!--[if IE]>
    <link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
    <![endif]-->

</head>
<body>
    <div class="formpage3col">
        <div class="navigation">
        	<input type="button" class="backbutton" value="<?php echo $lang_Common_Back;?>"
        		onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
        </div>
		<div id="status" style="float:right;display:none;">
            <?php echo $lang_Commn_PleaseWait;?>
        </div>
        <div class="outerbox">
            <div class="mainHeading">
                <h2><?php echo $heading; ?></h2></div>
			<div class="messagebar"><?php echo $message;?></div>
		</div>
    </div>

    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    //]]>
    </script>

</body>
</html>
