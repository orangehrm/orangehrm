<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

/*$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '-Select-' , 'ID' , 'Description' );
*/
	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	//$headingInfo=$this->popArr['headinginfo'];

	$currentPage = $this->popArr['currentPage'];

	$message= $this->popArr['message'];

	if (!isset($this->getArr['sortField']) || ($this->getArr['sortField'] == '')) {
		$this->getArr['sortField']=0;
		$this->getArr['sortOrder0']='ASC';
	}

	function getNextSortOrder($curSortOrder) {
		switch ($curSortOrder) {
			case 'null' :
				return 'ASC';
				break;
			case 'ASC' :
				return 'DESC';
				break;
			case 'DESC'	:
				return 'ASC';
				break;
		}

	}

	function SortOrderInWords($SortOrder) {
		if ($SortOrder == 'ASC') {
			return 'Ascending';
		} else {
			return 'Descending';
		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<script>

	function nextPage() {
		var i=eval(document.standardView.pageNO.value);
		document.standardView.pageNO.value=i+1;
		document.standardView.submit();
	}

	function prevPage() {
		var i=eval(document.standardView.pageNO.value);
		document.standardView.pageNO.value=i-1;
		document.standardView.submit();
	}

	function chgPage(pNO) {
		document.standardView.pageNO.value=pNO;
		document.standardView.submit();
	}

	function sortAndSearch(action) {
		document.standardView.action = action;
		document.standardView.submit();
	}

	function returnAdd() {
<?php
		$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

		switch($headingInfo[2]) {
			case 1 : echo "location.href = './CentralController.php?uniqcode=".$this->getArr['uniqcode']."&capturemode=addmode".$esp."'";
					 break;
			case 2 : echo "var popup=window.open('../../genpop.php?uniqcode=".$this->getArr['uniqcode']."','Employees','modal=yes,height=450,width=600');";
        			 echo "if(!popup.opener) popup.opener=self;";
        			 break;
		}
?>
	}

	function returnDelete() {
		$check = 0;
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'chkLocID[]')){
					$check = 1;
				}
			}
		}

		if ($check == 1){

			var res = confirm("<?php echo $headingInfo[4].$lang_Common_ConfirmDelete?>");

			if(!res) return;

			document.standardView.delState.value = 'DeleteMode';
			document.standardView.pageNO.value=1;
			document.standardView.submit();
		}else{
			alert("<?php echo $lang_Common_SelectDelete; ?>");
		}
	}

	function returnSearch() {

		if (document.standardView.loc_code.value == -1) {
			alert("<?php echo $lang_Common_SelectField; ?>");
			document.standardView.loc_code.Focus();
			return;
		};
		document.standardView.captureState.value = 'SearchMode';
		document.standardView.pageNO.value=1;
		document.standardView.submit();
	}

	function doHandleAll()
	{
		with (document.standardView) {
			if(elements['allCheck'].checked == false){
				doUnCheckAll();
			}
			else if(elements['allCheck'].checked == true){
				doCheckAll();
			}
		}
	}

	function doCheckAll()
	{
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = true;
				}
			}
		}
	}

	function doUnCheckAll()
	{
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = false;
				}
			}
		}
	}

	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}
</script>
<body>
<p>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>
<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&sortField=<?php echo $this->getArr['sortField']?>&sortOrder<?php echo $this->getArr['sortField']?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']].$esp?>">
  </td>
  <td width='100%'><h2>
      <?php echo $headingInfo[3]?>
    </h2></td>
  <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td></tr>
</table></p>
</p>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3>
        <input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>">
        <input type="hidden" name="delState" value="">

        <input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>">

<?php	if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="returnAdd();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php	}

