<select name="cmbStatus">
<?php 
	$values = array_values($statusArr);
	$keys	= array_key($statusArr);
	
	for ($i=0; $i < count($keys); $i++) {
?>
  	<option value="<?php echo $keys[$i]; ?>"><?php echo $values[$i]; ?></option>
<?php 
	}
?>	
</select>