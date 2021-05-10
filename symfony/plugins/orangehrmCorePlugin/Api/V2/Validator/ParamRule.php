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

class ParamRule
{
    /**
     * @var string
     */
    protected string $paramKey;

    /**
     * @var bool
     */
    protected bool $required;

    /**
     * @var Rule[]
     */
    protected array $rules;

    /**
     * @var null|mixed
     */
    protected $default = null;

    /**
     * @var string|null
     */
    protected ?string $compositeClass = null;

    /**
     * @param string $paramKey
     * @param bool $required
     * @param Rule ...$rules
     */
    public function __construct(string $paramKey, bool $required = false, Rule ...$rules)
    {
        $this->paramKey = $paramKey;
        $this->required = $required;
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getParamKey(): string
    {
        return $this->paramKey;
    }

    /**
     * @param string $paramKey
     */
    public function setParamKey(string $paramKey): void
    {
        $this->paramKey = $paramKey;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return Rule[]
     * @throws ValidatorException
     */
    public function getRules(): array
    {
        if ($this->isRequired()) {
            $compositeClass = Rules::ALL_OF;
            if (!is_null($this->compositeClass)) {
                $compositeClass = $this->getCompositeClass();
            }
            $this->setCompositeClass(Rules::ONE_OF);
            return [
                new Rule(Rules::REQUIRED),
                new Rule(
                    $compositeClass,
                    $this->rules
                ),
            ];
        }

        return $this->rules;
    }

    /**
     * @param Rule[] $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed|null $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getCompositeClass(): string
    {
        if (is_null($this->compositeClass)) {
            $this->compositeClass = Rules::ALL_OF;
        }
        return $this->compositeClass;
    }

    /**
     * @param string|null $compositeClass
     * @throws ValidatorException
     */
    public function setCompositeClass(?string $compositeClass): void
    {
        $allowed = [Rules::ALL_OF, Rules::ONE_OF, Rules::ANY_OF, Rules::NONE_OF, null];
        if (!in_array($compositeClass, $allowed)) {
            throw new ValidatorException(
                sprintf('Expected one of `%s null`. But got `%s`.', implode('`, `', $allowed), $compositeClass)
            );
        }
        $this->compositeClass = $compositeClass;
    }
}
