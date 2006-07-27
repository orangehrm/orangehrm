<?php
	define('ROOT_PATH', dirname($_SERVER['DOCUMENT_ROOT'].'/OpenSourceEIM'));
	$_SESSION['path']=ROOT_PATH;
	
	require_once ROOT_PATH . '/lib/datastructures/dataStructTree.php';	
	require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
	
	$dbConnection = new DMLFunctions();
	$message2 = $dbConnection -> executeQuery("SELECT geninfo_values FROM `hs_hr_geninfo` WHERE code = 001");
 	
 	$arrCompInfo = mysql_fetch_array($message2, MYSQL_NUM);
 	$txtCompInfo=explode("|", $arrCompInfo[0]);
 	
	if ( !isset($_GET['root']) ) {
		$_GET['root']=$txtCompInfo[0];
	};
	
	$locations = $this->popArr['locations'];
	
	$treeCompStruct = new dataStructTree();	
	
?>
<html>
<head>
<title>Tree Demo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<SCRIPT LANGUAGE="JavaScript">
<!--
		
	function addChild(rgtz, txt, parnt) {		
		document.frmAddNode.reset();
		document.frmAddNode.action="/TreeDemo/add.php";
		document.getElementById("rgt").value=rgtz;				
		document.getElementById("parnt").innerHTML="Add a sub-division to "+txt;
		document.getElementById("txtParnt").value=parnt;
		document.getElementById("layerForm").style.visibility="visible";
	}
	
	function edit(id, txt, desc, loc){
		var words = txt.split(" ");
		
		document.frmAddNode.reset();	
		document.frmAddNode.action="/TreeDemo/edit.php";
		document.getElementById("rgt").value=id;
		document.getElementById("parnt").innerHTML="Edit "+txt;			
		document.getElementById("cmbType").value=words[words.length-1];	
		document.getElementById("cmbLocation").value=loc;
		words.splice(words.length-1,1);
		document.getElementById("txtTitle").value=words.join(" ");	
		document.getElementById("txtDesc").value=desc;
		document.getElementById("layerForm").style.visibility="visible";
	}
	
	function frmAddHide () {
		document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerForm").style.visibility="hidden";
	}
	
	function frmEditHide () {
		document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerEditForm").style.visibility="hidden";
	}
	
	function deleteChild(lftz, rgtz, txt) {
		
		var message='Are you sure you want to delete '+txt;
		var dependants = (((rgtz - lftz + 1)/2)-1);
		
		if (dependants > 0) {
			
			message = message+". Also "+dependants+" units under "+txt+" will be deteted";
		
		};
		message = message+'. It could cause the company structure to change.';
		if (confirm(message)) {
			
			window.location="/TreeDemo/delete.php?lft="+lftz+"&rgt="+rgtz;
		
		};
	}
	
	function cmbType_Change() {
		
		if (document.getElementById("cmbType").value == '') {			
			document.getElementById("txtType").style.visibility = "visible";			
		} else {			
			document.getElementById("txtType").style.visibility = "hidden";
			document.getElementById("txtType").value=document.getElementById("cmbType").value;
			
		}
	}
-->
</SCRIPT>
<style>
	.add {
		text-decoration: none;
		color: #0000FF;
		
	}
	
	.delete {
		text-decoration: none;
		color: #FF0000;
		
	}
	.frame {
		background-color: #FFFFFF;
		visibility:hidden
	}
	
	.btnAdd {
		border: 0px;
		background: url(/TreeDemo/btn_add.jpg) no-repeat;
		width: 65px;
	}
	
	table, tr, td {
		padding-top:0px;
		padding-bottom:0px;		
		border:0;		
		
	}
	
	#line {
		background: url(/TreeDemo/line.gif) repeat-y;
		
	}
	
	#ControlButton{		
		padding-left: 4px;		
	}
	
	div {
		position:static;
	}
