<?php

namespace OrangeHRM\Installer\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class UpgraderProcessController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('upgrade-process-screen');
        $this->setComponent($component);
    }
}