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

namespace OrangeHRM\Core\Api\V2\Validator;

class ParamRule
{
    /**
     * @var string
     */
    protected string $paramKey;

    /**
     * @var Rule[]
     */
    protected array $rules;

    /**
     * @var null|mixed
     */
    protected $default = null;

    /**
     * @var string
     */
    protected string $compositeClass = Rules::ALL_OF;

    /**
     * @param string $paramKey
     * @param Rule ...$rules
     */
    public function __construct(string $paramKey, Rule ...$rules)
    {
        $this->paramKey = $paramKey;
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
     * @return Rule[]
     */
    public function getRules(): array
    {
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
        return $this->compositeClass;
    }

    /**
     * @param string $compositeClass
     * @throws ValidatorException
     */
    public function setCompositeClass(string $compositeClass): void
    {
        $allowed = [Rules::ALL_OF, Rules::ONE_OF, Rules::ANY_OF, Rules::NONE_OF];
        if (!in_array($compositeClass, $allowed)) {
            throw new ValidatorException(
                sprintf('Expected one of `%s`. But got `%s`.', implode('`, `', $allowed), $compositeClass)
            );
        }
        $this->compositeClass = $compositeClass;
    }
}
