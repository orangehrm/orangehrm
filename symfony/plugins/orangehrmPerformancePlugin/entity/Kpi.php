<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kpi
 *
 * @ORM\Table(name="ohrm_kpi")
 * @ORM\Entity
 */
class Kpi
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="job_title_code", type="string", length=10)
     */
    private $jobTitleCode;

    /**
     * @var string
     *
     * @ORM\Column(name="kpi_indicators", type="string", length=255)
     */
    private $kpi_indicators;

    /**
     * @var int
     *
     * @ORM\Column(name="min_rating", type="integer", length=11)
     */
    private $min_rating;

    /**
     * @var int
     *
     * @ORM\Column(name="max_rating", type="integer", length=11)
     */
    private $max_rating;

    /**
     * @var int
     *
     * @ORM\Column(name="default_kpi", type="integer", length=2)
     */
    private $default_kpi;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\JobTitle", mappedBy="Kpi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="jobTitleCode", referencedColumnName="id")
     * })
     */
    private $JobTitle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->JobTitle = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
