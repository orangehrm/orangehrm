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

$addBtnAction = 'add()';
$delBtnAction = 'deleteProperties()';
$saveBtnAction = 'saveList()';

$authObj = $this->popArr['authObj'];

if (isset($this->popArr['emplist'])) {
	
	$employees = $this->popArr['emplist'];
	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Admin_Company_Property; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

var employeeSearchList = new Array();
var properties=new Array();

var arrEmpName = document.getElementsByName('txtUserEmp[]'); 
var arrEmpId = document.getElementsByName('cmbUserEmpID[]');
	
<?php

if(isset($this->getArr['action']) && (count($this->popArr['allProperties'])!=0))
{
	$thisProperty = ($this->getArr['action'] == 'edit') ? $this->getArr['name'] : '';

    $i=0;
    foreach($this->popArr['allProperties'] as $property)
    {
    	if ($property['prop_name'] != $thisProperty) {
        	echo("properties[$i]=\"".stripslashes($property['prop_name'])."\";");
			$i++;
    	}
    }
}

?>

<?php
if (isset($employees) ){
 
   if ($employees) {
       $i = 0;        
       foreach ($employees as $record) {?>
          employeeSearchList[<?php echo $i++; ?>] = new Array('<?php echo implode("', '", $record); ?>');
     <?php
     }
   }

}  
?>

function add()
{
    window.location = "./CentralController.php?uniqcode=TCP&action=add&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>";
}

function save()
{
    if(validateFrom()==true)
    {
        var form = document.getElementById('propertyForm');
        form.submit();
    }
}

function saveList()
{
    var sqlState =  document.getElementById('listSqlState');
    sqlState.value = 'UpdateRecord';
      	
	for(i=0; i < arrEmpName.length; i++) {

       if (arrEmpName[i].value == '') {
           arrEmpId[i].value = '-1';
        }

		for (a in employeeSearchList) {
    		if (arrEmpName[i].value == employeeSearchList[a][0]) {     			 
    			arrEmpId[i].value = employeeSearchList[a][2];    			
    		}
    	}    	
	}

    var form = document.getElementById('propertyList');
    form.submit();
}

function validateFrom()
{


    rtn = true;

    var propName = document.getElementById('txtPropertyName');

    exist=false;

    for(i=0; i<properties.length;i++)
    {
        if (properties[i]==propName.value)
            exist=true;
    }

    if(propName.value=="")
    {
        alert("<?php echo $lang_Admin_Company_Property_Err_Name_Empty?>");
        rtn=false;
    }
    else if(exist)
    {
        alert("<?php echo $lang_Admin_Company_Property_Err_Name_Exists?>");
        rtn=false;
    }

    return rtn;
}

function doHandleAll() {
    with (document.propertyList) {
        if(elements['allCheck'].checked == false){
            doUnCheckAll();
        }
        else if(elements['allCheck'].checked == true){
            doCheckAll();
        }
    }
}

function doUnCheckAll() {
    with (document.propertyList) {
        for (var i=0; i < elements.length; i++) {
            if (elements[i].type == 'checkbox') {
                elements[i].checked = false;
            }
        }
    }
}

function deleteProperties()
{
    var oneChecked = false;
    with (document.propertyList) {
        for (var i=0; i < elements.length; i++) {
            if ((elements[i].type == 'checkbox') && (elements[i].checked == true)) {
            	if (elements[i].name == 'allCheck') {
            		continue;
            	}

                oneChecked = true;
            }
        }
    }


    if(!oneChecked)
    {
        alert("<?php echo $lang_Admin_Company_Property_Err_Del_Not_Sel?>");
    }
    else if(confirm("<?php echo $lang_Admin_Company_Property_Warn_Delete?>"))
    {
        var sqlState =  document.getElementById('listSqlState');
        sqlState.value = 'delete';

        var form = document.getElementById('propertyList');
        form.submit();
    }
}

function checkIfAllChecked() {

    with (document.propertyList) {
        for (var i=0; i < elements.length; i++) {
            if ((elements[i].type == 'checkbox') && (elements[i].checked == false) && (elements[i].name != 'allCheck')) {
                elements['allCheck'].checked = false;
                return;
            }
        }

        elements['allCheck'].checked = true;
    }

}

function doCheckAll() {
    with (document.propertyList) {
        for (var i=0; i < elements.length; i++) {
            if (elements[i].type == 'checkbox') {
                elements[i].checked = true;
            }
        }
    }
}

function goBack()
{
    window.location = "./CentralController.php?uniqcode=TCP&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>";
}

function nextPage() {
	i=document.propertyList.pageNo.value;
	i++;
	document.propertyList.pageNo.value=i;
	document.propertyList.action = "./CentralController.php?uniqcode=TCP&VIEW=MAIN&pageNo="+i;
	document.propertyList.submit();
}

function prevPage() {
	var i=document.propertyList.pageNo.value;
	i--;
	document.propertyList.pageNo.value=i;
	document.propertyList.action = "./CentralController.php?uniqcode=TCP&VIEW=MAIN&pageNo="+i;
	document.propertyList.submit();
}

function chgPage(pNo) {
	document.propertyList.pageNo.value=pNo;
	document.propertyList.action = "./CentralController.php?uniqcode=TCP&VIEW=MAIN&pageNo="+pNo;
	document.propertyList.submit();
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
<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>
</head>


<body>
<?php
if (!isset($this->getArr['action'])) {
    $properties = $this->popArr['properties'];   
?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo $lang_Admin_Company_Property_Title; ?></h2></div>
    <div class="actionbar">
        <div class="actionbuttons">
			<?php if ($authObj->isAdmin()) { ?>
            <input type="button" class="addbutton"
                onclick="<?php echo $addBtnAction; ?>;"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Add;?>" />
			<?php } ?>
            <?php if (!empty($properties)) { ?>
                <input type="button" class="savebutton"
                    onclick="<?php echo $saveBtnAction; ?>"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Save;?>" />
				<?php if ($authObj->isAdmin()) { ?>
                <input type="button" class="delbutton"
                    onclick="<?php echo $delBtnAction; ?>"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Delete;?>" />
				<?php } ?>
        <?php     }
        ?>
        </div>
        <div class="noresultsbar"><?php echo (empty($properties)) ? $lang_empview_norecorddisplay : '';?></div>
        <div class="pagingbar">
        <?php
            $commonFunc = new CommonFunctions();
            $pageStr = $commonFunc->printPageLinks($this->popArr['recordCount'], $this->popArr['pageNo'], 10);
            $pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

            echo $pageStr;
        ?>
        </div>
    <br class="clear" />
    </div>
    <br class="clear" />

<!--Property List section-->

  <form action="./CentralController.php?uniqcode=TCP&amp;id=0" method="post" name='propertyList' id = 'propertyList'>

    <input type="hidden" name="sqlState" id='listSqlState' value="delete"/>
    <input type="hidden" name="pageNo" value="<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
        <thead>
        <tr>
            <td width="50">
<?php if (!empty($properties)) { ?>
                <input type='checkbox' class='checkbox' name='allCheck' value='' onClick="doHandleAll();">
<?php } ?>
            </td>
            <td><?php echo $lang_Admin_Property_Name ; ?> </td>
            <td><?php echo $lang_Admin_Prop_Emp_Name; ?></td>
         </tr>
         </thead>
         <tbody>


            <?php

            if(sizeof($properties)!=0)
            {
            $classBg = 'odd';

			$a =0;
			
            foreach ($properties as $property) {
            ?>


            <tr>

                        <td class="<?php echo $classBg ?>" width="50"> <input type='checkbox' class='checkbox' name='chkPropId[]' value='<?php echo $property['prop_id']?>' onchange='checkIfAllChecked()' /></td>
                        <td class="<?php echo $classBg ?>" width="250"><a href="./CentralController.php?id=<?php echo $property['prop_id']?>&name=<?php echo $property['prop_name']?>&uniqcode=TCP&action=edit&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>" class="listViewTdLinkS1"><?php echo $property['prop_name']?></a></td>
                        <td class="<?php echo $classBg ?>" width="400" nowrap="nowrap">
                        <input readonly="readonly" name="propId[]" type="hidden" value='<?php echo $property['prop_id']==0?'':$property['prop_id']?>'>
                        <input type='hidden' name="cmbUserEmpID[]" value="">    
                        
                        <div class="yui-skin-sam" style="float:left;margin-right:10px;">
	           				<div id="employeeSearchAC<?php echo $a; ?>" style="width:150px;">
								<input type="text" name="txtUserEmp[]" id="txtUserEmpID<?php echo $a; ?>" style="margin:1px 0px 2px 0px;position: relative" autocomplete="off"
								value="" />
							    <div id="employeeSearchACContainer<?php echo $a; ?>" style="margin:6px 0px 0px 0px;"></div>
						  </div>
			 		  </div>
                        
                        <script type="text/javascript">                         	
                         	for (a in employeeSearchList) {
							    if (employeeSearchList[a][2] == '<?php echo $property['emp_id']; ?>') {
							    	arrEmpName[<?php echo $a; ?>].value = employeeSearchList[a][0];
                                    break
							    } else {
                                    arrEmpName[<?php echo $a; ?>].value = '<?php echo $lang_Common_TypeHereForHints ;?>';
                                    arrEmpId[<?php echo $a; ?>].value = '-1';
                                }
    						}                        			
                         		
                         </script>
                        
                        </td>
            </tr>

            <?php
            	$a++;
            	
                if ($classBg=='odd')
                    $classBg = 'even';
                else
                    $classBg = 'odd';
            }//foreach
            }//if
            ?>
        </tbody>
        </table>
  </form>
</div>
<?php
}
?>


<!--Add new property or edit section-->
<?php
if (isset($this->getArr['action'])&& ($this->getArr['action']=='add' | $this->getArr['action']=='edit')) {
    ?>
<div class="formpage">
    <div class="navigation">
		<input type="button" class="savebutton" onclick="goBack();" tabindex="11"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Back;?>" />
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Admin_Company_Property_Title?></h2></div>


<form action="./CentralController.php?capturemode=editprop&uniqcode=TCP<?php echo $this->getArr['action']=='edit'?"&id={$this->getArr['id']}":''; ?>&amp;pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>" method="post" name="propertyForm" id="propertyForm" onSubmit="return validateFrom();">

    <input type="hidden" name="sqlState" value="<?php echo $this->getArr['action']=='add'?'NewRecord':'UpdateRecord'; ?>"/>
    <input type="hidden" name="capturemode" value="<?php echo $this->getArr['action']=='edit'?'editprop':'addmode'; ?>"/>

    <label for="txtPropertyName"><?php echo $lang_Admin_Property_Name;?><span class="required">*</span></label>
    <input type="text" name="txtPropertyName" id ="txtPropertyName" class="formInputText"
        value="<?php echo $this->getArr['action']=='edit'?stripslashes($this->getArr['name']):''; ?>" size="40" maxlength="256"/>
    <br class="clear"/>
    <div class="formbuttons">
        <input type="button" class="savebutton" id="saveBtn"
            onclick="save();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Save;?>" />
        <input type="button" class="clearbutton" onclick="reset();" tabindex="3"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Reset;?>" />
    </div>

</form>
</div>
<div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
<?php
}
?>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
    
    YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
            // Instantiate second JS Array DataSource
            this.oACDS = new YAHOO.widget.DS_JSArray(employeeSearchList);
            
            <?php 
            	if (isset($properties)) {
            		for($c = 0; $c<count($properties); $c++ ) { ?>
		            // Instantiate second AutoComplete
		            this.oAutoComp = new YAHOO.widget.AutoComplete('txtUserEmpID<?php echo $c; ?>','employeeSearchACContainer<?php echo $c; ?>', this.oACDS);
		            this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
		            this.oAutoComp.typeAhead = false;
		            this.oAutoComp.useShadow = true;
		            this.oAutoComp.forceSelection = true;
		            this.oAutoComp.formatResult = function(oResultItem, sQuery) {
		            var sMarkup = oResultItem[0] + "<br />" + oResultItem[1] .fontsize(-1).fontcolor('#999999')  + "&nbsp;";
		            return (sMarkup);
		            };
            <?php }} ?>
        };
//]]>
</script>
</body>
</html>
