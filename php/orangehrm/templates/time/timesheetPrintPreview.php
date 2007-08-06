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

$filterValues = $records[0];
$timesheetsCount = $records[1];
$recordPerPage = $records[2];

$pages = ceil($timesheetsCount/$recordPerPage);
?>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/connection/connection-min.js"></script>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
currPage=1;
commonAction="?timecode=Time&action=Print_Timesheet_Get_Page";
pages=<?php echo $pages; ?>;
connections=new Array(pages);
loadedPages=new Array(pages);

for (i=0; pages>i; i++) {
	connections[i]=false;
	loadedPages[i]=false;
}

function nextPage() {
	currPage++;
	chgPage(currPage);
}

function nextPage() {
	loadPage(currPage+1, false);
}

function prevPage() {
	loadPage(currPage-1, false);
}

function chgPage(page) {
	loadPage(page, false);
}

function loadPage(page, silent) {

	if (loadedPages[page-1]) {
		showPage(page);

		return true;
	}

	showLoading(page);

	sUrl=commonAction+"&page="+page;

	callback = {
					success:loadedPage,
					failure: failedToLoad,
					argument: [page, silent]
				};

	postData = buildPostString("filterTimesheets");

	connections[page-1] = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);

	showLoading(page);
}

function loadedPage(o) {
	page = o.argument[0];
	silent = o.argument[1];

	if (o.responseText !== undefined){
		$('page'+page).innerHTML = o.responseText;
	} else {
		return false;
	}

	loadedPages[page-1]=true;

	if (!silent) {
		showPage(page);
		hideLoading(page);
	}
}

function failedToLoad(o) {
	page = o.argument[0];
	silent = o.argument[1];

	if (!silent) {
		alert('Failed to load page '+page+'. Check your network connection.');
	}

	connections[page-1]=false;
	loadedPages[page-1]=false;

	hideLoading(page);
}

function showPage(page) {
	for (i=0; pages>i; i++) {
		if (loadedPages[page-1] && (i == (page+1))) {
			$("page"+page).style.display="none";
		}
	}

	if (loadedPages[page-1]) {
		$("page"+page).style.display="block";
	} else {
		alert("Page is not loaded yet, please try again");
	}
}

function showLoading(page) {
	if (connections[page-1] && !loadedPages[page-1]) {
		$('loadingMessage').style.display="block";
	}
}

function hideLoading(page) {
	if (!connections[page-1] || loadedPages[page-1]) {
		$("loadingMessage").style.display="none";
	}
}

</script>
<span id="loadingMessage"><?php echo $lang_Common_Loading; ?>...</span>
<h2><?php echo $lang_Time_PrintTimesheetsTitle; ?></h2>

<form id="filterTimesheets" name="filterTimesheets" method="post" action="?timecode=Time&action=Print_Timesheet_Get_Page">
	<input type="hidden" name="txtEmpID" id="txtEmpID" value="<?php echo $filterValues[0]; ?>" />
	<input type="hidden" name="txtLocation" id="txtLocation" value="<?php echo $filterValues[1]; ?>" />
	<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" value="<?php echo $filterValues[2]; ?>" />
	<input type="hidden" name="txtEmploymentStatus" id="txtEmploymentStatus" value="<?php echo $filterValues[3]; ?>" />
	<input type="hidden" name="txtStartDate" id="txtStartDate" value="<?php echo $filterValues[4]; ?>" />
	<input type="hidden" name="txtEndDate" id="txtEndDate" value="<?php echo $filterValues[5]; ?>" />
</form>

<div id="printPanel">
	<?php for ($i=0; $i<$pages; $i++) { ?>
		<div id="page<?php echo $i+1; ?>" class="hidden"></div>
	<?php } ?>
</div>
<div id="navPanel">
<?php
$temp = $timesheetsCount;
$currentPage = 1;
$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;
?>
</div>
<div id="controls">
</div>
<script type="text/javascript">
loadPage(1, false);
</script>
