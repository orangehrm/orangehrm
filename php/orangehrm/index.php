<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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

define('ROOT_PATH', dirname(__FILE__));

if(!is_file(ROOT_PATH . '/lib/confs/Conf.php')) {
	header('Location: ./install.php');
	exit ();
}

session_start();
if(!isset($_SESSION['fname'])) {

	header("Location: ./login.php");
	exit();
}

define('Admin', 'MOD001');
define('PIM', 'MOD002');
define('MT', 'MOD003');
define('Report', 'MOD004');
define('Leave', 'MOD005');
define('TimeM', 'MOD006');

$arrRights=array('add'=> false , 'edit'=> false , 'delete'=> false, 'view'=> false);
$arrAllRights=array(Admin => $arrRights,
					PIM => $arrRights,
					MT => $arrRights,
					Report => $arrRights,
					Leave => $arrRights,
					TimeM => $arrRights);

require_once ROOT_PATH . '/lib/models/maintenance/Rights.php';
require_once ROOT_PATH . '/lib/models/maintenance/UserGroups.php';

$_SESSION['path'] = ROOT_PATH;

if($_SESSION['isAdmin']=='Yes') {
	$rights = new Rights();

	//	$arrRights=array('add'=> true , 'edit'=> true, 'delete'=> true, 'view'=> true);

	foreach ($arrAllRights as $moduleCode=>$currRights) {
		$arrAllRights[$moduleCode]=$rights->getRights($_SESSION['userGroup'], $moduleCode);
	}

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="eim"))
		$arrRights=$arrAllRights[Admin];

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="hr"))
		$arrRights=$arrAllRights[PIM];

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="mt"))
		$arrRights=$arrAllRights[MT];

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="rep"))
		$arrRights=$arrAllRights[Report];

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="leave"))
		$arrRights=$arrAllRights[Leave];

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="time"))
		$arrRights=$arrAllRights[TimeM];


	$ugroup = new UserGroups();
	$ugDet = $ugroup ->filterUserGroups($_SESSION['userGroup']);

	$arrRights['repDef'] = $ugDet[0][2] == '1' ? true : false;

		$_SESSION['localRights']=$arrRights;
}

if (isset($_POST['styleSheet'])) {
	$styleSheet = $_POST['styleSheet'];
} else {
	$styleSheet = "beyondT";
}
if (($styleSheet == '') && (!isset($styleSheet))) {
	$styleSheet = "beyondT";
} else {
	$styleSheet = $styleSheet;
	session_register($styleSheet);
}

if(isset($_GET['ACT']) && $_GET['ACT']=='logout') {
	session_destroy();
	setcookie('Loggedin', '', time()-3600, '/');
	header("Location: ./login.php");
}

require_once ROOT_PATH . '/lib/common/authorize.php';

$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);

if ($authorizeObj->isESS()) {
	if ($authorizeObj->isSupervisor()) {
		$leaveHomePage = 'lib/controllers/CentralController.php?leavecode=Leave&action=Leave_FetchLeaveSupervisor';
	} else {
		$leaveHomePage = 'lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Summary&id='.$_SESSION['empID'];
	}
	$timeHomePage = 'lib/controllers/CentralController.php?timecode=Time&action=View_Current_Timesheet';
	$timesheetPage = 'lib/controllers/CentralController.php?timecode=Time&action=View_Current_Timesheet';
} else {
	$leaveHomePage = 'lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Type_Summary';
	$timeHomePage = 'lib/controllers/CentralController.php?timecode=Time&action=View_Select_Employee';

	$timesheetPage = 'lib/controllers/CentralController.php?timecode=Time&action=View_Select_Employee';
}

require_once ROOT_PATH . '/lib/common/Language.php';

$lan = new Language();

require_once($lan->getLangPath("full.php"));

?>
<html>
<head>
<title>OrangeHRM</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="themes/beyondT/pictures/styles.css" rel="stylesheet" type="text/css">
<link href="themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<link href="favicon.ico" rel="icon" type="image/gif"/>
<style type="text/css">@import url("themes/beyondT/css/menu.css"); </style>
<script language=javascript src="scripts/ypSlideOutMenus.js"></script>
<!DOCTYPE html PUBLIC "-//W3C//DTD html 4.01 Transitional//EN">
<script language="JavaScript">
//window.onresize = setSize();

		var yPosition = 108;

		var agt=navigator.userAgent.toLowerCase();

		var xPosition = 150;

		if (agt.indexOf("konqueror") != -1) var xPosition = 144;

		if (agt.indexOf("windows") != -1) var xPosition = 144;

		if (agt.indexOf("msie") != -1) var xPosition = 150;


		new ypSlideOutMenu("menu1", "right", xPosition, yPosition, 150, 230)
		new ypSlideOutMenu("menu2", "right", xPosition, yPosition + 22, 146, 360)
		new ypSlideOutMenu("menu3", "right", xPosition, yPosition + 44, 146, 220)
		new ypSlideOutMenu("menu4", "right", xPosition, yPosition + 66, 146, 80)
		new ypSlideOutMenu("menu5", "right", xPosition, yPosition + 88, 146, 130)
		//new ypSlideOutMenu("menu6", "right", xPosition, yPosition + 110, 146, 140)
		//new ypSlideOutMenu("menu7", "right", xPosition, yPosition + 132, 146, 205)
		//new ypSlideOutMenu("menu8", "right", xPosition, yPosition + 82, 146, 130)
		new ypSlideOutMenu("menu9", "right", xPosition, yPosition + 110, 146, 80)
		//new ypSlideOutMenu("menu10", "right", xPosition, yPosition + 110, 146, 120)
		new ypSlideOutMenu("menu12", "right", xPosition, yPosition + 132, 146, 120)
		new ypSlideOutMenu("menu15", "right", xPosition, yPosition + 154, 146, 120)
		new ypSlideOutMenu("menu13", "right", xPosition, yPosition, 146, 120)
		new ypSlideOutMenu("menu14", "right", xPosition, yPosition + 22, 146, 120)
		new ypSlideOutMenu("menu16", "right", xPosition, yPosition, 146, 120)
		//new ypSlideOutMenu("menu11", "right", xPosition, yPosition + 220, 146, 205)

