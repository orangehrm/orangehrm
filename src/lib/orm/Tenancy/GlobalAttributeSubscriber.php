<?php

namespace OrangeHRM\ORM\Tenancy;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class GlobalAttributeSubscriber implements EventSubscriber
{
    private int $globalAttributeValue;

    public function __construct($globalAttributeValue)
    {
        $this->globalAttributeValue = $globalAttributeValue;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof TenantAwareInterface) {
            $entity->setOrgId($this->globalAttributeValue);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof TenantAwareInterface) {
            $entity->setOrgId($this->globalAttributeValue);
        }
    }
}
