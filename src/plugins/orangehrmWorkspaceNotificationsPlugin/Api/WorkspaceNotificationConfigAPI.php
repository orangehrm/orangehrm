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

namespace OrangeHRM\WorkspaceNotifications\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Api\Model\WorkspaceNotificationConfigModel;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WorkspaceNotificationSettingsServiceTrait;

class WorkspaceNotificationConfigAPI extends Endpoint implements ResourceEndpoint
{
    use WorkspaceNotificationSettingsServiceTrait;

    public const PARAMETER_ENABLE = 'enable';

    /**
     * @OA\Get(
     *     path="/api/v2/admin/workspace-notification/config",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Get Workspace notification config",
     *     description="Returns the global enable flag and the supported event-type identifiers. Admin role only.",
     *     operationId="get-workspace-notification-config",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Slack-ConfigModel"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        return new EndpointResourceResult(
            WorkspaceNotificationConfigModel::class,
            [$this->getWorkspaceNotificationSettingsService()->isEnabled(), WorkspaceNotificationRegistration::EVENT_TYPES]
        );
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/workspace-notification/config",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Update Workspace notification config",
     *     description="Toggles the global on/off flag stored in hs_hr_config.",
     *     operationId="update-workspace-notification-config",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"enable"},
     *             @OA\Property(property="enable", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Slack-ConfigModel"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $enable = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENABLE
        );
        $this->getWorkspaceNotificationSettingsService()->setEnabled($enable);
        return new EndpointResourceResult(
            WorkspaceNotificationConfigModel::class,
            [$enable, WorkspaceNotificationRegistration::EVENT_TYPES]
        );
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            new ParamRule(self::PARAMETER_ENABLE, new Rule(Rules::BOOL_TYPE)),
        );
    }

    public function delete(): EndpointResourceResult
    {
        throw new NotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
