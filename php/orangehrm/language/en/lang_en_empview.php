<?php
$srchlist[0] = array( -1 , 0 , 1, 2, 3);
$srchlist[1] = array( '-Select-', 'Emp. ID', 'Emp. First Name', 'Emp. Last Name', 'Emp. Middle Name' );


$serach    			= 'Search';
$searchby 			= 'Search By:';
$description		= 'Description';
$norecorddisplay 	= '"No Records to Display !"';
$previous 			= 'Previous';
$next				= 'Next';
$employeeid 		= 'Employee Id';
$employeename 		= 'Employee Name';

$ADD_SUCCESS = 'Successfully Added';
$UPDATE_SUCCESS = 'Successfully Updated';
$DELETE_SUCCESS = 'Successfully Deleted';

$ADD_FAILURE = 'Failed to Add';
$UPDATE_FAILURE = 'Failed to Update';
$DELETE_FAILURE = 'Failed to Delete';

switch ($_GET['reqcode']) {
	
	case  'LAN' :
		       $headingInfo = array ('Language' ,1);
		       break;
		       
	case  'EXP' :
			   $headingInfo = array ('Work Experience' ,1);
			   break;
			   
	case  'SAL' :
			   $headingInfo = array ('Payment' ,1);
			   break;
	case  'SKI' :
			   $headingInfo = array ('Skills' ,1);
			   break;
			   
	case  'LIC' :
			  $headingInfo = array ('Licenses' ,1);
			  break;
			  
	case 'EMP' :
		   	 $headingInfo = array ('Employee Information',1);
			  break;
			  
	case  'MEM' :
			$headingInfo = array ('Memberships' ,1);
			break;
			
	case 'REP' :

		$headingInfo = array ('Report To',1);
		break;
}
?>