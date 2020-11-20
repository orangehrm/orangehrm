<?php

//namespace Orangehrm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * I18NLangString
 *
 * @ORM\Table(name="ohrm_i18n_lang_string")
 * @ORM\Entity
 */
class I18NLangString
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
     * @ORM\Column(name="unit_id", type="integer")
     */
    private $unitId;

    /**
     * @var int
     *
     * @ORM\Column(name="source_id", type="integer")
     */
    private $sourceId;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", unique=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=20)
     */
    private $version;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="I18NGroup", mappedBy="I18NLangString")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupId", referencedColumnName="id")
     * })
     */
    private $I18NGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="I18NSource", mappedBy="I18NLangString")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sourceId", referencedColumnName="id")
     * })
     */
    private $I18NSource;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->I18NGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->I18NSource = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return int
     */
    public function getUnitId(): int
    {
        return $this->unitId;
    }

    /**
     * @param int $unitId
     */
    public function setUnitId(int $unitId)
    {
        $this->unitId = $unitId;
    }

    /**
     * @return int
     */
    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    /**
     * @param int $sourceId
     */
    public function setSourceId(int $sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getI18NGroup(): \Doctrine\Common\Collections\Collection
    {
        return $this->I18NGroup;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $I18NGroup
     */
    public function setI18NGroup(\Doctrine\Common\Collections\Collection $I18NGroup)
    {
        $this->I18NGroup = $I18NGroup;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getI18NSource(): \Doctrine\Common\Collections\Collection
    {
        return $this->I18NSource;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $I18NSource
     */
    public function setI18NSource(\Doctrine\Common\Collections\Collection $I18NSource)
    {
        $this->I18NSource = $I18NSource;
    }
}
