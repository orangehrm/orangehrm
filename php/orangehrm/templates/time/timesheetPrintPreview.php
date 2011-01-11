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
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/event/event-min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['WPATH']; ?>/scripts/yui/connection/connection-min.js"></script>
<script type="text/javascript" src="../../scripts/archive.js"></script>

<script type="text/javascript">
currPage=1;
commonAction="?timecode=Time&action=";
pages=<?php echo $pages; ?>;
timesheetsCount=<?php echo $timesheetsCount; ?>;
connections=new Array(pages);
loadedPages=new Array(pages);

var LANG_NAV_FIRST = "<?php echo $lang_empview_first; ?>";
var LANG_NAV_PREVIOUS = "<?php echo $lang_empview_previous; ?>";
var LANG_NAV_NEXT = "<?php echo $lang_empview_next; ?>";
var LANG_NAV_LAST = "<?php echo $lang_empview_last; ?>";

var ITEMS_PER_PAGE = <?php echo $recordPerPage; ?>;
var PAGE_NUMBER_LIMIT = <?php echo CommonFunctions::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT; ?>;

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

	sUrl=commonAction+"Print_Timesheet_Get_Page&page="+page;

	callback = {
					success:loadedPage,
					failure: failedToLoad,
					argument: [page, silent]
				};

	postData = buildPostString("filterTimesheets");

	if (!silent) {
		showLoading(page);

		callback = {
					success:abortedPageLoad,
					failure: failedToAbortPageLoad,
					argument: [page, silent]
				   };

		for (i=1; pages>i; i++) {
			if (connections[i] && connections[i].argument[1]) {
				YAHOO.util.Connect.abort(connections[i], callback, false);
			}
		}
	}

	callback = {
					success:loadedPage,
					failure: failedToLoad,
					argument: [page, silent]
				};

	connections[page-1] = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
}

function abortedPageLoad(page) {
	connections[page-1]=false;
}

function failedToAbortPageLoad(page) {
	return false;
}

function loadedPage(o) {
	page = o.argument[0];
	silent = o.argument[1];

	if (o.responseText !== undefined){
		$('page'+page).innerHTML = o.responseText;
		$('page'+page).style.display="none";
	} else {
		return false;
	}

	connections[page-1]=false;
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
		if (loadedPages[i]) {
			$("page"+(i+1)).style.display="none";
		}
	}

	if (loadedPages[page-1]) {
		$("page"+page).style.display="block";
		navStr = printPageLinks(timesheetsCount, page);
		$("navPanel").innerHTML = navStr;
		currPage=page;
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

function printTimeSheets() {
	allLoaded=true;
	for (i=0; pages>i; i++) {
		if (!loadedPages[i]) {
			allLoaded=false;
		}
	}

	if (allLoaded) {
		popup.src = '?timecode=Time&action=Print';
		popup.parent = window;
	}
}

function popAndPrint() {
	frames['printFrame'].document.getElementById("printArea").innerHTML=$('printPanel').innerHTML;
	for (i=0; pages>i; i++) {
		frames['printFrame'].document.getElementById("page"+(i+1)).style.display="block";
	}
}

function init() {
	if (pages == 0) {
		$("loadingMessage").style.display="none";
		<?php
			$backButton = '<input type="button" class="backbutton" value="' . $lang_Common_Back . '"' .
					'onmouseout="moutButton(this)" onmouseover="moverButton(this)"  onclick="goBack()" />';
		?>
        $("printPanel").className = "notice";
		$("printPanel").innerHTML = '<b><?php echo CommonFunctions::escapeForJavascript($lang_Error_NoRecordsFound) . "<br /><br />{$backButton}"; ?></b>';
		return false;
	}
	loadPage(1, false);
	for (i=1; pages>i; i++) {
		loadPage(i+1, true);
	}
	popup = new getObj('printFrame').obj;
}

function goBack() {
	window.location=commonAction+"Select_Timesheets_View&cache=set";
}

YAHOO.util.Event.addListener(window, "load", init);
</script>

<style type="text/css">
div#nonPrintPanel, div#printIframePanel {
	margin: 8px;
}
.notice {
    color:#6e6a6a;
	font-size:16px;
}
</style>

<div id="nonPrintPanel">
	<span id="loadingMessage"><?php echo $lang_Common_Loading; ?>...</span>
	<h2><?php echo $lang_Time_PrintTimesheetsTitle; ?>  </h2> <h3><?php echo   $lang_Leave_Leave_list_From . ' : '     . LocaleUtil::getInstance()->formatDate($filterValues[4]); ?> <?php echo   $lang_Leave_Leave_list_To  . ' : '. LocaleUtil::getInstance()->formatDate($filterValues[5]); ?>  </h3>
	<?php if ($pages > 0) { ?>
  <div id="controls">
		<input type="button" value="<?php echo $lang_Common_Back; ?>" class="backbutton"
			onmouseout="moutButton(this)" onmouseover="moverButton(this)"
			onclick="goBack(); return false;" />
		<input type="button" class="plainbtn" name="btnPrint" id="btnPrint" value="<?php echo $lang_Time_Print; ?>"
			onmouseout="moutButton(this)" onmouseover="moverButton(this)"
			onclick="printTimeSheets();"/>
	</div>
	<?php } ?>
	<div id="navPanel"></div>

	<form id="filterTimesheets" name="filterTimesheets" method="post" action="?timecode=Time&action=Print_Timesheet_Get_Page">
		<input type="hidden" name="txtEmpID" id="txtEmpID" value="<?php echo $filterValues[0]; ?>" />
		<input type="hidden" name="txtLocation" id="txtLocation" value="<?php echo $filterValues[1]; ?>" />
		<input type="hidden" name="txtRepEmpID" id="txtRepEmpID" value="<?php echo $filterValues[2]; ?>" />
		<input type="hidden" name="txtEmploymentStatus" id="txtEmploymentStatus" value="<?php echo $filterValues[3]; ?>" />
		<input type="hidden" name="txtStartDate" id="txtStartDate" value="<?php echo LocaleUtil::getInstance()->formatDate($filterValues[4]); ?>" />
		<input type="hidden" name="txtEndDate" id="txtEndDate" value="<?php echo LocaleUtil::getInstance()->formatDate($filterValues[5]); ?>" />
	</form>

    <div id="printPanel" style="width:700px;">
		<?php for ($i=0; $i<$pages; $i++) { ?>
			<div id="page<?php echo $i+1; ?>" style="display:none;"></div>
		<?php } ?>
	</div>
</div>
<div id="printIframePanel" >
	<iframe id="printFrame" name="printFrame" width="0" height="0" style="border:none;"></iframe>
</div>
