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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\I18NTranslationDecorator;

/**
 * @method I18NTranslationDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_i18n_translate", uniqueConstraints={@ORM\UniqueConstraint(name="translateUniqueId", columns={"lang_string_id", "language_id"})})
 * @ORM\Entity
 */
class I18NTranslation
{
    use DateTimeHelperTrait;
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
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\I18NLangString", inversedBy="translations")
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
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    private ?string $value = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="customized", type="boolean", options={"default" : 0})
     */
    private bool $customized = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private DateTime $modifiedAt;

    public function __construct()
    {
        $this->setModifiedAt($this->getDateTimeHelper()->getNow());
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
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isCustomized(): bool
    {
        return $this->customized;
    }

    /**
     * @param bool $customized
     */
    public function setCustomized(bool $customized): void
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
    public function setModifiedAt(DateTime $modifiedAt): void
    {
        $this->modifiedAt = $modifiedAt;
    }
}
