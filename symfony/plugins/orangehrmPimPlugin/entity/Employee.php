<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table(name="hs_hr_employee")
 * @ORM\Entity
 */
class Employee
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $empNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_lastname", type="string", length=100)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_firstname", type="string", length=100)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_middle_name", type="string", length=100)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_nick_name", type="string", length=100)
     */
    private $nickName;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_smoker", type="integer", length=2)
     */
    private $smoker;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_ssn_num", type="string", length=100)
     */
    private $ssn;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_sin_num", type="string", length=100)
     */
    private $sin;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_other_id", type="string", length=100)
     */
    private $otherId;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_dri_lice_num", type="string", length=100)
     */
    private $licenseNo;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_military_service", type="string", length=100)
     */
    private $militaryService;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_street1", type="string", length=100)
     */
    private $street1;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_street2", type="string", length=100)
     */
    private $street2;

    /**
     * @var string
     *
     * @ORM\Column(name="city_code", type="string", length=100)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="coun_code", type="string", length=100)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="provin_code", type="string", length=100)
     */
    private $province;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_id", type="string", length=50)
     */
    private $employeeId;

    /**
     * @var string
     *
     * @ORM\Column(name="ethnic_race_code", type="string", length=13)
     */
    private $ethnic_race_code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="emp_birthday", type="date", length=25)
     */
    private $emp_birthday;

    /**
     * @var int
     *
     * @ORM\Column(name="nation_code", type="integer")
     */
    private $nation_code;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_gender", type="integer", length=2)
     */
    private $emp_gender;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_marital_status", type="string", length=20)
     */
    private $emp_marital_status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="emp_dri_lice_exp_date", type="date", length=25)
     */
    private $emp_dri_lice_exp_date;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_status", type="integer", length=13)
     */
    private $emp_status;

    /**
     * @var int
     *
     * @ORM\Column(name="job_title_code", type="integer", length=6)
     */
    private $job_title_code;

    /**
     * @var int
     *
     * @ORM\Column(name="eeo_cat_code", type="integer")
     */
    private $eeo_cat_code;

    /**
     * @var int
     *
     * @ORM\Column(name="work_station", type="integer", length=4)
     */
    private $work_station;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_zipcode", type="string", length=20)
     */
    private $emp_zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_hm_telephone", type="string", length=50)
     */
    private $emp_hm_telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_mobile", type="string", length=50)
     */
    private $emp_mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_work_telephone", type="string", length=50)
     */
    private $emp_work_telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_work_email", type="string", length=50)
     */
    private $emp_work_email;

    /**
     * @var string
     *
     * @ORM\Column(name="sal_grd_code", type="string", length=13)
     */
    private $sal_grd_code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="joined_date", type="date", length=25)
     */
    private $joined_date;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_oth_email", type="string", length=50)
     */
    private $emp_oth_email;

    /**
     * @var int
     *
     * @ORM\Column(name="termination_id", type="integer", length=4)
     */
    private $termination_id;

    /**
     * @var string
     *
     * @ORM\Column(name="custom1", type="string", length=250)
     */
    private $custom1;

    /**
     * @var string
     *
     * @ORM\Column(name="custom2", type="string", length=250)
     */
    private $custom2;

    /**
     * @var string
     *
     * @ORM\Column(name="custom3", type="string", length=250)
     */
    private $custom3;

    /**
     * @var string
     *
     * @ORM\Column(name="custom4", type="string", length=250)
     */
    private $custom4;

    /**
     * @var string
     *
     * @ORM\Column(name="custom5", type="string", length=250)
     */
    private $custom5;

    /**
     * @var string
     *
     * @ORM\Column(name="custom6", type="string", length=250)
     */
    private $custom6;

    /**
     * @var string
     *
     * @ORM\Column(name="custom7", type="string", length=250)
     */
    private $custom7;

    /**
     * @var string
     *
     * @ORM\Column(name="custom8", type="string", length=250)
     */
    private $custom8;

    /**
     * @var string
     *
     * @ORM\Column(name="custom9", type="string", length=250)
     */
    private $custom9;

    /**
     * @var string
     *
     * @ORM\Column(name="custom10", type="string", length=250)
     */
    private $custom10;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purged_at", type="datetime")
     */
    private $purged_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Subunit", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="work_station", referencedColumnName="id")
     * })
     */
    private $subDivision;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\JobTitle", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="job_title_code", referencedColumnName="id")
     * })
     */
    private $jobTitle;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmploymentStatus", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_status", referencedColumnName="id")
     * })
     */
    private $employeeStatus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="subordinates")
     */
    private $supervisors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Location", mappedBy="employees")
     */
    private $locations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpDependent", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $dependents;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpEmergencyContact", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $emergencyContacts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeImmigrationRecord", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $immigrationDocuments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpWorkExperience", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $workExperience;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeEducation", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $education;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSkill", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $skills;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeLanguage", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $languages;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeLicense", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $licenses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeMembership", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $memberships;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $salary;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpContract", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $contracts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeAttachment", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $attachments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ProjectAdmin", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $projectAdmin;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeTerminationRecord", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $EmployeeTerminationRecord;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Country", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coun_code", referencedColumnName="cou_code")
     * })
     */
    private $EmployeeCountry;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subDivision = new \Doctrine\Common\Collections\ArrayCollection();
        $this->jobTitle = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employeeStatus = new \Doctrine\Common\Collections\ArrayCollection();
        $this->supervisors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dependents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emergencyContacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->immigrationDocuments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->workExperience = new \Doctrine\Common\Collections\ArrayCollection();
        $this->education = new \Doctrine\Common\Collections\ArrayCollection();
        $this->skills = new \Doctrine\Common\Collections\ArrayCollection();
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->licenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salary = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contracts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projectAdmin = new \Doctrine\Common\Collections\ArrayCollection();
        $this->EmployeeTerminationRecord = new \Doctrine\Common\Collections\ArrayCollection();
        $this->EmployeeCountry = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
