<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectActivity
 *
 * @ORM\Table(name="ohrm_project_activity")
 * @ORM\Entity
 */
class ProjectActivity
{
    /**
     * @var int
     *
     * @ORM\Column(name="activity_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $activityId;

    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer", length=4)
     */
    private $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="is_deleted", type="integer", length=1)
     */
    private $is_deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=110)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Project", mappedBy="ProjectActivity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     * })
     */
    private $Project;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Project = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
