<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Skill
 *
 * @ORM\Table(name="ohrm_skill")
 * @ORM\Entity
 */
class Skill
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string | null
     *
     * @ORM\Column(name="name", type="string", length=120, nullable=true)
     */
    private ?string $name;

    /**
     * @var string | null
     *
     * @ORM\Column(name="description", type="string", length=2147483647, nullable=true)
     */
    private ?string $description;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmployeeSkill", mappedBy="skill")
     */
    private Collection $employeeSkills;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employeeSkills = new ArrayCollection();
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getEmployeeSkills(): Collection
    {
        return $this->employeeSkills;
    }

    /**
     * @param Collection $employeeSkills
     */
    public function setEmployeeSkills(Collection $employeeSkills): void
    {
        $this->employeeSkills = $employeeSkills;
    }
}
