<?
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

require_once OpenSourceEIM . '/lib/Models/companyinfo/Location.php';
require_once OpenSourceEIM . '/lib/Models/companyinfo/CostCenter.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CurrencyTypes.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/Designations.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/SalaryGrades.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CompHier.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/JDKra.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CorpTit.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/Qualifications.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/Branches.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CashBen.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/NonCashBen.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CashBenSal.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/NonCashBenSal.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/EmployeeTypes.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/JDCategory.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/JDType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/QualificationType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/RatingTypes.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/Skills.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/ExtraCurrActType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/MembershipType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/UniformType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/SatutoryInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/EmployeeCat.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/EmployeeGroup.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/RouteInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DwellingType.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/NationalityInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/ReligionInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/CountryInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DesDis.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DesQual.php';
require_once OpenSourceEIM . '/lib/Models/companyinfo/HierarchyDefInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/TaxInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/ProvinceInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DistrictInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/ElectorateInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/BankInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/LanguageInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/MembershipInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/SubSkillInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/ExtraCurActInfo.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/SubjectInfo.php';


require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_BankInfo.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_CashBen.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_CostCenter.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_CountryInfo.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_CurrencyType.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_DwellingType.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_ElectorateInfo.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_EmployeeCat.php';
require_once OpenSourceEIM . '/lib/interface/eimadmin/INTERFACE_EmployeeGroup.php';

class ViewController {

	var $indexCode;
	var $message;
	var $pageID;
	var $headingInfo;
	
		
	function ViewController() {
		if(!isset($_SESSION)) {
			header("Location: ./relogin.htm");
			exit();
		}
	}
	
