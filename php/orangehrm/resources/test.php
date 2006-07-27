<?php

$conn = mysql_connect('127.0.0.1', 'root', '');
mysql_select_db('moha');

$sql = 'SELECT * FROM `country`';
$message2 =mysql_query($sql);
$selCountry = '<option';
$selCountry2 = "";
echo "<select>";
while ($line = mysql_fetch_array($message2)) { 	
	echo "<option value='".$line['iso']."'>".$line['printable_name']."</option>\n";
}
echo "</select>";
?>