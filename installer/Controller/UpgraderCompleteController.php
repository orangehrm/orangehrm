<?php

namespace OrangeHRM\Installer\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class UpgraderCompleteController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('upgrader-complete-screen');
        $this->setComponent($component);
    }
}
