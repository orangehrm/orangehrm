<?
$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '-Select-' , 'ID' , 'Name' );

$Previous = 'Previous';
$Next     = 'Next';
$dispMessage = "No Records to Display !";
$SearchBy = 'Search By:';
$description = 'Name';
$search = 'Search';

switch ($_GET['uniqcode']) {

		case 'EST' :
			$headingInfo = array('Employment Status ID','Employment Status Name',1,'Employment Status','Deletion may affect Job Titles');
			break;

		case 'JOB' :
			$headingInfo = array('Job Title ID','Job Title Name',1,'Job Title','Deletion may affetct Pay Grades');
			break;
			
		case 'SKI' :
			$headingInfo = array('Skill ID','Skill Name',1,'Skills','Deletion might affect JDKRA');
			break;		
				
		case 'LOC' :
		
			$headingInfo = array ('Location ID','Location Name',1,'Locations','Deletion might affect Company Hierarchy');
			break;
			
		case 'COS' :

			$headingInfo = array ('Cost CenterID','Cost Center Name',1,'Cost Centers','Deletion might affect Employee Information');
			break;

		case 'CUR' :

			$headingInfo = array ('Currency Type ID','Currency Name',1,'Currency Types','Deletion might affect Salary Currency Detail');
			break;

		case 'CHI' :

			$headingInfo = array ('Company Hierarchy ID','Company Hierarchy Name',1,'Company Hierarchy','Deletion might affect Employee Information');
			break;

		case 'JDC' :

			$headingInfo = array ('JDCatergory ID','JDCatergory Name',1,'JD Catergory','Deletion might affect JD Type,Designation Description, Job Specifiction');
			break;

		case 'JDT' :

			$headingInfo = array ('JDType ID','JDType Name',1, 'JD Type','Deletion might affect JDCategory, Designation Description');
			break;

		case 'QLF' :

			$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
			break;

		case 'RTM' :

			$headingInfo = array ('Rating Method ID','Rating Method Name',1,'Rating Method','Deletion might affect Qualification, Languages');
			break;

		case 'CTT' :

			$headingInfo = array ('Corporate TitleID','Corporate Title Name',1,'Corporate Title','Deletion might affect Employee Information, Designations');
			break;

		case 'EXC' :

			$headingInfo = array ('Extra Curricular Activity Category ID','Extra Curricular Activity Category Name',1,'Extra Curricular Activity Category','Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities');
			break;

		case 'MEM' :

			$headingInfo = array ('Membership Type ID','Membership Type Name',1,'Membership Type','Deletion might affect Employee Memberships, Memberships');
			break;

		case 'UNI' :

			$headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
			break;

		case 'SAT' :

			$headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
			break;

		case 'EMC' :

			$headingInfo = array ('Employee Category ID','Employee Category Name',1,'Employee Category','Deletion might affect Employee Information');
			break;

		case 'EMG' :

			$headingInfo = array ('Employee Group ID','Employee Group Name',1,'Employee Group','Deletion might affect Employee Information');
			break;

		case 'RTE' :

			$headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
			break;

		case 'DWT' :

			$headingInfo = array ('Dwelling TypeID','Dwelling Type Name',1,'Dwelling Types','Deletion might affect Employee Information');
			break;

		case 'NAT' :

			$headingInfo = array ('Nationality ID','Nationality Name',1,'Nationality','Deletion might affect Employee Information');
			break;

		case 'RLG' :

			$headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
			break;

		case 'COU' :

			$headingInfo = array ('Country ID','Country Name',1,'Country','Deletion might affect Employee Information');
			break;

		case 'DEF' :

			$headingInfo = array ('Hierarchy Definition ID','Hierarchy Definition Name',1,'Hierarchy Definition','Deletion might affect Company Hierachy!');
			break;

		case 'TAX' :

			$headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
			break;

		case 'PRO' :

			$headingInfo = array ('State/Province ID','State/Province Name',1, 'State/Province','Deletion might affect Employee Information');
			break;

		case 'DIS' :

			$headingInfo = array ('City ID','City Name',1,'City','Deletion might affect Employee Information');
			break;


		case 'BNK' :

			$headingInfo = array ('Bank ID','Bank Name',1,'Banks','Deletion might affect Employee Banks, Branches');
			break;

		case 'LAN' :

			$headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
			break;

		case 'MME' :

			$headingInfo = array ('Membership ID','Membership Name',1,'Membership','Deletion might affect Employee Membership');
			break;

		case 'SSK' :

			$headingInfo = array ('Sub Skill ID','Sub Skill Name',1,'Sub Skill','');
			break;

		case 'EXA' :

			$headingInfo = array ('Extra Curricular Activities ID','Extra Curricular Activities Name',1,'Extra Curricular Activities','Deletion might affect Employee Ex. Curr. Activities');
			break;

		case 'SGR' :

			$headingInfo = array ('Pay Grade ID','Pay Grade Name',1,'Pay Grades','Deletion might affect Employee Information, Job Titles');
			break;

		case 'DSG' :

			$headingInfo = array ('Designation ID','Designation Name',1,'Designations','Deletion might affect Employee Information, Corporate Titles');
			break;

		case 'DDI' :

			$headingInfo = array ('Designation ID','Designation Name',2,'Designation Description','');
			break;

		case 'DQA' :

			$headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
			break;

		case 'JDK' :

			$headingInfo = array ('JDKRA ID','JDKRA Description',1,'JD Key Result Area','Deletion might affect Designation Description');
			break;
			
		case 'EDU' :

			$headingInfo = array ('Education ID','Education',1,'Education','Deletion might affect Education');
			break; 

		case 'BCH' :

			$headingInfo = array ('Branch ID','Branch Name',1,'Branches','Deletion might affect Employee Bank');
			break;

		case 'CCB' :

			$headingInfo = array ('Cash Benefit ID','Cash Benefit Name',1,'Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			break;

		case 'NCB' :

			$headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			break;

		case 'BBS' :

			$headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Cash Benefits Assigned to Salary Grade','');
			break;

		case 'NBS' :

			$headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
			break;

		case 'ETY' :

			$headingInfo = array ('Employee TypeID','Employee Type Name',1,'Employee Types','Deletion might affect Employee Information');
			break;

		case 'SBJ' :

			$headingInfo = array ('Subject ID','Subject Name',1,'Subjects','Deletion might affect Employee Qualification, Designation Qualification');
			break;
			
		case 'EEC' :

			$headingInfo = array ('EEO Job Category ID','EEO Job Category Name',1,'EEO Job Category','Deletion might affect Employee Information');
			break;
			
		
			
	case 'LAN' :
		$headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
		break;
		
	case 'ETH' :

		$headingInfo = array ('Ethnic Race ID','Ethnic Race Name',1,'Ethnic Races','Deletion might affect Employee');
		break;
		
	case 'DIS' :

		$headingInfo = array ('City ID','City Name',1,'City','Deletion might affect Employee Information');
		break;
		
	case 'UNI' :

		$headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
	    break;
	    
	case 'TAX' :

		$headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
		break;
		
	case 'SAT' :

		$headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
		break;
		
	case 'SGR' :

		$headingInfo = array ('Salary Grade ID','Salary Grade Name',1,'Salary Grades','Deletion might affect Employee Information, Corporate Titles');
		break;
		
	case 'RTE' :

		$headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
		break;
		
	case 'RLG' :

		$headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
		break;

	case 'QLF' :

		$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
		break;
		
	case 'NBS' :

		$headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
		break;
		
	case 'NCB' :

		$headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
		break;
		
	case 'DQA' :

		$headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
		break;
		
	
		
	case 'LIC' :

			$headingInfo = array ('Licenses ID','Licenses Description',1,'Licenses','Deletion might affect Employee Information');
			break;
	

}
			 

?>	