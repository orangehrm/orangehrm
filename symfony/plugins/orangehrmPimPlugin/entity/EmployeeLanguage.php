<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeLanguage
 *
 * @ORM\Table(name="hs_hr_emp_language")
 * @ORM\Entity
 */
class EmployeeLanguage
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
     * @var string
     *
     * @ORM\Column(name="lang_id", type="string", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $langId;

    /**
     * @var int
     *
     * @ORM\Column(name="fluency", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fluency;

    /**
     * @var int
     *
     * @ORM\Column(name="competency", type="integer")
     */
    private $competency;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=100)
     */
    private $comments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeLanguage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Language", mappedBy="EmployeeLanguage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * })
     */
    private $Language;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Language = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
