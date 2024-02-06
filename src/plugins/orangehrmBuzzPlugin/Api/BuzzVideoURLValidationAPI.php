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

namespace OrangeHRM\Buzz\Api;

use OrangeHRM\Buzz\Dto\BuzzVideoURL\BuzzEmbeddedURL;
use OrangeHRM\Buzz\Exception\InvalidURLException;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class BuzzVideoURLValidationAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_VIDEO_LINK = 'url';
    public const PARAMETER_VALID_VIDEO_LINK = 'valid';

    /**
     * @OA\Get(
     *     path="/api/v2/buzz/validation/links",
     *     tags={"Buzz/Validation"},
     *     summary="Validate Video Link",
     *     operationId="validate-video-link",
     *     @OA\PathParameter(
     *         name="link",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="url", type="string"),
     *                 @OA\Property(property="embeddedURL", type="string"),
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     * @throws InvalidURLException
     */
    public function getOne(): EndpointResult
    {
        $videoLink = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_VIDEO_LINK
        );

        $buzzEmbeddedURL = new BuzzEmbeddedURL($videoLink);

        $isValid = $buzzEmbeddedURL->isValidURL();
        $response = [
            self::PARAMETER_VALID_VIDEO_LINK => $isValid,
            'url' => null,
            'embeddedURL' => null,
        ];

        if ($isValid) {
            $response['url'] = $buzzEmbeddedURL->getURL();
            $response['embeddedURL'] = $buzzEmbeddedURL->getEmbeddedURL();
        }

        return new EndpointResourceResult(ArrayModel::class, $response);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getVideoValidationRule()
        );
    }

    /**
     * @return ParamRule
     */
    private function getVideoValidationRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_VIDEO_LINK,
            new Rule(Rules::REQUIRED),
            new Rule(Rules::STRING_TYPE),
            //TODO - length validation
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