function swapImgRestore() {
  var i,x,a=document.sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function preloadImages() {
  var d=document; if(d.images){ if(!d.p) d.p=new Array();
    var i,j=d.p.length,a=preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.p[j]=new Image; d.p[j++].src=a[i];}}
}
function findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}
function swapImage() {
  var i,j=0,x,a=swapImage.arguments; document.sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=findObj(a[i]))!=null){document.sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function showHideLayers() {
  var i,p,v,obj,args=showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style	; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}

function setSize() {
	var iframeElement = document.getElementById('rightMenu');
	iframeElement.style.height = (window.innerHeight - 20) + 'px'; //100px or 100%
	iframeElement.style.width = '100%'; //100px or 100%
}
</SCRIPT>
<style type="text/css">
#rightMenu {
	z-index: 0;
}
</style>

</head>
<body  onload="preloadImages('themes/beyondT/pictures/buttons01_on.gif','themes/beyondT/pictures/buttons02_on.gif','themes/beyondT/pictures/buttons03_on.gif','themes/beyondT/pictures/buttons04_on.gif','themes/beyondT/pictures/buttons05_on.gif',
     'themes/beyondT/pictures/buttons06_on.gif','themes/beyondT/pictures/buttons07_on.gif','themes/beyondT/pictures/buttons08_on.gif','themes/beyondT/pictures/buttons09_on.gif','themes/beyondT/pictures/buttons10_on.gif','themes/beyondT/pictures/buttons11_on.gif')">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<form name="indexForm" action="./menu.php?TEST=1111" method="post">
