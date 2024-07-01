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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_i18n_error")
 * @ORM\Entity
 */
class I18NError
{
    public const PLACEHOLDER_MISMATCH = 'placeholder_mismatch';
    public const SELECT_MISMATCH = 'select_placeholder_mismatch';
    public const PLURAL_MISMATCH = 'plural_placeholder_mismatch';
    public const UNNECESSARY_PLACEHOLDER = 'unnecessary_placeholder';
    public const INVALID_SYNTAX = 'invalid_syntax';
    public const ERROR_MAP = [
        self::PLACEHOLDER_MISMATCH,
        self::SELECT_MISMATCH,
        self::PLURAL_MISMATCH,
        self::UNNECESSARY_PLACEHOLDER,
        self::INVALID_SYNTAX,
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @ORM\Id
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private string $message;

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
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
