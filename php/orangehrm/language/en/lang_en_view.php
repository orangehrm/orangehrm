<?

$Previous = 'Previous';
$Next     = 'Next';
$dispMessage = "No Records to Display !";
$SearchBy = 'Search By:';
$description = 'Name';
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
			$headingInfo = array('Job Title ID','Job Title Name',1,'Job : Job Title','Deletion may affetct Pay Grades');
			break;
			
		case 'SKI' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Skill ID','Skill Name');
			$headingInfo = array('Skill ID','Skill Name',1,'Qualification : Skills','Deletion might affect Employee Information');
			break;		
				
		case 'LOC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name', 'City');
			$headings= array('Location ID','Location Name', 'City');
			$headingInfo = array ('Location ID','Location Name',1,'Company Info : Locations','Deletion might affect Company Hierarchy');
			break;
			
		case 'COS' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Cost CenterID','Cost Center Name');
			$headingInfo = array ('Cost CenterID','Cost Center Name',1,'Cost Centers','Deletion might affect Employee Information');
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

		case 'JDC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('JDCatergory ID','JDCatergory Name');
			$headingInfo = array ('JDCatergory ID','JDCatergory Name',1,'JD Catergory','Deletion might affect JD Type,Designation Description, Job Specifiction');
			break;

		case 'JDT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('JDType ID','JDType Name');
			$headingInfo = array ('JDType ID','JDType Name',1, 'JD Type','Deletion might affect JDCategory, Designation Description');
			break;

		case 'QLF' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Qualification Type ID','Qualification Name');
			$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
			break;

		case 'RTM' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Rating Method ID','Rating Method Name');
			$headingInfo = array ('Rating Method ID','Rating Method Name',1,'Rating Method','Deletion might affect Qualification, Languages');
			break;

		case 'CTT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Corporate TitleID','Corporate Title Name');
			$headingInfo = array ('Corporate TitleID','Corporate Title Name',1,'Corporate Title','Deletion might affect Employee Information, Designations');
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

		case 'UNI' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Uniform Type ID','Unifrorm Type Name');
			$headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
			break;

		case 'SAT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Statutory ID','Statutory Name');
			$headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
			break;

		case 'EMC' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Employee Category ID','Employee Category Name');
			$headingInfo = array ('Employee Category ID','Employee Category Name',1,'Employee Category','Deletion might affect Employee Information');
			break;

		case 'EMG' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Employee Group ID','Employee Group Name');
			$headingInfo = array ('Employee Group ID','Employee Group Name',1,'Employee Group','Deletion might affect Employee Information');
			break;

		case 'RTE' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Route ID','Route Name');
			$headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
			break;

		case 'DWT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Dwelling TypeID','Dwelling Type Name');
			$headingInfo = array ('Dwelling TypeID','Dwelling Type Name',1,'Dwelling Types','Deletion might affect Employee Information');
			break;

		case 'NAT' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Nationality ID','Nationality Name');
			$headingInfo = array ('Nationality ID','Nationality Name',1,'Nationality & Race : Nationality','Deletion might affect Employee Information');
			break;

		case 'RLG' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Religion ID','Religion Name');
			$headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
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

		case 'TAX' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Tax Info ID','Tax Name');
			$headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
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
		
		case 'BNK' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Bank ID','Bank Name',1,'Banks');
			$headingInfo = array ('Bank ID','Bank Name',1,'Banks','Deletion might affect Employee Banks, Branches');
			break;

		case 'LAN' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Language ID','Language Name');
			$headingInfo = array ('Language ID','Language Name',1,'Qualification : Languages','Deletion might affect Employee Language');
			break;

		case 'MME' :
			$srchlist = array( '-Select-' , 'ID' , 'Name', 'Type' );
			$headings= array('Membership ID','Membership Name', 'Membership Type');			
			$headingInfo = array ('Membership ID','Membership Name',1,'Memberships : Membership','Deletion might affect Employee Membership');
			break;

		case 'SSK' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Sub Skill ID','Sub Skill Name');
			$headingInfo = array ('Sub Skill ID','Sub Skill Name',1,'Sub Skill','');
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

		case 'DSG' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Designation ID','Designation Name');
			$headingInfo = array ('Designation ID','Designation Name',1,'Designations','Deletion might affect Employee Information, Corporate Titles');
			break;

		case 'DDI' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Designation ID','Designation Name');
			$headingInfo = array ('Designation ID','Designation Name',2,'Designation Description','');
			break;

		case 'DQA' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Designation ID','Designation Name');
			$headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
			break;

		case 'JDK' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('JDKRA ID','JDKRA Description');
			$headingInfo = array ('JDKRA ID','JDKRA Description',1,'JD Key Result Area','Deletion might affect Designation Description');
			break;
			
		case 'EDU' :
			$srchlist = array( '-Select-' , 'ID' , 'Name', 'Institute');
			$headings= array('Education ID','Education', 'Institute');
			$headingInfo = array ('Education ID','Education',1,'Qualification : Education','Deletion might affect Education');
			break; 

		case 'BCH' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('','');
			$headingInfo = array ('Branch ID','Branch Name',1,'Branches','Deletion might affect Employee Bank');
			break;

		case 'CCB' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Cash Benefit ID','Cash Benefit Name');
			$headingInfo = array ('Cash Benefit ID','Cash Benefit Name',1,'Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			break;

		case 'NCB' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Non Cash Benefit ID','Non Cash Benefit Name');
			$headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			break;

		case 'BBS' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Salary Grade ID','Salary Grade Name');
			$headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Cash Benefits Assigned to Salary Grade','');
			break;

		case 'NBS' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Salary Grade Non Cash BenefitID','Benefit Name');
			$headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
			break;

		case 'ETY' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Employee TypeID','Employee Type Name');
			$headingInfo = array ('Employee TypeID','Employee Type Name',1,'Employee Types','Deletion might affect Employee Information');
			break;

		case 'SBJ' :
			$srchlist = array( '-Select-' , 'ID' , 'Name' );
			$headings= array('Subject ID','Subject Name');
			$headingInfo = array ('Subject ID','Subject Name',1,'Subjects','Deletion might affect Employee Qualification, Designation Qualification');
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
		
	case 'UNI' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Uniform Type ID','Unifrorm Type Name');
		$headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
	    break;
	    
	case 'TAX' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Tax Info ID','Tax Name');
		$headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
		break;
		
	case 'SAT' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Statutory ID','Statutory Name');
		$headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
		break;
		
	case 'SGR' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Salary Grade ID','Salary Grade Name');
		$headingInfo = array ('Salary Grade ID','Salary Grade Name',1,'Job : Salary Grades','Deletion might affect Employee Information, Corporate Titles');
		break;
		
	case 'RTE' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Route ID','Route Name');
		$headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
		break;
		
	case 'RLG' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Religion ID','Religion Name');
		$headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
		break;

	case 'QLF' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Qualification Type ID','Qualification Name');
		$headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
		break;
		
	case 'NBS' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Salary Grade Non Cash BenefitID','Benefit Name');
		$headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
		break;
		
	case 'NCB' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Non Cash Benefit ID','Non Cash Benefit Name');
		$headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
		break;
		
	case 'DQA' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Designation ID','Designation Name');
		$headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
		break;
		
	
		
	case 'LIC' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('Licenses ID','Licenses Description');
		$headingInfo = array ('Licenses ID','Licenses Description',1,'Qualification : Licenses','Deletion might affect Employee Information');
		break;
		
	case 'USR' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('User ID','User Name');
		$headingInfo = array ('User ID','User Name',1, 'Users: Users','Deletion could make Orange HRM unusable');
		break;
		
	case 'USG' :
		$srchlist = array( '-Select-' , 'ID' , 'Name' );
		$headings= array('User Group ID','User Group Name');
		$headingInfo = array ('User Group ID','User Group Name',1, 'Users: User Groups','Deletion could make Orange HRM unusable');
		break;
	

}
			 

?>	