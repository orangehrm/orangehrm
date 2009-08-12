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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

function apply(jobId) {
    window.location = "?recruitcode=ApplicantViewApplication&id=" + jobId;
}

function showhide(link, row) {
    if (link.className == 'expanded') {
        link.className = 'collapsed';
        $('details_' + row).style.display = 'none';
        $('summary_' + row).style.display = 'block';
    } else {
        link.className = 'expanded';
        $('details_' + row).style.display = 'block';
        $('summary_' + row).style.display = 'none';
    }
}

function expandAll() {
    toggleDescriptions(true);
}

function collapseAll() {
    toggleDescriptions(false);
}

function toggleDescriptions(expand) {

    var numVacancies = <?php echo count($vacancies);?>;
    for (var row=1; row <= numVacancies; row++) {
        var link = $('link_' + row);

        if (expand) {
            link.className = 'expanded';
            $('details_' + row).style.display = 'block';
            $('summary_' + row).style.display = 'none';
        } else {
            link.className = 'collapsed';
            $('details_' + row).style.display = 'none';
            $('summary_' + row).style.display = 'block';
        }
    }
}

//]]>
</script>
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<link href="../../themes/<?php echo $styleSheet;?>/css/jobs.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="formpage3col">
    <div class="outerbox" id="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Recruit_ApplicantVacancyList_Heading;?></h2></div>

<?php
    if (empty($vacancies)) {
?>
	   <div class="novacancies"><?php echo $lang_Recruit_Applicant_NoVacanciesFound;?></div>
<?php
    } else {
?>
       <div class="actionbar">
            <a id="expandAll" href="#" onclick="expandAll()"><?php echo $lang_Recruit_Applicant_ExpandAll;?></a>
            <a id="collapseAll" href="#" onclick="collapseAll()"><?php echo $lang_Recruit_Applicant_CollapseAll;?></a>
       </div><br class="clear"/>
<?php
        $row = 0;
        foreach ($vacancies as $vacancy) {

            $cssClass = ($row%2) ? 'odd' : 'even';
            $row++;

            $id = $vacancy->getId();
            $title = $vacancy->getJobTitleName();
            $description = $vacancy->getDescription();
            $summary = substr($description, 0, 125) . '...';
?>
        <div class="jobHeading <?php echo $cssClass;?>" style="height:36px">
            <a href="#" class="collapsed" id="link_<?php echo $row;?>" onclick="showhide(this, <?php echo $row;?>)" style="height:30px">
                <span class="jobTitle" style="width:auto"><?php echo $title; ?></span>
                <br class="clear" />
                <span class="jobSummary" id="summary_<?php echo $row;?>"><?php echo $summary;?></span>
            </a>
            <br class="clear"/>
        </div>

        <div class="jobDetails <?php echo $cssClass;?>" id="details_<?php echo $row;?>" style="display:none;">
            <div class="jobDescription"><?php echo nl2br($description); ?></div>
            <div class="applydiv">
            <input type="button" class="applybutton"
                onclick="apply(<?php echo $id; ?>);" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Apply;?>" />
            </div>
        </div>
<?php
    	}
?>
    <div class="<?php echo ($row%2) ? 'odd' : 'even'?>">
    <br class="clear"/>
    </div>
<?php
    }
?>



    </div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>
</body>
</html>