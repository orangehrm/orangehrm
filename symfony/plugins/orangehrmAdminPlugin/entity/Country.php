<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="hs_hr_country")
 * @ORM\Entity
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="cou_code", type="string", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cou_code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cou_name", type="string", length=80)
     */
    private $cou_name;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3)
     */
    private $iso3;

    /**
     * @var int
     *
     * @ORM\Column(name="numcode", type="integer", length=2)
     */
    private $numcode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Location", mappedBy="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cou_code", referencedColumnName="country")
     * })
     */
    private $locations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
