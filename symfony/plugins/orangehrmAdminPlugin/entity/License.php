<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * License
 *
 * @ORM\Table(name="ohrm_license")
 * @ORM\Entity
 */
class License
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    //mappedBy = license , ColumnName = "license_id"

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="EmployeeLicense", mappedBy="License")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="license_id")
     * })
     */
    private $EmployeeLicense;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeLicense = new ArrayCollection();
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
    public function setId(int $id)
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
    public function setName(string $name)
    {
        $this->name = $name;
    }

}
