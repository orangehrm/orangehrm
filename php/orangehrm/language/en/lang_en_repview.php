<?
$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '-Select-' , 'ID' , 'Description' );

$Previous = 'Previous';
$Next     = 'Next';
$dispMessage = "No Records to Display !";
$SearchBy = 'Search By:';
$description = 'Description';
$search = 'Search';

switch ($_GET['repcode']) {

		case 'EMPVIEW' :
			$headingInfo = array ('Report ID','Report Name',1,'Employee Reports','Deletion might affect Company Hierarchy');
			break;

		case 'EMPDEF' :
			$headingInfo = array ('Report ID','Report Name',0,'Employee Reports','Deletion might affect Company Hierarchy');
			break;
}
?>	