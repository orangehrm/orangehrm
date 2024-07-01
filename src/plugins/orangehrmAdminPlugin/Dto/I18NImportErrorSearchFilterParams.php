<?php

namespace OrangeHRM\Admin\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class I18NImportErrorSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['importError.langString'];

    /**
     * @var int
     */
    protected int $languageId;

    /**
     * @var int
     */
    protected int $empNumber;

    public function __construct()
    {
        $this->setSortField('importError.langString');
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
    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

}
