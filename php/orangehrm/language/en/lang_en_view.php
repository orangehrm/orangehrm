<?php

$Previous = 'Previous';
$Next     = 'Next';
$dispMessage = "No Records to Display !";
$SearchBy = 'Search By:';
$description = 'Search For:';
$search = 'Search';
$ADD_SUCCESS = 'Successfully Added';
$UPDATE_SUCCESS = 'Successfully Updated';
$DELETE_SUCCESS = 'Successfully Deleted';

$ADD_FAILURE = 'Failed to Add';
$UPDATE_FAILURE = 'Failed to Update';
$DELETE_FAILURE = 'Failed to Delete';

switch ($_GET['uniqcode']) {

		case 'EST' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Employment Status ID','Employment Status Name');
			$headingInfo = array('Employment Status ID','Employment Status Name',1,'Job : Employment Status','Deletion may affect Job Titles');	
			break;

		case 'JOB' :
			$srchlist = array( '-Select-' , 'ID' , 'Name');
			$headings= array('Job Title ID','Job Title Name');
			$headingInfo = array('Job Title ID','Job Title Name',1,'Job : Job Title','Deletion may affect Pay Grade of Employees in PIM');
			break;
			
		case 'SKI' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Skill ID','Skill Name');
			$headingInfo = array('Skill ID','Skill Name',1,'Skills : Skills','Deletion might affect Employee Information');
			break;		
				
		case 'LOC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name', 'City');
			$headings= array('Location ID','Location Name', 'City');
			$headingInfo = array ('Location ID','Location Name',1,'Company Info : Locations','Deletion might affect Company Hierarchy. If the Location has associations deletion may fail');
			break;
			
		case 'CUR' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Currency Type ID','Currency Name');
			$headingInfo = array ('Currency Type ID','Currency Name',1,'Currency Types','Deletion might affect Salary Currency Detail');
			break;

		case 'CHI' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Company Hierarchy ID','Company Hierarchy Name');
			$headingInfo = array ('Company Hierarchy ID','Company Hierarchy Name',1,'Company Hierarchy','Deletion might affect Employee Information');
			break;

		case 'QLF' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Qualification Type ID','Qualification Name');
			$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
			break;

		case 'EXC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Extra Curricular Activity Category ID','Extra Curricular Activity Category Name');
			$headingInfo = array ('Extra Curricular Activity Category ID','Extra Curricular Activity Category Name',1,'Extra Curricular Activity Category','Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities');
			break;

		case 'MEM' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Membership Type ID','Membership Type Name');					
			$headingInfo = array ('Membership Type ID','Membership Type Name',1,'Memberships : Membership Type','Deletion might affect Employee Memberships, Memberships');
			break;

	
		case 'NAT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Nationality ID','Nationality Name');
			$headingInfo = array ('Nationality ID','Nationality Name',1,'Nationality & Race : Nationality','Deletion might affect Employee Information');
			break;

		case 'COU' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Country ID','Country Name');
			$headingInfo = array ('Country ID','Country Name',1,'Country','Deletion might affect Employee Information');
			break;

		case 'DEF' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Hierarchy Definition ID','Hierarchy Definition Name');
			$headingInfo = array ('Hierarchy Definition ID','Hierarchy Definition Name',1,'Hierarchy Definition','Deletion might affect Company Hierachy!');
			break;

		case 'PRO' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('State/Province ID','State/Province Name');
			$headingInfo = array ('State/Province ID','State/Province Name',1, 'State/Province','Deletion might affect Employee Information');
			break;

		case 'DIS' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('City ID','City Name');
			$headingInfo = array ('City ID','City Name',1,'City','Deletion might affect Employee Information');
			break;
		
		case 'LAN' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Language ID','Language Name');
			$headingInfo = array ('Language ID','Language Name',1,'Skills : Languages','Deletion might affect Employee Language');
			break;

		case 'MME' :
			$srchlist = array( '-Select-' , 'ID' , 'Name', 'Type' );
			$headings= array('Membership ID','Membership Name', 'Membership Type');			
			$headingInfo = array ('Membership ID','Membership Name',1,'Memberships : Membership','Deletion might affect Employee Membership');
			break;

		case 'EXA' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Extra Curricular Activities ID','Extra Curricular Activities Name');
			$headingInfo = array ('Extra Curricular Activities ID','Extra Curricular Activities Name',1,'Extra Curricular Activities','Deletion might affect Employee Ex. Curr. Activities');
			break;

		case 'SGR' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Pay Grade ID','Pay Grade Name');
			$headingInfo = array ('Pay Grade ID','Pay Grade Name',1,'Job : Pay Grades','Deletion might affect Employee Information, Job Titles');
			break;
		
		case 'EDU' :
			$srchlist = array( '-Select-' , 'ID' , 'Course', 'Institute');
			$headings= array('Education ID','Course', 'Institute');
			$headingInfo = array ('Education ID','Education',1,'Qualification : Education','Deletion might affect Education');
			break; 

		case 'ETY' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Employee TypeID','Employee Type Name');
			$headingInfo = array ('Employee TypeID','Employee Type Name',1,'Employee Types','Deletion might affect Employee Information');
			break;

		case 'EEC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('EEO Job Category ID','EEO Job Category Name');
			$headingInfo = array ('EEO Job Category ID','EEO Job Category Name',1,'Job : EEO Job Category','Deletion might affect Employee Information');
			break;
						
	case 'LAN' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Language ID','Language Name');
		$headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
		break;
		
	case 'ETH' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Ethnic Race ID','Ethnic Race Name');
		$headingInfo = array ('Ethnic Race ID','Ethnic Race Name',1,'Nationality & Race :Ethnic Races','Deletion might affect Employee');
		break;
		
	case 'DIS' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('City ID','City Name');
		$headingInfo = array ('City ID','City Name',1,'City','Deletion might affect Employee Information');
		break;
		
	case 'SGR' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Salary Grade ID','Salary Grade Name');
		$headingInfo = array ('Salary Grade ID','Salary Grade Name',1,'Job : Salary Grades','Deletion might affect Employee Information, Corporate Titles');
		break;
		
		
	case 'QLF' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Qualification Type ID','Qualification Name');
		$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
		break;
		
	
	case 'LIC' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('License ID','License Description');
		$headingInfo = array ('License ID','License Description',1,'Qualification : License','Deletion might affect Employee Information');
		break;
		
	case 'USR' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('User ID','User Name');
		
		$esp = (isset($_GET['isAdmin']) && ($_GET['isAdmin'] == 'Yes'))? 'HR Admin' : 'ESS';
		
		$headingInfo = array ('User ID','User Name',1, 'Users: '.$esp.' Users','Deletion could make Orange HRM unusable');
		break;
		
	case 'USG' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('User Group ID','User Group Name');
		$headingInfo = array ('User Group ID','User Group Name',1, 'Users: User Groups','Deletion could make Orange HRM unusable');
		break;
	

}
			 

?>	