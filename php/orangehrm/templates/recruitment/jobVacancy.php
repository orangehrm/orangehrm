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

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$action = $_GET['action'];

if ($action == 'ViewAdd') {
	$new = true;
	$btnAction="addSave()";
	$heading = $lang_Recruit_JobVacancy_Add_Heading;
	$formAction = "{$baseURL}&action=Add";
	$disabled = '';
} else {
	$new = false;
	$btnAction="addUpdate()";
	$heading = $lang_Recruit_JobVacancy_Edit_Heading;
	$formAction = "{$baseURL}&action=Update";
	$disabled = "disabled='disabled'";
}

$noOfEmployees = $records['noOfEmployees'];
$employeeSearchList = $records['employeeSearchList'];
$manager = $records['manager'];
$jobTitles = $records['jobTitles'];
$vacancy = $records['vacancy'];
$locRights=$_SESSION['localRights'];

$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script>
//<![CDATA[
	var editMode = <?php echo $new ? 'true' : 'false'; ?>;

	var employeeSearchList = new Array();

    function goBack() {
        location.href = "<?php echo $baseURL; ?>&action=List";
    }

	function validate() {
		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();
        if ($('cmbHiringManager').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobVacancy_PleaseSpecifyHiringManager; ?>\n";
        }

        if ($('cmbJobTitle').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobVacancy_PleaseSpecifyJobTitle; ?>\n";
        }

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

    function save() {

    	$('cmbHiringManager').value = '-1';

    	for (i in employeeSearchList) {
    		if ($('txtHiringManagerSearch').value == employeeSearchList[i][0]) {
    			$('cmbHiringManager').value = employeeSearchList[i][2];
    			break;
    		}
    	}

		if (validate()) {
        	$('frmJobVacancy').submit();
		} else {
			return false;
		}
    }

	function reset() {
		$('frmJobVacancy').reset();
	}

	function edit()	{

<?php if($locRights['edit']) { ?>
		if (editMode) {
			save();
			return;
		}
		editMode = true;
		var frm = $('frmJobVacancy');

		for (var i=0; i < frm.elements.length; i++) {
			frm.elements[i].disabled = false;
		}

		$('editBtn').value = "<?php echo $lang_Common_Save; ?>";

<?php } else {?>
		alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
	}

	function showAutoSuggestTip(obj) {
		if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
			obj.value = '';
			obj.style.color = '#000000';
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

    <style type="text/css">
    <!--

    #active {
        width: 15px;
        height: 15px;
        background-color: transparent;
        vertical-align: bottom;
    }

    #txtDesc {
        width: 330px;
        height: 150px;
    }

	#nohiringmanagers {
		font-style: italic;
		color: red;
        padding-left: 10px;
        width: 400px;
        border: 1px;
	}

	#employeeSearchAC {
 	    width:15em; /* set width here */
 	    padding-bottom:2em;
        margin: 10px 0px 2px 10px;
 	}

 	#employeeSearchAC {
 	    z-index:9000; /* z-index needed on top instance for ie & sf absolute inside relative issue */
 	}

 	#txtEmployeeSearch {
 	    _position:absolute; /* abs pos needed for ie quirks */
 	}
    -->
