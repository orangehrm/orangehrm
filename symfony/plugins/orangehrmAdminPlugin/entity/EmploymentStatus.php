<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmploymentStatus
 *
 * @ORM\Table(name="ohrm_employment_status")
 * @ORM\Entity
 */
class EmploymentStatus
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmploymentStatus")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="id", referencedColumnName="emp_status")
//     * })
//     */
//
//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmploymentStatus")
//     */
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmploymentStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="emp_status")
     * })
     */
    private $employees;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees(): \Doctrine\Common\Collections\Collection
    {
        return $this->employees;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $employees
     */
    public function setEmployees(\Doctrine\Common\Collections\Collection $employees): void
    {
        $this->employees = $employees;
    }

}
