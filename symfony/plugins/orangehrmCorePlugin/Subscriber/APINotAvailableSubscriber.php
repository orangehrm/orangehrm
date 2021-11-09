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

namespace OrangeHRM\Core\Subscriber;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class APINotAvailableSubscriber extends AbstractEventSubscriber
{
    use TextHelperTrait;

    /**
     * @var ModuleService|null
     */
    protected ?ModuleService $moduleService = null;

    /**
     * Get Module Service
     * @return ModuleService|null
     */
    public function getModuleService(): ModuleService
    {
        if (is_null($this->moduleService)) {
            $this->moduleService = new ModuleService();
        }
        return $this->moduleService;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestEvent', 100000],
            ],
        ];
    }

    public function onRequestEvent(RequestEvent $event)
    {
        $disabledModules = [];
        $modules = $this->getModuleService()->getModuleList();
        foreach ($modules as $module) {
            if (!$module->getStatus()) {
                array_push($disabledModules, $module->getName());
            }
        }
        if ($event->isMasterRequest()) {
            foreach ($disabledModules as $diabledModule) {
                if ($this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/api/v2/' . $diabledModule)) {
                    throw new ForbiddenException('Unauthorized');
                }
            }
        }
    }
}
