<?php
$srchlist[0] = array( -1 , 0 , 1 );
$srchlist[1] = array( '-Select-' , 'ID' , 'Name' );

$searchby = 'Search by:';
$searchfor = 'Search for:';

$Previous = 'Previous';
$Next     = 'Next';
$dispMessage = "No Records to Display !";
$SearchBy = 'Search By:';
$description = 'Description';
$search = 'Search';

$ADD_SUCCESS = 'Successfully Added';
$UPDATE_SUCCESS = 'Successfully Updated';
$DELETE_SUCCESS = 'Successfully Deleted';

$ADD_FAILURE = 'Failed to Add';
$UPDATE_FAILURE = 'Failed to Update';
$DELETE_FAILURE = 'Failed to Delete';


switch ($_GET['repcode']) {

		case 'EMPVIEW' :
			$heading = array ('Report ID','Report Name',1,'View Employee Reports','Deletion might affect Company Hierarchy');
			break;

		case 'EMPDEF' :
			$heading = array ('Report ID','Report Name',0,'Define Employee Reports','Deletion might affect viewing of reports.');
			break;
}
?>	