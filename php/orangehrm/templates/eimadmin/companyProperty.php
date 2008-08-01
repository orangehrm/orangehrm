<?php
$addBtnAction = 'add()';
$delBtnAction = 'deleteProperties()';
$saveBtnAction = 'saveList()'
?>

<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="PHPEclipse 1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $lang_Admin_Company_Property; ?></title>

<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css"
 rel="stylesheet" type="text/css">
  <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
  <style type="text/css">
    <!--
    @import url("../../themes/beyondT/css/octopus.css");

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width:500px;
    }

    .roundbox_content {
        padding:15px 15px 25px 35px;
    }

    input[type=checkbox] {
        border:0px;
        background-color: transparent;
        margin: 0px;
        width: 12px;
        vertical-align: bottom;
    }
    
    .message {
        float:left;
        width:400px;
        text-align:right;
        font-family: Verdana, Arial, Helvetica, sans-serif;
    }

    -->
 </style>

<script language="JavaScript" type="text/javascript">

var properties=new Array();
<?php

if(isset($this->getArr['action']) && (count($this->popArr['allProperties'])!=0))
{
	$thisProperty = ($this->getArr['action'] == 'edit') ? $this->getArr['name'] : '';
	
    $i=0;
    foreach($this->popArr['allProperties'] as $property)
    {
    	if ($property['prop_name'] != $thisProperty) {
        	echo("properties[$i]='{$property['prop_name']}';");
			$i++;
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

function back()
{
    window.location = "./CentralController.php?uniqcode=TCP&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>";
}

function popEmpList()
{
    var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400');
    if(!popup.opener) popup.opener=self;
    popup.focus();
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

</script>


</head>


<body bgcolor="#FFFFFF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">

<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?php echo $lang_Admin_Company_Property_Title; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>
    <b><div  id="status"></div></b></td>
  </tr>
</table>
<br>

<!--Add and delete button section-->
<?php
if (!isset($this->getArr['action']))
{
    ?>
<table width="700">
<tr>
<td>
<div name="addDelButton" id="addDelButton">
    <img onClick="<?php echo $addBtnAction; ?>;"
        onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.gif';"
        onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.gif';"
        src="../../themes/beyondT/pictures/btn_add.gif">
    <img
        onClick="<?php echo $delBtnAction; ?>"
        src="../../themes/beyondT/pictures/btn_delete.gif"
        onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.gif';"
        onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';">
</div>
</td><td>
<?php
	$commonFunc = new CommonFunctions();
	$pageStr = $commonFunc->printPageLinks($this->popArr['recordCount'], $this->popArr['pageNo'], 10);
	$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

	echo $pageStr;
?>
</td>
</tr>
</table>
<?php
}
?>

<!--Property List section-->
<?php
if (!isset($this->getArr['action']))
{
?>

  <form action="./CentralController.php?uniqcode=TCP&id=0" method="post" name='propertyList' id = 'propertyList'>

    <input type="hidden" name="sqlState" id='listSqlState' value="delete"/>
    <input type="hidden" name="pageNo" value="<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>">

<table border="0" width="100%">
<?php
	$properties = $this->popArr['properties'];
    if (empty($properties)) {
?>
	<tr nowrap>
    	<td colspan="3" align="right"><?php echo $lang_empview_norecorddisplay;?>!
    	</td>
    </tr>
<?php
    }
?>

        </table>
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
        <thead>
        <tr>

          <td class="r1_c1" width="12"></td>
          <td class="tableTopMiddle" width="50"></td>
                    <td width="200" class="tableTopMiddle"></td>
                    <td width="200" class="tableTopMiddle"></td>

          <td class="tableTopRight"></td>
         </tr>
         </thead>
            <tr nowrap>
                <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                <td width="50" NOWRAP class="listViewThS1" scope="col">
<?php if (!empty($properties)) { ?>
                                    <input type='checkbox' class='checkbox' name='allCheck' value='' onClick="doHandleAll();">
<?php } ?>
                                </td>
                                <td scope="col" width="250" class="listViewThS1"><?php echo $lang_Admin_Property_Name ; ?> </td>
                                <td scope="col" width="250" class="listViewThS1"><?php echo $lang_Admin_Prop_Emp_Name; ?>  </td>



                    <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
            </tr>


            <?php

            if(sizeof($properties)!=0)
            {
            $classBg = 'odd';


            foreach ($properties as $property)
            {
            ?>


            <tr>
            <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                        <td class="<?php echo $classBg ?>" width="50"> <input type='checkbox' class='checkbox' name='chkPropId[]' value='<?php echo $property['prop_id']?>' onchange='checkIfAllChecked()' /></td>
                        <td class="<?php echo $classBg ?>" width="250"><a href="./CentralController.php?id=<?php echo $property['prop_id']?>&name=<?php echo $property['prop_name']?>&uniqcode=TCP&action=edit&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>" class="listViewTdLinkS1"><?php echo $property['prop_name']?></a></td>
                        <td class="<?php echo $classBg ?>" width="400" nowrap="nowrap">
                        <input readonly="readonly" name="propId[]" type="hidden" value='<?php echo $property['prop_id']==0?'':$property['prop_id']?>'>
                        <select name='cmbUserEmpID[]'>
                            <option <?php echo ($property['emp_id']==-1)|($property['emp_id']=='')?'selected':'' ?> value="-1"><?php echo $lang_Admin_Property_Please_Select;?></option>
                        <?php
                        if(isset($this->popArr['emplist']) && $this->popArr['emplist']!=0)
                        {
                        foreach($this->popArr['emplist'] as $emp)
                        {
                            ?>

                            <option <?php echo $emp[0]==$property['emp_id']?'selected':'' ?> value="<?php echo $emp[0];?>"><?php echo $emp[1];?></option>


                        <?php }} ?>
                        </select>
                        </td>
                        <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
            </tr>

            <?php
                if ($classBg=='odd')
                    $classBg = 'even';
                else
                    $classBg = 'odd';
            }//foreach
            }//if
            ?>

       <tr>
                   <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align='right'>
<?php       if (!empty($properties)) { ?>
                                <img onClick="<?php echo $saveBtnAction; ?>;"
                        style="margin-top:10px;"
                        onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';"
                        onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
                        src="../../themes/beyondT/pictures/btn_save.gif">
<?php } ?>
            </td>
            <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>

       </tr>
       <tr>
          <td class="r3_c1" height="16"></td>
          <td class="r3_c2" height="16"></td>
                    <td width="250" class="r3_c2" height="16"</td>
                    <td width="250" class="r3_c2" height="16"</td>
          <td class="r3_c3" height="16"></td>
         </tr>

        </table>
  </form>
<?php
}
?>


<!--Add new property or edit section-->
<?php
if (isset($this->getArr['action'])&& ($this->getArr['action']=='add' | $this->getArr['action']=='edit'))
{
    ?>
<div id="addProperty">

<form action="./CentralController.php?capturemode=editprop&uniqcode=TCP<?php echo $this->getArr['action']=='edit'?"&id={$this->getArr['id']}":''; ?>&pageNo=<?php echo (isset($this->popArr['pageNo']))?$this->popArr['pageNo']:'1' ?>" method="post" name="propertyForm" id="propertyForm" onSubmit="return validateFrom();">

  <table cellpadding='0' cellspacing='0'>

          <tr>

          <td class="r1_c1" width="12"></td>
          <td class="tableTopMiddle" width="50"></td>
                    <td class="tableTopMiddle"></td>
                    <td class="tableTopMiddle"></td>

          <td class="tableTopRight"></td>
         </tr>
    <tr>
      <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
      <td><?php echo $lang_Admin_Property_Name;?></td>
      <td width="20"></td>
      <td><input type="text" name="txtPropertyName" id ="txtPropertyName"  value="<?php echo $this->getArr['action']=='edit'?$this->getArr['name']:''; ?>" size="40" maxlength="256"/></td>
        <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
    </tr>
    <tr>
     <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
      <td>&nbsp;</td>
       <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
    </tr>
    <tr>
         <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
      <td>
        </td>

        <td>&nbsp;</td>

        <td alingnment='right'>

<img onclick="save();"
 onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';"
 onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
 src="../../themes/beyondT/pictures/btn_save.gif">

        <img title="Back"
 onmouseout="this.src='../../themes/beyondT/pictures/btn_back.gif';"
 onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"
 src="../../themes/beyondT/pictures/btn_back.gif"
 onclick="back();">
        </td>
                <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
    </tr>

       <tr>
          <td class="r3_c1" height="16"></td>
          <td class="r3_c2" height="16"></td>
                    <td class="r3_c2" height="16"</td>
                    <td class="r3_c2" height="16"</td>
          <td class="r3_c3" height="16"></td>
         </tr>

  </table>

  <input type="hidden" name="sqlState" value="<?php echo $this->getArr['action']=='add'?'NewRecord':'UpdateRecord'; ?>"/>
  <input type="hidden" name="capturemode" value="<?php echo $this->getArr['action']=='edit'?'editprop':'addmode'; ?>"/>

</form>
</div>
<?php
}
?>

</html>
