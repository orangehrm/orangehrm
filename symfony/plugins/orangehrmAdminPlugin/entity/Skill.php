<?php

namespace OrangeHRM\Entity;

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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=120)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2147483647)
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSkill", mappedBy="Skill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="skillId")
     * })
     */
    private $EmployeeSkill;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeSkill = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
