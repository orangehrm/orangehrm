<style>
table {
	border-collapse: collapse;
	border: 1px solid grey;
	margin: 0;
	margin-top: 1em;
}

td, th {
	border: 1px solid grey;
	margin: 0;
	padding-left: 5px;
	padding-right: 5px;
}

.pass {
	background-color: #065a02;
	border-right: 4px solid #73B65A !important;
}

.class-pass {
  background-color: #12f447;
  border-right: 4px solid #73B65A !important;
}

.fail, .error { 
	background-color: #E75C58;
	border-right: 4px solid #E75C58 !important;
}

.class-fail {
  background-color: #fb6662;
  border-right: 4px solid #E75C58 !important;
}

.method {padding-left: 50px}

</style>

<h2>Result for: <?php echo $path ?></h2>

<?php if (!empty($tests)) : ?>
	
	<span>Time: <?php echo number_format($result->time(), 2) ?></span>
  <br />
	<span>Pass: <?php echo count($result->passed()) ?></span>
	<br />
	<?php if ($result->failureCount()) : ?>
		<span>Fail: <?php echo $result->failureCount() ?></span>
		<br />
	<?php endif; ?>
	<?php if ($result->errorCount()) : ?>
	  <span>Error: <?php echo $result->errorCount() ?></span>
	  <br />
	<?php endif; ?>
	<?php if ($result->skippedCount()) : ?>
	  <span>Skiped: <?php echo $result->skippedCount() ?></span>
	  <br />
  <?php endif; ?>	  
  <?php if ($result->notImplementedCount()) : ?>
	  <span>Incomplite: <?php echo $result->notImplementedCount() ?></span>
	  <br />
	<?php endif; ?>
	
	<table>
		<thead>
			<tr class="class-<?php echo $result->wasSuccessful() ? 'pass' : 'fail' ?>">
				<th>Method</th>
				<th>Time</th>
				<th>Status</th>
				<th>Message</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($tests as $name => $data) : ?>
				<tr>
					<td colspan="4" class="class-<?php echo $data['status'] ?>"><?php echo $name ?></td>
			  </tr>
				<?php foreach ($data['methods'] as $test) : ?>
					<tr class="<?php echo $test->status ?>">
						<td class="method">
						  <?php echo substr($test->test, strpos($test->test, '::')+2, strlen($test->test)) ?>
						</td>
						<td class="method"><?php echo number_format($test->time, 2) ?></td>
						<td class="method"><?php echo $test->status ?></td>
					  <td class="method"><?php echo $test->message ?></td>
				  </tr>
			 	<?php endforeach; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
  <span>There is nothin to test.</span>
<?php endif; ?>