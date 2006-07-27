<html>
<head>
<title>Tree Demo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

	<form name="frmAddNode" method="post" action="add.php">
		<input type="hidden" value="" id="rgt" name="rgt">
		<LABEL id="sub_division">New Child's Name : </LABEL><input type="text" value="" id="title" name="title"><br>
		<input type="Submit" value="Add" id="Add" name="Add" onClick="alert(document.frmAddNode.rgt.value);">
	</form>
	
</body>
</html>