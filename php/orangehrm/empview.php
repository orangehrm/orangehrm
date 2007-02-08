<?php
/*
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
require_once ROOT_PATH . '/lib/models/eimadmin/CompStruct.php';

/*$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '-Select-' , 'Employee ID' , 'Employee Name' );
*/
	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];
	//$headingInfo =$this->popArr['headinginfo'];

    $currentPage = $this->popArr['currentPage'];
	$emplist= $this->popArr['emplist'];

	if (!isset($this->getArr['sortField']) || ($this->getArr['sortField'] == '')) {
		$this->getArr['sortField']=4;
		$this->getArr['sortOrder4']='ASC';
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
<?php if($this->getArr['reqcode']=='EMP') { ?>
	function returnAdd() {

		location.href = "./CentralController.php?reqcode=<?php echo $this->getArr['reqcode']?>&capturemode=addmode";

	}
	function returnDelete() {
		$check = 0;
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}

		if ( $check == 1 ){
			document.standardView.delState.value = 'DeleteMode';
			document.standardView.pageNO.value=1;
			document.standardView.submit();
		}else{
			alert("Select at least one record to delete");
		}
	}

<?php } else { ?>
	function returnAdd() {
        var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=<?php echo $this->getArr['reqcode']?>','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
	}
<?php } ?>

	function returnSearch() {
		if (document.standardView.loc_code.value == -1) {
			alert("Select the field to search!");
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

parent.scrollTo(0, 0);
</script>
<body style="padding-left:20px;" onFocus="document.getElementById('content').focus()">
<p id="content">
<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $this->getArr['sortField']; ?>&sortOrder<?php echo $this->getArr['sortField']; ?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']]?>"">
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
<tr>
	<td valign='top'>
	</td>
  <td width='100%'>
  <h2><?php echo $headingInfo[0]?></h2></td>
  <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
</tr>
</table>
</p>
</p>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3>
        <input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>">
        <input type="hidden" name="delState" value="">

        <input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>">
        <input type="hidden" name="empID" value="">
<?php	if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="returnAdd();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php 	} else { ?>
        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_add.jpg">
<?php	}
 if($this->getArr['reqcode']=='EMP') {
		if($locRights['delete']) { ?>
	        <img title="Delete" onClick="returnDelete();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
	<?php 	} else { ?>
	        <img onClick="alert('<?php echo $sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
	<?php 	}
 }
 ?>

        </h3></td>
    <td width='78%'><IMG height='1' width='1' src='../../pictures/blank.gif' alt=''>
	 </td>
  </tr>
</table>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3><?php echo $serach?></h3></td>
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
                    <tr nowrap="nowrap">
                      <td width="200" class="dataLabel" nowrap="nowrap"><slot><?php echo $searchby?></slot>&nbsp;&nbsp;<slot>
                        <select name="loc_code" id="loc_code">
<?php                        for($c=0;count($srchlist[0])>$c;$c++)
								if(isset($this->postArr['loc_code']) && $this->postArr['loc_code']==$srchlist[0][$c])
								   echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
								else
								   echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
?>
                        </select>
                      </slot></td>
                      <td width="200" class="dataLabel" noWrap><slot><?php echo $description?></slot>&nbsp;&nbsp;<slot>
                        <input type=text size="20" name="loc_name" class=dataField  value="<?php echo isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']):''?>">
                     </slot></td>
                    <td align="right" width="180" class="dataLabel"><img title="Search" onClick="returnSearch();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_search.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_search_02.jpg';" src="../../themes/beyondT/pictures/btn_search.jpg">&nbsp;&nbsp;<img title="Clear" onClick="clear_form();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" src="../../themes/beyondT/pictures/btn_clear.jpg"></td>
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
              <table border="0" cellpadding="0" cellspacing="0">
              <!-- Start paging links -->
   			  <tr>
              <!-- no results message -->
<?php 		if ((isset($emplist)) && ($emplist =='')) {
    ?>
        		<td colspan="4">
                    <font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $norecorddisplay ?></font>
                </td>
<?php
        	}
?>
			  <td colspan="2" height="40" valign="bottom" align="right">
<?php
$temp = $this->popArr['temp'];
if($temp)
    $recCount=$temp;
else
	$recCount=0;
	$noPages=(int)($recCount/$sysConst->itemsPerPage);
	if($recCount%$sysConst->itemsPerPage)
	   $noPages++;
	if ($noPages > 1) {
		if ($currentPage==1)
			echo "<font color='Gray'>$previous</font>";
		else
    		echo "<a href='#' onClick='prevPage()'>$previous</a>";
    	echo "  ";

		for ( $c = 1 ; $noPages >= $c ; $c++) {
	    	if($c == $currentPage)
				echo "<font color='Gray'>" .$c. "</font>";
			else
	    		echo "<a href='#' onClick='chgPage(" .$c. ")'>" .$c. "</a>";

	    	echo "  ";
		}

		if ($currentPage == $noPages)
			echo "<font color='Gray'>$next</font>";
		else
    		echo "<a href='#' onClick='nextPage()'>$next</a>";
		}

		for ($j=0; $j<9;$j++) {
			if (!isset($this->getArr['sortOrder'.$j])) {
				$this->getArr['sortOrder'.$j] = 'null';
			}
		}

?>
                </td>
               </tr>
              <!-- End paging links -->
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                  		<tr>
                  		  <td width="50">&nbsp;&nbsp;&nbsp;</td>
                  		  <?php $j=0; ?>
						  <td scope="col" width="250" class="listViewThS1"><a href="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $j; ?>&sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $employeeid; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle"></td>
						  <?php $j=7; ?>
						  <td scope="col" width="400" class="listViewThS1"><a href="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $j; ?>&sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $employeename; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle"></td>
						  <?php $j=6; ?>
						  <td scope="col" width="80" class="listViewThS1"><a href="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $j; ?>&sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_JobTitle; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle"></td>
						  <?php $j=8; ?>
						  <td scope="col" width="250" class="listViewThS1"><a href="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $j; ?>&sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_SubDivision; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle"></td>
						  <td scope="col" width="150" class="listViewThS1"><?php echo $lang_empview_Supervisor; ?></td>
						 </tr>
        <?php
			if ((isset($emplist)) && ($emplist !='')) {
			 $compStructObj = new CompStruct();
			 $compStructObj->buildAllWorkStations();
			 for ($j=0; $j<count($emplist);$j++) {
			 	$descField=$emplist[$j][1];
			 	$subDivision = "-";
			 	if (isset($emplist[$j][3]) && !empty($emplist[$j][3])) {
			 		$subDivision = $compStructObj->fetchHierarchString($emplist[$j][3]);
			 	}
			 	if ($sysConst->viewDescLen <= strlen($descField)) {
			 	   $descField = substr($descField,0,$sysConst->viewDescLen);
			 	   $descField .= "....";
			 	}

			 	 //print_r($emplist[$j][5]);

		?>
					<tr valign="top">
		<?php
			if(!($j%2)) {
				$cssClass = 'odd';
			} else {
			 	$cssClass = 'even';
			}
			if($_GET['reqcode']=='EMP') {
		?>
            	  <td width="50" class="<?php echo $cssClass?>"><input type="checkbox" class="checkbox" name="chkLocID[]" value="<?php echo $emplist[$j][2]?>"></td>
        <?php } else { ?>
                  <td width="50" class="<?php echo $cssClass?>"></td>
		<?php } ?>
				  <td width="250" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][0]))?$emplist[$j][0]:$emplist[$j][2]?></td>
		  		  <td width="400" class="<?php echo $cssClass?>" >
                     <a href="./CentralController.php?id=<?php echo $emplist[$j][2]?>&capturemode=updatemode&reqcode=<?php echo $this->getArr['reqcode']?>"
                         class="listViewTdLinkS1"><?php echo $descField?></a>
                  </td>
                  <td width="70" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][4]))?$emplist[$j][4]:"-"; ?></td>
                  <td width="250" class="<?php echo $cssClass?>"><?php echo $subDivision; ?></td>
                  <td width="250" class="<?php echo $cssClass?>"><?php
										if (isset($emplist[$j][5]) && is_array($emplist[$j][5])) {
											foreach ($emplist[$j][5] as $supervisorArr) {
												echo $supervisorArr[1]."<br/>";
											}
										} else {
											echo "-";
										}
                  					?>
                  </td>
			</tr>
         <?php }
        	  }
         ?>
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
<!--  newtable -->
</form>
</body>
</html>
