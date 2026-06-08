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

namespace OrangeHRM\WorkspaceNotifications\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelConstructorArgsAwareInterface;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;

/**
 * @OA\Schema(
 *     schema="Slack-ConfigModel",
 *     type="object",
 *     description="Singleton Slack config: global on/off + the supported event-type identifiers.",
 *     @OA\Property(property="enable", type="boolean", description="Global on/off (backed by hs_hr_config).", example=true),
 *     @OA\Property(
 *         property="eventTypes",
 *         type="array",
 *         description="Identifiers of supported notification events. Drives the UI dropdown; adding a new event type is a backend-only change.",
 *         @OA\Items(type="string", enum={"BIRTHDAY", "LEAVE_TODAY"})
 *     )
 * )
 */
class WorkspaceNotificationConfigModel implements Normalizable, ModelConstructorArgsAwareInterface
{
    private bool $enabled;
    /** @var string[] */
    private array $eventTypes;

    /**
     * @param string[] $eventTypes
     */
    public function __construct(bool $enabled, array $eventTypes = [])
    {
        $this->enabled = $enabled;
        $this->eventTypes = $eventTypes;
    }

    public function toArray(): array
    {
        return [
            'enable' => $this->enabled,
            'eventTypes' => $this->eventTypes,
        ];
    }
}
