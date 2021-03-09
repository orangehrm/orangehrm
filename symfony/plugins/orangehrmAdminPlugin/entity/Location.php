<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location
 *
 * @ORM\Table(name="ohrm_location")
 * @ORM\Entity
 */
class Location
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=110)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=3)
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=60)
     */
    private $province;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=60)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=35)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=35)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=35)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=255)
     */
    private $notes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Country", mappedBy="Location")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_code", referencedColumnName="cou_code")
     * })
     */
    private $country;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="locations")
     */
    private $employees;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->country = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
