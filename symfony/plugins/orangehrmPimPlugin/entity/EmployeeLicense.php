<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeLicense
 *
 * @ORM\Table(name="ohrm_emp_license")
 * @ORM\Entity
 */
class EmployeeLicense
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $empNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="license_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $licenseId;

    /**
     * @var string
     *
     * @ORM\Column(name="license_no", type="string", length=50)
     */
    private $licenseNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="license_issued_date", type="date")
     */
    private $licenseIssuedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="license_expiry_date", type="date")
     */
    private $licenseExpiryDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeLicense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $Employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\License", mappedBy="EmployeeLicense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="license_id", referencedColumnName="id")
     * })
     */
    private $License;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->License = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