    function delParser($indexCode,$arrList) {
        $this->indexCode=$indexCode;

        switch ($this->indexCode)  {
        	
        	case 'LOC' :
	
	            $this->location = new Location();
	            $this->location->delLocation($arrList);
	            break;

			case 'COS' :

				$this-> costcenter = new CostCenter();
				$this-> costcenter -> delCostCenters($arrList);
				break;

			case 'CUR' :

				$this-> currencytype = new CurrencyTypes();
				$this-> currencytype -> delCurrencyTypes($arrList);
				break;

			case 'CHI' :

				$this-> comphier = new CompHierachy();
				$this-> comphier -> delCompHierachy($arrList);
				break;

			case  'CTT' :

				$this-> corptit = new CorpTitle();
				$this-> corptit -> delCorpTitles($arrList);
				break;

			case 'JDC' :

				$this-> jdcategory = new JDCategory();
				$this-> jdcategory -> delJDCategorys($arrList);
				break;

			case 'JDT' :

				$this-> jdtype = new JDType();
				$this-> jdtype -> delJDTypes($arrList);
				break;

			case 'QLF' :

				$this-> qualtype = new QualificationType();
				$this-> qualtype -> delQualificationTypes($arrList);
				break;
			
			case 'RTM' :

				$this-> ratingmethods = new RatingTypes();
				$this-> ratingmethods -> delRatingTypes($arrList);
				break;
			
			case 'SKI' :

				$this-> skills = new Skills();
				$this-> skills -> delSkills($arrList);
				break;

			case 'EXC' :

				$this-> extracurract = new ExtraCurrActType();
				$this-> extracurract -> delCurrActType($arrList);
				break;

			case 'MEM' :

				$this-> membershiptype = new MembershipType();
				$this-> membershiptype -> delMembershipType($arrList);
				break;

			case 'UNI' :

				$this-> uniformtypes = new UniformType();
				$this-> uniformtypes -> delUniformType($arrList);
				break;

			case 'SAT' :

				$this-> satutoryinfo = new SatutoryInfo();
				$this-> satutoryinfo -> delSatutoryInfo($arrList);
				break;

			case 'EMC' :

			$this-> employeecat = new EmployeeCat();
			$this-> employeecat -> delEmployeeCat($arrList);
			break;

		case 'EMG' :

			$this-> employeegroup = new EmployeeGroup();
			$this-> employeegroup -> delEmployeeGroup($arrList);
			break;

		case 'RTE' :

			$this-> routeinformation = new RouteInfo();
			$this-> routeinformation -> delRouteInfo($arrList);
			break;

		case 'DWT' :
		
			$this-> routeinformation = new DwellingType();
			$this-> routeinformation -> delDwellingType($arrList);
			break;

		case 'NAT' :

			$this-> nationalityinfo = new NationalityInfo();
			$this-> nationalityinfo -> delNationalityInfo($arrList);
			break;

		case 'RLG' :

			$this-> religioninfo = new ReligionInfo();
			$this-> religioninfo -> delReligionInfo($arrList);
			break;

		case 'COU' :

			$this-> countryinfo = new CountryInfo();
			$this-> countryinfo -> delCountryInfo($arrList);
			break;

		case 'DEF' :

			$this-> hierachinfo = new HierarchyDefInfo();
			$this-> hierachinfo -> delHierarchyDefInfo($arrList);
			break;

		case 'TAX' :

			$this-> taxinfo = new TaxInfo();
			$this-> taxinfo -> delTaxInfo($arrList);
			break;
			
		case 'PRO' :

			$this-> provinceinfo = new ProvinceInfo();
			$this-> provinceinfo -> delProvinceInfo($arrList);
			break;

		case 'DIS' :

			$this-> districtinfo = new DistrictInfo();
			$this-> districtinfo -> delDistrictInfo($arrList);
			break;

		case 'ELE' :

			$this-> electorateinfo = new ElectorateInfo();
			$this-> electorateinfo -> delElectorateInfo($arrList);
			break;

		case 'BNK':

			$this-> bankinfo = new BankInfo();
			$this-> bankinfo -> delBankInfo($arrList);
			break;

		case 'LAN':

			$this-> languageinfo = new LanguageInfo();
			$this-> languageinfo -> delLanguageInfo($arrList);
			break;

		case 'MME':

			$this-> membershipinformation = new MembershipInfo();
			$this-> membershipinformation -> delMembershipInfo($arrList);
			break;

		case 'SSK':

			$this-> subskillinformation = new SubSkillInfo();
			$this-> subskillinformation -> delSubSkillInfo($arrList);
			break;

		case 'EXA':

			$this-> extracurractinfo = new ExtraCurActInfo();
			$this-> extracurractinfo -> delExtraCurActInfo($arrList);
			break;

		case 'SGR':

			$this-> salarygrade = new SalaryGrades();
			$this-> salarygrade -> delSalaryGrades($arrList);
			break;

		case 'DSG':

			$this-> designation = new Designations();
			$this-> designation -> delDesignations($arrList);
			break;

		case 'DDI':

			$this-> desigdes = new DesDescription();
			$this-> desigdes -> delJDKPI($arrList);
			break;

		case 'DQA':

			$this-> desigqual = new DesQualification();
			$this-> desigqual -> delJDQual($arrList);
			break;

    	case 'JDK':

			$this-> jdkra = new JDKra();
			$this-> jdkra -> delJDKra($arrList);
			break;

    	case 'QQL':

			$this-> qual = new Qualifications();
			$this-> qual -> delQualifications($arrList);
			break;

    	case 'BCH':

			$this-> brch = new Branches();
			$this-> brch -> delBranches($arrList);
			break;

    	case 'CCB':

			$this-> cashben = new CashBen();
			$this-> cashben -> delCashBenefits($arrList);
			break;

    	case 'NCB':

			$this-> noncashben = new NonCashBen();
			$this-> noncashben -> delNonCashBenefits($arrList);
			break;

    	case 'BBS':

			$this-> cashbensal = new CashBenSalary();
			$this-> cashbensal -> delCashBenefits($arrList);
			break;

    	case 'NBS':

			$this-> noncashbensal = new NonCashBenSalary();
			$this-> noncashbensal -> delCashBenefits($arrList);
			break;

    	case 'ETY':

			$this-> emptyp = new EmployeeType();
			$this-> emptyp -> delEmployeeTypes($arrList);
			break;

		case 'SBJ':

			$this-> subjectinfo = new SubjectInfo();
			$this-> subjectinfo -> delSubjectInfo($arrList);
			break;
		}
    }

