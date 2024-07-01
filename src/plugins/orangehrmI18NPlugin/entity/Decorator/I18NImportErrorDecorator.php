<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NImportError;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;

class I18NImportErrorDecorator
{
    use EntityManagerHelperTrait;

    private I18NImportError $importError;

    public function __construct(I18NImportError $importError)
    {
        $this->importError = $importError;
    }

    /**
     * @return I18NImportError
     */
    public function getImportError(): I18NImportError
    {
        return $this->importError;
    }

    /**
     * @param I18NImportError $importError
     */
    public function setImportError(I18NImportError $importError): void
    {
        $this->importError = $importError;
    }

    /**
     * @param int $langStringId
     */
    public function setLangStringById(int $langStringId): void
    {
        $langString = $this->getReference(I18NLangString::class, $langStringId);
        $this->getImportError()->setLangString($langString);
    }

    /**
     * @param int $languageId
     */
    public function setLanguageById(int $languageId): void
    {
        $language = $this->getReference(I18NLanguage::class, $languageId);
        $this->getImportError()->setLanguage($language);
    }

    /**
     * @param string $errorName
     */
    public function setErrorByName(string $errorName): void
    {
        $error = $this->getReference(I18NError::class, $errorName);
        $this->getImportError()->setError($error);
    }

    /**
     * @param int $empNumber
     */
    public function setImportedEmployeeByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getImportError()->setImportedBy($employee);
    }
}