<input type="hidden" name="tabnumber" value="1">
<a name="a"></a>
<tr>
  <td colspan="2"><table cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr height="50">
        <td width="23%"><img src=<?php echo '"' . "themes/" . $styleSheet . "/pictures/orange3.png" . '"'; ?>  width="264" height="62" alt="Company Logo" border="0" style="margin-left: 10px;"></td>
        <td width="77%" align="right" nowrap class="myArea"><img src="themes/beyondT/pictures/top_img.jpg" width="300" height="62">
        </td>
      </tr>
      <tr>
        <?php
	if (!isset($_GET['menu_no_top'])) {
		$_GET['menu_no_top'] = "home";
	}

	if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="home")) {
	?>
        <td colspan="2"><table cellspacing="0" cellpadding="0" border="0" width="100%">
          <tr height="20">
            <td><img src="" width="8" height="1" border="0" alt="Home"></td>
            <td style="background-image : url();" ></td>
            <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
            <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                <tr height="20">
                  <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                  <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" nowrap><a class="currentTab"  href="./index.php?module=Home&menu_no=0&menu_no_top=home&submenutop=home1" ><?php echo $lang_Menu_Home; ?></a></td>
                  <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                  <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                </tr>
            </table></td>
            <?php } else { ?>
            <td colspan="2"><table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr height="20">
                  <td><img src="" width="8" height="1" border="0" alt="Home"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="Dashboard"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a class="otherTab"  href="./index.php?module=Home&menu_no=0&menu_no_top=home&submenutop=home1"><?php echo $lang_Menu_Home; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="Dashboard"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } ?>
                  <?php
                  if($_SESSION['isAdmin']=='Yes') {
						if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="eim") && $arrRights['view']) {

					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a   class="currentTab"  href="./index.php?module=Home&menu_no=1&submenutop=EIMModule&menu_no_top=eim" ><?php echo $lang_Menu_Admin; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a class="otherTab" href="index.php?module=Home&menu_no=1&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } ?>
                  <?php
						if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="hr") && $arrAllRights[PIM]['view']) {
					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a   class="currentTab"  href="./index.php?module=Home&menu_no=12&submenutop=home1&menu_no_top=hr" >PIM</a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else if ($arrAllRights[PIM]['view']) { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a   class="otherTab"  href="./index.php?module=Home&menu_no=12&submenutop=home1&menu_no_top=hr">PIM</a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php }
                  }
                  if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="leave") && (($_SESSION['isAdmin']!='Yes') || $arrAllRights[Leave]['view'])) {
					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a class="currentTab"  href="./index.php?module=Home&menu_no=1&submenutop=LeaveModule&menu_no_top=leave" ><?php echo $lang_Menu_Leave; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else if (($_SESSION['isAdmin']!='Yes') || $arrAllRights[Leave]['view']) { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a   class="otherTab"  href="index.php?module=Home&menu_no=3&menu_no_top=leave"><?php echo $lang_Menu_Leave; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php }
                  if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="time") && (($_SESSION['isAdmin']!='Yes') || $arrAllRights[TimeM]['view'])) {
					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a class="currentTab"  href="./index.php?module=Home&menu_no=1&submenutop=LeaveModule&menu_no_top=time" ><?php echo $lang_Menu_Time; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else if (($_SESSION['isAdmin']!='Yes') || $arrAllRights[TimeM]['view']) { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a   class="otherTab"  href="index.php?module=Home&menu_no=3&menu_no_top=time"><?php echo $lang_Menu_Time; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php }
                  if($_SESSION['isAdmin']=='Yes') {
						if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="rep") && $arrAllRights[Report]['view']) {
					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a   class="currentTab"  href="./index.php?module=Home&menu_no=12&submenutop=home1&menu_no_top=rep"><?php echo $lang_Menu_Reports; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else if ($arrAllRights[Report]['view']) { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a   class="otherTab"  href="./index.php?module=Home&menu_no=12&submenutop=home1&menu_no_top=rep"><?php echo $lang_Menu_Reports; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } ?>
                  <?php
                  } else {
						if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="ess")) {
					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a class="currentTab"  href="./index.php?module=Home&menu_no=1&submenutop=EIMModule&menu_no_top=ess" ><?php echo $lang_Menu_Ess; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a   class="otherTab"  href="index.php?module=Home&menu_no=3&menu_no_top=ess"><?php echo $lang_Menu_Ess; ?></a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php }
                  }
						if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="bug")) {

					?>
                  <td style="background-image : url();" ></td>
                  <td style="padding-left:7px; background-image :url(themes/beyondT/pictures/nCurrentTab_left.gif);"></td>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_left.gif);" ></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_middle.gif);" class="currentTab" nowrap><a   class="currentTab"  href="./index.php?module=Home&menu_no=1&submenutop=EIMModule&menu_no_top=bug" >Bug Tracker</a></td>
                        <td style="background-image : url(themes/beyondT/pictures/nCurrentTab_right.gif);"><img src="" width="8" height="1" border="0" alt="Home"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } else { ?>
                  <td><table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
                      <tr height="20">
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_left.png);" ><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_middle.png);" class="otherTab" nowrap><a class="otherTab" href="index.php?module=Home&menu_no=1&submenutop=EIMModule&menu_no_top=bug">Bug Tracker</a></td>
                        <td style="background-image : url(themes/beyondT/pictures/otherTab_right.png);"><img src="" width="8" height="1" border="0" alt="My Portal"></td>
                        <td style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                      </tr>
                  </table></td>
                  <?php } ?>
                  <td width="100%" style="background-image : url(themes/beyondT/pictures/emptyTabSpace.png);"><img src="" width="1" height="1" border="0" alt=""></td>
                </tr>
            </table></td>
          </tr>
          <tr height="20">
            <input type="hidden" name="action" value="UnifiedSearch">
            <input type="hidden" name="module" value="Home">
            <input type="hidden" name="search_form" value="false">
            <td class="subTabBar" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0" height="20">
                <tr>
                  <td class="welcome" width="100%"><?php echo preg_replace('/#username/', ((isset($_SESSION['fname'])) ? $_SESSION['fname'] : ''), $lang_index_WelcomeMes); ?></td>
                  <td class="search" align="right" nowrap="nowrap"><a href="./lib/controllers/CentralController.php?mtcode=CPW&capturemode=updatemode&id=<?php echo $_SESSION['user']?>" target="rightMenu"><strong><?php echo $lang_index_ChangePassword; ?></strong></a></td>
                  <td class="search" style="padding: 0px" align="right" width="11"><img src="themes/beyondT/pictures/nSearchSeparator.gif" width="12" height="20" border="0" alt="Search"></td>
                  <td class="search" style="padding: 0px" align="right" nowrap="nowrap">&nbsp;&nbsp;<a href="./index.php?ACT=logout"><strong><?php echo $lang_index_Logout; ?></strong></a></td>
                  <td class="search" nowrap>&nbsp;&nbsp; </td>
                </tr>
            </table></td>
          </tr>
        </table>
      </table>

<?php if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']!="hr" && $_GET['menu_no_top']!="ess" )) {  ?>

	<table border="0" align="top" cellpadding="0" cellspacing="0">
          <tr>
            <td width="200" valign="top"><!-- Rollover buttons -->
              <TABLE cellSpacing=0 cellPadding=0 border=0>
                <TBODY>
                  <TR vAlign=top>
