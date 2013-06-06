<?php
if (is_object($showDefineKpiList)) {	
	$row = 0;	
	foreach ($showDefineKpiList as $kpiList) {
		$cssClass = ($row %2) ? 'even' : 'odd';
		$row = $row + 1;						
?>	
<div class="contentBoxKpi">
	<?php echo $kpiList['desc'] ?> Rating Scale (<?php echo $kpiList['min'] ?> - <?php echo $kpiList['max'] ?>)
</div>
<br class="clear" />
<?php 
	} 
}
if ($row == 0) {
?>
<div class="contentBoxKpi">
	No KPI's defined.
</div>
<br class="clear" />
<?php 
}
?>
<input value="<?php echo $row;?>" id="row_count" name="row_count" type="hidden"/>



