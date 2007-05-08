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

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
$btnAction="addSave()";
if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$formAction="{$formAction}&id={$this->getArr['id']}&capturemode=updatemode";
	$btnAction="addUpdate()";
}
?>
<html>
<head>
<title>Customer</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php require_once ROOT_PATH . '/scripts/archive.js'; ?>
<?php require_once ROOT_PATH . '/scripts/octopus.js'; ?>
<script>

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }

    function addSave() {

        if(document.frmCustomer.txtId.value=='') {
            alert("<?php echo $lang_Admin_Customer_PleaseSpecifyTheCustormerId; ?>");
            document.frmCustomer.txtId.focus();
            return;
        }

        if (document.frmCustomer.txtName.value == '') {
            alert ("<?php echo $lang_Admin_Customer_Error_PleaseSpecifyTheName; ?>");
            return false;
        }


        document.frmCustomer.sqlState.value = "NewRecord";
        document.frmCustomer.submit();
    }

  function addUpdate() {

		if(document.frmCustomer.txtId.value=='') {
            alert("<?php echo $lang_Admin_Customer_PleaseSpecifyTheCustormerId; ?>");
            document.frmCustomer.txtId.focus();
            return;
        }

	 	if (document.frmCustomer.txtName.value == '') {
            alert ("<?php echo $lang_Admin_Customer_Error_PleaseSpecifyTheName; ?>");
            return false;
        }

		document.frmCustomer.sqlState.value  = "UpdateRecord";
		document.frmCustomer.submit();
	}

	function clearAll() {
		document.frmCustomer.txtId.value='';
		document.frmCustomer.txtName.value='';
		document.frmCustomer.txtDescription.value=''
	}

</script>

    <link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 75px;
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
        width: 250px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .version_label {
        display: block;
        float: left;
        width: 150px;
        font-weight: bold;
        margin-left: 10px;
        margin-top: 10px;
    }

    .roundbox {
        margin-top: 50px;
        margin-left: 0px;
    }

    .roundbox_content {
        padding:15px;
    }
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2>Customer</h2>
		  		</td>
	  		<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td></tr>
		</table>
	</p>
  	<div id="navigation">
  		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
	</div>
	<font color="red" face="Verdana, Arial, Helvetica, sans-serif">
    <?php
            if (isset($this->getArr['message'])) {
                $expString  = $this->getArr['message'];
                $expString = explode ("%",$expString);
                $length = sizeof($expString);
                for ($x=0; $x < $length; $x++) {
                    echo " " . $expString[$x];
                }
            }
   ?>
   </font>
  <form name="frmCustomer" method="post" action="<?php echo $formAction;?>">
        <input type="hidden" name="sqlState" value="">
        <div class="roundbox">
      <?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
            <label for="txtId"><?php echo $lang_Commn_code; ?></label>
            <input type="text" id="txtId" name="txtId" value="<?php echo $this->popArr['newID']; ?>" tabindex="1" readonly/>
            <br/>
			<label for="txtName"><span class="error">*</span> <?php echo $lang_Commn_name; ?></label>
            <input type="text" id="name" name="txtName" tabindex="2"/>
			<br/>
            <label for="txtDescription"><?php echo $lang_Commn_description; ?></label>
            <textarea name="txtDescription" id="txtDescription" rows="3" cols="30" tabindex="3"></textarea>
            <br>
       <?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {

			$message = $this->popArr['editArr'];
		?>
			<label for="txtId"><?php echo $lang_Commn_code; ?></label>
			<input type="text" id="txtId" name="txtId" value="<?php echo $message->getCustomerId(); ?>" tabindex="1" readonly/>
            <br/>
			<label for="txtName"><span class="error">*</span> <?php echo $lang_Commn_name; ?></label>
            <input type="text" id="txtName" name="txtName" value="<?php echo $message->getCustomerName(); ?>" tabindex="2"/>
			<br/>
            <label for="txtDescription"><?php echo $lang_Commn_description; ?></label>
            <textarea name="txtDescription" id="txtDescription" rows="3" cols="30" tabindex="3"><?php echo $message->getCustomerDescription(); ?></textarea><br>
			<br/>
		<?php } ?>
            <div align="center">
            <img onClick="<?php echo $btnAction; ?>;" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
			<img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="clearAll();" >


            </div>
        </div>
        <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
        </script>
    </form>
    <span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
