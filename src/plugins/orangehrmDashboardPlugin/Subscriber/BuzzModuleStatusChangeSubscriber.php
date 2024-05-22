<?php

namespace OrangeHRM\Dashboard\Subscriber;

use OrangeHRM\Core\Event\ModuleEvent;
use OrangeHRM\Core\Event\ModuleStatusChange;
use OrangeHRM\Dashboard\Traits\Service\ModuleServiceTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;

class BuzzModuleStatusChangeSubscriber extends AbstractEventSubscriber
{
    use ModuleServiceTrait;

    public const MODULE_BUZZ = 'buzz';

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ModuleEvent::MODULE_STATUS_CHANGE => [['onStatusChangeEvent', 0]]
        ];
    }

    /**
     * @param ModuleStatusChange $moduleStatusChange
     */
    public function onStatusChangeEvent(ModuleStatusChange $moduleStatusChange): void
    {
        $previousModule = $moduleStatusChange->getPreviousModule();
        $currentModule = $moduleStatusChange->getCurrentModule();

        if ($previousModule->getName() === self::MODULE_BUZZ) {
            $this->getModuleService()
                ->getModuleDao()
                ->updateDataGroupPermissionForWidgetModules('dashboard_buzz_widget', $currentModule->getStatus());
        }
    }
}
