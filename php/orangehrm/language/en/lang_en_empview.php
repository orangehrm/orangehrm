<?
$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '-Select-' , 'Employee ID' , 'Employee Name' );


$serach    			= 'Search';
$searchby 			= 'Search By:';
$description		= 'Description';
$norecorddisplay 	= '"No Records to Display !"';
$previous 			= 'Previous';
$next				= 'Next';
$employeeid 		= 'Employee Id';
$employeename 		= 'Employee Name';

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
}
?>