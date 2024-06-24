<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\I18NImportErrorDecorator;

/**
 * @method I18NImportErrorDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_i18n_import_error")
 * @ORM\Entity
 */
class I18NImportError
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var I18NLangString
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\I18NLangString")
     * @ORM\JoinColumn(name="lang_string_id", referencedColumnName="id")
     */
    private I18NLangString $langString;

    /**
     * @var I18NLanguage
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\I18NLanguage")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private I18NLanguage $language;

    /**
     * @var I18NError
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\I18NError")
     * @ORM\JoinColumn(name="error_name", referencedColumnName="name")
     */
    private I18NError $error;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="imported_by", referencedColumnName="emp_number")
     */
    private Employee $importedBy;

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
     * @return I18NLangString
     */
    public function getLangString(): I18NLangString
    {
        return $this->langString;
    }

    /**
     * @param I18NLangString $langString
     */
    public function setLangString(I18NLangString $langString): void
    {
        $this->langString = $langString;
    }

    /**
     * @return I18NLanguage
     */
    public function getLanguage(): I18NLanguage
    {
        return $this->language;
    }

    /**
     * @param I18NLanguage $language
     */
    public function setLanguage(I18NLanguage $language): void
    {
        $this->language = $language;
    }

    /**
     * @return I18NError
     */
    public function getError(): I18NError
    {
        return $this->error;
    }

    /**
     * @param I18NError $error
     */
    public function setError(I18NError $error): void
    {
        $this->error = $error;
    }

    /**
     * @return Employee
     */
    public function getImportedBy(): Employee
    {
        return $this->importedBy;
    }

    /**
     * @param Employee $importedBy
     */
    public function setImportedBy(Employee $importedBy): void
    {
        $this->importedBy = $importedBy;
    }
}