<?php if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="eim") && $arrRights['view']) {  ?>
                    <TD width=158>
                      <ul id="menu">
  						<li id="compinfo"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu1');" onMouseOut="ypSlideOutMenu.hideMenu('menu1');"><?php echo $lang_Menu_Admin_CompanyInfo; ?></a></li>
  						<li id="job"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu2');" onMouseOut="ypSlideOutMenu.hideMenu('menu2');"><?php echo $lang_Menu_Admin_Job; ?></a></li>
  						<li id="qualification"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu3');" onMouseOut="ypSlideOutMenu.hideMenu('menu3');"><?php echo $lang_Menu_Admin_Quali; ?></a></li>
  						<li id="skills"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu4');" onMouseOut="ypSlideOutMenu.hideMenu('menu4');"><?php echo $lang_Menu_Admin_Skills; ?></a></li>
  						<li id="memberships"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu5');" onMouseOut="ypSlideOutMenu.hideMenu('menu5');"><?php echo $lang_Menu_Admin_Memberships; ?></a></li>
  						<li id="natandrace"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu9');" onMouseOut="ypSlideOutMenu.hideMenu('menu9');"><?php echo $lang_Menu_Admin_NationalityNRace; ?></a></li>
						<li id="users"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu12');" onMouseOut="ypSlideOutMenu.hideMenu('menu12');"><?php echo $lang_Menu_Admin_Users; ?></a></li>
						<li id="notifications"><a href="#" onMouseOver="ypSlideOutMenu.showMenu('menu15');" onMouseOut="ypSlideOutMenu.hideMenu('menu15');"><?php echo $lang_Menu_Admin_EmailNotifications; ?></a></li>
						<li id="customers"><a href="index.php?uniqcode=CUS&menu_no=2&submenutop=EIMModule&menu_no_top=eim" ><?php echo $lang_Menu_Admin_Customers; ?></a></li>
						<li id="customers"><a href="index.php?uniqcode=PRJ&menu_no=2&submenutop=EIMModule&menu_no_top=eim" ><?php echo $lang_Menu_Admin_Projects; ?></a></li>
</ul></TD>
<?php			} else if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="rep")) { ?>
                    <TD width=158>
                    <ul id="menu">
  						<li id="viewemprep"><A href="index.php?repcode=EMPVIEW&menu_no=1&submenutop=HR&menu_no_top=rep"><?php echo $lang_Menu_Reports_ViewReports; ?></A></li>

<?php               if($arrRights['repDef']) {?>
						<li id="defemprep"><A href="index.php?repcode=EMPDEF&menu_no=1&submenutop=HR&menu_no_top=rep"><?php echo $lang_Menu_Reports_DefineReports; ?></A></li>
<?php					}
					} else ?>
					</ul>
                      </TD>
