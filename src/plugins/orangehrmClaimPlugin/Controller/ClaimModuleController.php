<?php

namespace OrangeHRM\Claim\Controller;

use OrangeHRM\Core\Controller\AbstractModuleController;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;

class ClaimModuleController extends AbstractModuleController
{
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public function handle(): RedirectResponse
    {
        $defaultPath = $this->getUserRoleManager()->getModuleDefaultPage('claim');
        return $this->redirect($defaultPath);
    }
}
