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

class ParamRuleCollection
{
    public const DEFAULT_EXCLUDED_PARAM_KEYS = ['_api', '_key', '_controller', '_route', '_route_params'];

    /**
     * @var array<string, ParamRule>
     */
    protected array $paramValidations = [];

    /**
     * @var bool
     */
    protected bool $strict = true;

    /**
     * @var string[]
     */
    protected array $excludedParamKeys = self::DEFAULT_EXCLUDED_PARAM_KEYS;

    /**
     * @param ParamRule ...$paramValidations
     */
    public function __construct(ParamRule ...$paramValidations)
    {
        $this->setParamValidations($paramValidations);
    }

    /**
     * @return ParamRule[]
     */
    public function getParamValidations(): array
    {
        return array_values($this->paramValidations);
    }

    /**
     * @param ParamRule[] $paramValidations
     */
    public function setParamValidations(array $paramValidations): void
    {
        $this->paramValidations = [];
        foreach ($paramValidations as $paramValidation) {
            $this->addParamValidation($paramValidation);
        }
    }

    /**
     * @param ParamRule $paramValidation
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function addParamValidation(ParamRule $paramValidation): void
    {
        if (isset($this->paramValidations[$paramValidation->getParamKey()])) {
            throw new ValidatorException(
                sprintf(
                    'Multiple instance of `%s` found for `%s` request parameter.',
                    ParamRule::class,
                    $paramValidation->getParamKey()
                )
            );
        }
        $this->paramValidations[$paramValidation->getParamKey()] = $paramValidation;
    }

    /**
     * @param string $paramKey
     * @return ParamRule|null
     */
    public function removeParamValidation(string $paramKey): ?ParamRule
    {
        if (isset($this->paramValidations[$paramKey])) {
            $paramRule = $this->paramValidations[$paramKey];
            unset($this->paramValidations[$paramKey]);
            return $paramRule;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * @param bool $strict
     */
    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }

    /**
     * @return array<string, ParamRule>
     */
    public function getMap(): array
    {
        return $this->paramValidations;
    }

    /**
     * @return string[]
     */
    public function getExcludedParamKeys(): array
    {
        return $this->excludedParamKeys;
    }

    /**
     * @param string[] $excludedParamKeys
     */
    public function setExcludedParamKeys(array $excludedParamKeys): void
    {
        $this->excludedParamKeys = $excludedParamKeys;
    }

    /**
     * @param string $excludedParamKey
     */
    public function addExcludedParamKey(string $excludedParamKey): void
    {
        $this->excludedParamKeys[] = $excludedParamKey;
    }
}
