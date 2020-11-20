<?php

//namespace Orangehrm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * I18NTranslate
 *
 * @ORM\Table(name="ohrm_i18n_translate", uniqueConstraints={@ORM\UniqueConstraint(name="translateUniqueId", columns={"lang_string_id", "language_id"})})
 * @ORM\Entity
 */
class I18NTranslate
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
     * @ORM\Column(name="lang_string_id", type="integer")
     */
    private $langStringId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="translated", type="boolean")
     */
    private $translated;

    /**
     * @var bool
     *
     * @ORM\Column(name="customized", type="boolean")
     */
    private $customized;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="I18NLangString", mappedBy="I18NTranslate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="langStringId", referencedColumnName="id")
     * })
     */
    private $I18NLangString;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="I18NLanguage", mappedBy="I18NTranslate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="languageId", referencedColumnName="id")
     * })
     */
    private $I18NLanguage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->I18NLangString = new \Doctrine\Common\Collections\ArrayCollection();
        $this->I18NLanguage = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function getLangStringId(): int
    {
        return $this->langStringId;
    }

    /**
     * @param int $langStringId
     */
    public function setLangStringId(int $langStringId)
    {
        $this->langStringId = $langStringId;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId(int $languageId)
    {
        $this->languageId = $languageId;
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
     * @return bool
     */
    public function getTranslated(): bool
    {
        return $this->translated;
    }

    /**
     * @param bool $translated
     */
    public function setTranslated(bool $translated)
    {
        $this->translated = $translated;
    }

    /**
     * @return bool
     */
    public function getCustomized(): bool
    {
        return $this->customized;
    }

    /**
     * @param bool $customized
     */
    public function setCustomized(bool $customized)
    {
        $this->customized = $customized;
    }

    /**
     * @return DateTime
     */
    public function getModifiedAt(): DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime $modifiedAt
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getI18NLangString(): \Doctrine\Common\Collections\Collection
    {
        return $this->I18NLangString;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $I18NLangString
     */
    public function setI18NLangString(\Doctrine\Common\Collections\Collection $I18NLangString)
    {
        $this->I18NLangString = $I18NLangString;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getI18NLanguage(): \Doctrine\Common\Collections\Collection
    {
        return $this->I18NLanguage;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $I18NLanguage
     */
    public function setI18NLanguage(\Doctrine\Common\Collections\Collection $I18NLanguage)
    {
        $this->I18NLanguage = $I18NLanguage;
    }
}
