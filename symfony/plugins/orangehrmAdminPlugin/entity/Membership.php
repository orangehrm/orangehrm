<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membership
 *
 * @ORM\Table(name="ohrm_membership")
 * @ORM\Entity
 */
class Membership
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

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeMembership", mappedBy="Membership")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="membershipId")
     * })
     */
    private $EmployeeMembership;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeMembership = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
