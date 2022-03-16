<?php


namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReviewerGroup
 *
 * @ORM\Table(name="ohrm_reviewer_group")
 * @ORM\Entity
 */
class ReviewerGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=50,nullable=true)
     */
    private ?string $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="piority", type="integer", length=7,nullable=true)
     */
    private ?int $piority;

    /**
     * @var Reviewer[]
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Reviewer", mappedBy="ReviewerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="reviewer_group_id")
     * })
     */
    private $ratings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ratings = new ArrayCollection();
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getPiority(): ?int
    {
        return $this->piority;
    }

    /**
     * @param int|null $piority
     */
    public function setPiority(?int $piority): void
    {
        $this->piority = $piority;
    }


}