</style>
<body>
 <table>
  <tr>
   <td>
	<h2>Company Structure</h2>
	<table border="0" cellspacing="0" cellpadding="0" style="BORDER-COLLAPSE: collapse" bordercolor="#111111">	
	<?php
		$treeHierarchy = $treeCompStruct->displayTree($_GET['root']);
		
		$depth=(($treeHierarchy[0][0]['rgt']-$treeHierarchy[0][0]['lft']+1)/2);
		
		unset($indentor);
			
		foreach ($treeHierarchy as $child) {				
			
	?>
			<tr>
				<td valign="middle">
					<?php 
											
						if ( $child['depth'] > 0 ) {
							
							if ($child['isLast']) {
								$indentor[$child['depth']]="<image src='/TreeDemo/space.gif'>";
							} else {
								$indentor[$child['depth']]="<image src='/TreeDemo/space.gif' id='line'>";
							}
							for ($i=1; $i<$child['depth']; $i++) {
							
								echo $indentor[$i];
							
							}
							//echo str_repeat("|<image src='space.png'>",($child['depth']-1));
					
							echo "<image src='/TreeDemo/arrow.gif'>";		?>				
							<a class="add" href="#layerForm" onClick='edit(<?echo $child[0]['ID']; ?>, "<?echo $child[0]['title']; ?>", "<? echo $child[0]['Description']?>", "<? echo $child[0]['loc_code']?>")'><?echo $child[0]['title']; ?></a>
						
						<?php
						 } else {
							
							echo $child[0]['title'];
							
						 } ?>
					
					
				</td>				
				<td id="ControlButton" valign="bottom">
					<a href='#layerForm' class="add" onClick="addChild(<? echo $child[0]['rgt']; ?>, '<? echo $child[0]['title']; ?>', <? echo $child[0]['ID']; ?>, '<? echo $child[0]['loc_code']?>')"">Add</a>
				</td>
				<td id="ControlButton" valign="bottom"> 
				<? if ( $child['depth'] > 0 ) {?>
					| </td>
				<td id="ControlButton" valign="bottom">
					<a class="delete" href="#layerForm" onClick="deleteChild(<? echo $child[0]['lft']; ?>, <? echo $child[0]['rgt']; ?>, '<? echo $child[0]['title']; ?>');">Delete</a>
				<? }; ?>
				</td>
			</tr>
	<?	}	?>	
	</table>
   </td>
  </tr>
  <tr>
   <td>
    <br>
	<div id="layerForm"  class="frame">
		<h4><LABEL id="parnt"></LABEL></h4>
		<form name="frmAddNode" method="post" action="/TreeDemo/add.php">
		<input type="hidden" value="" id="rgt" name="rgt">
		<input type="hidden" value="" id="txtParnt" name="txtParnt">
		<table>
			<tr>
				<td valign="top">
					
					<LABEL id="lblSubDivision" for="txtTitle">Name</LABEL>
				</td>
				<td>
					<input type="text" value="" id="txtTitle" name="txtTitle" >
				</td>
			</tr>
			<tr>
				<td valign="top">		
					<LABEL id="lblType" for="cmbType">Type</LABEL>
				</td>
				<td>	
  					<select name="cmbType" id="cmbType" onChange="cmbType_Change()" onMouseOver="cmbType_Change()">
    					<option value="null">--SELECT--</option>
    					<option value="Division">Division</option>
    					<option value="Department">Department</option>
    					<option value="Team">Team</option>
    					<option value="">Other</option>
  					</select>&nbsp;&nbsp;
  					<input type="text" value="" id="txtType" name="txtType" style="visibility:hidden;">
  				</td>
  			</tr>
  			<tr>
				<td valign="top">		
					<LABEL id="lblLocation" for="cmbLocation">Location</LABEL>
				</td>
				
				<td>	  					
  					<select name="cmbLocation" id="cmbLocation">  
  						<option value="">--SELECT--</option>
  						<?foreach ($locations as $location) { ?>
  						<option value="<? echo $location[0]; ?>"><? echo $location[1]; ?></option>
  						<?	} ?>
    					<option value="Other">Other</option>
  					</select>
  				</td>
  			</tr>
  			<tr>
				<td valign="top">		
					<LABEL id="lblDesc" for="txtDesc">Description</LABEL>
				</td>
				<td>	  					
  					<textarea name="txtDesc" id="txtDesc"></textarea>  
  				</td>
  			</tr>
			<tr>
				<td></td>
				<td align="right">
					<input type="Submit" value="" id="Add" name="Add"class="btnAdd">
					<input type="Reset" value="Clear" id="Clear" name="Clear">
					<input type="Reset" value="Hide" id="Hide" name="Hide" onClick="frmAddHide()">					
				</td>
			</tr>
		</table>
	</form>	
	</div>
	
   </td>
  </tr>
 </table>
		
</body>
</html>