<?php			 if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="leave" )) { ?>
             <TD width=158>
                      <ul id="menu">
                      	<?php
                      		$allowedRoles = array($authorizeObj->roleAdmin, $authorizeObj->roleSupervisor);

                 			//if ($authorizeObj->firstRole($allowedRoles)) {

                 			if ($authorizeObj->isESS()) {
                 				$linkSummary = 'href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Summary&id='.$_SESSION['empID'].'"';
                 			} else {
                 				$linkSummary = "";
                 			}
                      	?>
  						<li id="leaveSummary"><a <?php echo $linkSummary; ?> target="rightMenu" onMouseOver="ypSlideOutMenu.showMenu('menu13');" onMouseOut="ypSlideOutMenu.hideMenu('menu13');"><?php echo $lang_Menu_Leave_LeaveSummary; ?></a></li>
  						<?php
  							if ($authorizeObj->isAdmin()) {
						?>
  						<li id="defineLeaveType"><a target="rightMenu" onMouseOver="ypSlideOutMenu.showMenu('menu14');" onMouseOut="ypSlideOutMenu.hideMenu('menu14');"><?php echo $lang_Menu_Leave_DefineDaysOff; ?></a></li>
  						<li id="defineLeaveType"><a href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Type_Summary" target="rightMenu"><?php echo $lang_Menu_Leave_LeaveTypes; ?></a></li>
  						<?php
  							}
                 			if ($authorizeObj->isESS()) {
  						?>
  						<li id="leaveList"><a href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_FetchLeaveEmployee" target="rightMenu"><?php echo $lang_Menu_Leave_LeaveList; ?></a></li>
  						<li id="applyLeave"><a href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Apply_view" target="rightMenu"><?php echo $lang_Menu_Leave_Apply; ?></a></li>
  						<?php
                      		}
                      		if ($authorizeObj->isAdmin() || $authorizeObj->isSupervisor()) {
                      	?>
                      	<li id="applyLeave"><a href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Apply_Admin_view" target="rightMenu"><?php echo $lang_Menu_Leave_Assign; ?></a></li>
                 		<?php
                      		}
                 			if ($authorizeObj->isSupervisor()) {
                 		?>
  						<li id="approveLeave"><a href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_FetchLeaveSupervisor" target="rightMenu"><?php echo $lang_Menu_Leave_ApproveLeave; ?></a></li>
						<?php } ?>
  					</ul>
			</TD>
<?php			}

				if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="time" )) { ?>
           	<TD width=158>
	            <ul id="menu">
	            	<li id="timesheets"><a href="<?php echo $timesheetPage; ?>" target="rightMenu" onMouseOver="ypSlideOutMenu.showMenu('menu16');" onMouseOut="ypSlideOutMenu.hideMenu('menu16');"><?php echo $lang_Menu_Time_Timesheets; ?></a></li>
  				</ul>
			</TD>

<?php			}
				if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="home")) {  ?>
		                <TD valign="top" width=158>
		                    <ul id="menu">
		  						<li id="viewemprep"><a href="http://www.orangehrm.com/home/index.php?option=com_content&task=section&id=13&Itemid=73" target="_blank"><?php echo $lang_Menu_Home_Support; ?></a></li>
		  						<li id="viewemprep"><a href="http://www.orangehrm.com/forum/" target="_blank"><?php echo $lang_Menu_Home_Forum; ?></a></li>
		  						<li id="viewemprep"><a href="http://orangehrm.blogspot.com/" target="_blank"><?php echo $lang_Menu_Home_Blog; ?></a></li>
		  				    </ul>
		  				</td>

<?php			}
				 if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="bug" )) { ?>
				  <TD height="800" bgcolor="#FFB121" width=158><p><br>
                    </p></TD>
<?php			}  ?>
                </TR>
                </TBODY>
              </TABLE>
              <!-- End Rollover buttons -->
              <!--------------------- Menu start --------------------->
              <!-- Begin SubMenu1 -->
              <DIV id=menu1Container>
                <DIV id=menu1Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu1')" onMouseOut="ypSlideOutMenu.hideMenu('menu1')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=GEN&menu_no=1&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_CompanyInfo_Gen; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu1')" onMouseOut="ypSlideOutMenu.hideMenu('menu1')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=CST&menu_no=1&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_CompanyInfo_CompStruct; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu1')" onMouseOut="ypSlideOutMenu.hideMenu('menu1')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=LOC&menu_no=1&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_CompanyInfo_Locations; ?></A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu1 -->
              <!-- Begin SubMenu2 -->
              <DIV id=menu2Container>
                <DIV id=menu2Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu2')" onMouseOut="ypSlideOutMenu.hideMenu('menu2')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=JOB&menu_no=2&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Job_JobTitles; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu2');ypSlideOutMenu.showMenu('menu2')" onMouseOut="ypSlideOutMenu.hideMenu('menu2');ypSlideOutMenu.hideMenu('menu2')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=SGR&menu_no=2&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Job_PayGrades; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu2')" onMouseOut="ypSlideOutMenu.hideMenu('menu2')" vAlign=center align=left width=142 height=17><A class="rollmenu" href="index.php?uniqcode=EST&menu_no=2&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Job_EmpStatus; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu2')" onMouseOut="ypSlideOutMenu.hideMenu('menu2')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=EEC&menu_no=2&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Job_EEO; ?></A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu2  -->
              <!-- Begin SubMenu3 -->
              <DIV id=menu3Container>
                <DIV id=menu3Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu3')" onMouseOut="ypSlideOutMenu.hideMenu('menu3')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=EDU&menu_no=3&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Quali_Education; ?></A> </TD>
                    </TR>
                    <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu3')" onMouseOut="ypSlideOutMenu.hideMenu('menu3')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=LIC&menu_no=3&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Quali_Licenses; ?></A></TD>
                    </TR>
                    </TBODY>

                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu3 -->
              <!-- Begin SubMenu4 -->
              <DIV id=menu4Container>
                <DIV id=menu4Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu4')" onMouseOut="ypSlideOutMenu.hideMenu('menu4')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=SKI&menu_no=3&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Skills_Skills; ?></A></TD>
                    </TR>
                    <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu4')" onMouseOut="ypSlideOutMenu.hideMenu('menu4')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=LAN&menu_no=3&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Skills_Languages; ?></A></TD>
                    </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu4 -->
              <!-- Begin SubMenu5 -->
              <DIV id=menu5Container>
                <DIV id=menu5Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu5')" onMouseOut="ypSlideOutMenu.hideMenu('menu5')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=MEM&menu_no=4&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Memberships_MembershipTypes; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu5')" onMouseOut="ypSlideOutMenu.hideMenu('menu5')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=MME&menu_no=4&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_Memberships_Memberships; ?></A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu5 -->
              <!-- Begin SubMenu6
              <DIV id=menu6Container>
                <DIV id=menu6Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <TD onmouseover="ypSlideOutMenu.showMenu('menu6')" onmouseout="ypSlideOutMenu.hideMenu('menu6')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=CCB&menu_no=6&submenutop=EIMModule&menu_no_top=eim">Cash Benefits</A></TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu6')" onmouseout="ypSlideOutMenu.hideMenu('menu6')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=NCB&menu_no=6&submenutop=EIMModule&menu_no_top=eim">Non-Cash Benefits</A> </TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu6')" onmouseout="ypSlideOutMenu.hideMenu('menu6')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=UNI&menu_no=6&submenutop=EIMModule&menu_no_top=eim">Uniform Types</A> </TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu6')" onmouseout="ypSlideOutMenu.hideMenu('menu6')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=BBS&menu_no=6&submenutop=EIMModule&menu_no_top=eim">Cash Benefits Assigned to Salary &nbsp;Grade</A></TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu6')" onmouseout="ypSlideOutMenu.hideMenu('menu6')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=NBS&menu_no=6&submenutop=EIMModule&menu_no_top=eim">Non - Cash Benefits Assigned to Salary &nbsp;Grade</A> </TD>
                    </TR>
                    </TBODY>

                  </TABLE>
                </DIV>
              </DIV>
               End SubMenu7 -->
              <!-- Begin SubMenu7
              <DIV id=menu7Container>
                <DIV id=menu7Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <TD onmouseover="ypSlideOutMenu.showMenu('menu7')" onmouseout="ypSlideOutMenu.hideMenu('menu7')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=ETY&menu_no=7&submenutop=EIMModule&menu_no_top=eim">Employee Type</A></TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu7')" onmouseout="ypSlideOutMenu.hideMenu('menu7')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=SAT&menu_no=7&submenutop=EIMModule&menu_no_top=eim">Statutory Classification</A></TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu7')" onmouseout="ypSlideOutMenu.hideMenu('menu7')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=EMC&menu_no=7&submenutop=EIMModule&menu_no_top=eim">Employee Category</A></TD>
                    </TR>
                    <TR>
                      <TD onmouseover="ypSlideOutMenu.showMenu('menu7')" onmouseout="ypSlideOutMenu.hideMenu('menu7')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=EMG&menu_no=7&submenutop=EIMModule&menu_no_top=eim">Employee Groups</A></TD>
                    </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
                End SubMenu7 -->
              <!-- Begin SubMenu8
              <DIV id=menu8Container>
                <DIV id=menu8Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onmouseover="ypSlideOutMenu.showMenu('menu8')" onmouseout="ypSlideOutMenu.hideMenu('menu8')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=RTE&menu_no=8&submenutop=EIMModule&menu_no_top=eim">Route Information</A></TD>
                      </TR>
                      <TR>
                        <TD onmouseover="ypSlideOutMenu.showMenu('menu8')" onmouseout="ypSlideOutMenu.hideMenu('menu8')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=DWT&menu_no=8&submenutop=EIMModule&menu_no_top=eim">Dwelling Type</A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
               End SubMenu8 -->
              <!-- Begin SubMenu9 -->
              <DIV id=menu9Container>
                <DIV id=menu9Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu9')" onMouseOut="ypSlideOutMenu.hideMenu('menu9')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=NAT&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_NationalityNRace_Nationality; ?></A></TD>
                      </TR>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu9')" onMouseOut="ypSlideOutMenu.hideMenu('menu9')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=ETH&menu_no=9&submenutop=EIMModule&menu_no_top=eim"><?php echo $lang_Menu_Admin_NationalityNRace_EthnicRaces; ?></A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu9 -->
              <!-- Begin SubMenu10
              <DIV id=menu10Container>
                <DIV id=menu10Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <TD onMouseOver="ypSlideOutMenu.showMenu('menu10')" onMouseOut="ypSlideOutMenu.hideMenu('menu10')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=COU&menu_no=10&submenutop=EIMModule&menu_no_top=eim">Country</A></TD>
                    </TR>
                    <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu10')" onMouseOut="ypSlideOutMenu.hideMenu('menu10')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=PRO&menu_no=10&submenutop=EIMModule&menu_no_top=eim">State/Province</A></TD>
                    </TR>
                    <TR>
                      <TD onMouseOver="ypSlideOutMenu.showMenu('menu10')" onMouseOut="ypSlideOutMenu.hideMenu('menu10')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=DIS&menu_no=10&submenutop=EIMModule&menu_no_top=eim">City</A></TD>
                    </TR>
                    </TBODY>

                  </TABLE>
                </DIV>
              </DIV>
               End SubMenu10 -->
			   <!-- Begin SubMenu11
              <DIV id=menu11Container>
                <DIV id=menu11Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onmouseover="ypSlideOutMenu.showMenu('menu11')" onmouseout="ypSlideOutMenu.hideMenu('menu11')" vAlign=center align=left width=142 height=17><A class=rollmenu href="index.php?uniqcode=TAX&menu_no=11&submenutop=EIMModule&menu_no_top=eim">Define Tax</A></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              End SubMenu11 -->
              <!-- Begin SubMenu12 -->
              <DIV id=menu12Container>
                <DIV id=menu12Content>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu12')" onMouseOut="ypSlideOutMenu.hideMenu('menu12')" vAlign=center align=left width=142 height=17><A class=rollmenu  href="index.php?uniqcode=USR&menu_no=1&submenutop=BR&menu_no_top=eim&isAdmin=Yes"><?php echo $lang_Menu_Admin_Users_HRAdmin; ?></A></TD>
					 </TR>
					 <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu12')" onMouseOut="ypSlideOutMenu.hideMenu('menu12')" vAlign=center align=left width=142 height=17><A class=rollmenu  href="index.php?uniqcode=USR&menu_no=1&submenutop=BR&menu_no_top=eim&isAdmin=No"><?php echo $lang_Menu_Admin_Users_ESS; ?></A></TD>
					 </TR>
					 <tr>
						<TD onMouseOver="ypSlideOutMenu.showMenu('menu12')" onMouseOut="ypSlideOutMenu.hideMenu('menu12')" vAlign=center align=left width=142 height=17><A class=rollmenu  href="index.php?uniqcode=USG&menu_no=1&submenutop=BR&menu_no_top=eim"><?php echo $lang_Menu_Admin_Users_UserGroups; ?></A></TD>
						</TR>
                    </TBODY>
                  </TABLE>
                </DIV>
              </DIV>
              <!-- End SubMenu12 -->
               <!-- Begin SubMenu13 -->
              <DIV id=menu13Container>
                <DIV id=menu13Content>
                 <?php
                 	$allowedRoles = array($authorizeObj->roleAdmin, $authorizeObj->roleSupervisor);

                 	if ($authorizeObj->firstRole($allowedRoles)) {
                 ?>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <?php
                    	if ($authorizeObj->isESS()) {
                    ?>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu13')" onMouseOut="ypSlideOutMenu.hideMenu('menu13')" onClick="ypSlideOutMenu.hideMenu('menu13')" vAlign=center align=left width=142 height=17><A class=rollmenu href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Summary&id=<?php echo $_SESSION['empID']; ?>" target="rightMenu"><?php echo $lang_Menu_Leave_PersonalLeaveSummary; ?></A></TD>
					 </TR>
					<?php
                    	}
                    ?>
					 <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu13')" onMouseOut="ypSlideOutMenu.hideMenu('menu13')" onClick="ypSlideOutMenu.hideMenu('menu13')" vAlign=center align=left width=142 height=17><A class=rollmenu href="lib/controllers/CentralController.php?leavecode=Leave&action=Leave_Select_Employee_Leave_Summary" target="rightMenu"><?php echo $lang_Menu_Leave_EmployeeLeaveSummary; ?></A></TD>
					 </TR>
                    </TBODY>
                  </TABLE>
                  <?php
                 	}
                 ?>
                </DIV>
              </DIV>
              <!-- End SubMenu13 -->
              <!-- Begin SubMenu14 -->
              <DIV id=menu14Container>
                <DIV id=menu14Content>
                 <?php
				 	if ($authorizeObj->isAdmin()) {
                 ?>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu14')" onMouseOut="ypSlideOutMenu.hideMenu('menu14')" onClick="ypSlideOutMenu.hideMenu('menu14')" vAlign=center align=left width=142 height=17><A class=rollmenu  href="lib/controllers/CentralController.php?leavecode=Leave&action=Holiday_Weekend_List" target="rightMenu"><?php echo $lang_Menu_Leave_DefineDaysOff_Weekends; ?></A></TD>
					 </TR>
					 <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu14')" onMouseOut="ypSlideOutMenu.hideMenu('menu14')" onClick="ypSlideOutMenu.hideMenu('menu14')" vAlign=center align=left width=142 height=17><A class=rollmenu  href="lib/controllers/CentralController.php?leavecode=Leave&action=Holiday_Specific_List" target="rightMenu"><?php echo $lang_Menu_Leave_DefineDaysOff_SpecificHolidays; ?></A></TD>
					 </TR>
                    </TBODY>
                  </TABLE>
                  <?php
                 	}
                 ?>
                </DIV>
              </DIV>
              <!-- End SubMenu14 -->
              <!-- Begin SubMenu15 -->
              <DIV id=menu15Container>
                <DIV id=menu15Content>
                 <?php
				 	if ($authorizeObj->isAdmin()) {
                 ?>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                      <tr>
                        <td onMouseOver="ypSlideOutMenu.showMenu('menu15')" onMouseOut="ypSlideOutMenu.hideMenu('menu15')" vAlign=center align=left width=142 height=17><a class=rollmenu  href="index.php?uniqcode=EMX&submenutop=EIMModule&menu_no_top=eim" ><?php echo $lang_Menu_Admin_EmailConfiguration; ?></a></td>
					 </tr>
					 <tr>
                        <td onMouseOver="ypSlideOutMenu.showMenu('menu15')" onMouseOut="ypSlideOutMenu.hideMenu('menu15')" vAlign=center align=left width=142 height=17><a class=rollmenu href="index.php?uniqcode=ENS&submenutop=EIMModule&menu_no_top=eim" ><?php echo $lang_Menu_Admin_EmailSubscribe; ?></a></td>
					 </tr>
                    </TBODY>
                  </TABLE>
                  <?php
                 	}
                 ?>
                </DIV>
              </DIV>
              <!-- End SubMenu15 -->
               <!-- Begin SubMenu16 -->
              <DIV id=menu16Container>
                <DIV id=menu16Content>
                 <?php
                 	$allowedRoles = array($authorizeObj->roleAdmin, $authorizeObj->roleSupervisor);

                 	if ($authorizeObj->firstRole($allowedRoles)) {
                 ?>
                  <TABLE cellSpacing=0 cellPadding=0 width=142 border=0>
                    <TBODY>
                    <?php
                    	if ($authorizeObj->isESS()) {
                    ?>
                      <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu16')" onMouseOut="ypSlideOutMenu.hideMenu('menu16')" onClick="ypSlideOutMenu.hideMenu('menu16')" vAlign=center align=left width=142 height=17>
                        	<A class=rollmenu href="lib/controllers/CentralController.php?timecode=Time&action=View_Current_Timesheet" target="rightMenu"><?php echo $lang_Menu_Time_PersonalTimesheet; ?></A>
                        </TD>
					 </TR>
					<?php
                    	}
                    ?>
					 <TR>
                        <TD onMouseOver="ypSlideOutMenu.showMenu('menu16')" onMouseOut="ypSlideOutMenu.hideMenu('menu16')" onClick="ypSlideOutMenu.hideMenu('menu16')" vAlign=center align=left width=142 height=17>
                        	<A class=rollmenu href="lib/controllers/CentralController.php?timecode=Time&action=View_Select_Employee" target="rightMenu"><?php echo $lang_Menu_Time_EmployeeTimesheets; ?></A>
                        </TD>
					 </TR>
                    </TBODY>
                  </TABLE>
                  <?php
                 	}
                 ?>
                </DIV>
              </DIV>
              <!-- End SubMenu16 -->
              <!--------------------- End Menu --------------------->
            </td>
            <td width="779" valign="top" id="rightMenuHolder">
            <table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
              <tr>
                <td>
            <td valign="top">
