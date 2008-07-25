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
	$disabled = "disabled='true'";
}

$noOfEmployees = $records['noOfEmployees'];
$employeeSearchList = $records['employeeSearchList'];
$manager = $records['manager']; 
$jobTitles = $records['jobTitles'];
$vacancy = $records['vacancy'];
$locRights=$_SESSION['localRights'];
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>
	var editMode = <?php echo $new ? 'true' : 'false'; ?>;

	var employeeSearchList = new Array();
	
	<?php 
		$i = 0; 
		
		foreach ($employeeSearchList as $record) {
	?>
		employeeSearchList[<?php echo $i++; ?>] = new Array('<?php echo implode("', '", $record); ?>');
	<?php 
		}
	?>

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


	function mout() {
		if(editMode) {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif';
		} else {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_edit.gif';
		}
	}

	function mover() {
		if(editMode) {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save_02.gif';
		} else {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_edit_02.gif';
		}
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
		$('editBtn').src="../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif";
		$('editBtn').title="<?php echo $lang_Common_Save; ?>";

<?php } else {?>
		alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
	}

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

	.items {
		border-top: none;
		border-left: solid 1px #999999;
		border-right: solid 1px #999999;
		border-bottom: solid 1px #999999;
		padding: 4px;
		display: none;
		width: 240px;
	}

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }
    input[type=checkbox] {
		width: 15px;
		background-color: transparent;
		vertical-align: bottom;
    }

    #active {
        width: 15px;
        height: 15px;
        background-color: transparent;
        vertical-align: bottom;
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 110px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10px;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 330px;
        height: 150px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 500px;
    }

    .roundbox_content {
        padding:15px;
    }

	.hidden {
		display: none;
	}

	.display-block {
		display: block;
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
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $heading; ?></h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
  	<div id="navigation" style="margin:0;">
  		<img title="Back" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_back.gif';"
  			 onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_back_02.gif';"
  			 src="../../themes/<?php echo $styleSheet;?>/pictures/btn_back.gif" onClick="goBack();">
	</div>
    <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
    	if (isset($message)) {
			$col_def = CommonFunctions::getCssClassForMessage($message);
			$message = "lang_Common_" . $message;
	?>
	<div class="message">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
			<?php echo (isset($$message)) ? $$message: ""; ?>
		</font>
	</div>
	<?php }	?>
  <div class="roundbox">
  <form name="frmJobVacancy" id="frmJobVacancy" method="post" action="<?php echo $formAction;?>" onSubmit="return false;">
  		<?php
			$prevEmpNum = isset($this->postArr['cmbHiringManager']) ? $this->postArr['cmbHiringManager'] : $vacancy->getManagerId();
			if ($prevEmpNum == '') {
				$prevEmpNum = '-1';
				$empName = '';
			} else {
				$empName = $manager;
			}
		?>
  		<input type="hidden" name="cmbHiringManager" id="cmbHiringManager" value="<?php echo $prevEmpNum ?>" />
		<input type="hidden" id="txtId" name="txtId" value="<?php echo $vacancy->getId();?>"/><br/>
		<label for="cmbJobTitle"><span class="error">*</span> <?php echo $lang_Recruit_JobTitleName; ?></label>
        <select id="cmbJobTitle" name="cmbJobTitle" tabindex="1" <?php echo $disabled;?>>
	        <option value="-1">-- <?php echo $lang_Recruit_JobVacancy_JobTitleSelect;?> --</option>
                <?php
                $prevTitleCode = isset($this->postArr['cmbJobTitle']) ? $this->postArr['cmbJobTitle'] : $vacancy->getJobTitleCode();
                foreach ($jobTitles as $jobTitle) {
                	$jobTitleCode = $jobTitle[0];
                    $selected = ($prevTitleCode == $jobTitleCode) ? 'selected' : '';
	                echo "<option " . $selected . " value=". $jobTitleCode . ">" . $jobTitle[1] . "</option>";
                }
                ?>
        </select>
		<br />
		<div>
		<label for="txtHiringManagerSearch"><span class="error">*</span> <?php echo $lang_Recruit_HiringManager; ?></label>
		<div class="yui-ac" id="employeeSearchAC" style="float: left">
 	 		      <input autocomplete="off" class="yui-ac-input" id="txtHiringManagerSearch" type="text" value="<?php echo $empName ?>" <?php echo $disabled; ?> tabindex="2" />
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
		<br/>
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
        <textarea id="txtDesc" name="txtDesc" tabindex="3"
        	<?php echo $disabled;?>><?php echo htmlspecialchars($vacancy->getDescription()); ?></textarea><br/>
		<label for="active"><?php echo $lang_Recruit_JobVacancy_Active; ?></label>
        <input type="checkbox" id="active" name="active" tabindex="4" <?php echo $disabled;?>
        	<?php echo $vacancy->isActive() ? 'checked="1"':"";?> />
		<br/><br/>

        <div align="left">
            <img onClick="edit();" id="editBtn"
            	onMouseOut="mout();" onMouseOver="mover();"
            	src="../../themes/<?php echo $styleSheet;?>/pictures/<?php echo $new ? 'btn_save.gif' : 'btn_edit.gif';?>">
			<img id="saveBtn" src="../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif"
			onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif';"
			onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_clear_02.gif';" onClick="reset();" >
        </div>
	</form>
    </div>

    <div id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
	<script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}

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
 	 	-->
 	 </script>
</body>
</html>
