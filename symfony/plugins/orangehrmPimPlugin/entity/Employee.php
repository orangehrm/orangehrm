<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $empNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_lastname", type="string", length=100)
     */
    private string $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_firstname", type="string", length=100)
     */
    private string $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_middle_name", type="string", length=100)
     */
    private string $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="emp_nick_name", type="string", length=100)
     */
    private string $nickName;

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
     * @var string|null
     *
     * @ORM\Column(name="employee_id", type="string", length=50, nullable=true)
     */
    private ?string $employeeId;

    /**
     * @var string
     *
     * @ORM\Column(name="ethnic_race_code", type="string", length=13)
     */
    private $ethnic_race_code;

    /**
     * @var DateTime
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
     * @var DateTime
     *
     * @ORM\Column(name="emp_dri_lice_exp_date", type="date", length=25)
     */
    private $emp_dri_lice_exp_date;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="EmploymentStatus", inversedBy="employees")
     * @ORM\Column(name="emp_status", type="integer", length=13)
     */
    private $empStatus;

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
     * @var DateTime
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
     * @var DateTime|null
     *
     * @ORM\Column(name="purged_at", type="datetime", nullable=true)
     */
    private ?DateTime $purgedAt;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Subunit", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="work_station", referencedColumnName="id")
     * })
     */
    private $subDivision;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\JobTitle", mappedBy="employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="job_title_code", referencedColumnName="id")
     * })
     */
    private $jobTitle;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmploymentStatus", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_status", referencedColumnName="id")
     * })
     */
    private $employeeStatus;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="subordinates")
     */
    private $supervisors;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Location", mappedBy="employees")
     */
    private $locations;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpDependent", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $dependents;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpEmergencyContact", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $emergencyContacts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeImmigrationRecord", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $immigrationDocuments;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpWorkExperience", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $workExperience;

//    /**
//     * @var Collection
//     *
//     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeEducation", mappedBy="employee")
//     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
//     */
//    private $education;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSkill", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $skills;

//    /**
//     * @var Collection
//     *
//     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeLanguage", mappedBy="Employee")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
//     * })
//     */
//    private $languages;

//    /**
//     * @var Collection
//     *
//     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeLicense", mappedBy="Employee")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
//     * })
//     */
//    private $licenses;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeMembership", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $memberships;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $salary;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmpContract", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $contracts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeAttachment", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $attachments;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ProjectAdmin", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $projectAdmin;

    /**
     * @var EmployeeTerminationRecord|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\EmployeeTerminationRecord")
     * @ORM\JoinColumn(name="termination_id", referencedColumnName="id")
     */
    private ?EmployeeTerminationRecord $employeeTerminationRecord;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Country", mappedBy="Employee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coun_code", referencedColumnName="cou_code")
     * })
     */
    private $EmployeeCountry;

    /**
     * @var User[]|Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\User", mappedBy="employee")
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subDivision = new ArrayCollection();
        $this->jobTitle = new ArrayCollection();
        $this->employeeStatus = new ArrayCollection();
        $this->supervisors = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->dependents = new ArrayCollection();
        $this->emergencyContacts = new ArrayCollection();
        $this->immigrationDocuments = new ArrayCollection();
        $this->workExperience = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->licenses = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->salary = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->projectAdmin = new ArrayCollection();
        $this->EmployeeCountry = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName(string $middleName): void
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @param string|null $employeeId
     */
    public function setEmployeeId(?string $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return EmployeeTerminationRecord|null
     */
    public function getEmployeeTerminationRecord(): ?EmployeeTerminationRecord
    {
        return $this->employeeTerminationRecord;
    }

    /**
     * @param EmployeeTerminationRecord|null $employeeTerminationRecord
     */
    public function setEmployeeTerminationRecord(?EmployeeTerminationRecord $employeeTerminationRecord): void
    {
        $this->employeeTerminationRecord = $employeeTerminationRecord;
    }

    /**
     * @return DateTime|null
     */
    public function getPurgedAt(): ?DateTime
    {
        return $this->purgedAt;
    }

    /**
     * @param DateTime|null $purgedAt
     */
    public function setPurgedAt(?DateTime $purgedAt): void
    {
        $this->purgedAt = $purgedAt;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection|User[] $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }
}
