<?
 $employeeinformation			= 'Employee Information';
 $code							= 'Code';
 $employeeid		  			= 'Employee ID';
 $lastname			  			= 'Last Name';
 $firstname						= 'First Name';
 $middlename					= 'Middle Name';
 $nickname						= 'Nick Name';
 $photo      					= 'Photo';
 
 $ssnno			  				= 'SSN No :';
 $nationality					= 'Nationality';
 $sinno							= 'SIN No :';
 $dateofbirth					= 'Date of Birth';
 $otherid      					= 'Other ID';
 $maritalstatus              	= 'Marital Status';
 $selectnatio					= '--Select Nationality--';
 $selmarital					= '--Select--';
 $smoker                 		= 'Smoker';
 $gender 						= 'Gender';
 $dlicenno						= 'Driver\'s License Number';
 $licexpdate					= 'License Expiry Date';
 $militaryservice				= 'Military Service';
 $ethnicrace      				= 'Ethnic Race';
 $selethnicrace              	= '--Select Ethnic Race--';
 
 
 $jobtitle			  			= 'Job Title';
 $empstatus						= 'Employment Status';
 $selempstat					= '--Select Empl. Status--';
 $eeocategory					= 'EEO Category';
 $seleeocat      				= '--Select EEO Cat--';
 $location              		= 'Location';
 $selectlocation				= '--Select Location--';
 $joindate						= 'Joined Date';
 
 $dependents			  		= 'Dependents';
 $children						= 'Children';
 $name			  				= 'Name';
 $relationship					= 'Relationship';
 $hmtele						= 'Home Telephone';
 $mobile						= 'Mobile';
 $worktele      				= 'Work Telephone';
 
 
 $country             			= 'Country';
 $selectcountry					= '--Select Country--';
 $street1						= 'Street 1';
 $state							= 'State';
 $selstate                 		= '--Select State--';
 $street2 						= 'Street 2';
 $city							= 'City/Town';
 $selcity						= '--Select--';
 $zipcode						= 'ZIP Code';
 $workemail      				= 'Work Email';
 $otheremail              		= 'Other Email';
 
 $passport             			= ' Passport ';
 $visa							= ' Visa ';
 $citizenship					= 'Citizenship';
 $passvisano					= 'Passport/Visa No';
 $issueddate                 	= 'Issued Date';
 $i9status 						= 'I9 Status';
 $dateofexp						= 'Date of Expiry';
 $i9reviewdate					= 'I9 Review Date';
 $comments						= 'Comments';
 
 $path      					= 'Path';
 $description              		= 'Description';
 $filename      				= 'File Name';
 $size              			= 'Size';
 $type      					= 'Type';

 //language
$employeelanguageflu 	= 'Employee Language Fluency';
 $employeeid		  	= 'Employee ID';
 $lastname			  	= 'Last Name';
 $firstname				= 'First Name';
 $middlename			= 'Middle Name';
 $language				= 'Language';
 $fluency				= 'Area Of Competence';
 $ratinggarde			= 'Fluency';
 $assignlanguage		= 'Assigned Languages';
 
 $lantype = array ( 'Writing'=> 1 , 'Speaking'=>2 , 'Reading'=>3 );
 $grdcodes = array( 'Poor'=> 1 ,'Basic'=>2 , 'Good'=>3 ,'Mother Tongue'=>4);

 //license
 $employeelicen			= 'Employee Licenses';
 $employeeid		  	= 'Employee ID';
 $lastname			  	= 'Last Name';
 $firstname				= 'First Name';
 $middlename			= 'Middle Name';
 $assignlicenses		= 'Assigned Licenses';
 $startdate				= 'Date';
 $enddate 				= 'Renewal Date';
 $licentype				= 'License Type';
 $assignlicen			= 'Assigned Licenses';

 //memberships
 $subown = array ( 'Company' , 'Individual');
 $employeemembership 	= 'Employee Memberships';
 $employeeid 			= 'Employee ID';
 $lastname			  	= 'Last Name';
 $firstname				= 'First Name';
 $middlename			= 'Middle Name';
 $membershiptype 		= 'Membership Type';
 $membership 			= 'Membership';
 $subownership 			= 'Subscription Ownership';
 $subamount 			= 'Subscription Amount';
 $subcomdate 			= 'Subscription Commence Date';
 $subredate 			= 'Subscription Renwal Date';
 $selmemtype 			= '-Select M\'ship Type-';
 $selmemship 			= '-----Select M\'ship----';
 $selownership 			= '-Select Ownership-';
 $assignmemship 		= 'Assigned Memberships';

 //payment
 $paygrade 				= 'Pay Grade';
 $currency				= 'Currency';
 $minpoint				= 'Min Point';
 $maxpoint				= 'Max Point';
 $bassalary				= 'Basic Salary';
 $assignedsalary		= 'Assigned Salary';
 
 //report-to
 $heading				= 'Employee Report';
 $employeeid				= 'Employee ID';
 $lastname				= 'Last Name';
 $firstname	    		= 'First Name';
 $nickname    			= 'Nick Name';
 $supervisorsubordinator= 'Supervisor / Subordinator';
 $reportingmethod 	 	= 'Reporting Method';
 $supervisorinfomation  = 'Supervisor Infomation';
 $employeename  		= 'Employee Name';
 $subordinateinfomation = 'Subordinate Infomation';

 $selectreporttype		= '-Select Report Type-';
 $selecttype			= '------Select Type------';
 $arrRepType = array ('Supervisor','Subordinate');
 $arrRepMethod = array ('Direct' => 1,'Indirect' => 2);

 //skills
 $employeeskill			= 'Employee Skills';
 $employeeid		  	= 'Employee ID';
 $lastname			  	= 'Last Name';
 $firstname				= 'First Name';
 $middlename			= 'Middle Name';
 $yearofex      		= 'Years of Experience';
 $comments              = 'Comments';
 $assignskill			= 'Assigned Skills';
 $skill                 = 'Skill';
 $assignskills 			= 'Assigned Skills';

 //work-experiance
 $employerworkex		= 'Employee Work Experience';
 $employeeid		  	= 'Employee ID';
 $lastname			  	= 'Last Name';
 $firstname				= 'First Name';
 $middlename			= 'Middle Name';
 $employer				= 'Employer';
 $enddate				= 'End Date';
 $jobtitle				= 'Job title';
 $startdate				= 'Start Date';
 $briefdes				= 'Comments';
 $assignworkex			= 'Assigned Work Experiance';
 $workexid				= 'Work Experiance ID';

?>