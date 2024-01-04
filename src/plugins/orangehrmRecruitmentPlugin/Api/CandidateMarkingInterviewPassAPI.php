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

namespace OrangeHRM\Recruitment\Api;

use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Interview;
use OrangeHRM\Entity\WorkflowStateMachine;

class CandidateMarkingInterviewPassAPI extends AbstractCandidateActionAPI
{
    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRuleForUpdate();
        $paramRuleCollection->addParamValidation(
            new ParamRule(
                self::PARAMETER_INTERVIEW_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Interview::class])
            ),
        );
        return $paramRuleCollection;
    }

    /**
     * @OA\Put(
     *     path="/api/v2/recruitment/candidates/{candidateId}/interviews/{interviewId}/pass",
     *     tags={"Recruitment/Candidate Workflow"},
     *     summary="Mark Candidate Interview as Passed",
     *     operationId="mark-candidate-interview-as-passed",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="interviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="note", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateHistoryDefaultModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getResultingState(): int
    {
        return WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED;
    }
}