if($headingInfo[2]==1) {

	if($locRights['delete']) { ?>
        <img title="Delete" onClick="returnDelete();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<?php 	}
}?>

      </h3></td>
    <td width='78%'><IMG height='1' width='1' src='../../pictures/blank.gif' alt=''></td>
  </tr>
</table>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3><?php echo $search?></h3></td>
    <td width='78%' align="right"><IMG height='1' width='1' src='../../pictures/blank.gif' alt=''>
     <?php
		if (isset($this->getArr['message'])) {

			$expString  = $this->getArr['message'];
			$expString = explode ("_",$expString);
			$length = count($expString);

			$col_def=$expString[$length-1];

			$expString=$this->getArr['message'];
	?>
			<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
	<?php
				echo $$expString;
	?>
			</font>
	<?php
		}
		?>
      &nbsp;&nbsp;&nbsp;&nbsp;</td>
  </tr>
</table>

<!--  newtable -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200" class="dataLabel"><slot><?php echo $SearchBy?></slot>&nbsp;&nbsp;<slot>
                        <select style="z-index: 99;" name="loc_code">
<?php                        for($c=-1;count($srchlist)-1>$c;$c++)
								if(isset($this->postArr['loc_code']) && $this->postArr['loc_code']==$c)
								   echo "<option selected value='" . $c ."'>".$srchlist[$c+1] ."</option>";
								else
								   echo "<option value='" . $c ."'>".$srchlist[$c+1] ."</option>";
?>
                        </select>
                      </slot></td>
                      <td width="300" class="dataLabel" noWrap><slot><?php echo $description?></slot>&nbsp;&nbsp;<slot>
                        <input type=text size="20" name="loc_name" class=dataField  value="<?php echo isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']):''?>">
                     </slot></td>
                    <td align="right" width="180" class="dataLabel"><img tabindex=3 title="Search" onClick="returnSearch();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_search.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_search_02.jpg';" src="../../themes/beyondT/pictures/btn_search.jpg">&nbsp;&nbsp;<img title="Clear" onClick="clear_form();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td>

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
			  <table border="0" width="100%">
			  <tr>
			  <td height="40" valign="bottom" align="right">

<?php
$temp = $this->popArr['temp'];
$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;

	if ($message == '') { ?>
		<span class="error"><?php echo $dispMessage; ?></span>
<?php  } ?>
		</td>
		<td width="25"></td>
		</tr>
		</table>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
		<thead>
		<tr>
          <td class="r1_c1" width="12"></td>
          <td class="tableTopMiddle" width="50"></td>
          <?php for ($j=0; $j < count($headings); $j++) {?>
          <td width="200" class="tableTopMiddle"></td>
          <?php } ?>
          <td class="tableTopMiddle" width="300"></td>
          <td class="tableTopRight"></td>
         </tr>
		 </thead>
			<tr nowrap>
				<td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
				<td width="50" NOWRAP class="listViewThS1" scope="col">
				<?php	if($headingInfo[2]==1) { ?>
					<input type='checkbox' class='checkbox' name='allCheck' value='' onClick="doHandleAll();">
				<?php	}	?>
				</td>
				<?php
					for ($j=0; $j < count($headings); $j++) {
						if (!isset($this->getArr['sortOrder'.$j])) {
							$this->getArr['sortOrder'.$j] = 'null';
						}
				?>
				<td scope="col" width="250" class="listViewThS1"><a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&sortField=<?php echo $j?>&sortOrder<?php echo $j?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j]).$esp?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $headings[$j]?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle"></td>
				<?php } ?>
      		<td class="listViewThS1"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
      		<td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
    		</tr>
    		<?php
				if ((isset($message)) && ($message !='')) {
					for ($j=0; $j < count($message);$j++) {
	 		?>
    		<tr>
       		<td class="r2_c1"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
       		<?php
	 	 	 			if(!($j%2)) {
							$cssClass = 'odd';
			 			} else {
			 				$cssClass = 'even';
			 			}

		 	 			if($headingInfo[2]==1) {
		 		?>
       		<td class="<?php echo $cssClass?>" width="50"><input type='checkbox' class='checkbox' name='chkLocID[]' value='<?php echo $message[$j][0]?>'></td>
		 		<?php 	} else { ?>
       		<td class="<?php echo $cssClass?>" width="50"></td>
		 		<?php 	}  ?>
		 		<td class="<?php echo $cssClass?>" width="250"><a href="./CentralController.php?id=<?php echo $message[$j][0]?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode<?php echo $esp?>" class="listViewTdLinkS1"><?php echo $message[$j][0]?></a></td>
		 		<?php
		 			  $k=1;
		 			  if ($k < count($headings)) {
		 					$descField=$message[$j][$k];

		  	 				if($sysConst->viewDescLen <= strlen($descField)) {

			 	   				$descField = substr($descField,0,$sysConst->viewDescLen);
			 	   				$descField .= "....";
			 				}
		 			  }
			 	?>
		 		<td class="<?php echo $cssClass?>" width="400" ><a href="./CentralController.php?id=<?php echo $message[$j][0]?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode<?php echo $esp?>" class="listViewTdLinkS1"><?php echo $descField?></a></td>
		 		<?php
		 				for ($k=2; $k < count($headings); $k++) {

		  	 				$descField=$message[$j][$k];

		  	 				if($sysConst->viewDescLen <= strlen($descField)) {

			 	   				$descField = substr($descField,0,$sysConst->viewDescLen);
			 	   				$descField .= "....";
			 				}
		 		?>
		 		<td class="<?php echo $cssClass?>" width="400" ><?php echo $descField?></td>
		 		<?php } ?>
				<td class="<?php echo $cssClass?>" width="400" >&nbsp;</td>
		 		<td class="r2_c3"><img src="../../themes/beyondT/pictures/spacer.gif" width="13" height="1" border="0" alt=""></td>
		 	</tr>
		 	<?php
		 		 }
		 		}
		  ?>
		  <tr>
          <td class="r3_c1" height="16"></td>
          <td class="r3_c2" height="16"></td>
          <?php for ($j=0; $j < count($headings); $j++) {?>
          <td width="250" class="r3_c2" height="16"</td>
          <?php } ?>
          <td class="r3_c2" height="16"></td>
          <td class="r3_c3" height="16"></td>
         </tr>
 		</table>


<!--  newtable -->

</form>
</body>
</html>