</style>
<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>
</head>
<body class="yui-skin-sam">
    <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" value="<?php echo $lang_Common_Back;?>"
        		onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $heading;?></h2></div>

        <?php $message =  isset($this->getArr['message']) ? $this->getArr['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

  <form name="frmJobVacancy" id="frmJobVacancy" method="post" action="<?php echo $formAction;?>" onSubmit="return false;">
  		<?php
			$prevEmpNum = isset($this->postArr['cmbHiringManager']) ? $this->postArr['cmbHiringManager'] : $vacancy->getManagerId();
			if ($prevEmpNum == '') {
				$prevEmpNum = '-1';
				$empName = $lang_Common_TypeHereForHints;
			} else {
				$empName = $manager;
			}
		?>
     <input type="hidden" value="<?php echo $token;?>" name="token" />
  		<input type="hidden" name="cmbHiringManager" id="cmbHiringManager" value="<?php echo $prevEmpNum ?>" />
		<input type="hidden" id="txtId" name="txtId" value="<?php echo $vacancy->getId();?>"/><br class="clear"/>
		<label for="cmbJobTitle"><?php echo $lang_Recruit_JobTitleName; ?><span class="required">*</span> </label>
        <select id="cmbJobTitle" name="cmbJobTitle" tabindex="1" <?php echo $disabled;?> class="formSelect">
	        <option value="-1">-- <?php echo $lang_Recruit_JobVacancy_JobTitleSelect;?> --</option>
                <?php
                $prevTitleCode = isset($this->postArr['cmbJobTitle']) ? $this->postArr['cmbJobTitle'] : $vacancy->getJobTitleCode();
                foreach ($jobTitles as $jobTitle) {
                	$jobTitleCode = $jobTitle[0];
                    $selected = ($prevTitleCode == $jobTitleCode) ? 'selected="selected"' : '';
	                echo "<option " . $selected . " value=". $jobTitleCode . ">" . $jobTitle[1] . "</option>";
                }
                ?>
        </select>
        <br class="clear"/>
		<div>
		<label for="txtHiringManagerSearch"><?php echo $lang_Recruit_HiringManager; ?><span class="required">*</span></label>
		<div class="yui-ac" id="employeeSearchAC" style="float: left">
 	 		      <input autocomplete="off" class="yui-ac-input" id="txtHiringManagerSearch" type="text" value="<?php echo CommonFunctions::escapeHtml($empName) ?>" <?php echo $disabled; ?> tabindex="2" onfocus="showAutoSuggestTip(this)" style="color: #999999" />
 	 		      <div class="yui-ac-container" id="employeeSearchACContainer" style="top: 28px; left: 10px;">
 	 		        <div style="display: none; width: 159px; height: 0px; left: 100em" class="yui-ac-content">
 	 		          <div style="display: none;" class="yui-ac-hd"></div>
 	 		          <div class="yui-ac-bd">
 	 		            <ul>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		              <li style="display: none;"></li>
 	 		            </ul>
 	 		          </div>
 	 		          <div style="display: none;" class="yui-ac-ft"></div>
 	 		        </div>
 	 		        <div style="width: 0pt; height: 0pt;" class="yui-ac-shadow"></div>
 	 	      </div>
    	</div>
    	</div>
        <br class="clear"/>
		<?php
				if ($noOfEmployees == 0) {
		?>
			<div id="nohiringmanagers">
				<?php echo $lang_Recruit_NoHiringManagersNotice; ?>
			</div>
		<?php
				}
		?>
		<label for="txtDesc"><?php echo $lang_Commn_description; ?></label>
        <textarea id="txtDesc" name="txtDesc" tabindex="3" rows="10" cols="50" class="formTextArea"
        	<?php echo $disabled;?>><?php echo htmlspecialchars($vacancy->getDescription()); ?></textarea><br class="clear"/>
		<label for="active"><?php echo $lang_Recruit_JobVacancy_Active; ?></label>
        <input type="checkbox" id="active" name="active" tabindex="4" <?php echo $disabled;?> class="formCheckbox"
        	<?php echo $vacancy->isActive() ? 'checked="checked"':"";?> />
        <br class="clear"/>

        <div class="formbuttons">
            <input type="button" class="<?php echo $new ? 'savebutton': 'editbutton';?>" id="editBtn" tabindex="5"
                onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" />
            <input type="button" class="clearbutton" onclick="reset();" tabindex="6"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Common_Reset;?>" />
        </div>
        <br class="clear"/>
	</form>
    </div>

        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }

		<?php
			$i = 0;

			foreach ($employeeSearchList as $record) {
                foreach ($record as $pos => $item) {
                    $record[$pos] = CommonFunctions::escapeForJavascript($item);

                }
		?>
			employeeSearchList[<?php echo $i++; ?>] = new Array('<?php echo implode("', '", $record); ?>');
		<?php
			}
		?>


 	 	YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {

			// Instantiate second JS Array DataSource
		    this.oACDS = new YAHOO.widget.DS_JSArray(employeeSearchList);

		    // Instantiate second AutoComplete
		    this.oAutoComp = new YAHOO.widget.AutoComplete('txtHiringManagerSearch','employeeSearchACContainer', this.oACDS);
		    this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
		    this.oAutoComp.typeAhead = false;
		    this.oAutoComp.useShadow = true;
		    this.oAutoComp.forceSelection = true;
		    this.oAutoComp.formatResult = function(oResultItem, sQuery) {
		        var sMarkup = oResultItem[0] + "<br />" + oResultItem[1] .fontsize(-1).fontcolor('#999999')  + "&nbsp;";
		        return (sMarkup);
		    };

 	 	};
        //]]>
 	 </script>
    </div>
</body>
</html>
