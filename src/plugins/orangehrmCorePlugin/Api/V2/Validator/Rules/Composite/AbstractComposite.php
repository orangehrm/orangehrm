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

namespace OrangeHRM\Core\Api\V2\Validator\Rules\Composite;

use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use ReflectionClass;
use ReflectionException;

abstract class AbstractComposite extends AbstractRule
{
    /**
     * @var Rule[]
     */
    protected array $rules;

    /**
     * @param Rule ...$rules
     */
    public function __construct(Rule ...$rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param mixed $input
     * @return bool
     * @throws ReflectionException
     */
    public function validate($input): bool
    {
        $ruleClasses = [];
        foreach ($this->rules as $rule) {
            $params = $rule->getRuleConstructorParams();
            if (!is_array($params)) {
                $params = [];
            }
            $ruleClasses[] = (new ReflectionClass($rule->getRuleClass()))->newInstanceArgs($params);
        }
        $compositeRuleClass = $this->getRuleClass();
        /** @var AbstractComposite $paramValidatorRule */
        $paramValidatorRule = new $compositeRuleClass(...$ruleClasses);
        return $paramValidatorRule->validate($input);
    }

    /**
     * @return string
     */
    abstract protected function getRuleClass(): string;
}
