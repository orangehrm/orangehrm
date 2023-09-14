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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\Language;
use OrangeHRM\Pim\Dto\EmployeeLanguagesSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeLanguageService;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeAllowedLanguageModel",
 *     type="object",
 *     @OA\Property(property="id", description="The numerical ID of the language", type="integer"),
 *     @OA\Property(property="name", description="The name of the language", type="string"),
 *     @OA\Property(
 *         property="allowedFluencyIds",
 *         description="A list of allowed fluency IDs corresponding  to writing, speaking and reading fluency",
 *         type="array",
 *         @OA\Items(type="integer")
 *     )
 * )
 */
class EmployeeAllowedLanguageModel implements CollectionNormalizable
{
    public const EMP_NUMBER = 'empNumber';
    public const LANGUAGES = 'languages';

    /**
     * @var int
     */
    protected int $empNumber;

    /**
     * @var Language[]
     */
    protected array $languages;

    /**
     * @var EmployeeLanguageService|null
     */
    protected ?EmployeeLanguageService $employeeLanguageService = null;

    public function __construct(array $data)
    {
        $this->empNumber = $data[self::EMP_NUMBER];
        $this->languages = $data[self::LANGUAGES];
    }

    /**
     * @return EmployeeLanguageService
     */
    protected function getEmployeeLanguageService(): EmployeeLanguageService
    {
        if (!$this->employeeLanguageService instanceof EmployeeLanguageService) {
            $this->employeeLanguageService = new EmployeeLanguageService();
        }
        return $this->employeeLanguageService;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $allowedLanguages = [];
        $fluencyIds = array_keys(EmployeeLanguage::FLUENCIES);
        $employeeLanguagesSearchFilterParams = new EmployeeLanguagesSearchFilterParams();
        $employeeLanguagesSearchFilterParams->setEmpNumber($this->empNumber);
        $employeeLanguagesSearchFilterParams->setLimit(0);
        $employeeLanguagesSearchFilterParams->setSortField(null);

        $languageIds = array_map(
            function (Language $language) {
                return $language->getId();
            },
            $this->languages
        );
        $employeeLanguagesSearchFilterParams->setLanguageIds($languageIds);
        $employeeLanguages = $this->getEmployeeLanguageService()
            ->getEmployeeLanguageDao()
            ->getEmployeeLanguages($employeeLanguagesSearchFilterParams);

        $employeeLanguagesAssoc = [];
        foreach ($employeeLanguages as $employeeLanguage) {
            if (!isset($employeeLanguagesAssoc[$employeeLanguage->getLanguage()->getId()])) {
                $employeeLanguagesAssoc[$employeeLanguage->getLanguage()->getId()] = [];
            }
            array_push($employeeLanguagesAssoc[$employeeLanguage->getLanguage()->getId()], $employeeLanguage);
        }

        foreach ($this->languages as $language) {
            $employeeLanguages = $employeeLanguagesAssoc[$language->getId()] ?? [];
            $alreadyUsedFluencyIds = array_map(
                function (EmployeeLanguage $employeeLanguage) {
                    return $employeeLanguage->getFluency();
                },
                $employeeLanguages
            );

            $allowedFluencyIds = array_values(array_diff($fluencyIds, $alreadyUsedFluencyIds));
            $allowedLanguage = [
                'id' => $language->getId(),
                'name' => $language->getName(),
                'allowedFluencyIds' => $allowedFluencyIds,
            ];
            $allowedLanguages[] = $allowedLanguage;
        }
        return $allowedLanguages;
    }
}
