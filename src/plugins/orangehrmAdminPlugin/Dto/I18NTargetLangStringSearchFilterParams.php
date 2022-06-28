<?php

namespace OrangeHRM\Admin\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class I18NTargetLangStringSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'langString.value',
        'translation.value',
        'module.name',
        'translation.translated',
    ];

    protected int $languageId;

    /**
     * @var string|null
     */
    protected ?string $sourceText = null;

    /**
     * @var string|null
     */
    protected ?string $translatedText = null;

    /**
     * @var string|null
     */
    protected ?string $ModuleName = null;

    /**
     * @var bool|null
     */
    protected ?bool $ShowCategory = null;

    /**
     * TargetLangStringSearchFilterParams constructor.
     */
    public function __construct()
    {
        $this->setSortField('langString.value');
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
     * @return string|null
     */
    public function getSourceText(): ?string
    {
        return $this->sourceText;
    }

    /**
     * @param string|null $sourceText
     * @return void
     */
    public function setSourceText(?string $sourceText): void
    {
        $this->sourceText = $sourceText;
    }

    /**
     * @return string|null
     */
    public function getTranslatedText(): ?string
    {
        return $this->translatedText;
    }

    /**
     * @param string|null $translatedText
     * @return void
     */
    public function setTranslatedText(?string $translatedText): void
    {
        $this->translatedText = $translatedText;
    }

    /**
     * @return string|null
     */
    public function getModuleName(): ?string
    {
        return $this->ModuleName;
    }

    /**
     * @param string|null $ModuleName
     */
    public function setModuleName(?string $ModuleName): void
    {
        $this->ModuleName = $ModuleName;
    }

    /**
     * @return bool|null
     */
    public function getShowCategory(): ?bool
    {
        return $this->ShowCategory;
    }

    /**
     * @param bool|null $ShowCategory
     */
    public function setShowCategory(?bool $ShowCategory): void
    {
        $this->ShowCategory = $ShowCategory;
    }
}
