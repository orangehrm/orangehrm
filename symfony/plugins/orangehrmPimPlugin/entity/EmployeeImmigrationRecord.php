<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeImmigrationRecord
 *
 * @ORM\Table(name="hs_hr_emp_passport")
 * @ORM\Entity
 */
class EmployeeImmigrationRecord
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $empNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ep_seqno", type="decimal", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $recordId;

    /**
     * @var string
     *
     * @ORM\Column(name="ep_passport_num", type="string", length=100)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="ep_i9_status", type="string", length=100)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ep_passportissueddate", type="datetime")
     */
    private $issuedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ep_passportexpiredate", type="datetime")
     */
    private $expiryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="ep_comments", type="string", length=255)
     */
    private $notes;

    /**
     * @var int
     *
     * @ORM\Column(name="ep_passport_type_flg", type="integer", length=2)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ep_i9_review_date", type="date", length=25)
     */
    private $reviewDate;

    /**
     * @var string
     *
     * @ORM\Column(name="cou_code", type="string", length=6)
     */
    private $countryCode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeImmigrationRecord")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
