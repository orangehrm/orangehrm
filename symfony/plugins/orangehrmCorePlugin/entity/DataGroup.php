<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataGroup
 *
 * @ORM\Table(name="ohrm_data_group")
 * @ORM\Entity
 */
class DataGroup
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="can_read", type="integer", length=1, nullable=true)
     */
    private $canRead;

    /**
     * @var int|null
     *
     * @ORM\Column(name="can_create", type="integer", length=1, nullable=true)
     */
    private $canCreate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="can_update", type="integer", length=1, nullable=true)
     */
    private $canUpdate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="can_delete", type="integer", length=1, nullable=true)
     */
    private $canDelete;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Screen", mappedBy="dataGroup")
     */
    private $screens;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->screens = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getCanRead(): ?int
    {
        return $this->canRead;
    }

    /**
     * @param int|null $canRead
     */
    public function setCanRead(?int $canRead): void
    {
        $this->canRead = $canRead;
    }

    /**
     * @return int|null
     */
    public function getCanCreate(): ?int
    {
        return $this->canCreate;
    }

    /**
     * @param int|null $canCreate
     */
    public function setCanCreate(?int $canCreate): void
    {
        $this->canCreate = $canCreate;
    }

    /**
     * @return int|null
     */
    public function getCanUpdate(): ?int
    {
        return $this->canUpdate;
    }

    /**
     * @param int|null $canUpdate
     */
    public function setCanUpdate(?int $canUpdate): void
    {
        $this->canUpdate = $canUpdate;
    }

    /**
     * @return int|null
     */
    public function getCanDelete(): ?int
    {
        return $this->canDelete;
    }

    /**
     * @param int|null $canDelete
     */
    public function setCanDelete(?int $canDelete): void
    {
        $this->canDelete = $canDelete;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScreens()
    {
        return $this->screens;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $screens
     */
    public function setScreens($screens): void
    {
        $this->screens = $screens;
    }
}
