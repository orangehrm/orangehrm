<html>
<body>
<h1>Adding ohrm_leave_leave_entitlement data</h1>

<?php

//get the parameters required to access the database

//$dbhost = $_POST['dbhost'];
//$dbusername = $_POST['dbusername'];
//$dbpassword = $_POST['dbpassword'];
//$dbname = $_POST['dbname'];

$dbhost = 'localhost';
$dbusername = 'root';
$dbpassword = 'emma';
$dbname = 'ohrm30';

$con = mysql_connect($dbhost, $dbusername, $dbpassword);
//eg. $con = mysql_connect("localhost", "root", "password");


if($con){

	echo "Successfully connected to ".$dbhost."!<br/>";

	//eg. $db = mysql_select_db("hr_mysqlbcc", $con);
	$db = mysql_select_db($dbname, $con);
	
	if($db){

		echo "Successfully connected to database: ".$dbname."!<br/>";



		//read the ohrm_leave records
		$get_record_count_query = "SELECT `id` FROM `ohrm_leave`"; 
		$record_count_result = mysql_query($get_record_count_query);


		$record_count = mysql_num_rows($record_count_result);

		//$record_count = 20; //test a sample - first 20 records
		
		//to identify current record
		$count = 0;
		
		echo "Record count: ".$record_count;

		while($count < $record_count){
		
			//get the current record
			$get_curr_rec_sql = "SELECT * FROM `ohrm_leave` LIMIT ".$count.", 1";

			//get values of the current record to varaibles
			$get_curr_rec_result = mysql_query($get_curr_rec_sql);	
			while($get_curr_rec_row = mysql_fetch_assoc($get_curr_rec_result))
			{

				$status = $get_curr_rec_row['status'];
				$new_entitlement_id = $get_curr_rec_row['new_entitlement_id'];
				$length_days = $get_curr_rec_row['length_days'];
				$leave_id = $get_curr_rec_row['id'];
				$emp_number = $get_curr_rec_row['emp_number'];
				$leave_type_id = $get_curr_rec_row['leave_type_id'];
				$date = $get_curr_rec_row['date'];

				if (($status != 4) && ($status != 5)) {
					$get_leave_sum_sql = "SELECT SUM(`length_days`) FROM `ohrm_leave` WHERE `entitlement_id` = ".$new_entitlement_id ;

					$get_leave_sum_result = mysql_query($get_leave_sum_sql);	
					while($get_leave_sum_row = mysql_fetch_assoc($get_leave_sum_result))
					{
						$leave_sum = $get_leave_sum_row['SUM(length_days)'];
					}					
					
					if ($leave_sum == NULL) {
						$leave_sum = 0;
					}

					$get_curr_bal_sql = "SELECT `no_of_days` - `days_used` FROM `ohrm_leave_entitlement` WHERE `id` = ".$new_entitlement_id ;

					$get_curr_bal_result = mysql_query($get_curr_bal_sql);	
					while($get_curr_bal_row = mysql_fetch_assoc($get_curr_bal_result))
					{
						$curr_bal = $get_curr_bal_row['no_of_days - days_used'];
					}					


					//if no matching leave quota is there, you need to add to the ohrm_leave_entitlement, & take the leave entitlement id
					if ($new_entitlement_id == 0) {

                                                //$insert_ohrm_leave_entitlement_sql = "INSERT INTO `ohrm_leave_entitlement` (emp_number, no_of_days, leave_type_id, from_date, to_date, credited_date, note, entitlement_type, `deleted`) VALUES (" . $emp_number . ", 0.00, ". $leave_type_id . ", CONCAT(YEAR(" . $date . "), '-01-01'), CONCAT(YEAR(" . $date . "), '-12-31'), CONCAT(YEAR(" . $date . "), '-01-01'), 'added by the script', 1, 0) ;";
						$from_date = date('Y', strtotime($date)) . "-01-01";
                                                $to_date = date('Y', strtotime($date)) . "-12-31";

                                                $insert_ohrm_leave_entitlement_sql = "INSERT INTO `ohrm_leave_entitlement` (emp_number, no_of_days, leave_type_id, from_date, to_date, credited_date, note, entitlement_type, `deleted`) VALUES (" . $emp_number . ", 0.00, ". $leave_type_id . ", '" . $from_date . "', '" . $to_date . "', '" . $from_date . "', 'added by the script', 1, 0) ;";
                                                
						$added1 = mysql_query($insert_ohrm_leave_entitlement_sql);

						if($added1){
							echo ">>>Added ohrm_leave_entitlement <br/>";

							$get_new_entitlement_id_sql = "SELECT LAST_INSERT_ID()";
                                                        $get_new_entitlement_id_result = mysql_query($get_new_entitlement_id_sql);	
                                                        
                                                        while($get_new_entitlement_id_row = mysql_fetch_assoc($get_new_entitlement_id_result))
                                                        {
                                                            $new_entitlement_id = $get_new_entitlement_id_row['LAST_INSERT_ID()'];
                                                        }					
                                                        
						}
						else{
							die("Could not add: ".$new_entitlement_id."!\nError: ".mysql_error());
						}


					}



					if ($leave_sum <= $curr_bal) {
						//insert length_days
						$insert_leave_leave_entitlement_sql = "INSERT INTO `ohrm_leave_leave_entitlement` (`leave_id`, `entitlement_id`, `length_days`) VALUES (".$leave_id.", ".$new_entitlement_id.", ".$length_days.");";
					}
					elseif ($curr_bal > 0) {
						//insert curr_bal
						$insert_leave_leave_entitlement_sql = "INSERT INTO `ohrm_leave_leave_entitlement` (`leave_id`, `entitlement_id`, `length_days`) VALUES (".$leave_id.", ".$new_entitlement_id.", ".$curr_bal.");";
					}

					if ($insert_leave_leave_entitlement_sql != NULL) {



						$added = mysql_query($insert_leave_leave_entitlement_sql);

						if($added){
							echo ">>>Added <br/>";
						}
						else{
							die("Could not add: ".$new_entitlement_id."!\nError: ".mysql_error());
						}
						
					}
					else
					{
						echo "Nothing happens";
					}
					$insert_leave_leave_entitlement_sql = NULL;

				}
			}

			$count++;
		}
		
                
                //drop unwanted column
                $drop_new_entitlement_id_sql = "alter table `ohrm_leave` drop column new_entitlement_id;";
                $dropped = mysql_query($drop_new_entitlement_id_sql);

		if($dropped){
			echo ">>> Dropped unwanted column<br/>";
		}
		else{
			die("Could not drop!\nError: ".mysql_error());
		}

                //set days_used
                $set_days_used_sql = "UPDATE `ohrm_leave_entitlement` le SET le.`days_used` = (SELECT SUM(l.`length_days`) FROM `ohrm_leave` l WHERE l.`emp_number` = le.`emp_number` AND l.`leave_type_id` = le.leave_type_id AND l.date BETWEEN le.from_date AND le.to_date);";
                $saved = mysql_query($set_days_used_sql);

		if($saved){
			echo ">>> Saved days_used<br/>";
		}
		else{
			die("Could not Save!\nError: ".mysql_error());
		}


		//display success message
		echo "<h2>Leave Leave Entitlements (" . $count . ") are added!!!</h2>";
		
                
                //updating terminated employees - specific to internal system due to some issues related to old upgrades
                $get_to_be_terminated_employees_sql = "SELECT emp_number FROM hs_hr_employee WHERE termination_id IS NULL AND emp_status = 1;";
                $get_to_be_terminated_employees_result = mysql_query($get_to_be_terminated_employees_sql);	
		
                while($get_to_be_terminated_employees_row = mysql_fetch_assoc($get_to_be_terminated_employees_result))
		{
                    $empNum = $get_to_be_terminated_employees_row['emp_number'];
                    
                    $add_missing_termination_sql = "INSERT INTO ohrm_emp_termination (`emp_number`, `reason_id`, `termination_date`) VALUES (" . $empNum . ", 1, date(now()));";

                    $savedTerm = mysql_query($add_missing_termination_sql);

                    if($savedTerm){
                            echo ">>> Saved missing emp termination <br/>";
                            
                            $update_sql = "UPDATE hs_hr_employee e SET e.termination_id=(SELECT t.id FROM ohrm_emp_termination t WHERE t.emp_number = " . $empNum . " AND t.termination_date = date(now())) WHERE e.emp_number = " . $empNum . ";";
                    
                            $updatedTerm = mysql_query($update_sql);

                            if($updatedTerm){
                                    echo ">>> Updated missing emp termination <br/>";

                            }
                            else{
                                    die("Could not Update!\nError: ".mysql_error());
                            }

                    }
                    else{
                            die("Could not Save!\nError: ".mysql_error());
                    }

		}					

	}

	else{

		//display error message, if can't connect to db
		echo "Could not connect to database!\nError: ".mysql_error()."<br/><br/>";
		?><!--<a href='DBDetailsForm.php'>Back</a>--><?php
	}

}

else{

	//display error message, if can't connect to db host
	echo "Could not connect to database host!\nError: ".mysql_error()."<br/><br/>";
	?><!--<a href='DBDetailsForm.php'>Back</a>--><?php
}

?>
</body>
</html>

