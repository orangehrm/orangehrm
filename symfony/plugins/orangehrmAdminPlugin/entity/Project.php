<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="ohrm_project")
 * @ORM\Entity
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", length=4)
     */
    private $customerId;

    /**
     * @var int
     *
     * @ORM\Column(name="is_deleted", type="integer", length=1)
     */
    private $is_deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=256)
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Customer", mappedBy="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="customer_id")
     * })
     */
    private $Customer;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ProjectActivity", mappedBy="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     * })
     */
    private $ProjectActivity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ProjectAdmin", mappedBy="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     * })
     */
    private $ProjectAdmin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Customer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProjectActivity = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProjectAdmin = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
