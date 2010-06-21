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
require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];   
   
$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
$new = true;
$disabled = '';
$groupId = '';
$groupName = '';    

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $formAction="{$formAction}&amp;id={$this->getArr['id']}&amp;capturemode=updatemode";
    $new = false;
    $disabled = "disabled='disabled'";
    $editData = $this->popArr['editArr'];
    $groupId = $editData[0][0];
    $groupName = $editData[0][1];    
}
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    var editMode = <?php echo $new ? 'true' : 'false'; ?>;

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }
      
    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';
        var errors = new Array();

        var name = trim($('txtUserGroupName').value);

        if (name == '') {
            err = true;
            msg += "\t- <?php echo $lang_Admin_Users_Errors_NameCannotBeBlank; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            return true;
        }
    }

    function reset() {
        $('frmUserGroup').reset();
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmUserGroup').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('frmUserGroup');

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }
        $('editBtn').value="<?php echo $lang_Common_Save; ?>";
        $('editBtn').title="<?php echo $lang_Common_Save; ?>";      
        $('editBtn').className = "savebutton";

<?php } else {?>
        alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
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
</head>

<body>
    <div class="formpage">
        <div class="navigation">
        	<input type="button" class="savebutton" onclick="goBack();" tabindex="11"
        	  onmouseover="moverButton(this);" onmouseout="moutButton(this);"
              value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Admin_Users_UserGroup;?></h2></div>
        
        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>
     
            <form name="frmUserGroup" id="frmUserGroup" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">                    
               <input type="hidden" value="<?php echo $token;?>" name="token" />
                <input type="hidden" name="sqlState" value="<?php echo $new ? 'NewRecord' : 'UpdateRecord'; ?>"/>  

                <?php if (!$new) { ?>
                    <label for="txtUserGroupID"><?php echo $lang_Commn_code; ?></label>
                    <input type="hidden" id="txtUserGroupID" name="txtUserGroupID" value="<?php echo $groupId;?>"/>
                    <span class="formValue"><?php echo $groupId;?></span><br class="clear"/>
                <?php } ?>
                          
                <label for="txtUserGroupName"><?php echo $lang_Commn_name; ?><span class="required">*</span></label>
                <input type="text" id="txtUserGroupName" name="txtUserGroupName" tabindex="1" class="formInputText"
                    value="<?php echo $groupName; ?>" <?php echo $disabled;?> />
                <br class="clear"/>
                                                
                <div class="formbuttons">
<?php if($locRights['edit']) { ?>                
                    <input type="button" class="<?php echo $new ? 'savebutton': 'editbutton';?>" id="editBtn" 
                        onclick="edit();" tabindex="2" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                        value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="reset();" tabindex="3"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
                         value="<?php echo $lang_Common_Reset;?>" />
                <?php if (!$new) {                    
                         $rightsLabel = $lang_Admin_Users_Assign_User_Rights;
                         if ($_SESSION['userGroup'] == $groupId) {
                            $rightsLabel = $lang_Admin_Users_View_User_Rights;
                         }
                    ?>
                    
                <a href="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=UGR"><?php echo $rightsLabel; ?></a>
                <?php } ?>    
                         
<?php } ?>                         
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');                
            }
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
