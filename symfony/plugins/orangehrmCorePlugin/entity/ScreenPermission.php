<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScreenPermission
 *
 * @ORM\Table(name="ohrm_user_role_screen")
 * @ORM\Entity
 */
class ScreenPermission
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
     * @var int
     *
     * @ORM\Column(name="user_role_id", type="integer")
     */
    private $userRoleId;

    /**
     * @var int
     *
     * @ORM\Column(name="screen_id", type="integer")
     */
    private $screenId;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_read", type="boolean")
     */
    private $canRead;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_create", type="boolean")
     */
    private $canCreate;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_update", type="boolean")
     */
    private $canUpdate;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_delete", type="boolean")
     */
    private $canDelete;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\UserRole", mappedBy="ScreenPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_role_id", referencedColumnName="id")
     * })
     */
    private $userRole;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Screen", mappedBy="ScreenPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="screen_id", referencedColumnName="id")
     * })
     */
    private $screen;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userRole = new \Doctrine\Common\Collections\ArrayCollection();
        $this->screen = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