<?php		if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="home")) {  ?>
			  <iframe src="home.html" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"></iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="eim") && $arrRights['view']) {  ?>
              <iframe src="./lib/controllers/CentralController.php?uniqcode=<?php echo (isset($_GET['uniqcode'])) ? $_GET['uniqcode'] : 'GEN'?>&VIEW=MAIN<?php echo isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : ''?>" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="hr") && $arrRights['view']) {  ?>
              <iframe src="./lib/controllers/CentralController.php?reqcode=<?php echo (isset($_GET['reqcode'])) ? $_GET['reqcode'] : 'EMP'?>&VIEW=MAIN" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php			} else if ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="bug")) {  ?>
              <iframe src="./lib/controllers/CentralController.php?mtcode=BUG&capturemode=addmode" id="rightMenu" name="rightMenu" width="100%" height="750" frameborder="0"> </iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="rep")) {  ?>
              <iframe src="./lib/controllers/CentralController.php?repcode=<?php echo isset($_GET['repcode']) ? $_GET['repcode'] : 'EMPVIEW'?>&VIEW=MAIN" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="ess")) {  ?>
              <iframe src="./lib/controllers/CentralController.php?reqcode=ESS&id=<?php echo $_SESSION['empID']?>&capturemode=updatemode" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="ess")) {  ?>
              <iframe src="./lib/controllers/CentralController.php?reqcode=<?php echo (isset($_GET['reqcode'])) ? $_GET['reqcode'] : 'ESS'?>&id=<?php echo $_SESSION['empID']?>" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="leave")) {  ?>
              <iframe src="<?php echo $leaveHomePage; ?>" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php 		} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="time")) {  ?>
              <iframe src="<?php echo $timeHomePage; ?>" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php 		} ?>

            </td>
          </tr>
