<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Core\Api\V2\Validator;

class Rule
{
    /**
     * @var string
     */
    protected string $ruleClass;

    /**
     * @var array
     */
    protected array $ruleConstructorParams;

    /**
     * @var string|null
     */
    protected ?string $message;

    /**
     * @param string $ruleClass
     * @param array $ruleConstructorParams
     * @param string|null $message
     */
    public function __construct(string $ruleClass, array $ruleConstructorParams = [], ?string $message = null)
    {
        $this->ruleClass = $ruleClass;
        $this->ruleConstructorParams = $ruleConstructorParams;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getRuleClass(): string
    {
        return $this->ruleClass;
    }

    /**
     * @param string $ruleClass
     */
    public function setRuleClass(string $ruleClass): void
    {
        $this->ruleClass = $ruleClass;
    }

    /**
     * @return array
     */
    public function getRuleConstructorParams(): array
    {
        return $this->ruleConstructorParams;
    }

    /**
     * @param array $ruleConstructorParams
     */
    public function setRuleConstructorParams(array $ruleConstructorParams): void
    {
        $this->ruleConstructorParams = $ruleConstructorParams;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}
