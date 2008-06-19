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

if(isset($this->getArr['action']) && (count($this->popArr['properties'])!=0))
{
    $i=0;
    foreach($this->popArr['properties'] as $property)
    {
        echo("properties[$i]='{$property['prop_name']}';");
        $i++;
    }
}

?>

function add()
{
    window.location = "./CentralController.php?uniqcode=TCP&action=add";
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
                oneChecked=true;
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
    window.location = "./CentralController.php?uniqcode=TCP";
}

function popEmpList()
{
    var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&USR=USR','Employees','height=450,width=400');
    if(!popup.opener) popup.opener=self;
    popup.focus();
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
    </body>
</div>
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


<table border="0" width="100%">
              <tr>

              <td height="40" valign="bottom" align="right">

        </td>
        <td width="25"></td>
        </tr>
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
                                    <input type='checkbox' class='checkbox' name='allCheck' value='' onClick="doHandleAll();">
                                </td>
                                <td scope="col" width="250" class="listViewThS1"><?php echo $lang_Admin_Property_Name ; ?> </td>
                                <td scope="col" width="250" class="listViewThS1"><?php echo $lang_Admin_Prop_Emp_Name; ?>  </td>



                    <td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
            </tr>


            <?php
            $properties = $this->popArr['properties'];

            if(sizeof($properties)!=0)
            {
            $classBg = 'odd';


            foreach ($properties as $property)
            {
            ?>


            <tr>
            <td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                        <td class="<?php echo $classBg ?>" width="50"> <input type='checkbox' class='checkbox' name='chkPropId[]' value='<?php echo $property['prop_id']?>' /></td>
                        <td class="<?php echo $classBg ?>" width="250"><a href="./CentralController.php?id=<?php echo $property['prop_id']?>&name=<?php echo $property['prop_name']?>&uniqcode=TCP&action=edit" class="listViewTdLinkS1"><?php echo $property['prop_name']?></a></td>
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
                                <img onClick="<?php echo $saveBtnAction; ?>;"
                        style="margin-top:10px;"
                        onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';"
                        onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
                        src="../../themes/beyondT/pictures/btn_save.gif">
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

<form action="./CentralController.php?capturemode=editprop&uniqcode=TCP<?php echo $this->getArr['action']=='edit'?"&id={$this->getArr['id']}":''; ?>" method="post" name="propertyForm" id="propertyForm" onSubmit="return validateFrom();">

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