</table>
<?php	} elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="hr") && $arrRights['view']) {  ?>
              <iframe src="./lib/controllers/CentralController.php?reqcode=<?php echo (isset($_GET['reqcode'])) ? $_GET['reqcode'] : 'EMP'?>&VIEW=MAIN" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php } elseif ((isset($_GET['menu_no_top'])) && ($_GET['menu_no_top']=="ess")) { ?>
              <iframe src="./lib/controllers/CentralController.php?reqcode=ESS&id=<?php echo $_SESSION['empID']?>&capturemode=updatemode" id="rightMenu" name="rightMenu" width="100%" height="400" frameborder="0"> </iframe>
<?php } ?>
<table width="100%">
<tr>
<td align="center"><a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a> ver 2.2_beta_6 &copy; OrangeHRM Inc. 2005 - 2007 All rights reserved.</td>
</tr>
</table>
<script language="javascript">
function windowDimensions() {
	if (document.compatMode && document.compatMode != "BackCompat") {
   		x = document.documentElement.clientWidth;
	} else {
   		x = document.body.clientWidth;
   	}
   	y = document.body.clientHeight;

   	return [x,y];
}
function exploitSpace() {
	dimensions = windowDimensions();

	if (document.getElementById("rightMenu")) {
		document.getElementById("rightMenu").height = dimensions[1]-130;
	}

	if (document.getElementById("rightMenuHolder")) {
		document.getElementById("rightMenuHolder").width = dimensions[0]-200;
	}
}

exploitSpace();
window.onresize = exploitSpace;
</script>
</body>
</html>
