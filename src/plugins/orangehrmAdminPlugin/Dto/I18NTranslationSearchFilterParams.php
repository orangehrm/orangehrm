<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class I18NTranslationSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['langString.value'];

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
    protected ?string $groupId = null;

    /**
     * @var bool|null
     */
    protected ?bool $onlyTranslated = null;

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
     */
    public function setTranslatedText(?string $translatedText): void
    {
        $this->translatedText = $translatedText;
    }

    /**
     * @return string|null
     */
    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    /**
     * @param string|null $groupId
     */
    public function setGroupId(?string $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return bool|null
     */
    public function getOnlyTranslated(): ?bool
    {
        return $this->onlyTranslated;
    }

    /**
     * @param bool|null $onlyTranslated
     */
    public function setOnlyTranslated(?bool $onlyTranslated): void
    {
        $this->onlyTranslated = $onlyTranslated;
    }
}