	function selectIndexId($pageNO,$schStr,$mode) {
		
		switch ($this->indexCode) {
					
		case 'LOC' :
		
			$this-> location = new Location();
			$message = $this-> location -> getListofLocations($pageNO,$schStr,$mode);
			return $message;
			
		case 'COS' :
			
			$this-> costcenter = new CostCenter();
			$message = $this-> costcenter -> getListofCostCenters($pageNO,$schStr,$mode);
			return $message;
			
		case 'CUR' :
			
			$this-> currencytype = new CurrencyTypes();
			$message = $this-> currencytype -> getListofCurrencyTypes($pageNO,$schStr,$mode);
			return $message;

		case 'CHI' :

			$this-> comphier = new CompHierachy();
			$message = $this-> comphier -> getListofCompHierachy($pageNO,$schStr,$mode);
			return $message;

		case 'CTT' :

			$this-> corptit = new CorpTitle();
			$message = $this-> corptit -> getListofCorpTitles($pageNO,$schStr,$mode);
			return $message;

		case 'JDC' :
			
			$this-> jdcategory = new JDCategory();
			$message = $this-> jdcategory -> getListofJDCategorys($pageNO,$schStr,$mode);
			return $message;
			
		case 'JDT' :
			
			$this-> jdtype = new JDType();
			$message = $this-> jdtype -> getListofJDTypes($pageNO,$schStr,$mode);
			return $message;

			
		case 'QLF' :
			
			$this-> qualtype = new QualificationType();
			$message = $this-> qualtype -> getListofQualificationTypes($pageNO,$schStr,$mode);
			return $message;
			
		case 'RTM' :
			
			$this-> ratingmethods = new RatingTypes();
			$message = $this-> ratingmethods -> getListofRatingTypes($pageNO,$schStr,$mode);
			return $message;
			
		case 'SKI' :
			
			$this-> skills = new Skills();
			$message = $this-> skills -> getListofSkills($pageNO,$schStr,$mode);
			return $message;
			
		case 'EXC' :
			
			$this-> extracurract = new ExtraCurrActType();
			$message = $this-> extracurract -> getListofExtraCurrActType($pageNO,$schStr,$mode);
			return $message;
			
		case 'MEM' :
			
			$this-> membershiptype = new MembershipType();
			$message = $this-> membershiptype -> getListofMembershipType($pageNO,$schStr,$mode);
			return $message;
			
		case 'UNI' :
			
			$this-> uniformtypes = new UniformType();
			$message = $this-> uniformtypes -> getListofUniformType($pageNO,$schStr,$mode);
			return $message;
			
		case 'SAT' :
			
			$this-> satutoryinfo = new SatutoryInfo();
			$message = $this-> satutoryinfo -> getListofSatutoryInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'EMC' :
			
			$this-> employeecat = new EmployeeCat();
			$message = $this-> employeecat -> getListofEmployeeCat($pageNO,$schStr,$mode);
			return $message;
			
		case 'EMG' :
			
			$this-> employeegroup = new EmployeeGroup();
			$message = $this-> employeegroup -> getListofEmployeeGroup($pageNO,$schStr,$mode);
			return $message;
			
		case 'RTE' :
			
			$this-> routeinformation = new RouteInfo();
			$message = $this-> routeinformation -> getListofRouteInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'DWT' :
			
			$this-> routeinformation = new DwellingType();
			$message = $this-> routeinformation -> getListofDwellingType($pageNO,$schStr,$mode);
			return $message;
			
		case 'NAT' :
			
			$this-> nationalityinfo = new NationalityInfo();
			$message = $this-> nationalityinfo -> getListofNationalityInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'RLG' :
			
			$this-> religioninfo = new ReligionInfo();
			$message = $this-> religioninfo -> getListofReligionInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'COU' :
			
			$this-> countryinfo = new CountryInfo();
			$message = $this-> countryinfo -> getListofCountryInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'DEF' :
			
			$this-> hierachinfo = new HierarchyDefInfo();
			$message = $this-> hierachinfo -> getListofHierarchyDefInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'TAX' :
			
			$this-> taxinfo = new TaxInfo();
			$message = $this-> taxinfo -> getListofTaxInfo($pageNO,$schStr,$mode);
			return $message;
			
		case 'PRO' :
			
			$this-> provinceinfo = new ProvinceInfo();
			$message = $this-> provinceinfo -> getListofProvinceInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'DIS' :
			
			$this-> districtinfo = new DistrictInfo();
			$message = $this-> districtinfo -> getListofDistrictInfo($pageNO,$schStr,$mode);
			
			return $message;
			 
		case 'ELE' :
			
			$this-> electorateinfo = new ElectorateInfo();
			$message = $this-> electorateinfo -> getListofElectorateInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'BNK' :
			
			$this-> bankinfo = new BankInfo();
			$message = $this-> bankinfo -> getListofBankInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'LAN' :
			
			$this-> languageinfo = new LanguageInfo();
			$message = $this-> languageinfo -> getListofLanguageInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'MME' :
			
			$this-> membershipinformation = new MembershipInfo();
			$message = $this-> membershipinformation -> getListofMembershipInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'SSK' :
			
			$this-> subskillinformation = new SubSkillInfo();
			$message = $this-> subskillinformation -> getListofSubSkillInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		case 'EXA' :
			
			$this-> extracurractinfo = new ExtraCurActInfo();
			$message = $this-> extracurractinfo -> getListofExtraCurActInfo($pageNO,$schStr,$mode);
			
			return $message;

		case 'SGR' :

			$this-> salarygrade = new SalaryGrades();
			$message = $this-> salarygrade -> getListofSalaryGrades($pageNO,$schStr,$mode);

			return $message;

		case 'DSG' :

			$this-> designation = new Designations();
			$message = $this-> designation -> getListofDesignations($pageNO,$schStr,$mode);

			return $message;

		case 'DDI' :

			$this-> designation = new DesDescription();
			$message = $this-> designation -> getListofDesignations($pageNO,$schStr,$mode);

			return $message;

		case 'DQA' :

			$this-> designation = new DesQualification();
			$message = $this-> designation -> getListofDesignations($pageNO,$schStr,$mode);

			return $message;

    	case 'JDK' :

			$this-> jdkra = new JDKra();
			$message = $this-> jdkra -> getListofJDKra($pageNO,$schStr,$mode);

			return $message;

    	case 'QQL' :

			$this-> qual = new Qualifications();
			$message = $this-> qual -> getListofQualifications($pageNO,$schStr,$mode);

			return $message;

    	case 'BCH' :

			$this-> brch = new Branches();
			$message = $this-> brch -> getListofBranches($pageNO,$schStr,$mode);

			return $message;

    	case 'CCB' :

			$this-> cashben = new CashBen();
			$message = $this-> cashben -> getListofCashBenefits($pageNO,$schStr,$mode);

			return $message;

    	case 'NCB' :

			$this-> noncashben = new NonCashBen();
			$message = $this-> noncashben -> getListofNonCashBenefits($pageNO,$schStr,$mode);

			return $message;

    	case 'BBS' :

			$this-> cashbensal = new CashBenSalary();
			$message = $this-> cashbensal -> getListofCashBenefits($pageNO,$schStr,$mode);

			return $message;

    	case 'NBS' :

			$this-> noncashbensal = new NonCashBenSalary();
			$message = $this-> noncashbensal -> getListofCashBenefits($pageNO,$schStr,$mode);

			return $message;

    	case 'ETY' :

			$this-> emptyp = new EmployeeType();
			$message = $this-> emptyp -> getListofEmployeeTypes($pageNO,$schStr,$mode);

			return $message;

		case 'SBJ' :
			
			$this-> subjectinfo = new SubjectInfo();
			$message = $this-> subjectinfo -> getListofSubjectInfo($pageNO,$schStr,$mode);
			
			return $message;
			
		}
	}
	
	
	function getPageID($indexCode) {
	
		$this->indexCode = $indexCode;	
							
		switch ($this->indexCode) {
		
			case 'LOC' :
		
			$this->pageID = './locations';
			return $this->pageID;
			
			case 'COS' :
				
				$this->pageID = './costcenters';
				return $this->pageID;
				
			case 'CUR' :
				
				$this->pageID = './currencytypes';
				return $this->pageID;
	
			case 'CHI' :
	
				$this->pageID = './comphier';
				return $this->pageID;
	
			case 'JDC' :
				
				$this->pageID = './jdcategory';
				return $this->pageID;
	
			case 'JDT' :
				
				$this->pageID = './jdtypes';
				return $this->pageID;
				
			case 'QLF' :
				
				$this->pageID = './qualificationtypes';
				return $this->pageID;
				
			case 'RTM' :
				
				$this->pageID = './ratingmethods';
				return $this->pageID;
				
			case 'SKI' :
				
				$this->pageID = './skills';
				return $this->pageID;
				
			case 'EXC' :
				
				$this->pageID = './extracurractivity';
				return $this->pageID;
				
			case 'MEM' :
				
				$this->pageID = './membershiptypes';
				return $this->pageID;
				
			case 'UNI' :
				
				$this->pageID = './uniformtypes';
				return $this->pageID;
				
			case 'SAT' :
				
				$this->pageID = './satutoryinfo';
				return $this->pageID;
				
			case 'CTT' :
	
				$this->pageID = './corptit';
				return $this->pageID;
	
			case 'EMC' :
				
				$this->pageID = './empcatinfo';
				return $this->pageID;
				
			case 'EMG' :
				
				$this->pageID = './employeegroups';
				return $this->pageID;
				
			case 'RTE' :
				
				$this->pageID = './routeinformation';
				return $this->pageID;
				
			case 'DWT' :
				
				$this->pageID = './dwellinginformation';
				return $this->pageID;
				
			case 'NAT' :
				
				$this->pageID = './nationalityinformation';
				return $this->pageID;
				
			case 'RLG' :
				
				$this->pageID = './religioninformation';
				return $this->pageID;
				
			case 'COU' :
				
				$this->pageID = './countryinformation';
				return $this->pageID;
				
			case 'DEF' :
				
				$this->pageID = './hierarchydef';
				return $this->pageID;
				
			case 'TAX' :
				
				$this->pageID = './taxinformation';
				return $this->pageID;
				
			case 'PRO' :
						
				$this->pageID = './provinceinformation';
				return $this->pageID;
				
			case 'DIS' :
						
				$this->pageID = './districtinformation';
				return $this->pageID;
				
			case 'ELE' :
						
				$this->pageID = './electorateinformation';
				return $this->pageID;
				
			case 'BNK' :
						
				$this->pageID = './bankinformation';
				return $this->pageID;
				
			case 'LAN' :
						
				$this->pageID = './languageinformation';
				return $this->pageID;
				
			case 'MME' :
						
				$this->pageID = './membershipinformation';
				return $this->pageID;
				
			case 'SSK' :
						
				$this->pageID = './subskillinformation';
				return $this->pageID;
				
			case 'EXA' :
						
				$this->pageID = './extracurractinfo';
				return $this->pageID;
	
			case 'SGR' :
	
				$this->pageID = './salarygrades';
				return $this->pageID;
	
			case 'DSG' :
	
				$this->pageID = './designations';
				return $this->pageID;
	
			case 'DDI' :
	
				$this->pageID = './desdis';
				return $this->pageID;
	
			case 'DQA' :
	
				$this->pageID = './desqua';
				return $this->pageID;
	
			case 'JDK' :
	
				$this->pageID = './jdkra';
				return $this->pageID;
	
			case 'QQL' :
	
				$this->pageID = './qualifications';
				return $this->pageID;
	
			case 'BCH' :
	
				$this->pageID = './branches';
				return $this->pageID;
	
			case 'CCB' :
	
				$this->pageID = './cashben';
				return $this->pageID;
	
			case 'NCB' :
	
				$this->pageID = './noncashben';
				return $this->pageID;
	
			case 'BBS' :
	
				$this->pageID = './cashbensal';
				return $this->pageID;
	
			case 'NBS' :
	
				$this->pageID = './noncashbensal';
				return $this->pageID;
	
			case 'ETY' :
	
				$this->pageID = './emptypes';
				return $this->pageID;
	
			case 'SBJ' :
						
				$this->pageID = './subjectinformation';
				return $this->pageID;
			
		}
	} 
	
	
	function getHeadingInfo($indexCode) {
		
		$this->indexCode = $indexCode;
		
		switch ($this->indexCode) {
		
		case 'LOC' :
		
			$this->headingInfo = array ('Location ID','Location Name',1,'Locations','Deletion might affect Company Hierarchy');
			return $this->headingInfo;
			
		case 'COS' :

			$this->headingInfo = array ('Cost CenterID','Cost Center Name',1,'Cost Centers','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'CUR' :

			$this->headingInfo = array ('Currency Type ID','Currency Name',1,'Currency Types','Deletion might affect Salary Currency Detail');
			return $this->headingInfo;

		case 'CHI' :

			$this->headingInfo = array ('Company Hierarchy ID','Company Hierarchy Name',1,'Company Hierarchy','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'JDC' :

			$this->headingInfo = array ('JDCatergory ID','JDCatergory Name',1,'JD Catergory','Deletion might affect JD Type,Designation Description, Job Specifiction');
			return $this->headingInfo;

		case 'JDT' :

			$this->headingInfo = array ('JDType ID','JDType Name',1, 'JD Type','Deletion might affect JDCategory, Designation Description');
			return $this->headingInfo;

		case 'QLF' :

			$this->headingInfo = array ('Qualification Type ID','Qualification Name',1,'Qualification Type','Deletion might affect Qualifications, Designation Qualification, Employee Qualification');
			return $this->headingInfo;

		case 'RTM' :

			$this->headingInfo = array ('Rating Method ID','Rating Method Name',1,'Rating Method','Deletion might affect Qualification, Languages');
			return $this->headingInfo;

		case 'CTT' :

			$this->headingInfo = array ('Corporate TitleID','Corporate Title Name',1,'Corporate Title','Deletion might affect Employee Information, Designations');
			return $this->headingInfo;

		case 'SKI' :

			$this->headingInfo = array ('Skill ID','Skill Name',1,'Skills','Deletion might affect JDKRA, Sub Skills');
			return $this->headingInfo;

		case 'EXC' :

			$this->headingInfo = array ('Extra Curricular Activity Category ID','Extra Curricular Activity Category Name',1,'Extra Curricular Activity Category','Deletion might affect Ex. Curr. Activities, Employee Ex. Curr. Activities');
			return $this->headingInfo;

		case 'MEM' :

			$this->headingInfo = array ('Membership Type ID','Membership Type Name',1,'Membership Type','Deletion might affect Employee Memberships, Memberships');
			return $this->headingInfo;

		case 'UNI' :

			$this->headingInfo = array ('Uniform Type ID','Unifrorm Type Name',1,'Uniform Type','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'SAT' :

			$this->headingInfo = array ('Statutory ID','Statutory Name',1,'Statutory Status','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'EMC' :

			$this->headingInfo = array ('Employee Category ID','Employee Category Name',1,'Employee Category','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'EMG' :

			$this->headingInfo = array ('Employee Group ID','Employee Group Name',1,'Employee Group','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'RTE' :

			$this->headingInfo = array ('Route ID','Route Name',1,'Routes','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DWT' :

			$this->headingInfo = array ('Dwelling TypeID','Dwelling Type Name',1,'Dwelling Types','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'NAT' :

			$this->headingInfo = array ('Nationality ID','Nationality Name',1,'Nationality','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'RLG' :

			$this->headingInfo = array ('Religion ID','Religion Name',1,'Religions','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'COU' :

			$this->headingInfo = array ('Country ID','Country Name',1,'Country','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DEF' :

			$this->headingInfo = array ('Hierarchy Definitiion ID','Hierarchy Definition Name',1,'Hierarchy Definition','Deletion might affect Company Hierachy!');
			return $this->headingInfo;

		case 'TAX' :

			$this->headingInfo = array ('Tax Info ID','Tax Name',1,'Tax','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'PRO' :

			$this->headingInfo = array ('State ID','State Name',1, 'State','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'DIS' :

			$this->headingInfo = array ('County ID','County Name',1,'County','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'ELE' :

			$this->headingInfo = array ('Electorate ID','Electorate Name',1,'Electorate','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'BNK' :

			$this->headingInfo = array ('Bank ID','Bank Name',1,'Banks','Deletion might affect Employee Banks, Branches');
			return $this->headingInfo;

		case 'LAN' :

			$this->headingInfo = array ('Language ID','Language Name',1,'Languages','Deletion might affect Employee Language');
			return $this->headingInfo;

		case 'MME' :

			$this->headingInfo = array ('Membership ID','Membership Name',1,'Membership','Deletion might affect Employee Membership');
			return $this->headingInfo;

		case 'SSK' :

			$this->headingInfo = array ('Sub Skill ID','Sub Skill Name',1,'Sub Skill','');
			return $this->headingInfo;

		case 'EXA' :

			$this->headingInfo = array ('Extra Curricular Activities ID','Extra Curricular Activities Name',1,'Extra Curricular Activities','Deletion might affect Employee Ex. Curr. Activities');
			return $this->headingInfo;

		case 'SGR' :

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',1,'Salary Grades','Deletion might affect Employee Information, Corporate Titles');
			return $this->headingInfo;

		case 'DSG' :

			$this->headingInfo = array ('Designation ID','Designation Name',1,'Designations','Deletion might affect Employee Information, Corporate Titles');
			return $this->headingInfo;

		case 'DDI' :

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Description','');
			return $this->headingInfo;

		case 'DQA' :

			$this->headingInfo = array ('Designation ID','Designation Name',2,'Designation Qualification','');
			return $this->headingInfo;

		case 'JDK' :

			$this->headingInfo = array ('JDKRA ID','JDKRA Description',1,'JD Key Result Area','Deletion might affect Designation Description');
			return $this->headingInfo;

		case 'QQL' :

			$this->headingInfo = array ('Qualification ID','Qualification Name',1,'Qualifications','Deletion might affect Designation Qualification, Employee Qualification, Subjects');
			return $this->headingInfo;

		case 'BCH' :

			$this->headingInfo = array ('Branch ID','Branch Name',1,'Branches','Deletion might affect Employee Bank');
			return $this->headingInfo;

		case 'CCB' :

			$this->headingInfo = array ('Cash Benefit ID','Cash Benefit Name',1,'Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			return $this->headingInfo;

		case 'NCB' :

			$this->headingInfo = array ('Non Cash Benefit ID','Non Cash Benefit Name',1,'Non Cash Benefits','Deletion might affect Employee Benefits, Benefits Assigned to Salary Grades');
			return $this->headingInfo;

		case 'BBS' :

			$this->headingInfo = array ('Salary Grade ID','Salary Grade Name',2,'Cash Benefits Assigned to Salary Grade','');
			return $this->headingInfo;

		case 'NBS' :

			$this->headingInfo = array ('Salary Grade Non Cash BenefitID','Benefit Name',2,'Non Cash Benefits Assigned to Salary Grade','');
			return $this->headingInfo;

		case 'ETY' :

			$this->headingInfo = array ('Employee TypeID','Employee Type Name',1,'Employee Type Names','Deletion might affect Employee Information');
			return $this->headingInfo;

		case 'SBJ' :

			$this->headingInfo = array ('Subject ID','Subject Name',1,'Subjects','Deletion might affect Employee Qualification, Designation Qualification');
			return $this->headingInfo;
        }

	}
	
	
	
	function getInfo($indexCode,$pageNO,$schStr='',$mode=0) {
	
		$this->indexCode = $indexCode;
		return $this->selectIndexId($pageNO,$schStr,$mode);
	
	}
	
	
	function getPageName($indexCode) {
		
		$this->indexCode = $indexCode;
		return $this->getPageID();
		
	}
	
	function countList($index,$schStr='',$mode=0) {
		
		$this->indexCode=$index;
					
		switch ($this->indexCode) {

			case 'LOC' :
		
			$this-> location = new Location();
			$message = $this-> location -> countLocations($schStr,$mode);
			return $message;
			
		case 'COS' :
			
			$this-> costcenter = new CostCenter();
			$message = $this-> costcenter -> countCostCenters($schStr,$mode);
			return $message;
			
		case 'CUR' :
			
			$this-> currencytype = new CurrencyTypes();
			$message = $this-> currencytype -> countCurrencyTypes($schStr,$mode);
			return $message;

		case 'CHI' :

			$this-> comphier = new CompHierachy();
			$message = $this-> comphier -> countCompHierachy($schStr,$mode);
			return $message;

		case 'CTT' :

			$this-> corptit = new CorpTitle();
			$message = $this-> corptit -> countCorpTitles($schStr,$mode);
			return $message;

		case 'JDC' :
			
			$this-> jdcategory = new JDCategory();
			$message = $this-> jdcategory -> countJDCategorys($schStr,$mode);
			return $message;
			
		case 'JDT' :
			
			$this-> jdtype = new JDType();
			$message = $this-> jdtype -> countJDTypes($schStr,$mode);
			return $message;

			
		case 'QLF' :
			
			$this-> qualtype = new QualificationType();
			$message = $this-> qualtype -> countQualificationTypes($schStr,$mode);
			return $message;
			
		case 'RTM' :
			
			$this-> ratingmethods = new RatingTypes();
			$message = $this-> ratingmethods -> countRatingTypes($schStr,$mode);
			return $message;
			
		case 'SKI' :
			
			$this-> skills = new Skills();
			$message = $this-> skills -> countSkills($schStr,$mode);
			return $message;
			
		case 'EXC' :
			
			$this-> extracurract = new ExtraCurrActType();
			$message = $this-> extracurract -> countExtraCurrActType($schStr,$mode);
			return $message;
			
		case 'MEM' :
			
			$this-> membershiptype = new MembershipType();
			$message = $this-> membershiptype -> countMembershipType($schStr,$mode);
			return $message;
			
		case 'UNI' :
			
			$this-> uniformtypes = new UniformType();
			$message = $this-> uniformtypes -> countUniformType($schStr,$mode);
			return $message;
			
		case 'SAT' :
			
			$this-> satutoryinfo = new SatutoryInfo();
			$message = $this-> satutoryinfo -> countSatutoryInfo($schStr,$mode);
			return $message;
			
		case 'EMC' :
			
			$this-> employeecat = new EmployeeCat();
			$message = $this-> employeecat -> countEmployeeCat($schStr,$mode);
			return $message;
			
		case 'EMG' :
			
			$this-> employeegroup = new EmployeeGroup();
			$message = $this-> employeegroup -> countEmployeeGroup($schStr,$mode);
			return $message;
			
		case 'RTE' :
			
			$this-> routeinformation = new RouteInfo();
			$message = $this-> routeinformation -> countRouteInfo($schStr,$mode);
			return $message;
			
		case 'DWT' :
			
			$this-> routeinformation = new DwellingType();
			$message = $this-> routeinformation -> countDwellingType($schStr,$mode);
			return $message;
			
		case 'NAT' :
			
			$this-> nationalityinfo = new NationalityInfo();
			$message = $this-> nationalityinfo -> countNationalityInfo($schStr,$mode);
			return $message;
			
		case 'RLG' :
			
			$this-> religioninfo = new ReligionInfo();
			$message = $this-> religioninfo -> countReligionInfo($schStr,$mode);
			return $message;
			
		case 'COU' :
			
			$this-> countryinfo = new CountryInfo();
			$message = $this-> countryinfo -> countCountryInfo($schStr,$mode);
			return $message;
			
		case 'DEF' :
			
			$this-> hierachinfo = new HierarchyDefInfo();
			$message = $this-> hierachinfo -> countHierarchyDefInfo($schStr,$mode);
			return $message;
			
		case 'TAX' :
			
			$this-> taxinfo = new TaxInfo();
			$message = $this-> taxinfo -> countTaxInfo($schStr,$mode);
			return $message;
			
		case 'PRO' :
			
			$this-> provinceinfo = new ProvinceInfo();
			$message = $this-> provinceinfo -> countProvinceInfo($schStr,$mode);
			
			return $message;
			
		case 'DIS' :
			
			$this-> districtinfo = new DistrictInfo();
			$message = $this-> districtinfo -> countDistrictInfo($schStr,$mode);
			
			return $message;
			 
		case 'ELE' :
			
			$this-> electorateinfo = new ElectorateInfo();
			$message = $this-> electorateinfo -> countElectorateInfo($schStr,$mode);
			
			return $message;
			
		case 'BNK' :
			
			$this-> bankinfo = new BankInfo();
			$message = $this-> bankinfo -> countBankInfo($schStr,$mode);
			
			return $message;
			
		case 'LAN' :
			
			$this-> languageinfo = new LanguageInfo();
			$message = $this-> languageinfo -> countLanguageInfo($schStr,$mode);
			
			return $message;
			
		case 'MME' :
			
			$this-> membershipinformation = new MembershipInfo();
			$message = $this-> membershipinformation -> countMembershipInfo($schStr,$mode);
			
			return $message;
			
		case 'SSK' :
			
			$this-> subskillinformation = new SubSkillInfo();
			$message = $this-> subskillinformation -> countSubSkillInfo($schStr,$mode);
			
			return $message;
			
		case 'EXA' :
			
			$this-> extracurractinfo = new ExtraCurActInfo();
			$message = $this-> extracurractinfo -> countExtraCurActInfo($schStr,$mode);
			
			return $message;

		case 'SGR' :

			$this-> salarygrade = new SalaryGrades();
			$message = $this-> salarygrade -> countSalaryGrades($schStr,$mode);

			return $message;

		case 'DSG' :

			$this-> designation = new Designations();
			$message = $this-> designation -> countDesignations($schStr,$mode);

			return $message;

		case 'DDI' :

			$this-> designation = new DesDescription();
			$message = $this-> designation -> countDesignations($schStr,$mode);

			return $message;

		case 'DQA' :

			$this-> designation = new DesQualification();
			$message = $this-> designation -> countDesignations($schStr,$mode);

			return $message;

    	case 'JDK' :

			$this-> jdkra = new JDKra();
			$message = $this-> jdkra -> countJDKra($schStr,$mode);

			return $message;

    	case 'QQL' :

			$this-> qual = new Qualifications();
			$message = $this-> qual -> countQualifications($schStr,$mode);

			return $message;

    	case 'BCH' :

			$this-> brch = new Branches();
			$message = $this-> brch -> countBranches($schStr,$mode);

			return $message;

    	case 'CCB' :

			$this-> cashben = new CashBen();
			$message = $this-> cashben -> countCashBenefits($schStr,$mode);

			return $message;

    	case 'NCB' :

			$this-> noncashben = new NonCashBen();
			$message = $this-> noncashben -> countNonCashBenefits($schStr,$mode);

			return $message;

    	case 'BBS' :

			$this-> cashbensal = new CashBenSalary();
			$message = $this-> cashbensal -> countCashBenefits($schStr,$mode);

			return $message;

    	case 'NBS' :

			$this-> noncashbensal = new NonCashBenSalary();
			$message = $this-> noncashbensal -> countCashBenefits($schStr,$mode);

			return $message;

    	case 'ETY' :

			$this-> emptyp = new EmployeeType();
			$message = $this-> emptyp -> countEmployeeTypes($schStr,$mode);

			return $message;

		case 'SBJ' :
			
			$this-> subjectinfo = new SubjectInfo();
			$message = $this-> subjectinfo -> countSubjectInfo($schStr,$mode);
			
			return $message;
			
		}
	}

	function parsePostData($index,$postArr) {
		$this->indexCode=$index;
		
		switch ($this->indexCode) {
			
			case 'BNK':

				$interface_bank_info = new INTERFACE_BankInfo();
				$interface_bank_info->parseData($postArr);
				break;

			case 'CCB':

				$interface_cash_ben = new INTERFACE_CashBen();
				$interface_cash_ben->parseData($postArr);
				break;

			case 'COS':

				$interface_cost_center = new INTERFACE_CostCenter();
				$interface_cost_center->parseData($postArr);
				break;

			case 'COU':

				$interface_country_info = new INTERFACE_CountryInfo();
				$interface_country_info->parseData($postArr);
				break;

			case 'CUR':

				$interface_currency_type = new INTERFACE_CurrencyType();
				$interface_currency_type->parseData($postArr);
				break;

			case 'DWT':

				$interface_dwelling_type = new INTERFACE_DwellingType();
				$interface_dwelling_type->parseData($postArr);
				break;

			case 'ELE':

				$interface_electorate_info = new INTERFACE_ElectorateInfo();
				$interface_electorate_info->parseData($postArr);
				break;

			case 'EMC':

				$interface_employeecat_info = new INTERFACE_EmployeeCat();
				$interface_employeecat_info->parseData($postArr);
				break;

			case 'EMG':

				$interface_employee_group = new INTERFACE_EmployeeGroup();
				$interface_employee_group->parseData($postArr);
				break;
		}
	}

	function getEditData($index,$id) {
		$this->indexCode=$index;
		
		switch($this->indexCode) {
			
			case 'BNK':
			
				$interface_bank_info = new INTERFACE_BankInfo();
				return $interface_bank_info->editData($id);
		
			case 'CCB':
			
				$interface_cash_ben = new INTERFACE_CashBen();
				return $interface_cash_ben->editData($id);

			case 'COS':
			
				$interface_cost_center = new INTERFACE_CostCenter();
				return $interface_cost_center->editData($id);

			case 'COU':
			
				$interface_country_info = new INTERFACE_CountryInfo();
				return $interface_country_info->editData($id);

			case 'CUR':
			
				$interface_currency_type = new INTERFACE_CurrencyType();
				return $interface_currency_type->editData($id);

			case 'DWT':
			
				$interface_dwelling_type = new INTERFACE_DwellingType();
				return $interface_dwelling_type->editData($id);

			case 'ELE':
			
				$interface_electorate_info = new INTERFACE_ElectorateInfo();
				return $interface_electorate_info->editData($id);

			case 'EMC':
			
				$interface_employeecat_info = new INTERFACE_EmployeeCat();
				return $interface_employeecat_info->editData($id);

			case 'EMG':
			
				$interface_employee_group = new INTERFACE_EmployeeGroup();
				return $interface_employee_group->editData($id);
		}
	}
}

?>
